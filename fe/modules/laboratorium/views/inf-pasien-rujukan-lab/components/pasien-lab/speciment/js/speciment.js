var table;
$(document).ready(function(){
	table = $('#table-sample').docoTabel({
		filter: true,
		sorting: [[2, 'asc']],
		displayLength: 10,
		processing: true,
		serverSide: true,
		ajax: baseUrl+'laboratorium/inf-pasien-rujukan-lab/get-data-speciment?id=' + id,
		columns: [
			{
				title: 'No',
				data: 'rowNum',
				searchable: false,
				orderable: false
			},
			{data: 'nama_sample', name: 'nama_sample'},
			{data: 'tgl_ambilsample', name: 'tgl_ambilsample'},
			{data: 'jam_ambilsample', name: 'jam_ambilsample'},
			{data: 'jumlah', name: 'jumlah'},
			{data: 'satuanlab_nama', name: 'satuanlab_nama'},
			{data: 'keterangan', name: 'keterangan'},
			{data: 'daftartindakan_nama'},
		],
		drawCallback: function( settings ) {
			
			var api = this.api();
			if(typeof api.data().length != 'undefined' && api.data().length > 0 && is_ambil_sample == true){
				$('#btn-batal-speciment').prop('disabled', false);
			}else{
				$('#btn-batal-speciment').prop('disabled', true);
			}
		}
	});
	$('.dataTables_filter').hide();
	$('#btn-ulang').on('click', function () {
		location.reload();
	});
	$(document).on('keydown', null, 'alt+s', function (event) {
		$('#btn-simpan-speciment').click();
	});
})

$('#temp-sample').attr('style', 'display:none')
appendSample(transLab)
$(document).on('click', '.add', function () {
	let key = $(this).attr('data-key')
	let tr = $(this).closest('.tr_clone' + key)
	let cloned = tr.clone()
	cloned.find('#').val('')
	tr.after(cloned)
})

$(document).on('click', '.del', function () {
	let key = $(this).attr('data-key')
	let count = $('.delete' + key).length
	if (parseInt(count) < 2) {
		return false
	} else {
		$(this).parent().parent().remove()
	}
})

$('#btn-simpan-speciment').on('click', function (event) {
	let total = 0
	$('.list_sample').each(function () {
		let jml = parseInt($(this).attr('data-jml'))
		total += jml
	})
	event.preventDefault()
	$(this).docoForm('click', {
		url: '/laboratorium/inf-pasien-rujukan-lab/save-speciment',
		data: { id: id },
		method: 'post',
		success: function (data) {
			$('#btn-kembali-speciment').trigger('click')
		},
	})
})

$(document).on("click", "#deleted", function () {
   let id = $(this).attr("data-id");
   let penunjang = $(this).attr("data-penunjang");
   let button = this;
   $(this).docoForm("click", {
      url: '/laboratorium/inf-pasien-rujukan-lab/delete-cache-speciment/', // point to server-side PHP script 
      data: {
         id: id,
         penunjang_id: penunjang,
      },
      method: 'post',
      confirmTitle: i18next.t("Konfirmasi"),
      confirmMessage: i18next.t("Apa anda yakin ingin menghapus data ini?"),
      success: function (data) {
         $(button).parent().parent().remove();
         if (typeof transLab != "undefined") {
            if (typeof transLab != "undefined") {
               delete transLab[id]
            }
            appendSample(transLab);
         }
      }
   });
});

function appendSample(object) {
	let _no = 0
	let _html = ''
	$('.default-value').attr('style', 'display:none')
	$.each(object, function (x, y) {
		if (typeof object !== 'undefined') {
			$('#temp-sample').attr('style', 'display:block')
			$('#btn-save').prop('disabled', false)
			_no++
			_html += '<tr class="resep">'
			_html +=
				"<td style='display:none;'><input type='hidden' data-jml=" +
				y.jml_pemeriksaan +
				' data-sample=' +
				y.samplelab_id +
				" class='list_sample' data-tindakan=" +
				y.daftartindakan_id +
				'></td>'
			_html += '<td class="numbering">' + _no + '</td>'
			_html += '<td>' + y.nama_sample + '</td>'
			_html += '<td>' + y.tgl + '</td>'
			_html += '<td>' + y.jam + '</td>'
			_html += '<td>' + y.jumlah + '</td>'
			_html += '<td>' + y.satuan_nama + '</td>'
			_html += '<td>' + y.keterangan + '</td>'
			_html += '<td>' + y.nama_pemeriksaan + '</td>'
			_html +=
				'<td><a id="deleted" class="btn btn-sm btn-danger" data-penunjang="' +
				y.pasienmasukpenunjang_id +
				'" data-id="' +
				x +
				'" action="/laboratorium/speciment/delete-cache?id=' +
				x +
				'&penunjang=' +
				y.pasienmasukpenunjang_id +
				'"><i class="fa fa-trash"></i></a></td>'
			_html += '</tr>'
		}
		object[x] = y
	})
	if (_no < 1) {
		$('#temp-sample').attr('style', 'display:none')
		$('#btn-save').prop('disabled', true)
	}
	if (_html === '') {
		_html += '<tr>'
		_html +=
			'<td colspan="7" id="data-null" class="text-center">Data Tidak Ditemukan</td>'
		_html += '</tr>'
	}
	$('#list-sample').html('')
	$('#list-sample').prepend(_html)
}

$('#btn-ulang').on('click', function () {
   location.reload()
})

$('#btn-batal-speciment').on('click', function (event) {
	event.preventDefault()
	if(is_ambil_sample){
		$(this).docoForm('click', {
			url: '/laboratorium/inf-pasien-rujukan-lab/batal-speciment',
			data: { id: id },
			method: 'post',
			success: function (data) {
				// table.draw();
				$('#btn-add-modal').prop('disabled', false);
				$('#btn-kembali-speciment').trigger('click');
			},
		})
	}else{
		docoNotification('error', 'Proses Gagal', 'Tidak bisa membatalkan speciment.');
	}
})


