/*
* @Author: afil
* @Date:   2018-01-17 14:27:28
* @Last Modified by:   Naufal
* @Last Modified time: 2018-01-19 10:00:59
*/

/**
 *
 * Block comment
 *
 */
$("#ajax-form").docoForm("submit",{
    success : function(data) {
        if (data.status == 201)
            this.formInput[0].reset();
        table.draw();
    }
});

// Event Delete
$(document).on("click", ".data-delete", function(e) {
    e.preventDefault();
    $(this).docoForm("delete",{
        success : function (data) {
            table.draw()
        }
    });
    return false;
});

// Datepicker
     $(".daterange").daterangepicker({
                applyClass: "bg-slate-600",
                cancelClass: "btn-default",
                locale: {
                    format: "DD MMM YYYY"
                }
      });