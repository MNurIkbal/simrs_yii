$(document).ready(function() {
    var _type1 = "";
    var _type2 = "";
    var _type3 = "";
    var _params = "is_prcyto="+_type1+"&is_admin="+_type2;
    if(!isBarang) {
        _params += "&is_consignment="+_type3;
    }

    $(".legend-information").css("cursor", "pointer");
    $(".legend-information").each(function (params) {
        var _id = $(this).attr("id");

        $(document).on("click", "#" + _id, function () {
            if (typeof $(this).attr("data-type-cito") != "undefined") {
                _type1 = $(this).attr("data-type-cito");
                $("#"+$(this).attr("data-params-cito")+" .legend-information").removeClass("selected-btn-group");
                $("#" + _id).addClass("selected-btn-group");
            }
            if (typeof $(this).attr("data-type-admin") != "undefined") {
                _type2 = $(this).attr("data-type-admin");
                $("#"+$(this).attr("data-params-admin")+" .legend-information").removeClass("selected-btn-group");
                $("#" + _id).addClass("selected-btn-group");
            }

            if (typeof $(this).attr("data-type-consignment") != "undefined") {
                _type3 = $(this).attr("data-type-consignment");
                $("#"+$(this).attr("data-params-consignment")+" .legend-information").removeClass("selected-btn-group");
                $("#" + _id).addClass("selected-btn-group");
            }
            
            _params = "is_prcyto="+_type1+"&is_admin="+_type2;
            if(!isBarang) {
                _params += "&is_consignment="+_type3;
            }
            const tableElement = $(`#`+tableName).DataTable()
            showLoader();
            $('.data-excel').attr('data-url', url+excel+_params+"&");
            tableElement.ajax.url(url+get+_params).load()
        })
    })

    $(document).on("click", ".data-reset", function () {
        const tableElement = $(`#`+tableName).DataTable()
        showLoader();
        $(".legend-information").removeClass("selected-btn-group");
        $("#cito-all").addClass("selected-btn-group");
        $("#admin-all").addClass("selected-btn-group");
        $("#consignment-all").addClass("selected-btn-group");
        _type1 = "";
        _type2 = "";
        _type3 = "";
        _params = "is_prcyto="+_type1+"&is_admin="+_type2;
        if(!isBarang) {
            _params += "&is_consignment="+_type3;
        }
        $('.data-excel').attr('data-url', url+excel+_params+"&");
        tableElement.ajax.url(url+get+_params).load()
    })
});
