var table_bayi;
function formattingChild ( d ) {
    return 'Fitur Belum Tersedia';
}


$("#kelahiranbayiform-tinggi_badan,#kelahiranbayiform-berat_badan").on("keypress", function (event) {
    return isNumberKey(event, this, "with-commas")
})

$(document).ready(function(){

    $('.jam_lahir').timepicker({
        showMeridian: false,
        minuteStep: 5,
        defaultTime: false
    });
        
    $('#collapse-bayibarulahir').on('show.bs.collapse', function () {
       $('.panel-toolbars').hide();
    });
    $('#collapse-bayibarulahir').on('hide.bs.collapse', function () {
        if($(this).find('.loading-panel-partograf').length != 0){
            return false;
        }
    });
    $('#collapse-bayibarulahir').on('hidden.bs.collapse', function () {
        $('.panel-toolbars').show();
        $('#form-bayibarulahir').trigger('reset');
    });
    $('#collapse-bayibarulahir').on('shown.bs.collapse', function () {
        $('#kelahiranbayiform-berat_badan').focus();
    });
    $(".field-kelahiranbayiform-asfiksia_tindakan").hide();
    $(".field-kelahiranbayiform-asfiksia_tindakan_lainnya").hide();
    
    $("input:radio[name='KelahiranBayiForm[asfiksia]']").click(function(){
        var asfiksia = $("input:radio[name='KelahiranBayiForm[asfiksia]']:checked").val();
        var asfiksiaByText = $("input:radio[name='KelahiranBayiForm[asfiksia]']:checked").attr('text');
        if(asfiksiaByText !== "Tidak Ada") {
            $(".field-kelahiranbayiform-asfiksia_tindakan").show();
            $(".field-kelahiranbayiform-asfiksia_tindakan_lainnya").show();
        }else{
            $(".field-kelahiranbayiform-asfiksia_tindakan").hide();
            $(".field-kelahiranbayiform-asfiksia_tindakan_lainnya").hide();
        }
    });

    table_bayi = $('#table-bayibarulahir').docoTabel({
        columnDefs: [ {
            sortable: false,
            className: "select-checkbox",
            targets:   0
        }],
        select: {
            style:    "os",
            selector: "td:first-child"
        },
        searching:false,
        sort:false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: '/igd/pemeriksaan-igd/get-data-bayibarulahir?id='+pendaftaran_id,
        bInfo:false,
        bPaginate:false,
        lengthChange:false,
        columns: [
            {data: null, searchable: false, sortable: false, defaultContent:""},
            // {
            //     title: "Bayi",
            //     data: "bayi_urut",
            //     searchable: false,
            //     orderable: false
            // },
            {
                title: "Bayi",
                data: "nama_pasien",
                searchable: false,
                orderable: false,
                render: (data, displ, row) => {
                    return data != null ? `${row.nama_depan} ${data}` : `By. Ny. ${namaPasien}`
                }
            },{
                title: "No. Peneng",
                data: "no_peneng",
                searchable: false,
                orderable: false,
            },
            {
                title: "Berat Badan (Gram)",
                data: "berat_badan",
                searchable: false,
                orderable: false
            },
            {
                title: "Panjang Badan (Cm)",
                data: "tinggi_badan",
                searchable: false,
                orderable: false
            },
            {
                title: "Tanggal Lahir",
                data: "tanggal_lahir",
                searchable: false,
                orderable: false,
                className: 'text-center',
                render: (data) => {
                    return data != null ? convertDateByFormat(data, 'd-m-Y h:i') : '-'
                }
            },
            {
                title: "Jenis Kelamin",
                data: "jenis_kelamin",
                searchable: false,
                orderable: false
            },
            {
                title: "Penilaian",
                data: "penilaian",
                searchable: false,
                orderable: false
            },
            {
                title: "Kondisi Bayi",
                data: "kondisi_bayi",
                searchable: false,
                orderable: false
            }
        ]
    });

    $('#table-bayibarulahir tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table_bayi.row(tr);
        if ( row.child.isShown() ) {
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            row.child( formattingChild(row.data()) ).show();
            tr.addClass('shown');
        }
    });

    $('#btn-tambah-bayibarulahir').on('click',function(){
        $('#collapse-bayibarulahir').collapse('show');
        $('#collapse-bayibarulahir').loadingPanelPartograf('clear');
        $('.btn-hapus-hipotermi').trigger('click');
    });

    $('#btn-ubah-bayibarulahir').on('click',function(){
        $('div').removeClass('has-error');
        $('span.help-block.error').remove();
        $('div.help-block.error').remove();
        var tableData = table_bayi.row(".selected").data();
        if (typeof tableData !== "undefined") {
            var id = tableData.primary;
            var url = "/igd/pemeriksaan-igd/get-kondisi-bayi-baru-lahir?id="+pendaftaran_id+"&kelahiranbayi_id="+id;
            $.ajax({
                type : "GET",
                dataType : "JSON",
                url : url,
                success : function (response) {
                    $('.btn-hapus-hipotermi').trigger('click');
                    var tindakan_normal = JSON.parse(response.normal_tindakan);
                    var hipotermi = JSON.parse(response.keterangan_hipotermi);
                    var asfiksia_tindakan = JSON.parse(response.asfiksia_tindakan);
                    _bayiId = id;
                    var tmpValue = [];
                    var tmpValueTindakan = [];
                    
                    $.each(tindakan_normal, function(key,val){
                        tmpValue[val] = true;
                    });
                    $.each(asfiksia_tindakan, function(key,val){
                        tmpValueTindakan[val] = true;
                    });

                    if(hipotermi !== null) {
                        var countHipertomi = hipotermi.length;
                        for(x=1;x<=(countHipertomi - 1);x++) {
                            $('.btn-tambah-hipotermi').trigger('click');
                        }

                        $.each($(".hipotermi_keterangan"), function(key,val) {
                            if(typeof hipotermi[key] !== 'undefined') {
                                $(this).val(hipotermi[key]);
                            }
                        })
                    }
                    
                    $.each($(".opt_normal_tindakan"), function(){
                        var getValue = $(this).val();
                        var updateValue = typeof tmpValue[getValue] !== 'undefined' ? tmpValue[getValue] : false;
                        if(updateValue) {
                            $(this).prop("checked", true);
                        }
                    });

                    $.each($(".opt_asfiksia_tindakan"), function(){
                        var getValue = $(this).val();
                        var updateValue = typeof tmpValueTindakan[getValue] !== 'undefined' ? tmpValueTindakan[getValue] : false;
                        if(updateValue) {
                            $(this).prop("checked", true);
                        }
                    });

                    $('#collapse-bayibarulahir').collapse('show');
                    $('#collapse-bayibarulahir').loadingPanelPartograf('clear');

                    $("#kelahiranbayiform-berat_badan").val(response.berat_badan);
                    $("#kelahiranbayiform-tinggi_badan").val(response.tinggi_badan);
                    $("#kelahiranbayiform-warna_kulit").val(response.warna_kulit);
                    $("#kelahiranbayiform-tgl_lahir").val(response.tgl_lahir);
                    // if(response.jenis_kelamin == 16) {
                    //     $("#jenis_kelamin-0").prop("checked", true);
                    // }
                    // else {
                    //     $("#jenis_kelamin-1").prop("checked", true);
                    // }
                    $('input:radio[name="KelahiranBayiForm[jenis_kelamin]"][value="' + response.jenis_kelamin + '"]').prop('checked', true);


                    if(response.penilaian == 83) {
                        $("#penilaian-0").prop("checked", true);
                    }
                    else {
                        $("#penilaian-1").prop("checked", true);
                    }

                    if(response.kondisi_bayi == 85) {
                        $("#kondisi_bayi_normal").prop("checked", true);
                    }
                    else if(response.kondisi_bayi == 86) {
                        $("#kondisi_bayi_cacat").prop("checked", true);
                    } else {
                        $("#kondisi_bayi_hipotermi").prop("checked", true);
                    }
                    
                    if (response.asfiksia_name !== "Tidak Ada") {
                        $(".field-kelahiranbayiform-asfiksia_tindakan").show();
                        $(".field-kelahiranbayiform-asfiksia_tindakan_lainnya").show();
                    }else{
                        $(".field-kelahiranbayiform-asfiksia_tindakan").hide();
                        $(".field-kelahiranbayiform-asfiksia_tindakan_lainnya").hide();
                    }

                    if(response.asfiksia_name == 'Ringan') {
                        $("#asfiksia-0").prop("checked", true);
                    } else if(response.asfiksia_name == 'Pucat') {
                        $("#asfiksia-1").prop("checked", true);
                    } else if(response.asfiksia_name == 'Biru') {
                        $("#asfiksia-2").prop("checked", true);
                    } else if (response.asfiksia_name == 'Lemas') {
                        $("#asfiksia-3").prop("checked", true);
                    }else{
                        $("#asfiksia-4").prop("checked", true);
                    }

                    if(response.is_asi == true) {
                        $("#is_asi_ya").prop("checked", true);
                        $("#kelahiranbayiform-keterangan_asi_ya").val(response.keterangan_asi);
                    }
                    else {
                        $("#is_asi_tidak").prop("checked", true);
                        $("#kelahiranbayiform-keterangan_asi_tidak").val(response.keterangan_asi);
                    }

                    $("#kelahiranbayiform-masalah_lain").val(response.masalah_lain);
                    $("#kelahiranbayiform-hasil").val(response.hasil);
                    $("#kelahiranbayiform-cacat_kondisi").val(response.keterangan_cacat);
                    $('#kelahiranbayiform-lingkar_kepala').val(response.lingkar_kepala)
                    $('#kelahiranbayiform-golongan_darah').val(response.golongan_darah)
                    $('#kelahiranbayiform-pegawai_id').val(response.pegawai_id).trigger('change')
                    $('#kelahiranbayiform-kamartempattidur_id').append(new Option(response.kamartempattidur_text, response.kamartempattidur_id)).val(response.kamartempattidur_id).trigger('change');
                    // $("#kelahiranbayiform-asfiksia_tindakan").val(response.asfiksia_tindakan);
                },
                error : function (error) {
                    console.log(error);
                }
            });
        }else{
            docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');
        }
    });

    $('#btn-hapus-bayibarulahir').on('click',function(){
        var tableData = table_bayi.row(".selected").data();
        if (typeof tableData !== "undefined") {
            var id = tableData.primary;
            var url = "/igd/pemeriksaan-igd/delete-bayi-baru-lahir?kelahiranbayi_id="+id;
            event.preventDefault();
            $(this).docoForm('delete', {
                url: url,
                success : function (data) {
                    table_bayi.draw();
                }
            });
        }else{
            docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');
        }
    });

    $(document).on('keyup','#kelahiranbayiform-cacat_kondisi',function(){
        if($('#kelahiranbayiform-cacat_kondisi').val() != ''){
            $('#kondisi_bayi_cacat').prop('checked',true);
            $('#form_kondisi_normal').find(':input').each(function(){
                $(this).prop('checked',false);
            });
            $('#kelahiranbayiform-asfiksia_tindakan_lainnya').val('');

            var length = $(".hipotermi").length;
            if (length) {
                $(".hipotermi").each(function(index, object) {
                    if (index != (length - 1)) {
                        $(object).remove();
                    }
                });
            }

            $(".hipotermi_keterangan").val("");
        }
    });
    $(document).on("keyup", ".hipotermi_keterangan", function() {
        $(".hipotermi_keterangan").each(function(index, object) {
            if ($(object).val() != "") {
                $('#kondisi_bayi_hipotermi').prop('checked',true);
                $('#form_kondisi_normal').find(':input').each(function(){
                    $(this).prop('checked',false);
                });
                $('#kelahiranbayiform-cacat_kondisi').val('');
                $('#kelahiranbayiform-asfiksia_tindakan_lainnya').val('');
            }
        });
    });
    $(document).on('keyup','#kelahiranbayiform-asfiksia_tindakan_lainnya',function(){
        if($('#kelahiranbayiform-asfiksia_tindakan_lainnya').val() != ''){
            $('#kondisi_bayi_normal').prop('checked',true);
            $('#kelahiranbayiform-cacat_kondisi').val('');

            var length = $(".hipotermi").length;
            if (length) {
                $(".hipotermi").each(function(index, object) {
                    if (index != (length - 1)) {
                        $(object).remove();
                    }
                });
            }

            $(".hipotermi_keterangan").val("");
        }
    });
    $(document).on('change','.normal_options',function(){
        if($(this).prop('checked') == true){
            $('#kondisi_bayi_normal').prop('checked',true);
            $('#kelahiranbayiform-cacat_kondisi').val('');

            var length = $(".hipotermi").length;
            if (length) {
                $(".hipotermi").each(function(index, object) {
                    if (index != (length - 1)) {
                        $(object).remove();
                    }
                });
            }

            $(".hipotermi_keterangan").val("");
        }
    });
    $('#kelahiranbayiform-keterangan_asi_ya').on('keyup',function(){
        if($('#kelahiranbayiform-keterangan_asi_ya').val() != ''){
            $('#kelahiranbayiform-keterangan_asi_tidak').val('');
            $('#is_asi_ya').prop('checked',true);
        }
    });
    $('#kelahiranbayiform-keterangan_asi_tidak').on('keyup',function(){
        if($('#kelahiranbayiform-keterangan_asi_tidak').val() != ''){
            $('#kelahiranbayiform-keterangan_asi_ya').val('');
            $('#is_asi_tidak').prop('checked',true);
        }
    });

    $.ajax({
        url: '/igd/end-point/get-pegawai-data',
        success: function(response) {
            const {data} = response
            let _arrData = [];
            $.each(data, function(k,v) {
                _arrData.push({
                    id: k,
                    text: v
                })
            })
            $('#kelahiranbayiform-pegawai_id').select2({
                placeholder: 'Pilih Pegawai',
                data: _arrData,
            })
        },
        error: function(xhr) {

        }
    })
    $('#kelahiranbayiform-kamartempattidur_id').select2InfinityScroll({
        url: '/igd/end-point/get-kamar-tempat-tidur',
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                    status_isi: false,
                }
            }
        }
    })
});

$('#simpan-bayi-baru-lahir').off().on('click',function(e){
    var formData = [];
    var fields_input = $('#form-bayibarulahir').serializeArray();
    var tgl_lahir = '';
    var jam_lahir = '';
    jQuery.each( fields_input, function( i, field ) {
        if(field.value != ''){
            if(field.name === 'KelahiranBayiForm[tgl_lahir]') {
                tgl_lahir = field.value;
            }else if(field.name === 'jam_lahir'){
                jam_lahir = field.value;
            }else{
                formData.push({name:field.name,value:field.value});
            }
        }
    });
    var keterangan_kondisi = '';
    var keterangan_asi = '';
    var is_asi = '';
    var kondisi_bayi = '';
    if(ket_asi_ya = formData.find(x => x.name === 'KelahiranBayiForm[keterangan_asi_ya]')){
        keterangan_asi = ket_asi_ya.value;
        is_asi = 1;
    }else if(ket_asi_tidak = formData.find(x => x.name === 'KelahiranBayiForm[keterangan_asi_tidak]')){
        keterangan_asi = ket_asi_tidak.value;
        is_asi = 0;
    }
    if(ket_kondisi =formData.find(x => x.name === 'KelahiranBayiForm[cacat_kondisi]')){
        keterangan_kondisi = ket_kondisi.value;
        kondisi_bayi = $('input[name="KelahiranBayiForm[kondisi_bayi]"]:checked').val();
    }else if(ket_kondisi =formData.find(x => x.name === 'KelahiranBayiForm[hipotermi_keterangan][]')) {
        if (formData.length) {
            var temp_kondisi = [];

            for (var key in formData) {
                if (formData.hasOwnProperty(key)) {
                    if (formData[key].name === "KelahiranBayiForm[hipotermi_keterangan][]") {
                        temp_kondisi.push(formData[key].value);
                    }
                }
            }

            if (temp_kondisi.length) {
                keterangan_kondisi = JSON.stringify(temp_kondisi);
            }

            kondisi_bayi = $('input[name="KelahiranBayiForm[kondisi_bayi]"]:checked').val();
        }
    }else if($('input.normal_options:checked').length > 0 || $('#kelahiranbayiform-asfiksia_tindakan_lainnya').val() != ''){
        kondisi_bayi = $('input[name="KelahiranBayiForm[kondisi_bayi]"]:checked').val();
        keterangan_kondisi = $('#kelahiranbayiform-asfiksia_tindakan_lainnya').val();
        var normalTindakan = $("input.opt_normal_tindakan:checked").map(function(){
            var isian = '';
            if(typeof(listNormalTindakan[$(this).val()]) != "undefined" ){
                isian = {id:$(this).val(),text:listNormalTindakan[$(this).val()]};
            }
          return isian;
        }).get();
        if(normalTindakan.length > 0){
            formData.push({name:'KelahiranBayiForm[list_normal_tindakan]',value:JSON.stringify(normalTindakan)});
        }
        var asfiksiaTindakan = $("input.opt_asfiksia_tindakan:checked").map(function(){
            var isian = '';
            if(typeof(listAsfiksiaTindakan[$(this).val()]) != "undefined" ){
                isian = {id:$(this).val(),text:listAsfiksiaTindakan[$(this).val()]};
            }
          return isian;
        }).get();
        if(asfiksiaTindakan.length > 0){
            formData.push({name:'KelahiranBayiForm[list_asfiksia_tindakan]',value:JSON.stringify(asfiksiaTindakan)});
        }
    }
    formData.push({name:'KelahiranBayiForm[tgl_lahir]',value:tgl_lahir+' '+jam_lahir});
    formData.push({name:'KelahiranBayiForm[keterangan_kondisi]',value:keterangan_kondisi});
    formData.push({name:'KelahiranBayiForm[keterangan_asi]',value:keterangan_asi});
    formData.push({name:'KelahiranBayiForm[is_asi]',value:is_asi});
    formData.push({name:'KelahiranBayiForm[kondisi_bayi]',value:kondisi_bayi});

    $(this).docoForm("click", {
        type: 'POST',
        url:'/igd/pemeriksaan-igd/partograf-bayibarulahir?id='+pendaftaran_id + '&kelahiranbayi_id=' + _bayiId,
        data: formData,
        dataType:'json',
        skipSuccessNotif: true,
        beforeSend:function(){
            $('#collapse-bayibarulahir').loadingPanelPartograf('show');
        },
        success:function(res){
            if (typeof res.data != 'undefined' && typeof res.data.messageError != 'undefined') {
                docoNotification('error', 'Terjadi kesalahan', res.data.messageError)
            } else {
                $('#collapse-bayibarulahir').loadingPanelPartograf('clear');
                $('#collapse-bayibarulahir').collapse('hide');
                $('#kelahiranbayiform-pegawai_id').val(null).trigger('change')
                $('#kelahiranbayiform-kamartempattidur_id').val(null).trigger('change')
                table_bayi.draw();
                docoNotification('success', 'Proses Berhasil', 'Data berhasil disimpan')
            }
        },
        error:function(){
            $('#collapse-bayibarulahir').loadingPanelPartograf('clear');
        }
    });
});
$('#batalsimpan-bayi-baru-lahir').off().on('click',function(e){
    $('#collapse-bayibarulahir').collapse('hide');
    _bayiId = 0;
});

$(document).on("click", ".btn-hapus-hipotermi", function() {
    var hipotermi;
    var length = 0;
    var lastHipotermi = $(this).closest(".hipotermi");
    var flag = false;

    lastHipotermi.remove();

    if (lastHipotermi.hasClass("last-hipotermi")) {
        flag = true;
    }

    hipotermi = $(".hipotermi");
    length = hipotermi.length;

    if (length) {
        hipotermi.each(function(index, object) {
            if (index === (length - 1)) {
                $(object).addClass("last-hipotermi");
            }
        });
    }
});