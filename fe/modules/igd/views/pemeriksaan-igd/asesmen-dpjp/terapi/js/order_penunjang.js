// ----- order penunjang
$(document).ready(function(){
  $(".btn-pemeriksaan-tambah").click(function(){
    var params = 'ruangan_id=' +$('#penunjang_ruangan_id').val()
    params += '&penjamin_id='+$('#penunjang_penjamin_id').val()
    params += '&kelaspelayanan_id='+$('#penunjang_kelaspelayanan_id').val()
    params += '&instalasi_id='+$('#penunjang_instalasi_id').val()
    $(".btn-pemeriksaan-tambah").attr("action", "/igd/pemeriksaan-igd/modal-pemeriksaan-penunjang?"+params);
  });
});

var lastno = 0;
$('.btn-pemeriksaan-clear').on('click', function(){
    list_pemeriksaanlab = [];
    pemeriksaanlab = {}
    loadpemeriksaan(pemeriksaanlab)
    
    $('#penunjang_instalasi_id2').val('');
    $('#penunjang_ruangan_id2').val('');
    $('#penunjang_instalasi_id').attr('disabled', false);
    $('#penunjang_ruangan_id').attr('disabled', false);
})
$(document).on('click','.btn-remove-pemeriksaan', function(){ 
    var key = $(this).data('key')
    var id = $(this).data('id').toString()
    if(typeof pemeriksaanlab[key] !== 'undefined'){
        delete pemeriksaanlab[key]
        list_pemeriksaanlab = list_pemeriksaanlab.filter(e => e !== id)
        loadpemeriksaan(pemeriksaanlab)
    }
})


$(document).on('change','.check-cyto', function(){
    var key = $(this).data('key')
    var is_cyto = 'false'
    if($(this).prop('checked')){
        is_cyto = 'true'
    }
    if(typeof pemeriksaanlab[key] !== 'undefined'){
        pemeriksaanlab[key].is_cyto = is_cyto
    }
    loadpemeriksaan(pemeriksaanlab)

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
            row += '<td align="right"> Rp. ' + docoHelper.convertToRupiah(parseInt(v.harga_tariftindakan)) + '</td>';
            row += '<td><input type="checkbox" class="check-cyto" '+cyto+' data-key="'+k+'"></td>';
            row += '<td align="right"> Rp. ' + docoHelper.convertToRupiah(parseInt(v.harga_tariftindakan) * (parseInt(v.persencyto_tindakan) / 100)) + '</td>';
            row += '<td align="right"> Rp. ' + docoHelper.convertToRupiah(parseInt(v.harga_tariftindakan) + hargacyto) +'</td>';
            row += '<td><a class="btn btn-danger btn-sm btn-remove-pemeriksaan" data-key="'+k+'" data-id="'+v.daftartindakan_id+'"><i class="fa fa-trash"></i></a></td>';
            row += '</tr>';
        })
    }else{
        row += '<tr class="row-default"> <td class="text-center" colspan="7">Belum ada data yang ditambahkan</td> </tr>'
    }
    sumHarga(pemeriksaanlab)
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

// ----- end order penunjang