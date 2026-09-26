
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

"use strict";

$(".aja").on("change click keyup ", function () {
    var _this       = $(this);
    var rotateClock = _this.val();
    var _val = $(this).val();
    var tarif_inacbg = $(".tarif_inacbg").html();
    var sub_total = $(".sub_total").html();
    var primary = $(this).attr('data-primary');
    var subtotal = docoHelper.convertToAngka($("#sub_total_"+primary).html());

    if(rotateClock == 0 || _val < 0) {
        rotateClock = 0;
        $(this).val(0);
    }

    if (rotateClock > 100){
        rotateClock = 100;
        $(this).val(100);
    }

    if (rotateClock == "00" || rotateClock == ""){
        rotateClock = 0;
        $(this).val(0);
    }
    if (rotateClock == "01"){
        rotateClock = 1;
        $(this).val(1);
    }
    if (rotateClock == "02"){
        rotateClock = 2;
        $(this).val(2);
    }
    if (rotateClock == "03"){
        rotateClock = 3;
        $(this).val(3);
    }
    if (rotateClock == "04"){
        rotateClock = 4
        $(this).val(4);
    }
    if (rotateClock == "05"){
        rotateClock = 5;
        $(this).val(5);
    }
    if (rotateClock == "06"){
        rotateClock = 6;
        $(this).val(6);
    }
    if (rotateClock == "07"){
        rotateClock = 7;
        $(this).val(7);
    }
    if (rotateClock == "08"){
        rotateClock = 8;
        $(this).val(8);
    }
    if (rotateClock == "09"){
        rotateClock = 9;
        $(this).val(9);
    }

    if(rotateClock == 0) {
        var totalPersen = 0;
        var persen = 0;
        var hasilPersen = 0;
        docoNotification('warning', "Peringatan", "Persentase tidak boleh 0. Minimal 1");
    }
    else {
        hasilPersen = (parseInt(_val) /100) * docoHelper.convertToAngka(tarif_inacbg);
        totalPersen = (docoHelper.convertToAngka(subtotal)/hasilPersen) * 100;
        totalPersen = Math.ceil(totalPersen);
    }
    
    $("#persenTagihan-"+primary).html(' ('+totalPersen+'%)');
    $("#persen-inacbg-"+primary).html(docoHelper.convertToRupiah(hasilPersen));

    var _parent     = $(_this.closest(".dashboard"));
    var rangeMeter  = $(_parent.find("input.rang-number")); 
    var rangeShow   = $(_parent.find(".meter-clock")); 
    var persen = (totalPersen >= 100) ? 100 : (-90 + totalPersen * 180 / 100);
    
    rangeMeter.val(rotateClock + "%");
    rangeShow.attr("style","transform:rotate(" + persen + "deg)");
});

$(".aja").on("change", function(){
    var sum = 0;
    var primary = $(this).attr('data-primary');

    $(".aja").each(function(){
        if(!isNaN(this.value) && this.value.length != 0) {
            sum += parseInt(this.value);
        }
    });

    if(isNaN(this.value) || this.value == 0 || this.value < 0) {
        $(this).val(0);
        $("#persenTagihan-"+primary).html(' (0%)');
        $(this).focus();
    }
    else {
        if(sum > 100) {
            docoNotification('warning', "Peringatan", "Maaf Persentase sudah melebihi 100%");
            $(this).val(0);
            $("#persenTagihan-"+primary).html(' (0%)');
            $(this).focus();
        }
        else {
            var _val = $(this).val();
            var subtotal = $(this).attr('data-subtotal');
            var pendaftaran_id = $('.pendaftaran_id').val();
            var pasienadmisi_id = $('.pasienadmisi_id').val();

            $.ajax({
                url: '/penjamin-asuransi/monitoring-pasien-bpjs/update-persen',
                type: 'GET',
                data: {
                    persen: _val,
                    subtotal: subtotal,
                    monitorbpjs_id: primary,
                    pendaftaran_id: pendaftaran_id,
                    pasienadmisi_id: pasienadmisi_id,
                },
                success: function () {
                    docoNotification('success', "Berhasil", "Persentase Berhasil di Update.");
                }
            })
        }
    }
})

$(document).ready(function(){
    var tarif_inacbg = docoHelper.convertToAngka($(".tarif_inacbg").html());
    var sum = 0;
    
    $('.aja').each(function(){
        var _val = ($(this).val() == '') ? 0 : $(this).val();
        var _this = $(this);
        var primary = $(this).attr('data-primary');
        var subtotal = docoHelper.convertToAngka($("#sub_total_"+primary).html());
        var hasilPersen = (_val == 0) ? 0 : (parseInt(_val) /100) * tarif_inacbg;
        var totalPersen = (hasilPersen == 0) ? 0 : (docoHelper.convertToAngka(subtotal)/hasilPersen) * 100;

        totalPersen = Math.ceil(totalPersen);
        $("#persenTagihan-"+primary).html(' ('+totalPersen+'%)');
        $("#persen-inacbg-"+primary).html(docoHelper.convertToRupiah(hasilPersen));

        var _parent     = $(_this.closest(".dashboard"));
        var rangeMeter  = $(_parent.find("input.rang-number")); 
        var rangeShow   = $(_parent.find(".meter-clock"));

        rangeMeter.val(_val + "%");
        if(totalPersen >= 100) {
            rangeShow.attr("style","transform:rotate(" + 100 + "deg)");
        }
        else {
            rangeShow.attr("style","transform:rotate(" + (-90 + totalPersen * 180 / 100) + "deg)");
        }
    });
})