
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Web Cofradia Buena muerte</title>
    <meta
      name="description"
      content="bootstrap, html, css, js, php, formularios, bbdd"
    />
    <meta name="keywords" content="" />

    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon" />

    <!-- Fuentes-->
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />

    <!-- Vendor CSS Files -->
    <link
      href="../assets/vendor/bootstrap/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="../assets/vendor/bootstrap-icons/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link href="../assets/vendor/aos/aos.css" rel="stylesheet" />
    <link href="../assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet" />
    <link
      href="../assets/vendor/glightbox/css/glightbox.min.css"
      rel="stylesheet"
    />

    <!-- Main CSS File -->
    <link href="../assets/css/main.css" rel="stylesheet" />

    <!-- =======================================================
  * Template Name: MeFamily
  * Template URL: https://bootstrapmade.com/family-multipurpose-html-bootstrap-template-free/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
  </head>

  <body class="index-page">
    <header id="header" class="header d-flex align-items-center sticky-top">
      <div
        class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between"
      >
        <a href="../index.html" class="logo d-flex align-items-center">
          <img src="../assets/img/logo.png" alt="escudo de la Cofradia" />
          <h1 class="sitename">BUENA MUERTE Y AMARGURA</h1>
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="../index.html" class="active">Inicio</a></li>
            <li class="dropdown">
              <a href="#"
                ><span>Hermandad</span>
                <i class="bi bi-chevron-down toggle-dropdown"></i
              ></a>
              <ul>
                <li><a href="../historia.html">Historia</a></li>
                <li class="dropdown">
                  <a href="#"
                    ><span>Colectivos</span>
                    <i class="bi bi-chevron-down toggle-dropdown"></i
                  ></a>
                  <ul>
                    <li><a href="../juntaGobierno.html">Junta de Gobierno</a></li>
                    <li><a href="../grupoJoven.html">Grupo Joven</a></li>
                    <li><a href="../costaleros.html">Costaleros</a></li>
                  </ul>
                </li>
              </ul>
            </li>
            <li><a href="../patrimonio.html">Patrimonio</a></li>
            <li><a href="../galeria.html">Galeria</a></li>
            <li><a href="#">Acceso</a></li>
            <li><a href="../contacto.html">Contacto</a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
      </div>
    </header>

    <main class="main">
      <!--El siguiente trozo de codigo podriamos ponerlo en un archivo aparte y ejecutarlo solo al desplegar mi aplicacion -->
      <?php
      /*
      include("./mysqlConexion.php");
      include("./bibliotecaFunciones.php");
      include("../scripts/basedatos.php");
      
      $baseDatos="cofradia";
      $pdo=conexionSinBase();
      existeBaseDatos($pdo,$baseDatos,$sqlBaseDatos);
     
      */
      ?>

      
      <!-- About Section -->
      <section id="contact" class="contact section"> 
        
        <!-- Page Title -->
        <div class="page-title light-background">
          <div
            class="container d-lg-flex justify-content-between align-items-center"
          >
            <h1 class="mb-2 mb-lg-0">Acceso</h1>
            <nav class="breadcrumbs">
              <ol>
                <li><a href="../index.html">Inicio</a></li>
                <li class="current">Acceso</li>
              </ol>
            </nav>
          </div>
        </div>
        <!-- End Page Title -->
        <div class="container mt-5">
          <div class="row gy-4">
            <div class="col-lg-6">
              <img src="../assets/img/acceso.jpeg" class="img-fluid" alt="imagen del Cristo de la Buena muerte" />
            </div>
            <div class="col-lg-6">
              <div class="text-center mb-4" data-aos="fade-up" data-aos-delay="200">
                  <img alt="escudo cofradía" style="width: 35%" class="mb-3" src="../assets/img/favicon.png"/>
                  <h4 class="fw-bold mb-1 mt-5">
                    Cofradía Buena Muerte y Amargura
                  </h4>
                  <p class="text-muted">ACCESO PRIVADO</p>
              </div> 
              <!-- Para mostrar error de una forma mas amigable con el usuario -->
              <div id="error" style ="color: red; margin-bottom : 1rem; display: none;"></div>      
              <form id="loginForm" action="login.php" method="POST"  class="php-acceso-form mt-5" data-aos="fade-up" data-aos-delay="300">
                <div class="mb-4">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email"
                  name="email" placeholder="e-mail" required>
                </div>

                <div class="mb-4">
                  <label for="password" class="form-label">Contraseña</label>
                  <input type="password" class="form-control" id="password"
                  name="password" placeholder="Contraseña" required>
                </div>

                <div class="col-md-12 text-center mt-5" data-aos="fade-up" data-aos-delay="400">
                  <button type="submit">Acceder
                  </button>
                </div>
              </form>  
              <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="500">
                <a class="text-decoration-none fw-4" href="./registro.php">Quiero ser Hermano/a</a>
              </div>
            </div>
          </div>
        </div>
      </section>
      <!--/About Section -->
    </main>

    <footer id="footer" class="footer dark-background">
      <div class="container">
        <h3 class="sitename">Buena Muerte y Amargura</h3>
        <p>
          Real e Ilustre Cofradia del Cristo de la Buena Muerte y María
          Santísima de la Amargura
        </p>
        <div class="social-links d-flex justify-content-center">
          <a href=""><i class="bi bi-twitter-x"></i></a>
          <a href=""><i class="bi bi-facebook"></i></a>
          <a href=""><i class="bi bi-instagram"></i></a>
          <a href=""><i class="bi bi-tiktok"></i></a>
        </div>
        <div class="container">
          <div class="copyright">
            <span>Copyright</span>
            <strong class="px-1 sitename">Buena Muerte y Amargura</strong>
            <span>All Rights Reserved</span>
          </div>
          <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you've purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
            Designed by
            <a href="https://bootstrapmade.com/">
              BootstrapMade &amp; LidiaLopez</a
            >
          </div>
        </div>
      </div>
    </footer>

    <!-- Scroll Top -->
    <a
      href="#"
      id="scroll-top"
      class="scroll-top d-flex align-items-center justify-content-center"
      ><i class="bi bi-arrow-up-short"></i
    ></a>

    <!-- Preloader -->
    <div id="preloader"></div>

     <!-- Ficheros de control JS -->
    <script src="../js/controlAcceso.js" defer></script>

    <!-- Vendor JS Files -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>
    <script src="../assets/vendor/aos/aos.js"></script>
    <script src="../assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>


    <!-- Main JS File -->
    <script src="../assets/js/main.js" defer></script>
  </body>
</html>
