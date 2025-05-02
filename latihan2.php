<?php

$data = file_get_contents ('cobalagi.json');
$masuk = json_decode($data, TRUE);

var_dump ($masuk);

foreach ($masuk as $item) {
    echo "Nama: " . $item["Nama"] . " - Jenis: " . $item["jenis"] . "<br>";
}

// echo $masuk[0]["Nama"];

?>