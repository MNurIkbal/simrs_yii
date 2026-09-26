var nonBpjs = false;
var bpjs = false;
var _noajuan = [];
var _penjaminId = null;
var _caraBayar = null;
var _penjamin = null;
var date = new Date();
var _randString = null;

var dateStr =
   date.getFullYear() + "-" +
   ("00" + (date.getMonth() + 1)).slice(-2) + "-" +
   ("00" + date.getDate()).slice(-2) + " " +
   ("00" + date.getHours()).slice(-2) + ":" +
   ("00" + date.getMinutes()).slice(-2) + ":" +
   ("00" + date.getTime()).slice(-2);

var startDate = new Date(date);
startDate.setMinutes(startDate.getMinutes() - 1);
var dateStart =
   date.getFullYear() + "-" +
   ("00" + (startDate.getMonth() + 1)).slice(-2) + "-" +
   ("00" + startDate.getDate()).slice(-2) + " " +
   ("00" + startDate.getHours()).slice(-2) + ":" +
   ("00" + startDate.getMinutes()).slice(-2) + ":" +
   ("00" + startDate.getTime()).slice(-2);

var _getDate = function () {
    function AddZero(num) {
       return (num >= 0 && num < 10) ? "0" + num : num + "";
    }
    var monthNamesx = ["01","02","03","04","05","06","07","08","09","10","11","12"];
    var now = new Date();
    var strDateTime = [
        [now.getFullYear(), 
        monthNamesx[now.getMonth()], 
        AddZero(now.getDate())].join("-"), 
        [AddZero(now.getHours()), 
        AddZero(now.getMinutes()),time()].join(":")].join(" ");
    return strDateTime;
 }

$('#penerimaanpembayaranform-is_nontunai').on('change', function(){
    if($(this).is(':checked')){
        $('#penerimaanpembayaranform-pemilik_rekening').removeAttr('readonly')
        $('#penerimaanpembayaranform-bank').removeAttr('readonly')
        $('#penerimaanpembayaranform-no_rekening').removeAttr('readonly')
    }else{
        $('#penerimaanpembayaranform-pemilik_rekening').val('').attr('readonly', true)
        $('#penerimaanpembayaranform-bank').val('').attr('readonly', true)
        $('#penerimaanpembayaranform-no_rekening').val('').attr('readonly', true)
    }
})
 
$('.btn-add-ajuan').on('click', function(e){
    e.preventDefault();
    $("#penerimaanpembayaranform-tgl_terimabayarklaim").val("");
    $().docoForm('click', {
        data: $('#form-ajuan').serializeArray(),
        url : '/penjamin-asuransi/transaksi-penerimaan-pembayaran/add-ajuan',
        skipConfirm: true,
        success: function(response){
            _table.draw();
            $('#form-ajuan')[0].reset();
            $('#no-pengajuanklaim').val('').trigger('change');
        }
    })
})

$(document).on('click', '.btn-hapus', function(){
    $("#penerimaanpembayaranform-tgl_terimabayarklaim").val("");
    var _key = $(this).attr('data-key');
    $().docoForm('click', {
        confirmMessage: 'Yakin Akan Menghapus Data Ajuan Ini?',
        data: {key: _key},
        skipConfirm : true,
        url : '/penjamin-asuransi/transaksi-penerimaan-pembayaran/hapus-ajuan',
        success: function(response){
            _table.draw();
        }
    })
});
 
$('#pembayaran').on('keyup', function(){
    var _val = parseInt( docoHelper.convertToAngka($(this).val()));
    var _totalPengajuan = parseInt(docoHelper.convertToAngka($('#total-pengajuan').val()));
    var _totalTerbayar = parseInt(docoHelper.convertToAngka($('#total-terbayar').val()));
    var nilaiMax = _totalPengajuan - _totalTerbayar;
    if (_val > nilaiMax) {
        $(this).val(nilaiMax).trigger('change');
        $('#total-sisapiutang').val(0).trigger('change');
        return false;
    }
    var _pengajuan = parseInt( docoHelper.convertToAngka($('#total-pengajuan').val() ) ) - parseInt( docoHelper.convertToAngka($('#total-terbayar').val() ) );
    var _sisa = nilaiMax - _val;
    if (isNaN(_val)) {
        _sisa = nilaiMax;
    } 
    $('#total-sisapiutang').val(_sisa).trigger('change');
});
 
$('#total_terimabayar').on('keyup', function(){
    var _val = parseInt( docoHelper.convertToAngka($(this).val()));
    var _totalPengajuan = parseInt(docoHelper.convertToAngka($('#total-pengajuan').val()));
    var _totalTerbayar = parseInt(docoHelper.convertToAngka($('#total-terbayar').val()));
    var nilaiMax = _totalPengajuan - _totalTerbayar;
    if (_val > nilaiMax) {
        $(this).val(nilaiMax).trigger('change');
        $('#total-sisapiutang').val(0).trigger('change');
        return false;
    }
    var _pengajuan = parseInt( docoHelper.convertToAngka($('#total-pengajuan').val() ) ) - parseInt( docoHelper.convertToAngka($('#total-terbayar').val() ) );
    var _sisa = nilaiMax - _val;
    if (isNaN(_val)) {
        _sisa = nilaiMax;
    } 
    $('#total-sisapiutang').val(_sisa).trigger('change');
});

$(document).on('change','#penerimaanpembayaranform-carabayar_id', function (event) {
    event.preventDefault();
    var _value = $(this).val();
    if (_value) {
        _caraBayar = _value;
        if(_caraBayar == _caraBayarBpjs) {
            $('.before-upload-wrapper').show();
            $('#total_terimabayar').prop('readonly', true);
            $('#penerimaanpembayaranform-is_nontunai').prop('checked', true).trigger('change');
            $('.form-ajuan-detail').hide();
            initTableBpjs({
                data: []
            });
        }
        else {
            $('.before-upload-wrapper').hide();
            $('#total_terimabayar').prop('readonly', false);
            $('#penerimaanpembayaranform-is_nontunai').prop('checked', false).trigger('change');
            initTableNonBpjs({
                data: []
            })
        }
    } 
});

$(document).on('change','#penjamin_id', function (event) {
    event.preventDefault();
    var _value = $(this).val();
    docoResetForm($('#form-ajuan'));
    if(_caraBayar != _caraBayarBpjs) {
        if (_value) {
            _penjaminId = _value;
            $('.form-ajuan-detail').show();
        } else {
            _penjaminId = null;
            $('.form-ajuan-detail').hide();
        }
    }
});

$(document).ready(function(){
    $('.before-upload-wrapper').hide();
    initTableNonBpjs({
        data: []
    })
    $('#penerimaanpembayaranform-pegawaipenerima_id').select2Pegawai(ruangan);
});



$(document).on('click','.data-reset', function (event) {
    event.preventDefault();
    moment.locale('id')
    $.ajax({
        url : '/penjamin-asuransi/transaksi-penerimaan-pembayaran/reset-session',
        success : function (data) {
            _table.draw();
            docoResetForm($('#form-penerimaan-pembayaran'));
            $("#penerimaanpembayaranform-tgl_terimabayarklaim").val(moment().format("YYYY-MM-DD HH:mm:ss")).trigger('change');
            
        }
    });
})

function initTableBpjs({
    data
}) {
    uploadTemplateTable = $('#tbl-ajuan').DataTable({
        data,
        columns: [
            {
                title: "No",
                orderable: false,
                data: 'no',
            },
            {
                title: "No Pengajuan",
                data: 'no_pengajuan',
            },
            {
                title: "No SEP",
                data: 'no_sep',
            },
            {
                title: "Tanggal Verifikasi",
                data: 'tgl_verifikasi',
            },
            {
                title: "Tagihan Rumah Sakit",
                data: 'riil_rs',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: "Tagihan Diajukan",
                data: 'diajukan',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: "Tagihan Disetujui",
                data: 'disetujui',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
        ],
        scrollX: '100%',
        destroy: true,
        filter: false,
    });
    setTimeout(() => {
        $('#tbl-ajuan').DataTable().columns.adjust();
    }, 100);
}

function initTableNonBpjs({
    data
}) {
    uploadTemplateTable = $('#tbl-ajuan').DataTable({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        scrollX: true,
        info: false,
        destroy: true,
        ajax: baseUrl+'penjamin-asuransi/transaksi-penerimaan-pembayaran/get-session',
        columns: [
            {
                title: "No",
                orderable: false,
                data: 'no',
            },
            {
                title: "No Pengajuan",
                data: 'no_pengajuan',
            },
            {
                title: "Total Pengajuan",
                data: 'total_pengajuan',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: "Telah Bayar",
                data: 'total_terbayar',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: "Pembayaran",
                data: 'pembayaran',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: "Sisa Piutang",
                data: 'total_sisapiutang',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: "Aksi",
                data: 'aksi',
            },
        ],
        drawCallback : function (settings) {
            var api = this.api();
            var dataRows = api.rows( {page:"current"} ).data();
            _caraBayar = _penjamin = null;
            if (dataRows.length > 0) {
                let response = settings.jqXHR.responseJSON.data;
                if(response.length > 1){
                    response.sort(function(a,b){
                        return new Date(b.tgl_pengajuanklaim) - new Date(a.tgl_pengajuanklaim);
                    });
                }
                if(response.length > 0){
                    $("#penerimaanpembayaranform-tgl_terimabayarklaim").datetimepicker({
                        format : 'yyyy-mm-dd hh:ii:ss',
                        minuteStep : 1,
                        timePicker24Hour : true,
                        startDate : response[0].tgl_pengajuanklaim,
                        autoclose : false,
                        endDate : dateStr,
     
                    });
                    $("#penerimaanpembayaranform-tgl_terimabayarklaim").datetimepicker('setStartDate', response[0].tgl_pengajuanklaim);
                    $("#penerimaanpembayaranform-tgl_terimabayarklaim").datetimepicker('setEndDate', dateStr);
                }
                var datepicker= document.getElementsByClassName('datetimepicker');
                if(datepicker.length > 1) {
                    document.getElementsByClassName('datetimepicker')[0].remove();
                }
                $(document).on('click', function(e) {
                    if (e.target.id !== 'penerimaanpembayaranform-tgl_terimabayarklaim') {
                        document.getElementsByClassName('datetimepicker')[0].style.display = 'none';
                    } 
                })
                $("#penerimaanpembayaranform-tgl_terimabayarklaim").on('click', function(){
                    document.getElementsByClassName('datetimepicker')[0].style.display = 'block';
                });
                _caraBayar = $('#penerimaanpembayaranform-carabayar_id').val();
                _penjamin = $('#penjamin_id').val();
                $('.cara-pembayaran').prop('disabled',true);
            } else {
                $("#penerimaanpembayaranform-tgl_terimabayarklaim").datetimepicker({
                    format : 'yyyy-mm-dd hh:ii:ss',
                    minuteStep : 1,
                    timePicker24Hour : true,
                    startDate : startDate,
                    autoclose : false,
                    endDate : dateStr,
                });
                $("#penerimaanpembayaranform-tgl_terimabayarklaim").datetimepicker('setStartDate', startDate);
                $("#penerimaanpembayaranform-tgl_terimabayarklaim").datetimepicker('setEndDate', dateStr);
                var datepicker= document.getElementsByClassName('datetimepicker');
                if(datepicker.length > 1) {
                    document.getElementsByClassName('datetimepicker')[0].remove();
                }
                $(document).on('click', function(e) {
                    if (e.target.id !== 'penerimaanpembayaranform-tgl_terimabayarklaim') {
                        document.getElementsByClassName('datetimepicker')[0].style.display = 'none';
                    } 
                })
                $("#penerimaanpembayaranform-tgl_terimabayarklaim").on('click', function(){
                    document.getElementsByClassName('datetimepicker')[0].style.display = 'block';
                });
                $('.cara-pembayaran').prop('disabled',false);
            }
        },
    });
    setTimeout(() => {
        $('#tbl-ajuan').DataTable().columns.adjust();
    }, 100);
}

function failedProgressBar() {
    $("#label-file").html('Pilih File');
    $(".lihat_file").attr('data-file', 'Pilih File');
    $(".myprogress").css("width", "0%");
    $(".myprogress").html("0%");
    $('.msg').text('');
}

function toggleUploadWrapper() {
    $('.myprogress').css('width', '0');
    $('.msg').text('');
}
 
$('#simpan-penerimaan').unbind();
$('#simpan-penerimaan').bind('click', function (e) {
    event.preventDefault();
    var _data = $('#form-penerimaan-pembayaran').serializeArray();
    $().docoForm('click', {
        url : '/penjamin-asuransi/transaksi-penerimaan-pembayaran/save?randString=' + _randString,
        data : _data,
        skipSuccessNotif : true,
        success : function (data) {
            var _noPembayaran = data.response.no_pembayaran
            $('div').removeClass('has-error');
            $('span.help-block.error').remove();
            $('div.help-block.error').remove();
            docoResetForm($('#form-penerimaan-pembayaran'));
            moment.locale('id')
            $("#penerimaanpembayaranform-tgl_terimabayarklaim").val(moment().format("YYYY-MM-DD HH:mm:ss")).trigger('change');
            if(_caraBayar != _caraBayarBpjs) {
                _table.draw();
            }
            else {
                initTableBpjs({
                    data: []
                });
                $('#total_terimabayar').prop('readonly', false);
                $('#penerimaanpembayaranform-is_nontunai').prop('checked', false).trigger('change');
            }
            (new PNotify({
                title: "Proses Berhasil !",
                text: "Data Penerimaan Pembayaran Klaim dengan Nomor Pembayaran <strong>" + _noPembayaran + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
                addclass: "alert alert-success alert-arrow-right alert-styled-right",
                type: "success",
                buttons: {
                    closer: false,
                    sticker: false
                },
                hide: false,
                confirm: {
                    confirm: true,
                    buttons: [
                    {
                        text: 'Ya',
                        addClass: 'btn btn-xs btn-success',
                    },
                    {
                        text: 'Tidak',
                        addClass: 'btn btn-xs btn-danger',
                    }
                    ]
                },
                history: {
                    history: false
                }
            })).get().on('pnotify.confirm', function() {
                window.open("/penjamin-asuransi/transaksi-penerimaan-pembayaran/cetak-penerimaan?no_pembayaran=" + _noPembayaran);
            }).on('pnotify.cancel', function() {

            });
        }
    });
})

$('.btn-modal-upload').unbind();