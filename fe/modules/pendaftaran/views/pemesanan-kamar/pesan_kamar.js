$(function(){
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

    // $(".datetime").val(function() {
    //     var d = new Date();
    //     return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);;
    // });

    // $(".datetime-trx").val(function() {
    //     var d = new Date();
    //     return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);;
    // });

    $(".datetime").AnyTime_picker({
        format: "%d-%m-%Y %H:%i",
        earliest: new Date(),
        latest: new Date(new Date().getTime() + 24 * 60 * 60 * 1000)
    });

    $('.ddl_no_rekam_medik').on('change',function(e){
        no_rm = $('.ddl_no_rekam_medik').val();
        PNotify.removeAll();
        $.ajax({
            url: '/pendaftaran/pemesanan-kamar/get-info-pasien',
            type: 'GET',
            async: true,
            dataType: 'json',
            data: {
                no_rm: no_rm
            },
            beforeSend : function () {
                console.log('Mohon Tunggu');
            },
            success: function(data){
                const jenis_kamar = $('#kamarruangan_jenis').val();
                const jenis_kelamin_booking = $('#jenis_kelamin_booking').val();

                $('#trapemesanankamarform-jenisidentitas').val(data.jenisidentitas).trigger('change').attr('disabled',true);
                $('#trapemesanankamarform-no_identitas_pasien').val(data.no_identitas_pasien).attr('readonly',true);
                $('#trapemesanankamarform-nama_pasien').val(data.nama_pasien).attr('readonly',true);
                $('#trapemesanankamarform-nama_bin').val(data.nama_bin).attr('readonly',true);
                $('#trapemesanankamarform-tempat_lahir').val(data.tempat_lahir).attr('readonly',true);
                $('#trapemesanankamarform-tanggal_lahir').pickadate('picker').set('select', data.tanggal_lahir, { format: 'yyyy-mm-dd' }).set('disable',true);
                $('#trapemesanankamarform-pasien_id').val(data.pasien_id);

                $('#trapemesanankamarform-umur').val(data.umur_pasien + ' Tahun');
                $('input:radio[name="TraPemesananKamarForm[jeniskelamin]"]').filter('[value="'+data.jeniskelamin+'"]').prop('checked',true);
                $("#trapemesanankamarform-jeniskelamin input:radio").attr('disabled',true);
                $('#trapemesanankamarform-alamat_pasien').val(data.alamat_pasien);
                $('#trapemesanankamarform-pekerjaan_id').val(data.pekerjaan_id).trigger('change');
                $('#trapemesanankamarform-agama').val(data.agama).trigger('change').attr('disabled',true);

                var telp = "";
                if (data.no_telepon_pasien) {
                    telp = data.no_telepon_pasien;
                }
                if (data.no_mobile_pasien) {
                    if (telp != "") {
                        telp += "/"+data.no_mobile_pasien;
                    } else {
                        telp = data.no_mobile_pasien;
                    }
                }
                $('#trapemesanankamarform-no_telepon_pasien').val(telp);

                if (jenis_kelamin_booking) {
                    if (jenis_kelamin_booking != data.jeniskelamin) {
                        docoNotification('warning', i18next.t('Perhatian'), i18next.t('Kamar tidak sesuai dengan jenis kelamin !'));
                        $('#jeniskasuspenyakit_id').val('').trigger('change');
                        $('#select2_list_kelaspelayanan').val('').trigger('change');
                        $('#ruangan_id').val('').trigger('change');
                        $('#nokamar').val('');
                    }
                }
            },
            error : function (data) {
                console.log('ERROR');
            },
        })
    });

    $('#trapemesanankamarform-tanggal_lahir').on('change',function () {
        var umur = getUmur(convertTanggalYmd($(this).val()), new Date());
        $('#trapemesanankamarform-umur').val(umur);
    });
    $('.ddl_no_rekam_medik').select2({
            minimumInputLength: 3,
            ajax: {
                url: '/pendaftaran/pemesanan-kamar/get-pasien-rekam-medik',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

    $(document).on('click', '.btn-cetak', function(){
        // console.log($(this).attr('data-target'))
        window.open($(this).attr('data-target'), '_blank')
    })
    $('#cari_kamar').on('click',function(e){
        var ruangan_id = $('#ruangan_id').val();
        var kelas_id = $('#select2_list_kelaspelayanan').val();
        var jenis_id = $('#jeniskasuspenyakit_id').val();
        PNotify.removeAll();
        if(!jenis_id){
            return new PNotify({
            title: "Terjadi Kesalahan",
            text: "Jenis Kasus belum dipilih!",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
            });
        }
        if(!kelas_id){
            return new PNotify({
            title: "Terjadi Kesalahan",
            text: "List Kelas Pelayanan belum dipilih!",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning"
            });
        }
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
        url = _this.attr('href')+'?jenis_id='+jenis_id+'&kelas_id='+kelas_id+'&ruangan_id='+ruangan_id;

        if (typeof width != 'undefined') {
            modal_content.css('width',width);
        }

        $('.modal-content', modal).empty();
        var _html = '<div class="text-center">';
            _html += '<h3><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>'+ i18next.t("memuat") +' . . . </b></h3>';
        _html +=    '</div>';
        $('[data-popup="tooltip"]').tooltip('destroy');
        // console.log(modal.find('.modal-content'));
        modal.find('.modal-content').html(_html).load(url, function() {
            modal.modal({show:true});
        });
    });

    $('.data-reset').on('click',function(e){

        if($('#trapemesanankamarform-pasien_id').val() != ''){
            $("#trapemesanankamarform-jeniskelamin input:radio").attr('disabled',false);
            $('#trapemesanankamarform-jenisidentitas').attr('disabled',false);
            $('#trapemesanankamarform-pekerjaan_id').attr('disabled',false);
            $('#trapemesanankamarform-agama').attr('disabled',false);
            $('#trapemesanankamarform-no_identitas_pasien').attr('readonly',false);
            $('#trapemesanankamarform-nama_pasien').attr('readonly',false);
            $('#trapemesanankamarform-nama_bin').attr('readonly',false);
            $('#trapemesanankamarform-tempat_lahir').attr('readonly',false);
            $('#trapemesanankamarform-alamat_pasien').attr('readonly',false);
            $('#trapemesanankamarform-no_telepon_pasien').attr('readonly',false);
            $('#trapemesanankamarform-pasien_id').val('');
        }
        $("#trapemesanankamarform-jeniskelamin input:radio").prop('checked',false);
        $(':input','#pemesanan-kamar-form')
          .not(':button, :submit, :reset,:radio')
          .val('')
          .prop('checked', false)
          .prop('selected', false)
          .trigger('change');
        $('#trapemesanankamarform-umur').val('');
        $('#pemesanan-kamar-form')[0].reset();
        $(".datetime-trx").val(function() {
            var d = new Date();
            return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);;
        });
    });

    $('#btn-pesan-kamar').click(function(e){
        e.preventDefault();
        $('#pemesanan-kamar-form').submit();
    });

    $('#pemesanan-kamar-form').on('beforeSubmit', function(e){
        PNotify.removeAll();
        if($('#trapemesanankamarform-pasien_id').val() != ''){
            $("#trapemesanankamarform-jeniskelamin input:radio").attr('disabled',false);
            $('#trapemesanankamarform-jenisidentitas').attr('disabled',false);
            $('#trapemesanankamarform-pekerjaan_id').attr('disabled',false);
            $('#trapemesanankamarform-agama').attr('disabled',false);
            $('#trapemesanankamarform-no_identitas_pasien').attr('readonly',false);
            $('#trapemesanankamarform-nama_pasien').attr('readonly',false);
            $('#trapemesanankamarform-nama_bin').attr('readonly',false);
            $('#trapemesanankamarform-tempat_lahir').attr('readonly',false);
            $('#trapemesanankamarform-alamat_pasien').attr('readonly',false);
            $('#trapemesanankamarform-no_telepon_pasien').attr('readonly',false);
        }

        var form = $(this);
        var formData = form.serialize();
        console.log('cek');
        $.ajax({
            url: '/pendaftaran/pemesanan-kamar/create',
            method: "POST",
            type: "json",
            data : formData,
            beforeSend: function(){
                var overlayTemplate = '<div id="confirm-dialog-overlay" class="confirm-dialog-overlay"></div>';
                var dialogTemplate = '<div id="confirm-dialog">';
                        dialogTemplate += '<div class="dialog-content">';
                            dialogTemplate += '<div class="row"><h2 class=\"confirm-header-text text-center\"></h2></div><p class=\"confirm-message-text\"></p>';
                        dialogTemplate += '</div>';
                    dialogTemplate += '</div>';

                    $('body').append(overlayTemplate);
                    $('body').append(dialogTemplate);
                    $('.confirm-header-text').html('<i class="fa fa-gear fa-spin fa-3x fa-fw" style="margin:18px 0 19px 0;"></i>&nbsp;Sedang memproses . . .');
                // $('.form-group').removeClass('has-error');
                // $('span.help-block.error').remove();
                // $('div.help-block.error').remove();
            },
            success: function(data){
                var succTitle = 'Proses Berhasil';
                var succMsg = 'Data Berhasil Disimpan!';
                $(function(){
                    new PNotify({
                        title: succTitle,
                        text: succMsg,
                        addclass: "alert alert-success alert-arrow-right alert-styled-right",
                        type: 'success',
                    });
                });
                $('.btn-cetak').attr('disabled', false)
                $('.btn-cetak').attr('data-target', '/pendaftaran/pemesanan-kamar/cetak-pemesanan?id=' + data.response.id)
                // location.reload();
            },
            error: function(data){
                txt = "Kesalahan Internal";
                if(data.responseJSON){
                    txt = data.response.text;
                }
                $(function(){
                    new PNotify({
                        title: "Terjadi Kesalahan",
                        text: txt,
                        addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                        type: "warning"
                    });
                }); 
            },
            complete: function(){
                hideQuestionDialog();
                $('body').find('.confirm-dialog-overlay').remove();
                $('#trapemesanankamarform-pasien_id').val('');
                $("#trapemesanankamarform-jeniskelamin input:radio").prop('checked',false);
                $(':input','#pemesanan-kamar-form')
                    .not(':button, :submit, :reset,:radio')
                    .val('')
                    .prop('checked', false)
                    .prop('selected', false)
                    .trigger('change');
                $('#pemesanan-kamar-form')[0].reset();
                $(".datetime-trx").val(function() {
                    var d = new Date();
                    return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);;
                });
                setTimeout(function () {
                    $('#trapemesanankamarform-tanggal_lahir').pickadate('picker').set('select', new Date(), {
                        format: 'yyyy-mm-dd'
                    }).set('disable', false);
                    $('#trapemesanankamarform-tanggal_lahir').val(null).trigger("change");
                    $('#trapemesanankamarform-umur').val('');
                    $("#trapemesanankamarform-jeniskelamin input:radio").attr('disabled', false);
                    $('#trapemesanankamarform-jenisidentitas').attr('disabled', false);
                    $('#trapemesanankamarform-pekerjaan_id').attr('disabled', false);
                    $('#trapemesanankamarform-agama').attr('disabled', false);
                    $('#trapemesanankamarform-no_identitas_pasien').attr('readonly', false);
                    $('#trapemesanankamarform-nama_pasien').attr('readonly', false);
                    $('#trapemesanankamarform-nama_bin').attr('readonly', false);
                    $('#trapemesanankamarform-tempat_lahir').attr('readonly', false);
                    $('#trapemesanankamarform-alamat_pasien').attr('readonly', false);
                    $('#trapemesanankamarform-no_telepon_pasien').attr('readonly', false);
                    $('#jeniskasuspenyakit_id').val('').trigger('change');
                    $('#select2_list_kelaspelayanan').val('').trigger('change');
                    $('#trapemesanankamarform-pekerjaan_id').val('').trigger('change');
                    $('#trapemesanankamarform-agama').val('').trigger('change');
                    $('#ruangan_id').val('').trigger('change');
                }, 500);

                setTimeout(function() {
                    $('.form-group').removeClass('has-error');
                    $('.form-group').find('small.help-block').hide();
                }, 1000);
            }
        })
    }).on('submit', function(e){
        e.preventDefault();
    });

    /* UBAH PESANAN KAMAR */
    $('#btn-ubah-pesan-kamar').click(function(e) {
        // Prevent default
        e.preventDefault();
        if ($('#trapemesanankamarform-pasien_id').val() != '') {
            $("#trapemesanankamarform-jeniskelamin input:radio").attr('disabled', false);
        }

        // Submit
        var form = $("#pemesanan-kamar-form");
        var formData = form.serialize();

        // Get id
        var id = $("#bookingkamar-id").val();

        // Ajax
        $.ajax({
            url: '/pendaftaran/pemesanan-kamar/update?id='+id,
            method: "POST",
            type: "json",
            data : formData,
            beforeSend: function(){
                var overlayTemplate = '<div id="confirm-dialog-overlay" class="confirm-dialog-overlay"></div>';
                var dialogTemplate = '<div id="confirm-dialog">';
                        dialogTemplate += '<div class="dialog-content">';
                            dialogTemplate += '<div class="row"><h2 class=\"confirm-header-text text-center\"></h2></div><p class=\"confirm-message-text\"></p>';
                        dialogTemplate += '</div>';
                    dialogTemplate += '</div>';

                    $('body').append(overlayTemplate);
                    $('body').append(dialogTemplate);
                    $('.confirm-header-text').html('<i class="fa fa-gear fa-spin fa-3x fa-fw" style="margin:18px 0 19px 0;"></i>&nbsp;Sedang memproses . . .');
                $('.form-group').removeClass('has-error');
                $('span.help-block.error').remove();
                $('div.help-block.error').remove();
            },
            success: function(data){
                var succTitle = 'Proses Berhasil';
                var succMsg = 'Data Berhasil Diubah!';
                docoNotification('success', succTitle, succMsg);

                // Redirect
                window.location.replace("/pendaftaran/pemesanan-kamar/informasi");
            },
            error: function(data){
                var errTitle = 'Proses Gagal';
                var errMsg = 'Data Gagal Diubah!';
                docoNotification('error', errTitle, errMsg);  
            },
            complete: function(){
                hideQuestionDialog();
                $('body').find('.confirm-dialog-overlay').remove();
            }
        })
    });

    if($('#norm').val() !== ''){
        var data = {
                    id: $("#norm").val(),
                    text: $("#norm").val()+" - "+$("#trapemesanankamarform-nama_pasien").val()
                };
        var option = [];
        option[0] = new Option(data.text, data.id, false, false);
        $('.ddl_no_rekam_medik').append(option);
        $('.ddl_no_rekam_medik').val($('#norm').val()).trigger('change');

    }

    // Check booking kamar id
    if ($("#bookingkamar-id").val() == 'asdadasd') {
        // Ajax
        $.ajax({
            url: '/pendaftaran/pemesanan-kamar/get-list-ruangan',
            method: "POST",
            type: "json",
            data : {"depdrop_parents": [$("#jeniskasuspenyakit_id").val()]},
            success: function(response) {
                /* APPEND OPTIONS KE DROPDOWN RUANGAN */
                // Assign some variables
                var data = response.output;
                var options = [];

                // Loop
                for (var i = 0; i < data.length; i++) {
                    // Assign data untuk dimasukan ke list ruangan
                    var value = {
                        id: data[i].id,
                        text: data[i].name
                    };

                    // Option untuk di append ke dropdown ruangan
                    options[i] = new Option(value.text, value.id, false, false);
                }

                // Append ke dropdown ruangan
                $('#ruangan_id').append(options);

                /* ENABLE DROPDOWN RUANGAN */
                // Enable dropdown ruangan
                $("#ruangan_id").prop("disabled", false);

                /* APPEND OPTION KE DROPDOWN NOMOR REKAM MEDIK */
                // Assign data untuk dimasukan ke list ruangan
                var data = {
                    id: $("#trapemesanankamarform-pasien_id").val(),
                    text: $("#trapemesanankamarform-no_rekam_medik").val()+" - "+$("#trapemesanankamarform-nama_pasien").val()
                };

                // Append ke dropdown rekam medik
                var option = [];
                option[0] = new Option(data.text, data.id, false, false);

                // Assign value
                // Append ke dropdown rekam medik
                $('.ddl_no_rekam_medik').append(option);

                // Selected
                $('.ddl_no_rekam_medik').select2().val(data.id);
            }
        })
    }

    // Get element by id and remove class
    var element = document.getElementById("btn-reset");
    element.classList.remove("btn-toolbar");

    // Assign lokasi rak dan subrak
    $("#btn-reset").click(function(event) {
        location.reload();
    });

    let link = document.URL;
    let patternAction = link.match(/update/g);
    if (patternAction) {
        if (patternAction[0] == 'update') {
            $('.jk').change(function() {
                var jenis_kelamin = $(this).val();
                if (jenis_kelamin_booking != jenis_kelamin) {
                    docoNotification('warning', i18next.t('Perhatian'), i18next.t('Kamar tidak sesuai dengan jenis kelamin !'));
                    $('#jeniskasuspenyakit_id').val('').trigger('change');
                    $('#select2_list_kelaspelayanan').val('').trigger('change');
                    $('#ruangan_id').val('').trigger('change');
                    $('#nokamar').val('');
                }
            })
        }
    }
});