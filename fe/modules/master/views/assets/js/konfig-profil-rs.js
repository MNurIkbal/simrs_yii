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
        // data: $("#konfig-form").serializeArray(),
        dataType: false, // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        method: 'post',
        isUpload: true,
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