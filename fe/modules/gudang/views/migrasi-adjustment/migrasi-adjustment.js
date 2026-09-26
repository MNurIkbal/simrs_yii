$(document).ready(function() {
    $("#btn-submit-migrasi").on('click', function() {
        $("#invalid_wrapper").css("display", "none");
        $("#invalid_table tbody").remove();
        $("#invalid_table").append("<tbody></tbody>");
        $("#import-adjustment").submit();
    });

    $("#import-adjustment").submit(function(e) {
        e.preventDefault();
        var dataForm = new FormData(this);

        $(this).docoForm("submit", {
            dataType: false,
            cache: false,
            contentType: false,
            processData: false,
            data: dataForm,
            method: 'post',
            isUpload: true,
            skipSuccessNotif: true,
            success: function(data) {
                $("#btn-submit-migrasi").attr("disabled", true);
                var response = data.response;

                if (response.no_adjusmen == null) {
                    docoNotification('warning', 'Perhatian!', "Tidak ada data valid");
                    renderInvalidData(response.invalid_data.data);
                    $("#btn-submit-migrasi").attr("disabled", false);
                    return true;
                }

                if (response.stok_habis != false) {
                    docoNotification('warning', 'Perhatian!', response.stok_habis);
                }

                if (response.all_valid == false && response.no_adjusmen != null) {
                    docoNotification('success', response.title, response.text + "Dengan No.Transaksi "+response.no_adjusmen);
                    docoNotification('warning', 'Perhatian!', "Data berhasil tersimpan, terdapat beberapa data yang gagal karena tidak valid");
                    renderInvalidData(response.invalid_data.data);
                    $("#btn-submit-migrasi").attr("disabled", false);
                } else {
                    docoNotification('success', response.title, response.text + "Dengan No.Transaksi "+response.no_adjusmen);
                    setTimeout(function(){
                        location.reload();
                    }, 3500);
                }

            },
            error: function(error) {
                docoNotification('error', 'Gagal!', "Terdapat  kesalahan")
            }
        });
    });

    function renderInvalidData(data) {
        $("#invalid_wrapper").css("display", "inherit");
        var table = $("#invalid_table tbody");
        $.each(data, function(key, value) {
            var newRow = "";
            newRow = "<tr class='danger'>";
            newRow += "<td>" +value.no+ "</td>";
            newRow += "<td>" +value.obatalkes_kode+ "</td>";
            newRow += "<td>" +value.obatalkes_nama+ "</td>";
            newRow += "<td>" +value.tglkadaluarsa+ "</td>";
            newRow += "<td>" +value.satuan_nama+ "</td>";
            newRow += "<td>" +value.qty+ "</td>";
            newRow += "<td>" +value.harganetto+ "</td>";
            newRow += "</tr>";
            table.append(newRow);
        })
    }
})




