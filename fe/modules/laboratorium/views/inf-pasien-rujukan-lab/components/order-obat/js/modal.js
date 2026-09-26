$(document).ready(function () {
	$('.cb_penunjang').closest('td').css('margin-bottom', '8px !important')
	$('.cb_penunjang').uniform()
	$('.search-jenispemeriksaan').select2();
	$('.search-radlab').val(null).trigger('change')
})
$('.cb_penunjang').on('click', function () {
	callCbPenunjang(this)
})

$('#txt-search').keyup(function (e) {
	if (e.keyCode === 13) {
		$('#btn-search_radlab').click()
	}
});

$('#btn-search_radlab').on('click', function () {
	var params = 'ruangan_id=' + $('.search-radlab').attr('data-ruangan_id');
	params += '&penjamin_id=' + $('.search-radlab').attr('data-penjamin_id');
	params += '&kelaspelayanan_id=' + $('.search-radlab').attr('data-kelaspelayanan_id');
	params += '&instalasi_id=' + $('.search-radlab').attr('data-instalasi_id');
	params += '&daftartindakan_nama=' + $('.search-radlab').val();
	params += '&jenispemeriksaanlab_id=' + $('.search-jenispemeriksaan').val();

	if ($('.search-radlab').val() == null || $('.search-radlab').val() == '') {
		$('.search-radlab').val(null).trigger('change');
	}

	var url = `/${_module}/${_endPoint}/search?${params}`;
	$('.content-radlab').html('');
	var loading = $('#loading-content');
	loading.append('<h1 align="center"><i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>Memuat ... </b></h1>');
	$.get(url, function (datax) {
		let html = '';
		var data = JSON.parse(datax);
		var countDetail = 0;
		if (data && data.length != 0) {
			html += '<div class="col-sm-12">';
			$.each(data, function (header, detail) {
				var _title = convertToSlug(header);
				countDetail = detail.length;
				html += '<div class="col-sm-4">';
				html += '<div class="panel panel-default">';

				html += '<a id="heading-' + _title + '" data-toggle="collapse" href="#tab-' + _title + '" role="button" aria-expanded="true" aria-controls="tab-' + _title + '" class="">';
				html += '<div class="panel-heading flex-container" style="background-color:#37474f;color:white;">';
				html += '<h6 class="panel-title text-bold" style="font-size:12px;">' + header.toUpperCase() + '</h6>';
				html += '<ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-up"></i></li></ul>';
				html += '</div>';
				html += '</a>';

				html += '<div class="panel-body multi-collpase label-information collapse in" id="tab-' + _title + '" aria-expanded="true">';
				html += '<div class="row">';
				if (countDetail == 0) {
					html += '<p style="text-align:center;font-weight:bold;">Data Tidak Ditemukan.</p>';
				}
				else {
					$.each(detail, function (key, detail2) {
						html += '<p style="margin-left:10px;margin-top:10px;">';
						html += '<label>';
						html += '<input type = "checkbox" id = "' + detail2.daftartindakan_id + '" class="cb_penunjang" ' +
							' data-tariftindakan_id="' + detail2.tariftindakan_id + '" ' +
							' data-daftartindakan_id="' + detail2.daftartindakan_id + '" ' +
							' data-daftartindakan_nama="' + detail2.daftartindakan_nama + '" ' +
							' data-harga_tariftindakan="' + detail2.harga_tariftindakan + '" ' +
							' data-persencyto_tindakan="' + detail2.persencyto_tindakan + '" ' +
							' data-jenispemeriksaanlab_id="' + detail2.jenispemeriksaanlab_id + '" ' +
							' data-jenispemeriksaanlab_nama="' + detail2.jenispemeriksaanlab_nama + '" ' +
							' data-pemeriksaanlab_id="' + detail2.pemeriksaanlab_id + '" ' +
							' data-pemeriksaanlab_nama="' + detail2.pemeriksaanlab_nama + '" ' +
							' data-persen_penyulit="' + detail2.persen_penyulit + '" ' +
							' autocomplete="new-password">&nbsp;&nbsp;' + detail2.kode + ' - ' + detail2.daftartindakan_nama + ' </label></p>';
					});
				}
				html += '</div>';
				html += '</div>';
				html += '</div>';
				html += '</div>';
			});
			html += '</div>';
			loading.empty();
			$('.content-radlab').append(html);
			$('.cb_penunjang').closest('td').css('margin-bottom', '8px !important')
			$('.cb_penunjang').uniform()
			$('.cb_penunjang').on('click', function () {
				callCbPenunjang(this)
			})
		} else {
			loading.empty();
			$('.content-radlab').html('<p style="text-align:center;font-weight:bold;">Data Tidak Ditemukan.</p>');
		}
	});
});

$('#btn-reset').on('click', function () {
	$('.search-jenispemeriksaan').val(null).trigger('change');
	$('.search-radlab').val(null).trigger('change');
	$('#btn-search_radlab').click()
});

var callCbPenunjang = ($this) => {
	let _checkboxData = $($this).data()
	$.each(_checkboxData, (k, v) => {
		if (v == '') {
			_checkboxData[k] = null
		}
		if (k == 'is_cyto') {
			_checkboxData[k] = false
		}
	})

	var _url = `/${_module}/${_endPoint}/simpan-tindakan?pasienkirimkeunitlain_id=${pasienkirimkeunitlain_id}`;
	if ($($this).prop('checked') == true) {
		$().docoForm('click', {
			skipConfirm: true,
			skipSuccessNotif: true,
			data: _checkboxData,
			url: _url,
			success: function (data) {
				table.draw();
				setTimeout(function () {
					$('.search-radlab').val(null).trigger("change");
				}, 1000);

			}
		})
	}

	return true
}

var convertToSlug = (text) => {
	return text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
}

$('.search-jenispemeriksaan').on('change', function () {
	var _textPemeriksaan = $('.search-radlab').val();
	if (_textPemeriksaan == '' || _textPemeriksaan == null) {
		$('.search-radlab').val(null).trigger('change')
	}
})
