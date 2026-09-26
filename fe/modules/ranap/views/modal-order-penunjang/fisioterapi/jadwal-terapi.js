/**
 * @Author: Andri Amirul Sonjaya
 * @Date:   2022-05-29
 */

var calendar = null;

$(function (){

    $("#date-lihat-jadwal").pickadate({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        format: "yyyy-mm-dd",
        onStart: function () {
            var date = new Date();
            this.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
        }
    }); 

    $(`.picker__holder`).removeClass(`picker_modal-one`);
    $(`.picker__holder`).addClass(`picker_modal-two`);

    $('.close-modal-pemeriksaan').on('click', function () {
        $(`.picker__holder`).removeClass(`picker_modal-two`);
        $(`.picker__holder`).addClass(`picker_modal-one`);
    });
    
    calendar = new tui.Calendar('#calendar', {
        defaultView: 'day',
        taskView: false,
        scheduleView: ['time'],
        isReadOnly: true,
    });

    changeTitleDate();
    setLoading();
    getData(true).then((value)=>{
        var newVal = JSON.parse(JSON.stringify(value));
        setValue(newVal.data);
        setLoading(false);
    });

    $('#date-lihat-jadwal').on('change', function(){
        var selectedDate = $(this).val();
        if(selectedDate){
            calendar.setDate(selectedDate);
            changeTitleDate();
        }
    });

    $('#btnNext').on('click', function(){
        calendar.next();
        changeTitleDate();
    });

    $('#btnPrev').on('click', function(){
        calendar.prev();
        changeTitleDate();
    });
});

function setLoading(loading = true){
    if(loading == true){
        $('#calendar').hide();
        $('.lihat-jadwal-date').prop('disabled', true);
        $('#btnToday').prop('disabled', true);
        $('#btnNext').prop('disabled', true);
        $('#btnPrev').prop('disabled', true);
        $('#loading').html(`<h3 style="text-align:center;">
        <i class="icon-spinner4 spinner position-center"></i>
        &nbsp;&nbsp;<b> Memuat... </b>
        </h3>`);
    }else{
        $('.lihat-jadwal-date').prop('disabled', false);
        $('#btnToday').prop('disabled', false);
        $('#btnNext').prop('disabled', false);
        $('#btnPrev').prop('disabled', false);
        $('#calendar').show();
        $('#loading').html(''); 
    }
}

function changeTitleDate(){
    var tempDate = calendar.getDate();
    var tanggalTerapi = `${tempDate.getFullYear()}.${(tempDate.getMonth() + 1).toString().padStart(2, "0")}.${tempDate.getDate().toString().padStart(2, "0")}`;
    $('#renderRange').html(`${tanggalTerapi}`);
    $("#date-lihat-jadwal").val(tanggalTerapi);
}

async function getData(withValue = false){
    var initialValue = null;
    var tanggalTerapi = 'All';
    var terapis = pegawaiId;
    var data = withValue && withValue == true ? `?tgl_penjadwalan_awal=${tanggalTerapi}&pegawai_id=${terapis}` : '';
    var url = `/ranap/pemeriksaan-rawat-inap/get-data-terapi-fisio${data}`;
    await $.ajax({
        type: 'GET',
        url: url,
        contentType: 'application/json',
        success: function (res) {
            initialValue = res;
        },
    });
    return initialValue;
}

function setValue(initialValue = []){
    if(initialValue && initialValue.length > 0){
        for(let i = 0; i < initialValue.length ; i++){
            var endDate = new Date(initialValue[i]['tgl_penjadwalan_akhir']);
            var startDate = new Date(initialValue[i]['tgl_penjadwalan_awal']);
            var title = `${initialValue[i]['nama_pasien']} (${initialValue[i]['jenis_kelamin']}) - ${initialValue[i]['no_rekam_medik']} - ${startDate.getHours()}:${startDate.getMinutes()} sd ` + endDate.getHours() + ':' + endDate.getMinutes();
            calendar.createSchedules([
                {
                    id: i + 1,
                    calendarId: i + 1,
                    start: initialValue[i]['tgl_penjadwalan_awal'],
                    end: initialValue[i]['tgl_penjadwalan_akhir'], 
                    row: i + 1,
                    title: title,
                    category: 'time',
                    bgColor: '#34bfa3',
                    color: 'white',
                }
            ]);
        }
    }
}