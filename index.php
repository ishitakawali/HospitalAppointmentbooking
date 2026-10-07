<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MediCare | Hospital Appointment Booking</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <!-- NAVBAR -->

    <header class="navbar">

        <div class="logo">
            <span class="logo-icon">✚</span>
            <span>MediCare</span>
        </div>

        <nav>
            <a href="index.php" class="active">Home</a>
            <a href="#services">Services</a>
            <a href="#departments">Departments</a>
            <a href="#how-it-works">How It Works</a>
        </nav>

        <div class="nav-buttons">
            <a href="login.php" class="login-btn">Login</a>
            <a href="register.php" class="register-btn">Register</a>
        </div>

    </header>


    <!-- HERO SECTION -->

    <section class="hero">

        <div class="hero-content">

            <div class="small-heading">
                <span>✦</span> Your health matters to us
            </div>

            <h1>
                Healthcare made
                <span>simple & accessible.</span>
            </h1>

            <p>
                Find trusted doctors, check available schedules and
                book your hospital appointments — all in one place.
            </p>

            <div class="hero-buttons">

                <a href="doctors.php" class="primary-btn">
                    Book an Appointment
                    <span>→</span>
                </a>

                <a href="doctors.php" class="secondary-btn">
                    Find a Doctor
                </a>

            </div>

            <div class="hero-features">

                <div>
                    <span>✓</span>
                    Easy Booking
                </div>

                <div>
                    <span>✓</span>
                    Trusted Doctors
                </div>

                <div>
                    <span>✓</span>
                    Convenient Scheduling
                </div>

            </div>

        </div>


        <div class="hero-image">

            <div class="image-card">

                <img
                    src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=900&q=85"
                    alt="Doctor">

                <div class="doctor-card">

                    <div class="doctor-icon">🩺</div>

                    <div>
                        <strong>Expert Care</strong>
                        <small>When you need it</small>
                    </div>

                    <span class="green-dot"></span>

                </div>

            </div>

        </div>

    </section>


    <!-- SERVICES -->

    <section class="section" id="services">

        <div class="section-heading">

            <span>OUR SERVICES</span>

            <h2>Everything you need,<br>
                <em>in one place.</em>
            </h2>

            <p>
                A simple way to manage your healthcare appointments.
            </p>

        </div>


        <div class="service-grid">

            <div class="service-card blue">
                <div class="service-icon">📅</div>
                <h3>Easy Appointment Booking</h3>
                <p>
                    Choose a doctor, select a convenient time slot
                    and book your appointment easily.
                </p>
            </div>


            <div class="service-card lavender">
                <div class="service-icon">👨‍⚕️</div>
                <h3>Find the Right Doctor</h3>
                <p>
                    Browse doctors by specialization and view
                    their available schedules.
                </p>
            </div>


            <div class="service-card peach">
                <div class="service-icon">🔔</div>
                <h3>Appointment Management</h3>
                <p>
                    View, reschedule or cancel your upcoming
                    appointments whenever required.
                </p>
            </div>

        </div>

    </section>


    <!-- DEPARTMENTS -->

    <section class="departments" id="departments">

        <div class="section-heading">

            <span>MEDICAL DEPARTMENTS</span>

            <h2>Care for every <em>need.</em></h2>

        </div>


        <div class="department-grid">

            <div class="department-card">
                <span>❤️</span>
                <h3>Cardiology</h3>
                <p>Heart & cardiovascular care</p>
            </div>

            <div class="department-card">
                <span>🧠</span>
                <h3>Neurology</h3>
                <p>Brain & nervous system care</p>
            </div>

            <div class="department-card">
                <span>🦴</span>
                <h3>Orthopedics</h3>
                <p>Bones, joints & muscles</p>
            </div>

            <div class="department-card">
                <span>🌿</span>
                <h3>Dermatology</h3>
                <p>Skin & hair care</p>
            </div>

        </div>

    </section>


    <!-- HOW IT WORKS -->

    <section class="how-it-works" id="how-it-works">

        <div class="section-heading">

            <span>HOW IT WORKS</span>

            <h2>Three simple steps to<br>
                <em>better healthcare.</em>
            </h2>

        </div>


        <div class="steps">

            <div class="step">

                <div class="step-number">01</div>

                <h3>Create your account</h3>

                <p>
                    Register as a patient and securely manage
                    your appointments.
                </p>

            </div>


            <div class="step">

                <div class="step-number">02</div>

                <h3>Choose your doctor</h3>

                <p>
                    Browse doctors and check their available
                    dates and time slots.
                </p>

            </div>


            <div class="step">

                <div class="step-number">03</div>

                <h3>Book your appointment</h3>

                <p>
                    Select a suitable slot and receive your
                    appointment confirmation.
                </p>

            </div>

        </div>

    </section>


    <!-- CALL TO ACTION -->

    <section class="cta">

        <div>

            <span>READY TO GET STARTED?</span>

            <h2>Your health,<br>our priority.</h2>

        </div>

        <a href="register.php">
            Create Your Account →
        </a>

    </section>


    <!-- FOOTER -->

    <footer>

        <div class="footer-logo">
            <span>✚</span> MediCare
        </div>

        <p>
            Hospital Appointment Booking System
        </p>

        <p class="copyright">
            © 2026 MediCare. All rights reserved.
        </p>

    </footer>

</body>

</html>