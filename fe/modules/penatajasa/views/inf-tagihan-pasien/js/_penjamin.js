/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

// Global Variable

var input = document.getElementById("nokartuasuransi");

$(document).ready(function() {
    // infinity scroll Select2 with helper docoHealth.js
    // config = {} : untuk melakukan custom config pada js untuk kebutuhan data di select2 / modifikasi response ajax
    $("#penjamin_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Cari Penjamin --',      // custom placeholder (optional) default null
            _api : '/kasir/pembayaran-tagihan/list-penjamin',   // get data
        }
    )

    $('#penjamin_id').on('change', function(e){
        // reset field
        $("#nokartuasuransi").val("");
    })

    // for save data table temporary
    $('#simpan-tagihan-penjamin').on('click', function(e){
        e.preventDefault()
        if ($('#penjamin_id').val() == null || $('#penjamin_id').val() == "") {
            docoNotification("error", "Data Penjamin Belum Dipilih!", "pilih salah satu penjamin!");
            return false
        }

        if ($('#nokartuasuransi').val() == null || $('#nokartuasuransi').val() == "") {
            docoNotification("error", "Nomor Kartu Belum di Input!", "harus input Nomor Kartu terlebih dahulu!");
            return false
        }

        if ($('#nominal_dijamin').val() == null || $('#nominal_dijamin').val() == "") {
            docoNotification("error", "Nominal Dijamin Belum di Input!", "harus input nominal yang dijamin terlebih dahulu!");
            return false
        }

        if (docoHelper.convertToAngka($('#nominal_dijamin').val() < 0 )) {
            docoNotification("error", "Nominal Dijamin Tidak Boleh Minus!", "harus input nominal lebih dari 0 (nol)!");
            return false
        }
        
        var harga = $("#penjamin_id option:selected").data()
        var penjamin_carabayar = $("#penjamin_id option:selected").text()
        var selected = harga.data.datavalue || {}
        var today = new Date();
        var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate()+' '+today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
        $('#id_penjamin').val(selected.penjamin_id)
        $('#nokartuasuransi').val()
        $('#nominal_dijamin').val(docoHelper.convertToAngka($('#nominal_dijamin').val()))
        $('#tgl_konfirmasi').val(date)
        $('#pendaftaran_id').val(id_pendaftaran)
        $('#asuransipasien_id').val()
        $('#penjamin_carabayar').val(penjamin_carabayar)
        $('#pasien_id').val(pasien_id)
        $('#nama_pasien').val(nama_pasien)
        $('#carabayar_nama').val(selected.carabayar_nama)
        $('#carabayar_id').val(selected.carabayar_id)
        $('#penjamin_nama').val(selected.penjamin_nama)
        $('#nama_pemilik').val()
        $('#namapemilikasuransi').val()
        $('#nomorpokokperusahaan').val()
        $('#kelastanggunganasuransi_id').val()
        $('#namaperusahaan').val()
        $('#status_konfirmasi').val()
        var _form = $('#multi-penjamin-form');

        $(this).docoForm('click',{
            url : _form.attr('action'),
            data : _form.serializeArray(),
            success : function (res) {
                var pendaftaran_ids = res.response.pendaftaran_ids;
                var pendaftaranID = res.response.pendaftaran_id;
                penjamin.draw()
                $("#nokartuasuransi").val("");
                $("#nominal_dijamin").val("");
                $('#penjamin_id').val(null).trigger('change');
            }
        });

    })
    
    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("simpan-tagihan-penjamin").click();
        }
    });
});
