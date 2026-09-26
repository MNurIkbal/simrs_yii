// var table;
$(() => {
   moment.locale("en");
   const status = [
      { id: 470, text: 'BELUM DISETUJUI' },
      { id: 471, text: 'SUDAH DISETUJUI' },
      { id: 472, text: 'BATAL' },
   ];
   $("#btn-approve").prop("disabled", true);
   $("#btn-edit-tanggal").prop("disabled", true);
   
   tableRujukan = $("#tbl-rujukan").docoTabel({
      info: false,
      filter: false,
      columnDefs: [{
         orderable: false,
         className: 'select-checkbox',
         targets: 0
      }],
      select: {
         style: 'os',
         selector: 'tr'
      },
      sorting: [[3, "desc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: false,
      scrollY: true,
      ajax: {
         url: baseController + "get-data-rujukan",
      },
      columns: [
         {
            data: null,
            searchable: false,
            orderable: false,
            defaultContent: '',
         },
         {
            data: null,
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData, rowAdditionalData) => {
               var tableInfo = tableRujukan.page.info()
               return tableInfo.start + rowAdditionalData.row + 1
            }
         },
         {
            title: "Status Pembayaran",
            data: "status_bayar",
            orderable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }

         },
         {
            title: "Tanggal Rujukan",
            data: "tgl_rujukan",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY")
            }
         },
         {
            title: "Detail Diagnosa",
            data: "detail_diagnosa"
         },
         {
            title: "Pasien",
            data: "nama_pasien",
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData) => {
               return `
                   <p style="margin-bottom: 2px"> <b> ${rowData.nama_pasien != null ? rowData.nama_pasien : ''} (${rowData.jenis_kelamin_kode != null ? rowData.jenis_kelamin_kode : '-'})</b></p>
                   <p style="margin-bottom: 2px"> <b> ${rowData.tanggal_lahir != null ? moment(rowData.tanggal_lahir).format("DD MMM YYYY") : '-'} </b></p>
                   <p style="margin-bottom: 2px"> <b> ${rowData.no_pendaftaran != null ? rowData.no_pendaftaran : '-'} - ${rowData.no_rekam_medik != null ? rowData.no_rekam_medik : '-'}</b></p>
                   <p style="margin-bottom: 2px;"> <b> ${rowData.no_sep != null ? 'No. SEP : ' + rowData.no_sep : '-'}</b></p>
               `
            }
         },
         {
            title: "Pemeriksaan",
            data: "pemeriksaan",
            orderable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Dokter Perujuk",
            data: "dokter_perujuk",
            orderable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Cara Bayar / Penjamin",
            data: "carabayar_nama",
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData) => {
               return `${rowData.carabayar_nama != null ? rowData.carabayar_nama : ''} / ${rowData.penjamin_nama != null ? rowData.penjamin_nama : ''}`
            }
         },
         {
            title: "Nomor Rujukan",
            data: "no_rujukan",
            orderable: false,
            render: (data, rowElement, rowData) => {
               return `
                   <p style="margin-bottom: 2px"> ${rowData.no_rujukan != null ? rowData.no_rujukan : ''} </p>
                   <p style="margin-bottom: 2px"> ${rowData.ruanganasal_nama != null ? rowData.ruanganasal_nama : '-'} </p>
               `
            }
            // render: (data) => {
            //    return data == "" || data == null ? "-" : data
            // }
         },
         // {
         //    title: "Asal Rujukan",
         //    data: "ruangan",
         //    searchable: false,
         //    render: (data) => {
         //       return data == "" || data == null ? "-" : data
         //    }
         // },
      ],
      formFilters: [
         {
            fieldName: 'tgl_rujukan',
            label: 'Tanggal Rujukan',
            type: {
               name: 'rangeDate',
               payload: {
                  allDate: true,
               }
            }
         },
         'no_pendaftaran',
         'no_rekam_medik',
         'nama_pasien',
         'no_rujukan',
         {
            fieldName: 'tanggal_lahir',
            label: 'Tanggal Lahir',
            type: {
               name: 'rangeDate',
               payload: {
                  allDate: true,
               }
            }
         },
         {
            fieldName: 'carabayar_id',
            label: 'Cara Bayar',
            type: {
               name: 'dropdownScroll',
               url: baseController + "filters",
               additionalPayload: {
                  type: 'carabayar',
               }
            }
         },
         {
            fieldName: 'penjamin_id',
            label: 'Penjamin',
            type: {
               name: 'dropdownScroll',
               url: baseController + "filters",
               additionalPayload: {
                  type: 'penjamin',
               }
            }
         },
         {
            fieldName: 'ruanganasal_id',
            label: 'Asal Rujukan',
            type: {
               name: 'dropdownScroll',
               url: baseController + "filters",
               additionalPayload: {
                  type: 'ruangan',
               }
            }
         },
         {
            fieldName: 'pegawai_id',
            label: 'Dokter Perujuk',
            type: {
               name: 'dropdownScroll',
               url: baseController + "filters",
               additionalPayload: {
                  type: 'dokter',
               }
            }
         },
         {
            fieldName: 'status_penunjang',
            label: 'Status',
            type: {
               name: 'select',
               payload: status
            }
         },
      ],
      filterRendered: (wrapper) => {
         var defaultPlaceHolder = [
            {
               id: '',
               text: '- Semua -'
            }
         ]
         $(wrapper).find('[name="carabayar_id"]').bind('change', ({ currentTarget }) => {
            if ($(currentTarget).val() == '' || $(currentTarget).val() == null) {
               $(wrapper).find('[name="penjamin_id"]').html('')
               $(wrapper).find('[name="penjamin_id"]').select2({
                  defaultPlaceHolder,
               })
            }
            else {
               $(wrapper).find('[name="penjamin_id"]').select2InfinityScroll({
                  url: baseController + 'filters',
                  callbackData: (params) => {
                     return {
                        term: params.term,
                        page: params.page || 1,
                        limit: params.limit,
                        type: 'penjamin',
                        additionalPayload: {
                           carabayar_id: $(currentTarget).val(),
                        }
                     }
                  }
               });
            }
         });
      },
      rowCallback: (rowElement, data) => {
         if (data.carabayar_kode_warna != null) {
            $($(rowElement).find('td')[7]).css('background-color', data.carabayar_kode_warna);
            $($(rowElement).find('td')[7]).css('color', invertColor(data.carabayar_kode_warna, true));
         }
         if(data.is_cyto) {
            $(rowElement).css('background-color', 'rgb(255,137,0)').css("color", "black")
         }
      }

      // rowCallback: (rowElement, data) => {
      //    if (data.status_penunjang == constBatal) {
      //       if (data.is_cyto) {
      //          $(rowElement).css('background-color', '#F1948A').css("color", "black")
      //          $($(rowElement).find('td')[4]).css('background-color', '#85C1E9').css("color", "black")
      //       }
      //       else {
      //          $(rowElement).css('background-color', '#F1948A').css("color", "black")
      //       }

      //    } else if (data.status_penunjang == 470) {
      //       if (data.is_cyto) {
      //          $(rowElement).css("background-color", "#FFFfff").css("color", "black");
      //          $($(rowElement).find('td')[4]).css('background-color', '#85C1E9').css("color", "black")
      //       }
      //       else {
      //          $(rowElement).css("background-color", "#FFFfff").css("color", "black");
      //       }

      //    } else if (data.status_penunjang == constDisetujui) {
      //       var statusBayarUpdate = false;
      //       if (data.jumlah_bayar > 0 && data.jumlah_tagihan == 0) {
      //          statusBayarUpdate = true;
      //       }
      //       if (data.status_periksa == 476) {
      //          if (data.is_cyto) {
      //             $(rowElement).css('background-color', '#F1948A').css("color", "black")
      //             $($(rowElement).find('td')[4]).css('background-color', '#85C1E9').css("color", "black")
      //          }
      //          else {
      //             $(rowElement).css('background-color', '#F1948A').css("color", "black")
      //          }
      //       }
      //       else {
      //          if (statusBayarUpdate == true) {
      //             if (data.jml_pemeriksaan == data.jml_pemeriksaan_approve) {
      //                if (data.is_cyto) {
      //                   $(rowElement).css('background-color', '#58D68D').css("color", "black")
      //                   $($(rowElement).find('td')[4]).css('background-color', '#85C1E9').css("color", "black")
      //                }
      //                else {
      //                   $(rowElement).css('background-color', '#58D68D').css("color", "black")
      //                }
      //             }
      //             else {
      //                if (data.is_cyto) {
      //                   $(rowElement).css('background-color', '#BB8FCE').css("color", "black")
      //                   $($(rowElement).find('td')[4]).css('background-color', '#85C1E9').css("color", "black")
      //                }
      //                else {
      //                   $(rowElement).css('background-color', '#BB8FCE').css("color", "black")
      //                }
      //             }
      //          } else if ((statusBayarUpdate == null || !statusBayarUpdate)) {
      //             if (data.jml_pemeriksaan == data.jml_pemeriksaan_approve) {
      //                if (data.is_cyto) {
      //                   $(rowElement).css('background-color', '#FAF844').css("color", "black")
      //                   $($(rowElement).find('td')[4]).css('background-color', '#85C1E9').css("color", "black")
      //                }
      //                else {
      //                   $(rowElement).css('background-color', '#FAF844').css("color", "black")
      //                }
      //             }
      //             else {
      //                if (data.is_cyto) {
      //                   $(rowElement).css('background-color', '#F8C471').css("color", "black")
      //                   $($(rowElement).find('td')[4]).css('background-color', '#85C1E9').css("color", "black")
      //                }
      //                else {
      //                   $(rowElement).css('background-color', '#F8C471').css("color", "black")
      //                }
      //             }
      //          }
      //       }
      //    }
      // }
   });
   $(document).on("click", ".btn-reset", function (e) {
      const tableId = "tbl-rujukan";
      const element = $(`#filter-section__${tableId}`)
      const formWrapper = $(`#form-filter__${tableId}`)
      element.find('input').val('')
      element.find('select').val(null).trigger('change')
      element.find('#tgl_rujukan-startDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      element.find('#tgl_rujukan-endDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      const tableElement = $(`#${tableId}`).DataTable()
      showLoader()
      tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
      tableElement.ajax.url(baseController + "get-data-rujukan?type=0").load()
      $("#btn-approve").prop("disabled", true);
      $("#btn-edit-tanggal").prop("disabled", true);
      $("#btn-batal").prop("disabled", true);
   });
   $(document).on("click", "#tbl-rujukan tbody tr", function () {
      try {
         primaryKey = tableRujukan.row('.selected').data().primary ? tableRujukan.row('.selected').data().primary : null;
         statusPenunjang = tableRujukan.row(".selected").data().status_penunjang ? tableRujukan.row(".selected").data().status_penunjang : null;
         statusBayar = tableRujukan.row(".selected").data().is_bayar ? tableRujukan.row(".selected").data().is_bayar : null;
         caraBayar = tableRujukan.row(".selected").data().carabayar_id ? tableRujukan.row(".selected").data().carabayar_id : null;
         statusPeriksa = tableRujukan.row(".selected").data().status_periksa ? tableRujukan.row(".selected").data().status_periksa : null;
         instalasi = tableRujukan.row(".selected").data().instalasi_id ? tableRujukan.row(".selected").data().instalasi_id : null
         penjamin = tableRujukan.row(".selected").data().penjamin_id ? tableRujukan.row(".selected").data().penjamin_id : null
         jml_pemeriksaan = tableRujukan.row(".selected").data().jml_pemeriksaan ? tableRujukan.row(".selected").data().jml_pemeriksaan : null
         jml_pemeriksaan_approve = tableRujukan.row(".selected").data().jml_pemeriksaan_approve ? tableRujukan.row(".selected").data().jml_pemeriksaan_approve : null
         jumlah_tagihan = tableRujukan.row(".selected").data().jumlah_tagihan ? tableRujukan.row(".selected").data().jumlah_tagihan : 0
         jumlah_bayar = tableRujukan.row(".selected").data().jumlah_bayar ? tableRujukan.row(".selected").data().jumlah_bayar : 0
         statusBatal = tableRujukan.row(".selected").data().is_bayar ? tableRujukan.row(".selected").data().is_bayar : null;
         is_referred = tableRujukan.row(".selected").data().is_referred ? tableRujukan.row(".selected").data().is_referred : false;
         is_aps = tableRujukan.row(".selected").data().is_aps ? tableRujukan.row(".selected").data().is_aps : false;

      } catch (e) {
         primaryKey = false
         statusPenunjang = false;
         statusPeriksa = false;
         statusBatal = false;
      }
      if (statusPeriksa != constBelumPeriksa && statusPeriksa) {
         if (statusPeriksa == 476) {
            statusBatal = true
         }
         statusPeriksa = true;
      } else {
         statusPeriksa = false;
      }

      if (primaryKey) {
         $("#btn-approve").attr("data-target", approveUrl + primaryKey);
         $("#btn-batal").attr("data-target", batalUrl + primaryKey);
      }

      if ($('#tbl-rujukan tr.selected').length == 0) {
         $("#btn-approve").prop("disabled", true);
         $("#btn-edit-tanggal").prop("disabled", true);
         $("#btn-batal").prop("disabled", true);
      }
      else {
         $("#btn-approve").prop("disabled", false);
         $("#btn-edit-tanggal").prop("disabled", false);
         $("#btn-batal").prop("disabled", false);
         $("#btn-batal").attr("data-options", 'link');

         if(is_aps) {
            $("#btn-rujuk").prop("disabled", true);
            $("#cetak-rujukan").prop("disabled", true);
         }
         else {
            $("#btn-rujuk").prop("disabled", false);
            $("#cetak-rujukan").prop("disabled", false);
            if(is_referred) {
               $("#cetak-rujukan").attr("data-target", cetakRujukUrlLab + primaryKey);
               $("#cetak-rujukan").prop("disabled", false);
            }
            else {
               $("#cetak-rujukan").prop("disabled", true);
            }
            // if (statusPenunjang == STATUS_BELUM_DISETUJUI) {
            //    $("#btn-rujuk").prop("disabled", false);
            // }
            if (statusPenunjang == constDisetujui){
               $("#btn-rujuk").prop("disabled", true);
            }
         }

         if (statusPenunjang == constDisetujui) {
            $("#btn-approve").prop("disabled", true);
            $("#btn-edit-tanggal").prop("disabled", true);
            if (jml_pemeriksaan != jml_pemeriksaan_approve) {
               $("#btn-approve").prop("disabled", false);
            }
         }
         if (statusPenunjang == constBatal) {
            $("#btn-approve").prop("disabled", true);
            $("#btn-edit-tanggal").prop("disabled", true);
            $("#btn-batal").prop("disabled", true);
         }
         if (statusPenunjang == constAmbilSample || statusPeriksa) {
            $("#btn-approve").prop("disabled", true);
            $("#btn-edit-tanggal").prop("disabled", true);
            $("#btn-batal").attr("data-options", 'click');
            $("#btn-batal").attr("data-target", "#");
            $("#btn-batal").attr("data-pesan-error", "Tidak Bisa membatalkan karena sudah mengambil sampel");
         }
         var statusBayarUpdate = false;
         if ((jumlah_bayar > 0 && jumlah_tagihan == 0) || (jumlah_bayar > 0 && jumlah_tagihan > 0)) {
            statusBayarUpdate = true;
         }
         if (statusBayarUpdate && instalasi == constInstalasiRJ && penjamin == constPenjaminPerseorangan) {
            if (jml_pemeriksaan != jml_pemeriksaan_approve) {
               $("#btn-approve").prop("disabled", false);
            } else {
               $("#btn-approve").prop("disabled", true);
               $("#btn-edit-tanggal").prop("disabled", true);
               $("#btn-batal").attr("data-options", 'click');
               $("#btn-batal").attr("data-target", "#");
               $("#btn-batal").attr("data-pesan-error", "Pasien dari instalasi Rawat Jalan dan Penjamin Perseorangan tidak bisa dibatalkan jika sudah melakukan pembayaran, harap melakukan pembatalan pembayaran.");
            }
         } else {
            if (statusPeriksa) {
               $("#btn-batal").attr("data-options", 'click');
               $("#btn-batal").attr("data-target", "#");
               if (statusBatal) {
                  $("#btn-batal").prop("disabled", true);
               } else {
                  $("#btn-batal").attr("data-pesan-error", "Tidak Bisa membatalkan karena sudah diperiksa");
               }
            }
         }
      }
   });
   // $(document).on('click', "#btn-batal", function () {
   //    if ($("#btn-batal").attr("data-options") == "click") {
   //       docoNotification('error', "Gagal Melakukan Batal", $("#btn-batal").attr("data-pesan-error"));
   //    }
   // });
   $('#tgl_rujukan-startDate').val(moment().format("DD MMM YYYY"))
   $('#tgl_rujukan-endDate').val(moment().format("DD MMM YYYY"))

   var _classRujukan = '#legend-cito'
   var _urlDataRujukan = ''

   $(_classRujukan).css('cursor', 'pointer');
   $(_classRujukan).removeClass('active')
   $(_classRujukan).on('click', function () {
      var _type = $(this).data("type");
      var tableId = "tbl-rujukan";
      var tableElement = $(`#${tableId}`).DataTable()
      
      if($('#legend-cito').hasClass('active') === true) {
         $('#legend-cito').removeClass('active');
         $('.legend-information-rujukan').css('border-color', '')
         _urlDataRujukan = '/laboratorium/inf-pasien-rujukan-lab/get-data-rujukan?'
      }
      else  {
         $('#legend-cito').addClass('active');
         $('.legend-information-rujukan').css('border-color', '#04AA6D')
         _urlDataRujukan = "/laboratorium/inf-pasien-rujukan-lab/get-data-rujukan?type=" + _type
      }
      showLoader();
      tableElement.ajax.url(_urlDataRujukan).load()
   })
   $('#btn-search__tbl-rujukan').css('display', 'none');
   $('#btn-reset__tbl-rujukan').css('display', 'none');
});
