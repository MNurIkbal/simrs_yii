$('#assign-dokter-form').docoForm('submit',{
    skipConfirm: true,
    success : function(data) {
        var form = $('#assign-dokter-form');
        form[0].reset();
        // if(!crossModule) {
        //     table.draw();
        // }
        $('#modal_backdrop').modal('toggle');
    },
    error : function(data){
        var res = data.responseJSON.response.title;

        if (typeof res !== 'undefined' && res == 'Gagal Assign Dokter') {
            table.draw()
            $('#modal_backdrop').modal('toggle');
          }
        $(this).find('.error').hide();
        $(document).ready(function () {
            $('div.help-block').remove();
        });
    }
});

// date & time
function getCurrentDate(){
    var d = new Date();
    var date = d.getDate();
    var month = d.getMonth();
    var montharr = ["Jan","Feb","Mar","April","May","June","July","Aug","Sep","Oct","Nov","Dec"];

    month = montharr[month];

    var year = d.getFullYear();
    var day = d.getDay();

    var dayarr =["Sun","Mon","Tues","Wed","Thurs","Fri","Sat"];

    day = dayarr[day];
    return date +" "+ month +" "+ year;
}
function clock() {
    var d = new Date();
    var month = d.getMonth() +1;
    var hour = checkTime(d.getHours());
    var min = checkTime(d.getMinutes());
    var sec = checkTime(d.getSeconds());
    var ampm = (hour >= 12) ? 'PM' : 'AM';
    var currentTime = hour +":"+ min +":"+ sec;

    if(document.getElementById ("tgl_masukperiksa") != null){
        document.getElementById ("tgl_masukperiksa").innerHTML = getCurrentDate() + '  ' + currentTime;
        $('input[name="AturDokterForm[tgl_masukperiksa]"]').val(  d.getFullYear()+"-"+ month +"-"+d.getDate()+" "+hour+":"+min+":"+sec);
    }
    // set time
    if(document.getElementById ("tglpasienpulang") != null){
        document.getElementById ("tglpasienpulang").innerHTML = getCurrentDate() + '  ' + currentTime;
        $('input[name="PasienPulangForm[tglpasienpulang]"]').val(  d.getFullYear()+"-"+ month +"-"+d.getDate()+" "+hour+":"+min+":"+sec);
    }
}
function checkTime(i) {
    if (i < 10) {i = "0" + i;}  // add zero in front of numbers < 10
    return i;
}

setInterval(clock, 1000);
// document.getElementById ("waktu-permintaan").innerHTML = getCurrentDate() + ' ' + clock();

function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);

    // $('#tgl_masukperiksa').innerHTML =  h + ":" + m + ":" + s;
    var t = setTimeout(startTime, 500);
}
