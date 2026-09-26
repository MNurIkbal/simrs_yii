
var column_jadwal  = null;
(function( $ ){
  $.fn.dataJadwal = function(params) {
    var jadwalpoli = new Jadwalpoli();
    var return_first = function () {
        var tmp = null;
        $.ajax({
            'async': false,
            'type': "get",
            'url': params.url,
            'data': params.data,
            'success': function (data) {
              var objData = JSON.parse(data);
              jadwalpoli.addDays(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);
              jadwalpoli.setTotalRow(objData.recordsTotal);
              var listJadwal = objData.rowData;
              var jmlPoli = objData.countPoli;
              var jmlJadwal = objData.recordsTotal;
              $('#jmlJadwal').text(jmlJadwal);
              $('#jmlPoli').text(jmlPoli);
              for (var key in listJadwal) {
                 if (listJadwal.hasOwnProperty(key)) {
                     jadwalpoli.addJadwal(listJadwal[key].ruangan.ruangan_nama,listJadwal[key].hari,listJadwal[key].jam_mulai,listJadwal[key].jam_tutup,listJadwal[key].kuota);
                 }
              }
              tmp = jadwalpoli;
            }
        });
        return tmp;
    }();
    return return_first;
  }
  $.fn.jadwalPoliFilter = function() {
      // var paramObj = {};
      // $.each(this.serializeArray(), function(_, kv) {
      //   paramObj[kv.name] = kv.value;
      // });
      // console.log(paramObj);
      column_jadwal = this.serializeArray();
      return this;
   };
   $.fn.resetJadwalPoliFilter = function() {
       $(this).find("input[type=text], textarea").val("");
      return this;
   };
   $.fn.drawJadwal = function(bodyJadwal) {
        var renderer = new Jadwalpoli.Renderer(bodyJadwal);
        renderer.drawList(this.get(0));
    return this;
   }  
}( jQuery ));

$(document).on('click','.data-filter',function(){

  $("#filter-form-jadwal").jadwalPoliFilter();
  var bodyJadwal = $("#list_jadwal").dataJadwal({
                  url: baseUrl+"pendaftaran/informasi-jadwal-poliklinik/get-data-jadwal-poliklinik",
                  data: column_jadwal
                });
  $("#list_jadwal").drawJadwal(bodyJadwal);
});
$(document).on('click','.data-reset',function(){
  
  $("#filter-form-jadwal").resetJadwalPoliFilter();
  var bodyJadwal = $("#list_jadwal").dataJadwal({
                  url: baseUrl+"pendaftaran/informasi-jadwal-poliklinik/get-data-jadwal-poliklinik",
                  // data: column_jadwal
                });
  $("#list_jadwal").drawJadwal(bodyJadwal);
});
$(document).on('click','.btn-pdf',function(){

  $("#filter-form-jadwal").jadwalPoliFilter();
  var arr = new Array();
  arr[0] = new Array();
  $('#table-informasi-jadwal-poliklinik > thead > tr > th').each(function (index) {
    arr[0][index] = $(this).text();
  });
  $('#table-informasi-jadwal-poliklinik > tbody > tr').each(function (index) {
    arr[index+1] = new Array();
    $(this).children('td').each(function(index_td){
      arr[index+1][index_td] = $(this).text();
    })
  });
  var txt_col = JSON.stringify(arr);
  // var obj_filter = {};
  // $(column_jadwal).each(function(i,field){
  //   if(field.name != '_csrf'){
  //     obj_filter[field.name] = field.value;
  //   }
  //   console.log(field.name);
  // });
  var txt_filter = {};
  txt_filter['ruangan'] = null;
  txt_filter['jam_mulai'] = null;
  txt_filter['jam_tutup'] = null;
  if($('#jadwalpoliklinikform-ruangan_id').val() != ''){
    txt_filter['ruangan'] = $('#select2-jadwalpoliklinikform-ruangan_id-container').text();
  }
  if($('#jamMulai').val() != ''){
    txt_filter['jam_mulai'] = $('#jamMulai').val();
  }
  if($('#jamTutup').val() != ''){
    txt_filter['jam_tutup'] = $('#jamTutup').val();
  }
  var column_filter = JSON.stringify(txt_filter);
  $.ajax({
    type:'POST',
    url:baseUrl+"pendaftaran/informasi-jadwal-poliklinik/export-pdf",
    data:{col_data:txt_col,col_filter:column_filter},
    success:function(data){
      console.log('ok');
    }
  });
});
var column_jadwal = $("#filter-form-jadwal").serializeArray();
var bodyJadwal = $("#list_jadwal").dataJadwal({
                  url: baseUrl+"pendaftaran/informasi-jadwal-poliklinik/get-data-jadwal-poliklinik",
                  data: column_jadwal
                });
$("#filter-form-jadwal").jadwalPoliFilter();
$("#list_jadwal").drawJadwal(bodyJadwal);


