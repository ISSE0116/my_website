

<?php include 'db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
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
    <title>About Me</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="profile-card">
            <!-- プロフィール写真 -->
            <div class="profile-photo mb-4">
                <img src="./images/icon.jpg" alt="Profile Photo">
            </div>

            <!-- 名前と所属 -->
            <h2>Issei Kikuchi</h2>
            <p class="text-muted" style="color: var(--text-muted);">January 16, 2001 </p>

            <hr class="my-4" style="border-top: 1px solid var(--border);">

            <!-- 趣味 -->
            <h4 class="mb-3">Hobbies</h4>
            <div class="mb-4">
                <span class="pill-badge">Cycling</span>
                <span class="pill-badge">Photography</span>
            </div>

            <!-- スキル -->
            <h4 class="mb-3">Skills & Frameworks</h4>
            <div class="mb-4">
                <span class="pill-badge">C / C++ / C#</span>
                <span class="pill-badge">Python</span>
                <span class="pill-badge">JavaScript</span>
                <span class="pill-badge">HTML/CSS</span>
                <span class="pill-badge">AWS</span>
                <span class="pill-badge">Azure</span>
                <span class="pill-badge">Flask</span>
                <span class="pill-badge">React</span>
                <span class="pill-badge">PyTorch</span>
            </div>

            <!-- 連絡先 -->
            <h4 class="mb-3">Contact</h4>
            <div class="mb-4">
                <a href="mailto:yukizaru.2587@icloud.com" class="pill-badge"><i class="fas fa-envelope"></i> yukizaru.2587@icloud.com</a>
            </div>

            <!-- SNS -->
            <h4 class="mb-3">Connect</h4>
            <div>
                <a href="https://www.instagram.com/issei__masaoka" target="_blank" class="mx-2" style="color: var(--primary);"><i class="fab fa-instagram fa-2x"></i></a>
                <a href="https://github.com/ISSE0116" target="_blank" class="mx-2" style="color: var(--text-main);"><i class="fab fa-github fa-2x"></i></a>
            </div>
        </div>
    </div>

</body>
</html>
