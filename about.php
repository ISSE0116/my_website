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
    <!-- Font Awesome CSS -->
</head>
<body>
    <div id="navbar"></div>
    <script>
      fetch('navbar.php')
        .then(response => response.text())
        .then(data => {
          document.getElementById('navbar').innerHTML = data;
        });
    </script>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8">
                <h2><strong style="font-size: 1em; color: #2c3e50;">Issei Kikuchi</strong></h2>

                
                <h4>Affiliation</h4>
                <p>graduate student</p>

                <h4>Research Topic</h4>
                <p>The Development of an Exercise Support System Using Spatial Audio</p>
                
                <h4>Hobbies</h4>
                <ul class="indented-list">
                    <li>cycling</li>
                    <li>photography</li>
                </ul>
                
                <h4>Contact</h4>
                <ul>
                    <li>Email: <a href="mailto:yukizaru.2587@icloud.com">yukizaru.2587@icloud.com</a></li>
                </ul>

                <h4>SNS</h4>
                <ul class="list-inline">
                    <li class="list-inline-item"><a href="https://www.instagram.com/issei__masaoka" target="_blank"><i class="fab fa-instagram fa-2x"></i></a></li>
                    <li class="list-inline-item"><a href="https://github.com/ISSE0116" target="_blank"><i class="fab fa-github fa-2x"></i></a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
