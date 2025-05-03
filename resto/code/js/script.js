// Fungsi untuk merender menu ke dalam #daftar-menu
function tampilkanMenu(menu) {
    let content = '';
    $.each(menu, function(index, item) {
        content += '<div class="col-md-4">' +
            '<div class="card mb-4" style="width: 18rem;">' +
            '<img src="' + item.gambar + '" class="card-img-top" alt="' + item.nama + '">' +
            '<div class="card-body">' +
            '<h5 class="card-title">' + item.nama + '</h5>' +
            '<p class="card-text">' + item.deskripsi + '</p>' +
            '<h5 class="card-title">Rp ' + item.harga.toLocaleString('id-ID') + ',-</h5>' +
            '<a href="#" class="btn btn-primary">Pesan Sekarang</a>' +
            '</div></div></div>';
    });
    $('#daftar-menu').html(content); // Ganti konten, bukan append
}

// Fungsi untuk menampilkan semua menu
function SemuaMenu() {
    $.getJSON('../code/daftarmenu.json', function(data) {
        tampilkanMenu(data.menu);
    });
}

// Panggil semua menu saat halaman pertama kali dimuat
SemuaMenu();

// Event handler untuk navigasi kategori
$('.nav-link').on('click', function () {
    $('.nav-link').removeClass('active');
    $(this).addClass('active');

    let kategoriPilih = $(this).html();
    $('h1').html(kategoriPilih);

    if (kategoriPilih === 'All Menu') {
        SemuaMenu();
        return;
    }

    $.getJSON('../code/daftarmenu.json', function(data) {
        let menu = data.menu;
        let menuFilter = menu.filter(function(item) {
            return item.kategori.toLowerCase() === kategoriPilih.toLowerCase();
        });

        tampilkanMenu(menuFilter);
    });
});
