$(document).ready(function () {
    $("#search_nama_pasien").on("change", function(e) {
        $.ajax({
            url: "/apotek/informasi-retur/get-data-pasien-retur",
            type: "get",
            data: {
                identifier: $(this).val()
            },
            success: function(response) {
                $(".data-pasien").css("display", "block")
                var detail = response.data
                populateDetail(detail)
                return true;
            },
        });
    });

    function populateDetail(detail) {
        var _no = 1
        var _html = ""
        $.each(detail, function (key, value) {
            _html += `
                <tr>
                    <td>`+ _no +`</td>
                    <td>`+ value.tanggal_pendaftaran +`</td>
                    <td>`+ value.nama_pasien +` - `+ value.no_pendaftaran +`</td>
                    <td>`+ value.instalasi_nama +` - `+ value.ruanganakhir +`</td>
                    <td>`+ value.carabayar_nama +` - `+ value.penjamin_nama +`</td>
                    <td>`+ value.status_periksa +`</td>
                    <td><a id="pilih-data" class="btn btn-sm btn-success" data-id="`+value.id_encrypt+`" data-jumlah="`+value.jumlah_transaksi+`" data-status="`+value.status_retur+`" data-noretur="`+value.no_returresep+`"><i class=\"fa fa-check\"></i> Pilih</a></td>
                </tr>
            `;
            _no++;
        })

        if (_html === "") {
          _html += "<tr>";
          _html +=
            '<td colspan="7" id="data-null" class="text-center">Data Tidak Ditemukan</td>';
          _html += "</tr>";
        }
        $("#detail-pasien").html("");
        $("#detail-pasien").prepend(_html);
    }

    $(document).on("click", "#pilih-data", function (event) {
        event.preventDefault();
        var daftar = $(this).data("id");
        var jumlah = $(this).data("jumlah");
        var statusRetur = $(this).data("status");
        var noretur = $(this).data("noretur");
        console.log(noretur)

        if (jumlah == null) {
          docoNotification('warning', 'Perhatian!', 'Pasien tidak memiliki data transaksi resep/BMHP');
        } else {
            if(statusRetur > 0){
                docoNotification('warning', 'Perhatian!', 'Sudah terdapat transaksi retur dengan nomor "'+noretur+'".');
            }else{
                window.location.href = '/apotek/informasi-retur/tambah?id='+daftar;
            }
        }
    });
})
