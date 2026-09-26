/*
* @Author: Rizqi Fitrianto
* @Date:   2018-04-04 10:44:07
* @Last Modified by:   Rizqi Fitrianto
* @Last Modified time: 2018-04-06 13:46:57
*/
$('#ajax-form').submit(function(event){
	event.preventDefault();
	var _val = $(this).serializeArray();
	if (Object.keys(attributes).length) {
        $.each(attributes, function (key, val) {
            _val.push({
                name: key,
                value: val
            });
        });
    }	
	$(this).docoForm('submit', {            
		data: _val,
        success: function (data) {
            // console.log(data);
            table.draw();
        }
    });
});

$('.selectBarang').select2({
	placeholder: '',
	language: {
                errorLoading: function () { return "Searching..." } 
            },
	minimumInputLength: 2,  
	ajax: {
	    url: '/gudang/informasi-pemakaian-barang/get-barang',
	    dataType: 'json',
	    quietMillis: 250,
	    data: function(term, page){
	        return{
	            q: term,
	            page: page
	        }
	    },
	    processResults: function (data) {                
	      $.each(data.result, function(key, val){
	      	_detailBarang.item[val.id] = val;
	        _detailBarang.satuan[val.id] = {};
	        _detailBarang.stok[val.id] = val.stok;
	        _detailBarang.satuankecil[val.id] = val.satuankecil_id;
	        $.each(val.satuan, function (id, item) {
	            _detailBarang.satuan[val.id][id] = item;
	        });
	      });		      
	      return {
	        results: data.result
	      };
	    }                   
	},
	dropdownCssClass: 'bigdrop',
	escapeMarkup: function (m) { return m; },
}).on('change', function(ev){
	ev.preventDefault();
	var value = $(this).val();
    var list_html = "";
    list_html += " <option value=\"\"></option>";
    data = [];

    if (typeof _detailBarang.satuan[value] !== "undefined") {
        data = _detailBarang.satuan[value];
    }

    if (typeof _detailBarang.stok[value] !== "undefined") {
        $("#pemakaian-barang-stok").val(_detailBarang.stok[value]);
        _detailBarang.currentStok = _detailBarang.stok[value];
    }

    var defaultValue = null;

    if (typeof _detailBarang.satuankecil[value] !== "undefined") {
        _detailBarang.currentSatuan = _detailBarang.satuankecil[value];
        defaultValue = _detailBarang.satuankecil[value];
    }

    if (typeof _detailBarang.item[value] !== "undefined") {
        attributes = _detailBarang.item[value];
    }

    $.each(data, function (i, item) {
        if (defaultValue == i) {
            list_html += "<option value=\'" + i + "\' selected>" + item + "</option>";
        } else {
            list_html += "<option value=\'" + i + "\'>" + item + "</option>";
        }
    });    
    $("#list-satuan").html(list_html);
    var count = Object.keys(data).length;
    if (count > 1) {
        $("#list-satuan").removeAttr("disabled");
        $("#list-satuan").select2({ placeholder: "--Pilih--" });
    } else {
        $("#list-satuan").select2("enable", false);
    }

    $("#list-satuan").select2({ placeholder: "--Pilih--" });
});	
$(document).on('click', '.data-reset', function(e){
	e.preventDefault();
	$.ajax({
		url: '/gudang/informasi-pemakaian-barang/reset-cache',
		success: function(data){
			$("#barang_id").val('').trigger('change');
            $("#list-satuan").val('').trigger('change');
            $("#pemakaian-barang-stok, #pemakaian-barang-qty").val("");			
			table.draw();
		}
	});
});
$(document).on('click','.delete', function(){
	$(this).docoForm('delete', {            		
        success: function (data) {            
            table.draw();
        }
    });
});
$(document).on('click','.btn-simpan', function(){
	$(this).docoForm('click',{      
	    title: 'sukses',          
	    method : 'POST',
	    type : 'json',                
	    success : function (response) {
	    	table.draw();
	        console.log(response);
	    }
	});
});
