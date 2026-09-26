var table;

$(document).ready(function() {

    // table.draw();

    jQuery("#btn-save-permintaan-konsul").removeClass("btn-toolbar");
    jQuery("#btn-reset-permintaan-konsul").removeClass("btn-toolbar");
    jQuery("#btn-back-permintaan-konsul").removeClass("btn-toolbar");
    jQuery("#btn-ubah-permintaan-konsul").removeClass("btn-toolbar");
    jQuery("#btn-kembali-permintaan-konsul").removeClass("btn-toolbar");

    // Show hide form
    $("#btn-back-verbal-order").on("click", function(event) {
        // Prevent default
        event.preventDefault();

        // Show hide form
        jQuery("div #div-verbal-order").prop("hidden", true);
        jQuery("div #div-soap").prop("hidden", true);
        jQuery("div #div-cppt").prop("hidden", false);
    });

    $("#btn-reset-permintaan-konsul").on("click", function(event) {
        event.preventDefault();
        var form = $("#permintaan-konsul-form");

        form[0].reset();
        $('#dokter_id-permintaan-konsul').val('').trigger('change');
        $("#jenis_konsul-permintaan-konsul").select2().select2("val", null);
        $('#permintaankonsulform-ket_konsul').val('');
        $('input[name="PermintaanKonsulForm[permintaankonsul_id]"]').val('');

    });

    $("#btn-kembali-permintaan-konsul").on("click", function(event) {
        event.preventDefault();

        var form = $("#permintaan-konsul-form");

        form[0].reset();
        $('#dokter_id-permintaan-konsul').val('').trigger('change');
        $("#jenis_konsul-permintaan-konsul").select2().select2("val", null);
        $('#permintaankonsulform-ket_konsul').val('');
        $('input[name="PermintaanKonsulForm[permintaankonsul_id]"]').val('');

        $('#btn-save-permintaan-konsul').show();
        $('#btn-reset-permintaan-konsul').show();
        $('#btn-ubah-permintaan-konsul').hide();
        $('#btn-kembali-permintaan-konsul').hide();
        // hide form static
        $('.field-permintaankonsulform-dokter_nama').addClass('hidden');
        $('.field-permintaankonsulform-jenis_konsul_nama').addClass('hidden');
        $('.field-dokter_id-permintaan-konsul').removeClass('hidden');
        $('.field-jenis_konsul-permintaan-konsul').removeClass('hidden');
    });


});

function ubahData(permintaankonsul_id){
   $.ajax({
        url: '/ranap/pemeriksaan-rawat-inap/get-permintaan-konsul?permintaankonsul_id=' + permintaankonsul_id,
        type: 'GET',
        success: function(data) {
            $('#dokter_id-permintaan-konsul').hide();
            $('#jenis_konsul-permintaan-konsul').hide();
            $('#dokter_nama').text(data.dok_konsul);
            $('#jenis_konsul_nama').text(data.jenis_konsul_nama);
            $('#permintaankonsulform-ket_konsul').val(data.ket_konsul);
            $('input[name="PermintaanKonsulForm[permintaankonsul_id]"]').val(data.permintaankonsul_id);

            $('#btn-save-permintaan-konsul').hide();
            $('#btn-reset-permintaan-konsul').hide();
            $('#btn-ubah-permintaan-konsul').show();
            $('#btn-kembali-permintaan-konsul').show();
            // show static input
            $('.field-permintaankonsulform-dokter_nama').removeClass('hidden');
            $('.field-permintaankonsulform-jenis_konsul_nama').removeClass('hidden');
            $('.field-dokter_id-permintaan-konsul').addClass('hidden');
            $('.field-jenis_konsul-permintaan-konsul').addClass('hidden');
        }
    });

}

$('#btn-ubah-permintaan-konsul').hide();
$('#btn-kembali-permintaan-konsul').hide();


$('#dokter_id-permintaan-konsul').select2();

// date & time
function getCurrentDate(){
    var d = new Date();
    var date = d.getDate();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();

    return (("" + date).length < 2 ? "0" : "") + date + "/" + (("" + month).length < 2 ? "0" : "") + month + "/" + year;
}

function clock() {
    var d = new Date();
    var hour = checkTime(d.getHours());
    var min = checkTime(d.getMinutes());
    var sec = checkTime(d.getSeconds());
    var ampm = (hour >= 12) ? 'PM' : 'AM';
    var currentTime = hour +":"+ min +":"+ sec;

    // set time
    document.getElementById("waktu-permintaan").innerHTML = getCurrentDate() + '  ' + currentTime;
    $('input[name="PermintaanKonsulForm[waktu_permintaan]"]').val(  d.getFullYear()+"-"+d.getMonth()+"-"+d.getDate()+" "+hour+":"+min+":"+sec);
}
function checkTime(i) {
    if (i < 10) {i = "0" + i;}  // add zero in front of numbers < 10
    return i;
}

setInterval(clock, 1000);
// document.getElementById ("waktu-permintaan").innerHTML = getCurrentDate() + ' ' + clock();

function startTime() {
    alert('asdas');
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);

    $('#waktu-permintaan').innerHTML =  h + ":" + m + ":" + s;
    var t = setTimeout(startTime, 500);
}

table = $("#tb_permintaan_konsultasi").docoTabel({
    filter: false,
    pageLength: 20,
    // lengthMenu: [5, 10, 25, 100],
    bLengthChange: false,
    serverSide: true,
    stateSave: true,
    processing: true,
    scrollX: true,
    sorting:[[1,'asc']],
    ajax: baseUrl + "ranap/pemeriksaan-rawat-inap/get-list-permintaan-konsul?id=" + pendaftaran_id,
    columns: [
        {
            data: "rowNum",
            searchable: false,
            orderable: false
        },
        {
            data: "waktu_permintaan",
            searchable: false,
            orderable: false
        },
        {
            data: "dokterdpjpasal_nama",
            searchable: false,
            orderable: false
        },
        {
            data: "dok_konsul",
            searchable: false,
            orderable: false
        },
        {
            data: "jenis_konsul_nama",
            searchable: false,
            orderable: false,
            // class: "text-center"
        },
        {
            data: "ket_konsul",
            searchable: false,
            orderable: false,
            // class: "text-center"
        },
        {
            data: "aksi",
            searchable: false,
            orderable: false,
            class: "text-center"
        },
    ],
   /* fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        // console.log(aData.rekonsiliasiobat_id);
        var rekonsiliasiobat_id = aData.rekonsiliasiobat_id;

        if (!rekonsiliasiobat_id) {
            // Set color
            $("td", nRow).css("background-color", "#fdfd96");
        }
    }*/
});

$('#btn-save-permintaan-konsul').on('click', function(e){
    e.preventDefault();
    var data = $("#permintaan-konsul-form").serializeArray();
    $(this).docoForm("click", {
        url: '/ranap/pemeriksaan-rawat-inap/save-permintaan-konsul',
        data: data,
        success: function (res) {
            $('.tabbable ul li a[href="#view-permintaan-konsul"]').click();
        }
    });
});

$('#btn-ubah-permintaan-konsul').on('click', function(e){
    e.preventDefault();
    var data = $("#permintaan-konsul-form").serializeArray();
    $(this).docoForm("click", {
        url: '/ranap/pemeriksaan-rawat-inap/update-permintaan-konsul',
        data: data,
        // skipConfirm: true,
        success: function (res) {
            $('.tabbable ul li a[href="#view-permintaan-konsul"]').click();
        }
    });
})

$("#cetak-pdf").on("click",function (event) {
    event.preventDefault();
    var url = window.location.origin;
    var target = $(this).attr('data-target');
    window.open(url+target);
});

// Event Batal
// $(document).on("click", ".data-batal", function (e) {
function batal(attr) {
    // console.log(attr); return;
    var action = $(attr).attr("action");
    // alert(action);
    $(attr).docoForm("delete",{
        url: action,
        confirmTitle: i18next.t("Konfirmasi"),
        confirmMessage: i18next.t("Apa anda yakin ingin membatalkan data ini?"),
        success : function (data) {
            // table.draw();
            $('.tabbable ul li a[href="#view-permintaan-konsul"]').click();
        }
    });
    return false;
};
