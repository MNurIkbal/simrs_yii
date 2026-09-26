$(document).ready(function(){
   table = $("#table-job-order").docoTabel({
      filter: false,
      select: {
         style: 'os',
         selector: 'tr'
      },
      sorting: [[1, "desc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollY: "300px",
      scrollCollapse: true,
      cache: false,
      cacheFilter : false,
      ajax: {
         url: "/kasir/pembayaran-tagihan/get-data-order?pendaftaran_id=" + pendaftaran_id,
      },
      columns: [
         {
            data: null,
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData, rowAdditionalData) => {
               var tableInfo = table.page.info()
               return tableInfo.start + rowAdditionalData.row + 1
            }
         },
         {
            title: "Tanggal",
            data: "tgl_pelayanan",
            searchable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY")
            }
         },
         {
            title: "Instalasi  / Ruangan",
            data: "instalasi_nama",
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData) => {
               return `
                  ${rowData.instalasi_nama != null ? rowData.instalasi_nama : '-'}/${rowData.ruangan_nama != null ? rowData.ruangan_nama : '-'}
               `
            }
         },
         {
            title: "Tindakan  / Obat",
            data: "daftartindakan_nama",
            searchable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Qty",
            data: "qty",
            className: "text-right",
            orderable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Instalasi Tujuan",
            data: "instalasi_tujuan",
            searchable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Pegawai Pengorder",
            data: "pegawai_nama",
            searchable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
      ],
   });
});
$(".dataTables_filter").hide();
$('#btn-search__example').css('display', 'none');
$('#btn-reset__example').css('display', 'none');
$('.btn-kembali').on('click', function(){
   $('#modal_backdrop').modal('hide')
})
