<?php 
$content = json_decode(file_get_contents("content.json"), true);
$services = $content['services']; // Extract the services array from the content
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dental Services</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #fdf4e5;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .header {
      background-color: #d9c3a0;
      text-align: center;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 1000;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      padding: 15px 0;
    }
    .header h1 {
      margin: 0;
      font-size: 2rem;
      color: #333;
    }
    .services-section {
      padding-top: 80px;
    }
    .top-section {
      position: relative;
      min-height: 100vh;
      background-color: transparent !important;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 2rem;
    }
    .top-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: url('resources/images/1bg.png');
      background-size: contain;
      background-position: center;
      background-repeat: no-repeat;
      z-index: 1;
    }
    .service-card {
      background-color: #AC8E63;
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      text-align: center;
      padding: 20px;
      height: 100%;
      transition: transform 0.3s ease;
    }
    .service-card:hover {
      transform: translateY(-5px);
    }
    .circle-frame {
      width: 80px;
      height: 80px;
      background-color: #f9b3b3;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 15px;
      overflow: hidden;
    }
    .circle-frame img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }
    .service-card h3 {
      margin: 10px 0;
      font-size: 1.2rem;
      text-transform: uppercase;
      color: #333;
    }
    .service-card p {
      font-size: 0.9rem;
      color: #555;
    }
    @media (max-width: 576px) {
      .header h1 {
        font-size: 1.5rem;
      }
      .services-section {
        padding-top: 70px;
      }
      .top-section {
        min-height: 30vh;
      }
      .circle-frame {
        width: 60px;
        height: 60px;
      }
    }
  </style>
</head>
<body>
  <header class="header">
    <h1>DENTAL SERVICES</h1>
  </header>

  <section class="services-section">
    <div class="container">
      <div class="top-section"></div>
      <div class="row g-4">
        <?php foreach ($services as $service): ?>
          <div class="col-12 col-md-6 col-lg-3">
            <div class="service-card">
              <div class="circle-frame">
                <img src="resources/images/<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>">
              </div>
              <h3><?php echo htmlspecialchars($service['title']); ?></h3>
              <p><?php echo htmlspecialchars($service['description']); ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
