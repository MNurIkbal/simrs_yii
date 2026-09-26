$(document).ready(function () {
   $('#btn-kembali').on('click', function () {
      $(location).attr('href', redirectUrl);
   });

   table = $("#example").docoTabel({
      filter: false,
      sorting: [[1, "asc"]],
      paging: false,
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: true,
      ajax: baseUrl + "radiologi/inf-pasien-rujukan-rad/get-data-order-pemeriksaan?pasienkirimkeunitlain_id=" + pasienKirimKeUnitLainId,
      columns: [
         {
            title: "No",
            data: "rowNum",
            searchable: false,
            orderable: false
         },
         {
            title: "Jenis Pemeriksaan",
            data: "jenispemeriksaanrad_nama",
            searchable: false,
         },
         {
            title: "Nama Pemeriksaan",
            data: "daftartindakan_nama",
            searchable: false,
         },
         {
            title: "Diagnosa",
            data: "diagnosa",
            searchable: false,
         },
         {
            title: "Qty",
            data: "qtypermintaan",
            searchable: false,
         },
         {
            title: "Cyto",
            data: "is_cyto",
            searchable: false,
            orderable: false
         },
         {
            title: "Dokter Perujuk",
            data: "dokter",
            searchable: false,
            orderable: false
         },
         {
            title: "Dirujuk",
            data: "dirujuk",
            searchable: false,
            orderable: false
         },
      ],
      drawCallback: function(settings) {
         $("select[name=\"dokter\"]").select2InfinityScroll({
            url: '/radiologi/inf-pasien-rujukan-rad/dokter-list'
         })
         $(".diagnosa").select2InfinityScroll({
            url: "/radiologi/inf-pasien-rujukan-rad/filters?type=diagnosa",
            callbackData: (param) => {
               return {
                  payload: {
                     ...param,
                  }
               }
            }
         })

         var dataDokter = {
            id: _dokterRujukId,
            text: _dokterRujukNama
         };
         var dataDiagnosa = {
            id: _diagnosaUtamaId,
            text: _diagnosaUtamaNama
         };
         var newOption = new Option(dataDokter.text, dataDokter.id, true, true);
         var optionDiagnosa = new Option(dataDiagnosa.text, dataDiagnosa.id, true, true);

         $('select[name=\"dokter\"]').append(newOption).trigger('change');
         $('.diagnosa').append(optionDiagnosa).trigger('change');
      }
   });

   $('#btn-simpan').on('click', function () {
      var _form = $("#form-rujuk").serializeArray();
      const detail = []
      let valid = 1
      var arrPemeriksaan = [];
      $(".rujukCheck:checked").each(function () {
         var daftartindakanId = $(this).attr("data-value");
         arrPemeriksaan.push(daftartindakanId);
      })
      var countChecked = arrPemeriksaan.length;
      if (countChecked == 0) {
         valid = 0
         docoNotification('error', 'Terjadi kesalahan pada input.', 'Tidak ada Pemeriksaan yang akan di Rujuk!')
      }
      _form.push({
         name: "pasienkirimkeunitlain_id",
         value: $('#pasien-kirim-unit-lain-id').val()
      });
      $('#example tbody tr').each((index, element) => {
         $(element).find('.rujukCheck:checked').each(function () {
            var _diagnosa = $(element).find('select[name="diagnosa"]').select2('data');
            _diagnosa = _diagnosa[0].text;
            _detail = {
               permintaankepenunjang_id: $(this).data('idpenunjang'),
               pasienkirimkeunitlain_id: $(this).data('idunitlain'),
               dokter_id: $(element).find('select[name="dokter"]').val(),
               nama_tindakan: $(this).data('namatindakan'),
               daftartindakan_id: $(this).data('value'),
               diagnosa: $(element).find('select[name="diagnosa"]').val(),
               diagnosa_nama: _diagnosa,
            }
            _form.push({
               name: "detail[]",
               value: JSON.stringify(_detail)
            });
            if ($(element).find('select[name="dokter"]').val() == null) {
               valid = 0
               docoNotification('error', 'Terjadi kesalahan pada input.', 'Dokter Pemeriksaan <strong><i>' + $(element).data('namatindakan') + '</i></strong> Belum Dipilih')
            }
            if ($(element).find('select[name="diagnosa"]').val() == null) {
               valid = 0
               docoNotification('error', 'Terjadi kesalahan pada input.', 'Diagnosa Pemeriksaan <strong><i>' + $(element).data('namatindakan') + '</i></strong> Belum Dipilih')
            }
         });
      })
      
      if (valid == 1) {
         $(this).docoForm('click', {
            url: "/radiologi/inf-pasien-rujukan-rad/rujuk?instalasi_id=" + _instalasi + '&id=' + pasienKirimKeUnitLainId,
            method: "POST",
            type: "json",
            data: _form,
            success: function (data) {
               var _res = data.response;
               $("#cetak-rujukan").attr("data-target", cetakRujukUrl + _res.rujukankeluar_id);
               setTimeout(() => {
                  $("#form-rujuk")[0].reset();
                  $('#rujukanpenunjangform-rs_tujuan').val(null).trigger('change')
                  $('#rujukanpenunjangform-pegawai_menyetujui').val(null).trigger('change')
                  table.draw();
                  $('#btn-simpan').prop("disabled", true)
                  $('#cetak-rujukan').prop("disabled", false);
               }, 1000);
            }
         });
      }
   });
   $(".rs_tujuan").select2InfinityScroll({
      url: "/radiologi/inf-pasien-rujukan-rad/filters?type=rs_tujuan",
      callbackData: (param) => {
         return {
            payload: {
               ...param,
            }
         }
      }
   })
   $(".pegawai_menyetujui").select2InfinityScroll({
      url: "/radiologi/inf-pasien-rujukan-rad/filters?type=pegawai_menyetujui",
      callbackData: (param) => {
         return {
            payload: {
               ...param,
            }
         }
      }
   })
});