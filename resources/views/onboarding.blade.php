<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concord Hospital</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?fam ily=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/pages/onboarding.css'])
@endif

</head>
<body>
<!-- Header -->
<header>
    <div class="container">
        <nav>
            <div class="logo">Concord Hospital</div>
            <ul>
                <li>
                    <a href="#home" class="btn btn-call">
                        <i class="bi bi-telephone-fill"></i> Call Us
                    </a>
                </li>
                <li>
                    <a href="#appointments" class="btn btn-book">
                        <i class="bi bi-calendar-check-fill"></i> Book Now
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<!-- Sub Navigation -->
<div class="sub-nav">
    <div class="container">
        <a href="#home" class="sub-link">Home</a>
        <a href="#doctors" class="sub-link">Doctors</a>
        <a href="#appointments" class="sub-link">Appointments</a>
        <a href="#careers" class="sub-link">Careers</a>
    </div>
</div>

<!-- Hero Section -->
<section class="hero" id="home">
    <div class="container hero-content">
        <div class="hero-text">
            <h1>Trusted Care for Every Patient</h1>
            <p>
                Concord Hospital delivers compassionate, patient-centered
                healthcare with modern facilities and experienced professionals.
            </p>
        </div>

        <div class="hero-image">
            <img src="{{ asset('images/achievment.jpeg') }}" alt="Healthcare Professionals">
        </div>
    </div>
</section>


<section id="appointments" class="container appointments-section">

    <div class="appointments-header">
        <h2>Book an Appointment</h2>
        <p>Schedule your consultation with our specialists quickly and easily.</p>
    </div>

    <form action="" method="POST" class="appointment-form">
        @csrf
        <div class="form-grid">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="tel" name="phone" placeholder="Phone Number" required>
            <select name="department" required>
                <option value="">Select Department</option>
                <option value="consultancy">Consultancy</option>
                <option value="residency">Residency & Fellowship</option>
                <option value="internship">Internship</option>
            </select>
            <input type="date" name="date" required>
            <input type="time" name="time" required>
        </div>
        <button type="submit" class="btn-book">Book Now</button>
    </form>

</section>


<section id="doctors" class="container doctors-section">

    <div class="doctors-header">
        <h2>Multi-awarded Doctors</h2>
        <p>Meet our top medical professionals who deliver excellence in healthcare.</p>
    </div>

    <div class="doctors-list">
        <div class="doctor-card">
            <img src="{{ asset('images/onboarding/robert.jpeg') }}" alt="Dr. Rober">
            <h4>Dr. Robert</h4>
            <p>Cardiology</p>
        </div>

        <div class="doctor-card">
            <img src="{{ asset('images/onboarding/gilbert.jpeg') }}" alt="Dr. Kathy">
            <h4>Dr. Kathy</h4>
            <p>Neurology</p>
        </div>

        <!-- Highlighted Doctor in the middle -->
        <div class="doctor-card highlight">
            <img src="{{ asset('images/onboarding/kim.jpeg') }}" alt="Dr. Kim">
            <h4>Dr. Kim</h4>
            <p>Chief Surgeon</p>
        </div>

        <div class="doctor-card">
            <img src="{{ asset('images/onboarding/kubica.jpeg') }}" alt="Dr. Damian">
            <h4>Dr. Damian</h4>
            <p>Pediatrics</p>
        </div>

        <div class="doctor-card">
            <img src="{{ asset('images/onboarding/lebron.jpeg') }}" alt="Dr. Joshua">
            <h4>Dr. Joshua</h4>
            <p>Orthopedics</p>
        </div>
    </div>

</section>


   <section id="careers" class="container careers-section">

    <!-- Section Title -->
    <div class="careers-header">
        <h2>Careers</h2>
        <p>Join our dedicated healthcare team and make a real difference in patients' lives.</p>
    </div>

    <!-- Image + Text -->
    <div class="careers-content">
        <div class="careers-image">
            <img src="{{ asset('images/career.jpeg') }}" alt="Careers at Concord Hospital">
        </div>
        <div class="careers-text">
            <h3>Why Work With Us?</h3>
            <p>At Concord Hospital, we value compassion, excellence, and collaboration. We provide a supportive environment where healthcare professionals can grow, innovate, and deliver the best care to our patients.</p>
            <p>We offer opportunities across clinical, administrative, and support roles, ensuring a fulfilling career path for everyone.</p>
        </div>
    </div>

        <div class="careers-cards">
            <div class="career-card">
                <i class="bi bi-person-badge"></i>
                <h4>Consultancy</h4>
                <p>Deliver compassionate patient care in a supportive environment.</p>
            </div>

            <div class="career-card">
                <i class="bi bi-clipboard2-pulse"></i>
                <h4>Residency & Fellowship</h4>
                <p>Work alongside experienced physicians using modern medical technologies.</p>
            </div>

            <div class="career-card">
                <i class="bi bi-journal-text"></i>
                <h4>Internship</h4>
                <p>Support hospital operations and ensure quality healthcare delivery.</p>
            </div>
        </div>


</section>


<!-- Footer -->
<footer>
    <div class="container">
        <p>&copy; 2025 CityCare Hospital. All rights reserved.</p>
        <p>
            <a href="#privacy">Privacy Policy</a> |
            <a href="#terms">Terms of Service</a>
        </p>
    </div>
</footer>

<!-- Sub NavLink Highlight JS -->
<script>
    const sections = document.querySelectorAll("section[id]");
    const subLinks = document.querySelectorAll(".sub-link");

    window.addEventListener("scroll", () => {
        let scrollPos = window.scrollY + 150;
        sections.forEach(section => {
            if(scrollPos >= section.offsetTop && scrollPos < section.offsetTop + section.offsetHeight){
                subLinks.forEach(link => {
                    link.classList.remove("active");
                    if(link.getAttribute("href") === "#" + section.id){
                        link.classList.add("active");
                    }
                });
            }
        });
    });
</script>

</body>
</html>