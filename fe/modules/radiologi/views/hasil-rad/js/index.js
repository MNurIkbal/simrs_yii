$("#btn-upload").prop("disabled", true);
$("#expertise").prop("disabled", true);
$("#ambil-foto").prop("disabled", true);
$("#batal-input-hasil").prop("disabled", true);
$("#batal-verifikasi").prop("disabled", true);
$("#verifikasi").prop("disabled", true);

let verifikasi = $('.terverifikasi').text();
$(document).on("click", "#table-hasil-rad tbody tr", function () {
    try {
        primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
    } catch (e) {
        primaryKey = false;
    }

    let table = $('#table-hasil-rad').DataTable();
    let tableData = table.row(".selected").data();
    if (typeof tableData !== 'undefined') {
        const primaryId = tableData.primary;
        const tgl_ambilfoto = tableData.tgl_ambilfoto;
        const tgl_hasilrad = tableData.tgl_hasilrad;
        const hasilpemeriksaanrad_id = tableData.hasilpemeriksaanrad_id;

        if (tgl_ambilfoto) {
            // $("#btn-upload").prop("disabled", false);
            // $("#obat-alkes").prop("disabled", false);
            $("#expertise").prop("disabled", false);
            $("#btn-upload").prop("disabled", false);
            $("#ambil-foto").prop("disabled", true);

        } else {
            // $("#btn-upload").prop("disabled", true);
            // $("#obat-alkes").prop("disabled", true);
            $("#expertise").prop("disabled", false);
            $("#btn-upload").prop("disabled", true);
            $("#ambil-foto").prop("disabled", false);
        }

        if (hasilpemeriksaanrad_id && !verifikasi) {
            $("#batal-input-hasil").prop("disabled", false);
            $("#batal-verifikasi").prop("disabled", true);
            $("#verifikasi").prop("disabled", false);
        } else {
            $("#batal-input-hasil").prop("disabled", true);
        }

        if (verifikasi) {
            $("#batal-verifikasi").prop("disabled", false);
            $("#btn-upload").prop("disabled", true);
            $("#ambil-foto").prop("disabled", true);
        }

        // if (tgl_hasilrad ) {
        //     $("#cetak-label").prop("disabled", false);
        // } else {
        //     $("#cetak-label").prop("disabled", true);
        // }
    }
});

// $('#btn-upload').on('click', function(){
//     let table = $('#table-hasil-rad').DataTable();
//     let tableData = table.row(".selected").data();
//     if (typeof tableData !== 'undefined') {
//         const primaryId = tableData.primary;
//         const tindakan_id = tableData.tindakan_id;
//         const tindakanpelayanan_id = tableData.tindakanpelayanan_id;
//         const pasienmasukpenunjang_id = $('.pasienmasukpenunjang_id').val();
//         const dataPost = {
//             tindakan_id: tindakan_id,
//             tindakanpelayanan_id: tindakanpelayanan_id,
//             pasienmasukpenunjang_id: pasienmasukpenunjang_id,
//         };
//     }

//     const link = '/radiologi/input-hasil/upload-hasil?id=' + primaryId + '&tindakan_id=' + tindakan_id + '&penunjang_id=' + pasienmasukpenunjang_id;
//     $(this).docoForm("click", {
//         url: link,
//         data: dataPost,
//         // confirmMessage: i18next.t("Apakah anda yakin untuk menyimpan data ini ? waktu ambil foto akan tersetting dan hasil pemeriksaan dapat diisi"),
//         success: function (data) {
//             $("#btn-upload").prop("disabled", true);
//             table.draw();
//         }
//     });
// });

function ambilFoto(tableData, skipConfirm=false, redirectToExpertise=false) {
    const primaryId = tableData.primary;
    const tindakan_id = tableData.tindakan_id;
    const pemeriksaanradiologi_id = tableData.pemeriksaanradiologi_id;
    const tindakanpelayanan_id = tableData.tindakanpelayanan_id;
    const penunjangId = tableData.penunjang_id;
    const status = tableData.status;
    const hasilpemeriksaanrad_id = tableData.hasilpemeriksaanrad_id;
    const pendaftaran_id = $('.pendaftaran_id').val();
    const pasienmasukpenunjang_id = $('.pasienmasukpenunjang_id').val();
    const pasienadmisi_id = $('.pasienadmisi_id').val();
    const pegawai_id = $('.pegawai_id').val();
    const dataPost = {
        tindakanpelayanan_id: tindakanpelayanan_id,
        pendaftaran_id: pendaftaran_id,
        pasienmasukpenunjang_id: pasienmasukpenunjang_id,
        pasienadmisi_id: pasienadmisi_id,
        pegawai_id: pegawai_id,
        pemeriksaanradiologi_id: pemeriksaanradiologi_id
    };
    const link = '/radiologi/input-hasil/ambil-foto?id=' + primaryId + '&tindakan_id=' + tindakan_id;

    console.log(dataPost);

    $().docoForm("click", {
        url: link,
        data: dataPost,
        skipConfirm: skipConfirm,
        confirmMessage: i18next.t("Apakah anda yakin untuk menyimpan data ini ? waktu ambil foto akan tersetting dan hasil pemeriksaan dapat diisi"),
        success: function (data) {
            $("#ambil-foto").prop("disabled", true);
            table.draw();
            if (redirectToExpertise) {
                const link = '/radiologi/expertise/index?id=' + primaryId +
                    '&hasilpemeriksaanrad_id=' + data.response.hasilpemeriksaanrad_id +
                    '&penunjang_id=' + penunjangId +
                    '&status=' + status;

                window.open(link, '_self');
            }
        }
    });

}

$('#ambil-foto').on('click', function (e, redirectToExpertise) {
    let table = $('#table-hasil-rad').DataTable();
    let tableData = table.row(".selected").data();
    if (typeof tableData !== 'undefined') {
        ambilFoto(tableData, false, redirectToExpertise);
    } else {
        docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Belum ada data yang dipilih!"));
        return false;
    }
});

$('#expertise').on('click', function () {
    let table = $('#table-hasil-rad').DataTable();
    let tableData = table.row(".selected").data();
    if (typeof tableData !== 'undefined') {
        const primaryId = tableData.primary;
        const penunjangId = tableData.penunjang_id;
        const status = tableData.status;
        const hasilpemeriksaanrad_id = tableData.hasilpemeriksaanrad_id;
        if (typeof hasilpemeriksaanrad_id == 'undefined' || hasilpemeriksaanrad_id == '') {
            $('#ambil-foto').trigger('click', [true]);
        } else {
            const link = '/radiologi/expertise/index?id=' + primaryId +
                '&hasilpemeriksaanrad_id=' + hasilpemeriksaanrad_id +
                '&penunjang_id=' + penunjangId +
                '&status=' + status;

            window.open(link, '_self');
        }

    } else {
        docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Belum ada data yang dipilih!"));
        return false;
    }
});

$('#verifikasi').on('click', function () {
    let table = $('#table-hasil-rad').DataTable();
    let tableData = table.row(".selected").data();
    if (typeof tableData !== 'undefined') {
        const id = tableData.penunjang_id;
        const hasilId = tableData.hasilpemeriksaanrad_id;
        const tindakanId = tableData.primary;
        let link = `/radiologi/hasil-rad/verifikasi?id=${id}&hasilId=${hasilId}&tindakan_id=${tindakanId}`;
        $(this).docoForm("click", {
            url: link,
            confirmMessage: i18next.t("Apakah anda yakin untuk verifikasi ? edit data expertise tetap dapat dilakukan setelah verifikasi"),
            success: function (data) {
                location.reload();
            }
        });
    } else {
        docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Belum ada data yang dipilih!"));
        return false;
    }
})

$('#batal-verifikasi').on('click', function () {
    let table = $('#table-hasil-rad').DataTable();
    let tableData = table.row(".selected").data();
    if (typeof tableData !== 'undefined') {
        const id = tableData.penunjang_id;
        const hasilId = tableData.hasilpemeriksaanrad_id;
        const tindakanId = tableData.primary;
        let link = `/radiologi/hasil-rad/batal-verifikasi?id=${id}&hasilId=${hasilId}&tindakan_id=${tindakanId}`;
        $(this).docoForm("click", {
            url: link,
            confirmMessage: i18next.t("Apakah anda yakin akan membatalkan verifikasi ?"),
            success: function (data) {
                location.reload();
            }
        });
    } else {
        docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Belum ada data yang dipilih!"));
        return false;
    }
})


$('#catatan-radiologi').on('change', function (e) {
    e.preventDefault();
    var _idPenunjang = $('input[name=pasienmasukpenunjang_id]').val();
    console.log(_idPenunjang)
    $().docoForm('click', {
        url: `/radiologi/hasil-rad/update-catatan?id=${_idPenunjang}`,
        skipConfirm: true,
        data: {
            catatan: $(this).val()
        }
    })
})

if (verifikasi) {
    $('#verifikasi').prop('disabled', true);
}

// if (is_bayar == 1) {
//     $("#obat-alkes").prop("disabled", true);
// }


$('#batal-input-hasil').on('click', function () {
    let table = $('#table-hasil-rad').DataTable();
    let tableData = table.row(".selected").data();

    if (typeof tableData !== 'undefined') {

        const primaryId = tableData.primary;
        const tindakan_id = tableData.tindakan_id;
        const pemeriksaanradiologi_id = tableData.pemeriksaanradiologi_id;
        const hasilpemeriksaanrad_id = tableData.hasilpemeriksaanrad_id;
        const tindakanpelayanan_id = tableData.tindakanpelayanan_id;
        const pasienmasukpenunjang_id = tableData.penunjang_id;

        if (!hasilpemeriksaanrad_id) {
            docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Tidak bisa membatalkan, belum ada hasil!"));
            return false;
        }
        if (verifikasi) {
            docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Tidak bisa membatalkan, data sudah diverifikasi!"));
            return false;
        }
        const dataPost = {
            hasilpemeriksaanrad_id: hasilpemeriksaanrad_id,
            pasienmasukpenunjang_id: pasienmasukpenunjang_id,
        };
        const link = '/radiologi/input-hasil/batal';

        $(this).docoForm("click", {
            url: link,
            data: dataPost,
            confirmMessage: i18next.t("Apakah Anda yakin akan membatalkan input hasil Radiologi?"),
            success: function (data) {
                $("#batal-input-hasil").prop("disabled", true);
                table.draw();
                is_periksa = false;
            }
        });
    } else {
        docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Belum ada data yang dipilih!"));
        return false;
    }
});