<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FamilyCarePlus - Edukasi Pasien Stroke</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Quicksand&display=swap" rel="stylesheet">

  <!-- AOS Animation -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

  <style>
    body {
      font-family: 'Quicksand', sans-serif;
      scroll-behavior: smooth;
    }

    /* Navbar */
    .navbar {
      background: rgba(255,255,255,0.7);
      backdrop-filter: blur(15px);
      transition: all 0.4s ease;
    }
    .navbar.scrolled {
      background: #ffffff;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .navbar-brand {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      font-size: 1.6rem;
      color: #202c60 !important;
    }
    .nav-link {
      color: #202c60 !important;
      font-weight: 600;
      transition: 0.4s;
      position: relative;
    }
    .nav-link::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      left: 0;
      bottom: -3px;
      background: #e63946;
      transition: width 0.4s ease;
    }
    .nav-link:hover::after,
    .nav-link.active::after {
      width: 100%;
    }
    .nav-link:hover {
      color: #e63946 !important;
      text-shadow: 0 0 8px rgba(230,57,70,0.4);
    }

    /* Hero */
    #hero {
      height: 100vh;
      background: url("https://source.unsplash.com/1600x900/?healthcare,doctor") no-repeat center center/cover;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      color: #fff;
      overflow: hidden;
    }
    #hero::before {
      content: "";
      position: absolute;
      top:0;left:0;right:0;bottom:0;
      background: linear-gradient(135deg, rgba(32,44,96,0.8), rgba(230,57,70,0.6));
    }
    #hero .content {
      position: relative;
      text-align: center;
      max-width: 700px;
      z-index: 2;
      animation: zoomIn 2s;
    }
    #hero h1 {
      font-family: 'Montserrat', sans-serif;
      font-size: 3rem;
      font-weight: 700;
    }
    #hero .btn {
      transition: all 0.4s ease;
    }
    #hero .btn:hover {
      transform: scale(1.1);
      box-shadow: 0 0 20px rgba(255,255,255,0.6);
    }

    /* Section */
    section {
      padding: 90px 0;
    }
    section h2 {
      font-family: 'Montserrat', sans-serif;
      font-weight: 700;
      margin-bottom: 50px;
      color: #202c60;
      text-align: center;
      position: relative;
    }
    section h2::after {
      content: "";
      display: block;
      width: 60px;
      height: 4px;
      background: #e63946;
      margin: 10px auto 0;
      border-radius: 2px;
    }

    /* Card Edukasi */
    .edu-card {
      background: #fff;
      border-radius: 20px;
      padding: 30px;
      text-align: center;
      transition: 0.4s ease;
      box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .edu-card:hover {
      transform: translateY(-12px) scale(1.05);
      box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    .edu-card i {
      font-size: 2.5rem;
      color: #e63946;
      margin-bottom: 15px;
      transition: 0.3s;
    }
    .edu-card:hover i {
      transform: rotate(15deg) scale(1.2);
    }

    /* Contact */
    #contact {
      background: linear-gradient(135deg, #f8f9fa, #eef2f3);
    }
    .contact-card {
      background: rgba(255,255,255,0.8);
      border-radius: 20px;
      padding: 40px;
      backdrop-filter: blur(10px);
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      text-align: center;
      transition: 0.4s;
    }
    .contact-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 45px rgba(0,0,0,0.15);
    }
    .contact-card a {
      color: #202c60;
      font-weight: 600;
      transition: 0.3s;
    }
    .contact-card a:hover {
      color: #e63946;
      text-shadow: 0 0 6px rgba(230,57,70,0.4);
    }

    /* Footer */
    footer {
      background: #202c60;
      color: white;
      padding: 60px 0 20px;
    }
    footer h6 {
      font-weight: 700;
      margin-bottom: 15px;
    }
    footer a {
      color: #ddd;
      text-decoration: none;
      transition: 0.3s ease;
    }
    footer a:hover {
      color: #e63946;
      padding-left: 4px;
    }
    footer .bi {
      transition: 0.3s;
    }
    footer .bi:hover {
      transform: scale(1.3);
      color: #e63946;
    }
    footer img {
      filter: grayscale(100%);
      transition: 0.4s ease;
    }
    footer img:hover {
      filter: grayscale(0%);
      transform: scale(1.1);
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav id="navbar" class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand" href="#">FamilyCarePlus</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link active" href="#hero">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="#edukasi">Edukasi</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Hubungi Kami</a></li>
        </ul>
        <a href="/login" class="btn btn-danger ms-3 rounded-pill">Login</a>
      </div>
    </div>
  </nav>

  <!-- Hero -->
  <section id="hero">
    <div class="content">
      <h1 class="animate__animated animate__fadeInDown">Edukasi Pasien Stroke</h1>
      <p class="animate__animated animate__fadeInUp mt-3">Memberikan informasi, panduan, dan motivasi untuk mendukung pasien serta keluarga dalam perjalanan pemulihan stroke.</p>
      <a href="#edukasi" class="btn btn-light mt-4 px-4 py-2 rounded-pill animate__animated animate__zoomIn">Mulai Belajar</a>
    </div>
  </section>

  <!-- Edukasi -->
  <section id="edukasi">
    <div class="container">
      <h2 data-aos="fade-up">Materi Edukasi</h2>
      <div class="row g-4">
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
          <div class="edu-card">
            <i class="bi bi-heart-pulse"></i>
            <h5>Gejala Stroke</h5>
            <p>Kenali tanda-tanda stroke sejak dini untuk mencegah komplikasi yang lebih parah.</p>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
          <div class="edu-card">
            <i class="bi bi-capsule-pill"></i>
            <h5>Manajemen Obat</h5>
            <p>Panduan penggunaan obat dengan tepat agar terapi berjalan optimal.</p>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
          <div class="edu-card">
            <i class="bi bi-bicycle"></i>
            <h5>Rehabilitasi & Latihan</h5>
            <p>Aktivitas fisik dan terapi untuk meningkatkan kualitas hidup pasien stroke.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Hubungi Kami -->
  <section id="contact">
    <div class="container">
      <h2 data-aos="fade-up">Hubungi Kami</h2>
      <div class="contact-card" data-aos="zoom-in">
        <p>📞 WhatsApp: <a href="https://wa.me/6281234567890" target="_blank">+62 812-3456-7890</a></p>
        <p>📧 Email: <a href="mailto:info@familycareplus.com">info@familycareplus.com</a></p>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer mt-5">
    <div class="container py-5">
      <div class="row">
        <!-- Informasi Layanan -->
        <div class="col-md-3 col-6 mb-4" data-aos="fade-right">
          <h6 class="fw-bold">Informasi Layanan</h6>
          <ul class="list-unstyled">
            <li><a href="#">Panduan Penggunaan</a></li>
            <li><a href="#">Edukasi</a></li>
            <li><a href="#">Unduh Aplikasi</a></li>
            <li><a href="#">Kebijakan Cookies</a></li>
          </ul>
        </div>

        <!-- Tentang Kami -->
        <div class="col-md-3 col-6 mb-4" data-aos="fade-up">
          <h6 class="fw-bold">Tentang Kami</h6>
          <ul class="list-unstyled">
            <li><a href="#">Profil Perusahaan</a></li>
            <li><a href="#">Syarat & Ketentuan</a></li>
            <li><a href="#">Kebijakan Privasi</a></li>
            <li><a href="#">Program Kemitraan</a></li>
          </ul>
        </div>

        <!-- Bantuan -->
        <div class="col-md-3 col-6 mb-4" data-aos="fade-up" data-aos-delay="200">
          <h6 class="fw-bold">Bantuan</h6>
          <ul class="list-unstyled">
            <li><a href="#">FAQ</a></li>
            <li><a href="#">Pusat Bantuan</a></li>
            <li><a href="mailto:info@familycareplus.com">📧 info@familycareplus.com</a></li>
            <li><a href="https://wa.me/6281234567890" target="_blank">📱 +62 812-3456-7890</a></li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div class="col-md-3 col-12 mb-4" data-aos="fade-left">
          <h6 class="fw-bold">Tetap Terhubung</h6>
          <p>Berlangganan newsletter FamilyCarePlus untuk info terbaru.</p>
          <form class="d-flex mb-3">
            <input type="email" class="form-control me-2" placeholder="Masukkan email anda">
            <button class="btn btn-danger rounded-pill">Daftar</button>
          </form>
          <!-- Ikon Sosial -->
          <div class="d-flex gap-3">
            <a href="#"><i class="bi bi-facebook fs-4 text-white"></i></a>
            <a href="#"><i class="bi bi-instagram fs-4 text-white"></i></a>
            <a href="#"><i class="bi bi-youtube fs-4 text-white"></i></a>
          </div>
        </div>
      </div>

      <!-- Logo Mitra -->
      <div class="row mt-4 align-items-center" data-aos="zoom-in-up">
        <div class="col-md-12 text-center">
          <p class="mb-2 fw-bold">Anggota Dari:</p>
          <div class="d-flex justify-content-center gap-4 flex-wrap">
            <img src="https://upload.wikimedia.org/wikipedia/commons/9/99/WHO_logo.svg" alt="WHO" height="40">
            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2d/Ministry_of_Health_Indonesia.svg" alt="Kemenkes" height="40">
            <img src="https://upload.wikimedia.org/wikipedia/commons/2/27/Red_Cross_logo.svg" alt="Red Cross" height="40">
          </div>
        </div>
      </div>
    </div>

    <!-- Copyright -->
    <div class="text-center py-3 mt-4 border-top">
      <small>© 2025 FamilyCarePlus. All Rights Reserved.</small>
    </div>
  </footer>

  <!-- Bootstrap & Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    // AOS init
    AOS.init({
      duration: 1200,
      easing: 'ease-in-out-back',
      once: false
    });

    // Navbar scroll effect
    window.addEventListener("scroll", function () {
      const navbar = document.getElementById("navbar");
      if (window.scrollY > 50) {
        navbar.classList.add("scrolled");
      } else {
        navbar.classList.remove("scrolled");
      }
    });

    // Scrollspy active link
    const sections = document.querySelectorAll("section");
    const navLinks = document.querySelectorAll(".nav-link");
    window.addEventListener("scroll", () => {
      let current = "";
      sections.forEach(section => {
        const sectionTop = section.offsetTop - 120;
        if (pageYOffset >= sectionTop) {
          current = section.getAttribute("id");
        }
      });
      navLinks.forEach(link => {
        link.classList.remove("active");
        if (link.getAttribute("href") === "#" + current) {
          link.classList.add("active");
        }
      });
    });
  </script>
</body>
</html>
