$(function () {
    $("#dokter_id").select2({
      placeholder: "-- Pilih --",
      minimumInputLength: 3,
      ajax: {
        url: "/rm/info-kunjungan-pasien/get-dokter",
        dataType: "json",
        quietMillis: 250,
        data: function (params) {
          var query = {
            search: params,
          };
          return params;
        },
        processResults: function (data) {
          return {
            results: data.result,
          };
        },
        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) {
          return m;
        },
      },
    });

    $("#diagnosadokter_utama").select2({
        placeholder: "-- Pilih --",
        minimumInputLength: 3,
        tags:true, 
        ajax : {
            url: "/api/rm/diagnosa/list",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    type: "diagnosa_utama",
                    formatResponse: 0
                };
            },
            templateResult: function(data) {
                return data.text
            },
            templateSelection: function(data) {
                return data.text
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        },
    });
    
});

$(document).on('click', '#search', function() {
    applyFillter()
})

function applyFillter() {
    let filter = {
        'periode' : 4,
        'dokter': 5,
        'diagnosa': 6,
    }
    // searchFilter(tabRekap, filter)
    searchFilter(tabRekap)
}

$(document).on('change keyup click', '.date', function(){
    let start = $('#rangeDemoStart').val()
    let end = $('#rangeDemoFinish').val()
    periode = start +' - '+ end
})

$(document).on('change keyup click', '#dokter_id', function(){
    dokter = this.value
})

$(document).on('change keyup click', '#diagnosadokter_utama', function(){
    diagnosa = this.value
})

$(document).on('change', '#filter_nama_pasien', function(e){
    e.stopImmediatePropagation();
    e.preventDefault()
    nama = this.value
})

$(document).on('change', '#filter_no_rekam_medik', function(e){
    e.stopImmediatePropagation();
    e.preventDefault()
    norm = this.value
})

$(document).on('change keyup click', '#filter_no_rekam_medik', function(){
    norm = this.value
})

$(document).on('change keyup click', '#filter_instalasi', function(e){
    e.stopImmediatePropagation();
    e.preventDefault()
    instalasi = this.value
})

$(document).on('change keyup click', '#filter_ruangan', function(){
    ruangan = this.value
})

$('.nav-tab-type').bind('click', ({ currentTarget }) => {
    $('.nav-item').removeClass('active')
    $(currentTarget).addClass('active')

    const data = $(currentTarget).data()
    posNav = 'type='+data.type+'&'
})

$('#data-export-excel-serconn').click(function (e) { 
    e.preventDefault();
    let baseUrl = $(this).data('url');
    // $(this).attr('data-url', null)
    $(this).attr('data-url', baseUrl + posNav)
});

function resetTable() {
    tabRekap.columns().search('').draw()
    tabDetail.columns().search('').draw()
}

$(document).on('click', '#reset', function() {
    periode = ''
    dokter = ''
    diagnosa = ''
    nama = ''
    norm = ''
    instalasi = ''
    ruangan = ''
    resetTable()
})

function searchFilter(targetTab){
    targetTab.column(4).search(periode)
    .column(15).search(dokter)
    .column(6).search(diagnosa)
    .column(7).search(nama)
    .column(8).search(norm)
    .column(9).search(instalasi)
    .column(10).search(ruangan)
    .column(11).search('rekap')
    .draw()
}

// function searchFilter(targetTab, filter){
//     let search;
//     for (const key in filter) {
        
//         search += '.'+column(`${key}`).search(`${filter[key]}`)
//         // console.log(`${key}: ${filter[key]}`);
//     }
//     console.log(targetTab.search)
//     console.log(search)
//     targetTab.search.draw()
// }
