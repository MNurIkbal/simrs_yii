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
       info: false,
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
          }, //0
          {
             title: "No",
             data: "rowNum",
             searchable: false,
             orderable: false
          }, //1
          {
             title: "Tanggal Pembayaran",
             data: "tgl_pembayaran",
             name:"tgl_pembayaran"
          }, //2
          {
             title: "Tanggal Masuk",
             data: "tgl_pendaftaran",
             name: "tgl_pendaftaran",
             searchable: false,
             orderable: false,
             render: function (data, type, row, meta) {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY");
             }
          }, //3
          {
             title: "Tanggal Stop Akomodasi",
             data: "tgl_stopakomodasi",
             name: "tgl_stopakomodasi",
             searchable: false,
             orderable: false,
             render: function (data, type, row, meta) {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY");
             }
          }, //4
          {
             title: "Tanggal Pulang",
             data: "tgl_pulang",
             name: "tgl_pulang",
             searchable: false,
             orderable: false,
             render: function (data, type, row, meta) {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY");
             }
          }, //5
          {
             title: "No Pembayaran",
             data: "no_pembayaran",
             name:"no_pembayaran"
          }, //6
          {
             title: "Instalasi - Ruangan Akhir",
             data: "instalasi_nama",
             name:"instalasi_nama",
             searchable: false,
             orderable: false
          }, //7
          {
             title: "Kamar / Tempat Tidur",
             orderable: false,
             searchable: false,
             render: (data, rowElement, rowData) => {
                 return `${rowData.kamarruangan_nokamar != null ? rowData.kamarruangan_nokamar : "-"} / ${rowData.no_tempattidur != null ? rowData.no_tempattidur : "-"}`
             }
          }, //8
          {
            title: "Instalasi Akhir", 
            data: "instalasi_nama", 
            visible: false
          }, //9
          {
            title: "Ruangan Akhir", 
            data: "ruangan_nama", 
            visible: false
          }, //10
          {
             title: "No Pendaftaran",
             data: "no_pendaftaran",
             name:"no_pendaftaran"
          }, //11
          {
             title: "No SEP",
             data: "nosep",
             name:"nosep"
          }, //12
          {
             title: "Nama Pasien",
             data: "nama_pasien",
             name:"nama_pasien",
             searchable: false,
          }, //13
          {title: "No Rekam Medik", data: "no_rekam_medik"}, //14
          {title: "Nama Pasien", data: "nama_pasien", visible: false}, //15
          {
             title: "Cara Bayar - Penjamin",
             data: "carabayar_nama",
             name:"carabayar_nama",
             searchable: false,
             orderable: false
          }, //16
          {title: "Cara Bayar", data: "carabayar_nama", visible: false}, //17
          {title: "Penjamin", data: "penjamin_nama", visible: false}, //18
          {title: "Tanggal Masuk", data: "tgl_pendaftaran", visible: false}, //19
          {title: "Tanggal Pulang", data: "tgl_pulang", visible: false}, //20
          {
            title: "Kelas Ditempati / Kelas Tagihan",
            data: "hak_kelas",
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData) => {
                let _wording = `${rowData.kelaspelayanan_nama != null ? rowData.kelaspelayanan_nama : "-"} / ${rowData.kelas_tagihan != null ? rowData.kelas_tagihan : "-"}`
                if(rowData.is_pasientitipan) {
                    _wording = `${data != null ? data : "-"} / ${rowData.kelas_tagihan != null ? rowData.kelas_tagihan : "-"}`
                } else if (data != null) {
                    _wording = `${rowData.kelaspelayanan_nama != null ? rowData.kelaspelayanan_nama : "-"} / ${rowData.kelas_tagihan != null ? rowData.kelas_tagihan : "-"}`
                }
                return _wording
            }
          }, //21
          {
             title: "Status Kamar",
             data: "status_kelas",
             name:"nama_pasien",
             searchable: false,
          }, //22
          {
             title: "Jumlah Tagihan",
             data: "tagihan",
             name:"tagihan",
             searchable: false,
             className: "text-right",
          }, //23
          {
             title: "Diskon",
             data: "total_discountpembayaran",
             className: "text-right",
             searchable: false,
          }, //24
          {
             title: "Jumlah Dibayar Penjamin",
             data: "total_dijamin",
             name:"total_dijamin",
             searchable: false,
             className: "text-right",
          }, //25
          {
             title: "Jumlah Dibayar Pasien",
             data: "total_dibayar",
             name:"total_dibayar",
             searchable: false,
             className: "text-right",
          }, //26
          {
             title: "Pegawai Kasir",
             data: "pegawai_kasir",
             name:"pegawai_kasir",
             searchable: false,
             sortable: false,
          }, //27
          {
             title: "",
             data: "pendaftaran_id",
             visible: false,
             searchable: false
          }, //28
          {
             title: "Nominal Penjamin > 0",
             data: "total_dijamin",
             name:"total_dijamin",
             visible: false,
             className: "text-right",
          }, //29
       ],
       createdRow: (rowElement, data) => {
        var is_pasientitipan = (data.is_pasientitipan != null) ? data.is_pasientitipan : false;
        var is_stoptitipan = (data.is_stoptitipan != null) ? data.is_stoptitipan : false;

        if (is_pasientitipan && !is_stoptitipan) {
            /* $(rowElement).css("font-weight", "bold") */
            /* $(rowElement).css("background-color", "#ffcccc") */
        }
       }
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
       [2, _filterTanggal],
       [9, _filterInstalasi],
       [17, _filterCaraBayar],
       [18, _filterPenjamin],
       [19, _filterTanggalMasuk],
       [20, _filterTanggalPulang],
       [25, _filterCheckNominal],
    ], {
       2:0,
       6:1,
       11:2,
       14:3,
       15:4,
       17:5,
       18:6,
       19:7,
       20:8,
       12:9,
       9:10,
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