$(document).ready(function(){

    $('#content-td').docoLoad({
        url: '/master/tipe-diskon/tipe-diskon',
        dataType: 'html',
        success : function(data) {
        }
    });

    $(document).on('click', '.sp', function(e) {
            e.preventDefault();
            var type = $(this).attr('data-type');
            var render = $(this).attr('data-render');
            var contentTarget = $(this).attr('data-content');
            var urlRender;
            var baseController = "/master/tipe-diskon/";
            urlRender = baseController + render;

            if(type == 'edit'){
                var tableId = $(this).attr('data-table');
                var table = $(tableId).DataTable();
                var tableData = table.row('.selected').data();
                var selectedLength =  table.row('.selected').length;
                localStorage.removeItem('changeData');
                if (selectedLength == 0) {
                    docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Belum ada data yang dipilih!"));
                    return false;
                } else {
                    var primaryId = tableData.primary;
                    if (typeof primaryId !== 'undefined') {
                        $('#' + contentTarget).docoLoad({
                            url: urlRender+primaryId,
                            dataType: 'html',
                            success : function(data) {
                                $(" .select2 ").select2();
                                $('.pickadate').pickadate({
                                    format: 'dd mmm, yyyy',
                                    selectMonths: true,
                                    formatSubmit: 'yyyy-mm-dd',
                                });
                            }
                        });
                    }
                }
            }else{
                $('#' + contentTarget).docoLoad({
                    url: urlRender,
                    dataType: 'html',
                    success : function(data) {
                        $(" .select2 ").select2();
                    }
                });
            }
    });

    $(document).on('click', '#btn-save-kp', function(e) {
        e.preventDefault();
        // var type = $(this).attr('data-type');
        var render = $(this).attr('data-render');
        var target = $(this).attr('data-target');
        // var tab = $(this).attr('data-tab');
        var action = $(this).attr('action');
        var form_id = $(this).attr('form-id');
        var contentTarget = $(target + " div").attr("id");
        var urlRender;
        var baseController = "/master/tarif-tindakan/";
        urlRender = baseController + render;
        
        if (typeof action !== 'undefined') {
            var data_form = $('#' + form_id).serializeArray();
            var data_detail = JSON.parse(localStorage.getItem('changeData'))
            var tmpData = [];
            $.each(data_detail, function (key, value) {
                tmpData.push(value)
            });
            data_form.push({name:"KontrakPenjaminForm[detail]", value : JSON.stringify(tmpData )})

            $(this).docoForm("click", {
                data: data_form,
                url: action,
                method: 'POST',
                success: function (data) {
                    localStorage.removeItem('changeData')

                    setTimeout(function () {
                        $('#' + contentTarget).docoLoad({
                            url: urlRender,
                            dataType: 'html',
                            success : function(data) {
                                $(" .select2 ").select2();
                            }
                        });
                    }, 1000);
                }
            });
        }
    });

    $(document).on('change', '.disc-persen', function(e){
        e.preventDefault();
        let disc_persen = $(this).val();

        if(docoHelper.convertToAngka(disc_persen) > 100){
            docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Persen tidak boleh lebih dari 100!"));
            $(this).val("");
            return false;
        }

        if(docoHelper.convertToAngka(disc_persen) <= 0){
            docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Persen harus lebih dari 0!"));
            $(this).val("");
            return false;
        }
    })

    $(document).on('change', '.layanan-id', function(e){
        e.preventDefault();
        let layanan = $(this).find('option:selected').text();
        let _formId = $(this).closest("form").attr("id");
        let _form = $('#'+_formId);
        _form.find('.layanan').val(layanan);
        _form.find('.is-change').val(true);
    })

    $(document).on('click', '#btn-simpan', function(e){
        e.preventDefault();
        removeForm();
        let tableId = $('#table-result-'+jenis_layanan).DataTable();
        let _formId = $(this).closest("form").attr("id");
        let _form = $('#tipe-diskon-detail-form-'+jenis_layanan);
        /** untuk repopulate layanan_id jika asalnya disabled (kasus edit select 2 nya disabled) */
        if(_form.find('.layanan-id').prop("disabled")){
            _form.find('.layanan-id').prop("disabled", false);
        }
        /** ----- */
        
        let data_form = _form.serializeArray();
        $(this).docoForm("click", {
            data: data_form,
            url: '/master/tipe-diskon/create-detail?stat='+action,
            method: 'POST',
            success: function (data) {
                setTimeout(function () {
                    tableId.draw();
                    resetForm();
                }, 1000);
            }
        });
    })

    $(document).on('click', '#btn-simpan-all', function(e) {
        e.preventDefault();
        
        var data_form = $('#tipe-diskon-form').serializeArray();

        $(this).docoForm("click", {
            data: data_form,
            url: '/master/tipe-diskon/save',
            method: 'POST',
            success: function (data) {
                setTimeout(function () {
                    $('#btn-back').trigger('click');
                }, 1000);
            }
        });
    });

    $(document).on('click', '.delete-cache', function(e){
        e.preventDefault();
        let key = $(this).data('key');
        let cacheName = $(this).data('cache');
        let jenislayanan_id = $(this).data('jl');
        let tableId = $('#table-result-'+jenis_layanan).DataTable();
        let _formId = $(this).closest("form").attr("id");
        let _form = $('#'+_formId);
        $(this).docoForm('click',{
            confirmMessage: 'Anda yakin akan menghapus item ini?',
            url: '/master/tipe-diskon/unset-cache?key='+key+'&cacheName='+cacheName,
            success : function (response) {
                    tableId.draw();
                    _form.find('.layanan-id').prop("disabled", false);
                    resetForm();
            }
        });
    });

    $(document).on('click', '.edit-cache', function(e){
        e.preventDefault();
        let key = $(this).data('key');
        let cacheName = $(this).data('cache');
        let jenislayanan_id = $(this).data('jl');
        let tableId = $('#table-result-'+jenis_layanan).DataTable();
        var table = $(tableId).DataTable();
        var tableData = tableId.row('.selected').data();
        var selectedLength =  table.row('.selected').length;
        let _formId = $(this).closest("form").attr("id");
        let _form = $('#'+_formId);

        if(typeof tableData != "undefined"){
           _form.find('.max-dijamin').val(docoHelper.convertToRupiah(tableData.max_dijamin));
           _form.find('.disc-persen').val(tableData.disc_persen);
           _form.find('.layanan-id').val(tableData.layanan_id).trigger('change');
           _form.find('.is-change').val(false);
           _form.find('.layanan-id').prop("disabled", true);
           $("#btn-simpan").html("<b><i class='fa fa-floppy-o'></i></b> Simpan");
        }else{
            resetForm();
            _form.find('.is-change').val(true);
            _form.find('.layanan-id').prop("disabled", false);
           $("#btn-simpan").html("<b><i class='fa fa-plus'></i></b> Tambah");
        }
        return false;
    });
    
});




