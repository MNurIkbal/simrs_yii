/*
* @Author: afil
* @Date:   2018-01-12 15:50:34
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-01-25 15:56:45
*/

/**
 *
 * keperluan js index
 *
 */

$(".daterange").daterangepicker({
    applyClass: "bg-slate-600",
    cancelClass: "btn-default",
    locale: {
        format: "DD-MMMM-YYYY"
    }
});

$(".date").pickadate({
    applyClass: "bg-slate-600",
    cancelClass: "btn-default",
    locale: {
        format: "DD/MMMM/YYYY"
    }
});

$(".data-reset").on("click", function (e) {
    e.preventDefault();
    // tabel.reset();
    $(document).ready(function() {
        var today = moment().format('DD-MMMM-YYYY');
        var defaultTgl = today+' - '+today;
        $('input[name="tgl_pendaftaran"]').val(defaultTgl);
    });
});