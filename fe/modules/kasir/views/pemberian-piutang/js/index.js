$(".panel-informasi").hide();
$(".detail-form").hide();

let min_tgl_pendaftaran;
let tagihanRanap = 0;

$(document).ready(function(){
    $('label[for="karyawan_id"]').parent().prepend( '<input type="checkbox" id="check_pegawai" value="0">' );
    $('#pemberianpiutangform-pegawai_id').select2({
        placeholder: '— Pilih —',
        minimumInputLength: 3,
        ajax: {
            url: '/kasir/pemberian-piutang/get-data-pegawai',
            dataType: 'json',
            quietMillis: 250,
            data: function(params){
                var query = {
                    search: params,
                }
                return params;
            },
            processResults: function (data) {
                return {
                    results: data.result
                };
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        },
    });

    $("#pendaftaran_id").select2({
        placeholder: "-- Cari No Pendaftaran / Nama Pasien / No Resep / No Rekam Medik --",
        minimumInputLength: 3,
        ajax: {
            url: "/kasir/pemberian-piutang/get-data-pendaftaran-reseptur",
            dataType: "json",
            delay: 500,
            data: function(params) { 
                return {
                    q:params.term, 
                    page:params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.result,
                    pagination: {
                        more: data.pagination.more
                    }
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (markup) { 
                return markup; 
            },
            templateResult: function(object) { 
                return object.text; 
            },
            templateSelection: function (subject) { 
                return subject.text; 
            },
        },
    }).on('select2:select', function(item){
        var data = item.params.data.datavalue;
        if(data.total_piutang > 0) {
            docoNotification('warning', 'Peringatan', 'No Pendaftaran/No Reseptur ini sudah pernah diberi piutang.');
        }
        else if(data.total_tagihan == 0) {
            docoNotification('warning', 'Peringatan', 'Pasien Tidak Memiliki Tagihan.');
        }
        else if (data.is_gabungbilling && data.pendaftaran_id != data.ref_pendaftaran_id) {
            docoNotification('warning', 'Peringatan', 'Pendaftaran sudah digabung billing. silahkan lakukan pemberian piutang ke '+data.ref_no_pendaftaran);
        }
        else {
            $(".btn-simpan").prop("disabled", false);
            $("#pendaftaran_id_hidden").val($(this).val());
            $("#pendaftaran_id").prop("disabled", true);
            $('.panel-informasi').show();
            $('.detail-form').show();

            var _jenis = data.jenis;
            var _is_pembulatankeatas = data.is_pembulatankeatas;
            var _satuanpembulatan = data.satuanpembulatan;
            var _total_tagihan = data.total_tagihan;
            var _tagihanRanap = parseInt(data.tagihan_ranap);
            var _piutang_sudahbayar = data.piutang_sudahbayar;
            var _balance_piutang = _total_tagihan - _piutang_sudahbayar;
            var _tgl_pendaftaran = data.tgl_pendaftaran;
            var _tgl_pendaftaran_value = data.tgl_pendaftaran_value;
            var _tarif_max = parseInt(data.tarif_max);
            var _biaya_admin = data.biaya_admin;

            docoHelper.is_pembulatankeatas = _is_pembulatankeatas;
            docoHelper.satuanpembulatan = _satuanpembulatan;
            
            var _total = parseInt(_total_tagihan) + parseInt(_biaya_admin);
            var d = new Date(_tgl_pendaftaran.split('-').reverse().join('-'));
            var dd = d.getDate();
            var mm = d.getMonth()+1;
            var yy = d.getFullYear();

            docoHelper.pembulatan(_total, $("#pembulatan"),$("#total-pembulatan"));
            var totalPembulatan = docoHelper.convertToAngka($("#total-pembulatan").val())

            $('#pemberianpiutangform-tgl_pemberianpiutang').pickadate({
                format: 'dd-mm-yyyy',
                min: [yy,mm-1,dd],
                max: new Date(),
            });

            if (_jenis == 'resep_bebas') {
                $("#pendaftaran_id_hidden").val(data.pendaftaran_id);
                $('#penjualanresep_id').val(data.pendaftaran_id);
                $('#no_resep').val(data.no_pendaftaran);
            }else if (_jenis == 'tagihan_rs') {
                $('#penjualanresep_id').val(null);
                $('#no_resep').val(null);
            }
            $('#tgl_pendaftaran').val(_tgl_pendaftaran_value);
            $('.no_rekam_medik').html(data.no_rekam_medik);
            $('.nama_pasien').html(data.nama_pasien);
            $('.tanggal_lahir').html(data.tanggal_lahir);
            $('.tgl_pendaftaran').html(_tgl_pendaftaran);
            $('.total_tagihan').html(docoHelper.convertToRupiah(totalPembulatan)).css('font-weight', 'bold').css('font-size', '17px');
            $('#total_bayarpiutang').val(docoHelper.convertToRupiah(0)).css('font-weight', 'bold');
            $('#total_sisapiutang').val(docoHelper.convertToRupiah(0)).css('font-weight', 'bold');
            $("#total_tagihan").val(totalPembulatan);
        }
    });

    $("#karyawan_id").docoPaginationSelec2(
        config = {
            placeholder : '-- Cari Karyawan --',     
            _api : '/kasir/pemberian-piutang/get-data-karyawan',
        }
    );
    $('#karyawan_id').prop('disabled', true);
    $('#is_checkpegawai').val(0);

   
});

$("span.input-group-addon > #refresh-pendaftaran_id").on("click", function(){
    $(".btn-simpan").prop("disabled", true);
    $("#pendaftaran_id").prop("disabled", false);
    $(".panel-informasi").hide();
    $(".detail-form").hide();
    $("#pendaftaran_id").val("").trigger("change");
    $("#pemberianpiutangform-pegawai_id").val("").trigger("change");
    $("#pemberianpiutangform-total_piutang").val(null).trigger("change");
    $("#pemberianpiutangform-catatan").val(null).trigger("change");
});

$("#refresh-pegawai_id").on("click", function(){
    $("#pemberianpiutangform-pegawai_id").val("").trigger("change");
});

$("#refresh-karyawan_id").on("click", function(){
    $("#karyawan_id").val("").trigger("change");
});

$("#ajax-form").docoForm("submit",{
    success : function(data) {
        $("#refresh-pendaftaran_id").trigger('click');
    }
});

var getTagihanRanap = function(pendaftaran_id) {
    $.ajax({
        url: '/kasir/pemberian-piutang/get-tagihan-ranap?pendaftaran_id=' + pendaftaran_id,
        dataType: "json",
        method: 'GET',
        success: function(data) {
            setTimeout(function(){
                tagihanRanap = data;
                $("#tagihan_ranap").val(data).trigger('change');
            },100);
        }
    });
}

$('.data_karyawan').on('click',function(){
    if($('#check_pegawai').is(':checked')){
        $('#karyawan_id').prop('disabled', false);
        $('#is_checkpegawai').val(1);
    } else {
        $('#karyawan_id').prop('disabled', true);
        $('#karyawan_id').empty();
        $('#is_checkpegawai').val(0);
    }
})

