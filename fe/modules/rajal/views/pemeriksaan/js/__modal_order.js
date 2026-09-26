$(document).ready( function() {
    if( _listpemeriksaanpenunjang.length ){
        $.each(_listpemeriksaanpenunjang, (k,v) => {
            $(`#${v.daftartindakan_id}`).prop('checked', true)
        })
    }
    $('.cb_penunjang').uniform()
})
$('.cb_penunjang').on('click', function(){
    let dataPost = []
    let _checkboxData = $(this).data()
    $.each(_checkboxData, (k,v) => {
        if(v == ''){
            _checkboxData[k] = null
        }
        if(k == 'is_cyto') {
            _checkboxData[k] = false
        }
    })
    if($(this).prop('checked') == true){
        if (_listpemeriksaanpenunjang.length && _listpemeriksaanpenunjang.find(_obj => _obj.daftartindakan_id == _checkboxData.daftartindakan_id)) {
            return true
        }
        _listpemeriksaanpenunjang.push(_checkboxData)
        let _tarifCyto = parseFloat(_checkboxData.harga_tariftindakan) * (parseFloat(_checkboxData.persencyto_tindakan) / 100)
        let _html = `
        <tr class='order-row-${_checkboxData.daftartindakan_id}'>
                <td></td>
                <td>${_checkboxData.jenispemeriksaanlab_nama}</td>
                <td>${_checkboxData.daftartindakan_nama}</td>
                <td class="text-right">Rp. ${docoHelper.convertToRupiah(_checkboxData.harga_tariftindakan)}</td>
                <td>
                    <input type='checkbox' class='check-uniform update-cyto' data-daftartindakan_id='${_checkboxData.daftartindakan_id}'>
                </td>
                <td class="text-right col-harga-cyto">Rp. 0</td>
                <td class="text-right col-total-harga">${'Rp. ' + docoHelper.convertToRupiah(_checkboxData.harga_tariftindakan)}</td>
                <td>
                    <button style="margin-bottom: 5px; margin-top: 0" class='btn btn-danger btn-xs btn-delete-item' data-daftartindakan_id='${_checkboxData.daftartindakan_id}' type='button'><i class='fa fa-trash'></i></button>
                </td>
        </tr>`
        $('#modal-lab').find('#tbl-order-penunjang tbody').find('.no-data-row').remove()
        $('#modal-lab').find('#tbl-order-penunjang tbody').append(_html)
        $(`.order-row-${_checkboxData.daftartindakan_id}`).find('.check-uniform').uniform()

        $(`.order-row-${_checkboxData.daftartindakan_id}`).find('.update-cyto').bind('click', ({delegateTarget}) => {
            const {daftartindakan_id} = $(delegateTarget).data()
            let _index = _listpemeriksaanpenunjang.findIndex(_obj => _obj.daftartindakan_id == daftartindakan_id)
                _tarifCyto = 0
                _totalTarif = parseFloat(_listpemeriksaanpenunjang[_index].harga_tariftindakan)
            _listpemeriksaanpenunjang[_index].is_cyto = $(delegateTarget).prop('checked')
            if( $(delegateTarget).prop('checked') ){
                _tarifCyto = _totalTarif * (parseFloat(_listpemeriksaanpenunjang[_index].persencyto_tindakan) / 100)
                _totalTarif += _tarifCyto
            }
            $('#modal-lab').find('#tbl-order-penunjang tbody').find(`.order-row-${daftartindakan_id}`).find('.col-harga-cyto').html(`Rp. ${docoHelper.convertToRupiah(_tarifCyto)}`)
            $('#modal-lab').find('#tbl-order-penunjang tbody').find(`.order-row-${daftartindakan_id}`).find('.col-total-harga').html(`Rp. ${docoHelper.convertToRupiah(parseFloat(_totalTarif))}`)
            orderSummary()
        })
        $(`.order-row-${_checkboxData.daftartindakan_id}`).find('.btn-delete-item').bind('click', ({delegateTarget}) => {
            const {daftartindakan_id} = $(delegateTarget).data()
            let _index = _listpemeriksaanpenunjang.findIndex(_obj => _obj.daftartindakan_id == daftartindakan_id)
            _listpemeriksaanpenunjang.splice(_index, 1)
            $('#modal-lab').find('#tbl-order-penunjang tbody').find(`.order-row-${daftartindakan_id}`).remove()
            if(_listpemeriksaanpenunjang.length == 0) {
                $('#modal-lab').find('#tbl-order-penunjang tbody').append(`
                    <tr>
                        <td colspan="8" class="text-center no-data-row">Belum Ada Data yang Diinputkan</td>
                    </tr>
                `)
            }
            orderSummary()
        })
    } else {
        $(this).closest('td').removeClass('selected')
        let _index = _listpemeriksaanpenunjang.findIndex(obj => obj.daftartindakan_id == _checkboxData.daftartindakan_id)
        _listpemeriksaanpenunjang.splice(_index, 1)
        $('#modal-lab').find('#tbl-order-penunjang tbody').find(`.order-row-${_checkboxData.daftartindakan_id}`).remove()
        if(_listpemeriksaanpenunjang.length == 0) {
            $('#modal-lab').find('#tbl-order-penunjang tbody').append(`
                <tr>
                    <td colspan="8" class="text-center no-data-row">Belum Ada Data yang Diinputkan</td>
                </tr>
            `)
        }
    }
    orderSummary()
    return true
})

var orderSummary = () => {
    let _total = 0
    $.each(_listpemeriksaanpenunjang, (k,v) => {
        let _tarif = parseFloat(v.harga_tariftindakan)
            _tarifCyto = v.is_cyto ? _tarif * (parseFloat(v.persencyto_tindakan) / 100) : 0
            _totalTarif = v.is_cyto ? _tarif + _tarifCyto : _tarif
        _total += _totalTarif
    })
    $('#modal-lab').find('#tbl-order-penunjang tfoot').find('.order-summary').html(`<b>Rp. ${docoHelper.convertToRupiah(_total)}</b>`)
}