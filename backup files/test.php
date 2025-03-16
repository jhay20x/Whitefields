<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Whitefields Dental Clinic Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 900px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .logo h1 {
            font-size: 1.8rem;
            color: #7A6244;
        }
        .illustration img {
            width: 100%;
            height: auto;
        }
        .form-section {
            padding: 2rem;
        }
        .login-button {
            background-color: #007bff;
            color: white;
        }
        .login-button:hover {
            background-color: #0056b3;
        }
        .extras a {
            text-decoration: none;
            color: #007bff;
        }
        .extras a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <!-- Illustration Section -->
            <div class="col-md-6 d-none d-md-block illustration">
                <img src="./resources/images/dentist.svg" alt="Dentist and Patient Illustration">
            </div>
            
            <!-- Form Section -->
            <div class="col-md-6 form-section">
                <div class="text-center mb-4 logo">
                    <img src="./resources/images/wfdc-logo-67459a4f483d0.webp" alt="Whitefields Dental Clinic Logo" class="mb-2" style="width: 300px;">
                    <h1>Whitefields Dental Clinic</h1>
                </div>
                <form>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email or Username</label>
                        <input type="text" class="form-control" id="email" placeholder="Enter your email or username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" placeholder="Enter your password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">👁️</button>
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="captcha">
                        <label class="form-check-label" for="captcha">I'm not a robot</label>
                    </div>
                    <button type="submit" class="btn login-button w-100">LOGIN</button>
                </form>
                <div class="text-center mt-3 extras">
                    <a href="#">Forgot password?</a><br>
                    <span>No account yet? <a href="#">Sign up</a></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle (Optional for form enhancements) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Optional: Toggle Password Visibility Script -->
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        });
    </script>
</body>
</html>
