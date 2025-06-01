<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load environment variables
function loadEnv($path) {
  if (!file_exists($path)) {
      throw new Exception('.env file not found');
  }
  
  $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
      if (strpos(trim($line), '#') === 0) {
          continue; // Skip comments
      }
      
      list($name, $value) = explode('=', $line, 2);
      $name = trim($name);
      $value = trim($value);
      
      if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
          putenv(sprintf('%s=%s', $name, $value));
          $_ENV[$name] = $value;
          $_SERVER[$name] = $value;
      }
  }
}

// Load .env file
try {
  loadEnv(__DIR__ . '/.env');
} catch (Exception $e) {
  die('Error loading .env file: ' . $e->getMessage());
}

// Ambil data user dari GitHub API (TETAP SEPERTI SEMULA)
$user_data = json_decode(file_get_contents("https://api.github.com/users/Ikiiloh", false, stream_context_create([
    "http" => [
        "user_agent" => "request"
    ]
])), true);

// Ambil data repositori dari GitHub API (TETAP SEPERTI SEMULA)
$repos_data = json_decode(file_get_contents("https://api.github.com/users/Ikiiloh/repos", false, stream_context_create([
    "http" => [
        "user_agent" => "request"
    ]
])), true);

// Mendapatkan data dari API Instagram (Apify)
$api_url = getenv('APIFY_INSTAGRAM_API_URL');

if (!$api_url) {
  die('Instagram API URL not found in environment variables');
}

// 1. Definisikan fungsi get_CURL terlebih dahulu (TETAP SEPERTI SEMULA)
function get_CURL($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $output = curl_exec($ch);
    if(curl_errno($ch)){
        // echo 'Curl error in get_CURL: ' . curl_error($ch);
    }
    curl_close($ch);
    return json_decode($output, true);
}

// TAMBAHAN: Fungsi khusus untuk menangani gambar Instagram
function fetch_instagram_image_with_fallback($image_url) {
    if (empty($image_url)) {
        return create_placeholder_image();
    }

    // Method 1: Coba dengan headers yang lebih lengkap
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $image_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    // Headers untuk meniru browser asli
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept: image/webp,image/apng,image/*,*/*;q=0.8',
        'Accept-Language: en-US,en;q=0.9',
        'Accept-Encoding: gzip, deflate, br',
        'Referer: https://www.instagram.com/',
        'Sec-Fetch-Dest: image',
        'Sec-Fetch-Mode: no-cors',
        'Sec-Fetch-Site: cross-site',
        'sec-ch-ua: "Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
        'sec-ch-ua-mobile: ?0',
        'sec-ch-ua-platform: "Windows"'
    ]);
    
    $image_data = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    if ($image_data !== false && empty($curl_error) && $http_code == 200) {
        return 'data:image/jpeg;base64,' . base64_encode($image_data);
    }
    
    // Method 2: Coba dengan proxy service
    $proxy_url = "https://images.weserv.nl/?url=" . urlencode($image_url) . "&w=150&h=150&fit=cover&mask=circle";
    
    $ch_proxy = curl_init();
    curl_setopt($ch_proxy, CURLOPT_URL, $proxy_url);
    curl_setopt($ch_proxy, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch_proxy, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch_proxy, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    $proxy_data = curl_exec($ch_proxy);
    $proxy_code = curl_getinfo($ch_proxy, CURLINFO_HTTP_CODE);
    curl_close($ch_proxy);
    
    if ($proxy_data !== false && $proxy_code == 200) {
        return 'data:image/jpeg;base64,' . base64_encode($proxy_data);
    }
    
    return create_placeholder_image();
}

function create_placeholder_image() {
    // Buat placeholder SVG yang menarik
    $svg = '<svg width="150" height="150" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#405DE6;stop-opacity:1" />
                <stop offset="50%" style="stop-color:#833AB4;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#FD1D1D;stop-opacity:1" />
            </linearGradient>
        </defs>
        <circle cx="75" cy="75" r="70" fill="url(#grad1)"/>
        <circle cx="75" cy="75" r="45" fill="none" stroke="white" stroke-width="3"/>
        <circle cx="75" cy="75" r="15" fill="none" stroke="white" stroke-width="3"/>
        <circle cx="95" cy="55" r="8" fill="white"/>
        <text x="75" y="130" text-anchor="middle" font-family="Arial" font-size="10" fill="#666">Instagram</text>
    </svg>';
    
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}

// 2. Panggil get_CURL untuk mengisi $ig_data (TETAP SEPERTI SEMULA)
$ig_data = get_CURL($api_url);

// 3. Debug (OPSIONAL - bisa dicomment jika tidak perlu)
// echo "<pre>Debug Instagram Data:\n";
// var_dump($ig_data);
// echo "</pre>";

// 4. Dapatkan data Instagram
$image_url = isset($ig_data[0]['profilePicUrlHD']) ? $ig_data[0]['profilePicUrlHD'] : '';
$ig_username = isset($ig_data[0]['username']) ? $ig_data[0]['username'] : 'ikiiloh';
$ig_url = isset($ig_data[0]['url']) ? $ig_data[0]['url'] : 'https://instagram.com/ikiiloh';

// 5. Proses gambar Instagram dengan fungsi yang sudah diperbaiki
if (!empty($image_url)) {
    $base64_image = fetch_instagram_image_with_fallback($image_url);
} else {
    $base64_image = create_placeholder_image();
}

?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&family=Press+Start+2P&family=Rubik+80s+Fade&family=Sixtyfour+Convergence&family=Style+Script&display=swap" rel="stylesheet">

    <!-- My CSS -->
    <link rel="stylesheet" href="css/style.css">

    <title>My Portfolio</title>
  </head>
  <body>
    <!-- Loading Screen -->
    <div id="loading-screen" class="loading-screen">
      <div class="loading-content">
        <div class="loading-text">LOADING</div>
        <div class="loading-bar">
          <div class="loading-bar-fill"></div>
        </div>
        <div class="loading-pixel"></div>
      </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="#home">M.R.R</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="#home">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#about">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#social">Social</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#portfolio">Repository</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="jumbotron" id="home">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-4 text-center">
            <img src="https://res.cloudinary.com/dlcljeoih/image/upload/v1748716691/profile_gigup_arroia.jpg" class="img-thumbnail profile-image rounded-circle" style="width: 300px; height: 300px;">
          </div>
          <div class="col-md-8">
            <h1 class="display-4 text-center font-weight-bold">
              <?php echo isset($ig_data[0]['fullName']) ? $ig_data[0]['fullName'] : 'M. Riski Ramadani'; ?>
            </h1>
            <h2 class="lead text-center font-weight-bold">Student | Beginner Programmer</h2>
              <p class="text-justify">I'm a junior developer from Indonesia, currently learning web development, API integration, Python, and JavaScript. I'm working on personal projects and uni tasks, exploring GitHub Actions and automation.   
              I love coding at night 🌙 and open to collaborate on web and data projects. </p>
          </div>
        </div>
      </div>
    </div>

    <!-- About -->
    <section class="about" id="about">
      <div class="container py-1">
        <div class="row mb-4">
          <div class="col text-center font-weight-bold">
            <h2>About Me</h2>
          </div>
        </div>
        <div class="row justify-content-center mb-5">
          <div class="col-md-8">
            <div class="card shadow" style="border-radius: 15px;">
              <div class="card-body">
                <div class="mb-4">
                  <h5>🛠️ Languages & Tools:</h5>
                  <div class="d-flex flex-wrap justify-content-center gap-2">
                    <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
                    <img src="https://img.shields.io/badge/JavaScript-F0DB4F?style=for-the-badge&logo=javascript&logoColor=black"/>
                    <img src="https://img.shields.io/badge/Python-306998?style=for-the-badge&logo=python&logoColor=white"/>
                    <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white"/>
                    <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white"/>
                  </div>
                </div>

                <div class="mb-4">
                  <h5>📈 GitHub Stats:</h5>
                  <div class="text-center">
                    <img src="https://github-readme-stats.vercel.app/api?username=ikiiloh&show_icons=true&theme=github_dark" alt="ikiiloh" style="max-width: 100%;" />
                  </div>
                </div>

                <div class="text-center">
                  <h2>📫 Connect with me:</h2>
                  <p class="contact-info">
                    📧 Email: <a href="mailto:senga8818@gmail.com">senga8818@gmail.com</a><br>
                    💼 LinkedIn: <a href="https://www.linkedin.com/in/m-riski-ramadani-144449201/" target="_blank">My LinkedIn</a>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Social Media -->
     <section class="social py-5" id="social">
      <div class="container">
        <div class="row">
          <div class="col text-center font-weight-bold">
            <h2 class="mt-3"> Social Media </h2>
            <br>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-md-5 d-flex align-items-center justify-content-center mb-2">
            <div class="card w-100 shadow social-card">
              <div class="card-body d-flex align-items-center">
                <img src="https://github.com/<?php echo $user_data['login']; ?>.png" class="img-thumbnail mr-3 social-profile-image">
                <div class="flex-grow-1 text-center">
                  <h5 class="mb-1">
                    <a href="https://github.com/<?php echo $user_data['login']; ?>" target="_blank" class="social-username-link">
                      <?php echo $user_data['login']; ?>
                    </a>
                  </h5>
                  <a href="https://github.com/<?php echo $user_data['login']; ?>" target="_blank" class="btn btn-dark btn-sm mt-2">
                    <i class="fab fa-github"></i> FOLLOW ON GITHUB
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-5 d-flex align-items-center justify-content-center mb-2">
            <div class="card w-100 shadow social-card">
              <div class="card-body d-flex align-items-center">
                <img src="<?php echo $base64_image; ?>" class="img-thumbnail mr-3 social-profile-image">
                <div class="flex-grow-1 text-center">
                  <h5 class="mb-1">
                    <a href="<?php echo $ig_url; ?>" target="_blank" class="social-username-link">
                      <?php echo $ig_username; ?>
                    </a>
                  </h5>
                  <a href="<?php echo $ig_url; ?>" target="_blank" class="btn btn-dark btn-sm mt-2">
                    <i class="fab fa-instagram"></i> FOLLOW ON INSTAGRAM
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
     </section>

    <!-- Repo -->
    <section class="portfolio " id="portfolio">
      <div class="container">
        <div class="row pt-4 mb-4 mt-4">
          <div class="col text-center font-weight-bold">
            <h2>My Personal Project</h2>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-md-5 d-flex align-items-center justify-content-center mb-2">
            <div class="card w-100 shadow social-card">
              <div class="card-body d-flex align-items-center">
              <img src="./img/logo_web_game.svg" class="img-thumbnail mr-3 project-image">
                <div class="flex-grow-1 text-center">
                  <h5 class="mb-1">Ikiiloh Game</h5>
                  <div class="mt-2">
                    <a href="https://github.com/Ikiiloh/API_Training/tree/de8024fa3ef26301d678cf75b70cf28aae7d30cb/Ikiloh_Game" target="_blank" class="btn btn-dark btn-sm">View on GitHub</a>
                    <a href="https://ikiiloh-game.vercel.app" target="_blank" class="btn btn-dark btn-sm ml-2">Visit Web</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-5 d-flex align-items-center justify-content-center mb-2">
            <div class="card w-100 shadow social-card">
              <div class="card-body d-flex align-items-center">
              <img src="./img/White-Pixel-Mouse-Cursor-Arow-Fixed.svg" class="img-thumbnail mr-3 project-image">
                <div class="flex-grow-1 text-center">
                  <h5 class="mb-1">My Another Project</h5>
                  <div class="mt-2">
                    <a href="https://github.com/Ikiiloh?tab=repositories" target="_blank" class="btn btn-dark btn-sm">View on GitHub</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer class="site-footer">
      <div class="container text-center">
          <p>
              &copy; 2025 - Created by
              <a href="https://github.com/Ikiiloh/API_Training/tree/main/Ikiloh_Game" target="_blank" title="Kunjungi GitHub Saya">
                  <i class="fab fa-github"></i> Ikiiloh
              </a>
          </p>
      </div>
    </footer>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>

    <!-- Custom JavaScript -->
    <script src="js/script.js"></script>
  </body>
</html>