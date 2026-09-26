$('#container_is_pelayanan_jenazah').prop('hidden',true);
$('.field-is_meninggal').hide();
$('.field-tgl_meninggal').hide();
$('.field-is_pelayanan_jenazah').hide();
$('.field-is_rencanakontrol').hide();
$('.field-tgl_rencanakontrol').hide();
$('.field-pasiendirujukkeluar_id').hide();
$('.field-nama_spesialis').hide();
$('.field-dokterdpjp_nama').hide();


$(document).ready(function () {
    var variable = "DI RUJUK RAWAT INAP";
    var value = 5;
    $('.caraKeluar option[value="'+value+'"]').attr("disabled", true);
    // $(".caraKeluar option:contains('" + variable + "')").attr("disabled","disabled");
    // $(".caraKeluar").prop("selectedIndex",-1);
    $(document).on('click', '.delete-data', function(e){
        e.preventDefault();
        var _url = $(this).attr('action');
        $(this).docoForm("click", {
            url: _url,
            skipConfirm:true,
            skipSuccessNotif:true,
            success : function(data) {
                table_jenazah_tindakan.draw();
                table_jenazah_obat.draw();
                table_jenazah_linen.draw();
                table_jenazah_alat.draw();
            }
        });
    })

    $('#btn-add-tindakan-jenazah').on('click',function(e){
        e.preventDefault();
        var formData = [];
        var dataHarga = 0;
        if($('#tindakan_jenazah').select2('data')[0]){
            data_selecttindakan = $('#tindakan_jenazah').select2('data')[0];
            if(data_selecttindakan.id == ''){
                docoNotification('error', 'Terjadi Kesalahan', 'Tindakan Tidak Boleh Kosong');
                return false;
            }
            if($('#tindakan_qty').val() == '' || $('#tindakan_qty').val() == 0){
                docoNotification('error', 'Terjadi Kesalahan', 'Qty Tidak Boleh Kosong');
                return false;
            }

            formData.push({name:'tindakan_jenazah',value:$('#tindakan_jenazah option:selected').text()});
            formData.push({name:'daftartindakan_id',value:$('#tindakan_jenazah').val()});
            formData.push({name:'qty_tindakan',value:$('#tindakan_qty').val()});
            formData.push({name:'tarif_satuan',value:data_selecttindakan.tarif_satuan});
            formData.push({name:'tarifcyto_tindakan',value:0});
            formData.push({name:'cyto_tindakan',value:0});

            var tarifTindakan = data_selecttindakan.tarif_satuan * $('#tindakan_qty').val();
            formData.push({name:'tarif_tindakan',value:tarifTindakan});

            if(data_selecttindakan.list_komponen != ''){
                var arr_listkomponen = JSON.parse(data_selecttindakan.list_komponen);
                var tind_komp = [];
                $.each(arr_listkomponen,function(k,v){
                    v = JSON.parse(v);
                    arr_listkomponen[k] = {
                        komponentarif_id:v.komponentarif_id,
                        tindakanpelayanan_id:null,
                        tarif_kompsatuan:v.harga_tariftindakan,
                        tarif_tindakankomp:v.harga_tariftindakan,
                        tarifcyto_tindakankomp:0,
                        subsidiasuransikomp:0,
                        subsidipemerintahkomp:0,
                        subsidirumahsakitkomp:0,
                        iurbiayakomp:0
                    };
                });
                formData.push({name:'additional_data',value:JSON.stringify({list_komponen:arr_listkomponen})});
            }
        }
        $(this).docoForm("click", {
            type: 'POST',
            url: 'save-cache-jenazah?id='+pendaftaranId+'&type=tindakan',
            data: formData,
            dataType:'json',
            skipConfirm:true,
            skipSuccessNotif:true,
            success : function(data) {
                $('#tindakan_jenazah').val('').trigger('change');
                $('#tindakan_qty').val('');
                table_jenazah_tindakan.draw();
            }
        });

    });

    $('#btn-add-obat-jenazah').on('click',function(e){
        e.preventDefault();
        var formData = [];
        var dataHarga = 0;
        if($('#obat_jenazah').select2('data')[0]){
            data_selectobat = $('#obat_jenazah').select2('data')[0];
            dataHarga = data_selectobat.harga;
            if(data_selectobat.id == ''){
                docoNotification('error', 'Terjadi Kesalahan', 'Obat Alkes Tidak Boleh Kosong');
                return false;
            }
            if($('#qty_obat').val() == '' || $('#qty_obat').val() == 0){
                docoNotification('error', 'Terjadi Kesalahan', 'Qty Tidak Boleh Kosong');
                return false;
            }
            var qty = $('#qty_obat').val();
            if(qty > data_selectobat.qty_tersedia){
                docoNotification('error', 'Terjadi Kesalahan', 'Qty Tidak Boleh Melebihi Stok Tersedia');
                return false;
            }
            if($('#satuan_obat').val() == ''){
                docoNotification('error', 'Terjadi Kesalahan', 'Satuan tidak boleh Kosong');
                return false;
            }
        
            formData.push({name:'obatalkes_id',value:$('#obat_jenazah').val()});
            formData.push({name:'qty',value:$('#qty_obat').val()});
            formData.push({name:'qty_tersedia',value:data_selectobat.qty_tersedia});
            formData.push({name:'satuankecil_id',value:$('#satuan_obat').val()});
            formData.push({name:'harganetto',value:data_selectobat.harganetto});
            formData.push({name:'hargajual',value:dataHarga});
            formData.push({name:'persendiscount',value:data_selectobat.persendiscount});
            formData.push({name:'persenppn',value:data_selectobat.persenppn});
            formData.push({name:'persenmargin',value:data_selectobat.persenmargin});
            formData.push({name:'jmldiscount',value:data_selectobat.jmldiscount});
            formData.push({name:'jmlmargin',value:data_selectobat.jmlmargin});
            formData.push({name:'jmlppn',value:data_selectobat.jmlppn});
            formData.push({name:'obat_jenazah',value:$('#obat_jenazah option:selected').text()});
            formData.push({name:'obat_satuan',value:$('#satuan_obat option:selected').text()});
            var obatHarga = dataHarga * $('#qty_obat').val();
            formData.push({name:'obat_harga',value:dataHarga});
        }

        $(this).docoForm("click", {
            url: 'save-cache-jenazah?id='+pendaftaranId+'&type=obat',
            data: formData,
            skipConfirm:true,
            skipSuccessNotif:true,
            success : function(data) {
                $('#obat_jenazah').val('').trigger('change');
                $('#satuan_obat').val('').trigger('change');
                $('#qty_obat').val('');
                table_jenazah_obat.draw();
            }
        });

    });

    $('#btn-add-linen-jenazah').on('click',function(e){
        e.preventDefault();
        var formData = [];
        if($('#linen_jenazah').select2('data')[0]){
            data_selectlinen = $('#linen_jenazah').select2('data')[0];
            if(data_selectlinen.id == ''){
                docoNotification('error', 'Terjadi Kesalahan', 'Linen Tidak Boleh Kosong');
                return false;
            }
            if($('#qty_linen').val() == '' || $('#qty_linen').val() == 0){
                docoNotification('error', 'Terjadi Kesalahan', 'Qty Linen Tidak Boleh Kosong');
                return false;
            }
        }
        formData.push({name:'linen_jenazah',value:$('#linen_jenazah option:selected').text()});
        formData.push({name:'barang_id',value:$('#linen_jenazah').val()});
        formData.push({name:'qty',value:$('#qty_linen').val()});

        $(this).docoForm("click", {
            url: 'save-cache-jenazah?id='+pendaftaranId+'&type=linen',
            data: formData,
            skipConfirm:true,
            skipSuccessNotif:true,
            success : function(data) {
                $('#linen_jenazah').val('').trigger('change');
                $('#qty_linen').val('');
                table_jenazah_linen.draw();
            }
        });
    });
    
    $('#btn-add-alat-jenazah').on('click',function(e){
        e.preventDefault();
        var formData = [];
        if($('#alat_jenazah').select2('data')[0]){
            data_selectalat = $('#alat_jenazah').select2('data')[0];
            if(data_selectalat.id == ''){
                docoNotification('error', 'Terjadi Kesalahan', 'Alat Tidak Boleh Kosong');
                return false;
            }
            if($('#qty_alat').val() == '' || $('#qty_alat').val() == 0){
                docoNotification('error', 'Terjadi Kesalahan', 'Qty Alat Tidak Boleh Kosong');
                return false;
            }
        }
        formData.push({name:'alat_jenazah',value:$('#alat_jenazah option:selected').text()});
        formData.push({name:'obatalkes_id',value:$('#alat_jenazah').val()});
        formData.push({name:'qty',value:$('#qty_alat').val()});

        $(this).docoForm("click", {
            url: 'save-cache-jenazah?id='+pendaftaranId+'&type=alat',
            data: formData,
            skipConfirm:true,
            skipSuccessNotif:true,
            success : function(data) {
                $('#alat_jenazah').val('').trigger('change');
                $('#qty_alat').val('');
                table_jenazah_alat.draw();
            }
        });
    });

 });

// $(function(){
    $('.pickadate').pickadate({
        formatSubmit: 'yyyy-mm-dd',
    });

    $('.pickadate-today').pickadate({
        formatSubmit: 'yyyy-mm-dd',
        clear:'',
        onStart: function ()
        {
            var date = new Date();
            this.set('select', [date.getFullYear(), date.getMonth() + 1, date.getDate()]);
            this.set('disable',true);
        },
    });

    $('.pickadate-w-month').pickadate({
        format: 'dd mmm, yyyy',
        selectMonths: true,
          selectYears: 99,
        max: true,
        formatSubmit: 'yyyy-mm-dd',
        clear:''
    });

    $(".datetime").AnyTime_picker({
        format: "%d-%m-%Y %H:%i",
        earliest: new Date(),
        latest: new Date(new Date().setHours(23, 59, 59, 999))
    });

    $(".datepick").pickadate({
        format: "dd-mm-yyyy",
        // disable: [{
        //     from: [0, 0, 0],
        //     // to: new Date(),
        // }],
    });

    var thisday = new Date();
    thisday.setDate(thisday.getDate() - 1);
    $(".datepickNext").pickadate({
        format: "dd-mm-yyyy",
        disable: [{
            from: [0, 0, 0],
            to: thisday,
        }],
    });

    
    $('#cari_kamar').on('click',function(e){
        var ruangan_id = $('#ruangan_id').val();
        var kelas_id = $('#infopasienranapform-kelaspelayanan_id').val();
        var jenis_id = $('#infopasienranapform-jeniskasuspenyakit_id').val();
        // alert(ruangan_id+' '+kelas_id+' '+jenis_id); exit;
        PNotify.removeAll();
        if(!ruangan_id){
            return new PNotify({
            title: "Terjadi Kesalahan",
            text: "Ruangan belum dipilih!",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
            });
        }
        _this = $(this);
        modal = $(_this.data('target'));
        // modal.modal('toggle');
        // console.log(modal);
        width = _this.data('width');
        var modal_content = modal.find('div.modal-dialog');
        url = _this.attr('href')+'?ruangan_id='+ruangan_id+'&kelas_id='+kelas_id+'&jenis_id='+jenis_id;
        
        if (typeof width != 'undefined') {
            modal_content.css('width',width);
        }

        $('.modal-content', modal).empty();
        var _html = '<div class="text-center">';
        _html += '<h3><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>'+i18next.t("memuat")+'...</b></h3>';
        _html += '</div>';

        $('[data-popup="tooltip"]').tooltip('destroy');
        // console.log(modal.find('.modal-content'));
        modal.find('.modal-content').html(_html).load(url, function() {
            modal.modal({show:true});
        });
    });

    // Get element by id and remove class
    var element = document.getElementById("btn-reset");
    element.classList.remove("btn-toolbar");

    // Assign lokasi rak dan subrak
    $("#btn-reset").click(function(event) {
        location.reload();
    });

    $("#btn-pasien-pulang").on('click', function (event) {
        event.preventDefault();
        var dataPost = $("#pulang-kamar-form").serializeArray();
        var dataPasienRujuk = $("#pasien-rujuk-form").serializeArray();
        var isOrderJenazah = false;
        if($('#is_pelayanan_jenazah').prop('checked') == true){
            isOrderJenazah = true;
            var frm_pelayanan_jenazah = $('#highlighted-justified-tab1 :input').serializeArray();
            $.merge(dataPost,frm_pelayanan_jenazah);
        }
        var isDataRujukanPasien = false;
        if (typeof dataPasienRujuk != 'undefined' && $('#carakeluar_id').val() == '2') {
            isDataRujukanPasien = true;
            $.merge(dataPost, dataPasienRujuk);
        }
            $(this).docoForm("click", {
                data: dataPost,
                method: 'post',
                skipSuccessNotif: true,
                success: function (response) {
                    // console.log(response.response.message);
                    // if(response.response.message == "Data Berhasil di simpan" ){
                        if(isOrderJenazah){
                            PNotify.prototype.options.styling = "bootstrap3";
                            (new PNotify({
                                title: "Berhasil",
                                text: "Data berhasil disimpan, apakah Anda ingin melakukan cetak?",
                                addclass: "alert alert-success alert-arrow-right alert-styled-right",
                                type: "success",
                                buttons: {
                                    closer: false,
                                    sticker: false
                                },
                                hide: false,
                                confirm: {
                                    confirm: true
                                },
                                history: {
                                    history: false
                                }
                            })).get().on('pnotify.confirm', function() {
                                // Print                            
                                window.open("/jenazah/informasi-pasien-meninggal/cetak-belum-diterima?pendaftaran_id="+pendaftaranId);
                                setTimeout(function () {
                                    window.location.href = "/ranap"
                                }, 3000);
                            }).on('pnotify.cancel', function() {
                                setTimeout(function () {
                                    window.location.href = "/ranap"
                                }, 1000);
                            });
                        }else if (isDataRujukanPasien) {
                            PNotify.prototype.options.styling = "bootstrap3";
                            (new PNotify({
                                title: "Berhasil",
                                text: "Data berhasil disimpan, apakah Anda ingin melakukan cetak?",
                                addclass: "alert alert-success alert-arrow-right alert-styled-right",
                                type: "success",
                                buttons: {
                                    closer: false,
                                    sticker: false
                                },
                                hide: false,
                                confirm: {
                                    confirm: true
                                },
                                history: {
                                    history: false
                                }
                            })).get().on('pnotify.confirm', function() {
                                // Print                            
                                window.open("/ranap/inf-pasien-ranap/cetak-rujukan?id="+pendaftaranId);
                                setTimeout(function () {
                                    window.location.href = "/ranap/inf-pasien-pulang"
                                }, 3000);
                            }).on('pnotify.cancel', function() {
                                setTimeout(function () {
                                    window.location.href = "/ranap/inf-pasien-pulang"
                                }, 1000);
                            });
                        }else{
                            let title = "Proses Berhasil !";
                            let message = "Pasien berhasil dipulangkan.";
                            
                            if (response.response.resKontrol && response.response.resKontrol.length > 0) {
                                let errorMessageRencanaKontrol = response.response.resKontrol;
                                
                                let htmlError = errorMessageRencanaKontrol.reduce(function(total, val){
                                    return total+"<li>"+val+"</li>";
                                }, "");
                                errorMessageRencanaKontrol = " namun pembuatan surat rencana kontrol gagal, karena :<ul>"+htmlError+"</ul> Silahkan hubungi petugas administrasi";
                                message += errorMessageRencanaKontrol;
                                
                            }
                            setTimeout(function () {
                                let notifPulang = {
                                    title: title,
                                    message: message
                                }
                                sessionStorage.setItem('notifPulang', JSON.stringify(notifPulang));
                                window.location.href = "/ranap"
                            }, 500);
                        }
                    // }
                }
            });
    });
    $("#is_meninggal").change(function() {
        if(this.checked) {
            $("#tgl_meninggal").prop("disabled", false);
            $("input[name='PasienPulangForm[is_rencanakontrol]']").prop('checked', false);
            $("#tgl_rencanakontrol").prop("disabled", true);
            $("#tgl_rencanakontrol").val(null);
            $('#nama_spesialis').prop("disabled", true);
            $('#nama_spesialis').val('');
            $('#dokterdpjp_nama').prop("disabled", true);
            $('#dokterdpjp_nama').val('');

            $("input[name='PasienPulangForm[pasiendirujukkeluar_id]']").prop('checked', false);
            $("#display_hidden").prop("hidden", true);

            $('#container_is_pelayanan_jenazah').prop('hidden',false);
        }else{
            $("#tgl_meninggal").prop("disabled", true);
            $("#tgl_meninggal").val(null);
            $('#container_is_pelayanan_jenazah').prop('hidden',true);
            $("#display_hidden_jenazah").prop("hidden", true);
            $("input[name='PasienPulangForm[is_pelayanan_jenazah]']").prop('checked', false);
        }
    });

    $("#is_pelayanan_jenazah").change(function(){
        if(this.checked) {
            $("#display_hidden_jenazah").prop("hidden", false);
            table_jenazah_tindakan = $("#tabel-tindakan").docoTabel({
                destroy:true,
                scrollX: false,
                filter: false,
                sorting: [[1, "asc"]], 
                processing: true,
                serverSide: true,
                paging: false,
                ajax: "get-cache-jenazah?cachetype=tindakan&id="+pendaftaranId,
                columnDefs: [
                    {
                        targets: 3,
                        className: 'text-right'
                    }
                  ],
                columns: [
                    {title: "No", data: "rownum"},
                    {title: "Nama Tindakan", data: "tindakan_jenazah"},
                    {title: "Qty", data: "qty_tindakan"},
                    {
                        title: "Harga", 
                        data: "tarif_tindakan",
                        render:function(tarif_satuan){
                            return 'Rp. '+docoHelper.convertToRupiah(tarif_satuan);
                        }
                    },
                    {
                        title: "Aksi",
                        data: "aksi",
                        searchable: false,
                        orderable: false,
                    },
                ],
            });
            table_jenazah_obat = $("#tabel-obat").docoTabel({
                destroy:true,
                scrollX: false,
                filter: false,
                sorting: [[1, "asc"]], 
                processing: true,
                serverSide: true,
                paging: false,
                ajax: "get-cache-jenazah?cachetype=obat&id="+pendaftaranId,
                columnDefs: [
                    {
                        targets: 4,
                        className: 'text-right'
                    }
                  ],
                columns: [
                    {title: "No", data: "rownum"},
                    {title: "Nama Obat Alkes", data: "obat_jenazah"},
                    {title: "Qty", data: "qty"},
                    {title: "Satuan", data: "obat_satuan"},
                    {
                        title: "Harga", 
                        data: "obat_harga",
                        render:function(obat_harga){
                            return 'Rp. '+docoHelper.convertToRupiah(obat_harga);
                        }
                    },
                    {
                        title: "Aksi",
                        data: "aksi",
                        searchable: false,
                        orderable: false,
                    },
                ],
            });

            table_jenazah_linen = $("#tabel-linen").docoTabel({
                destroy:true,
                scrollX: false,
                filter: false,
                sorting: [[1, "asc"]], 
                processing: true,
                serverSide: true,
                paging: false,
                ajax: "get-cache-jenazah?cachetype=linen&id="+pendaftaranId,
                columns: [
                    {title: "No", data: "rownum"},
                    {title: "Nama Linen", data: "linen_jenazah"},
                    {title: "Qty", data: "qty"},
                    {
                        title: "Aksi",
                        data: "aksi",
                        searchable: false,
                        orderable: false,
                    },
                ],
            });

            table_jenazah_alat = $("#tabel-alat").docoTabel({
                destroy:true,
                scrollX: false,
                filter: false,
                sorting: [[1, "asc"]], 
                processing: true,
                serverSide: true,
                paging: false,
                ajax: "get-cache-jenazah?cachetype=alat&id="+pendaftaranId,
                columns: [
                    {title: "No", data: "rownum"},
                    {title: "Nama Alat", data: "alat_jenazah"},
                    {title: "Qty", data: "qty"},
                    {
                        title: "Aksi",
                        data: "aksi",
                        searchable: false,
                        orderable: false,
                    },
                ],
            });

        }else{

            $('#display_hidden_jenazah').find(':input')
              .not(':button, :submit, :reset, :hidden,[readonly]')
              .val('')
              .prop('checked', false)
              .prop('selected', false);
            $('#pelayananjenazahform-hub_keluarga').val('').trigger('change');
            $('#tindakan_jenazah').val('').trigger('change');
            $('#obat_jenazah').val('').trigger('change');
            $('#linen_jenazah').val('').trigger('change');
            $('#alat_jenazah').val('').trigger('change');
            $("#display_hidden_jenazah").prop("hidden", true);
        }
    });

    $("#is_rencanakontrol").change(function() {
        if(this.checked) {
            $("#tgl_rencanakontrol").prop("disabled", false);
            $("#tgl_rencanakontrol").val(moment().format("DD-MM-YYYY"));
            $('#nama_spesialis').prop("disabled", false);
            $('#dokterdpjp_nama').prop("disabled", false);
            $("input[name='PasienPulangForm[is_meninggal]']").prop('checked', false);
            $("#tgl_meninggal").prop("disabled", true);
            $("#tgl_meninggal").val(null);

            $("input[name='PasienPulangForm[pasiendirujukkeluar_id]']").prop('checked', false);
            $("#display_hidden").prop("hidden", true);

            $("#display_hidden_jenazah").prop("hidden", true);
            $('#container_is_pelayanan_jenazah').prop('hidden',true);
            $("input[name='PasienPulangForm[is_pelayanan_jenazah]']").prop('checked', false);
        }else{
            $("#tgl_rencanakontrol").prop("disabled", true);
            $("#tgl_rencanakontrol").val('');
            $('#nama_spesialis').prop("disabled", true);
            $('#nama_spesialis').val('');
            $('#dokterdpjp_nama').prop("disabled", true);
            $('#dokterdpjp_nama').val('');
        }
    });

    $("#pasiendirujukkeluar_id").change(function() {
        if(this.checked) {
            $("#display_hidden").prop("hidden", false);
            $("input[name='PasienPulangForm[is_meninggal]']").prop('checked', false);
            $("#tgl_meninggal").prop("disabled", true);
            $("#tgl_meninggal").val(null);
            
            $("input[name='PasienPulangForm[is_rencanakontrol]']").prop('checked', false);
            $("#tgl_rencanakontrol").prop("disabled", true);
            $("#tgl_rencanakontrol").val(null);
            $('#nama_spesialis').prop("disabled", true);
            $('#nama_spesialis').val('');
            $('#dokterdpjp_nama').prop("disabled", true);
            $('#dokterdpjp_nama').val('');

            $("#display_hidden_jenazah").prop("hidden", true);
            $('#container_is_pelayanan_jenazah').prop('hidden',true);
            $("input[name='PasienPulangForm[is_pelayanan_jenazah]']").prop('checked', false);
        }else{
            $('#display_hidden').find(':input')
              .not(':button, :submit, :reset, :hidden,[readonly]')
              .val('')
              .prop('checked', false)
              .prop('selected', false);
            $('#tgldirujuk').val('');
            $('#pegawai_id').val('').trigger('change');
            $('#rujukankeluar_id').val('').trigger('change');
            $("#display_hidden").prop("hidden", true);
        }
    });

    $('#carakeluar_id').on('change', function(){
        var valuedata = $(this).val();
        //  console.log(valuedata);
        if (valuedata) {
            $.ajax({
                type: 'GET',
                url: '/ranap/inf-pasien-ranap/get-data-kondisi-keluar?carakeluar_id='+valuedata,
                success: function(response){
                    var select = $('#kondisikeluar_id');
                    select.children().remove();
                    $('#kondisikeluar_id').append($('<option>', { value : '' }).text('-- Pilih --'));
                    $.each(response.result, function(index, item) {
                        $('#kondisikeluar_id').append($('<option>', { value : item.id }).text(item.text));
                    });
                    $("#kondisikeluar_id").prop("disabled", false);
                }
            });
            $("input[name='PasienPulangForm[is_meninggal]']").prop('checked', false).trigger('change');
            // $("input[name='PasienPulangForm[pasiendirujukkeluar_id]']").prop('checked', false).trigger('change');
            $("input[name='PasienPulangForm[is_rencanakontrol]']").prop('checked', false).trigger('change');
            $('#form_rujukan_pasien').prop('hidden', true);
            $('.field-pasiendirujukkeluar_id').prop('hidden', false);
            if(valuedata == '4'){
                $('.field-is_meninggal').show();
                $('.field-tgl_meninggal').show();
                // $('.field-is_pelayanan_jenazah').show();
                $('.field-pasiendirujukkeluar_id').hide();
                $('.field-is_rencanakontrol').hide();
                $('.field-tgl_rencanakontrol').hide();
                $('.field-nama_spesialis').hide();
                $('.field-dokterdpjp_nama').hide();
                $("input[name='PasienPulangForm[is_meninggal]']").prop('checked', true).trigger('change');
            }else if(valuedata == '2'){
                $('.field-is_meninggal').hide();
                $('.field-tgl_meninggal').hide();
                $('.field-is_pelayanan_jenazah').hide();
                // $('.field-pasiendirujukkeluar_id').show();
                $('.field-is_rencanakontrol').hide();
                $('.field-tgl_rencanakontrol').hide();
                $('.field-nama_spesialis').hide();
                $('.field-dokterdpjp_nama').hide();
                $('#form_rujukan_pasien').prop('hidden', false);
                $('.field-pasiendirujukkeluar_id').prop('hidden', true);
                var paramsRujuk = 'pasienadmisi_id='+pasienadmisiId+'&id='+pendaftaranId;
                $.ajax({
                    type: 'GET',
                    url: '/ranap/inf-pasien-ranap/form-pasien-rujuk?'+paramsRujuk,
                    success: function(response){
                        $("#form_rujukan_pasien .tabbable").html(response);
                    }
                });
            }else{
                $('.field-is_meninggal').hide();
                $('.field-tgl_meninggal').hide();
                $('.field-is_pelayanan_jenazah').hide();
                $('.field-pasiendirujukkeluar_id').hide();
                $('.field-is_rencanakontrol').show();
                $('.field-tgl_rencanakontrol').show();
                $('.field-nama_spesialis').show();
                $('.field-dokterdpjp_nama').show();
            }
        }
    });
    
function formate(date) {
    if (typeof date == "string")
        date = new Date(date);
    var day = (date.getDate() <= 9 ? "0" + date.getDate() : date.getDate());
    var month = (date.getMonth() + 1 <= 9 ? "0" + (date.getMonth() + 1) : (date.getMonth() + 1));
    // var dateString = day + "/" + month + "/" + date.getFullYear() + " " + date.getHours() + ":" + date.getMinutes();
    var dateString = month + "/" + day + "/" + date.getFullYear();
    return dateString;
}

$(document).ready(function(){
    // jQuery("#btn-pasien-pulang").removeClass("btn-toolbar");
    
    var tglMasukKamar = $('#tgl_admisi_x').val() 
    var tglKeluarKamar = $('#tglpasienpulang_x').val()
    
    var convtglMasukKamar = formate(tglMasukKamar);
    var convtgltglKeluarKamar = formate(tglKeluarKamar);

    var date1 = new Date(convtglMasukKamar);
    var date2 = new Date(convtgltglKeluarKamar);
    var timeDiff = Math.abs(date2.getTime() - date1.getTime());
    var valueDiffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 

    if(valueDiffDays == 0){
        var valDays = 1;
    }else{
        var valDays = valueDiffDays;
    }

    // Round down.

    // $('input[name="PasienPulangForm[lama_rawat]"]').val( valDays );
});