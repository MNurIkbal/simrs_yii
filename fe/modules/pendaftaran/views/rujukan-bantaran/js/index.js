// Event Ready
$(document).on("click", "#tb-rujukan-bantaran tbody tr", function() {
    var statusBelumVerif = 4035;
    var statusSudahVerif = 4036;
    var statusTolakVerif = 4037;

    var statusReservasiTolak = 566;
    var statusReservasiApprove = 565;
    var statusReservasiPending = 564;

    var id_bantaran;
    var status_verif_bantaran;
    var pendaftaranol_id;
    var status_daftar_ol;
    var instalasi_id;
    try {
        id_bantaran = table.row(".selected").data().primary;
        status_verif_bantaran = table.row(".selected").data().status_verifikasi_bantaran_id;
        pendaftaranol_id = table.row(".selected").data().pendaftaranol_id;
        status_daftar_ol = table.row(".selected").data().status_daftar_ol;
        instalasi_id = table.row(".selected").data().instalasi_id;
    }
    catch(e) {
        id_bantaran = null;
        status_verif_bantaran = null;
        pendaftaranol_id = null;
        status_daftar_ol = null;
        instalasi_id = null;
    }

    if(id_bantaran == null) {
        $("#btn-detail-bantaran").attr('disabled', true).removeAttr('action');
        $("#btn-dokumen-bantaran").attr('disabled', true).removeAttr('action');
    } else {
        $("#btn-detail-bantaran").attr('disabled', false);
        $("#btn-dokumen-bantaran").attr('disabled', false);
    }

    if(status_verif_bantaran == statusBelumVerif) {
        $("#btn-verifikasi-bantaran").attr('disabled', false);
        $("#btn-daftarkan-bantaran").attr('disabled', true);
        $("#btn-batal-reservasi-bantaran").attr('disabled', true);
    } else if(status_verif_bantaran == statusSudahVerif) {
        $("#btn-verifikasi-bantaran").attr('disabled', true);

        if((status_daftar_ol == statusReservasiTolak || status_daftar_ol == statusReservasiApprove) || parseInt(instalasi_id) == 2) {
            $("#btn-daftarkan-bantaran").attr('disabled', true);
            $("#btn-batal-reservasi-bantaran").attr('disabled', true);
        } else {
            $("#btn-daftarkan-bantaran").attr('disabled', false);
            $("#btn-batal-reservasi-bantaran").attr('disabled', false);
        }

    } else {
        // tolak verif
        $("#btn-verifikasi-bantaran").attr('disabled', true);
        $("#btn-daftarkan-bantaran").attr('disabled', true);
        $("#btn-batal-reservasi-bantaran").attr('disabled', true);
    }

    $("#btn-detail-bantaran").attr('action', '/pendaftaran/rujukan-bantaran/detail-rujukan?id='+id_bantaran);
    $("#btn-verifikasi-bantaran").attr('action', '/pendaftaran/rujukan-bantaran/verifikasi-rujukan?id='+id_bantaran);
    if(id_bantaran != null) {
        $("#btn-dokumen-bantaran").attr('action', '/pendaftaran/rujukan-bantaran/kirim-dokumen?id='+id_bantaran);
    }
});

$("#btn-daftarkan-bantaran").click(function() {
    var data = table.row(".selected").data();
    if(typeof data !== 'undefined' && data.pendaftaranol_id != null){
        let tglDaftar = moment(new Date(data.tgl_kunjungan)).locale("en").format("DD-MMM-YYYY");
        let tglhariIni = moment().locale("en").format("DD-MMM-YYYY")

        if(tglDaftar > tglhariIni) {
            confirmationDialog( "Tgl kunjungan pasien bukan hari ini. Lanjutkan pendaftaran?", (isConfirm) => {
                if (isConfirm) {
                    window.open("/pendaftaran/daftar/index?pendaftaranol_id="+data.pendaftaranol_id);
                }
            });
        } else {
            window.open("/pendaftaran/daftar/index?pendaftaranol_id="+data.pendaftaranol_id);
        }
    }
});

$('#btn_batal_penolakan').click(function() {
    $('#form_keterangan_penolakan').val('');
    $('#modal_penolakan').modal('toggle');
});

$('#btn_simpan_penolakan').click(function() {
    var bantaran_id = $('#post_bantaran_id').val();
    var keterangan_penolakan = $('#form_keterangan_penolakan').val();

    $.ajax({
        type: 'POST',
        url: '/pendaftaran/rujukan-bantaran/reject-bantaran',
        data: {
            bantaran_id: bantaran_id,
            keterangan_tolak_rujukan: keterangan_penolakan
        },
        dataType: 'JSON',
        success: function (res) {
            docoNotification('success', 'Proses Berhasil', 'Data berhasil disimpan.')
            $('#form_keterangan_penolakan').val('');

            setTimeout(function() { 
                window.location.reload() 
            }, 3000);
        },
        error: function (res) {
            docoNotification('error', 'Proses Gagal', 'Data gagal disimpan.')
        },
    });
})
