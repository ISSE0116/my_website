<?php
/**
 * Notion API クライアント
 * - データベースから公開済み記事の一覧を取得
 * - 個別記事のブロックコンテンツを取得
 * - Notion ブロックを HTML に変換
 * - レスポンスをファイルキャッシュ（デフォルト10分）
 */
class NotionClient {
    private $apiKey;
    private $databaseId;
    private $cacheDir;
    private $cacheTtl;
    private $apiVersion = '2022-06-28';

    public function __construct($apiKey, $databaseId, $cacheDir = null, $cacheTtl = 60) {
        $this->apiKey = $apiKey;
        $this->databaseId = $databaseId;
        $this->cacheDir = $cacheDir ?: __DIR__ . '/cache/notion';
        $this->cacheTtl = $cacheTtl;

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * 公開済み記事の一覧を取得
     */
    public function getPublishedArticles() {
        $cacheKey = 'articles_list';
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $body = [
            'filter' => [
                'property' => 'Published',
                'checkbox' => ['equals' => true]
            ],
            'sorts' => [
                [
                    'property' => 'PublishedDate',
                    'direction' => 'descending'
                ]
            ]
        ];

        $response = $this->request('POST', "databases/{$this->databaseId}/query", $body);

        if (!$response || !isset($response['results'])) {
            return [];
        }

        $articles = [];
        foreach ($response['results'] as $page) {
            $articles[] = $this->parsePageProperties($page);
        }

        $this->setCache($cacheKey, $articles);
        return $articles;
    }

    /**
     * スラッグから記事を取得
     */
    public function getArticleBySlug($slug) {
        $cacheKey = 'article_' . $slug;
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $body = [
            'filter' => [
                'and' => [
                    [
                        'property' => 'Slug',
                        'rich_text' => ['equals' => $slug]
                    ],
                    [
                        'property' => 'Published',
                        'checkbox' => ['equals' => true]
                    ]
                ]
            ]
        ];

        $response = $this->request('POST', "databases/{$this->databaseId}/query", $body);

        if (!$response || empty($response['results'])) {
            return null;
        }

        $page = $response['results'][0];
        $article = $this->parsePageProperties($page);
        $article['content'] = $this->getPageBlocks($page['id']);

        $this->setCache($cacheKey, $article);
        return $article;
    }

    private function getPageBlocks($blockId) {
        $blocks = [];
        $cursor = null;

        do {
            $url = "blocks/{$blockId}/children?page_size=100";
            if ($cursor) {
                $url .= "&start_cursor={$cursor}";
            }

            $response = $this->request('GET', $url);

            if (!$response || !isset($response['results'])) {
                break;
            }

            foreach ($response['results'] as $block) {
                if (!empty($block['has_children'])) {
                    $block['children_blocks'] = $this->getPageBlocks($block['id']);
                }
                $blocks[] = $block;
            }

            $cursor = $response['has_more'] ? $response['next_cursor'] : null;
        } while ($cursor);

        return $blocks;
    }

    /**
     * ページプロパティをパース
     */
    private function parsePageProperties($page) {
        $props = $page['properties'];

        return [
            'id' => $page['id'],
            'title' => $this->extractTitle($props['Title'] ?? $props['Name'] ?? null),
            'slug' => $this->extractRichText($props['Slug'] ?? null),
            'description' => $this->extractRichText($props['Description'] ?? null),
            'tags' => $this->extractMultiSelect($props['Tags'] ?? null),
            'published_date' => $this->extractDate($props['PublishedDate'] ?? null),
            'cover' => $page['cover']['external']['url'] ?? ($page['cover']['file']['url'] ?? null),
        ];
    }

    /**
     * Notion ブロックを HTML に変換
     */
    public function blocksToHtml($blocks) {
        $html = '';
        $listOpen = false;
        $listType = '';

        foreach ($blocks as $block) {
            $type = $block['type'];

            // リストの開閉処理
            if ($type !== 'bulleted_list_item' && $type !== 'numbered_list_item') {
                if ($listOpen) {
                    $html .= ($listType === 'ul') ? '</ul>' : '</ol>';
                    $listOpen = false;
                }
            }

            $childrenHtml = '';
            if (!empty($block['children_blocks'])) {
                $childContent = $this->blocksToHtml($block['children_blocks']);
                if ($type !== 'bulleted_list_item' && $type !== 'numbered_list_item') {
                    $childrenHtml = '<div style="margin-left: 1.5em;">' . $childContent . '</div>';
                } else {
                    $childrenHtml = $childContent;
                }
            }

            switch ($type) {
                case 'paragraph':
                    $text = $this->richTextToHtml($block['paragraph']['rich_text']);
                    if (!empty(trim(strip_tags($text))) || $childrenHtml !== '') {
                        $html .= "<p>{$text}</p>{$childrenHtml}";
                    }
                    break;

                case 'heading_1':
                    $text = $this->richTextToHtml($block['heading_1']['rich_text']);
                    $html .= "<h2>{$text}</h2>{$childrenHtml}";
                    break;

                case 'heading_2':
                    $text = $this->richTextToHtml($block['heading_2']['rich_text']);
                    $html .= "<h3>{$text}</h3>{$childrenHtml}";
                    break;

                case 'heading_3':
                    $text = $this->richTextToHtml($block['heading_3']['rich_text']);
                    $html .= "<h4>{$text}</h4>{$childrenHtml}";
                    break;

                case 'bulleted_list_item':
                    if (!$listOpen || $listType !== 'ul') {
                        if ($listOpen) $html .= '</ol>';
                        $html .= '<ul class="article-list">';
                        $listOpen = true;
                        $listType = 'ul';
                    }
                    $text = $this->richTextToHtml($block['bulleted_list_item']['rich_text']);
                    $html .= "<li>{$text}{$childrenHtml}</li>";
                    break;

                case 'numbered_list_item':
                    if (!$listOpen || $listType !== 'ol') {
                        if ($listOpen) $html .= '</ul>';
                        $html .= '<ol class="article-list">';
                        $listOpen = true;
                        $listType = 'ol';
                    }
                    $text = $this->richTextToHtml($block['numbered_list_item']['rich_text']);
                    $html .= "<li>{$text}{$childrenHtml}</li>";
                    break;

                case 'code':
                    $text = $this->extractPlainText($block['code']['rich_text']);
                    $lang = $block['code']['language'] ?? '';
                    $html .= "<pre><code class=\"language-{$lang}\">" . htmlspecialchars($text) . "</code></pre>{$childrenHtml}";
                    break;

                case 'quote':
                    $text = $this->richTextToHtml($block['quote']['rich_text']);
                    $html .= "<blockquote>{$text}{$childrenHtml}</blockquote>";
                    break;

                case 'callout':
                    $text = $this->richTextToHtml($block['callout']['rich_text']);
                    $emoji = $block['callout']['icon']['emoji'] ?? '💡';
                    $html .= "<div class=\"callout\"><span class=\"callout-icon\">{$emoji}</span><div>{$text}{$childrenHtml}</div></div>";
                    break;

                case 'image':
                    $url = $block['image']['file']['url'] ?? ($block['image']['external']['url'] ?? '');
                    $caption = !empty($block['image']['caption']) ? $this->extractPlainText($block['image']['caption']) : '';
                    if ($url) {
                        $html .= "<figure class=\"article-image\"><img src=\"{$url}\" alt=\"{$caption}\" loading=\"lazy\"><figcaption>{$caption}</figcaption></figure>{$childrenHtml}";
                    }
                    break;

                case 'divider':
                    $html .= "<hr>{$childrenHtml}";
                    break;

                case 'bookmark':
                    $url = $block['bookmark']['url'] ?? '';
                    $caption = !empty($block['bookmark']['caption']) ? $this->extractPlainText($block['bookmark']['caption']) : $url;
                    $html .= "<a href=\"{$url}\" class=\"bookmark-link\" target=\"_blank\" rel=\"noopener\">{$caption}</a>{$childrenHtml}";
                    break;

                case 'table':
                    $hasColHeader = $block['table']['has_column_header'] ?? false;
                    $hasRowHeader = $block['table']['has_row_header'] ?? false;
                    $tableHtml = "<div class=\"table-responsive\" style=\"overflow-x: auto; margin-bottom: 1.5em;\"><table class=\"article-table\" style=\"width: 100%; border-collapse: collapse;\">";
                    
                    if (!empty($block['children_blocks'])) {
                        $tableHtml .= "<tbody>";
                        foreach ($block['children_blocks'] as $rowIndex => $rowBlock) {
                            if ($rowBlock['type'] !== 'table_row') continue;
                            
                            $tableHtml .= "<tr>";
                            foreach ($rowBlock['table_row']['cells'] as $colIndex => $cell) {
                                $text = $this->richTextToHtml($cell);
                                $isHeader = ($hasColHeader && $rowIndex === 0) || ($hasRowHeader && $colIndex === 0);
                                $tag = $isHeader ? 'th' : 'td';
                                $style = "border: 1px solid #ddd; padding: 12px 16px;";
                                if ($isHeader) {
                                    $style .= " background-color: #f8f9fa; font-weight: 600;";
                                }
                                $tableHtml .= "<{$tag} style=\"{$style}\">{$text}</{$tag}>";
                            }
                            $tableHtml .= "</tr>";
                        }
                        $tableHtml .= "</tbody>";
                    }
                    $tableHtml .= "</table></div>";
                    $html .= $tableHtml;
                    break;
            }
        }

        // リストが開いたまま終了した場合
        if ($listOpen) {
            $html .= ($listType === 'ul') ? '</ul>' : '</ol>';
        }

        return $html;
    }

    /**
     * リッチテキストを HTML に変換
     */
    private function richTextToHtml($richText) {
        if (empty($richText)) return '';

        $html = '';
        foreach ($richText as $text) {
            $content = htmlspecialchars($text['plain_text']);
            $annotations = $text['annotations'] ?? [];

            if (!empty($annotations['bold'])) $content = "<strong>{$content}</strong>";
            if (!empty($annotations['italic'])) $content = "<em>{$content}</em>";
            if (!empty($annotations['strikethrough'])) $content = "<del>{$content}</del>";
            if (!empty($annotations['code'])) $content = "<code>{$content}</code>";
            if (!empty($annotations['underline'])) $content = "<u>{$content}</u>";

            if (!empty($text['href'])) {
                $content = "<a href=\"{$text['href']}\" target=\"_blank\" rel=\"noopener\">{$content}</a>";
            }

            $html .= $content;
        }
        return $html;
    }

    /**
     * プレーンテキストを抽出
     */
    private function extractPlainText($richText) {
        if (empty($richText)) return '';
        return implode('', array_map(fn($t) => $t['plain_text'], $richText));
    }

    private function extractTitle($prop) {
        if (!$prop || empty($prop['title'])) return '';
        return $this->extractPlainText($prop['title']);
    }

    private function extractRichText($prop) {
        if (!$prop || empty($prop['rich_text'])) return '';
        return $this->extractPlainText($prop['rich_text']);
    }

    private function extractMultiSelect($prop) {
        if (!$prop || empty($prop['multi_select'])) return [];
        return array_map(fn($t) => $t['name'], $prop['multi_select']);
    }

    private function extractDate($prop) {
        if (!$prop || empty($prop['date'])) return null;
        return $prop['date']['start'] ?? null;
    }

    /**
     * Notion API リクエスト
     */
    private function request($method, $endpoint, $body = null) {
        $url = "https://api.notion.com/v1/{$endpoint}";

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Notion-Version: ' . $this->apiVersion,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($body) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Notion API error: HTTP {$httpCode} - {$response}");
            return null;
        }

        return json_decode($response, true);
    }

    /**
     * キャッシュ取得
     */
    private function getCache($key) {
        $file = $this->cacheDir . '/' . md5($key) . '.json';
        if (!file_exists($file)) return null;

        $mtime = filemtime($file);
        if (time() - $mtime > $this->cacheTtl) {
            unlink($file);
            return null;
        }

        $data = file_get_contents($file);
        return json_decode($data, true);
    }

    /**
     * キャッシュ保存
     */
    private function setCache($key, $data) {
        $file = $this->cacheDir . '/' . md5($key) . '.json';
        file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}
?>
