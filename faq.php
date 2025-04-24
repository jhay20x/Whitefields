<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - White Fields Dental Clinic</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #d7b7a3; /* Light beige from the header */
            --secondary: #e8d8c9; /* Lighter beige from the content box */
            --accent: #a77f5e; /* Darker brown accent */
            --text-dark: #5c4d3e; /* Dark brown text */
            --background: #fff9f4; /* Very light beige background */
            --white: #ffffff;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            color: var(--text-dark);
            background-color: var(--background);
        }
        
        .navbar {
            background-color: var(--white);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 0.5rem 1rem;
        }
        
        .navbar-brand {
            color: var(--text-dark);
            font-weight: bold;
        }
        
        .hero-section {
            background-color: var(--primary);
            color: var(--text-dark);
            padding: 3rem 0;
            text-align: center;
        }
        
        .faq-section {
            padding: 3rem 0;
            background-color: var(--background);
        }
        
        .faq-item {
            border-radius: 8px;
            margin-bottom: 1rem;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background-color: var(--white);
        }
        
        .faq-question {
            background-color: var(--secondary);
            padding: 1rem;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-dark);
        }
        
        .faq-question:hover {
            background-color: var(--primary);
        }
        
        .faq-answer {
            padding: 1rem;
            display: none;
        }
        
        .faq-answer.active {
            display: block;
        }
        
        .faq-category {
            color: var(--text-dark);
            border-bottom: 2px solid var(--accent);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .btn-primary {
            background-color: var(--accent);
            border-color: var(--accent);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background-color: #8e6848;
            border-color: #8e6848;
        }
        
        .icon-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--secondary);
            color: var(--accent);
            margin-bottom: 1rem;
        }
        
        .content-box {
            background-color: var(--secondary);
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--text-dark);
            position: relative;
        }
        
        .section-title:after {
            content: "";
            display: block;
            width: 80px;
            height: 3px;
            background-color: var(--accent);
            margin: 0.8rem auto 0;
        }
        
        .logo-container {
            max-width: 200px;
        }
        
        .logo-container img {
            width: 100%;
            height: auto;
        }
        
        .contact-card {
            background-color: var(--secondary);
            border-radius: 8px;
            padding: 1.5rem;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .faq-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }
        
        footer {
            background-color: var(--primary);
            color: var(--text-dark);
            padding: 2rem 0;
        }
        
        @media (max-width: 767.98px) {
            .hero-section {
                padding: 2rem 0;
            }
            
            .faq-section {
                padding: 2rem 0;
            }
            
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="images/logo.png" alt="White Fields Dental Clinic" height="40">
            </a>
            <!-- No navigation buttons as requested -->
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Frequently Asked Questions</h1>
            <p class="lead">Where Dental Care Meets Comfort and Compassion</p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
          

            <!-- Quick Help -->
            <div class="row text-center mb-5">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="icon-box">
                        <i class="fas fa-calendar-alt fa-lg"></i>
                    </div>
                    <h5>Appointment Booking</h5>
                    <p>Quick help for booking</p>
                    <a href="#appointments" class="btn btn-sm btn-primary">Learn More</a>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="icon-box">
                        <i class="fas fa-user fa-lg"></i>
                    </div>
                    <h5>Account Management</h5>
                    <p>Managing your profile</p>
                    <a href="#account" class="btn btn-sm btn-primary">Learn More</a>
                </div>
                <div class="col-md-4">
                    <div class="icon-box">
                        <i class="fas fa-cog fa-lg"></i>
                    </div>
                    <h5>Technical Support</h5>
                    <p>Help with using our system</p>
                    <a href="#technical" class="btn btn-sm btn-primary">Learn More</a>
                </div>
            </div>

            <div class="content-box">
                <div class="row">
                    <div class="col-lg-12">
                        <!-- Getting Started Section -->
                        <h3 class="section-title" id="getting-started">Getting Started</h3>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                How do I create an account?
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>To create an account on our patient portal, follow these simple steps:</p>
                                <ol>
                                    <li>Click on the "Register" button in the bottom right corner</li>
                                    <li>Fill in the required information in the registration form</li>
                                    <li>Verify your email address by filling out otp (one-time-password) sent to your inbox</li>
                                    <li>Complete your profile by adding your personal information, dental history etc.</li>
                                </ol>
                                <p>If you encounter any issues during registration, please contact our support team.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                What information do I need to register?
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>To complete the registration process, please have the following information ready:</p>
                                <ul>
                                    <li>Username</li>
                                    <li>Email address</li>
                                    <li>Password</li>
                                    <li>Complete the reCaptcha</li>
                                </ul>
                                <p>Having this information on hand will help ensure a smooth registration process.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                Is my personal information secure?
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>At White Fields Dental Clinic, we take data security very seriously. All patient information is protected using industry-standard encryption and security protocols. Our system complies with all relevant healthcare data protection regulations.</p>
                                <p>We never share your personal information with third parties without your explicit consent, except when required by law.</p>
                            </div>
                        </div>

                        <!-- Appointment Booking Section -->
                        <h3 class="section-title mt-5" id="appointments">Appointment Booking</h3>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                How do I book a new appointment?
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Booking an appointment with White Fields Dental Clinic is easy and convenient:</p>
                                <ol>
                                    <li>Log in to your account</li>
                                    <li>Go to the dashboard (3 lines, upper right corner)</li>
                                    <li>Select My Appointments</li>
                                    <li>Right on the schedule table, select create appointment</li>
                                    <li>Confirm your appointment details</li>
                                    <li>Submit your booking request</li>
                                </ol>
                                <p>You will receive a confirmation email once your appointment is scheduled.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                How can i cancel my appointment
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>To cancel an existing appointment:</p>
                                <ol>
                                    <li>Log in to your account</li>
                                    <li>Navigate to "My Appointments"</li>
                                    <li>Find the appointment you wish to cancel</li>
                                    <li>Click "View"</li>
                                    <li>Select "Cancel Appointment"</li>
                                    <li>Select reason, or select "others" if nonexistent</li>
                                    <li>Cancelled will appear on your status</li>
                                </ol>
                                <p>Cancellation should be paired with reasonable reason. </p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                What is your cancellation policy?
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Our cancellation policy is designed to be fair while ensuring efficient scheduling:</p>
                                <ul>
                                    <li>Cancellations made 24+ hours before the appointment: No charge</li>
                                    <li>Cancellations made less than 24 hours before the appointment: 50% of the appointment fee may apply</li>
                                    <li>No-shows: Full appointment fee may apply</li>
                                </ul>
                                <p><strong>Important:</strong> We understand that emergencies happen. If you need to cancel due to illness or emergency, please contact us as soon as possible, and we will do our best to accommodate your situation.</p>
                            </div>
                        </div>

                        <!-- Account Management Section -->
                        <h3 class="section-title mt-5" id="account">Account Management</h3>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                How do I update my personal information?
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Keeping your information up-to-date is important for your dental care. To update your personal information:</p>
                                <ol>
                                    <li>Log in to your account</li>
                                    <li>Navigate to "My Profile" from the dashboard</li>
                                    <li>Click on the Pencil button next to the information you want to update</li>
                                    <li>Make your changes and click "Save"</li>
                                </ol>
                                <p>Your updated information will be reflected in our system.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                I forgot my password. How do I reset it?
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>If you've forgotten your password, follow these steps to reset it:</p>
                                <ol>
                                    <li>Click on "Forgot Password" on the login page</li>
                                    <li>Enter your registered email address</li>
                                    <li>Check your email for a One Time Password (OTP)</li>
                                    <li>Input the OTP and create a new password</li>
                                </ol>
                                <p>For security reasons, password reset OTP expire after 24 hours. If you don't see the email, please check your spam folder.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                How can I view my appointment history?
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>To view your complete appointment history:</p>
                                <ol>
                                    <li>Log in to your account</li>
                                    <li>Navigate to "My Appointments" in the dasboard (3 lines in top right corner)</li>
                                    <li>And you will see the table.</li>
                                </ol>
                                <p>Your appointment history includes details such as appointment dates, dentist, status, and your oral concerns on that appointment. </p>
                            </div>
                        </div>

                        <!-- Technical Support Section -->
                        <h3 class="section-title mt-5" id="technical">Technical Support</h3>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                The website is not loading properly. What should I do?
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>If you're experiencing technical issues with our website, try these troubleshooting steps:</p>
                                <ul>
                                    <li>Try refreshing the page</li>
                                    <li>Clear your browser cache and cookies</li>
                                    <li>Try using a different browser</li>
                                    <li>Check your internet connection</li>
                                    <li>If the issue persists, contact our technical support team</li>
                                </ul>
                                <p>Our website works best with the latest versions of Chrome, Firefox, Safari, and Edge browsers.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                I'm not receiving email notifications. What could be wrong?
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>If you're not receiving email notifications from us, please check the following:</p>
                                <ul>
                                    <li>Check your spam or junk folder</li>
                                    <li>Add our email domain to your safe senders list</li>
                                    <li>Verify that your email address is correct in your profile</li>
                                    <li>Check if you have email notifications enabled in your account settings</li>
                                </ul>
                                <p>If you've tried all these steps and still aren't receiving emails, please contact our support team for assistance.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                How can I contact customer support?
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Our customer support team is available to assist you with any questions or issues:</p>
                                <div class="row mt-3">
                                    <div class="col-md-6 mb-3">
                                        <div class="card text-center p-3">
                                            <i class="fas fa-envelope fa-2x mb-3" style="color: var(--accent);"></i>
                                            <h5>Email</h5>
                                            <p>support@whitefields.com</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="card text-center p-3">
                                            <i class="fas fa-phone fa-2x mb-3" style="color: var(--accent);"></i>
                                            <h5>Phone</h5>
                                            <p>(123) 456-7890</p>
                                        </div>
                                    </div>
                                </div>
                                <p>Our support hours are Monday to Friday, 9:00 AM to 6:00 PM, and Saturday, 9:00 AM to 1:00 PM.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Still Need Help -->
            <div class="row mt-5">
                <div class="col-lg-12">
                    <div class="card p-4 text-center contact-card">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center mb-4 mb-md-0">
                                <div class="logo-container mx-auto">
                                    <img src="images/logo.png" alt="White Fields Dental Clinic Logo">
                                </div>
                                <h4>Dental Clinic</h4>
                                <p>Where Dental Care Meets Comfort and Compassion</p>
                            </div>
                            <div class="col-md-8">
                                <h4>Still Have Questions?</h4>
                                <p>If you couldn't find the answer to your question, please don't hesitate to contact us. Our team is always ready to assist you.</p>
                                <div class="d-flex justify-content-center">
                                    <a href="#" class="btn btn-primary">Contact Us</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>White Fields Dental Clinic</h5>
                    <p>Your trusted partner for comprehensive dental care and treatment.</p>
                    <div class="d-flex">
                        <a href="#" class="me-3 text-dark"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="me-3 text-dark"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="me-3 text-dark"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-dark"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-dark">Home</a></li>
                        <li><a href="#" class="text-dark">Services</a></li>
                        <li><a href="#" class="text-dark">About Us</a></li>
                        <li><a href="#" class="text-dark">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-map-marker-alt me-2"></i> 123 Dental Street, White Fields</li>
                        <li><i class="fas fa-phone me-2"></i> (123) 456-7890</li>
                        <li><i class="fas fa-envelope me-2"></i> info@whitefields.com</li>
                        <li><i class="fas fa-clock me-2"></i> Mon-Fri: 9am-6pm, Sat: 9am-1pm</li>
                    </ul>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-12 text-center">
                    <p class="mb-0">&copy; 2025 White Fields Dental Clinic. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // FAQ Accordion Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const questions = document.querySelectorAll('.faq-question');
            
            questions.forEach(question => {
                question.addEventListener('click', function() {
                    const answer = this.nextElementSibling;
                    const isOpen = answer.classList.contains('active');
                    
                    // Close all answers
                    document.querySelectorAll('.faq-answer').forEach(item => {
                        item.classList.remove('active');
                    });
                    
                    // Reset all icons
                    document.querySelectorAll('.faq-question i').forEach(icon => {
                        icon.className = 'fas fa-chevron-down';
                    });
                    
                    // If the clicked question wasn't open, open it
                    if (!isOpen) {
                        answer.classList.add('active');
                        this.querySelector('i').className = 'fas fa-chevron-up';
                    }
                });
            });
            
            // Search functionality
            const searchBox = document.querySelector('.search-box');
            searchBox.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const faqItems = document.querySelectorAll('.faq-item');
                
                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question').textContent.toLowerCase();
                    const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                    
                    if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>