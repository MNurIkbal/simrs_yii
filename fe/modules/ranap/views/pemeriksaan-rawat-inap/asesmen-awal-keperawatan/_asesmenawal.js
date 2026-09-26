
    $("#form-anamnesa").docoForm("submit",{
        success : function(data) {
            if(data.response.pendaftaran_id){
                $(".btn-cetak-anamnesa").removeClass("disabled").attr("data-id", data.response.pendaftaran_id)
            }
        }
    });
    $(" .select2 ").select2();

    $(document).ready(function(){   
        if(is_perawatasesmenawal == 0 || is_disabled){
            $("#asesmenawalForm :input").not('#cetaksementara-1').prop("disabled", true);
        }
        $('#collapse-riwayat-kesehatan').collapse('show');
        $('.fg_diambildari_nama').hide();
        $('.fg_diambildari_hubungan').hide();
        $('.fg_masuk_denganlain').hide();
        $('.field-asesmenawalform-hasil_rad').hide();
        $('.field-asesmenawalform-hasil_lab').hide();
        $('.field-asesmenawalform-hasil_lainnya').hide();
        $('.field-asesmenawalform-tgl_dirawat').hide();
        $('.field-asesmenawalform-alasan_dirawat').hide();
        $('.field-asesmenawalform-tgl_tindakan').hide();
        $('.field-asesmenawalform-nama_alergi').hide();
        $('.field-asesmenawalform-jeniskegiatantindakan_id').hide();
        $('.field-asesmenawalform-reaksi').hide();
        $('.field-asesmenawalform-penyakit_kel_lain').hide();
        // $(".date").pickadate({
        //     applyClass: "bg-slate-600",
        //     formatSubmit: "yyyy-mm-dd",
        //     cancelClass: "btn-default",
        //     locale: {
        //         format: "DD-MMMM-YYYY"
        //     }
        // });

        $(".datetime").AnyTime_picker({
            format: "%d %M %Y %H:%i:%s",
            monthNames : ["January","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","Nopember","Desember"],
            monthAbbreviations : [ "Jan","Feb","Mar","Apr","Mei","Jun","Jul","Aug","Sep","Okt","Nop","Des" ],
            labelDayOfMonth : "Tanggal",
            labelYear : "Tahun",
            labelMonth : "Bulan",
            labelHour : "Jam",
            labelMinutes: "Menit",
            labelSecond: "Detik",
        });

        if($("input:radio[name='AsesmenAwalForm[asesmen_diambildari]']:checked").val() == '2'){
            $('.fg_diambildari_nama').show();
            $('.fg_diambildari_hubungan').show();
        }
        $('#asesmenawalform-asesmen_diambildari').change(function(){

            var sourceVal = $("input:radio[name='AsesmenAwalForm[asesmen_diambildari]']:checked").val();
            if(sourceVal == '2'){
                $('.fg_diambildari_nama').show();
                $('.fg_diambildari_hubungan').show();
            }else{
                $('.fg_diambildari_nama').hide();
                $('.fg_diambildari_hubungan').hide();
            }
        });

        if($("input:radio[name='AsesmenAwalForm[masuk_dengan]']:checked").val() == '6'){
            $('.fg_masuk_denganlain').show();
        }
        $('#asesmenawalform-masuk_dengan').change(function(){
            var sourceVal = $("input:radio[name='AsesmenAwalForm[masuk_dengan]']:checked").val();
            if(sourceVal == '6'){
                $('.fg_masuk_denganlain').show();
            }else{
                $('.fg_masuk_denganlain').hide();
            }
        });

        if($("input:radio[name='AsesmenAwalForm[hasil_pemeriksaan]']:checked").val() == '1'){
            $('.field-asesmenawalform-hasil_rad').show();
            $('.field-asesmenawalform-hasil_lab').show();
            $('.field-asesmenawalform-hasil_lainnya').show();
        }

        $('#asesmenawalform-hasil_pemeriksaan').change(function(){
            var sourceVal = $("input:radio[name='AsesmenAwalForm[hasil_pemeriksaan]']:checked").val();
            if(sourceVal == '1'){
                $('.field-asesmenawalform-hasil_rad').show();
                $('.field-asesmenawalform-hasil_lab').show();
                $('.field-asesmenawalform-hasil_lainnya').show();
            }else{
                $('.field-asesmenawalform-hasil_rad').hide();
                $('.field-asesmenawalform-hasil_lab').hide();
                $('.field-asesmenawalform-hasil_lainnya').hide();

            }
        });

        if($("input:radio[name='AsesmenAwalForm[pernah_dirawat]']:checked").val() == '1'){
                $('.field-asesmenawalform-tgl_dirawat').show();
                $('.field-asesmenawalform-alasan_dirawat').show();
        }
        $('#asesmenawalform-pernah_dirawat').change(function(){
            var sourceVal = $("input:radio[name='AsesmenAwalForm[pernah_dirawat]']:checked").val();
            if(sourceVal == '1'){
                $('.field-asesmenawalform-tgl_dirawat').show();
                $('.field-asesmenawalform-alasan_dirawat').show();
            }else{
                $('#asesmenawalform-tgl_dirawat').val('');
                $('#asesmenawalform-alasan_dirawat').val('');
                $('.field-asesmenawalform-tgl_dirawat').hide();
                $('.field-asesmenawalform-alasan_dirawat').hide();

            }
        });

        if($("input:radio[name='AsesmenAwalForm[pernah_tindakan]']:checked").val() == '1'){
                $('.field-asesmenawalform-tgl_tindakan').show();
                $('.field-asesmenawalform-jeniskegiatantindakan_id').show();
        }
        $('#asesmenawalform-pernah_tindakan').change(function(){
            var sourceVal = $("input:radio[name='AsesmenAwalForm[pernah_tindakan]']:checked").val();
            if(sourceVal == '1'){
                $('.field-asesmenawalform-tgl_tindakan').show();
                $('.field-asesmenawalform-jeniskegiatantindakan_id').show();
            }else{
                $('#asesmenawalform-tgl_tindakan').val('');
                $('#asesmenawalform-jeniskegiatantindakan_id').val('');
                $('.field-asesmenawalform-tgl_tindakan').hide();
                $('.field-asesmenawalform-jeniskegiatantindakan_id').hide();

            }
        });

        if($("input:radio[name='AsesmenAwalForm[r_alergi]']:checked").val() == '1'){
                $('.field-asesmenawalform-nama_alergi').show();
        }
        $('#asesmenawalform-r_alergi').change(function(){
            var sourceVal = $("input:radio[name='AsesmenAwalForm[r_alergi]']:checked").val();
            if(sourceVal == '1'){
                $('.field-asesmenawalform-nama_alergi').show();
            }else{
                $('.field-asesmenawalform-nama_alergi').hide();

            }
        });

        if($("input:radio[name='AsesmenAwalForm[transfusi]']:checked").val() == '1'){
                $('.field-asesmenawalform-reaksi').show();
        }
        $('#asesmenawalform-transfusi').change(function(){
            var sourceVal = $("input:radio[name='AsesmenAwalForm[transfusi]']:checked").val();
            if(sourceVal == '1'){
                $('.field-asesmenawalform-reaksi').show();
            }else{
                $('.field-asesmenawalform-reaksi').hide();

            }
        });


        $('div#asesmenawalform-r_penyakit_kel input[type=checkbox]').each(function() {
           if ($(this).is(":checked") && $(this).val() == '14') {
                $('.field-asesmenawalform-penyakit_kel_lain').show();
           }
        });
        $('#asesmenawalform-r_penyakit_kel').change(function(){
            var sch_lainnya = false
            $('div#asesmenawalform-r_penyakit_kel input[type=checkbox]').each(function() {
               if ($(this).is(":checked")) {
                    if($(this).val() == '14'){
                        sch_lainnya = true;
                    }
               }
            });
            if(sch_lainnya == true){
                $('.field-asesmenawalform-penyakit_kel_lain').show();
            }else{
                $('.field-asesmenawalform-penyakit_kel_lain').hide();

            }
        });

        $('body').on('click','.btn-tambah-hasil-pemeriksaan',function(){
            var parentEle = $(this).parent().parent().parent();
            var textfieldEle = $(this).closest("div.input-group").find("input[type='text']")[0];
            var jenispemeriksaan = $(textfieldEle).data('pemeriksaan');
            if($(textfieldEle).val().length >= 1){
                var newVal = $(textfieldEle).val();
                var textfieldName = $(textfieldEle).attr('name');
                var newEle = '<div class="input-group">';
                newEle +="<div class='form-control-static'>"+newVal+"</div>";
                newEle +="<input type='hidden' name='AsesmenAwalForm[hasil_"+jenispemeriksaan+"_opsional][]' value='"+newVal+"'/>";
                newEle +="<span class='input-group-btn'><button type='button' class='btn btn-danger btn-remove-hasil-pemeriksaan'>-</button></span>"
                newEle +="</div></div>";
                if(status_disabled == 1){
                    $(".btn-remove-hasil-pemeriksaan").prop("disabled", true);
                }
                $(newEle).prependTo(parentEle);
                $(textfieldEle).val('');
            }
        });

        $('body').find('.tf-hasil-pemeriksaan').each(function(){
            if($(this).val().length >= 1){
                var jenispemeriksaan = $(this).data('pemeriksaan');
                var parentEle = $(this).parent().parent();
                var valEle = $(this).val();
                const regex = /(),()/g;
                if(valEle.match(regex) != null){
                    const listEle = valEle.split(',');
                        $(listEle).each(function(index){
                            var newVal = listEle[index];
                            var newEle = '<div class="input-group">';
                            newEle +="<div class='form-control-static'>"+newVal+"</div>";
                            newEle +="<input type='hidden' name='AsesmenAwalForm[hasil_"+jenispemeriksaan+"_opsional][]' value='"+newVal+"'/>";
                            newEle +="<span class='input-group-btn'><button type='button' class='btn btn-danger btn-remove-hasil-pemeriksaan'>-</button></span>"
                            newEle +="</div></div>";
                            if(status_disabled == 1){
                                $(".btn-remove-hasil-pemeriksaan").prop("disabled", true);
                            }
                            $(newEle).prependTo(parentEle);
                        })
                }else {
                    var newVal = $(this).val();
                    var newEle = '<div class="input-group">';
                    newEle +="<div class='form-control-static'>"+newVal+"</div>";
                    newEle +="<input type='hidden' name='AsesmenAwalForm[hasil_"+jenispemeriksaan+"_opsional][]' value='"+newVal+"'/>";
                    newEle +="<span class='input-group-btn'><button type='button' class='btn btn-danger btn-remove-hasil-pemeriksaan'>-</button></span>"
                    newEle +="</div></div>";
                    if(status_disabled == 1){
                        $(".btn-remove-hasil-pemeriksaan").prop("disabled", true);
                    }
                    $(newEle).prependTo(parentEle);
                }
                $(this).val('');
            }
        });

        if(status_disabled == 1){
            $(".btn-remove-hasil-pemeriksaan").prop("disabled", true);
        }
        
        $('body').on('click','.btn-remove-hasil-pemeriksaan',function(){
            $(this).closest("div.input-group").remove();
        });        

        $('.field-group-bb-turun').hide();
        if($("input:radio[name='SkriningGiziForm[bb_ygdirencanakan]']:checked").val() == '2'){
            $('.field-group-bb-turun').show();
        }
        $('#skrininggiziform-bb_ygdirencanakan').change(function(){
            var sourceVal = $("input:radio[name='SkriningGiziForm[bb_ygdirencanakan]']:checked").val();
            if(sourceVal == '2'){
                $('.field-group-bb-turun').show();
            }else{
                $('.field-group-bb-turun').hide();
            }
            $("input:radio[name='SkriningGiziForm[bb_turun]']").prop('checked',false);
        });
        if($('#skrininggiziform-skor').val() != ''){
            $('#skor_skrining_gizi').html($('#skrininggiziform-skor').val());
        }
        $('#skrininggiziform-bb_ygdirencanakan,#skrininggiziform-bb_turun,#skrininggiziform-porsi_makan,#skrininggiziform-sakit_berat').change(function(){
            var val_bbturun = $("input:radio[name='SkriningGiziForm[bb_turun]']:checked").val();
            if(val_bbturun != undefined){
                val_bbturun = skor_gizi_bbturun[val_bbturun];
            }else{
                val_bbturun = 0;
            }
            var val_porsimakan = $("input:radio[name='SkriningGiziForm[porsi_makan]']:checked").val();
            if(val_porsimakan != undefined){
                val_porsimakan = skor_gizi_porsi[val_porsimakan];
            }else{
                val_porsimakan = 0;
            }
            var val_sakit_berat = $("input:radio[name='SkriningGiziForm[sakit_berat]']:checked").val();
            if(val_sakit_berat != undefined){
                val_sakit_berat = skor_gizi_sakitberat[val_sakit_berat];
            }else{
                val_sakit_berat = 0;
            }
            var jml_skor = parseInt(val_bbturun) + parseInt(val_porsimakan) + parseInt(val_sakit_berat);
            $('#skor_skrining_gizi').html(jml_skor);
            $('#skrininggiziform-skor').val(jml_skor);
        });
    });
    $(function(){
        $(".ddl_diagnosis_masuk").select2({
            minimumInputLength: 3,
            allowClear:true,
            placeholder:"Pilih",
            ajax: {
                url: "/ranap/pemeriksaan-rawat-inap/get-diagnosis-masuk",
                dataType: "json",
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
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });


        $.fn.stepy.defaults.legend = false;
        $.fn.stepy.defaults.transition = 'fade';
        $.fn.stepy.defaults.duration = 150;
        $.fn.stepy.defaults.backLabel = 'Kembali <b><i class=\'fa fa-chevron-left\'></i></b>';
        $.fn.stepy.defaults.nextLabel = 'Selanjutnya <b><i class=\'fa fa-chevron-right\'></i></b>';
        $('#asesmenawalForm').stepy({
            titleClick: true
        });
        $('#asesmenawalForm').find('.button-next').addClass('btn btn-xs btn-labeled btn-info');
        $('#asesmenawalForm').find('.button-back').addClass('btn btn-xs btn-labeled btn-info');
        
        $('#simpansementara-1').on('click',function(){
            var field_name = $(this).closest('fieldset').data('name');
            if($(this).closest('fieldset').find('input').length >= 1){


                var is_obatdarirumah = false;
                if($("input[name='AsesmenAwalForm[obat_darirumah]']:checked").val() == 1){
                    is_obatdarirumah = true;
                }
                var tab_rekon = $(document).find('#tab-ranap').find('li#tab-rekonsobat');

                var formdata = $(this).closest('fieldset').serializeArray();

                // var _diagnosamasuk = $('#asesmenawalform-diagnosa_masuk').val()+'_'+$('#asesmenawalform-diagnosa_masuk option:selected').text();
                
                formdata.push({name: 'submit-sementara', value: '1'});
                formdata.push({name: 'field-name', value: field_name});
                // formdata.push({name: 'diagnosa_masuk', value: _diagnosamasuk});
                $.ajax({
                    url:"/ranap/pemeriksaan-rawat-inap/asesmenawal?id="+pendaftaran_id+"&pasien_id="+pasien_id,
                    data: formdata,
                    type: "POST",
                    success: function(res){
                        if(is_obatdarirumah == true){
                            if(tab_rekon.hasClass('hidden')){
                                tab_rekon.removeClass('hidden');
                            }
                        }
                        docoNotification('success', 'Proses Berhasil', 'Data telah disimpan');
                    },
                    error: function(){
                        docoNotification('error','Proses Gagal','Terjadi Kesalahan');
                    }
                });
            }
        });
        $('#simpansementara-2').on('click',function(){
            var field_name = $(this).closest('fieldset').data('name');
            if($(this).closest('fieldset').find('input').length >= 1){
                var formdata = $(this).closest('fieldset').serializeArray();
                formdata.push({name: 'submit-sementara', value: '1'});
                $.ajax({
                    url:"/ranap/pemeriksaan-rawat-inap/simpan-skrining-gizi?id="+pendaftaran_id,
                    data: formdata,
                    type: "POST",
                    success: function(res){
                        docoNotification('success', 'Proses Berhasil', 'Data telah disimpan');
                    },
                    error: function(){
                        docoNotification('error','Proses Gagal','Terjadi Kesalahan');
                    }
                });
            }
        });
        $('#asesmenawalForm').on('beforeSubmit', function(e){
            var fieldset_asmenriwayat = $(document).find('fieldset[data-name="asmen_riwayat"]');
            var fieldset_skrininggizi = $(document).find('fieldset[data-name="skrining-gizi"]');
            
            var formData_asmenriwayat = fieldset_asmenriwayat.serializeArray();
            var formData_skrininggizi = fieldset_skrininggizi.serializeArray();

            var formData = [];
            formData = formData.concat(formData_asmenriwayat);
            formData = formData.concat(formData_skrininggizi);

            $(this).docoForm('click',{
                url : baseUrl+"ranap/pemeriksaan-rawat-inap/simpan-awal-keperawatan?id="+pendaftaran_id,
                method : "POST",
                type : "json",
                data: formData,
                success: function(data){
                    docoNotification('success', 'Proses Berhasil', 'Data telah disimpan');
                        var btn_cetak = $('#cetaksementara-1');
                        if(btn_cetak.hasClass('hidden')){
                            btn_cetak.removeClass('hidden');
                        }
                        location.reload();
                },
                error:function(){
                    docoNotification('error','Proses Gagal','Terjadi Kesalahan');
                }
            })
        }).on('submit', function(e){
            e.preventDefault();
        });

        $('#cetaksementara-1').on('click',function(e){
            e.preventDefault();
            var url="/ranap/pemeriksaan-rawat-inap/cetak-pdf-asesmen-awal?id="+pendaftaran_id+"&pasien_id="+pasien_id;
            window.open(url, '_blank');
        });
        
    });
