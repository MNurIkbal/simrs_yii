/*
* @Author: rizqi_fitrianto
* @Date:   2018-09-26 13:23:33
* @Last Modified by:   rizqi_fitrianto
* @Last Modified time: 2018-09-28 13:32:58
*/

var tempData = []
var _noajuan = [];
var _penjaminId = null;
var _caraBayar = null;
var _penjamin = null;

var date = new Date();

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

$('#penerimaanpembayaranform-is_nontunai').on('click', function(){
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

$('.data-pdf').on('click', function (event) {
    event.preventDefault();
    var _target = $(this).attr("data-target");
    window.open(_target,'_blank')
});

showLoader();

$(document).ready(function(){
    $(".styled, .multiselect-container input").uniform({
        radioClass: 'choice'
    });
    setTimeout(function(){
        _caraBayar = $('#penerimaanpembayaranform-carabayar_id').val();
        _penjamin = $('#penjamin_id').val();
    }, 300);

    if($('#penerimaanpembayaranform-carabayar_id').val() == _caraBayarBpjs) {
        $('#penerimaanpembayaranform-total_terimabayar').prop('readonly', true);
        $('.form-ajuan-detail').hide();
        $('#penerimaanpembayaranform-carabayar_id').prop('disabled', true);
        $('#penjamin_id').prop('disabled', true);
        initTableBpjs({
            data: tempData
        });
    }
    else {
        initTableNonBpjs({
            data: []
        });
    }
    
    $('#penjamin_id').trigger('change');
    $('#penerimaanpembayaranform-total_terimabayar').trigger('change');
    var _checked = $('#penerimaanpembayaranform-is_nontunai:checked').val();
    if (_checked) {
        $('#penerimaanpembayaranform-pemilik_rekening').attr('readonly', false)
        $('#penerimaanpembayaranform-bank').attr('readonly', false)
        $('#penerimaanpembayaranform-no_rekening').attr('readonly', false)
    }

    $('#penerimaanpembayaranform-pegawaipenerima_id').select2Pegawai(ruangan);

    $('#no-pengajuanklaim').select2noAjuan();

    $('#no-pengajuanklaim').on('change', function(){
        var _val = $(this).val();
        if(_val !== null){
            var _value = _noajuan[_val];
            var _terbayar = (_value.total_sisapiutang > 0) ? parseInt(_value.total_piutang) - parseInt(_value.total_sisapiutang) : 0;
            $('#total-pengajuan').val( docoHelper.convertToRupiah(_value.total_piutang))
            $('#total-terbayar').val( docoHelper.convertToRupiah( _terbayar ) )
            $('#total-sisapiutang').val( docoHelper.convertToRupiah( _value.total_sisapiutang ) )
            $('#pembayaran').val('')
            $('#pengajuanklaim-id').val(_value.no_pengajuanklaim)
            $('#status_pengajuan').val(_value.status_pengajuanklaim)
            $('#status_pengajuan').val(_value.status_pengajuanklaim)
            $('#tgl_pengajuanklaim').val(_value.tgl_pengajuanklaim)
        }
    })
    
})
$('.btn-add-ajuan').on('click', function(e) {
    showLoader();
    e.preventDefault();
    $().docoForm('click', {
        data: $('#form-ajuan').serializeArray(),
        url : '/penjamin-asuransi/informasi-penerimaan-pembayaran/add-ajuan',
        skipConfirm: true,
        success: function(response) {
            _table.draw();
            $('#form-ajuan')[0].reset();
            $('#no-pengajuanklaim').val('').trigger('change');
        }
    })
})

var _getDate = function () {
    function AddZero(num) {
        return (num >= 0 && num < 10) ? "0" + num : num + "";
    }
    var monthNamesx = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

    var now = new Date();
    var strDateTime = [
        [AddZero(now.getDate()), 
        monthNamesx[now.getMonth()], 
        now.getFullYear()].join("-"), 
        [AddZero(now.getHours()), 
        AddZero(now.getMinutes()),AddZero(now.getSeconds())].join(":")].join(" ");
    return strDateTime;
}

$(document).on('click', '.btn-hapus', function(){
    showLoader();
    var _key = $(this).attr('data-key');
    $().docoForm('click', {
        confirmMessage: 'Yakin Akan Menghapus Data Ajuan Ini?',
        data: {key: _key},
        skipConfirm : true,
        url : '/penjamin-asuransi/informasi-penerimaan-pembayaran/hapus-ajuan',
        success: function(response){
            _table.draw();
        }
    })
});

$('#pembayaran').on('keyup', function(){
    var _val = parseInt( docoHelper.convertToAngka($(this).val()));
    var _totalPengajuan = parseInt(docoHelper.convertToAngka($('#total-pengajuan').val()));
    if (_val > _totalPengajuan) {
        $(this).val(_totalPengajuan).trigger('change');
        $('#total-sisapiutang').val(0).trigger('change');
        return false;
    }
    var _pengajuan = parseInt( docoHelper.convertToAngka($('#total-pengajuan').val() ) ) - parseInt( docoHelper.convertToAngka($('#total-terbayar').val() ) );
    var _sisa = _pengajuan - _val;

    $('#total-sisapiutang').val( docoHelper.convertToRupiah(_sisa)).trigger('change');
});

$(document).on('change','#penjamin_id', function (event) {
    event.preventDefault();
    var _value = $(this).val();
    if (_statusAlokasi) {
        $('.form-ajuan-detail').hide();
        $('#form-penerimaan-pembayaran').find('input, textarea, select').prop('disabled',true);
        return false;
    }

    if($('#penerimaanpembayaranform-carabayar_id').val() != _caraBayarBpjs) {
        if (_value) {
            _penjaminId = _value;
            $('.form-ajuan-detail').show();
        } else {
            _penjaminId = null;
            $('.form-ajuan-detail').hide();
        }
    }
});

$(document).on('click','#simpan-penerimaan', function (event) {
    event.preventDefault();
    var _data = $('#form-penerimaan-pembayaran').serializeArray();
    _data.push({
        name : 'cara_bayar',
        value : _caraBayar
    });
    _data.push({
        name : 'penjamin',
        value : _penjamin
    });
    $().docoForm('click', {
        url : $('#form-penerimaan-pembayaran').attr('action'),
        data : _data,
        // skipSuccessNotif : true,
        success : function (data) {
            var _noPembayaran = data.response.no_pembayaran
            _table.draw();
            $('div').removeClass('has-error');
            $('span.help-block.error').remove();
            $('div.help-block.error').remove();
            // (new PNotify({
            //     title: "Proses Berhasil !",
            //     text: "Data Penerimaan Pembayaran Klaim dengan Nomor Pembayaran <strong>" + _noPembayaran + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
            //     addclass: "alert alert-success alert-arrow-right alert-styled-right",
            //     type: "success",
            //     buttons: {
            //         closer: false,
            //         sticker: false
            //     },
            //     hide: false,
            //     confirm: {
            //         confirm: true,
            //         buttons: [
            //             {
            //                 text: 'Ya',
            //                 addClass: 'btn btn-xs btn-success',
            //             },
            //             {
            //                 text: 'Tidak',
            //                 addClass: 'btn btn-xs btn-danger',
            //             }
            //         ]
            //     },
            //     history: {
            //         history: false
            //     }
            // })).get().on('pnotify.confirm', function() {
            //     window.open("/penjamin-asuransi/informasi-penerimaan-pembayaran/cetak-penerimaan?id="+data.response.id);
            // }).on('pnotify.cancel', function() {
            // });
        }
    });
});

$(document).on('click','.data-reset', function (event) {
    event.preventDefault();
    $.ajax({
        url : '/penjamin-asuransi/informasi-penerimaan-pembayaran/reset-session',
        success : function (data) {
            _table.draw();
            docoResetForm($('#form-penerimaan-pembayaran'));
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
            
        }
    });
})

function initTableNonBpjs({
    data
}) {
    _table = $('#tbl-ajuan').DataTable({
        filter: false,
        displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        scrollX: true,
        info: false,
        destroy: true,
        ajax: baseUrl+'penjamin-asuransi/informasi-penerimaan-pembayaran/get-session',
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: 'No Pengajuan',
                data: 'no_pengajuanklaim',
            },
            {
                title: 'Total Pengajuan',
                data: 'total_pengajuan',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
           
            {
                title: 'Telah Bayar',
                data: 'total_terbayar',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: 'Pembayaran',
                data: 'pembayaran',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: 'Sisa Piutang',
                data: 'total_sisapiutang',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: 'Aksi',
                data: 'aksi',
                visible : _statusAlokasi ? false : true
            }
            
        ],
        drawCallback : function (settings) {
            var api = this.api();
            var dataRows = api.rows( {page:"current"} ).data();
            var table = $('#tbl-ajuan').DataTable();
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
            }
        },
    });
    setTimeout(() => {
        $('#tbl-ajuan').DataTable().columns.adjust();
    }, 100);
}

function initTableBpjs({
    data
}) {
    _table = $('#tbl-ajuan').DataTable({
        scrollX: '100%',
        destroy: true,
        filter: true,
        displayLength: 10,
        ajax: baseUrl+'penjamin-asuransi/informasi-penerimaan-pembayaran/get-session',
        columns: [
            {
                title: "No",
                orderable: false,
                data: 'rowNum',
            },
            {
                title: "No Pengajuan",
                data: 'no_pengajuanklaim',
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
                data: 'tagihan_rs',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: "Tagihan Diajukan",
                data: 'total_pengajuan',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                title: "Tagihan Disetujui",
                data: 'total_terbayar',
                class: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
        ],
        language: {
            emptyTable: "Data Sedang di Proses"
        }
    });
    setTimeout(() => {
        $('#tbl-ajuan').DataTable().columns.adjust();
    }, 100);
}

