$(document).ready(function() {
   localStorage.clear();
   localStorage.setItem("penjamin-sudah-bayar", _dataPenjamin);
   table = $("#example").docoTabel({
      filter: true,
      sorting: [[1, "desc"]], 
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: true,
      ajax: baseUrl+"kasir/lap-pasien-sudah-bayar/get-data",
      columns: [
         {
            title: "No",
            data: "rowNum",
            searchable: false,
            orderable: false
         },
         {title: "Tanggal Pembayaran", data: "tgl_pembayaran"},
         {title: "Tanggal Masuk - keluar",  data: "tgl_masuk_keluar", searchable: false},
         {title: "Instalasi - Ruangan Akhir",  data: "instalasi_nama", searchable: false},
         {title: "No Pendaftaran", data: "no_pendaftaran", searchable: false},
         {title: "Nama Pasien", data: "nama_pasien"},
         {title: "Nomor Rekam Medik", data: "no_rekam_medik", searchable: false},
         {title: "Cara Bayar - Penjamin", data: "carabayar_nama", searchable: false},
         {title: "Cara Bayar", data: "carabayar_nama", visible: false},
         {title: "Penjamin", data: "penjamin_nama", visible: false},
         {title: "Tanggal Masuk", data: "tgl_pendaftaran", visible: false},
         {title: "Tanggal Pulang", data: "tgl_pulang", visible: false},
         {title: "Jumlah Tagihan", data: "total_tagihan", searchable: false, "class":"text-right"},
         {title: "Diskon", data: "total_discount", searchable: false, "class":"text-right"},
         {title: "Jumlah Dibayar Penjamin", data: "subsidi_asuransi", searchable: false, "class":"text-right"},
         {title: "Jumlah Dibayar Pasien", data: "total_sudah_dibayarkan", searchable: false, "class":"text-right"},
      ]
   });
   $(".dataTables_filter").hide();
   $(".filter-form").datatableBootstrapFilter(table, 
      [
         [
            1, _filterTanggal
         ], 
         [
            8, _filterCaraBayar
         ],
         [
            9, _filterPenjamin
         ],
         [
            10, _filterTanggalMasuk
         ],
         [
            11, _filterTanggalPulang
         ],
      ], {
         1:0,
         5:1,
         8:2,
         9:3,
         10:4,
         11:5,
      }, true);
      
      
   dateRangeHelper(".startDate",".endDate",".targetDate");
   dateRangeHelper(".startDateIn",".endDateIn",".targetDateIn");
   dateRangeHelper(".startDateOut",".endDateOut",".targetDateOut");
   $("#filter_carabayar").select2InfinityScroll({
      url: "/kasir/lap-pasien-sudah-bayar/filters?type=carabayar",
      callbackData: (param) => {
         return {
            payload: {
               ...param,
            }
         }
      }
   })
   $("#filter_penjamin").select2InfinityScroll({
      url: "/kasir/lap-pasien-sudah-bayar/filters?type=penjamin",
      callbackData: (param) => {
         return {
            payload: {
               ...param,
               carabayar_id: $("#filter_carabayar").val()
            }
         }
      }
   })

   $("#cetak-detail-invoice").click(function(e){
      e.preventDefault();
      var tableData = table.row(".selected").data();
      var pendaftaran_id = null;
      var pembayaran_id = null;
      if(typeof tableData !== "undefined") {
         // var pendaftaran_id = tableData.pendaftaran_id;
         // var pembayaran_id = tableData.pembayaran_id;
         var url = "/kasir/pembayaran-tagihan/cetak-detail-invoice?id=" + pendaftaran_id + "&invoice_id=" + pembayaran_id;
         window.open(url);
      }
   });
});