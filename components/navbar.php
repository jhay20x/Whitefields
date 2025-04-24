<nav class="navbar navbar-expand-lg bg-body-light">
    <div class="container-fluid d-flex justify-content-center">
        <a class="navbar-brand align-items-center" href="#">
            <img src="./resources/images/wfdc-logo-67459a4f483d0.webp" alt="Logo" width="250" height="80">
        </a>                

        <div class="collapse navbar-collapse d-flex justify-content-evenly navigationbar" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link fw-bold <?php echo (basename($_SERVER['PHP_SELF']) == 'home.php') ? 'active' : ''; ?>" aria-current="page" href="home.php">Home</a>
            </div>
            <div class="navbar-nav">
                <a class="nav-link fw-bold <?php echo (basename($_SERVER['PHP_SELF']) == 'services.php') ? 'active' : ''; ?>" href="services.php">Services</a>
            </div>
            <div class="navbar-nav">
                <a class="nav-link fw-bold <?php echo (basename($_SERVER['PHP_SELF']) == 'aboutus.php') ? 'active' : ''; ?>" href="aboutus.php">About Us</a>
            </div>
            <div class="navbar-nav">                        
                <a class="nav-link fw-bold <?php echo (basename($_SERVER['PHP_SELF']) == 'contactus.php') ? 'active' : ''; ?>" href="contactus.php">Contact Us</a>
            </div>
        </div>
    </div>
</nav>