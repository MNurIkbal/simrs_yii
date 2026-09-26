$(".data-batal-bayar").click(function(e){
   e.preventDefault()
   var tableData = table.row(".selected").data();
   PNotify.removeAll();
   if (typeof tableData !== "undefined") {
      var primary = tableData.primary;
      var url = $(this).attr("data-url");
      var conditions = $(this).attr("data-conditions") ? $(this).attr("data-conditions").split(",") : "";
      var ext = "";
      if(conditions.length > 0){
         $.each(conditions, function(index, value){
            ext += "&"+value+"="+tableData[value];
         });
      }
      $(this).data("url", url+primary+ext);
   } else {
      new PNotify({
      title: "Terjadi Kesalahan",
      text: "Belum ada data yang dipilih!",
      addclass: "alert alert-warning alert-arrow-right alert-styled-right",
      type: "warning"
   });
   }
});

$(document).ready(function(){
   localStorage.clear();
   table = $("#table-informasi").docoTabel({
      filter: true,
      columnDefs: [{
         orderable: false,
         className: "select-checkbox",
         targets:   0
      }],
      select: {
         style:    "os",
         selector: "tr"
      },
      sorting: [[2, "desc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: true,
      ajax: baseUrl+"kasir/inf-pasien-sudah-bayar/get-data",
      columns: [
         {
            data : null,
            render : function ( data, type, full, meta ) {
               return null;
            },
            searchable: false,
            orderable: false
         },
         {
            title: "No",
            data: "rowNum",
            searchable: false,
            orderable: false
         },
         {
            title: "Tanggal Pembayaran",
            data: "tgl_pembayaran",
            name:"tgl_pembayaran"
         },
         {
            title: "Tanggal Masuk",
            data: "tgl_pendaftaran",
            name:"tgl_pendaftaran",
            searchable: false,
            orderable: false,
            render: function (data, type, row, meta) {
               return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY");
            }
         },
         {
            title: "Tanggal Stop Akomodasi",
            data: "tgl_stopakomodasi",
            name: "tgl_stopakomodasi",
            searchable: false,
            orderable: false,
            render: function (data, type, row, meta) {
               return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY");
            }
         },
         {
            title: "Tanggal Pulang",
            data: "tgl_pulang",
            name: "tgl_pulang",
            searchable: false,
            orderable: false,
            render: function (data, type, row, meta) {
               return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY");
            }
         },
         {
            title: "No Pembayaran",
            data: "no_pembayaran",
            name:"no_pembayaran"
         },
         {
            title: "Instalasi - Ruangan Akhir",
            data: "instalasi_nama",
            name:"instalasi_nama",
            searchable: false,
            orderable: false
         },
         {title: "Instalasi Akhir", data: "instalasi_nama", visible: false},
         {title: "Ruangan Akhir", data: "ruangan_nama", visible: false},
         {
            title: "No Pendaftaran",
            data: "no_pendaftaran",
            name:"no_pendaftaran"
         },
         {
            title: "No SEP",
            data: "nosep",
            name:"nosep"
         },
         {
            title: "Nama Pasien",
            data: "nama_pasien",
            name:"nama_pasien",
            searchable: false,
         },
         {title: "No Rekam Medik", data: "no_rekam_medik"},
         {title: "Nama Pasien", data: "nama_pasien", visible: false},
         {
            title: "Cara Bayar - Penjamin",
            data: "carabayar_nama",
            name:"carabayar_nama",
            searchable: false,
            orderable: false
         },
         {title: "Cara Bayar", data: "carabayar_nama", visible: false},
         {title: "Penjamin", data: "penjamin_nama", visible: false},
         {title: "Tanggal Masuk", data: "tgl_pendaftaran", visible: false},
         {title: "Tanggal Pulang", data: "tgl_pulang", visible: false},
         {
            title: "Jumlah Tagihan",
            data: "tagihan",
            name:"tagihan",
            searchable: false,
            className: "text-right",
         },
         {
            title: "Diskon",
            data: "total_discountpembayaran",
            className: "text-right",
            searchable: false,
         },
         {
            title: "Jumlah Dibayar Penjamin",
            data: "total_dijamin",
            name:"total_dijamin",
            searchable: false,
            className: "text-right",
         },
         {
            title: "Jumlah Dibayar Pasien",
            data: "total_dibayar",
            name:"total_dibayar",
            searchable: false,
            className: "text-right",
         },
         {
            title: "Pegawai Kasir",
            data: "pegawai_kasir",
            name:"pegawai_kasir",
            searchable: false,
            sortable: false,
         },
         {
            title: "",
            data: "pendaftaran_id",
            visible: false,
            searchable: false
         },
         {
            title: "Nominal Penjamin > 0",
            data: "total_dijamin",
            name:"total_dijamin",
            visible: false,
            className: "text-right",
         },
      ],
   });
   $(".dataTables_filter").hide();
   $(".filter-form").datatableBootstrapFilter(table, [
      [2, _filterTanggal],
      [16, _filterCaraBayar],
      [17, _filterPenjamin],
      [8, _filterInstalasi],
      [22, _filterCheckNominal],
      [18, _filterTanggalMasuk],
      [19, _filterTanggalPulang],
   ], {
      2:0,
      6:1,
      10:2,
      13:3,
      14:4,
      16:5,
      17:6,
      18:7,
      19:8,
      11:9,
   }, true);

   $(document).on("keydown", "#alasan_batal", function(event){
      var arr = [8,9,16,17,20,32,188,189,190,191];
      
      for(var i = 65; i <= 90; i++){
          arr.push(i);
      }
      
      if($.inArray(event.which, arr) === -1){
          event.preventDefault();
      }

      if($(this).val().length >=5) {
         $("#simpan-batal").attr("disabled", false);
      } else {
         $("#simpan-batal").attr("disabled", true);
      }
  });

  $(document).on("input", "#alasan_batal", function(){
      var regexp = /[^a-zA-Z_@.,/-\s]/g;
      if($(this).val().match(regexp)){
        $(this).val( $(this).val().replace(regexp,"") );
      }

      if($(this).val().length >=5) {
         $("#simpan-batal").attr("disabled", false);
      } else {
         $("#simpan-batal").attr("disabled", true);
      }
   });

   $(document).on("click", "#table-informasi tbody tr", function(){
      var returbayarpelayanan_id = null;
      var instalasi_id = null;

      try {
          returbayarpelayanan_id = table.row(".selected").data().returbayarpelayanan_id;
          instalasi_id = table.row(".selected").data().instalasi_id1;
          groupcarabayar_id = table.row(".selected").data().groupcarabayar_id;
      }
      catch(e) {
          returbayarpelayanan_id = null;
          instalasi_id = null;
      }

      if (returbayarpelayanan_id == null ) {
          $(".data-batal-bayar").attr("disabled", false);
      }
      else {
          $(".data-batal-bayar").attr("disabled", true);
      }

      if(instalasi_id == INSTALASI_RANAP) {
          $("#print-sip").prop("disabled", false);
      }
      else {
          $("#print-sip").prop("disabled", true);
      }
      
      if(groupcarabayar_id === 418)
      {
          $("#detail-invoice-inacbgs").attr("disabled", false);
      }else{
          $("#detail-invoice-inacbgs").attr("disabled", true);
      }
   })
   dateRangeHelper(".startDate",".endDate",".targetDate");
   dateRangeHelper(".startDateIn",".endDateIn",".targetDateIn");
   dateRangeHelper(".startDateOut",".endDateOut",".targetDateOut");
   $("#filter_instalasi").select2InfinityScroll({
      url: "/kasir/inf-pasien-sudah-bayar/filters?type=instalasi",
      callbackData: (param) => {
         return {
            payload: {
               ...param,
            }
         }
      }
   })
   $("#filter_carabayar").select2InfinityScroll({
      url: "/kasir/inf-pasien-sudah-bayar/filters?type=carabayar",
      callbackData: (param) => {
         return {
            payload: {
               ...param,
            }
         }
      }
   })
   $("#filter_penjamin").select2InfinityScroll({
      url: "/kasir/inf-pasien-sudah-bayar/filters?type=penjamin",
      callbackData: (param) => {
         return {
            payload: {
               ...param,
               carabayar_id: $("#filter_carabayar").val()
            }
         }
      }
   })
   $("#filter_penjamin").attr("size",$("#filter_penjamin option").length);
})

$(".data-excel").on("click", function(e){
   e.preventDefault();
   window.open(baseUrl+"kasir/inf-pasien-sudah-bayar/export-excel?"+$.param(table.ajax.params()));
   return false;
});

$("#cetak-invoice").click(function(e){
   e.preventDefault();
   var kelompok = null;
   var tableData = table.row(".selected").data();
   var penjualanresep_id = null;

   if(typeof tableData !== "undefined") {
      var pendaftaran_id = tableData.pendaftaran_id;
      var pembayaran_id = tableData.pembayaran_id;
      var penjualanresep_id = tableData.penjualanresep_id;
      if(penjualanresep_id != null) {
         kelompok = PASIEN_ALKES;
      }
      var url = "/kasir/pembayaran-tagihan/cetak-invoice?id=" + pendaftaran_id + "&invoice_id=" + pembayaran_id + "&kelompok=" + kelompok;
      window.open(url);
      // $(this).attr("data-target", url);
   }
});

$("#cetak-detail-invoice").click(function(e){
   e.preventDefault();
   var tableData = table.row(".selected").data();
   var pendaftaran_id = null;
   var pembayaran_id = null;
   if(typeof tableData !== "undefined") {
      var pendaftaran_id = tableData.pendaftaran_id;
      var pembayaran_id = tableData.pembayaran_id;
      var url = "/kasir/pembayaran-tagihan/cetak-detail-invoice?id=" + pendaftaran_id + "&invoice_id=" + pembayaran_id;
      window.open(url);
   }
});

$(document).on("change", "#check_nominal", function(){
   $("#check_nominal").val(this.checked ? 1 : 0);
});

$("#detail-invoice-inacbgs").click(function(e){
   e.preventDefault();
   var tableData = table.row(".selected").data(); 
   var pendaftaran_id = null;
   if(typeof tableData !== "undefined") {
      var pendaftaran_id = tableData.pendaftaran_id;
      var url = "/kasir/pembayaran-tagihan/cetak-detail-invoice-inacbg?id=" + pendaftaran_id;
      window.open(url);
   }
});

$("#print-sip").click(function(e){
   e.preventDefault();
   var tableData = table.row(".selected").data();
   var pendaftaran_id = null;
   if(typeof tableData !== "undefined") {
      var pendaftaran_id = tableData.pendaftaran_id;
      var url = "/kasir/pembayaran-tagihan/cetak-sip?id=" + pendaftaran_id;
      window.open(url);
   }
});

$("#excel-bgprocess").unbind("click");
    $("#excel-bgprocess").on("click", function (event) {
        var _instalasi_nama = $("#filter_instalasi option:selected").text();
        var _instalasi_id = $("#filter_instalasi option:selected").val();
        var _carabayar_nama = $("#filter_carabayar option:selected").text();
        var _carabayar_id = $("#filter_carabayar option:selected").val();
        var penjamin =[];
        $('#filter_penjamin option').each(function() {
         penjamin.push($(this).text())
       });
      _penjamin = encodeURIComponent(penjamin.join("__"))
        _url = encodeURI(baseUrl+"kasir/inf-pasien-sudah-bayar/show-popup-excel?instalasi_nama="+ _instalasi_nama 
        +"&instalasi_id="+ _instalasi_id
        +"&carabayar_nama=" + _carabayar_nama
        +"&carabayar_id=" + _carabayar_id
        +"&penjamin=" + _penjamin +"&")
        $(this).attr("data-url",_url);
})