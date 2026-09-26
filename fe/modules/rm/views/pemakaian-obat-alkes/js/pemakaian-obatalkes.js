/*
* @Author: Rizqi Fitrianto
* @Date:   2018-01-15 15:35:46
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2018-01-23 16:50:54
*/

var selectObat = $('.selectObatAlkes');
var selectSatuan = $('.selectSatuan');
var txtObat = $('.kodeObat');
var txtSatuan = $('.satuan');
var txtQty = $('.qty');
var arrData = [];
var dataPilihan;
var dataopsi = {list_obat: {}, list_satuan: {}};
	$(function(){
        var pickdate = $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',            
        });        
    });
var datasimpan = [];
var txtIsidata = $('.isidata');
	
	


    $('#btn-add').on('click', function(){    	
    	
    	dataPilihan = {obatid: txtObat.val(), satuanid: txtSatuan.val(), qty: txtQty.val()};
    	arrData.push(dataPilihan);    	
    	let dataisi =  arrData[arrData.length-1];    	
    	let nama_obat = dataopsi.list_obat[dataisi.obatid].obatalkes_namalain;
    	let nama_satuan = dataopsi.list_satuan[dataisi.satuanid].satuankecil_nama;
    	$('.first-tr').addClass('hidden');
    	$('.clone-tr').clone()
    				  .addClass('tr-'+arrData.length)
    				  .removeClass('hidden clone-tr')
    				  .appendTo('tbody');				 				 
    	$('.tr-'+arrData.length+' td[data-id="numRow"]').html(arrData.length);
    	$('.tr-'+arrData.length+' td[data-id="obatNama"]').html(nama_obat);
    	$('.tr-'+arrData.length+' td[data-id="qty"]').html(dataisi.qty);
    	$('.tr-'+arrData.length+' td[data-id="satuan"]').html(nama_satuan);

    	

    });

    $('#btn-simpan').on('click', function(){
    	let tgl_pemakaian = $('input[name="PemakaianObatAlkesForm[tanggal_pemakaian]_submit"]').val();
    	let isidata = JSON.stringify(arrData);
    	$.ajax({
    		type: 'POST',
    		data: 'isidata='+isidata+'&tgl_pemakaian='+tgl_pemakaian,
    		success: function(response){
    			console.log(response);
    		}
    	});  	

    });

    $(document).ready(function(){
    	$.ajax({
    		url: '/rm/pemakaian-obat-alkes/get-data-opsi',
    		type: 'json',
    		success: function(response){
    			let listobat = [];
    			let dataobat = response.data_obat;
    			for (let i in dataobat){
    				listobat.push({id: dataobat[i].obatalkes_id, text: dataobat[i].obatalkes_namalain});
    				dataopsi.list_obat[dataobat[i].obatalkes_id] = dataobat[i];
    			}    			
    			selectObat.select2({
    				data: listobat,
    				type: "GET",
    				quietMillis: 50,
    				minimumInputLength: 2,
    			});
    			selectObat.change(function(){		
					let id = $(this).val();		
					txtObat.val(id);
				});

				let listsatuan = [];
				let datasatuan = response.data_satuan;
				for(let i in datasatuan){
					listsatuan.push({id: datasatuan[i].satuankecil_id, text: datasatuan[i].satuankecil_nama});
					dataopsi.list_satuan[datasatuan[i].satuankecil_id] = datasatuan[i];
				}

				selectSatuan.select2({
					data: listsatuan,
					type: "GET",
					quietMillis: 50,
					minimumInputLength: 2,
				});
				selectSatuan.change(function(){
					let id = $(this).val();		
					txtSatuan.val(id);
				});

    		},

    	})
    });