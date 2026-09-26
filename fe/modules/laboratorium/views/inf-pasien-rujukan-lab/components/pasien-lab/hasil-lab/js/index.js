// Identifier PHP
var urlVerif = `/laboratorium/inf-pasien-rujukan-lab/verifikasi?id=${id}`;
var urlUnverif = `/laboratorium/inf-pasien-rujukan-lab/unverifikasi?id=${id}`;

$('#obat-alkes').prop('disabled', true);
$("#btn-input").prop("disabled", true);
$("#btn-batal-input").prop("disabled", true);

if (isFinish) {
	$('#btn-input').prop('disabled', true);
	$('#btn-ulang').prop('disabled', true);
	$('#obat-alkes').prop('disabled', true);
	$("#btn-batal-input").prop("disabled", true);
	$("#btn-batal-input").hide();

	$('#verifikasi')
		.addClass('unverifBtn')
		.html("<b><i class='fa fa-key'></i></b> Cabut Verifikasi");
}

if (isBayar == 1) {
	$('#obat-alkes').prop('disabled', true);
}

$('#verifikasi').on('click', function () {
	var url = urlVerif;
	var confirmMessage = `Apa anda yakin ? Data tidak bisa di edit lagi jika sudah terverifikasi`
	if (isFinish) {
		confirmMessage = `Apa anda yakin ? Verifikasi akan dilepas, sehingga bisa menverifikasi ulang`
		url = urlUnverif
	}
	$(this).docoForm("click", {
		url,
		confirmMessage,
		success: function(data) {
		$('#btn-kembali-hasil').trigger('click');
		}
	});
})


// Datatables
var tableHasil;
$(document).on('click', '.data-reload', function () {
	tableHasil.draw();
});

function initDataTable() {
	tableHasil = $('#table-hasil-lab').docoTabel({
		filter: true,
		destroy: true,
		columnDefs: [{
		orderable: false,
		className: 'select-checkbox',
		targets: 0
		}],
		select: {
		style: 'os',
		selector: 'tr'
		},
		sorting: [[2, 'asc']],
		displayLength: 10,
		processing: true,
		serverSide: true,
		ajax: baseUrl + `laboratorium/inf-pasien-rujukan-lab/get-data-hasil-lab?id=${pasienMasukPenunjangId}`,
		columns: [
		{
			data: null,
			searchable: false,
			orderable: false,
			defaultContent: '',
		},
		{
			data: 'rowNum',
			searchable: false,
			orderable: false
		},
		{ data: 'nama_sample', name: 'nama_sample' },
		{ data: 'daftartindakan_nama' },
		{ data: 'is_expertise' },
		],
	});
	$('.dataTables_filter').hide();
}

$(document).on('click', '#table-hasil-lab tbody tr', function(){
	try {
		primaryKey = tableHasil.row('.selected').data().primary ? tableHasil.row('.selected').data().primary : null;
		pelayananId = tableHasil.row('.selected').data().pelayananId ? tableHasil.row('.selected').data().pelayananId : null;
		penunjangId = tableHasil.row('.selected').data().penunjang_id ? tableHasil.row('.selected').data().penunjang_id : null;
		isException = isException;
	} catch (e) {
		primaryKey = false
		penunjangId = false;
		isException = false;
	}
	
	if(pelayananId){
		let _dataRender = $('#obat-alkes').attr('data-render');
		$('#obat-alkes').attr('data-render',_dataRender + '&pelayananId=' + pelayananId);
		$('#obat-alkes').prop('disabled', false);
	}
	
	if (primaryKey) {
   	$("#btn-input").attr("data-render", 'input-hasil?id=' + primaryKey + '&penunjang_id=' + penunjangId);
		$("#btn-input").prop("disabled", false);
		if(is_periksa){
			$("#btn-batal-input").prop("disabled", false);
			$("#btn-batal-input").attr("data-url", 'batal-input?samplelab_id=' + primaryKey + '&pasienmasukpenunjang_id=' + penunjangId);
		}

 	}
	else {
		$("#btn-input").prop("disabled", true);
	}

	if (isException == 1) {
		$("#print-result").prop("disabled", true);
	}else{
		$("#print-result").prop("disabled", false);
	}
});
$(document).ready(initDataTable)


$('#btn-batal-input').on('click', function (event) {
	event.preventDefault()
	if (!isFinish && is_periksa) {
		let _url = '/laboratorium/inf-pasien-rujukan-lab/' + $("#btn-batal-input").attr("data-url");
		$(this).docoForm('click', {
			url: _url,
			data: { id: id },
			method: 'post',
			success: function (data) {
				$('#btn-kembali-hasil').trigger('click');
			},
		})
	}else{
		docoNotification('error', 'Proses Gagal.', 'Tidak dapat membatalkan input hasil.')
	}
})