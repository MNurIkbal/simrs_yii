$.fn.stepy.defaults.legend = false;
$.fn.stepy.defaults.transition = "fade";
$.fn.stepy.defaults.duration = 150;
$.fn.stepy.defaults.backLabel =
  '<i class="icon-arrow-left13 position-left"></i> Back';
$.fn.stepy.defaults.nextLabel =
  'Next <i class="icon-arrow-right14 position-right"></i>';


$("#konfig-form").addClass("stepy-basic");
$(".stepy-basic").stepy({
    titleClick: true,
    validate: true,
    block: true,
    next: function(index) {
        var next = true;
        if ($('.opt-tgl').val() == '') {
            var error = '<label class="label label-danger label-roundless">Tanggal berlaku harus di isi</label>';
            $("#error-opt-tgl").html(error);
            next = false;
        }
        if ($(".formula").val() == "") {
            var error = '<label class="label label-danger label-roundless">Formula harus di isi</label>';
            $("#error-formula").html(error);
            next = false;
        
        }
        if ($(".ppn").val() == "") {
            var error = '<label class="label label-danger label-roundless">Persen PPn harus di isi</label>';
            $("#error-ppn").html(error);
            next = false;
            
        }
        if ($(".margin").val() == "") {
            var error = '<label class="label label-danger label-roundless">Persen margin harus di isi</label>';
            $("#error-margin").html(error);
            next = false;
            
        }
        return next;
    },
    finish: function(index) {
        var finish = true;
        if ($(".pembulatan").val() == "") {
            var error = '<label class="label label-danger label-roundless">Pembulatan harga harus di isi</label>';
            $("#error-pembulatan").html(error);
            finish = false;
        }
        if ($(".harga").val() == "") {
            var error = '<label class="label label-danger label-roundless">Harga yang digunakan harus di isi</label>';
            $("#error-harga").html(error);
            finish = false;
        }
        if ($(".metode").val() == "") {
            var error = '<label class="label label-danger label-roundless">Metode antrian stok harus di isi</label>';
            $("#error-metode").html(error);
            finish = false;
        }
        return finish;
    }
});


$(".stepy-basic")
    .find(".button-next")
    .addClass("btn bg-teal-700 btn-huge-next");
$(".stepy-basic")
    .find(".button-back")
    .addClass("btn bg-slate btn-huge-prev pull-left");

$("#konfig-form").submit(function(event) {
    event.preventDefault();

     var formData = new FormData(this);
    //  console.log(data);
    $(this).docoForm("submit", {
        data: formData,
        method: 'post',
        success: function(data) {
            setTimeout(function() {
                // window.location.href = "/master/profil-rumah-sakit";
            }, 1000);
        },
        error: function() {
            setTimeout(() => {
                let count_error = $('#konfig-form-step-0').find('.error').length;
                // console.log(count_error);
                // $('.stepy-basic').stepy();
                if (parseInt(count_error) != 0) {
                    // $('.button-back').trigger('click');
                }
            }, 100);
        }
    });

});


var yesterday = new Date((new Date()).valueOf() - 1000 * 60 * 60 * 24);
$('.pickadate').pickadate({
    format: 'dd mmmm yyyy',
    disable: [{
        from: [0, 0, 0],
        to: yesterday
    }],
    onStart: function () {
        var date = new Date();
        this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
    }
});

$().ready(function() {
    $('select').select2({
        placeholder: {
          id: '', // the value of the option
          text: 'Select an option'
        }
    });

    // $('#kelas_pelayanan').select2('val', ['1', '7']);
});

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * dropdown pagination infinity scroll - tampilan
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */
$(document).ready(function() {
    
    $("#adm_tindakan_id").select2({
        placeholder: "-- Cari Tindakan --",
        minimumInputLength: 0,
        ajax: {
            url: "/master/konfig-system/list-tindakan",
            dataType: "json",
            data: function(params) { 
                return {
                    q:params.term, 
                    page:params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                results: data.result,
                    pagination: {
                        more: data.pagination
                                }
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (markup) { 
                return markup; 
            },
            templateResult: function(object) { 
                return object.text; 
            },
            templateSelection: function (subject) { 
                return subject.text; 
            },
        },
    });

    var konfig_kelompok= $("#konfig_kelompok_tindakan").select2({
        placeholder: "-- Cari Kelompok Tindakan --",
        minimumInputLength: 0,
        ajax: {
            url: "/master/konfig-system/list-kelompok-tindakan",
            dataType: "json",
            data: function(params) { 
                return {
                    q:params.term, 
                    page:params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                results: data.result,
                    pagination: {
                        more: data.pagination
                                }
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (markup) { 
                return markup; 
            },
            templateResult: function(object) { 
                return object.text; 
            },
            templateSelection: function (subject) { 
                return subject.text; 
            },
        },
    });
    var konfigInit = []
    Object.entries(setValKlpTindakan).forEach((val,key) =>{
        if(!konfig_kelompok.find('option:contains(' + val[1] + ')').length){
            newOption = $("<option></option>").val(val[0]).text(val[1])
            konfig_kelompok.append(newOption)
            konfigInit.push(val[0])
        }   
    });

konfig_kelompok.val(konfigInit).trigger("change"); 

    var old_values = [];
    var konfigKlp = $("#konfig_kelompok_tindakan");

    konfigKlp.on("select2:select", function(event) {
      var values = [];
      var value = [];
      selectAll = false
      pilihSemua = "Pilih Semua"
      $(event.currentTarget).find("option:selected").each(function(i, selected){ 
        values[i] = $(selected).text();
        if($(selected).text() == pilihSemua){
            selectAll = true
        }
      });

        var last = $(values).not(old_values).get();
        lastData = (old_values.length == 0) ? last[$(values).length-1] : last
        if(lastData != pilihSemua){
          selectAll = false
        }
        $(event.currentTarget).find("option:selected").each(function(i, selected){ 
            if(selectAll){
                if($(selected).text() != pilihSemua){
                    $('#konfig_kelompok_tindakan option[value="'+$(selected).val()+'"]').remove()
                }
            } else {
                if($(selected).text() == pilihSemua){
                    $('#konfig_kelompok_tindakan option[value="'+$(selected).val()+'"]').remove()
                }
            }
            
        });
      old_values = values;
    });
});

/**
 * pengaturan UI persentase dan daftar tindakan untuk di Show atau di Hide
 */
$(document).ready(function () {
    $(document).on('click', '#default_biaya', function() {
        if ($(this).is(":checked")) {
            $(".persentase-cls").show();
            $(".daftartindakan-cls").show();
        } else {
            $(".persentase-cls").hide();
            $(".daftartindakan-cls").hide();
        }
    });
});

$('#konfigsystemform-expired_time_program_fisio').on('change', function() {
    $('#jumlah-konfig-hari').remove();
    var jumlHari = $(this).val();
    if (jumlHari <= 0) {
        $(this).val(1);
        jumlHari = $(this).val();
        $('#konfigsystemform-expired_time_program_fisio').parent('.input-group').after('<p class="text-error" id="jumlah-konfig-hari" style="color: red">Minimal Hitung Mundur Adalah 1 Hari</p>');
    }
});