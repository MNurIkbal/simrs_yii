$(document).ready(function(){
    $('#content-group-margin').docoLoad({
        url: '/master/margin-harga/group-margin',
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

    $('#tab-group-margin').on("click", function(e){
        if($("#table-group-margin").length==0){
            $('#content-group-margin').docoLoad({
                url: '/master/margin-harga/group-margin',
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
            
        }
    });
    
    $('#tab-margin-harga-obat').on("click", function(e){
        if($("#table-margin-harga-obat").length==0){
            $('#content-margin-harga-obat').docoLoad({
                url: '/master/margin-harga/margin-harga-obat',
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
        }
    });
    
    $('#tab-margin-khusus').on("click", function(e){
        if($("#table-margin-khusus").length==0){
            $('#content-margin-khusus').docoLoad({
                url: '/master/margin-harga/margin-khusus',
                dataType: 'html',
                success : function(data) {
                    // $(" .select2 ").select2();
                    $('.pickadate').pickadate({
                        format: 'dd mmm, yyyy',
                        selectMonths: true,
                        formatSubmit: 'yyyy-mm-dd',
                    });
                    var table_khusus;
                }
            });
        }
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

var tableTemp;

$(document).on("click",".addrow-margin-khusus",function(event) {
    event.preventDefault();
    var isAllValid= true;
    $.each(tableTemp.$('.dropdown-jenisobatalkes'),function(){
        if($(this).val() == ''){
            $(this).closest("tr").css("background","#ff000047");
            isAllValid = false;
        }
    });
    if(isAllValid == false){
        return false;
    }
    var length = tableTemp.$('.dropdown-jenisobatalkes').length;
    var selectjenis =tableTemp.$('.dropdown-jenisobatalkes')[0].outerHTML; 
    var htmlSelect = $(selectjenis).attr('name','MarginKhususDetailForm['+length+'][jenisobat_id]');
    tableTemp.row.add({
        'no':'',
        'jenisobatalkes':'<div>'+htmlSelect[0].outerHTML+'<span class="help-block"></span></div>',
        'margin':'<div><input type="text" name="MarginKhususDetailForm['+length+'][margin]" class="text-right form-control margin" onchange="persenMarginKhusus( this );"><span class="help-block"></span></div>',
        'action':'<button type="button" class="btn btn-danger btn-sm btn-deletes-khusus"><i class="fa fa-trash"></i></button>'
    }).draw(false);
    $('.dropdown-jenisobatalkes').select2();
});

function persenMarginKhusus(res){
    var val = $(res).val().toString();
    var value = docoHelper.convertToDecimal(val, '.', ',' , true);
    $(res).val(value);
}

$(document).on("click", ".btn-deletes-khusus", function(event) {
    tableTemp.row($(this).parents('tr')).remove().draw( false );
});