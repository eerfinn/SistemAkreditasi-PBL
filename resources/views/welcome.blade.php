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
    <link href="{{ asset('assets/css/welcome.css') }}" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <header class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">
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
                    @guest
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <a class="btn btn-primary" href="/login">Login</a>
                        </li>
                    @else
                        <li class="nav-item ps-3">
                            <div class="dropdown nav-profile">
                            <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="profile-info d-flex align-items-center">
                                        <div class="profile-image">
                                        <img src="{{ Auth::user()->profile_photo_url ?? asset('assets/images/user.jpg') }}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div class="card border-0 mb-0">
                                        <div class="card-body px-0 py-2">
                                            <a href="/dashboard" class="dropdown-item ai-icon">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z" stroke="var(--primary)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M16.28 13.61C15.15 14.74 13.53 15.09 12.1 14.64L9.51001 17.22C9.33001 17.41 8.96001 17.53 8.69001 17.49L7.49001 17.33C7.09001 17.28 6.73001 16.9 6.67001 16.51L6.51001 15.31C6.47001 15.05 6.60001 14.68 6.78001 14.49L9.36001 11.91C8.92001 10.48 9.26001 8.86001 10.39 7.73001C12.01 6.11001 14.65 6.11001 16.28 7.73001C17.9 9.34001 17.9 11.98 16.28 13.61Z" stroke="var(--primary)" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M10.45 16.28L9.59998 15.42" stroke="var(--primary)" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M13.3945 10.7H13.4035" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <span class="ms-2">Dashboard</span>
                                            </a>
                                        </div>
                                        <div class="card-footer px-0 py-2">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                            <a href="javascript:void(0);" onclick="event.preventDefault(); this.closest('form').submit();" class="dropdown-item ai-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                                        <polyline points="16 17 21 12 16 7"></polyline>
                                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                                    </svg>
                                                    <span class="ms-2">Logout</span>
                                                </a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </header>

    <section id="home" class="hero" style="background-image: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.85)), url('{{ asset('assets/images/Gedung JTI.jpg') }}');">
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
                                            <i class="bi ybi-check-circle-fill fs-4"></i>
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
                            <a href="https://www.facebook.com/polinema" class="social-link"><i class="bi bi-facebook"></i></a>
                            <a href="https://www.instagram.com/polinema_campus/" class="social-link"><i class="bi bi-instagram"></i></a>
                            <a href="https://www.linkedin.com/school/polinema-joss/" class="social-link"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7" data-aos="fade-up" data-aos-delay="200">
                    <div class="map-container">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.5027343114866!2d112.61354597618481!3d-7.946885879169167!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78827687d272e7%3A0x789ce9a636cd3aa2!2sPoliteknik%20Negeri%20Malang!5e0!3m2!1sid!2sid!4v1746021758826!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
    <script src="{{ asset('assets/js/welcome.js') }}"></script>
</body>
</html>
