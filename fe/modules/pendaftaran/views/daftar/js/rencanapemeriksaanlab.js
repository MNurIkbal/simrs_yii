/*
* @Author: rizqi_fitrianto
* @Date:   2018-07-12 13:49:58
* @Last Modified by:   Sigit
* @Last Modified time: 2019-01-18 09:18:10
*/


var lastno = 0;
$('.btn-pemeriksaan-clear').on('click', function(){
    _formPendaftaran.listPenunjang = {};
    loadpemeriksaan(_formPendaftaran.listPenunjang);
})


$(document).on('change','.check-cyto', function(){
    var key = $(this).data('key')
    var is_cyto = 'false'
    if($(this).prop('checked')){
        is_cyto = 'true'
    }
    if(typeof _formPendaftaran.listPenunjang[key] !== 'undefined'){
        _formPendaftaran.listPenunjang[key].is_cyto = is_cyto
    }
    loadpemeriksaan(_formPendaftaran.listPenunjang)

})

 var loadpemeriksaan = function(_obj){
    var no = 0;
    var row = "";
    if(Object.keys(_obj).length > 0){
        $.each(_obj, function(k, v){
            let cyto
            let hargacyto
            if(v.is_cyto == 'true'){
                cyto = 'checked'
                hargacyto = parseInt(v.harga_tariftindakan) * (parseInt(v.persencyto_tindakan) / 100)
            }else{
                cyto = ''
                hargacyto = 0
            }
            no++
            row += '<tr class="row-data">';
            row += '<td>' + no + '</td>';
            row += '<td>' + v.jenispemeriksaan + '</td>';
            row += '<td>' + v.namapemeriksaan + '</td>';
            row += '<td>1</td>';
            row += '<td><input type="checkbox" class="check-cyto" '+cyto+' data-key="'+k+'"></td>';
            row += '<td> Rp. '+ docoHelper.convertToRupiah(parseInt(v.harga_tariftindakan)+hargacyto) +'</td>';
            row += '<td><a class="btn btn-danger btn-sm btn-remove-pemeriksaan" data-key="'+k+'"><i class="fa fa-trash"></i></a></td>';
            row += '</tr>';
        })
    }else{
        row += '<tr class="row-default"> <td class="text-center" colspan="7">Belum ada data yang ditambahkan</td> </tr>'
    }
    sumHarga(_formPendaftaran.listPenunjang)
    $('#table-pemeriksaan').find('tbody tr').remove()
    $('#table-pemeriksaan').find('tbody').append(row)
 }

var sumHarga = function(_obj){
    var total = 0;
    $.each(_obj, function(k,v){
        if(v.is_cyto == 'true'){
            cyto = 'checked'
            hargacyto = parseInt(v.harga_tariftindakan) * (parseInt(v.persencyto_tindakan) / 100)
        }else{
            cyto = ''
            hargacyto = 0
        }
        total += parseInt(v.harga_tariftindakan) + hargacyto
    })
    $('.total-pemeriksaan').empty().html('<b>Rp. '+docoHelper.convertToRupiah(total) + '</b>');
}

$(document).on('change', '#selectCarabayar', function() {
    var caraBayar = parseInt($(this).val());
    var noBpjs = $("#hidden_no_bpjs").val();

    if (caraBayar == 6 && noBpjs != '') {
        var noPesertaBpjs = $("#hidden_no_bpjs").val();

        $('#nomor_cari').val(noPesertaBpjs);
        $('input[name=source_peserta][value=2]').prop('checked', true);
    }
});