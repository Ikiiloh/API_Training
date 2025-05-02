// let mahasiswa = {
//     nama : "Riski",
//     NIM : 123456789,
//     prodi : "Teknik Informatika",
//     angkatan : 2020
// }
// console.log(JSON.stringify(mahasiswa));

// let baru = new XMLHttpRequest();
// baru.onreadystatechange = function () {
//     if (baru.readyState ==  4 && baru.status == 200) {
//         let mahasiswa = JSON.parse(this.responseText);
//         console.log(mahasiswa);
//     }
// }
// baru.open("GET", "coba.json", true);
// baru.send();

$.getJSON("coba.json", function (data) {
    console.log(data);
});