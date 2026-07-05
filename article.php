<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once 'notion_client.php';

$notionApiKey = $_ENV['NOTION_API_KEY'] ?? '';
$notionDbId = $_ENV['NOTION_DATABASE_ID'] ?? '';

// URLからスラッグを取得
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: /project');
    exit;
}

$article = null;

if (!empty($notionApiKey) && !empty($notionDbId)) {
    $notion = new NotionClient($notionApiKey, $notionDbId);
    $article = $notion->getArticleBySlug($slug);
}

if (!$article) {
    http_response_code(404);
    echo '<!DOCTYPE html><html><head><title>404</title><link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="/css/style.css"></head><body>';
    include 'navbar.php';
    echo '<div class="container mt-5 text-center"><h1>404</h1><p>記事が見つかりませんでした。</p><a href="/project" class="btn-back">← 記事一覧に戻る</a></div></body></html>';
    exit;
}

$contentHtml = $notion->blocksToHtml($article['content']);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-PHG2R409ND"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-PHG2R409ND');
    </script>
  
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> | Tech Blog</title>
    <meta name="description" content="<?php echo htmlspecialchars($article['description']); ?>">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/blog.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Prism.js for syntax highlighting -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
</head> 
<body>
    <?php include __DIR__ . '/navbar.php'; ?>

    <article class="article-container">
        <header class="article-header">
            <a href="/project" class="btn-back"><i class="fas fa-arrow-left"></i> 記事一覧</a>
            
            <div class="article-tags">
                <?php foreach ($article['tags'] as $tag): ?>
                    <span class="blog-tag"><?php echo htmlspecialchars($tag); ?></span>
                <?php endforeach; ?>
            </div>

            <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>

            <?php if (!empty($article['published_date'])): ?>
                <time class="article-date" datetime="<?php echo $article['published_date']; ?>">
                    <i class="far fa-calendar-alt"></i>
                    <?php echo date('Y年m月d日', strtotime($article['published_date'])); ?>
                </time>
            <?php endif; ?>
        </header>

        <?php if (!empty($article['cover'])): ?>
            <div class="article-cover">
                <img src="<?php echo htmlspecialchars($article['cover']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
            </div>
        <?php endif; ?>

        <div class="article-body">
            <?php echo $contentHtml; ?>
        </div>

        <footer class="article-footer">
            <a href="/project" class="btn-back"><i class="fas fa-arrow-left"></i> 記事一覧に戻る</a>
        </footer>
    </article>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-bash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-sql.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js"></script>
</body>
</html>
