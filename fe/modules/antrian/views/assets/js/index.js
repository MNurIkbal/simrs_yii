$(document).ready(function() {
	Array.prototype.chunk = function (chunk_size) {
        let results = [];
        const temp = this.slice(0);
        while (temp.length) {
            results.push(temp.splice(0, chunk_size));
        }
      
        return results;
    };

    var defaultAntrian = location.search.split('default=')[1];
    if (defaultAntrian != undefined) {
        setTimeout(function() {
            document.querySelector('.show-detail-pendaftaran').click();
        }, 100);
    }

	$('.show-detail-pendaftaran').click(function(e) {
    	e.preventDefault();
    	var id = $(this).data('id');

    	$('.carousel-parent').hide();
    	$('.btn-layar').hide();
    	$('.btn-kembali').show();
    	$('.arrow').hide();

    	var loading = $('#loading-content');
    	$.ajax({
            url: 'antrian/dashboard/jenis-antrian-detail',
            method: 'GET',
	        data: {id: id},
	        dataType: 'json',
         	beforeSend: function(){
                    loading.append('<h1 align="center"><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>Memuat ... </b></h1>');
            },
            success: function (res) {
            	loading.empty();
     			if (typeof res !== 'undefined' && res.length) {
            		var data = res;
            		var total_card = 0;
                    var total_slide = 1;
                    var items = [];
                    var pembagi = 0;

                     if (data.length > 0) {
                     	total_card = data.length;
                     	pembagi = total_card / 3;
                        total_slide = Math.ceil(pembagi);
                        items = data.chunk(3);
                     }
                    var elCarousel = '<div class="carousel-inner carousel-child">';

                    if (total_slide > 1) {
                    	$('.arrow').show();
                    }
                    for (var i=0; i < total_slide; i++) {
                    	var classActive = '';
                    	if (classActive == 0) {
                    		classActive ='active';
                    	}
                    	elCarousel += `<div class="item ${classActive}">
                    					<center>
                    					<div class="carousel-item ">`;

                    	if (items.length > 0) {
                    		 $.each(items[i], function(key, val) {
                    		 	elCarousel += `
                    		 				<div class="col-sm-4">
										        <div class="panel text-center" style="padding: 40px 4px 40px 4px;">
										        	<img src="${icon}" style="width:50%; border-radius: 50%;border: 2px solid #54be8b;">
										            <h6 class="no-margin text-semibold jenis-antrian">${val.jenisantrian_nama +' - '+ val.nama}
										            </h6>

										            <a href="#" class="btn btn-info btn-more ambil-antrian" data-detailid="${val.jenisantriandetail_id}" data-jenisid="${val.jenisantrian_id}">
														Ambil Antrian
													</a>
										        </div>
											</div>`;
                    		 });
                    	}
                    	elCarousel += `</div>
										</center>
									</div>`;
                    }
                    elCarousel += '</div>';
                    $('.carousel').append(elCarousel);
            	}
     		}
        });
    });

    $('.btn-kembali').click(function(e) {
    	$('.arrow').hide();
		$('.btn-kembali').hide();
    	$('.carousel-parent').show();
    	$('.arrow').show();
    	$('.btn-layar').show();
    	$('.carousel-child').remove();
    });

    $(document).on('click','.ambil-antrian', function(e){
    	e.preventDefault();
    	var detailId = $(this).data('detailid');
    	var jenisId = $(this).data('jenisid');

        if (defaultAntrian != undefined) {
            window.location.href = '/antrian/dashboard/jenis-antrian?jenis_id='+jenisId+'&detail_id='+detailId+'&default=false';
        } else {
            window.location.href = '/antrian/dashboard/jenis-antrian?jenis_id='+jenisId+'&detail_id='+detailId;
        }
    });
});
