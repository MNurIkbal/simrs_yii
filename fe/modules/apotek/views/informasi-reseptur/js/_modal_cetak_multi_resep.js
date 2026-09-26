$(document).ready(function() {

    $(document).on('click', '#tbl-resep-multiple tr', function(){
        var _data = table.row('.selected').data();

        if(typeof _data !== 'undefined'){
            $('#btn-print-multiple-resep').attr('disabled', false);

        }else{
            $('#btn-print-multiple-resep').attr('disabled', true);
        }
    });

    $(document).on('click', '.btn-print-resep-multi', function(e){
        var tableId = $(this).attr('data-table');
        var table = $(tableId).DataTable();
        var tableDatas = table.rows('.selected').data();
        let column = table.data().count();
        var conditions = $(this).attr('data-conditions') ? $(this).attr('data-conditions').split(',') : '';
        let url = $(this).attr('data-target') ? $(this).attr('data-target') : $(this).attr('data-href') ? $(this).attr('data-href') : null;
        var waktu_pemberian = $('.waktu_pemberian').val()
        var keterangan_pemberian = $('.keterangan_pemberian').val()
        $('#data_catatan').val(waktu_pemberian + ' ' + keterangan_pemberian)
        // var first = $("#'. Html::getInputId($model ,'catatan') .'").val();
        var input = (waktu_pemberian + ' ' + keterangan_pemberian).toUpperCase();
        if (tableDatas.length == 0) {
            docoNotification('warning', 'Terjadi Kesalahan', 'Tidak ada Data yang dipilih!');
        } else if (column === 0) {
            docoNotification('warning', 'Terjadi Kesalahan', 'Data Tidak Tersedia!');
        } else {
            var params = ''
            $.each(tableDatas, function (index, valueTable) {
                if(conditions.length > 0) {
                    $.each(conditions, function (index, value) {
                        params += value.trim() + '[]=' + valueTable[value] + '&';
                    });
                }
            })
            var inputParams = 'add_comment=' + input;
            window.open(url + params + inputParams, "_blank");

        }
        setTimeout(function () {
            location.reload();
        }, 1000);
    });

    $(document).on('click','#checkObat',function(e){
        selectAll = ($('#checkObat').is(':checked')) ? true : false
        $(document).find('td.select-checkbox').each(function(){
            if(selectAll){
                if($(this).parent('.selected').length == 0){
                    $(this).trigger('click')
                }
            }
            else {
                if($(this).parent('.selected').length == 1){
                    $(this).trigger('click')
                }
            }
        })
    })
});
