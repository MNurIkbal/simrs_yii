    $(".date").pickadate({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });



    $("#form-anamnesa").docoForm("submit",{
        success : function(data) {
            // if (data.status == 201 || )
            // this.formInput[0].reset();
            if(data.response.pendaftaran_id){
                $(".btn-cetak-anamnesa").removeClass("disabled").attr("data-id", data.response.pendaftaran_id)
            }
        }
    });

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
        $.fn.stepy.defaults.backLabel = '<i class="icon-arrow-left13 position-left"></i> Back';
        $.fn.stepy.defaults.nextLabel = 'Next <i class="icon-arrow-right14 position-right"></i>';
        $('#asesmenawalForm').stepy();
        
    });

        // $('#asesmenawalWizard').wizard()
        // .on('actionclicked.fu.wizard', function(e, data) {
        //     var fv         = $('#asesmenawalForm').data('formValidation'),
        //         step       = data.step,
        //         $container = $('#asesmenawalForm').find('.step-pane[data-step="' + step +'"]');

        // })
        // .on('finished.fu.wizard', function(e) {
        //     var fv         = $('#asesmenawalForm').data('formValidation'),
        //         step       = $('#asesmenawalWizard').wizard('selectedItem').step,
        //         $container = $('#asesmenawalForm').find('.step-pane[data-step="' + step +'"]');

        //     fv.validateContainer($container);

        //     var isValidStep = fv.isValidContainer($container);
        //     if (isValidStep === true) {
        //         $('#thankModal').modal();
        //     }
        // });