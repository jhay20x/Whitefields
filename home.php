<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Whitefields Dental Clinic</title>
	<link rel="shortcut icon" type="image/x-icon" href="./resources/images/logo-icon-67459a47526b9.webp"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=block" rel="stylesheet">
    <link rel="stylesheet" href="./resources/css/home.css">

</head>

<body>
    <?php include "./components/topbar.php" ?>

    <div class="container p-0 shadow">
        <div class="p-3 pb-0">

            <?php include "./components/navbar.php" ?>

            <div class="d-flex justify-content-center">
                <div class="d-flex align-items-center row headerrow">
                    <div class="col-md-6 p-0 ps-3 pb-3">
                        <h1 class="fw-bold display-4">DENTAL TREATMENT</h1>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                        <button class="btn btn-sm btn-secondary topheader">Read More</button>
                    </div>   

                    <div class="col-md-6 p-0">
                        <img src="./resources/images/smile-67459a4b6c4c3.webp" alt="Smile" class="img-fluid">
                    </div>             
                </div>
            </div>
        </div>
        

        <div class="container d-flex justify-content-center" style="background-color: rgba(166,143,98,255);">
            <div id="carouselExampleInterval" class="carousel slide carousel-fade col-md-8" data-bs-ride="carousel" data-bs-theme="dark">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleInterval" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleInterval" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleInterval" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    <button type="button" data-bs-target="#carouselExampleInterval" data-bs-slide-to="3" aria-label="Slide 4"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="./resources/images/carousel-1-67459a3f25558.webp" class="d-block img-fluid" alt="...">
                    </div> 
                    <div class="carousel-item"> 
                        <img src="./resources/images/carousel-2-67459a4400202.webp" class="d-block img-fluid" alt="...">
                    </div> 
                    <div class="carousel-item"> 
                        <img src="./resources/images/carousel-3-67459a4449f24.webp" class="d-block img-fluid" alt="...">
                    </div> 
                    <div class="carousel-item"> 
                        <img src="./resources/images/carousel-4-67459a45910cb.webp" class="d-block img-fluid" alt="...">
                    </div> 
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleInterval" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-center align-items-center">
            <img src="./resources/images/background-67459a3e2a49c.webp" width="100%" class="img-fluid" alt="">
            <div class="z-1 position-absolute text-center col-md-6 schedule">
                <h1 class="fw-bold display-4">Clinic Schedule</h1>
                <h3>Monday | 9:00 AM - 5:00 PM</h3>
                <h3>Tuesday | 9:00 AM - 5:00 PM</h3>
                <h3>Wednesday | Closed</h3>
                <h3>Thursday | 9:00 AM - 5:00 PM</h3>
                <h3>Friday | 9:00 AM - 5:00 PM</h3>
                <h3>Saturday | 9:00 AM - 5:00 PM</h3>
                <h3>Sunday | 9:00 AM - 5:00 PM</h3>
            </div>
        </div>

        <div class="d-flex justify-content-center align-items-center topheader" style="height: 100px;">            
            <div class="z-1 position-absolute text-center text-white col-md-6 schedule">
                <p class="lh-sm">whitefieldsdentalclinic@gmail.com <br> 0976-306-3833 <br> 78c Sanciangco St. Tonsuya Malabon City</p>
            </div>
        </div>
    </div>    
</body>
</html>