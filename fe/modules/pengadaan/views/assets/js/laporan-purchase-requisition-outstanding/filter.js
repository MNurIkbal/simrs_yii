$(document).ready(function() {
    var _type1 = "";
    var _type2 = "";
    var _type3 = "";
    var _params = "is_prcyto="+_type1+"&is_admin="+_type2+"&is_consignment="+_type3;
    $(".legend-information").css("cursor", "pointer");
    $(".legend-information").each(function (params) {
        var _id = $(this).attr("id");

        $(document).on("click", "#" + _id, function () {
            if (typeof $(this).attr("data-type-cito") != "undefined") {
                _type1 = $(this).attr("data-type-cito");
                $("#"+$(this).attr("data-params-cito")+" .legend-information").css("border", "1px solid #dddddd");
                $("#" + _id).css("border", "2px solid #2ca38b");
            }
            if (typeof $(this).attr("data-type-admin") != "undefined") {
                _type2 = $(this).attr("data-type-admin");
                $("#"+$(this).attr("data-params-admin")+" .legend-information").css("border", "1px solid #dddddd");
                $("#" + _id).css("border", "2px solid #2ca38b");
            }
            if (typeof $(this).attr("data-type-consignment") != "undefined") {
                _type3 = $(this).attr("data-type-consignment");
                $("#"+$(this).attr("data-params-consignment")+" .legend-information").css("border", "1px solid #dddddd");
                $("#" + _id).css("border", "2px solid #2ca38b");
            }
            
            _params = "is_prcyto="+_type1+"&is_admin="+_type2+"&is_consignment="+_type3;
            link = link;
            const tableElement = $(`#`+tableName).DataTable()
            showLoader();
            $('.data-pdf').attr('data-url', link+_params+"&");
            tableElement.ajax.url(url+_params).load()
        })
    })

    $(document).on("click", ".data-reset", function () {
        const tableElement = $(`#`+tableName).DataTable()
        showLoader();
        $(".legend-information").css("border", "1px solid #dddddd");
        $("#cito-all").css("border", "2px solid #2ca38b");
        $("#admin-all").css("border", "2px solid #2ca38b");
        $("#consignment-all").css("border", "2px solid #2ca38b");
        _type1 = "";
        _type2 = "";
        _type3 = "";
        _params = "is_prcyto="+_type1+"&is_admin="+_type2+"&is_consignment="+_type3;
            $('.data-pdf').attr('data-url', link+_params+"&");
        tableElement.ajax.url(url+_params).load()
    })
});
