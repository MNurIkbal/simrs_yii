/*
* @Author: afil
* @Date:   2018-01-22 14:34:15
* @Last Modified by:   afil
* @Last Modified time: 2018-03-28 11:21:32
*/

/**
 *
 * Index js function
 *
 */
$("#ajax-form").docoForm("submit",{
    success : function(data) {
        if (data.status == 201)
            this.formInput[0].reset();
        table.draw();
    }
});

// Datepicker
$(".daterange").daterangepicker({
    applyClass: "bg-slate-600",
    cancelClass: "btn-default",
    locale: {
        format: "DD-MMMM-YYYY"
    }
});
