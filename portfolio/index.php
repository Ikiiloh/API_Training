<?php
// Ambil data user dari GitHub API
$user_data = json_decode(file_get_contents("https://api.github.com/users/Ikiiloh", false, stream_context_create([
    "http" => [
        "user_agent" => "request"
    ]
])), true);

// Ambil data repositori dari GitHub API
$repos_data = json_decode(file_get_contents("https://api.github.com/users/Ikiiloh/repos", false, stream_context_create([
    "http" => [
        "user_agent" => "request"
    ]
])), true);

// Mendapatkan data dari API Instagram (Apify)
$api_url = "https://api.apify.com/v2/datasets/eq2DdWBPCrw69cuP2/items?clean=true&format=json";

// Cara 1: Menggunakan file_get_contents (pastikan allow_url_fopen = On di php.ini)
$data = file_get_contents($api_url);
$ig_data = json_decode($data, true);

// Cara 2: Menggunakan cURL (lebih universal)
function get_CURL($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($ch);
    curl_close($ch);
    return json_decode($output, true);
}
$ig_data = get_CURL($api_url);

$image_url = isset($ig_data[0]['profilePicUrlHD']) ? $ig_data[0]['profilePicUrlHD'] : ''; // Get the URL from API, or empty if not available
$base64_image = ''; // Initialize Base64 string as empty

// Get Instagram username and URL
$ig_username = isset($ig_data[0]['username']) ? $ig_data[0]['username'] : 'Username Error';
$ig_url = isset($ig_data[0]['url']) ? $ig_data[0]['url'] : '#';

// Check if the API URL is available and try to fetch the image data
if (!empty($image_url)) {
    // Use @file_get_contents to fetch data. @ suppresses warnings if fetching fails.
    // We also check if the result is not FALSE, which indicates failure.
    $image_data = @file_get_contents($image_url);
    
    if ($image_data !== FALSE) {
        // Successfully fetched data, encode it in Base64
        // Determine content type - assuming jpeg for profile pic, but can be made dynamic if needed
        $base64_image = 'data:image/jpeg;base64,' . base64_encode($image_data);
    }
    // If file_get_contents returns FALSE, $base64_image remains empty, resulting in an empty src below
}

// Display the image tag. The src will be the Base64 data URI if fetching succeeded, otherwise it will be empty.
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">

    <!-- My CSS -->
    <link rel="stylesheet" href="css/style.css">

    <title>My Portfolio</title>
  </head>
  <body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="#home">M. Riski Ramadani</a>
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
              <a class="nav-link" href="#portfolio">Portfolio</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>


    <div class="jumbotron" id="home">
      <div class="container">
        <div class="text-center">
          <img src="img/profile_gigup.jpeg" class="img-thumbnail" style="border-radius: 100px; width: 300px; height: 300px;">
          <h1 class="display-4">
  <?php echo isset($ig_data[0]['fullName']) ? $ig_data[0]['fullName'] : 'Nama Tidak Ditemukan'; ?>
</h1>
          <h3 class="lead">Student | Beginner Programmer</h3>
        </div>
      </div>
    </div>


    <!-- About -->
    <section class="about" id="about">
      <div class="container">
        <div class="row mb-4">
          <div class="col text-center">
            <h2>About</h2>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-md-5">
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Natus, molestiae sunt doloribus error ullam expedita cumque blanditiis quas vero, qui, consectetur modi possimus. Consequuntur optio ad quae possimus, debitis earum.</p>
          </div>
          <div class="col-md-5">
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Natus, molestiae sunt doloribus error ullam expedita cumque blanditiis quas vero, qui, consectetur modi possimus. Consequuntur optio ad quae possimus, debitis earum.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Social Media -->
     <section class="social bg-light py-5" id="social">
      <div class="container">
        <div class="row">
          <div class="col text-center">
            <h2 class="mt-3"> Social Media </h2>
            <br>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-md-5 d-flex align-items-center justify-content-center mb-2">
            <div class="card w-100 shadow" style="border-radius: 30px; max-width: 400px;">
              <div class="card-body d-flex align-items-center">
                <img src="https://github.com/<?php echo $user_data['login']; ?>.png" class="img-thumbnail mr-3" style="border-radius: 100px; width: 80px; height: 80px;">
                <div class="flex-grow-1 text-center">
                  <h5 class="mb-1">
                    <a href="https://github.com/<?php echo $user_data['login']; ?>" target="_blank" class="social-username-link" style="text-decoration: none; color: inherit;">
                      <?php echo $user_data['login']; ?>
                    </a>
                  </h5>
                  <a href="https://github.com/<?php echo $user_data['login']; ?>" target="_blank" class="btn btn-dark btn-sm mt-2"style="border-radius: 5px">
                    <i class="fab fa-github"></i> FOLLOW ON GITHUB
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-5 d-flex align-items-center justify-content-center mb-2">
            <div class="card w-100 shadow" style="border-radius: 30px; max-width: 400px;">
              <div class="card-body d-flex align-items-center">
                <img src="<?php echo $base64_image; ?>" class="img-thumbnail mr-3" style="border-radius: 100px; width: 80px; height: 80px;">
                <div class="flex-grow-1 text-center">
                  <h5 class="mb-1">
                    <a href="<?php echo $ig_url; ?>" target="_blank" class="social-username-link" style="text-decoration: none; color: inherit;">
                      <?php echo $ig_username; ?>
                    </a>
                  </h5>
                  <a href="<?php echo $ig_url; ?>" target="_blank" class="btn btn-dark btn-sm mt-2" style="border-radius: 5px">
                    <i class="fab fa-instagram"></i> FOLLOW ON INSTAGRAM
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
     </section>


    <!-- Portfolio -->
    <section class="portfolio " id="portfolio">
      <div class="container">
        <div class="row pt-4 mb-4">
          <div class="col text-center">
            <h2>Portfolio</h2>
          </div>
        </div>
        <div class="row">
          <?php if (!empty($repos_data)): // Check if repository data is available ?>
              <?php foreach ($repos_data as $repo): // Loop through each repository ?>
                  <div class="col-md mb-4">
                      <div class="card w-100 shadow" style="border-radius: 10px; max-width: 400px;">
                          <!-- Removed image tag -->
                          <div class="card-body">
                              <h5 class="card-title"><?php echo htmlspecialchars($repo['name']); ?></h5>
                              <p class="card-text"><?php echo htmlspecialchars($repo['description'] ?? 'No description available.'); ?></p>
                              <a href="<?php echo htmlspecialchars($repo['html_url']); ?>" target="_blank" class="btn btn-dark btn-sm mt-2" style="border-radius: 5px">View on GitHub</a>
                          </div>
                      </div>
                  </div>
              <?php endforeach; ?>
          <?php else: // Display a message if no repositories are found or API call failed ?>
              <div class="col text-center">
                  <p>Could not load repositories from GitHub.</p>
              </div>
          <?php endif; ?>
        </div>
      </div>
    </section>


    <!-- Contact -->
    <section class="contact bg-light" id="contact">
      <div class="container">
        <div class="row pt-4 mb-4">
          <div class="col text-center">
            <h2>Contact</h2>
          </div>
        </div>

        <div class="row justify-content-center">
          <div class="col-lg-4">
            <div class="card w-100 shadow bg-dark text-white" style="border-top-left-radius: 20px; border-top-right-radius: 0px; border-bottom-left-radius: 0px; border-bottom-right-radius: 20px; max-width: 400px;">
              <div class="card-body">
                <h5 class="card-title-centre">Contact Me</h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div>

          <div class="col-lg-6 mt-4">
            
            <form>
              <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama">
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" id="email">
              </div>
              <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" class="form-control" id="phone">
              </div>
              <div class="form-group">
                <label for="message">Message</label>
                <textarea class="form-control" id="message" rows="3"></textarea>
              </div>
              <div class="form-group">
                <button type="button" class="btn btn-dark btn-sm mt-2" style="border-radius: 5px">Send Message</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </section>


    <!-- footer -->
    <footer class="bg-dark text-white mt-5">
      <div class="container">
        <div class="row">
          <div class="col text-center">
            <p>Copyright &copy; 2018.</p>
          </div>
        </div>
      </div>
    </footer>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
  </body>
</html>