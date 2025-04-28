<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Index - Selecao Bootstrap Template</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="Selecao/assets/img/favicon.png" rel="icon">
  <link href="Selecao/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{asset('Selecao/assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{asset('Selecao/assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{asset('Selecao/assets/vendor/aos/aos.css')}}" rel="stylesheet">
  <link href="{{asset('Selecao/assets/vendor/animate.css/animate.min.css')}}" rel="stylesheet">
  <link href="{{asset('Selecao/assets/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">
  <link href="{{asset('Selecao/assets/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="Selecao/assets/css/main.css" rel="stylesheet">
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
        <h1 class="sitename">SiAkred</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#hero" class="active">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#contact">Contact</a></li>
          <li><a href="/login">Login</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section"style="background-image: url('{{ asset('Selecao/assets/img/Gedung JTI.jpg') }}'); background-size: cover; background-position: center;">
      <div id="hero-carousel" data-bs-interval="5000" class="container carousel carousel-fade" data-bs-ride="carousel">
        <div class="carousel-item active">
          <div class="carousel-container">
            <h2 class="animate__animated animate__fadeInDown">
              AKREDITASI D4 SISTEM INFORMASI BISNIS<br>JURUSAN TEKNOLOGI INFORMASI<br>POLITEKNIK NEGERI MALANG
            </h2>
            <a href="#about" class="btn-get-started animate__animated animate__fadeInUp scrollto">Read More</a>
          </div>
        </div>
      </div>
      <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28 " preserveAspectRatio="none">
        <defs>
          <path id="wave-path" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"></path>
        </defs>
        <g class="wave1">
          <use xlink:href="#wave-path" x="50" y="3"></use>
        </g>
        <g class="wave2">
          <use xlink:href="#wave-path" x="50" y="0"></use>
        </g>
        <g class="wave3">
          <use xlink:href="#wave-path" x="50" y="9"></use>
        </g>
      </svg>
    </section><!-- /Hero Section -->
    
    

    <!-- About Section -->
    <section id="about" class="about section">
      <div class="container section-title" data-aos="fade-up">
        <h2>About</h2>
        <p>PROFIL</p>
      </div>

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
            <p>
              Berawal dari Fakultas Non Gelar Teknologi Universitas Brawijaya yang beroperasi setelah disahkannya Surat Keputusan Presiden Republik Indonesia No. 59 Tahun 1982, Politeknik Negeri Malang saat ini telah berkembang menjadi institusi pendidikan vokasi mandiri.
            </p>
            <p>
              Perubahan status tersebut tercantum dalam Surat Keputusan Menteri Pendidikan dan Kebudayaan No. 0313/O/1991. Politeknik Negeri Malang berupaya secara terus menerus untuk melakukan perubahan ke arah perbaikan, khususnya dalam bidang Pendidikan, Penelitian dan Pengabdian kepada Masyarakat yang berorientasi pada teknologi terapan. Usaha tersebut menunjukkan hasil yang positif, yang ditunjukkan dengan pencapaian akreditasi A pada tahun 2018 (SK 409/SK/BANPT/Akred/PT/XII/2018) dan akreditasi internasional ASIC (Acreditation Service for International School Collage and University) pada tahun 2020 untuk 20 program studi.
            </p>
          </div>

          <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
            <p>Program studi D4-SIB didirikan pada tahun 2010 berdasarkanSuratKeputusanMenteriPendidikanNasionalno.50/D/O/2010.Padaawalnyaberdirinya,program studi D4 Sistem informasi bisnis berada di bawah jurusan Teknik Elektro, Politeknik NegeriMalang, sebelumpadaakhirnyamulaitahun2015, setelahdidirikannyajurusanTeknologiInformasi, program studi D4-SIB masuk ke dalamnya. Pada tahun 2018, program studi D4-SIBmendapatkan peringkat B untuk akreditasi program studi dari BAN-PT, berdasarkan SK Nomor1810/SK/BANPT/Akred/DiplIV/VII/2018.</p>
            {{-- <a href="#" class="read-more"><span>Read More</span><i class="bi bi-arrow-right"></i></a> --}}
          </div>
        </div>
      </div>
    </section><!-- /About Section -->

    <!-- Features Section -->
    <section id="features" class="features section">
      <div class="container">
        <ul class="nav nav-tabs row d-flex" data-aos="fade-up" data-aos-delay="100">
          <li class="nav-item col-3">
            <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#features-tab-1">
              <i class="bi bi-binoculars"></i>
              <h4 class="d-none d-lg-block">VISI</h4>
            </a>
          </li>
          <li class="nav-item col-3">
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-2">
              <i class="bi bi-box-seam"></i>
              <h4 class="d-none d-lg-block">MISI</h4>
            </a>
          </li>
          <li class="nav-item col-3">
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-3">
              <i class="bi bi-brightness-high"></i>
              <h4 class="d-none d-lg-block">TUJUAN</h4>
            </a>
          </li>
          <li class="nav-item col-3">
            <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-4">
              <i class="bi bi-command"></i>
              <h4 class="d-none d-lg-block">SASARAN</h4>
            </a>
          </li>
        </ul>

        <div class="tab-content" data-aos="fade-up" data-aos-delay="200">
          <div class="tab-pane fade active show" id="features-tab-1">
            <div class="row">
              <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
                <h3>Menjadi Program Studi yang Unggul dalam Bidang Sistem informasi bisnis Baik di Tingkat Nasional Maupun Internasional</h3>
              </div>
              <div class="col-lg-6 order-1 order-lg-2 text-center">
                <img src="Selecao/assets/img/working-1.jpg" alt="" class="img-fluid">
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="features-tab-2">
            <div class="row">
              <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
                <h3>1. Melaksanakan pendidikan vokasi yang inovatif berdasarkan pada sistem pendidikan terapan dengan memanfaatkan kemajuan teknologi, sehingga mampu menghasilkan lulusan yang memiliki kompetensi di bidang sistem informasi bisnis dan siap bersaing di tingkat nasional dan global.</h3><br>
                <h3>2. Menyelenggarakan penelitian terapan berbasis produk dan jasa bidang Sistem informasi bisnis.</h3><br>
                <h3>3. Melaksanakan pengabdian masyarakat dengan menggunakan kemajuan bidang Sistem informasi bisnis untuk meningkatkan kesejahteraan.</h3><br>
                <h3>4. Mewujudkan kerjasama yang saling menguntungkan dengan berbagai pihak baik didalam maupun diluar negeri pada bidang sistem informasi bisnis</h3>
              </div>
              <div class="col-lg-6 order-1 order-lg-2 text-center">
                <img src="Selecao/assets/img/working-2.jpg" alt="" class="img-fluid">
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="features-tab-3">
            <div class="row">
              <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
                <h3>1. Menghasilkan lulusan bidang sistem informasi bisnis yang sesuai kebutuhan, beretika dan bermoral baik, berpengetahuan dan berketerampilan tinggi, siap bekerja dan/atau berwirausaha yang mampu bersaing dalam skala nasional dan global;</h3><br>
                <h3>2. Menghasilkan penelitian terapan bidang sistem informasi bisnis yang berskala nasional dan internasional, meningkatkan efektivitas, efisiensi, dan produktivitas dalam dunia usaha dan industri, serta mengarah pada pencapaian Hak atas Kekayaan Intelektual (HaKI), perolehan paten, dan kesejahteraan masyarakat;</h3><br>
                <h3>3. Menghasilkan pengabdian kepada masyarakat yang dilaksanakan melalui penerapan dan penyebarluasan ilmu pengetahuan dan teknologi serta pemberian layanan hasil secara profesional dalam bidang sistem informasi bisnis sehingga bermanfaat secara langsung dalam meningkatkan kesejahteraan masyarakat;</h3><br>
                <h3>4. Menghasilkan sistem manajemen pendidikan bidang sistem informasi bisnis yang memenuhi prinsip-prinsip tata kelola yang baik;</h3><br>
                <h3>5. Terwujudnya kerja sama yang saling menguntungkan dengan berbagai pihak baik di dalam maupun di luar negeri pada bidang sistem informasi bisnis untuk meningkatkan daya saing.</h3>
              </div>
              <div class="col-lg-6 order-1 order-lg-2 text-center">
                <img src="Selecao/assets/img/working-3.jpg" alt="" class="img-fluid">
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="features-tab-4">
            <div class="row">
              <div class="col-lg-6 order-2 order-lg-1 mt-3 mt-lg-0">
                <h3>1. Meningkatnya akses relevansi, kuantitas, dan kualitas Pendidikan Program Studi D4 - SIB</h3><br>
                <h3>2. Meningkatnya relevansi dan kualitas kegiatan pembelajaran di Program Studi D4 - SIB</h3><br>
                <h3>3. Meningkatnya kualitas hasil kegiatan kemahasiswaan D4 - SIB dan inisiasi pembinaan karier untuk pembekalan lulusan.</h3><br>
                <h3>4. Meningkatnya relevansi, kuantitas, kualitas, dan kemanfaatan hasil penelitian seluruh sivitas akademika.</h3><br>
                <h3>5. Meningkatnya relevansi, kuantitas, kualitas, dan kemanfaatan hasil pengabdian kepada masyarakat untuk kesejahteraan masyarakat.</h3>
              </div>
              <div class="col-lg-6 order-1 order-lg-2 text-center">
                <img src="Selecao/assets/img/working-4.jpg" alt="" class="img-fluid">
              </div>
            </div>
          </div>
        </div>
      </div>
    </section><!-- /Features Section -->

    <!-- Contact Section -->
    <section id="contact" class="contact section">
      <div class="container section-title" data-aos="fade-up">
        <h2>Contact</h2>
        <p>Contact Us</p>
      </div>

      <div class="container" data-aos="fade" data-aos-delay="100">
        <div class="row gy-4">
          <div class="col-lg-4">
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
              <i class="bi bi-geo-alt flex-shrink-0"></i>
              <div>
                <h3>Address</h3>
                <p>Jl. Soekarno Hatta NO.9, Jatimulyo, Kec. Lowokwaru, Kota Malang, Jawa Timur 65141</p>
              </div>
            </div>

            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-telephone flex-shrink-0"></i>
              <div>
                <h3>Call Us</h3>
                <p>+1 5589 55488 55</p>
              </div>
            </div>

            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-envelope flex-shrink-0"></i>
              <div>
                <h3>Email Us</h3>
                <p>humas@polinema.ac.id</p>
              </div>
            </div>
          </div>

          <div class="col-lg-8">
        </div>
      </div>
    </section><!-- /Contact Section -->

  </main>

  <footer id="footer" class="footer dark-background">
    <div class="container">
      <div class="copyright">
        <span>Copyright</span> <strong class="px-1 sitename">Sistem Akreditasi 2025</strong>
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="Selecao/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="Selecao/assets/vendor/php-email-form/validate.js"></script>
  <script src="Selecao/assets/vendor/aos/aos.js"></script>
  <script src="Selecao/assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="Selecao/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="Selecao/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="Selecao/assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="Selecao/assets/js/main.js"></script>

</body>

</html>