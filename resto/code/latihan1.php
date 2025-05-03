<?php
// Ambil dan decode file JSON
$data = file_get_contents('daftarmenu.json');
$menu = json_decode($data, true);
$menuItems = $menu["menu"];
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>Resto Breaking Bad</title>
  </head>
  <body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img src="../img/logo.png" width="60" height="60" class="d-inline-block align-top">
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
      <div class="navbar-nav">
        <a class="nav-item nav-link active" href="#">All Menu</a>
      </div>
    </div>
  </div>
</nav>
 
<div class="container mt-4">

  <div class="row mt-3 mb-3">
    <div class = "col">

      <h1>All Menu</h1>

    </div>
  </div>
  
  <div class="row">
      <?php foreach ($menuItems as $item): ?>
        <div class="col-md-4">
          <div class="card mb-4" style="width: 18rem;">
            <img src="<?= $item["gambar"]; ?>" class="card-img-top" alt="<?= $item["nama"]; ?>">
            <div class="card-body">
              <h5 class="card-title"><?= $item["nama"]; ?></h5>
              <p class="card-text"><?= $item["deskripsi"]; ?></p>
              <h5 class="card-title">Rp <?= number_format($item["harga"], 0, ',', '.'); ?>,-</h5>
              <a href="#" class="btn btn-primary">Pesan Sekarang</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>


  <!-- <div class = "row">
    <div class="col-md-4">
      <div class="card" style="width: 18rem;">
        <img src="../img/menu/ayam geprek.jpg" class="card-img-top" alt="...">
        <div class="card-body">
          <h5 class="card-title">Ayam Geprek</h5>
          <p class="card-text">Ayam goreng tepung yang digeprek dengan sambal pedas khas, disajikan dengan nasi hangat.</p>
          <h5 class="card-title">Rp 15.000,-</h5>
          <a href="#" class="btn btn-primary">Pesan Sekarang</a>
      </div>
    </div>
</div> -->

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script 
    src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script 
    src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>