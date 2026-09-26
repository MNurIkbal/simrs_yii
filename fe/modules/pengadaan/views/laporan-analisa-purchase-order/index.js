var table;
$(() => {
   const _baseUrl = baseUrl + 'pengadaan/laporan-analisa-purchase-order';
   table = $("#example").docoTabel({
       filter: false,
       sorting: [[10, "desc"]],
       displayLength: 10,
       processing: true,
       serverSide: true,
       scrollX: true,
       scrollY: false,
       ajax: {
           'url': baseUrl + "pengadaan/laporan-analisa-purchase-order/get-data",
           'type': 'POST'
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
               title: "Kode Obat",
               data: "kode_obat",
               searchable: false,
               render: (data) => {
                   return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "Nama Obat",
               data: "nama_obat",
               searchable: true,
               render: (data) => {
                   return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "Manufaktur",
               data: "manufaktur",
               searchable: false,
               render: (data) => {
                   return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "Jenis Obat",
               data: "jenis_obat",
               searchable: false,
               render: (data) => {
                   return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "Nomor PR",
               data: "no_pr",
               searchable: false,
               render: (data) => {
                   return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "Tanggal PR",
               data: "created_date_pr",
               searchable: false,
               render: (data) => {
                  return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
               }
           },
           {
                title: "Tanggal Approve PR",
                data: "tgl_approve",
                searchable: false,
                render: (data) => {
                  return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
                }
            },
           {
               title: "Qty PR",
               data: "qty_pr",
               searchable: false,
               className: "text-right",
               render: $.fn.dataTable.render.number('.', ',', 0, ''),
           },
           {
               title: "UoM PR",
               data: "satuan_pr",
               searchable: false,
               render: (data) => {
                  return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "Catatan PR",
               data: "catatan",
               searchable: false,
               render: (data) => {
                  return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "Nomor. PO",
               data: "no_po",
               searchable: true,
               render: (data) => {
                  return data == "" || data == null ? "-" : data
               }
           },
           {
                title: "Cito",
                data: "po_cito",
                searchable: true,
                render: (data) => {
                    return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Admin",
                data: "po_admin",
                searchable: true,
                render: (data) => {
                   return data == "" || data == null ? "-" : data
                }
            },
            {
                title: "Consignment",
                data: "po_consigment",
                searchable: true,
                render: (data) => {
                   return data == "" || data == null ? "-" : data
                }
            },
           {
               title: "Tanggal PO",
               data: "tgl_po",
               render: (data) => {
                  return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
               }
           },
           {
               title: "Tanggal Validasi PO",
               data: "tgl_po_validasi",
               searchable: false,
               render: (data) => {
                   return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
               }
           },
           {
               title: "Qty PO",
               data: "qty_po",
               searchable: false,
               className: 'text-right',
               render: $.fn.dataTable.render.number('.', ',', 0, ''),
           },
           {
               title: "UoM PO",
               data: "satuan_po",
               searchable: false,
               render: (data) => {
                   return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "Harga (Rp.)",
               data: "harga",
               searchable: false,
               className: "text-right",
               render: $.fn.dataTable.render.number('.', ',', 2, ''),
           },
           {
               title: "Diskon (%)",
               data: "disc_persen",
               searchable: false,
               className: "text-right",
               render: $.fn.dataTable.render.number('.', ',', 2, ''),
           },
           {
               title: "PPn (%)",
               data: "ppn_persen",
               searchable: false,
               className: "text-right",
               render: $.fn.dataTable.render.number('.', ',', 2, ''),
           },
           {
               title: "Sub Total (Rp.)",
               data: "sub_total",
               searchable: false,
               className: "text-right",
               render: $.fn.dataTable.render.number('.', ',', 2, ''),
           },
           {
               title: "Total Harga (Rp.)",
               data: "total",
               searchable: false,
               className: "text-right",
               render: $.fn.dataTable.render.number('.', ',', 2, ''),
           },
           {
               title: "Status PO",
               data: "status_po_kondisi",
               render: (data) => {
                   return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "Tanggal Batal PO",
               data: "tgl_po_batal",
               searchable: false,
               render: (data) => {
                   return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
               }
           },
           {
               title: "Catatan Batal PO",
               data: "catatan_batal",
               searchable: false,
               render: (data) => {
                   return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "Kode Supplier",
               data: "kode_supplier",
               searchable: false,
               render: (data) => {
                   return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "Nama Supplier",
               data: "nama_supplier",
               render: (data) => {
                   return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "Tanggal Penerimaan",
               data: "tgl_penerimaan",
               searchable: false,
               render: (data) => {
                   return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
               }
           },
           {
               title: "Qty Penerimaan",
               data: "qty_penerimaan",
               searchable: false,
               className: "text-right",
               render: $.fn.dataTable.render.number('.', ',', 0, ''),
           },
           {
               title: "UoM Penerimaan",
               data: "penerimaan",
               searchable: false,
               render: (data) => {
                   return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "Sisa Penerimaan",
               data: "sisa_penerimaan",
               searchable: false,
               className: "text-right",
               render: $.fn.dataTable.render.number('.', ',', 0, ''),
           },
           {
               title: "UoM Sisa Penerimaan",
               data: "uom_penerimaan",
               searchable: false,
               render: (data) => {
                   return data == "" || data == null ? "-" : data
               }
           },
           {
               title: "PR diapprove ke PO dibuat",
               data: "pr_to_po",
               searchable: false,
               render: (data) => {
                  return data == null ? '-' : data + ' Hari'
               }
           },
           {
               title: "PR diapprove ke PO divalidasi",
               data: "pr_to_povalidasi",
               searchable: false,
               render: (data) => {
                   return data == null ? '-' : data + ' Hari'
               }
           },
           {
               title: "PO dibuat ke PO di Validasi",
               data: "po_to_povalidasi",
               searchable: false,
               render: (data) => {
                   return data == null ? '-' : data + ' Hari'
               }
           },
           {
               title: "PR diapprove ke penerimaan",
               data: "pr_to_penerimaan",
               searchable: false,
               render: (data) => {
                   return data == null ? '-' : data + ' Hari'
               }
           },
           {
               title: "PO di Validasi ke penerimaan",
               data: "povalidasi_to_penerimaan",
               searchable: false,
               render: (data) => {
                   return data == null ? '-' : data + ' Hari'
               }
           },
       ],
       formFilters: [
           {
               fieldName: 'tgl_pr',
               label: 'Tanggal Buat PR',
               type: {
               name: 'rangeDate',
               payload: {
                   allDate: true,
               }
               }
           },
           {
               fieldName: 'tgl_po',
               label: 'Tanggal Buat PO',
               type: {
               name: 'rangeDate',
               payload: {
                   allDate: true,
               }
               }
           },
           'nama_obat',
           {
               fieldName: 'no_po',
               label: 'Nomor PO',
           },
           {
               fieldName: 'status_po_kondisi',
               label: 'Status',
               type: {
                  name: 'select',
                  payload: dropdownStatus
               }
            },
           'nama_supplier'
       ]
   });
   $(document).on('click', '.export-excel-laporan', (e) => {
      e.preventDefault();
      if (table.data().count() > 0) {
         var url = _baseUrl + '/export-excel'
         $.ajax({
            url: url,
            method: 'post',
            data: $.param(table.ajax.params()),
            success: function (data) {
               window.open(_baseUrl + '/download-excel?data=' + data, '_blank');
            }
         })
      }
      else {
         docoNotification('warning', 'Terjadi Kesalahan', 'Data Tidak Tersedia!');
      }
   })
   $(".btn-reset").on("click", function () {
      $(document).find('input').val('');
      $(`#example`).DataTable().context[0].ajax.data.advancedFilter = serializeArrayToJson($(`#form-filter__example`))
      $(`#example`).DataTable().ajax.reload()
   });
   $('#btn-search__example').css('display', 'none')
   $('#btn-reset__example').css('display', 'none');
})
