
/*
* @Author: rizfardi@docotel.com
* @Date:   2018-04-17 16:23:04
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2018-12-28 18:06:29
*/

var arrData = [];

// Options
var oneDay = 24*60*60*1000;
var rangeDemoFormat = "%e-%b-%Y";
var rangeDemoConv = new AnyTime.Converter({format:rangeDemoFormat});

$(document).on('input', '.doco-decimal', function() {
    match = (/(\d{0,9})[^.]*((?:\.\d{0,9})?)/g).exec(this.value.replace(/[^\d.]/g, ''));
    this.value = match[1] + match[2];
});

$("#rangeDemoToday").click( function (e)  {
    $("#rangeDemoStart").val(rangeDemoConv.format(new Date())).change();
});

// Clear dates
$("#rangeDemoClear").click( function (e) {
    $("#rangeDemoStart").val("").change();
});
// Start date

$("#rangeDemoStart").AnyTime_picker({
    format: rangeDemoFormat
});

// On value change
$("#rangeDemoStart").change(function(e) {
    try {
        var fromDay = rangeDemoConv.parse($("#rangeDemoStart").val()).getTime();

        var dayLater = new Date(fromDay+oneDay);
            dayLater.setHours(0,0,0,0);

        var ninetyDaysLater = new Date(fromDay+(90*oneDay));
            ninetyDaysLater.setHours(23,59,59,999);

        // End date
        $("#rangeDemoFinish")
        .AnyTime_noPicker()
        .removeAttr("disabled")
        .val(rangeDemoConv.format(dayLater))
        .AnyTime_picker({
            earliest: dayLater,
            format: rangeDemoFormat,
            latest: ninetyDaysLater
        });
    }

    catch(e) {

        // Disable End date field
        $("#rangeDemoFinish").val("").attr("disabled","disabled");
    }
});

/*----------  Informasi formulir detail  ----------*/
$(document).on('keyup', '.stok_fisik_edit', function(e){
    let _val = $(this).val() != '' ? parseFloat($(this).val()).toFixed(0) : '';
    // let total_sistem_now = $("#stokopnameform-totalstok_sistem").val();
    let key = $(this).attr('data-key');
    // let totalstok_fisik = 0;
    // // let totalstok_sistem = parseInt(docoHelper.convertToAngka($('#totalstok_sistem').val()));
    arrData[key].stok_fisik = _val;

    // $(".stok_fisik_edit").each(function() {
        // totalstok_fisik = totalstok_fisik + parseFloat($(this).val());
    //     $(this).val(parseFloat($(this).val()).toFixed(0));
    // });

    // // let totalstok_selisih = totalstok_fisik - parseFloat(docoHelper.convertToAngka(totalstok_sistem));
    let stokselisih = $(this).val() - $(this).attr('data-stoksistem');

    if($(this).val() != '') {
        $(this).closest('tr').find('p.selisih-stok').text(stokselisih);
        $(this).closest("tr").removeClass("row-empty");
    } else {
        $(this).closest('tr').find('p.selisih-stok').text('');
    }
    
    // $('#totalstok_fisik').val(docoHelper.convertToRupiah(totalstok_fisik));
    // // $('#totalstok_selisih').val(docoHelper.convertToRupiah(totalstok_selisih));
});

$(document).on('keyup', '.stok_revisi_edit', function(e){
    let _val = $(this).val() != '' ? parseFloat($(this).val()).toFixed(0) : '';
    let key = $(this).attr('data-key');
    arrData[key].stok_revisi = _val;
    
    let stokSelisihRevisi = $(this).val() - $(this).attr('data-stoksistem');

    if($(this).val() != '') {
        $(this).closest('tr').find('p.selisih-revisi').text(stokSelisihRevisi);
        $(this).closest("tr").removeClass("row-empty");
    } else {
        $(this).closest('tr').find('p.selisih-revisi').text('');
    }
});

$('#stokopnamedetail-form').on('submit', function(e){
    e.preventDefault();
    let formData = {
        'jenisstokopname': $('#jenisstokopname').val()
    };

    let isErr = false;
    let listData = [];
    let i = 0, temp = 0;
    $.each(arrData, function(k,v){
        $.each(v, function(key, val){
            if(v.formstokopname_id != temp && stokopnamedetail_id !== 'undefined') {
                var stokopnamedetail_id = null;

                if(typeof v.stokopnamedetail_id !== 'undefined') {
                    stokopnamedetail_id = v.stokopnamedetail_id;
                }

                listData[i] = {
                    'stokopnamedetail_id' : stokopnamedetail_id,
                    'formstokopname_id': v.formstokopname_id,
                    'stok_fisik': v.stok_fisik,
                    'stok_revisi': v.stok_revisi,
                    'stok_sistem': v.stok_sistem
                };

                if(is_fulfilled == "true" && (v.stok_fisik == '' || (stokopnamedetail_id != null && v.stok_revisi == ''))) {
                    var stokLabel = '';
                    isErr = true;

                    $("tr").find("[data-key='" + v.formstokopname_id + "']").closest("tr").addClass("row-empty");
                    stokLabel = stokopnamedetail_id == null ? 'Stok fisik' : 'Stok revisi';
                    docoNotification('warning', 'Silahkan Cek Inputan', stokLabel + ' tidak boleh kosong.');

                    return false;
                }

                temp = k;
                i++;
            }
        });
    });

    if(isErr) {
        return false;
    }

    $(this).docoForm('submit', {
        type:'POST',
        data: {
            form: formData,
            list: JSON.stringify(listData)
        },
        dataType: 'json',
        isDataString: true,
        success: function(data){
            var id = data.response.id;
            var nomor = data.response.nomor;
            (new PNotify({
                    title: "Berhasil",
                    text: "Transaksi Stok Opname dengan Nomor " + "<strong>" + nomor + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
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
                    // Print
                    window.open('/apotek/informasi-stok-opname/export-pdf?id='+id);
                    setTimeout(() => {
                        window.open('/apotek/informasi-stok-opname/inf-stok-formulir-opname', '_self');
                    }, 100);
                }).on('pnotify.cancel', function() {
                    window.open('/apotek/informasi-stok-opname/inf-stok-formulir-opname', '_self');
                });
        }, error: function(res) {
            var data = res.responseJSON
            docoNotification('error', "Proses Gagal", data.message);
        }
    });
})
