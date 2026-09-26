var _dataRegist = {}
$(document).ready(function() {
    $("#list-pendaftaran").docoPaginationSelec2({
        placeholder : '-- Cari Pendaftaran --',
        minimumInputLength: 3,
        _api : '/kasir/perbandingan-harga/get-data-pendaftaran',
    }).on('select2:select', function (item) {
        var data = item.params.data.datavalue;
        kelaspelayanan_nama = data.kelaspelayanan_nama;
        if(data.kelas_ditagihkan){
            kelaspelayanan_nama = data.is_pasientitipan ? data.kelaspelayanan_nama : data.kelas_ditagihkan
        }
        _dataRegist = data
        $('#informasi').hide();
        $('#pembandingan-tagihan').hide();
        $('.nama_pasien').html(data.nama_pasien);
        $('.no_rekam_medik').html(data.no_rekam_medik);
        $('.tanggal_lahir').html(data.tanggal_lahir ? convertDateByFormat(data.tanggal_lahir, "d-m-y") : '');
        $('.tgl_pendaftaran').html(data.tgl_pendaftaran ? convertDateByFormat(data.tgl_pendaftaran, "d-m-y") : '');
        $('.no_pendaftaran').html(data.no_pendaftaran);
        $('.instalasi_nama').html(data.instalasi_nama);
        $('.ruangan_nama').html(data.ruangan_nama);
        $('.cara_bayar').html(data.carabayar_nama);
        $('.penjamin_nama').html(data.penjamin_nama);
        $('.kelas_pelayanan_nama').html(`<h6>${kelaspelayanan_nama}</h6>`);
        $('#data-pasien').html(`${data.no_rekam_medik} - <b class="font" >${data.nama_pasien}</b>`);
        $('#informasi').show();
        $('#pembandingan-tagihan').show();
        $('#kelas-pembanding').val('').trigger('change')
    });

    $('#kelas-pembanding').on('change', function (e) {
        var _btn = $('#btn-pembanding')
        _btn.prop('disabled', true)
        _btn.removeAttr('action');
        if ($(this).val()) {
            _btn.attr('action', `/kasir/perbandingan-harga/show-popup?id=${_dataRegist.pendaftaran_id}&kelasId=${$(this).val()}`);
            _btn.prop('disabled', false)
        }
    })
})