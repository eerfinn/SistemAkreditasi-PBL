<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>SiAkred - Sistem Akreditasi D4 SIB Polinema</title>
    <meta name="description" content="Sistem Akreditasi Program Studi D4 Sistem Informasi Bisnis - Jurusan Teknologi Informasi Politeknik Negeri Malang">
    <meta name="keywords" content="akreditasi, sistem informasi, politeknik negeri malang, D4 SIB">

    <!-- Favicons -->
    <link href="{{ asset('assets\images\Jti_polinema.png') }}" rel="icon">
    <link href="{{ asset('Selecao/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('Selecao/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('Selecao/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('Selecao/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('Selecao/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #1e293b;
            --light: #f8fafc;
            --dark: #0f172a;
            --gray: #64748b;
            --light-gray: #e2e8f0;
            --accent: #f59e0b;
            --success: #10b981;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--secondary);
            line-height: 1.6;
            background-color: #ffffff;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
        }

        /* Header */
        .navbar {
            padding: 1.5rem 0;
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 1rem 0;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            font-weight: 700;
        }

        .navbar-brand img {
            height: 40px;
            margin-right: 12px;
        }

        .navbar-brand .sitename {
            font-size: 1.5rem;
            color: var(--dark);
            letter-spacing: -0.5px;
        }

        .nav-link {
            font-weight: 500;
            color: var(--secondary);
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            position: relative;
        }

        .nav-link:before {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-link:hover:before,
        .nav-link.active:before {
            width: 100%;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 0.625rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
            padding: 0.625rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        /* Hero Section */
        .hero {
            position: relative;
            height: 100vh;
            min-height: 700px;
            display: flex;
            align-items: center;
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)),
                        url('{{ asset("Selecao/assets/img/Gedung JTI.jpg") }}') no-repeat center center;
            background-size: cover;
            color: white;
        }

        .hero-content {
            max-width: 800px;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
        }

        /* Section Styling */
        .section {
            padding: 6rem 0;
            position: relative;
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            color: var(--dark);
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }

        .section-title h2:after {
            content: '';
            position: absolute;
            width: 60px;
            height: 4px;
            background: var(--primary);
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }

        .section-title p {
            color: var(--gray);
            font-size: 1.125rem;
            max-width: 700px;
            margin: 0 auto;
        }

        /* About Section */
        .about-img {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .about-content {
            padding-left: 2rem;
        }

        .about-content h3 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .about-content p {
            color: var(--gray);
            margin-bottom: 1.5rem;
        }

        .feature-list {
            margin-top: 2rem;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .feature-icon {
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        /* Stats Section */
        .stats {
            background-color: var(--light);
            padding: 4rem 0;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .stat-title {
            color: var(--gray);
            font-size: 1rem;
        }

        /* Features Section */
        .features-tabs {
            border: none;
            justify-content: center;
            margin-bottom: 3rem;
        }

        .features-tabs .nav-link {
            border: none;
            padding: 1rem 2rem;
            color: var(--gray);
            font-weight: 600;
            position: relative;
            background: none;
        }

        .features-tabs .nav-link.active {
            color: var(--primary);
            background: none;
        }

        .features-tabs .nav-link.active:after {
            content: '';
            position: absolute;
            width: 100%;
            height: 3px;
            bottom: 0;
            left: 0;
            background: var(--primary);
            border-radius: 3px 3px 0 0;
        }

        .feature-tab-content {
            background: white;
            border-radius: 12px;
            padding: 3rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .feature-tab-img {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Contact Section */
        .contact-info {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            height: 100%;
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .contact-info-icon {
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .contact-info-content h3 {
            font-size: 1.125rem;
            margin-bottom: 0.25rem;
            color: var(--dark);
        }

        .contact-info-content p {
            color: var(--gray);
            margin-bottom: 0;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        .map-container {
            height: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            min-height: 400px;
            border: none;
        }

        /* Footer */
        .footer {
            background-color: var(--dark);
            color: white;
            padding: 5rem 0 2rem;
        }

        .footer-logo {
            margin-bottom: 1.5rem;
        }

        .footer-logo a {
            display: flex;
            align-items: center;
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
            text-decoration: none;
        }

        .footer-logo img {
            height: 40px;
            margin-right: 12px;
        }

        .footer p {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 2rem;
        }

        .footer-links h4 {
            color: white;
            font-size: 1.125rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.75rem;
        }

        .footer-links h4:after {
            content: '';
            position: absolute;
            width: 40px;
            height: 2px;
            background: var(--primary);
            bottom: 0;
            left: 0;
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }

        .footer-newsletter input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 8px;
            margin-bottom: 1rem;
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .footer-newsletter input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .footer-newsletter button {
            width: 100%;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 8px;
            background: var(--primary);
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .footer-newsletter button:hover {
            background: var(--primary-dark);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 2rem;
            margin-top: 3rem;
            text-align: center;
        }

        .footer-bottom p {
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 0;
        }

        /* Back to Top */
        .back-to-top {
            position: fixed;
            right: 2rem;
            bottom: 2rem;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .back-to-top.active {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
        }

        /* Responsive Adjustments */
        @media (max-width: 1199.98px) {
            .hero h1 {
                font-size: 3rem;
            }
        }

        @media (max-width: 991.98px) {
            .hero {
                min-height: 600px;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .section {
                padding: 4rem 0;
            }

            .about-content {
                padding-left: 0;
                margin-top: 2rem;
            }
        }

        @media (max-width: 767.98px) {
            .hero {
                min-height: 500px;
                text-align: center;
            }

            .hero h1 {
                font-size: 2.25rem;
            }

            .hero-buttons {
                justify-content: center;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .features-tabs .nav-link {
                padding: 0.75rem 1rem;
            }
        }

        @media (max-width: 575.98px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero-buttons {
                flex-direction: column;
                gap: 1rem;
            }

            .footer {
                padding: 3rem 0 1rem;
            }

            .footer-links {
                margin-bottom: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('assets\images\Jti_polinema.png') }}" alt="Polinema Logo">
                <span class="sitename">SiAkred</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" onclick="scrollToTop()">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Visi Misi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Kontak</a>
                    </li>
                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a class="btn btn-primary" href="/login">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content" data-aos="fade-up" data-aos-delay="100">
                <h1>Sistem Akreditasi D4 Sistem Informasi Bisnis</h1>
                <p>Jurusan Teknologi Informasi Politeknik Negeri Malang</p>
                <div class="hero-buttons">
                    <a href="#about" class="btn btn-primary">Selengkapnya</a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Profil Program Studi</h2>
                <p>D4 Sistem Informasi Bisnis - Jurusan Teknologi Informasi</p>
            </div>

            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="about-img">
                        <img src="{{ asset('assets\images\IMG_1397.JPG') }}" alt="Kelas SIB" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="about-content">
                        <h3>Tentang Program Studi Kami</h3>
                        <p>Berawal dari Fakultas Non Gelar Teknologi Universitas Brawijaya yang beroperasi setelah disahkannya Surat Keputusan Presiden Republik Indonesia No. 59 Tahun 1982, Politeknik Negeri Malang saat ini telah berkembang menjadi institusi pendidikan vokasi mandiri.</p>
                        <p>Perubahan status tersebut tercantum dalam Surat Keputusan Menteri Pendidikan dan Kebudayaan No. 0313/O/1991. Politeknik Negeri Malang berupaya secara terus menerus untuk melakukan perubahan ke arah perbaikan, khususnya dalam bidang Pendidikan, Penelitian dan Pengabdian kepada Masyarakat yang berorientasi pada teknologi terapan.</p>

                        <div class="feature-list">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <div>
                                    <h5>Akreditasi A (BAN-PT)</h5>
                                    <p class="text-muted">SK No. 409/SK/BAN-PT/Akred/PT/XII/2018</p>
                                </div>
                            </div>

                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="bi bi-check-lg"></i>
                                </div>
                                <div>
                                    <h5>Akreditasi Internasional</h5>
                                    <p class="text-muted">ASIC Accreditation for 20 Study Programs</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="section stats">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stat-number">120+</div>
                        <div class="stat-title">Mahasiswa Aktif</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-award"></i>
                        </div>
                        <div class="stat-number">15+</div>
                        <div class="stat-title">Dosen Berkompeten</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="stat-number">50+</div>
                        <div class="stat-title">Mata Kuliah</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="stat-number">10+</div>
                        <div class="stat-title">Mitra Industri</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="section bg-light">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Visi & Misi</h2>
                <p>Program Studi D4 Sistem Informasi Bisnis</p>
            </div>

            <ul class="nav nav-pills features-tabs mb-4" id="features-tab" role="tablist" data-aos="fade-up" data-aos-delay="100">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="vision-tab" data-bs-toggle="pill" data-bs-target="#vision" type="button" role="tab" aria-controls="vision" aria-selected="true">
                        <i class="bi bi-eye me-2"></i>Visi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="mission-tab" data-bs-toggle="pill" data-bs-target="#mission" type="button" role="tab" aria-controls="mission" aria-selected="false">
                        <i class="bi bi-bullseye me-2"></i>Misi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="goals-tab" data-bs-toggle="pill" data-bs-target="#goals" type="button" role="tab" aria-controls="goals" aria-selected="false">
                        <i class="bi bi-flag me-2"></i>Tujuan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="targets-tab" data-bs-toggle="pill" data-bs-target="#targets" type="button" role="tab" aria-controls="targets" aria-selected="false">
                        <i class="bi bi-bar-chart me-2"></i>Sasaran
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="features-tabContent" data-aos="fade-up" data-aos-delay="200">
                <div class="tab-pane fade show active" id="vision" role="tabpanel" aria-labelledby="vision-tab">
                    <div class="feature-tab-content">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <h3 class="mb-4">Visi Program Studi</h3>
                                <div class="d-flex align-items-start mb-4">
                                    <div class="me-3 text-primary">
                                        <i class="bi bi-check-circle-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">Menjadi Program Studi yang Unggul dalam Bidang Sistem Informasi Bisnis Baik di Tingkat Nasional Maupun Internasional</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mt-4 mt-lg-0">
                                <div class="feature-tab-img">
                                    <img src="{{ asset('assets\images\IMG_1393.JPG') }}" alt="Visi SIB" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="mission" role="tabpanel" aria-labelledby="mission-tab">
                    <div class="feature-tab-content">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <h3 class="mb-4">Misi Program Studi</h3>

                                <div class="d-flex align-items-start mb-4">
                                    <span class="badge bg-primary rounded-circle me-3 mt-1">1</span>
                                    <div>
                                        <h5>Pendidikan Vokasi Inovatif</h5>
                                        <p class="mb-0">Melaksanakan pendidikan vokasi yang inovatif berdasarkan pada sistem pendidikan terapan dengan memanfaatkan kemajuan teknologi.</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start mb-4">
                                    <span class="badge bg-primary rounded-circle me-3 mt-1">2</span>
                                    <div>
                                        <h5>Penelitian Terapan</h5>
                                        <p class="mb-0">Menyelenggarakan penelitian terapan berbasis produk dan jasa bidang Sistem informasi bisnis.</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-start mb-4">
                                    <span class="badge bg-primary rounded-circle me-3 mt-1">3</span>
                                    <div>
                                        <h5>Pengabdian Masyarakat</h5>
                                        <p class="mb-0">Melaksanakan pengabdian masyarakat dengan menggunakan kemajuan bidang Sistem informasi bisnis.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mt-4 mt-lg-0">
                                <div class="feature-tab-img">
                                    <img src="{{ asset('assets\images\IMG_1397.JPG') }}" alt="Misi SIB" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="goals" role="tabpanel" aria-labelledby="goals-tab">
                    <div class="feature-tab-content">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <h3 class="mb-4">Tujuan Program Studi</h3>

                                <div class="accordion" id="goalsAccordion">
                                    <div class="accordion-item mb-3 border-0">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                Lulusan Berkualitas
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#goalsAccordion">
                                            <div class="accordion-body">
                                                Menghasilkan lulusan bidang sistem informasi bisnis yang sesuai kebutuhan, beretika dan bermoral baik, berpengetahuan dan berketerampilan tinggi.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item mb-3 border-0">
                                        <h2 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                Penelitian Bermutu
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#goalsAccordion">
                                            <div class="accordion-body">
                                                Menghasilkan penelitian terapan bidang sistem informasi bisnis yang berskala nasional dan internasional, meningkatkan efektivitas, efisiensi, dan produktivitas dalam dunia usaha dan industri.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="accordion-item border-0">
                                        <h2 class="accordion-header" id="headingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                Pengabdian Masyarakat
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#goalsAccordion">
                                            <div class="accordion-body">
                                                Menghasilkan pengabdian kepada masyarakat yang dilaksanakan melalui penerapan dan penyebarluasan ilmu pengetahuan dan teknologi.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mt-4 mt-lg-0">
                                <div class="feature-tab-img">
                                    <img src="{{ asset('assets\images\IMG_1395.JPG') }}" alt="Tujuan SIB" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="targets" role="tabpanel" aria-labelledby="targets-tab">
                    <div class="feature-tab-content">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <h3 class="mb-4">Sasaran Program Studi</h3>

                                <ul class="list-unstyled">
                                    <li class="d-flex mb-4">
                                        <div class="me-3 text-primary">
                                            <i class="bi bi-check-circle-fill fs-4"></i>
                                        </div>
                                        <div>
                                            <h5>Peningkatan Akses Pendidikan</h5>
                                            <p class="mb-0">Meningkatnya akses relevansi, kuantitas, dan kualitas Pendidikan Program Studi D4 - SIB</p>
                                        </div>
                                    </li>

                                    <li class="d-flex mb-4">
                                        <div class="me-3 text-primary">
                                            <i class="bi bi-check-circle-fill fs-4"></i>
                                        </div>
                                        <div>
                                            <h5>Pembelajaran Berkualitas</h5>
                                            <p class="mb-0">Meningkatnya relevansi dan kualitas kegiatan pembelajaran di Program Studi D4 - SIB</p>
                                        </div>
                                    </li>

                                    <li class="d-flex">
                                        <div class="me-3 text-primary">
                                            <i class="bi bi-check-circle-fill fs-4"></i>
                                        </div>
                                        <div>
                                            <h5>Kegiatan Kemahasiswaan</h5>
                                            <p class="mb-0">Meningkatnya kualitas hasil kegiatan kemahasiswaan D4 - SIB dan inisiasi pembinaan karier untuk pembekalan lulusan.</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-6 mt-4 mt-lg-0">
                                <div class="feature-tab-img">
                                    <img src="{{ asset('assets\images\IMG_1393.JPG') }}" alt="Sasaran SIB" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Kontak Kami</h2>
                <p>Hubungi Program Studi D4 Sistem Informasi Bisnis</p>
            </div>

            <div class="row gy-4">
                <div class="col-lg-5" data-aos="fade-up" data-aos-delay="100">
                    <div class="contact-info">
                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div class="contact-info-content">
                                <h3>Alamat</h3>
                                <p>Jl. Soekarno Hatta NO.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <div class="contact-info-content">
                                <h3>Telepon</h3>
                                <p>(0341) 404424 - 404425</p>
                            </div>
                        </div>

                        <div class="contact-info-item">
                            <div class="contact-info-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div class="contact-info-content">
                                <h3>Email</h3>
                                <p>humas@polinema.ac.id</p>
                            </div>
                        </div>

                        <div class="social-links">
                            <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
                            <a href="https://www.instagram.com/polinema_campus/" class="social-link"><i class="bi bi-instagram"></i></a>
                            <a href="https://www.linkedin.com/school/polinema-joss/" class="social-link"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7" data-aos="fade-up" data-aos-delay="200">
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.3141822078877!2d112.6154143147792!3d-7.966446794256785!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7882865d069f6f%3A0x1030bfbc9a7a8216!2sPoliteknik%20Negeri%20Malang!5e0!3m2!1sen!2sid!4v1634025432537!5m2!1sen!2sid" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

 <!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="footer-logo mb-4">
                    <a href="#" class="d-inline-flex align-items-center justify-content-center">
                        <img src="{{ asset('assets/images/Jti_polinema.png') }}" alt="Polinema Logo" class="me-2" style="height: 40px;">
                        <span class="sitename" style="font-size: 1.5rem; font-weight: 700;">SiAkred</span>
                    </a>
                </div>
                <p class="mb-4 mx-auto" style="max-width: 600px;">
                    Sistem Akreditasi Program Studi D4 Sistem Informasi Bisnis<br>
                    Jurusan Teknologi Informasi Politeknik Negeri Malang
                </p>

            </div>
        </div>

        <div class="footer-bottom text-center pt-4 mt-4 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
            <p class="mb-0">&copy; 2025 SiAkred - Sistem Akreditasi D4 SIB. All Rights Reserved.</p>
        </div>
    </div>
</footer>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{ asset('Selecao/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('Selecao/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('Selecao/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>

    <!-- Main JS File -->
    <script>

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Back to top button
        window.addEventListener('scroll', function() {
            const backToTop = document.querySelector('.back-to-top');
            if (window.scrollY > 300) {
                backToTop.classList.add('active');
            } else {
                backToTop.classList.remove('active');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();

                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });

                    // Close mobile menu if open
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse.classList.contains('show')) {
                        navbarCollapse.classList.remove('show');
                    }
                }
            });
        });

        // Initialize AOS animation
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>

</body>
</html>
