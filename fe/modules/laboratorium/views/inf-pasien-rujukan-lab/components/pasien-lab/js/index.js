$("#btn-batal").prop("disabled", true);
$("#cetak-tagihan").prop("disabled", true);
$("#btn-ubah").prop("disabled", true);
$("#btn-speciment").prop("disabled", true);
$("#btn-hasil").prop("disabled", true);
$("#btn-cetak").prop("disabled", true);
$("#edit-pemeriksaan").prop("disabled", true);

$(document).ready(function () {
   
   var dataStatusBayar = [];
   var dataAsalRujukan = [];
   var dataStatusPeriksa = [];

   $.each(data.status_bayar, function( index, value ) {
      dataStatusBayar.push({
          id: index,
          text: value,
      });
   });

   $.each(data.asal_rujukan, function( index, value ) {
      dataAsalRujukan.push({
          id: value.asalrujukan_nama,
          text: value.asalrujukan_nama,
      });
   });

   $.each(data.status_periksa, function( index, value ) {
      dataStatusPeriksa.push({
          id: index,
          text: value,
      });
   });

   tablePasienLab = $('#table-pasien-lab').docoTabel({
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
      sorting: [
         [4, 'asc']
      ],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollY: false,
      scrollX: true,
      ajax: {
         url: baseController + "get-data-pasien-lab",
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
               var tableInfo = tablePasienLab.page.info()
               return tableInfo.start + rowAdditionalData.row + 1
            }
         },
         {
            data: 'status_bayar',
            searchable: false,
            orderable: false,
         },
         {
            data: 'status_periksa_nama',
            orderable: false,
            searchable: false,
         },
         {
            data: 'tglmasukpenunjang',
            render: (data) => {
               return data == null ? '-' : moment(data).format('DD MMM YYYY HH:mm:ss')
           },
         },
         {
            data: 'nama_pasien',
            orderable: false,
            searchable: false,
            render: (data, rowElement, rowData) => {
               return `
                   <p> <b> ${data != null ? data : ''} ${rowData.jenis_kelamin_kode != null ? "(" + rowData.jenis_kelamin_kode + ")" : '-'}</b> </p>
                   <p> <b> ${rowData.tanggal_lahir == null ? '-' : moment(rowData.tanggal_lahir).format('DD MMM YYYY')}</b> </p>
                   <p> <b> ${rowData.no_pendaftaran != null ? rowData.no_pendaftaran : ''} / ${rowData.no_rekam_medik != null ? rowData.no_rekam_medik : ''}</b> </p>
               `
           }
         },
         {
            data: 'pemeriksaan',
            orderable: false,
            searchable: false,
         },
         {
            data: 'dokter_penunjang',
            render: (data, rowElement, rowData) => {
               return `<select name="dokter" data-pasienmasukpenunjang_id="${rowData.pasienmasukpenunjang_id}" data-pasienkirimkeunitlain_id="${rowData.pasienkirimkeunitlain_id}" data-pendaftaran_id="${rowData.pendaftaran_id}" data-pasienadmisi_id="${rowData.pasienadmisi_id}"><option value="${rowData.pegawai_id}">${rowData.dokter_penunjang}</option></select>`
            }
         },
         {
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData) => {
               return `${rowData.carabayar_nama != null ? rowData.carabayar_nama : ''} / ${rowData.penjamin_nama != null ? rowData.penjamin_nama : ''}`
            }
         },
         {
            data: 'asalrujukan_nama',
            searchable: false,
            orderable: false,
         },
         {
            data: 'no_masukpenunjang',
            searchable: false,
            orderable: false,
         },
      ],
      formFilters: [
         {
            fieldName: 'tglmasukpenunjang',
            label: 'Tanggal Pendaftaran',
            type: {
               name: 'rangeDate',
            }
         },
         'no_pendaftaran',
         'no_rekam_medik',
         'nama_pasien',
         {
            fieldName: 'no_masukpenunjang',
            label: 'No Lab'
         },
         {
            fieldName: 'pegawai_id',
            label: 'Dokter',
            type: {
               name: 'dropdownScroll',
               url: baseController + "filters",
               additionalPayload: {
                  type: 'dokter',
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
            fieldName: 'is_status_bayar',
            label: 'Status Bayar',
            type: {  
               name: 'select',
               payload: dataStatusBayar
            }
         },
         {
            fieldName: 'status_periksa',
            label: 'Status Periksa',
            type: {
               name: 'select',
               payload: dataStatusPeriksa
            }
         },
         {
            fieldName: 'asalrujukan_nama',
            label: 'Asal Rujukan',
            type: {
               name: 'select',
               payload: dataAsalRujukan
            }
         },
      ],
      drawCallback: () => {
         $("select[name='dokter']").select2InfinityScroll({
               url: '/laboratorium/inf-pasien-rujukan-lab/get-dokter-lab'
         });
         $("select[name='dokter']").on('change', ({ currentTarget }) => {
            if ($(currentTarget).val() != '') {
               const { pasienmasukpenunjang_id, pasienkirimkeunitlain_id, pendaftaran_id, pasienadmisi_id } = $(currentTarget).data()
               $.ajax({
                  url: '/laboratorium/inf-pasien-rujukan-lab/update-dokter-lab',
                  method: 'POST',
                  contentType: 'application/json',
                  data: JSON.stringify({
                        pasienmasukpenunjang_id,
                        pasienkirimkeunitlain_id,
                        pendaftaran_id,
                        pasienadmisi_id,
                        pegawai_id: $(currentTarget).val()
                  }),
                  success: function (response) {
                     if (response.meta.code == 200) {
                        docoNotification("success", "Proses Berhasil!", response.data.message);
                        tablePasienLab.ajax.reload();
                     }
                  },
               })
            }
         });
      },
      rowCallback: function (row, data) {
         if (data.is_cyto == true) {
            $('td:eq(4)', row).css({ "background-color": "rgb(255,137,0)", "color": "#ffffff" });
         }

         if (data.carabayar_kode_warna != null) {
            $($(row).find('td')[8]).css('background-color', data.carabayar_kode_warna);
            $($(row).find('td')[8]).css('color', invertColor(data.carabayar_kode_warna, true));
         }
         // if (data.is_referred == true) {
         //    $('td:eq(6)', row).css({ "background-color": "#FFC300", "color": "#000000" });
         // }
      }
   });
   $('#btn-search__table-pasien-lab').css('display', 'none');
   $('#btn-reset__table-pasien-lab').css('display', 'none');
});

$(document).on("click", ".btn-reset-pasien-lab", function () {
   const tableId = "table-pasien-lab";
   const element = $(`#filter-section__${tableId}`)
   const formWrapper = $(`#form-filter__${tableId}`)
   element.find('input').val('')
   element.find('select').val(null).trigger('change')
   element.find('#tgl_pendaftaran-startDate').val(moment().add(-1, 'months').format("DD-MMM-YYYY")).trigger("change");
   element.find('#tgl_pendaftaran-endDate').val(moment().add(1, 'months').format("DD-MMM-YYYY")).trigger("change");
   const tableElement = $(`#${tableId}`).DataTable()
   showLoader()
   tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
   tableElement.ajax.url(baseController + "get-data-pasien-lab").load()

   $("#btn-approve").prop("disabled", true);
   $("#btn-batal").prop("disabled", true);
   $("#cetak-tagihan").prop("disabled", true);
   $("#btn-ubah").prop("disabled", true);
   $("#btn-speciment").prop("disabled", true);
   $("#btn-hasil").prop("disabled", true);
   $("#btn-cetak").prop("disabled", true);
   $("#edit-pemeriksaan").prop("disabled", true);
});

$(document).on("click", "#table-pasien-lab tbody tr", function () {
   try {
      primaryKey = tablePasienLab.row(".selected").data().primary ? tablePasienLab.row(".selected").data().primary : null;
      statusPeriksa = tablePasienLab.row(".selected").data().status_periksa ? tablePasienLab.row(".selected").data().status_periksa : null;
      caraBayar = tablePasienLab.row(".selected").data().carabayar_nama ? tablePasienLab.row(".selected").data().carabayar_nama : null;
      isBayar = tablePasienLab.row(".selected").data().is_status_bayar ? tablePasienLab.row(".selected").data().is_status_bayar : null;
      statusBayar = tablePasienLab.row(".selected").data().status_bayar ? tablePasienLab.row(".selected").data().status_bayar : null;
      pegawai_id = tablePasienLab.row(".selected").data().pegawai_id ? tablePasienLab.row(".selected").data().pegawai_id : null;
      pasienmasukpenunjang_id = tablePasienLab.row(".selected").data().pasienmasukpenunjang_id ? tablePasienLab.row(".selected").data().pasienmasukpenunjang_id : null;
      pendaftaran_id = tablePasienLab.row(".selected").data().pendaftaran_id ? tablePasienLab.row(".selected").data().pendaftaran_id : null;
      noRegis = tablePasienLab.row(".selected").data().no_pendaftaran ? tablePasienLab.row(".selected").data().no_pendaftaran : null;
      is_referred = tablePasienLab.row(".selected").data().is_referred ? tablePasienLab.row(".selected").data().is_referred : false;
   } catch (e) {
      primaryKey = false;
   }

   if ($('#table-pasien-lab tr.selected').length == 0) {
      $("#btn-batal").prop("disabled", true);
      $("#cetak-tagihan").prop("disabled", true);
      $("#btn-ubah").prop("disabled", true);
      $("#btn-speciment").prop("disabled", true);
      $("#btn-hasil").prop("disabled", true);
      $("#btn-cetak").prop("disabled", true);
      $("#edit-pemeriksaan").prop("disabled", true);
   } 
   else {
      $("#cetak-tagihan").prop("disabled", false);
      $("#btn-ubah").prop("disabled", false);
      $("#btn-speciment").prop("disabled", false);
      $("#btn-hasil").prop("disabled", false);
      $("#btn-cetak").prop("disabled", false);
      if(is_referred) {
         $("#cetak-rujukan-tab-lab").attr("data-target", cetakRujukUrlLab + primaryKey);
         $("#cetak-rujukan-tab-lab").prop("disabled", false);
      }
      else {
         $("#cetak-rujukan-tab-lab").prop("disabled", true);
      }

      if (statusPeriksa == 477) { // belum periksa
         $("#btn-hasil").prop("disabled", false);
         $("#btn-cetak").prop("disabled", true);
      }

      if (statusPeriksa == 474) { //ambil sample
         $("#btn-ubah").prop("disabled", true);
      }

      if (statusPeriksa == 473) { //periksa
         $("#btn-ubah").prop("disabled", true);
      }

      if (!isBayar && statusPeriksa == 477) { 
         $("#edit-pemeriksaan").prop("disabled", false);
         $("#btn-batal").prop("disabled", false);
      } else {
         $("#edit-pemeriksaan").prop("disabled", true);
         $("#btn-batal").prop("disabled", true);
      }
      
      /*       
      if (instalasiId == 21) { 	
         $("#edit-pemeriksaan").prop("disabled", true);	
         $("#btn-batal").prop("disabled", true);	
      } else {	
         $("#edit-pemeriksaan").prop("disabled", false);	
         $("#btn-batal").prop("disabled", false);	
      } 
      */	

      if (is_exception) {	
         if (typeof(cetakException) === 'number' && statusPeriksa != 477 && cetakException) {	
            $("#btn-cetak").prop("disabled", false);	
         } else {	
            $("#btn-cetak").prop("disabled", true);	
         }
      }

      $("#btn-riwayat-pasien-lab").prop("disabled", false);
      $("#btn-riwayat-pasien-lab").attr("action", '/igd/riwayat-pasien/history-patient?norm='+no_rekam_medik+'&instalasi='+_instalasiId+'&modal=is_modal', true);
   }
});

$(".btn-search--datatable").on('click', function () {
   $("#btn-batal").prop("disabled", true);
   $("#cetak-tagihan").prop("disabled", true);
   $("#btn-ubah").prop("disabled", true);
   $("#btn-speciment").prop("disabled", true);
   $("#btn-hasil").prop("disabled", true);
   $("#btn-cetak").prop("disabled", true);
   $("#edit-pemeriksaan").prop("disabled", true);
});

$("#edit-pemeriksaan").click(function () {
   if (!isBayar && statusPeriksa == 477) {
      $("#edit-pemeriksaan").attr("data-url", baseController + "form-edit-pemeriksaan?noRegis=" + noRegis + "&id=");
   } else {
      docoNotification('error', 'Terjadi kesalahan pada input.', 'Pemeriksaan ini tidak bisa diubah!')
      return false;
   }
});


// $(.legend-information).css('cursor', 'pointer'); // diaktifkan ketika semua legend digunakan filter
// $(.legend-information).removeClass('active');
$(".legend-information").on('click', function () {
   let _table_ajax_url = '';
   if ($(this).hasClass('active') === true) {
      $(this).removeClass('active');
      $(this).css('border-color', '#dddddd');
      _table_ajax_url = baseController + "get-data-pasien-lab";
      showLoader()
      tablePasienLab.ajax.url(_table_ajax_url).load()
   } else {
      let _dt_type = $(this).data("type")
      switch (_dt_type) {
         case 'penunjang_has_dirujuk':
            // sementara dibungkus disini karena hanya 1 legend yang bisa dipake filter
            $('.legend-information').removeClass('active');
            $('.legend-information').css('border-color', '#dddddd');
            $(this).addClass('active');
            $(this).css('border-color', '#04aa6d');
            _table_ajax_url = baseController + "get-data-pasien-lab?is_referred=true"
            showLoader()
            tablePasienLab.ajax.url(_table_ajax_url).load()
            break;
         default:
            // do nothing
            break;
      }
      // bisa diimprove lagi
   }
});

$(document).on("click", ".antrian", function (event) {
   const id_pendaftaran = $(this).attr("data-id");
   const no_antrian = $(this).attr("data-antrian");
   const dataPost = {
      no_antrian: no_antrian
   };
   $.ajax({
      url: baseController + "panggil-antrian",
      data: dataPost,
      type: "post",
      success: function (res) {
         if (typeof res.teks_panggil !== "undefined") {
            let text = res.teks_panggil;
            let player = $("#playerAudio");
            let arrayText = text.split(" ");
            arrayText.push("stop");
            arrayText = arrayText.filter(Boolean);
            let index = 0;
            player[0].defaultPlaybackRate = 1;
            player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
            player[0].play();
            player[0].addEventListener("ended", function () {
               index = index + 1;
               if (index < arrayText.length) {
                  player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
                  if (arrayText[index] == "stop") {
                  } else {
                     player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".mp3";
                     player[0].play();
                  }
               }
            });
         }
      }
   });
});

