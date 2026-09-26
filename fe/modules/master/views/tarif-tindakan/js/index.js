$(document).ready(function(){
    $('#content-komponen').docoLoad({
        url: '/master/tarif-tindakan/komponen',
        dataType: 'html',
        success : function(data) {
            // $(" .select2 ").select2();
            $('.pickadate').pickadate({
                    format: 'dd mmm, yyyy',
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd',
                });
            var table_komponen;
        }
    });

    $('#tab-perda').on("click", function(e){
        $('#content-perda').docoLoad({
            url: '/master/tarif-tindakan/perda',
            dataType: 'html',
            success : function(data) {
                // $(" .select2 ").select2();
                $('.pickadate').pickadate({
                    format: 'dd mmm, yyyy',
                    selectMonths: true,
                    formatSubmit: 'yyyy-mm-dd',
                });
                var table_perda;
            }
        });
    });

    $('#tab-tarif').on("click", function(e){
        $('#content-tarif').docoLoad({
            url: '/master/tarif-tindakan/tarif',
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

});
$(document).on('click', '.spa', function(e){
    var _type = $(this).attr('data-type')
    var _content = '#'+$(this).attr('data-content')
    var _target = $(this).attr('data-url') 
    // console.log(_type)
    if(_type == 'save'){
        var _form = '#'+$(this).attr('data-target')
        $(_form).submit()
    }else if(_type == 'edit'){
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
