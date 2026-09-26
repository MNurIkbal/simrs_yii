$('.box').css('cursor', 'pointer');

var tempArray = [];
$(document).ready(function (params) {
    var _id = $(this).attr("id");
    var data_type = '';
    $(".filter-legend").on('click', function ({ delegateTarget }) {
        let _attrType = $(delegateTarget).attr("data-type")
        if ($(".filter-legend").find(`[data-type='${_attrType}']`)) {
            var _this = $(this).find('.box')
            if (_this.hasClass("actives")) {
                _this.removeClass('actives');
            } else {
                _this.toggleClass('actives');
            }
        }
        if ($.inArray(_attrType, tempArray) < 0) {
            tempArray.push(_attrType);
        } else {
            tempArray = tempArray.filter(function (item) { return item !== _attrType; });
        }
        data_type = tempArray.toString();
        const tableId = "table-data-kamar";
        const tableElement = $(`#${tableId}`).DataTable()
        showLoader();
        if (data_type == null || data_type == "") {
            tableElement.ajax.url("/ranap/inf-data-kamar/get-data-tempat-tidur").load()
        }
        else {
            tableElement.ajax.url("/ranap/inf-data-kamar/get-data-tempat-tidur?type=" + data_type).load()
        }
    })

    $("#filter_status").on('change', function ({ delegateTarget }) {
        // let _attrType = $(delegateTarget).attr("data-type")
        // console.log( $("#filter_status").val());
        // if ($(".filter_status").find(`[data-type='${_attrType}']`)) {
        //     var _this = $(this).find('.box')
        //     if (_this.hasClass("actives")) {
        //         _this.removeClass('actives');
        //     } else {
        //         _this.toggleClass('actives');
        //     }
        // }
        // if ($.inArray(_attrType, tempArray) < 0) {
        //     tempArray.push(_attrType);
        // } else {
        //     tempArray = tempArray.filter(function (item) { return item !== _attrType; });
        // }
        data_type = $("#filter_status").val();
        const tableId = "table-data-kamar";
        const tableElement = $(`#${tableId}`).DataTable()
        showLoader();
        
        if (data_type == null || data_type == "") {
            tableElement.ajax.url("/ranap/inf-data-kamar/get-data-tempat-tidur-v2").load()
        }
        else {
            tableElement.ajax.url("/ranap/inf-data-kamar/get-data-tempat-tidur-v2?status=" + data_type).load()
        }
    })

})


$(document).on('click', "#btn-reset", function () {
    $(".filter-legend").find('.box').removeClass('actives')
    const tableId = "table-data-kamar";
    const tableElement = $(`#${tableId}`).DataTable()
    showLoader();
    tableElement.ajax.url("/ranap/inf-data-kamar/get-data-tempat-tidur-v2").load()
})









