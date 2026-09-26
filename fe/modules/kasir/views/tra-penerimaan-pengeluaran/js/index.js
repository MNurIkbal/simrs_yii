/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

$(document).ready(function(){

    var _endpoint = ''
    var _placeholder = ''
    // infinity scroll Select2 with helper docoHealth.js
    // config = {} : untuk melakukan custom config pada js untuk kebutuhan data di select2 / modifikasi response ajax
    $("#kategori").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Pilih Kategori --',      // custom placeholder (optional) default null
            _api : '/kasir/master-api/get-data-kategori-trx',   // get data
        }
    )

    $("#jenisnontunai_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Pilih Kategori --',      // custom placeholder (optional) default null
            _api : '/kasir/master-api/get-data-nontunai',   // get data
        }
    )

    $('#penerimaan-pengeluaran-form').docoForm('submit',{
        before: function(){
            return false;
        },  
        success : function(response) {
            this.formInput[0].reset();
            resetForm()
        }
    });
});

$('[name="PenerimaanPengeluaranForm[metode]"]').on('change', function (e) {
    e.preventDefault();
    var _value = parseInt($(this).val());
    if (_value === 28) {
        $('#jenisnontunai_id').prop("disabled", false)
    } else {
        $('#jenisnontunai_id').val(null).trigger('change');
        $('#jenisnontunai_id').prop("disabled", true)
    }
})

$("#tipe").on('change', function (e){
    e.preventDefault()
    var id = parseInt($(this).children("option:selected").val())
    if (id == _vendor) {
        $('.pendaftaran').addClass('hidden');
        _placeholder = '-- Pilih Vendor --'
        _endpoint = '/kasir/master-api/get-data-vendor'
    }
    if (id == _karyawan) {
        $('.pendaftaran').addClass('hidden');
        _placeholder = '-- Pilih Karyawan --'
        _endpoint = '/kasir/master-api/get-data-karyawan'
    }
    if (id == _pasien) {
        $('.pendaftaran').removeClass('hidden');
        _placeholder = '-- Pilih Pasien --'
        _endpoint = '/kasir/master-api/get-data-pasien'
    }

    $('#dari_kepada').val(null).trigger('change');
    $("#dari_kepada").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : _placeholder,      // custom placeholder (optional) default null
            _api : _endpoint,   // get data
        }
    )
});

$("#dari_kepada").on('change', function (e){
    e.preventDefault()
    var id = parseInt($(this).children("option:selected").val())
    var type = parseInt($("#tipe").children("option:selected").val())
    $('#no_pendaftaran').val(null).trigger('change');
    if($('#dari_kepada').children("option:selected").val() != null && type == _pasien){    
        $('#no_pendaftaran').select2InfinityScroll({
            url:'/kasir/tra-penerimaan-pengeluaran/get-no-pendaftaran?pasien_id='+id, 
        })
    }
});

function resetForm(){
    $('#dari_kepada').val(null).trigger('change');
    $('#tipe').val(null).trigger('change');
    $('#kategori').val(null).trigger('change');
    $('[name="PenerimaanPengeluaranForm[metode]"]').trigger('change');
    $('#jumlah').val("");
    $('#deskripsi').val("");
    $('#referensi').val("");
}