<?php

// $mahasiswa = [
//     ["nama" => "Riski Ramadani",
//     "NIM" => 2217020018,
//     "E-mail" => "senga8818@gmail.com"
//     ],
//     [
//     "nama" => "jo mama",
//     "NIM" => 0017020000,
//     "E-mail" => "jomama@gmail.com"
//     ]
// ];


$dbh = new PDO ('mysql:host=localhost;dbname=gudang','root','');

$db = $dbh -> prepare('SELECT * FROM masuk');
$db->execute();
$masuk = $db ->fetchAll(PDO::FETCH_ASSOC);

$data = json_encode ($masuk);
echo $data;
?>