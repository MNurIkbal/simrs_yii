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

    $(".datetime").AnyTime_picker({
        format: "%d-%m-%Y %H:%i",
        earliest: new Date(),
        latest: new Date(new Date().setHours(23, 59, 59, 999))
    });

    
    $('#cari_kamar').on('click',function(e){
        var ruangan_id = $('#ruangan_id').val();
        var kelas_id = $('#infopasienranapform-kelaspelayanan_id').val();
        var jenis_id = $('#infopasienranapform-jeniskasuspenyakit_id').val();
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

    $("#btn-pindah-kamar").on('click', function (event) {
        event.preventDefault();
        var dataPost = $("#pindah-kamar-form").serializeArray();
        $(this).docoForm("click", {
            data: dataPost,
            method: 'post',
            success: function (data) {
                setTimeout(function () {
                    window.location.href = "/ranap/inf-pasien-ranap"
                }, 1000);
            }
        });
    });

});