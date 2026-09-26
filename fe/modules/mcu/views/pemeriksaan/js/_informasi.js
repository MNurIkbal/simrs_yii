/*
 * @Author: Budi
 * @Date:   2020-01-28 17:06:50
*/

$(".daterange").daterangepicker({
    applyClass: "bg-slate-600",
    cancelClass: "btn-default",
    locale: {
        format: "DD-MMMM-YYYY"
    }
});

$(".date").pickadate({
    applyClass: "bg-slate-600",
    cancelClass: "btn-default",
    locale: {
        format: "DD-MMMM-YYYY",
    },
    onStart: function ()
    {
        var date = new Date();
        this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()] )
    },
});

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
        $('input[name="PendaftaranForm[tgl_masukperiksa]"]').val(  d.getFullYear()+"-"+ month +"-"+d.getDate()+" "+hour+":"+min+":"+sec);
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

function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);

    var t = setTimeout(startTime, 500);
}
