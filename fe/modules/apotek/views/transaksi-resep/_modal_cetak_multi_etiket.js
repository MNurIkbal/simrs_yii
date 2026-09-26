$(document).ready(function() {

    $(document).on('click', '#tbl-resep-multiple-etiket tr', function(){
        var _data = table.row('.selected').data();
        var _datas = table.rows('.selected').data();

        if(typeof _data !== 'undefined'){
            _arraydatas = _datas.toArray();
            const firstValue = _arraydatas[0].is_oral;
            const allSame = _arraydatas.every(item => item.is_oral === firstValue);
            if(allSame) {
                $('#btn-print-multiple-etiket').attr('disabled', false);
            }else{
                $('#btn-print-multiple-etiket').attr('disabled', true);
            }

        }else{
            $('#btn-print-multiple-etiket').attr('disabled', true);
        }
    });

    $(document).on('click', '#btn-print-multiple-etiket', function(e){
        var tableId = $(this).attr('data-table');
        var table = $(tableId).DataTable();
        var tableDatas = table.rows('.selected').data();
        let column = table.data().count();
        var conditions = $(this).attr('data-conditions') ? $(this).attr('data-conditions').split(',') : '';
        let url = $(this).attr('data-target') ? $(this).attr('data-target') : $(this).attr('data-href') ? $(this).attr('data-href') : null;
        if (tableDatas.length == 0) {
            // docoNotification('warning', 'Terjadi Kesalahan', 'Tidak ada Data yang dipilih!');
        } else if (column === 0) {
            // docoNotification('warning', 'Terjadi Kesalahan', 'Data Tidak Tersedia!');
        } else {
            _arraydatas = tableDatas.toArray();
            const firstValue = _arraydatas[0].is_oral;
            const allSame = _arraydatas.every(item => item.is_oral === firstValue);
            if(allSame) {
                var params = ''
                $.each(tableDatas, function (index, valueTable) {
                    if(conditions.length > 0) {
                        $.each(conditions, function (index, value) {
                            params += value.trim() + '[]=' + valueTable[value] + '&';
                        });
                    }
                })
                window.open(url + params , "_blank");
            } else {
                // docoNotification('warning', 'Proses Gagal', 'Jenis Obat Berbeda!');
            }

        }
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
