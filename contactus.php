<?php
$content = json_decode(file_get_contents("content.json"), true);
$contactData = $content['contact'] ?? null;

if (!$contactData) {
    echo "Error: Contact section not found in JSON data";
    exit;
}
?>
<?php
// Start a session to manage form submissions
session_start();

// Store messages in variables, not directly in the output
$successMessage = '';
$errorMessage = '';

// Check for session status messages
if(isset($_SESSION['form_status'])) {
    if($_SESSION['form_status'] == 'success') {
        $successMessage = $contactData['success'] ?? 'Your message has been sent successfully!';
    } else if($_SESSION['form_status'] == 'error') {
        $errorMessage = isset($_SESSION['error_msg']) ? $_SESSION['error_msg'] : ($contactData['error'] ?? 'There was a problem sending your message.');
    }
    
    // Clear the session variables after using them
    unset($_SESSION['form_status']);
    unset($_SESSION['error_msg']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Form</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #fdf6ec;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    
    .contact-container {
      background-color: #d9b08c;
      border-radius: 12px;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      width: 100%;
      max-width: 900px;
    }
    
    .logo-section {
      background-color: #6e5041;
      padding: 30px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      color: #fff;
      text-align: center;
    }
    
    .logo-section img {
      max-width: 100%;
      height: auto;
      margin-bottom: 20px;
    }
    
    .logo-text h1 {
      margin-bottom: 10px;
      font-size: 28px;
    }
    
    .logo-text p {
      margin-top: 0;
      font-size: 16px;
      line-height: 1.6;
    }
    
    .contact-form {
      padding: 30px;
      text-align: center;
    }
    
    h2 {
      color: #5d4037;
      font-size: 28px;
      margin-bottom: 25px;
      font-weight: 600;
    }
    
    .form-label {
      color: #5d4037;
      font-weight: 500;
      text-align: left;
    }
    
    .form-control {
      padding: 12px 15px;
      border: 1px solid #6e5041;
      border-radius: 6px;
      background-color: #f9f0e8;
      color: #5d4037;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    
    .form-control:focus {
      border-color: #a47551;
      box-shadow: 0 0 0 3px rgba(164, 117, 81, 0.2);
    }
    
    .btn-submit {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 6px;
      background-color: #6e5041;
      color: white;
      font-size: 18px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
    }
    
    .btn-submit:hover {
      background-color: #895f4a;
      transform: translateY(-2px);
    }
    
    .btn-submit:active {
      transform: translateY(0);
    }
    
    .success-message {
      background-color: #e8f5e9;
      color: #2e7d32;
      padding: 15px;
      border-radius: 6px;
      margin-bottom: 20px;
      font-weight: 500;
    }
    
    .error-banner {
      background-color: #ffebee;
      color: #c62828;
      padding: 15px;
      border-radius: 6px;
      margin-bottom: 20px;
      font-weight: 500;
    }

    @media (max-width: 767.98px) {
      .logo-section {
        padding: 20px;
      }
      
      .logo-text h1 {
        font-size: 24px;
      }
      
      .contact-form {
        padding: 20px;
      }
      
      h2 {
        font-size: 24px;
      }
    }
  </style>
</head>

<body>
  
  <div class="contact-container">
    <div class="row g-0">
      <div class="col-md-5">
        <div class="logo-section h-100">
          <img src="resources/images/logo-full-67459a46e0812.webp" alt="Dental Clinic Logo" class="img-fluid">
          <div class="logo-text">
            <h1>Dental Clinic</h1>
            <p>Your trusted partner for comprehensive dental care and treatment.</p>
          </div>
        </div>
      </div>
      
      <div class="col-md-7">
        <div class="contact-form">
          <?php if(!empty($successMessage)): ?>
            <div class="success-message"><?php echo $successMessage; ?></div>
          <?php endif; ?>
          
          <?php if(!empty($errorMessage)): ?>
            <div class="error-banner"><?php echo $errorMessage; ?></div>
          <?php endif; ?>
          
          <h2><?php echo htmlspecialchars($contactData['header']); ?></h2>
          <form id="contactForm" action="process_form.php" method="post" class="needs-validation" novalidate>
            <div class="mb-3">
              <label for="name" class="form-label"><?php echo htmlspecialchars($contactData['fields']['name']); ?></label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
              <div class="invalid-feedback">Please enter your name</div>
            </div>
            
            <div class="mb-3">
              <label for="email" class="form-label"><?php echo htmlspecialchars($contactData['fields']['email']); ?></label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
              <div class="invalid-feedback">Please enter a valid email address</div>
            </div>
            
            <div class="mb-3">
              <label for="subject" class="form-label"><?php echo htmlspecialchars($contactData['fields']['subject']); ?></label>
              <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter subject" required>
              <div class="invalid-feedback">Please enter a subject</div>
            </div>
            
            <div class="mb-4">
              <label for="message" class="form-label"><?php echo htmlspecialchars($contactData['fields']['message']); ?></label>
              <textarea class="form-control" id="message" name="message" rows="5" placeholder="Type your message here" required></textarea>
              <div class="invalid-feedback">Please enter your message</div>
            </div>
            
            <div class="mb-3">
              <button type="submit" name="send" class="btn-submit"><?php echo htmlspecialchars($contactData['button']); ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Form validation script
    (function () {
      'use strict'
      
      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      const forms = document.querySelectorAll('.needs-validation')
      
      // Loop over them and prevent submission
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          
          form.classList.add('was-validated')
        }, false)
      })
    })()
    
    // Additional email validation
    document.getElementById('email').addEventListener('input', function() {
      const email = this.value;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      
      if (emailRegex.test(email)) {
        this.setCustomValidity('');
      } else {
        this.setCustomValidity('Please enter a valid email address');
      }
    });
  </script>
</body>
</html>