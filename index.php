<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$googleMapsApiKey = $_ENV['GOOGLE_MAPS_API_KEY'];
$mapId = $_ENV['MAP_ID'];

session_start();

require 'db_connect.php';

$stmt = $pdo->prepare("SELECT * FROM photos");
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
    <title>Map View</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container-fluid mt-4 p-0">
        <div id="map"></div>
    </div>
    <script>
        function initMap() {
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 4,
                center: { lat: 35.9895, lng: 139.6917 },
                mapId: "<?php echo $mapId; ?>",
                disableDefaultUI: true
            });

            const infowindow = new google.maps.InfoWindow({
                maxWidth: 300 // ポップアップの最大幅を設定
            });

            const photos = <?php echo json_encode($photos); ?>;
            photos.forEach(photo => {
                const marker = new google.maps.marker.AdvancedMarkerElement({
                    position: { lat: parseFloat(photo.latitude), lng: parseFloat(photo.longitude) },
                    map: map,
                    title: photo.description
                });

                marker.addListener('click', () => {
                    // ポップアップの内容を画像のみ表示し、エッジを狭めるスタイルを適用
                    infowindow.setContent(`
                        <div class="infowindow-content" style="padding: 0; border-radius: 5px; overflow: hidden;">
                            <img src="${photo.path}" alt="photo" style="width: 100%; height: auto; border-radius: 5px;">
                        </div>
                    `);
                    infowindow.open(map, marker);
                });
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googleMapsApiKey; ?>&callback=initMap&libraries=marker&v=weekly" async defer></script>
</body>
</html>
