/**
 * ? @author : Budi (budi@sirs.co.id)
 * ? Powered by Sirs
 */

 var table;
 $(() => {
    const _baseUrl = baseUrl + 'pengadaan/laporan-analisa-po-non-medis';
    table = $("#example").docoTabel({
       filter: false,
       order: [[9, 'asc'], [8, 'asc'], [2, 'asc']],
       displayLength: 10,
       processing: true,
       serverSide: true,
       scrollX: true,
       scrollY: false,
       ajax: {
          'url': _baseUrl + "/get-data",
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
             title: "Kode Barang",
             data: "kode_barang",
             orderable: false,
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
             title: "Nama Barang",
             data: "nama_barang",
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
             title: "No. PR",
             data: "no_pr",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
             title: "Tanggal Buat PR",
             data: "created_date_pr",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
             }
          },
          {
            title: "Tanggal Approve PR",
            data: "tgl_approve",
            orderable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
            }
         },
          {
             title: "Qty PR",
             data: "qty_pr",
             orderable: false,
             // visible: false,
             className: "text-right",
             render: $.fn.dataTable.render.number('.', ',', 0, ''),
          },
          {
             title: "UoM PR",
             data: "satuan_pr",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
             title: "Catatan PR",
             data: "catatan",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
             title: "No. PO",
             data: "no_po",
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
            title: "Cito",
            data: "po_cito",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Admin",
            data: "po_admin",
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
          {
             title: "Tanggal Buat PO",
             data: "tgl_po",
             render: (data) => {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
             }
          },
          {
             title: "Tanggal Validasi PO",
             data: "tgl_validasi_po",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
             }
          },
          {
             title: "Tanggal Batal PO",
             data: "tgl_batal_po",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
             }
          },
          {
             title: "Catatan Batal PO",
             data: "catatan_batal_po",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
             title: "Qty PO",
             data: "qty_po",
             orderable: false,
             // visible: false,
             className: 'text-right',
             render: $.fn.dataTable.render.number('.', ',', 0, ''),
          },
          {
             title: "UoM PO",
             data: "satuan_po",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
             title: "Harga (Rp.)",
             data: "harga",
             orderable: false,
             // visible: false,
             className: "text-right",
             render: $.fn.dataTable.render.number('.', ',', 0, ''),
          },
          {
             title: "Diskon (%)",
             data: "diskon",
             orderable: false,
             // visible: false,
             className: "text-right",
             render: $.fn.dataTable.render.number('.', ',', 0, ''),
          },
          {
             title: "PPn (%)",
             data: "ppn",
             orderable: false,
             // visible: false,
             className: "text-right",
             render: $.fn.dataTable.render.number('.', ',', 0, ''),
          },
          {
             title: "Subtotal (Rp.)",
             data: "subtotal",
             orderable: false,
             // visible: false,
             className: "text-right",
             render: $.fn.dataTable.render.number('.', ',', 0, ''),
          },
          {
             title: "Total (Rp.)",
             data: "total",
             orderable: false,
             // visible: false,
             className: "text-right",
             render: $.fn.dataTable.render.number('.', ',', 0, ''),
          },
          {
             title: "No. Penerimaan",
             data: "no_penerimaan",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
             title: "Tanggal Penerimaan",
             data: "tgl_penerimaan",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
             }
          },
          {
             title: "Qty Penerimaan",
             data: "qty_penerimaan",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
             title: "UoM Penerimaan",
             data: "penerimaan",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
             title: "Sisa Penerimaan (PO Ballance)",
             data: "sisa_penerimaan",
             orderable: false,
             // visible: false,
             className: "text-right",
             render: $.fn.dataTable.render.number('.', ',', 0, ''),
          },
          {
             title: "UoM Sisa Penerimaan",
             data: "penerimaan",
             orderable: false,
             // visible: false,
             className: "text-right",
             render: $.fn.dataTable.render.number('.', ',', 0, '')
          },
          {
             title: "No. Faktur Penerimaan",
             data: "nofaktur_penerimaan",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
             title: "Tanggal Verifikasi Penerimaan",
             data: "tgl_verifikasi_penerimaan",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
             }
          },
          {
             title: "PR diapprove ke PO",
             data: "pr_jarak_po",
             orderable: false,
             // visible: false,
             render: (data) => {
               return data == null ? "-" : data + ' Hari'
             }
          },
          {
            title: "PO dibuat ke Validasi PO",
            data: "po_jarak_validasi_po",
            orderable: false,
            // visible: false,
            render: (data) => {
               return data == null ? "-" : data + ' Hari'
            }
         },
          {
             title: "PO dibuat ke Tanggal Penerimaan",
             data: "po_jarak_tgl_penerimaan",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == null ? "-" : data + ' Hari'
             }
          },
          {
             title: "PR diapprove ke Tanggal Penerimaan",
             data: "pr_jarak_tgl_penerimaan",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == null ? "-" : data + ' Hari'
             }
          },
          {
             title: "PO divalidasi ke Tanggal Penerimaan",
             data: "po_validasi_tgl_penerimaan",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == null ? "-" : data + ' Hari'
             }
          },
          {
             title: "Kode Supplier",
             data: "kode_supplier",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
             title: "Nama Supplier",
             data: "nama_supplier",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : data
             }
          },
          {
             title: "Catatan PO",
             data: "catatan_po",
             orderable: false,
             // visible: false,
             render: (data) => {
                return data == "" || data == null ? "-" : data
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
          'nama_barang',
          'no_po'
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
    $('.flex-1').css('display', 'none');
    if ($(this).find('row row__hidden')) {
       $("#filter-section__example .row__hidden").removeClass("row__hidden");
    }
    $('#example-nama_barang--form').on('keypress', (e) => {
       if (e.which == 13) {
          $(".btn-search--datatable").trigger('click');
       }
    })
 })