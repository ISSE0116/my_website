<?php
session_start();

require 'db_connect.php';

// Fetch photos from the database ordered by creation date
$stmt = $pdo->prepare("SELECT * FROM photos ORDER BY created_at DESC");
$stmt->execute();
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

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
    <title>Photo Gallery</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head> 
<body>
    <?php include 'navbar.php'; ?>
    <div class="container-fluid mt-4 p-0">
        <h1 class="text-center">Gallery</h1>
        <div class="photo-grid">
            <?php foreach ($photos as $photo): ?>
                <div class="photo-item">
                    <img src="<?php echo $photo['path']; ?>" alt="photo" 
                         data-toggle="modal" data-target="#photoModal" data-photo="<?php echo $photo['path']; ?>" data-description="<?php echo htmlspecialchars($photo['description'], ENT_QUOTES, 'UTF-8'); ?>" data-created="<?php echo htmlspecialchars($photo['created_at'], ENT_QUOTES, 'UTF-8'); ?>">
                    <br>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="photo" style="max-width: 100%; height: auto;">
                    <p id="modalDescription" class="mt-2"></p>
                    <p id="modalCreated" class="text-muted"></p> <!-- Display creation date here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#photoModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var photo = button.data('photo');
            var description = button.data('description');
            var created = button.data('created');
            var modal = $(this);
            modal.find('#modalImage').attr('src', photo);
            modal.find('#modalDescription').text(description);
            modal.find('#modalCreated').text('Added on: ' + created);
        });
    </script>
</body>
</html>
