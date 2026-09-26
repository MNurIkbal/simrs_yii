/*
* @Author: Sigit
* @Date:   2018-08-13 16:16:45
*/

$(document).ready(function() {
    var ruangan_id = $('#ruangan_id').val()
    var penjamin_id = $('#penjamin_id').val()
    var kelaspelayanan_id = $('#kelaspelayanan_id').val()
    $("#ruangan_id_dropdown").prop("disabled", true);

    $("#ruangan_id_dropdown").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Pilih Ruangan --',      // custom placeholder (optional) default null
            // _api : `/igd/master-api/list-pemberi-instruksi?ruangan_id=${ruangan_id}`,   // get data
            _api : '/igd/master-api/list-ruangan',   // get data
        }
    )

    $("#pemberi_instruksi_id").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Pilih Instruksi --',      // custom placeholder (optional) default null
            // _api : `/igd/master-api/list-pemberi-instruksi?ruangan_id=${ruangan_id}`,   // get data
            _api : '/igd/master-api/list-all-new-dokter',   // get data
        }
    )
    
    $("#fee_konsul").docoPaginationSelec2(
        // dapat disesuaikan dengan kebutuhan data / customize
        config = {
            placeholder : '-- Pilih Fee Konsul --',      // custom placeholder (optional) default null
            _api : `/igd/master-api/list-fee-konsul?ruangan_id=${ruangan_id}&penjamin_id=${penjamin_id}&kelaspelayanan_id=${kelaspelayanan_id}`,   // get data
        }
    )

    jQuery("#btn-back-verbal-order-igd").removeClass("btn-toolbar");
    jQuery("#btn-save-verbal-order-igd").removeClass("btn-toolbar");
    jQuery("#btn-reset-verbal-order-igd").removeClass("btn-toolbar");
});

$("#btn-back-soap").on("click", function(event) {
    event.preventDefault();
    
    $('.tabbable ul li a[href="#view-asesmen-dpjp"]').click();
});

$('#btn-ulang').on('click', function () {
    location.reload();
})

function kembaliVerbalOrder() {
    $("#panel-form-soap").prop("hidden", false);
    $("#panel-cppt-igd").prop("hidden", false);
    jQuery("div #div-verbal-order").prop("hidden", true);
    jQuery("div #div-soap").prop("hidden", true);
    jQuery("div #div-asesmen-dpjp").prop("hidden", false);
    jQuery("div #div-asesmen-dpjp-soap").prop("hidden", false);
}

function simpanVerbalOrder(index) {
    var data = $("#form-verbal-order").serializeArray();

    $(index).docoForm('click', {
        url: '/igd/pemeriksaan-igd/create-verbal-order',
        data: data,
        success : function(response) {
            var form = $("#form-verbal-order");
            form[0].reset();
            $('#pemberi_instruksi_id').val(null).trigger('change');
            $('#fee_konsul').val(null).trigger('change');
            $("#div-verbal-order").prop("hidden", true);
            window.location.reload();
            
        },
        error: function (res) {
          hideLoader()
          if ( res.responseText != undefined ) {
            let responseJson = JSON.parse(res.responseText)
    
            if ( responseJson.metadata != undefined && (responseJson.metadata.status >= 400 && responseJson.metadata.status <= 499) ) {
              docoNotification('error', 'Proses Tidak Bisa Dilanjutkan!', "Silahkan Cek Inputan!")
              return false
            }
    
            if ( responseJson.meta.code != undefined && (responseJson.meta.code >= 400 && responseJson.meta.code <= 500) ) {
              let errTitle = responseJson.meta.title != undefined ? responseJson.meta.title : 'Proses Tidak Bisa Dilanjutkan!'
              let errMsg = responseJson.meta.message != undefined ? responseJson.meta.message : 'Formulir tidak dapat diakses!'
    
              docoNotification('error', errTitle, errMsg)
              return false
            } 
          }
    
          docoNotification('error', "Terjadi Kesalahan!", "Formulir tidak dapat diakses!")
          return false;
        }
    });
}

function resetVerbalOrder() {
    var form = $("#form-verbal-order");
    form[0].reset();
    $('#pemberi_instruksi_id').val(null).trigger('change');
    $('#fee_konsul').val(null).trigger('change');
}

// clock

function clock() {
    var d = new Date();
    var day = checkTime(d.getDate());
    var month = checkTime(d.getMonth() + 1);
    var hour = checkTime(d.getHours());
    var min = checkTime(d.getMinutes());
    var sec = checkTime(d.getSeconds());
    var ampm = (hour >= 12) ? 'PM' : 'AM';
    var currentTime = hour +":"+ min +":"+ sec;

    // set time
    $('.tgl_cppt').html(day+"-"+ month +"-"+d.getFullYear()+" "+hour+":"+min+":"+sec);
}
function checkTime(i) {
    if (i < 10) {i = "0" + i;}  // add zero in front of numbers < 10
    return i;
}
setInterval(clock, 1000);
// document.getElementById ("waktu-permintaan").innerHTML = getCurrentDate() + ' ' + clock();