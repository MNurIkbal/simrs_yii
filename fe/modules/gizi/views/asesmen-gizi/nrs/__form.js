$('[id^=keterangan-]').on('change', function(e) {
	let total = 0;
	for(let ket of $('[id^=keterangan-]:checked'))  {
	    total += parseInt(ket.value);
	}
	if($('[id^=keterangan-]:checked').length == 4) {
		if(total === 0) {
			$('#skrining-warning').removeClass('hidden');
			$('.skrining-lanjut').addClass('hidden');
			$('.skrining-lanjut').find(':checked').prop('checked', false).trigger('change');
			$('[id^=nrsform-skor-dewasa-]').val('');

		} else {
			$('.skrining-lanjut').removeClass('hidden');
			$('#skrining-warning').addClass('hidden');
		}
	} else {
		$('#skrining-warning').addClass('hidden');
		$('.skrining-lanjut').addClass('hidden');
		$('.skrining-lanjut').find(':checked').prop('checked', false).trigger('change');
		$('[id^=nrsform-skor-dewasa-]').val('');
	}
})

$('[id^=skrining-]').on('change', function(e) {
	$('#nrsform-skor-dewasa-' + $(this).data('ket-id')).val($(this).data('skor'))
	let total = 0;
	for(let skor of $('.skor-skrining-lanjut'))  {
		if(skor.value == null || skor.value == '') {
			return;
		}
		total += parseInt(skor.value);
	}
	$('#nrsform-skor-dewasa-total').val(total);
	if(umur >= 70) {
		total += 1;
	}
	let textKesimpulan = '';
	for(let detail of kesimpulan[KATEGORI_NRS[0]]) {
		if(total >= detail.skor_awal && total < detail.skor_akhir) {
			textKesimpulan = detail.keterangan;
		}
	}
	$('#nrsform-skor-dewasa-keterangan').val(textKesimpulan);
})

$('[id^=skor-anak-]').on('change', function(e){
	if($("[id^=kategori-]:checked").val() == KATEGORI_NRS[1]){
		let total = 0;
		for(let skor of $('.skor-skrining-anak:checked'))  {
			if(skor.value == null || skor.value == '') {
				return;
			}
			total += parseInt(skor.value);
		}
		$('#nrsform-skor-anak-total').val(total);
		let textKesimpulan = '';
		for(let detail of kesimpulan[KATEGORI_NRS[1]]) {
			if(total >= detail.skor_awal && total < detail.skor_akhir) {
				textKesimpulan = detail.keterangan;
			}
		}
		$('#nrsform-skor-anak-keterangan').val(textKesimpulan);
	}
})

$("#btn-save-nrs").bind('click', () => {
	if($("[id^=kategori-]:checked").val() == KATEGORI_NRS[1]){
		if($('.skor-skrining-anak:checked').length < total_skrining_anak){
			docoNotification('error', 'Proses simpan gagal', 'Silahkan lengkapi Form NRS telebih dahulu');
			return;
		}
	}else{
		let total = 0;
		for(let ket of $('[id^=keterangan-]:checked'))  {
		    total += parseInt(ket.value);
		}
		if(total > 0 && $('.skrining-lanjut :checked').length === 0) {
			docoNotification('error', 'Proses simpan gagal', 'Terjadi kesalahan. Pilih Skrining Lanjut');
			return;
		}
	}

    $("[type='hidden']").prop('disabled', true);
    let payload = $("#form-nrs").serialize()
    showLoader()
    $.ajax({
        url: `/gizi/asesmen-gizi/save-nrs?id=${pendaftaran_id}`,
        method: 'POST',
        data: payload,
        success: () => {
            docoNotification('success', 'Proses berhasil!', 'Data Nrs berhasil disimpan.')
        },
        complete: () => {
            hideLoader()
        }
    })
})
$("[id^=kategori-]").on('change', (e) => {
	if($("[id^=kategori-]:checked").val() == KATEGORI_NRS[0]){
		$('#nrs-dewasa').show();
		$('#nrs-anak').hide();
	}else{
		$('#nrs-dewasa').hide();
		$('#nrs-anak').show();
	}
})



if($('[id^=kategori-]:checked').length == 0){
	$($('[id^=kategori-][value="dewasa"]')).prop('checked', true).trigger('change');
}else{
	$('[id^=kategori-]:checked').trigger('change')
	$('[id^=skor-anak-]:checked').trigger('change')
	$($('[id^=keterangan-]:checked')[0]).trigger('change')
	$('[id^=skrining-]:checked').trigger('change')
}
