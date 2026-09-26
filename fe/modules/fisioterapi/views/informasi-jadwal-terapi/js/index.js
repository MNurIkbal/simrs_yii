/**
 * @Author: Andri Amirul Sonjaya
 * @Date:   2022-05-29
 */

var resultResponse;
$('[data-toggle="popover"]').popover();
$(document).ready(() => {
    setLoading();
    $('#myDateCustom').attr('readonly', true);
    deleteAll().then((value)=>{
        initOption().then((value)=>{
            setValue(value);
            setLoading(false);
        });
    });

    $('.data-filter').on('click', function(){
        setLoading();   
        deleteAll().then((value)=>{
            reloadOption().then((value)=>{
                setValue(value);
                setLoading(false);
            });
        });
    });

    $('#myDateCustom').on('click', function(){
        $('#myDateCustom').attr('readonly', true);
        $('#myDateCustomButton').click();
    });

    $('.data-reset').on('click', function(){
        setLoading();
        $(`#select-pegawai`).prop('selectedIndex', 0);
        $(`#select-pegawai`).change();
        deleteAll().then((value)=>{
            reloadOption(true).then((value)=>{
                setValue(value);
                setLoading(false);
            });
        });
    });
});

function setLoading(loading = true){
    if(loading == true){
        $('#timeline-wrapper').hide();
        $('.data-reset').prop('disabled', true);
        $('.data-filter').prop('disabled', true);
        $('#loading').html(`<h3 style="text-align:center;">
        <i class="icon-spinner4 spinner position-center"></i>
        &nbsp;&nbsp;<b> Memuat... </b>
        </h3>`);
    }else{
        $('.data-filter').prop('disabled', false);
        $('.data-reset').prop('disabled', false);
        $('#timeline-wrapper').show();
        $('#loading').html(''); 
    }
}

async function deleteAll(){
    var deleteTimeline = null;
    deleteTimeline = await $('.jqtl-event-node').each(function(i, obj) {
       var eventId =  $(this).attr('id');
       var onlyId = eventId.split('-');
       $("#myTimeline").Timeline('removeEvent', [onlyId[1]]);
    });
    return deleteTimeline;
}

async function reloadOption(reset = false){
    // $('.jqtl-event-node').remove();
    var tanggalTerapi = $('#myDateCustom').val();
    var tempDate = new Date();
    if(!tanggalTerapi || reset == true){
        tanggalTerapi = `${tempDate.getDate().toString().padStart(2, "0")}-${(tempDate.getMonth() + 1).toString().padStart(2, "0")}-${tempDate.getFullYear()}`;
        $('#myDateCustom').val(tanggalTerapi);
        var initialValue = await getData(false);
    }if(reset != true){
        var initialValue = await getData(true);
    }
    var newVal = JSON.parse(JSON.stringify(initialValue));
    var sideBar = [];
    var size = Object.keys(newVal.data).length;
    var keys = Object.keys(newVal.data);
    if(size > 0){
        for(let sizeTemp = 0 ; sizeTemp < keys.length ; sizeTemp++){
            keys[sizeTemp] = `<p name="row-0${sizeTemp+1}" class="sidebar-timeline">${keys[sizeTemp]}<p>`
        }
        sideBar = keys;
    }
    var dateParts = tanggalTerapi.split("-");
    var date = new Date(dateParts[2], dateParts[1] - 1, +dateParts[0]).setHours(0,0,0,0);
    var dateEnd = new Date(dateParts[2], dateParts[1] - 1, +dateParts[0]).setHours(23,0,0,0);
    var new_options = {
        type: "bar",
        scale: "hour",
        startDatetime : date,
        endDatetime :  dateEnd,
        sidebar: {
            sticky:  true,
            overlay: true,
            list: sideBar,
        },
      };
    $('#myTimeline').Timeline('reload', new_options);
    $('.jqtl-side-index-margin').html('<div style="padding-left:8px;font-size:12px;line-height: 30px;">Dokter </div>');
    $('.jqtl-side-index-margin').css({'padding' : '10px !important'});

    return newVal.data;
}

async function getData(withValue = false){
    var tanggalTerapi = $('#myDateCustom').val();
    var terapis = $('#terapis').val();
    var initialValue = null;
    var data = withValue && withValue == true ? `?tgl_penjadwalan_awal=${tanggalTerapi}&pegawai_id=${terapis}` : '';
    var url = `/fisioterapi/informasi-jadwal-terapi/get-data${data}`;
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

async function initOption(){
    var initialValue = await getData();
    var sideBar = [];
    var newVal = JSON.parse(JSON.stringify(initialValue));
    var size = Object.keys(newVal.data).length;
    var keys = Object.keys(newVal.data);
    if(size > 0){
        for(let sizeTemp = 0 ; sizeTemp < keys.length ; sizeTemp++){
            keys[sizeTemp] = `<p name="row-${sizeTemp+1}" class="sidebar-timeline">${keys[sizeTemp]}<p>`
        }
        sideBar = keys;
    }
    await $("#myTimeline").Timeline({
        type: "bar",
        scale: "hour",
        startDatetime : new Date(new Date().setHours(0,0,0,0)),
        endDatetime : new Date(new Date().setHours(23,0,0,0)),
        minGridSize   : 24 * 3,
        range: 12,
        rowHeight: 40,
        sidebar: {
            sticky:  true,
            overlay: true,
            list: sideBar,
        },
        ruler: {
            truncateLowers: false,
            top: {
                lines:      [  'hour' ],
                height:     26,
                fontSize:   12,
                color:      "white",
                format:     {
                     hour: "fulltime"
                }
            },
        },
        
      });

    $('.jqtl-side-index-margin').html('<div style="padding-left:8px;font-size:12px;line-height: 30px;">Dokter </div>');
    $('.jqtl-side-index-margin').css({'padding' : '10px !important'});

    return newVal.data;
}

function setValue(initialValue = []){
    var size = Object.keys(initialValue).length;
    var keys = Object.keys(initialValue);
    if(initialValue && size > 0 && keys.length > 0){
        var schedule = [];
        for(let i = 0; i < keys.length ; i++){
            for(let y = 0; y < initialValue[keys[i]].length; y++){
                var endDate = new Date(initialValue[keys[i]][y]['tgl_penjadwalan_akhir']);
                var startDate = new Date(initialValue[keys[i]][y]['tgl_penjadwalan_awal']);
                var endLabel = endDate.getHours().toString().padStart(2, "0") + ':' + endDate.getMinutes().toString().padStart(2, "0");
                var startLabel = startDate.getHours().toString().padStart(2, "0") + ':' + startDate.getMinutes().toString().padStart(2, "0");
                var html = `<div class="popover-jadwal-terapi">
                    <div class="col-md-12" style="margin-top:3px;margin-bottom:3px;padding:0">
                        <div class="col-md-3" style="color:#cfcfcf;padding:0">Pasien</div>
                        <div class="col-md-1">:</div>
                        <div class="col-md-8" style="padding:0">${initialValue[keys[i]][y]['nama_pasien']} (${initialValue[keys[i]][y]['jenis_kelamin']}) - ${initialValue[keys[i]][y]['no_rekam_medik']}</div>
                    </div>
                    <div class="col-md-12" style="margin-top:3px;margin-bottom:3px;padding:0">
                        <div class="col-md-3" style="color:#cfcfcf;padding:0">Jam</div>
                        <div class="col-md-1">:</div>
                        <div class="col-md-8" style="padding:0">${startLabel} - ${endLabel}</div>
                    </div>
                    <div class="col-md-12" style="margin-top:3px;margin-bottom:20px;padding:0">
                        <div class="col-md-3" style="color:#cfcfcf;padding:0">Terapi</div>
                        <div class="col-md-1">:</div>
                        <div class="col-md-8" style="padding:0">${initialValue[keys[i]][y]['terapi_nama']}</div>
                    </div>
                </div>`;  
                schedule.push({
                    id: initialValue[keys[i]][y]['jadwalterapifisio_id'],
                    start: initialValue[keys[i]][y]['tgl_penjadwalan_awal'],
                    end: initialValue[keys[i]][y]['tgl_penjadwalan_akhir'], 
                    row: i+1,
                    label: endLabel,
                    bgColor: '#34bfa3',
                    color: 'white',
                    extend: {
                        toggle:'popover',
                        title: 'pasien data',   
                        trigger:'hover', 
                        content: html, 
                        placement: 'bottom', 
                        html: 'true', 
                        container: 'body'
                    }
                })
            }
        }
        $('#myTimeline').Timeline('addEvent', schedule);
    }
}