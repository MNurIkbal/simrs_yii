jQuery(function($) {
    $(document).on('click', '.btn-ganti-tindakan', function() {
        $(this).closest('tr').toggleClass('strikeout');
        var thisTr = $(this).closest('tr');
        var dataCount = $(this).closest('tr').data('count');
        var tr_instruksitindakanid = $(this).closest('tr').data('id_instruksi_tindakan');
        var newInput = '';
        newInput += '<input class="is_ubah_deleted" type="hidden" name="InstruksiTindakanForm['+dataCount+'][is_ubah_deleted]" value="1" readonly="readonly">';
        if($(this).closest('tr').find('input.is_ubah_deleted').length === 0){
            $(this).closest('tr').append(newInput);
        }else{
            $(this).closest('tr').find('input.is_ubah_deleted').remove();
        }

        update_daftar_tindakan();

        $(document).find('.tabel-bmhp > tbody > tr').each(function() {
            if($(this).data('id_instruksi_tindakan') != "0"){
                if($(this).data('id_instruksi_tindakan') == tr_instruksitindakanid){
                    $(this).data('id_instruksi_tindakan',0);
                    $(this).children('td.namatindakan').text('-');
                    $(this).children('input.id_instruksi_tindakan').val(0);
                    $(this).children('input.daftartindakan_id').val('');
                }
            }
        });

    });
    // On click btn remove
    $(document).on('click', '.btn-ganti-bmhp', function() {
        // Get obat id
        $(this).closest('tr').toggleClass('strikeout');
        var thisTr = $(this).closest('tr');
        var id = $(this).closest('tr').find(".obatalkes_id").val();
        var jumlah = $(this).closest('tr').find(".qty").val();
        var ruangan_id = $("#ruangan_id").val();
        var dataCount = $(this).closest('tr').data('count');
        var newInput = '';
        newInput += '<input class="is_ubah_deleted" type="hidden" name="InstruksiTindakanBmhpForm['+dataCount+'][is_ubah_deleted]" value="1" readonly="readonly">';
        if($(this).closest('tr').find('input.is_ubah_deleted').length === 0){
            $(this).closest('tr').append(newInput);
        }else{
            $(this).closest('tr').find('input.is_ubah_deleted').remove();
        }

        // Numbering
        $(document).find('.td-no-bmhp').each(function(index) {
            // Assign number
            $(this).text(index+1);
        });

    });

});

$(document).ready(function(){
  $("#terra-medik-soap").click(function(){
    $("#terra-medik-soap").attr("action", "/igd/riwayat-pasien/modal-history-terra-medik?pasien_id="+pasien_id);
  });

    var offsetTopNavTabs = $("#nav-sticky").offset().top;
    var heightNav = $($(".navbar-position")[0]).height();
    var patientHeight = $($('.patient-informations')[0]).height()
    var patientOffset = $($('.patient-informations')[0]).offset().top
    $(document).scroll(function () {
        var scrollTop = $(document).scrollTop();

        if ( (scrollTop + patientHeight) > patientOffset) {
            $('.patient-informations').addClass('floating-sticky')
        } else {
            $('.patient-informations').removeClass('floating-sticky')
        }

        if (scrollTop + heightNav >= offsetTopNavTabs) {
            $("#nav-sticky").addClass("nav-tabs__sticky");
        } else {
            $("#nav-sticky").removeClass("nav-tabs__sticky");
        }
    });
    // initial collapsed
    $('.can-expanded').each(function(){
        toggleExpandRiwayat($(this));
    })

    $('.expand-data').on('click', function(e){
        e.preventDefault();
        if(!$(this).attr('expanded')){
            $(this).html('Ringkaskan..');
            $(this).attr('expanded', true);
        }else{
            $(this).html($(this).attr('data-text'));
            $(this).removeAttr('expanded');
        }
        let elementExpand = $(this).siblings('.can-expanded');
        toggleExpandRiwayat(elementExpand);
    });

    function toggleExpandRiwayat(elementTarget){
        let expandElement = elementTarget.children();
        if(expandElement.length > 3){
            expandElement.each(function(index, element){
                if(index > 1){
                    if($(this).css('display') == 'none'){
                        $(this).show();
                    }else{
                        $(this).hide();
                    }
                }
            })
        }
    }
});
