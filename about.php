

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

    <div class="container mt-5">
        <div class="text-center">
            <!-- プロフィール写真 -->
            <div class="profile-photo mb-4">
                <img src="./images/con.jpg" alt="Profile Photo" class="rounded-circle">
            </div>

            <!-- 名前と所属 -->
            <h2><strong style="font-size: 1.5em; color: #2c3e50;">Issei Kikuchi</strong></h2>
            <h4>Graduate Student</h4>

            <!-- 趣味 -->
            <h4>Hobbies</h4>
            <ul class="list-inline">
                <li class="list-inline-item">Cycling</li>
                <li class="list-inline-item">Photography</li>
            </ul>

            <!-- スキル -->
            <h4>Skills(Language,Framework)</h4>
            <ul class="list-inline">
                <li class="list-inline-item">C</li>
                <li class="list-inline-item">C++</li>
                <li class="list-inline-item">C#</li>
                <li class="list-inline-item">Python</li>
                <li class="list-inline-item">JavaScript</li>
                <li class="list-inline-item">HTML/CSS</li>
                <li class="list-inline-item">AWS</li>
                <li class="list-inline-item">Flask</li>
                <li class="list-inline-item">React</li>
                <li class="list-inline-item">AWS</li>
                <li class="list-inline-item">Pytorch</li>
            </ul>

            <!-- 連絡先 -->
            <h4>Contact</h4>
            <ul class="list-inline">
                <li class="list-inline-item">Email: <a href="mailto:yukizaru.2587@icloud.com">yukizaru.2587@icloud.com</a></li>
            </ul>

            <!-- SNS -->
            <h4>SNS</h4>
            <ul class="list-inline">
                <li class="list-inline-item"><a href="https://www.instagram.com/issei__masaoka" target="_blank"><i class="fab fa-instagram fa-2x"></i></a></li>
                <li class="list-inline-item"><a href="https://github.com/ISSE0116" target="_blank"><i class="fab fa-github fa-2x"></i></a></li>
            </ul>
        </div>
    </div>

</body>
</html>
