<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once 'notion_client.php';

$notionApiKey = $_ENV['NOTION_API_KEY'] ?? '';
$notionDbId = $_ENV['NOTION_DATABASE_ID'] ?? '';

$articles = [];
$error = null;

if (!empty($notionApiKey) && !empty($notionDbId)) {
    try {
        $notion = new NotionClient($notionApiKey, $notionDbId);
        $articles = $notion->getPublishedArticles();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
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
    <title>Tech Blog | Project</title>
    <meta name="description" content="技術ブログ - Web開発、クラウド、プログラミングに関する記事">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/blog.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head> 
<body>
    <?php include 'navbar.php'; ?>

    <div class="blog-hero">
        <div class="blog-hero-content">
            <h1>Blog</h1>
            <p>学んだことをまとめています</p>
        </div>
    </div>

    <div class="container blog-container">
        <?php if (empty($articles)): ?>
            <div class="blog-empty">
                <i class="fas fa-pencil-alt blog-empty-icon"></i>
                <h2>Coming Soon..</h2>
            </div>
        <?php else: ?>
            <div class="blog-grid">
                <?php foreach ($articles as $article): ?>
                    <a href="article/<?php echo htmlspecialchars($article['slug']); ?>" class="blog-card-link">
                        <article class="blog-card">
                            <?php if (!empty($article['cover'])): ?>
                                <div class="blog-card-cover">
                                    <img src="<?php echo htmlspecialchars($article['cover']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" loading="lazy">
                                </div>
                            <?php else: ?>
                                <div class="blog-card-cover blog-card-cover-placeholder">
                                    <i class="fas fa-code"></i>
                                </div>
                            <?php endif; ?>
                            <div class="blog-card-body">
                                <div class="blog-card-tags">
                                    <?php foreach ($article['tags'] as $tag): ?>
                                        <span class="blog-tag"><?php echo htmlspecialchars($tag); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <h2 class="blog-card-title"><?php echo htmlspecialchars($article['title']); ?></h2>
                                <?php if (!empty($article['description'])): ?>
                                    <p class="blog-card-desc"><?php echo htmlspecialchars($article['description']); ?></p>
                                <?php endif; ?>
                                <div class="blog-card-meta">
                                    <?php if (!empty($article['published_date'])): ?>
                                        <time datetime="<?php echo $article['published_date']; ?>">
                                            <i class="far fa-calendar-alt"></i>
                                            <?php echo date('Y.m.d', strtotime($article['published_date'])); ?>
                                        </time>
                                    <?php endif; ?>
                                    <span class="blog-card-read">Read →</span>
                                </div>
                            </div>
                        </article>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
