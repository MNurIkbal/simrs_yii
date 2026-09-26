$(document).ready(function () {
    var begin = ': ';

    //Cek jika data rencana kontrol/inap tidak ditemukan dari WS BPJS
    if (rencanaKontrol.length === 0) {
        docoNotification('error', "Proses BPJS Error", "Data rencana kontrol/inap tidak ditemukan");
    } else {
        // jenis Kontrol 1 = SPRI, 2 = Rencana Kontrol
        if (rencanaKontrol.jnsKontrol == '1') {
            $('.no-surat-kontrol').hide();
            $('.no-spri').show();
            $('#ket-bpjs > a').trigger('click');
        } else {
            $('.no-surat-kontrol').show();
            $('.no-spri').hide();
        }

        // Set form data from WS BPJS
        $('#nama_spesialis').val(rencanaKontrol.namaPoliTujuan);
        $('#dokterdpjp_nama').val(rencanaKontrol.namaDokter);
        $('#kode_poli').val(rencanaKontrol.poliTujuan);
        $('#dokterdpjp_kode').val(rencanaKontrol.kodeDokter);

        // Set info data pasien
        $('.nama-pasien').html(pasien.nama_pasien);
        $('.rm-pasien').html(pasien.no_rekam_medik);

        // Set info data SEP dan asal rujukan SEP
        if (rencanaKontrol.sep.noSep != null && rencanaKontrol.sep.noSep != '') {
            $('.info-no_sep').html(begin + rencanaKontrol.sep.noSep);
            $('.info-tgl_sep').html(begin + rencanaKontrol.sep.tglSep);
            $('.info-jenis_pelayanan').html(begin + rencanaKontrol.sep.jnsPelayanan);
            $('.info-poli').html(begin + rencanaKontrol.sep.poli);
            $('.info-diagnosa').html(begin + rencanaKontrol.sep.diagnosa);

            // Set rujukan
            var asalRujukanSep = rencanaKontrol.sep.provPerujuk.nmProviderPerujuk + ' - ' + rencanaKontrol.sep.provPerujuk.kdProviderPerujuk;
            $('.info-no_rujukan').html(begin + rencanaKontrol.sep.provPerujuk.noRujukan);
            $('.prov-rujukan').html(begin + asalRujukanSep);
        }
    }

    // Set info data Peserta
    $('.info-no_kartu').html(begin + peserta.noKartu);
    $('.info-nama_peserta').html(begin + peserta.nama);
    $('.info-tgl_lahir').html(begin + peserta.tglLahir);
    $('.info-jenis_kelamin').html(begin + peserta.sex);
    $('.info-hak_kelas').html(begin + peserta.hakKelas.keterangan);
    $('.info-ppk_peserta').html(begin + peserta.provUmum.nmProvider+" - "+peserta.provUmum.kdProvider);
});

$('.pickadate-w-month').pickadate({
    format: 'dd-mm-yyyy',
    selectMonths: true,
    selectYears: 99,
    formatSubmit: 'dd-mm-yyyy',
});

function pilihDpjp(identifier) {
    const kode_poli = $(identifier).data('kode_poli');
    const nama_spesialis = $(identifier).data('nama_spesialis');
    const dokterdpjp_kode = $(identifier).data('dokterdpjp_kode');
    const dokterdpjp_nama = $(identifier).data('dokterdpjp_nama');

    $('#kode_poli').val(kode_poli);
    $('#nama_spesialis').val(nama_spesialis);
    $('#dokterdpjp_kode').val(dokterdpjp_kode);
    $('#dokterdpjp_nama').val(dokterdpjp_nama);

    $('#modal_pencarian_spesialis').modal('toggle');
}

$('.btn-batal').on('click', function () {
    window.location.href = "/pendaftaran/rencana-kontrol-inap"
});

$("#form").on("submit", function(event) {
    event.preventDefault();
    var formRencanaKontrol = $("#form").serializeArray();

    $(this).docoForm("submit", {
        data: formRencanaKontrol,
        success : function(response) {
            console.log(response);

            if (response.response.id == null) {
                window.open(window.location.origin + "/pendaftaran/rencana-kontrol-inap/print-rencana?is_vclaim=1&no_surat_kontrol=" + response.response.no_surat_kontrol, '_blank');
                window.location.replace("/pendaftaran/rencana-kontrol-inap/update?id="+response.response.no_surat_kontrol+"&vclaim=true&noKartu="+response.response.no_kartu);
            } else {
                window.open(window.location.origin + "/pendaftaran/rencana-kontrol-inap/print-rencana?rencanakontrol_id=" + response.response.id, '_blank');
                window.location.replace("/pendaftaran/rencana-kontrol-inap/update?id="+response.response.id);
            }

        },
        error: function(response) {
        }
    }); 
});