$(document).ready(() => {
   let _table = $('#table-verifikasi');

   if ($('.btn-save-post').length) {
      $('.btn-save-post').remove()
   }
   
   setTimeout(function () {
      $(document).find('.unhide-trigger').trigger('change')
   }, 50)

   if (_statusPeriksa == 482) {
      $('.stepy-navigator').addClass('hidden')
      $('.stepy-navigator .button-back').addClass('hidden')
   }
   else {
      $('.stepy-navigator').removeClass('hidden')
      $('.stepy-navigator .button-back').removeClass('hidden')
   }

   if (_isStopAkomodasi == true) {
      $('.btn-batal-verifikasi').attr('disabled',true)
      $('.button-batal-verifikasi').attr('data-toggle','tooltip').attr('data-placement','top').attr('title','Pasien sudah melakukan stop akomodasi').attr('style', 'curson: no-drop !important');
   }

   $('.edit-intra-operasi').on('click', () => {
      $('#tab-operasi').stepy('step', 1)
   })

   tableVerif = $("#table-verifikasi").docoTabel({
      filter: false,
      ordering: false,
      order: [[1, 'asc']],
      info: false,
      // columnDefs: [ {
      //    targets: [ 1 ],
      //    visible: false
      // }],
      paging: false,
      processing: true,
      serverSide: true,
      scrollX: true,
      ajax: baseUrl+"bedah/informasi-pasien-operasi/get-data-verifikasi?pasienmasukpenunjang_id=" + pasienpenunjangId,
      columns: [
         {
            title: "No",
            data: "rowNum",
            orderable: false,
         },
         {
            data: "daftartindakan_nama",
            searchable: false,
            orderable: false
         },
         {
            data: "kegiatanoperasi_nama",
            searchable: false,
            orderable: false
         },
         {
            data: "golonganoperasi_nama",
            searchable: false,
            orderable: false
         },
         {
            data: "nama_pegawai",
            searchable: false,
            orderable: false
         },
         {
            data: "posisi_operasi",
            searchable: false,
            orderable: false
         },
         {
            data: "input_qty",
            orderable: false,
            className: "text-right",
         },
         {
            data: "harga",
            className: "text-right",
            orderable: false,
            render: $.fn.dataTable.render.number( ".", ",", 0, "" )
         },
         {
            data: "input_cyto",
            searchable: false,
            orderable: false,
            className: "text-center",
         },
         {
            data: "input_penyulit",
            searchable: false,
            orderable: false,
            className: "text-center",
         },
         {
            data: "input_persentase",
            searchable: false,
            orderable: false,
            className: "text-right",
         },
         {
            data: "total_harga",
            className: "text-right",
            orderable: false,
            render: $.fn.dataTable.render.number( ".", ",", 0, "" )
         },
         {
            data: "pegawai_input",
            searchable: false,
            orderable: false
         },
      ],
      drawCallback: function(settings) {
         var api = this.api();
         var rows = api.rows( {page:'current'} ).nodes();
         var last = null;
         api.column(1, {page:'current'} ).data().each( function ( name, i ) {
            let row = $(rows).eq( i );
            let kegiatan = row.find('td:eq(2)').text();

            // if(kegiatan != "-" && i == 3) {
            //    $(rows).eq( i ).after(
            //       '<tr class="group" style="background-color:#FCF3CF;font-weight:bold;"><td colspan="13">TINDAKAN DI LUAR OPERASI</td></tr>'
            //    );
            // }
         });
      },
      footerCallback: function(row, data, start, end, display) {
         var api = this.api(), data;
         var intVal = function ( i ) {
            return typeof i === "string" ?
            i.replace(/[\$,]/g, "")*1 :
            typeof i === "number" ?
               i : 0;
         };

         subTotal = api
            .column( 11, { page: "current"} )
            .data()
            .reduce( function (a, b) {
               return intVal(a) + intVal(b);
            }, 0 );
         
         $(".total_tagihan").html("Rp. " + docoHelper.convertToRupiah(subTotal));
      },
      initComplete: function( settings, json ) {
         if(tableVerif.data().count() == 0){
            $('.submit-verifikasi').attr('disabled', true);
            $('.btn-batal-verifikasi').attr('disabled', true);
            $('.btn-batal-verifikasi').remove();
         }else{
            $('.submit-verifikasi').attr('disabled', false);
         }
       }
   });

   

   $(".dataTables_filter").hide();

   $('.submit-verifikasi').on('click', () => {
      if(tableVerif.data().count() == 0){
         new PNotify({
            title: 'Proses Gagal.',
            text: 'Data belum tersedia.',
            addclass: 'alert alert-success alert-arrow-right alert-styled-right',
            type: 'error',
          });
      }else{
         $().docoForm('click', {
            url: '/bedah/informasi-pasien-operasi/simpan-verifikasi?id=' + $('#penunjang-id').val(),
            skipSuccessNotif: false,
            method: 'post',
            beforeSend: () => {
               $('.submit-verifikasi').attr('disabled', true)
            },
            success: function (response) {
               // $('#tab-operasi').stepy('step', '4');
               $('.submit-verifikasi').remove();
               localStorage.removeItem('bills');
               location.reload();
            },
            error: () => {
               $('.submit-verifikasi').attr('disabled', false)
            }
         });
      }
   })

   generateTarif = function(type,id,val,tindakan_id,penunjang_id) {
      var _params = `id=${id}&tindakan_id=${tindakan_id}&penunjang_id=${penunjang_id}&type=${type}`;
      if(type == 'qty') {
         var _element = $('#input-qty-' + id)
         var _value = _element.val();
         var _payload = `${_params}&qty=${_value}`;
         if(_value <= 0) {
            docoNotification('error', "Peringatan!", "Qty tidak boleh kurang dari atau sama dengan 0");
            _element.val(val);
            return false;
         }
      }
      else if(type == 'persentase') {
         var _element = $('#input-persentase-' + id)
         var _value = _element.val();
         var _payload = `${_params}&persentase=${_value}`;
         if(_value <= 0) {
            docoNotification('error', "Peringatan!", "Persentase tidak boleh kurang dari atau sama dengan 0");
            _element.val(val);
            return false;
         }
         if(_value > 100) {
            docoNotification('error', "Peringatan!", "Persentase maksimal 100%");
            _element.val(val);
            return false;
         }
      }
      else if(type == 'cyto') {
         var _element = $('#input-cyto-' + id)
         var _value = (_element.is(':checked')) ? 1 : 0;
         var _payload = `${_params}&is_cyto=${_value}`;
      }
      else if(type == 'penyulit') {
         var _element = $('#input-penyulit-' + id)
         var _value = (_element.is(':checked')) ? 1 : 0;
         var _payload = `${_params}&is_penyulit=${_value}`;
      }

      $.ajax({
         url: '/bedah/informasi-pasien-operasi/edit-verifikasi?' + _payload,
         method: 'GET',
         success: function(res) {
            tableVerif.draw();
         }
      });
   }

   // $('.checkHarga').on('change', function () {
   //    let _id = $(this).data('id');
   //    let index = $(this).data('index');
   //    let bills = JSON.parse(localStorage.getItem("bills"));
   //    let bills_origin = JSON.parse(localStorage.getItem("bills_origin"));
   //    let harga_persentase = parseFloat(bills[index]['harga_persentase']);
   //    let total_harga;
   //    let _harga_persentase = harga_persentase;
   //    if (harga_persentase > parseFloat(bills[index]['total_harga_real'])) {
   //       _harga_persentase = -harga_persentase;
   //    }
   //    if ($(this).prop('checked') == false) {
   //       bills[index]['total_harga'] = bills[index]['harga_persentase'];
   //       bills[index]['harga'] = harga_persentase;
   //       bills[index]['harga_cyto'] = 0;
   //       bills[index]['harga_penyulit'] = 0;
   //       bills[index]['persen_penyulit'] = 0;
   //       bills[index]['persencyto_tindakan'] = 0;
   //       localStorage.removeItem('bills');
   //       localStorage.setItem("bills", JSON.stringify(bills));
   //       _total_tagihan = _total_tagihan - _harga_persentase;
   //       $('#total-harga-' + _id).html(`
   //       Rp. `+ docoHelper.convertToRupiah(harga_persentase) + `
   //       `)
   //       $('.total_tagihan').html(`
   //       Total Tagihan : Rp. `+ docoHelper.convertToRupiah(_total_tagihan) + `
   //       `)
   //    } else {
   //       bills[index] = bills_origin[index]
   //       total_harga = bills[index]['total_harga'];
   //       localStorage.removeItem('bills');
   //       localStorage.setItem("bills", JSON.stringify(bills));
   //       _total_tagihan = _total_tagihan + _harga_persentase;
   //       $('#total-harga-' + _id).html(`
   //             Rp. `+ docoHelper.convertToRupiah(total_harga) + `
   //       `)
   //       $('.total_tagihan').html(`
   //       Total Tagihan : Rp. `+ docoHelper.convertToRupiah(_total_tagihan) + `
   //       `)
   //    }
   // });

   // $('.is_cyto').on('change', function(){
   //    let _timoperasiId = $(this).data('timoperasi_id');
   //    let _harga = $(this).data('harga');
   //    let index = $(this).data('index');
   //    let bills = JSON.parse(localStorage.getItem("bills"));
   //    let bills_origin = JSON.parse(localStorage.getItem("bills_origin"));
   //    let persencyto_tindakan = parseFloat(bills[index]['persencyto_tindakan']);
   //    let persen_penyulit = parseFloat(bills[index]['persen_penyulit']);
   //    let persencyto_harga = 0;
   //    let persenpenyulit_harga = 0;
   //    let totalHarga = 0;
   //    if($(this).is(':checked')) {
   //       persencyto_harga = (persencyto_tindakan/100) * _harga;
   //       totalHarga = parseFloat(_harga + persencyto_harga + persenpenyulit_harga);
   //    }
   //    else {
   //       persencyto_harga = 0;
   //       totalHarga = _harga
   //    }
   //    $('#totalHarga-' + index).val(docoHelper.convertToRupiah(totalHarga)).trigger('change')
   // })
})