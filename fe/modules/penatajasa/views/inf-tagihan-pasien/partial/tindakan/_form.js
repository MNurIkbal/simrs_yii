var qty=0;

$(document).on('click','.delete-cache', function(event) {
   event.preventDefault();
   var id = $(this).data('id');
   var action = $(this).data('action');
   var button = this;
   var valButton = $(button).html();
   var ResData = {};

   $(this).docoForm('click',{
      url: action,
      data: ResData,
      skipConfirm : true,
      method: 'GET',
      before: function () {
         $(button).html("<i class=\"fa fa-spin fa-spinner\"></i>");
         $(button).prop("disabled", true);
     },
      success: function (res) {
         qty=res?.response?.qty;
         if(table_tindakan){
            table_tindakan.draw();
         }
         table_bmhp.draw();
         $(button).parent().parent().remove();
      }
   });
});

$(document).on('click','.delete-cache-tindakan', function(event) {
   event.preventDefault();
   var id = $(this).data('id');
   var action = $(this).data('action');
   var button = this;
   var valButton = $(button).html();
   var ResData = {};

   $(this).docoForm('click',{
      url: action,
      data: ResData,
      skipConfirm : true,
      method: 'GET',
      before: function () {
         $(button).html("<i class=\"fa fa-spin fa-spinner\"></i>");
         $(button).prop("disabled", true);
     },
      success: function (res) {
         qty=res?.response?.qty;
         if(table_bmhp){
            table_bmhp.draw();
         }
         table_tindakan.draw();
         $(button).parent().parent().remove();
      }
   });
});

$(document).ready(function () {
   $("#jenis_pelayanan_form").val('tindakan');
   $("#obatalkes_id").select2();
   $('#div_paket').hide()
   $('#div_pelayanan').hide()
   $('.div_kamar_ruangan').hide()
   $('.div_tempat_tidur').hide()
   $('#is_half_akomodasi').hide()
   $("#jenis_pelayanan").focus();
   $(".field-kamarruangan_id").find('label').after('<label" style="color:red"> * </label">')
   $(".field-kamartempattidur_id").find('label').after('<label" style="color:red"> * </label">')
   akomodasi_checkbox = $('.field-tindakan_paket_id').find('.input-group-addon')
   akomodasi_checkbox.append('<label><input type="checkbox" id="is_akomodasi" name="akomodasi" value="1" '+ _is_pasien_ri +'> Akomodasi</label>')

   table_bmhp = $("#table-list-bmhp").docoTabel({
      filter: false,
      paging: false,
      info: false,
      select: {
         style:    "os",
         selector: "tr"
      },
      processing: true,
      serverSide: true,
      // scrollY:"50vh",
      scrollCollapse: true,
      language: {
            emptyTable: "Belum Ada Data."
      },
      ajax: baseUrl+"penatajasa/inf-tagihan-pasien/get-list-obat?"+_id,
      columns: [
         {
            title: "No",
            data: "rowNum",
            searchable: false,
            orderable: false,
            width: '13%'
         },
         {
            title: "Tindakan",
            data: "daftartindakan_nama",
            searchable: false,
            orderable: false,
            width: '20%'
         },
         {
            title: "Depo",
            data: "ruangan_nama",
            searchable: false,
            orderable: false,
            width: '13%'
         },
         {
            title: "BMHP",
            data: "nama_bmhp",
            searchable: false,
            orderable: false,
            width: '20%'
         },
         {
            title: "Qty",
            data: "qty_obat",
            searchable: false,
            orderable: false,
            width: '10%'
         },
         {
            title: "Satuan",
            data: "nama_satuan",
            searchable: false,
            orderable: false,
            width: '13%',
            className : 'text-center',
         },
         {
            title: "Harga Satuan",
            data: "satuan_bmhp",
            searchable: false,
            orderable: false,
            width: '13%',
            className : 'text-right',
         },
         {
            title: "Ditagihkan",
            data: "is_ditagihkan",
            searchable: false,
            orderable: false,
            width: '13%',
            className : 'text-right',
         },
         {
            title: "Subtotal",
            data: "subtotal",
            searchable: false,
            orderable: false,
            width: '13%',
            className : 'text-right',
         },
         {
            title: "Aksi",
            data: "aksi",
            searchable: false,
            orderable: false,
            width: '8%',
            className : 'text-right'
         }
      ],
      drawCallback : function (settings) {
         if(qty == 20){
            $('#simpan-obat-bmhp').prop("disabled",true).attr("title", "Tidak dapat menambahkan lebih dari 20 item pada saat bersamaan");
            $('#tambah-tindakan').prop("disabled",true).attr("title", "Tidak dapat menambahkan lebih dari 20 item pada saat bersamaan");            
         }else{
            $('#simpan-obat-bmhp').prop("disabled",false).attr("title", "");
            $('#tambah-tindakan').prop("disabled",false).attr("title", "");

         }       
         $("#table-list-bmhp_wrapper thead").remove()
      },
      createdRow: function(row, data, dataIndex){
         if (data.is_available == 0) {
             $(row).css("background-color", "#f79494").css("color", "white");
         }
     },
   });
   table_tindakan = $("#table-tindakan").docoTabel({
      filter: false,
      paging: false,
      info: false,
      select: {
         style:    "os",
         selector: "tr"
      },
      processing: true,
      serverSide: true,
      scrollCollapse: true,
      language: {
            emptyTable: "Belum Ada Data."
      },
      ajax: baseUrl+"penatajasa/inf-tagihan-pasien/get-cache-tindakan",
      columns: [
         {
            title: "No",
            data: "rowNum",
            searchable: false,
            orderable: false,
         },
         {
            title: "Tanggal Tindakan",
            data: "tgl_transaksi",
            searchable: false,
            orderable: false,
         },
         {
            title: "Nama Tindakan/Paket/Akomodasi",
            data: "tindakan_nama",
            searchable: false,
            orderable: false,
         },
         {
            title: "Jumlah Tindakan",
            data: "qty",
            searchable: false,
            orderable: false,
         },
         {
            title: "Cito",
            data: "is_cyto",
            searchable: false,
            orderable: false,
            className : 'text-center',
         },
         {
            title: "Penyulit",
            data: "is_penyulit",
            searchable: false,
            orderable: false,
            className : 'text-right',
         },
         {
            title: "Aksi",
            data: "aksi",
            searchable: false,
            orderable: false,
            className : 'text-right',
         }
      ],
      drawCallback : function (settings) {
         if(qty == 20){
            $('#tambah-tindakan').prop("disabled",true).attr("title", "Tidak dapat menambahkan lebih dari 20 item pada saat bersamaan");
            $('#simpan-obat-bmhp').prop("disabled",true).attr("title", "Tidak dapat menambahkan lebih dari 20 item pada saat bersamaan");            
         }else{
            $('#tambah-tindakan').prop("disabled",false).attr("title", "");
            $('#simpan-obat-bmhp').prop("disabled",false).attr("title", "");
         }      
      },
   });

   $('#obatalkes_id').docoPaginationSelec2({
      _api: baseUrl+"penatajasa/inf-tagihan-pasien/get-bmhp",
      placeholder: "— Pilih —",
      minimumInputLength: 0,
      ajax : {
         dataType: 'json',
         quietMillis: 250,
         data: function(params) {
            depo_id = $("#depo_id").val();
            if(depo_id != ''){
               ruangan_id = depo_id;
            }else{
               ruangan_id = $('#ruangan_id').val();
            }
            params.ruangan_id = ruangan_id;
            params.id_pendaftaran = id_pendaftaran;
            params.instalasi_id = instalasi_id;
            params.penjamin_id = penjamin_id;
            params.kelaspelayanan_id = kelaspelayanan_id;
            var query = {
               search: params,
            }
            return params;
         },
         processResults: function (data) {
            return {
               results: data.result
            }
         },
      },
      cache: false
   });

   $('#obatalkes_id').on('change', function (event) {

      var data = $("#obatalkes_id option:selected").data()
      var selected = data.data || {}
      var obatalkes_id = selected.id
      var resData = {
         res : obatalkes_id,
      }
      if (obatalkes_id != null && obatalkes_id != ''){
         $().docoForm('click',{
            url :'/penatajasa/end-point/get-satuan-bmhp',
            data : resData,
            skipSuccessNotif : true,
            skipConfirm : true,
            success : function (res) {
               $('#satuan_bmhp').text(res.name);
               $('#satuan_bmhp').val(res.id);
            }
         });
      }

      $('#stok_obat').text(selected.qty_tersedia)
      $('#satuan_obat').text(docoHelper.convertToRupiah(selected.hargaygdipakai))
      $('#id_obat').val(selected.id)
      $('#nama_obat').val(selected.text)
      $('#is_ditagihkan').prop("checked",false)
      $('#satuan_id').val();
      $('#subtotal_obat').text("-");
      $('#satuan_bmhp').text("-");
      $('#qty_obat').val(1);
   })

   $('#is_paket').on('change', function(e){
      if($(this).is(':checked')) {
         $("#jenis_pelayanan_form").val('paket');
         closeFormAkomodasi()
      }
      else {
         $("#jenis_pelayanan_form").val('tindakan');
      }

      $('#tindakan_paket_id').val("").trigger("change")
      $('#is_cyto').prop("checked",false)
      $('#is_penyulit').prop("checked",false)
      $('#harga_satuan').val('').trigger('change')
      $('#tindakan_qty').val('').trigger('change')
      $('#total').val('').trigger('change')
   });

   $('#is_akomodasi').on('change', function(e){
      if($(this).is(':checked')){
         $('.div_kamar_ruangan').show()
         $('.div_tempat_tidur').show()
         $('#is_half_akomodasi').show()
         $('#jenis_pelayanan_form').val("akomodasi")
         $('#is_cyto').prop("disabled",true)
         $('#is_penyulit').prop("disabled",true)
         $('#is_cyto').prop("checked",false)
         $('#is_penyulit').prop("checked",false)
         $('#is_paket').prop("checked",false)
         $('#tindakan_paket_id').prop('readonly',true);
         $('#is_half').prop('checked',false)
         var akomodasiOption = new Option(_daftartindakan_nama_akomodasi, null, true, true);
         $('#tindakan_paket_id').append(akomodasiOption)
         $('#tindakan_paket_id').prop("disabled",true)
         $('#tindakan_qty').val(1)
         $('#tindakan_qty').prop('readonly',true)
         $('#qty').val(1)
         $('#total').val(0)
         $('#harga_satuan').val(null)
         $('#daftartindakan_id').val(_daftartindakan_akomodasi)
         $('#tindakan_pelayanan_id').val('akomodasi')
         $('#tindakan_qty').prop('disabled',false)
      } else {
         closeFormAkomodasi()
      }
   })

   $('#kamarruangan_id').on('change', function(e){
      $('#harga_satuan').val(null)
      $('#total').val(0)
   })

   $('#kamartempattidur_id').on('change',function(e){
      kamar_ruangan = $('#kamarruangan_id').val()
      tempat_tidur =  $('#kamartempattidur_id').val()
      penjamin_id =  $('#penjamin_id').val()
      ruangan_id =  $('#ruangan_id').val()
      kelas_pelayanan =  $('#kelas_pelayanan').val()
      _tindakanId = _daftartindakan_akomodasi;

      if(kamar_ruangan != null && tempat_tidur != null && ruangan_id != null){
        // Editable Qty
        //  $('#tindakan_qty').val(1)
        //  $('#tindakan_qty').prop('readonly',true)

         var _params = `daftartindakan_id=${_tindakanId}&ruangan_id=${ruangan_id}&penjamin_id=${penjamin_id}&kelaspelayanan_id=${kelas_pelayanan}&kamarruangan_id=${kamar_ruangan}&kamartempattidur_id=${tempat_tidur}&is_akomodasi=1`

         if(tempat_tidur != null && tempat_tidur != '' && typeof tempat_tidur != 'undefined' ){
            $().docoForm('click',{
               url: `/penatajasa/inf-tagihan-pasien/get-tarif?${_params}`,
               skipSuccessNotif : true,
               skipConfirm : true,
               success : function (res) {
                  if(res.harga_tariftindakan != null && typeof res.harga_tariftindakan != 'undefined'){
                     $('#harga_satuan').val(docoHelper.convertToRupiah(res.harga_tariftindakan))
                     sub_total_akomodasi = $('#is_half').is(':checked') ? 0.5* res.harga_tariftindakan : res.harga_tariftindakan
                     if($('#is_half').is(':checked')){
                        $('#tindakan_qty').val(0.5)
                     } else {
                      $('#tindakan_qty').val(1)
                     }
                     $('#total').val(docoHelper.convertToRupiah(sub_total_akomodasi))
                     $('#tindakan_akomodasi').val(res.daftartindakan_nama)
                     $('#harga_satuan_origin').val(res.harga_tariftindakan)
                     isChangePrice = false
                  } else {
                     docoNotification("error","Proses Gagal","Tarif kamar tidak ditemukan")
                  }
               }
            });
         }
      }
   })

   $('#is_half').on('change',function(e){
      if($(this).is(':checked')){
         $('#tindakan_qty').val(0.5)
         sub_total_akomodasi = 0.5 * docoHelper.convertToAngka($('#harga_satuan').val())
         $('#total').val(docoHelper.convertToRupiah(sub_total_akomodasi))
      } else {
         $('#tindakan_qty').val(1)
         $('#total').val($('#harga_satuan').val())
      }
   })

   $('#tindakan_paket_id').docoPaginationSelec2({
      _api: baseUrl+"penatajasa/inf-tagihan-pasien/tindakan-paket-ruangan",
      placeholder: "— Pilih —",
      minimumInputLength: 0,
      ajax : {
         dataType: 'json',
         quietMillis: 250,
         data: function(params) {

            params.ruangan_id = $('#ruangan_id').val();
            params.jenis = $("#jenis_pelayanan_form").val();
            params.id = _id;

            var _jenis = params.jenis;
            var _dataTindakan = $("#tindakan_paket_id option:selected").data()
            var _ruanganId = $('#ruangan_id').val();
            var _tindakanId = (_dataTindakan == 'undefined') ? _dataTindakan.data.id : null;
            var _dataKelas = $("#kelas_pelayanan option:selected").data()
            var _idKelas = _dataKelas.data.id;
            var _dataDokter = $("#dokter_pj option:selected").data()
            var _idDokter = _dataDokter.data.id;
            var query = {
               search: params
            }
            return {
               _type:params._type,
               ruangan_id:params.ruangan_id,
               jenis:params.jenis,
               daftartindakan_id:_tindakanId,
               penjamin_id:_penjamin_id,
               kelaspelayanan_id:_idKelas,
               dokter_id:_idDokter,
               jenis_pelayanan:_jenis,
               id:params.id,
               q:params.term,
               page:params.page || 1
            };
         },
      },
      cache: true
   })

   $('#tindakan_paket_id').on('change', function () {
      if($(this).val() == "") {
         return false;
      }

      var _jenis = $("#jenis_pelayanan_form").val();
      var _dataTindakan = $("#tindakan_paket_id option:selected").data()
      if(typeof _dataTindakan != 'undefined'){
         var _ruanganId = $('#ruangan_id').val();
         var _tindakanId = _dataTindakan.data.id;
         var _dataKelas = $("#kelas_pelayanan option:selected").data()
         var _idKelas = _dataKelas.data.id;
         var _dataDokter = $("#dokter_pj option:selected").data()
         var _idDokter = _dataDokter.data.id;
         var _params = `daftartindakan_id=${_tindakanId}&ruangan_id=${_ruanganId}&penjamin_id=${_penjamin_id}&kelaspelayanan_id=${_idKelas}&dokter_id=${_idDokter}&jenis_pelayanan=${_jenis}`
         $("#daftartindakan_id").val(_tindakanId)
         if(_jenis == 'tindakan') {
            $("#daftartindakan_id").val(_tindakanId)
         }else {
            $("#tipepaket_id").val(_tindakanId)
         }

         if( _idDokter  != ''){
            var _tindakanId = _dataTindakan.data.id;
            $.ajax({
               url: `/penatajasa/inf-tagihan-pasien/get-tarif?${_params}`,
               type: 'GET',
               success: function(data) {
                  $('#tindakan_qty').val(1).trigger("change")
                  $('#total').val(0).trigger("change")
                  if(data.length !== 0) {
                     $('#harga_satuan').val(docoHelper.convertToRupiah(data.harga_tariftindakan)).trigger("change");
                     $('#harga_satuan_origin').val(data.harga_tariftindakan)
                     $('#harga_tariftindakan').val(data.harga_tariftindakan)
                     $('#persencyto_tindakan').val(data.persencyto_tindakan)
                     // $('#penjamin_id').val(data.penjamin_id)
                     $('#penjamin_tindakan_id').val(data.penjamin_id)
                     $('#daftartindakan_id').val(data.daftartindakan_id)
                     $('#tipepaket_id').val(data.tipepaket_id)
                     $('#persen_penyulit').val(data.persen_penyulit);
                     isChangePrice = false;
                     _hitungTarif()
                  }
                  else {
                     $('#harga_satuan').val(docoHelper.convertToRupiah(0))
                     $('#harga_tariftindakan').val(0)
                     $('#harga_satuan_origin').val(0)
                  }
               }
            });
         }
      }
   })

   $('#tindakan_obat_id').docoPaginationSelec2({
      _api: baseUrl+"penatajasa/inf-tagihan-pasien/get-tindakan-list",
      placeholder: "— Pilih —",
      minimumInputLength: 0,
      ajax : {
         dataType: 'json',
         quietMillis: 250,
         data: function(params) {
            return {
               q:params.term,
               page:params.page || 1
            };
         },
      },
      cache: true
   })

   $('#dokter_pj').on('change', function (event) {
      var _dataKelas = $("#kelas_pelayanan option:selected").data()
      var _jenisPelayanan = $('#jenis_pelayanan_form').val();
      var _dataTindakan = $("#tindakan_paket_id option:selected").data()
      var _ruanganId = $('#ruangan_id').val();
      var _tindakanId = _dataTindakan.data.id;
      var _idKelas = _dataKelas.data.id;
      var _idDokter = $(this).val();
      if(_tindakanId.length == 0) {
         return false;
      }
      is_akomodasi = $('#is_akomodasi').is(':checked')
      if( (typeof _dataTindakan.data.id != 'undefined' || _dataTindakan.data.id != null) && !is_akomodasi){
         var _tindakanId = _dataTindakan.data.id;
         var _params = `daftartindakan_id=${_tindakanId}&ruangan_id=${_ruanganId}&penjamin_id=${_penjamin_id}&kelaspelayanan_id=${_idKelas}&dokter_id=${_idDokter}&jenis_pelayanan=${_jenisPelayanan}`
         $.ajax({
            url: `/penatajasa/inf-tagihan-pasien/get-tarif?${_params}`,
            type: 'GET',
            success: function(data) {
               if(data.length !== 0) {
                  var _hargaTotal = 0;
                  var _hargaTarif = data.harga_tariftindakan;
                  var _hargaCito = data.persencyto_tindakan;
                  var _hargaPenyulit = data.persen_penyulit;

                  $('#penjamin_tindakan_id').val(data.penjamin_id)
                  $('#harga_satuan').val(docoHelper.convertToRupiah(_hargaTarif))
                  $('#harga_tariftindakan').val(_hargaTarif)
                  $('#harga_satuan_origin').val(_hargaTarif)
                  $('#persencyto_tindakan').val(_hargaCito)
                  $('#persen_penyulit').val(_hargaPenyulit)
                  isChangePrice = false
                  _hitungTarif()
               }
               else {
                  $('#harga_satuan').val(docoHelper.convertToRupiah(0))
                  $('#harga_tariftindakan').val(0)
                  $('#harga_satuan_origin').val(0)
               }
            }
         });
      }
   })

   $('#instalasi_id').on('change', function(event){
      $('#tindakan_pelayanan_id').val("").trigger('change')
      $('#paket_pelayanan_id').val("").trigger('change')
      $('#obatalkes_id').find('option').remove();
      $('#satuan_id').val("").trigger('change')
      $('#harga_satuan').val('')
      $('#harga_satuan_origin').val('')
      $('#tindakan_qty').val('')
      $('#qty_obat').val('')
      $('#total').val('')
      $('#subtotal_obat').text('-')
      $('#satuan_obat').text('-')
      $('#is_ditagihkan').prop('checked',false)
      if($('#is_akomodasi').is(':checked')){
         closeFormAkomodasi()
         $('#is_akomodasi').prop('checked',false)
      }
      if($('#instalasi_id').val() != instalasi_ri){
         $('#is_akomodasi').prop('disabled',true)
      } else {
         $('#is_akomodasi').prop('disabled',false)
      }

   })

   $('#ruangan_id').on('change', function(event){
      $('#tindakan_pelayanan_id').val("").trigger('change')
      $('#paket_pelayanan_id').val("").trigger('change')
      $('#obatalkes_id').find('option').remove();
      $('#satuan_id').val("").trigger('change')
      $('#harga_satuan').val('')
      $('#harga_satuan_origin').val('')
      $('#tindakan_qty').val('')
      $('#qty_obat').val('')
      $('#total').val('')
      $('#kamartempattidur_id').val(null).trigger('change')
      $('#subtotal_obat').text('-')
      $('#satuan_obat').text('-')
      $('#is_ditagihkan').prop('checked',false)
   })

   $('#tindakan_qty').on('input', function (event) {
      _hitungTarif()
   })

   $('#qty_obat').on('input', function (event) {
      _hitungSubTotal()
   })

   $('#is_cyto').on('change', function (evelt) {
      _hitungTarif()
   })

   $('#is_penyulit').on('change', function (evelt) {
      _hitungTarif()
   })

   $('#is_ditagihkan').on('change', function (evelt) {
      _hitungSubTotal()
   })

   var _hitungTarif = function () {
      var qty = $('#tindakan_qty').val() ? parseFloat($('#tindakan_qty').val()) : 0;
      var harga_satuan = parseFloat($('#harga_tariftindakan').val())
      var persen_penyulit = typeof $('#persen_penyulit').val() == "undefined" ? 0 : $('#persen_penyulit').val();
      var persencyto_tindakan = typeof $('#persencyto_tindakan').val() == "undefined" ? 0 : $('#persencyto_tindakan').val();
      var harga_cyto = harga_penyulit = 0;

      if($('#is_penyulit').is(':checked')) {
         harga_penyulit = parseFloat((persen_penyulit/ 100) * harga_satuan)
         harga_satuan = parseFloat(harga_satuan + harga_penyulit)
      }

      if ($('#is_cyto').is(':checked')) {
         harga_cyto = parseFloat((persencyto_tindakan/100) * harga_satuan)
         harga_satuan = parseFloat(harga_satuan + harga_cyto)
      }

      jumlah = parseFloat(harga_satuan*qty);
      $('#total').val(docoHelper.convertToRupiah(jumlah))
   }

   var _hitungSubTotal = function () {
      var data = $("#obatalkes_id option:selected").data();
      var harga_satuan = data.data.hargaygdipakai;
      var qty = $('#qty_obat').val() ? parseFloat($('#qty_obat').val()) : 0;
      if($('#is_ditagihkan').is(':checked')) {
         jumlah = parseFloat(harga_satuan*qty);
      }
      else {
         jumlah = 0;
      }

      $('#subtotal_obat').text(docoHelper.convertToRupiah(jumlah));
   }
   $('#simpan-tindakan-proses').click(function(e) {
      e.preventDefault(); // Menghindari submit form secara default
      var _form = $('#tindakan-form'); // Pastikan ID form benar
      $.ajax({
          url: _form.attr('action'), // Mengambil action dari form atau ganti dengan URL spesifik
          type: 'POST',
          data: _form.serializeArray(), // Serialisasi data form
          success: function(response) {
              // Handle response
              // Anda bisa menambahkan kode untuk menutup modal atau meng-update UI
          },
          error: function(error) {
              console.error('Error:', error);
          }
      });
   });

   $('#simpan-tindakan').on('click', function (event) {
      var checkContents = setInterval(function(){
         if ($("#confirm-dialog").length > 0  && isChangePrice){
           $('#confirm-dialog').css('height','480px')
           clearInterval(checkContents);
         }
       },100);
      var header = 'Perhatian !';
      var textarea = '<div class="form-group col-md-12"><div class="col-md-3"><label>Alasan </label><span style="color:red"> *</span></div><div class="col-md-6"><textarea id="edit-harga-alasan" class="form-control input-sm" name="alasan_edit_harga" rows="5" placeholder="Alasan" aria-required="true"></textarea></div></div>';
      var labelUsername = '<div class="col-md-3"><label>Username </label><span style="color:red"> *</span></div>';
      var labelPassword = '<div class="col-md-3"><label>Password </label><span style="color:red"> *</span></div>';
      if (isChangePrice) {
         add = $('#confirm-form').clone().removeClass('hidden');
         add.find('.input-pemakai').removeAttr('readonly');
         add.find('.input-pemakai').attr('value', '');
         add.find('.input-pemakai').attr('id', 'pemakai-validasi');
         add.find('.input-pemakai').attr('placeholder', 'Username');
         add.find('.input-pemakai').parent().addClass('col-md-12');
         add.find('.input-pemakai').before(labelUsername);
         add.find('.input-pemakai').wrap('<div class="col-md-6 input-username"></div>');
         add.find('.input-username').after('<br><br>');
         add.find('.input-sandi').attr('id', 'sandi-validasi');
         add.find('.input-sandi').parent().addClass('col-md-12 password-text-input');
         add.find('.input-sandi').before(labelPassword);
         add.find('.input-sandi').wrap('<div class="col-md-6 input-password"></div>');
         add.find('.input-password').after('<br><br>');
         add.find('.password-text-input').after(textarea);
         add.find('.delete-confirm-custom').removeClass('col-md-6 col-md-offset-3');
         add.find('.delete-confirm-custom').addClass('col-md-12');
         add = add.html();
         var message = 'Apakah anda yakin untuk mengedit tarif tagihan pada transaksi ini ?' + add;
         var label = {
               buttons: {
                  'Yes': 'button-yes',
                  'No': 'button-no'
               },
               hidden: true
         };
      } else {
         var message = 'Apakah anda yakin untuk menyimpan data ini ?'
         var label = {
               buttons: {
                  'Yes': 'button-yes',
                  'No': 'button-no'
               }
         };
      }

      var instalasi_nama =  $("#instalasi_id option:selected").text();
      var ruangan_nama =  $("#ruangan_id option:selected").text();
      var tindakan_nama =  $("#tindakan_paket_id option:selected").text();
      var paket_nama =  $("#paket_pelayanan_id option:selected").text();
      var dokterpj_nama =  $("#dokter_pj option:selected").text();
      var jenis = $("#jenis_pelayanan_form").val();
      var _dataTindakan = $("#tindakan_paket_id option:selected").data();

      // if(jenis == 'tindakan') {
      //    $("#daftartindakan_id").val(_dataTindakan.data.id)
      // }
      // else {
      //    $("#tipepaket_id").val(_dataTindakan.data.id)
      // }

      $('#instalasi_nama').val(instalasi_nama);
      $('#ruangan_nama').val(ruangan_nama);
      $('#dokterdpjp_nama').val(dokterpj_nama);
      $('#tindakan_nama').val(tindakan_nama);
      $('#paket_nama').val(paket_nama);

      var _form = $('#tindakan-form');


 
      event.preventDefault();
      $.showQuestionDialog(header, message, label, function (reaction) {
         if (reaction == 'Yes') {
               if (isChangePrice) {
                  var user = $('#pemakai-validasi').val();
                  var pass = $('#sandi-validasi').val();
                  var edit_harga_alasan = $('#edit-harga-alasan').val();
                  if(edit_harga_alasan ==''){
                     docoNotification("warning","Proses Gagal","Alasan edit harga belum diisi")
                  } else {
                     $('#alasan_edit_harga').val(edit_harga_alasan)
                     $().docoForm('click', {
                        url: baseUrl + 'penatajasa/end-point/check-authorization',
                        skipConfirm: true,
                        skipSuccessNotif: true,
                        formInput:false,
                        data: {
                              nama_pemakai: user,
                              katakunci_pemakai: pass,
                              modul_id: modulId,
                              akses: 'save-tmp-tagihan',
                        },
                        success: function (data) {
                           var response = data.response;
                           setTimeout(function () {
                               showLoader()
                           }, 100);
                           $().docoForm('click',{
                              url : _form.attr('action'),
                              data : _form.serializeArray(),
                              skipConfirm: true,
                              formInput:false,
                              success : function (res) {
                                 $('#jenis_pelayanan').val("").trigger('change');
                                 $('#is_cyto').prop("checked",false);
                                 $('#tindakan_qty').val('');
                                 $("#jenis_pelayanan").focus();
                                 table.draw();
                                 $("#modal_backdrop").modal('toggle');
                                 // $("#modal_backdrop").data('modal',null);
                                 $('#modal_backdrop').on('hidden.bs.modal', function () {
                                    $('.modal-body').html('');
                                 });
                                 $('.delete-cache').unbind('click');
                              }
                           });
                        }
                     })
                  }
               } else {
                  $("#modal_riwayat").modal('show');
                  $().docoForm('click',{
                     url :_form.attr('action'),
                     data : _form.serializeArray(),
                     skipConfirm: true,
                     success : function (res) {
                        $('#jenis_pelayanan').val("").trigger('change');
                        $('#is_cyto').prop("checked",false);
                        $('#tindakan_qty').val('');
                        $("#jenis_pelayanan").focus();
                        table.draw();
                        $("#modal_backdrop").modal('toggle');
                        // $("#modal_backdrop").data('modal',null);
                        $('#modal_backdrop').on('hidden.bs.modal', function () {
                           $('.modal-body').html('');
                        });
                        $('.delete-cache').unbind('click');
                     }
                  });
               }
         }
         if (reaction == 'No') {
            hideQuestionDialog();
            $('[data-popup="tooltip"]').tooltip();
         }
      });
   })


   $('#simpan-obat-bmhp').on('click', function (e){
      e.preventDefault();
      var data_obat = [];
      var data = $("#obatalkes_id option:selected").data();
      var nama_satuan_ids = $("#satuan_bmhp").text();
      var satuan_bmhp_ids = data.data.hargaygdipakai;
      var nilainetto_ygdipakai = data.data.harganetto_ygdipakai;
      var bmhp_ids = data.data.id;
      var bmhp_nama_ids = data.data.text;
      var ruangan_id_obat = $("#ruangan_id option:selected").val();
      var instalasi_id_obat = $("#instalasi_id option:selected").val();
      let depo_id = $("#depo_id").val();
      var ruangan_nama = (depo_id!='') ? $("#depo_id option:selected").text() : $("#ruangan_id option:selected").text();
      let daftartindakan_id = $("#tindakan_obat_id").val();
      var daftartindakan_nama = (daftartindakan_id !='') ? $("#tindakan_obat_id option:selected").text() : '';

      data_obat.push({
         bmhp : bmhp_ids,
         qty_obat : $('#qty_obat').val(),
         satuan_id : $('#satuan_bmhp').val(),
         satuan_bmhp : satuan_bmhp_ids,
         nama_bmhp : bmhp_nama_ids,
         nama_satuan : nama_satuan_ids,
         is_ditagihkan : $('#is_ditagihkan').is(':checked'),
         harganetto_ygdipakai : nilainetto_ygdipakai,
         //Untuk cek stok obat
         ruangan_id_obat : ruangan_id_obat,
         instalasi_id_obat : instalasi_id_obat,
         depo_id : depo_id,
         ruangan_nama : ruangan_nama,
         daftartindakan_id : daftartindakan_id,
         daftartindakan_nama : daftartindakan_nama,
      });
      var resData = {
         res : data_obat,
      }
      $().docoForm('click',{
         url : '/penatajasa/inf-tagihan-pasien/add-cache-obat',
         data : resData,
         skipConfirm : true,
         success : function (res) {
            qty=res?.response?.qty;
            $('#obatalkes_id').find('option').remove();;
            $('#tindakan_obat_id').val("").trigger('change');
            $('#depo_id').val("").trigger('change');
            $('#satuan_id').val("").trigger('change');
            $('#qty_obat').val("").trigger('change');
            $('#is_ditagihkan').prop("checked",false);
            $('#satuan_obat').text('-');
            $('#subtotal_obat').text('-');
            $('#stok_obat').text('-');
            if(table_tindakan){
               table_tindakan.draw();
            }
            table_bmhp.draw();
            

         }
      });
   })

   $(".close-modal").click(function(e){
      var header = 'Perhatian !'
      var message = 'Anda yakin akan kembali dan menghapus semua yang sudah diinputkan?'
      var label = {
         buttons: {
            'Yes': 'button-yes',
            'No': 'button-no'
         }
      };

      e.preventDefault();
      if ((table_bmhp && table_bmhp.data().length > 0) || table_tindakan.data().length > 0) {
          $.showQuestionDialog(header, message, label, function (reaction) {
              if (reaction == 'Yes') {
                  $('#modal_backdrop').modal('hide')
              }
              if (reaction == 'No') {
                 return false;
              }
          });
      } else {
          $('#modal_backdrop').modal('hide')
      }
      setTimeout(function () {
         $(".table-tagihan-tindakan").attr("style", "width: 1188px !important;");
      }, 500);
   });

   function data_audit(pendaftaran_id){
      $.getJSON(baseUrl+"penatajasa/inf-tagihan-pasien/data-audit?id="+pendaftaran_id, function(res){
         if(res == true){
            window.onbeforeunload = confirmExit;
            function confirmExit()
            {
               return "Do you want to leave this page without saving ?";
            }
         }
      });
   }

   $('#tambah-tindakan').on('click', function (event) {
      event.preventDefault();
      var instalasi_nama =  $("#instalasi_id option:selected").text();
      var ruangan_nama =  $("#ruangan_id option:selected").text();
      var tindakan_nama =  $("#tindakan_paket_id option:selected").text();
      var paket_nama =  $("#paket_pelayanan_id option:selected").text();
      var dokterpj_nama =  $("#dokter_pj option:selected").text();

      $('#instalasi_nama').val(instalasi_nama);
      $('#ruangan_nama').val(ruangan_nama);
      $('#dokterdpjp_nama').val(dokterpj_nama);
      $('#tindakan_nama').val(tindakan_nama);
      $('#paket_nama').val(paket_nama);

      var _form = $('#tindakan-form');
      $(this).docoForm('click',{
         url : '/penatajasa/inf-tagihan-pasien/add-cache-tindakan?pendaftaran_id='+_id+'&isShowObatForm='+_isShowObatForm+'&isValidasiTglAkomodasi='+isValidasiTglAkomodasi,
         data : _form.serializeArray(),
         skipConfirm : true,
         success : function (res) {
            qty=res?.response?.qty;
            $('#jenis_pelayanan').val("").trigger('change');
            $('#is_cyto').prop("checked",false);
            $('#tindakan_qty').val('');
            $('#harga_satuan').val('');
            $('#penjamin_tindakan_id').val('');
            $('#total').val('');
            $("#jenis_pelayanan").focus();
            $('#tindakan_paket_id').text('');
            $('#is_cyto').prop('checked', false);
            $('#is_penyulit').prop('checked', false);
            table_tindakan.draw();
            if(table_bmhp){
               table_bmhp.draw();
            }
            $('.delete-cache-tindakan').unbind('click');
            if($('#is_akomodasi').is(':checked')){
               var akomodasiOption = new Option(_daftartindakan_nama_akomodasi, null, true, true);
               $('#tindakan_paket_id').append(akomodasiOption)
               $('#qty').val(1)
               $('#total').val(0)
               $('#kamartempattidur_id').select2({})
               $('#kamartempattidur_id').val(null).trigger('change')
               $('#is_half_akomodasi').prop('checked',false)
            }

         }
      });
   })

   $('#depo_id').on('change', function (e){
      $('#obatalkes_id').find('option').remove();
      $('#stok_obat').text('')
      $('#qty_obat').val(0);
   })

   function closeFormAkomodasi(){
      $('.div_kamar_ruangan').hide()
      $('.div_tempat_tidur').hide()
      $('#is_half_akomodasi').hide()
      $('#is_akomodasi').prop("checked",false)
      $('#is_cyto').prop("disabled",false)
      $('#is_penyulit').prop("disabled",false)
      $('#tindakan_paket_id').prop('disabled',false)
      $('#tindakan_paket_id').val(null)
      $('#tindakan_paket_id').text(null)
      $('#kamartempattidur_id').val(null).trigger('change')
      $('#tindakan_qty').prop('readonly',false)
      $('#tindakan_qty').val("")
      $('#harga_satuan').val(0)
      if($('#is_paket').is(':checked')){
         $('#jenis_pelayanan_form').val('paket')
      } else {
         $('#jenis_pelayanan_form').val('tindakan')
      }
      $('#daftartindakan_id').val("")
      $('#total').val("")
      $('#is_half_akomodasi').prop('checked',false)
   }
   $('#harga_satuan').on('change', function (e){
      if(docoHelper.convertToAngka($('#harga_satuan').val()) != $('#harga_satuan_origin').val()){
         isChangePrice = true;
      }
      $('#harga_tariftindakan').val(docoHelper.convertToAngka($('#harga_satuan').val()))
      _hitungTarif()
   })

 });
