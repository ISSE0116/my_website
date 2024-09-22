<!-- awesome CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- Popper.js, required by Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<!-- Bootstrap JavaScript -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php" style="margin-top: 5px;">
        <i class="fas fa-camera-retro fa-2x"></i>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item home">
                <a class="nav-link" href="index">HOME</a>
            </li>
            <li class="nav-item gallery">
                <a class="nav-link" href="list">Gallery</a>
            </li>
            <li class="nav-item project">
                <a class="nav-link" href="project">Project</a>
            </li>
            <li class="nav-item about-me">
                <a class="nav-link" href="about">About Me</a>
            </li>
            <li class="nav-item admin">
                <a class="nav-link" href="login">
                    <i class="fas fa-key "></i>
                </a>
            </li>
        </ul>
    </div>
</nav>

<script>
$(document).ready(function() {
    $(".navbar-toggler").click(function() {
        $(this).toggleClass("toggler-open");
    });
});
</script>
