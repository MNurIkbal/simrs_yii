// $("#btn-input").prop("disabled", false);

// $(document).on("click", "#table-hasil-lab tbody tr", function () {
//     // Try catch
//     try {
//         // Get primary
//         primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
//     } catch (e) {
//         // Make it false
//         primaryKey = false;
//     }

//     let table = $('#table-hasil-lab').DataTable();
//     let tableData = table.row(".selected").data();
//     if (typeof tableData !== 'undefined') {
//         const primaryId = tableData.primary;
//         const is_expertise = tableData.is_expertise;
//         if (is_expertise) {
//             $("#btn-input").prop("disabled", true);
//         } else {
//             $("#btn-input").prop("disabled", false);
//         }
//     }
// });

$('#verifikasi').on('click', function() {
    let link = $(this).attr('data-target');
    $(this).docoForm("click", {
        url: link,
        confirmMessage: i18next.t("Apa anda yakin ? Data tidak bisa di edit lagi jika sudah terverifikasi"),
        success: function (data) {
            location.reload();
        }
    });
})

$('#cetak-wynacom').on('click', function() {
    let link = $(this).attr('data-target');
    window.open(link);
})

let verifikasi = $('.terverifikasi').text();
if (verifikasi) {
    $('#btn-input').prop('disabled', true).css('background-color', '#555555');
    $('#btn-ulang').prop('disabled', true).css('background-color', '#555555');
    $('#obat-alkes').prop('disabled', true).css('background-color', '#555555');
    $('#verifikasi').prop('disabled', true).css('background-color', '#555555');
}