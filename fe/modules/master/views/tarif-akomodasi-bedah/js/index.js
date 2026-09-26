$(document).ready(function(){
    $('#content-tarif').docoLoad({
        url: '/master/tarif-akomodasi-bedah/tarif',
        dataType: 'html',
        success : function(data) {
            // $(" .select2 ").select2();
            $('.pickadate').pickadate({
                format: 'dd mmm, yyyy',
                selectMonths: true,
                formatSubmit: 'yyyy-mm-dd',
            });
            var table_tarif;
        }
    });
});
$(document).on('click', '.spa', function(e){
    var _type = $(this).attr('data-type')
    var _content = '#'+$(this).attr('data-content')
    var _target = $(this).attr('data-url') 
    if(_type == 'edit'){
        var tableId = $(this).attr('data-table');
        var table = $(tableId).DataTable();
        var tableData = table.row('.selected').data();

        if (typeof tableData !== 'undefined') {
            var primaryId = tableData.primary;
            if (typeof primaryId !== 'undefined') {
                $(_content).docoLoad({
                    url: _target+primaryId,
                    dataType: 'html',
                    success : function(data) {
                        // $(" .select2 ").select2();
                        $('.pickadate').pickadate({
                            format: 'dd mmm, yyyy',
                            selectMonths: true,
                            formatSubmit: 'yyyy-mm-dd',
                        });
                    }
                });
            }
        } else {
            docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Belum ada data yang dipilih!"));
            return false;
        }
    }else if(_type == 'delete'){
        var tableId = $(this).attr('data-table');
        var table = $(tableId).DataTable();
        var tableData = table.row('.selected').data();

        if (typeof tableData !== 'undefined') {
            var primaryId = tableData.primary;
            if (typeof primaryId !== 'undefined') {
                $(_content).docoLoad({
                    url: _target+primaryId,
                    dataType: 'html',
                    success : function(data) {
                        // $(" .select2 ").select2();
                        $('.pickadate').pickadate({
                            format: 'dd mmm, yyyy',
                            selectMonths: true,
                            formatSubmit: 'yyyy-mm-dd',
                        });
                    }
                });
            }
        } else {
            docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Belum ada data yang dipilih!"));
            return false;
        }
    }else{
        $(_content).docoLoad({
            url: _target,
            dataType: 'html',
            success : function(data) {
                // $(" .select2 ").select2();
                $('.pickadate').pickadate({
                    format: 'dd mmm, yyyy',
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd',
                });
            }
        });
    }
})
