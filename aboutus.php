<?php
$content = json_decode(file_get_contents("content.json"), true);
$about = $content['about'] ?? null;

if (!$about) {
    echo "Error: About section not found in JSON data";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>About Us - White Fields Dental Clinic</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" />
  <style>
    body {
      background-color: #fdf6ec;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #5d4037;
      line-height: 1.6;
    }
    .hero-section {
      background-color: #d9b08c;
      padding: 3rem 1rem;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .hero-section h1 {
      color: #6e5041;
      font-size: 2.25rem;
      margin-bottom: 1rem;
    }
    .hero-section p {
      font-size: 1.125rem;
      color: #5d4037;
    }
    .section-title {
      color: #6e5041;
      font-size: 1.75rem;
      text-align: center;
      margin-bottom: 1.875rem;
      position: relative;
      padding-bottom: 0.9375rem;
    }
    .section-title:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 5rem;
      height: 0.1875rem;
      background-color: #a47551;
    }
    .card {
      background-color: #e6d2c0;
      border: none;
      border-radius: 0.625rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      height: 100%;
    }
    .value-item {
      text-align: center;
      padding: 1.25rem;
    }
    .value-item h3 {
      color: #6e5041;
      margin-bottom: 0.625rem;
    }
    .value-icon {
      font-size: 2.25rem;
      color: #a47551;
      margin-bottom: 0.9375rem;
    }
    .cta-section {
      background-color: #d9b08c;
      text-align: center;
      padding: 3.125rem 1.25rem;
      border-radius: 0.625rem;
      margin-bottom: 2.5rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .cta-section h2 {
      color: #6e5041;
      margin-bottom: 1.25rem;
    }
    .btn-cta {
      background-color: #6e5041;
      color: #fff;
      padding: 0.75rem 1.875rem;
      border-radius: 0.3125rem;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s;
      display: inline-block;
    }
    .btn-cta:hover {
      background-color: #895f4a;
      color: #fff;
    }
    .team-photo {
      border-radius: 0.5rem;
      width: 100%;
      height: 250px;
      object-fit: cover;
    }
    .gallery-photo {
  width: 100%;
  height: 200px; /* or 180px - adjust as needed */
  object-fit: cover;
  border-radius: 0.5rem;
}

    footer {
      text-align: center;
      padding: 1.25rem;
      background-color: #d9b08c;
      color: #6e5041;
    }
  </style>
</head>
<body>

  <div class="hero-section">
    <div class="container">
      <h1><?= htmlspecialchars($about['hero']['title']); ?></h1>
      <p><?= htmlspecialchars($about['hero']['subtitle']); ?></p>
    </div>
  </div>

  <div class="container py-5">

    <!-- Mission & Vision -->
    <div class="card mb-5">
      <div class="card-body p-4">
        <h2 class="section-title">Our Purpose</h2>
        <div class="row">
          <div class="col-md-6 mb-4 mb-md-0">
            <h3 class="h4 text-center mb-3">Our Mission</h3>
            <p><?= htmlspecialchars($about['mission']); ?></p>
          </div>
          <div class="col-md-6">
            <h3 class="h4 text-center mb-3">Our Vision</h3>
            <p><?= htmlspecialchars($about['vision']); ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Values -->
    <?php if (!empty($about['values'])): ?>
    <div class="card mb-5">
      <div class="card-body p-4">
        <h2 class="section-title">Our Core Values</h2>
        <div class="row">
          <?php foreach ($about['values'] as $value): ?>
          <div class="col-6 col-md-3 mb-4">
            <div class="value-item">
              <div class="value-icon">
                <i class="bi <?= htmlspecialchars($value['icon']); ?>"></i>
              </div>
              <h3><?= htmlspecialchars($value['title']); ?></h3>
              <p><?= htmlspecialchars($value['description']); ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Team -->
    <?php if (!empty($about['team'])): ?>
    <div class="card mb-5">
      <div class="card-body p-4">
        <h2 class="section-title">Meet Our Team</h2>
        <div class="row">
          <?php foreach ($about['team'] as $member): ?>
          <div class="col-md-4 mb-4">
            <div class="text-center">
              <img src="resources/images/<?= htmlspecialchars($member['image']); ?>" class="team-photo mb-3" alt="<?= htmlspecialchars($member['name']); ?>">
              <h4><?= htmlspecialchars($member['name']); ?></h4>
              <p class="text-muted mb-1"><?= htmlspecialchars($member['role']); ?></p>
              <p><?= htmlspecialchars($member['bio']); ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Gallery -->
    <?php if (!empty($about['gallery'])): ?>
    <div class="card mb-5">
      <div class="card-body p-4">
        <h2 class="section-title">Our Clinic Facilities</h2>
        <div class="row">
          <?php foreach ($about['gallery'] as $item): ?>
          <div class="col-md-3 col-sm-6 mb-4">
            <img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['alt']); ?>" class="gallery-photo mb-2">
            <p class="text-center"><?= htmlspecialchars($item['alt']); ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- CTA -->
    <?php if (!empty($about['cta'])): ?>
    <div class="cta-section">
      <h2><?= htmlspecialchars($about['cta']['text']); ?></h2>
      <p class="mb-4"><?= htmlspecialchars($about['cta']['subtext']); ?></p>
      <a href="<?= htmlspecialchars($about['cta']['button']['link']); ?>" class="btn-cta">
        <?= htmlspecialchars($about['cta']['button']['label']); ?>
      </a>
    </div>
    <?php endif; ?>

  </div>

  <footer>
    <p class="mb-0">&copy; 2025 Whitefield's Dental Clinic. All rights reserved.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
