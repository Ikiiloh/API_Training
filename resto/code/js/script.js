$.getJSON('../code/daftarmenu.json', function(data) {
    let menu = data.menu;
    $.each(menu, function(index, item) {
        $('#daftar-menu').append('<div class="col-md-4">' +
          '<div class="card mb-4" style="width: 18rem;">' +
          '<img src='+ item.gambar +' class="card-img-top" alt="' + item.nama + '">' +
          '<div class="card-body">' +
          '<h5 class="card-title">' + item.nama + '</h5>' +
          '<p class="card-text">' + item.deskripsi + '</p>' +
          '<h5 class="card-title">Rp ' + item.harga.toLocaleString('id-ID') + ',-</h5>' +
          '<a href="#" class="btn btn-primary">Pesan Sekarang</a>' +
          '</div></div></div>');
    });
});
