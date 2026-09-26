var table_pemeriksaan;
var table_detail_pemeriksaan;
var loadpemeriksaan = function(_obj){
var no = 0;
var row = "";
if(Object.keys(_obj).length > 0){
    $.each(_obj, function(k, v){
        // let cyto
        // let hargacyto
        // if(v.is_cyto == 'true'){
        //     cyto = 'checked'
        //     hargacyto = parseInt(v.harga_tariftindakan) * (parseInt(v.persencyto_tindakan) / 100)
        // }else{
        //     cyto = ''
        //     hargacyto = 0
        // }
        no++
        row += '<tr class="row-data" data-tarif_id="'+v.tariftindakan_id+'" data-tindakan_id="'+v.daftartindakan_id+'">';
        row += '<td>' + no + '</td>';
        // row += '<td>' + v.jenispemeriksaan + '</td>';
        row += '<td>' + v.daftartindakan_nama + '</td>';
        // row += '<td align="right"> Rp. '+ docoHelper.convertToRupiah(parseInt(v.harga_tariftindakan)) + '</td>';
        // row += '<td><input type="checkbox" class="check-cyto" '+cyto+' data-key="'+k+'"></td>';
        // row += '<td align="right"> Rp. '+ docoHelper.convertToRupiah(parseInt(v.harga_tariftindakan) * (parseInt(v.persencyto_tindakan) / 100)) +'</td>';
        // row += '<td align="right"> Rp. '+ docoHelper.convertToRupiah(parseInt(v.harga_tariftindakan)+hargacyto) +'</td>';
        row += '<td><a class="btn btn-danger btn-sm btn-remove-pemeriksaan" data-key="'+k+'"><i class="fa fa-trash"></i></a></td>';
        row += '</tr>';
    })
}else{
    row += '<tr class="row-default"> <td class="text-center" colspan="7">Belum ada data yang ditambahkan</td> </tr>'
}
// sumHarga(pemeriksaanlab)
$('#table-pemeriksaan').find('tbody tr').remove()
$('#table-pemeriksaan').find('tbody').append(row)
}

$(function(){

	$('#toolbar-save').affix({
		offset: {top:50}
	});

	$('#pencarian_pasien').on('select2:select', function (e) {
	    var data = e.params.data;
	});
	var date = new Date();
    var yesterday = new Date((new Date()).valueOf()-1000*60*60*24);
    $('.pickadate').pickadate({
        format: 'dd mmmm yyyy',
        formatSubmit:'yyyy-mm-dd',
        disable: [
            { from: [0,0,0], to: yesterday }
        ]
    });
	$('#penunjang_tgl_kirimpasien').pickadate('picker');
	// function confDialog(message, yesCallback, noCallback) {
	//     $('.title').html(message);
	//     var dialog = $('#modal_dialog').dialog();

	//     $('#btnYes').click(function() {
	//         dialog.dialog('close');
	//         yesCallback();
	//     });
	//     $('#btnNo').click(function() {
	//         dialog.dialog('close');
	//         noCallback();
	//     });
	// }
	// $('#pencarian_pasien').on('select2:unselecting',confDialog);
	// $('#pencarian_pasien').find(':selected').data('no_pendaftaran');

	$('#order-penunjang').on('click',function(){
		var nodaftar = $('#pencarian_pasien').find(':selected').data('no_pendaftaran');
		if (nodaftar !== undefined && nodaftar !== null) {
			$('#modal_list_penunjang').modal('show');
		}
	});

    $('#modal_list_penunjang').on('shown.bs.modal',function(){
		var nodaftar = $('#pencarian_pasien').find(':selected').data('no_pendaftaran');
		if (nodaftar === undefined || nodaftar === null) {
			return false;
		}
    	table_pemeriksaan = $('#table-list-pemeriksaan').DataTable({
    		destroy:true,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style: 'multi',
                selector: 'tr'
            },
	    	ajax:'list-detail-pemeriksaan?noPendaftaran='+nodaftar,
	    	columns:[
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: '',
                },
                {
                	data: 'jenispemeriksaanrad_nama',
                },
                {
                	data: 'nama_kelompok',
                },
                {
                	data: 'daftartindakan_nama',
                }
            ]
	    });
    });


    $('.btn-modal-add-pemeriksaan').on('click', function(){
        var tindakan_row = table_pemeriksaan.rows('.selected').data().toArray();
        loadpemeriksaan(tindakan_row);
        $('#modal_list_penunjang').modal('toggle');
    });


    $('#modal_list_penunjang').on('hidden.bs.modal',function(){
        table_pemeriksaan.clear().draw();
    });

    $('#btn-save').on('click',function(e){
    	e.preventDefault();
    	var data = $('#order-radiologi-form').serializeArray();
		var nodaftar = $('#pencarian_pasien').find(':selected').data('no_pendaftaran');
    	var pemeriksaanrad = [];
    	data.push({name:'nodaftar',value:nodaftar});
    	// console.log(data);return;
    	$('#table-pemeriksaan tbody tr.row-data').each(function(k,v){
    		// console.log($(v).data());
    		var objdata = {};
    		objdata.tarif_id = $(v).data('tarif_id');
    		objdata.tindakan_id = $(v).data('tindakan_id');
    		// objdata.push({name:'tarif_id',value:$(v).data('tarif_id')});
    		// objdata.push({name:'tindakan_id',value:$(v).data('tindakan_id')});
    		pemeriksaanrad.push(objdata);
    	});
    	data.push({name:'listorder',value:JSON.stringify(pemeriksaanrad)});
    	// console.log(pemeriksaanrad);console.log('===');
    	// console.log(JSON.stringify(pemeriksaanrad));return;

    	$.ajax({
    		method:'POST',
    		url:'simpan',
    		dataType:'json',
    		data:data,
    		success:function(data){
    			if(data.statusCode == 200){
    				new PNotify({
			            title: 'Berhasil!',
			            text: 'Data berhasil disimpan',
			            addclass: 'alert alert-success alert-arrow-right alert-styled-right',
			            type: 'success'
			        });
    			}
    		}
    	});
    	// console.log(JSON.stringify(pemeriksaanrad));
        // data.push({name:'listperiksa', value:JSON.stringify(pemeriksaanrad)});
    });
});