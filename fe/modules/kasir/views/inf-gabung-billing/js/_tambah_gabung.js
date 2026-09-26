var tableTujuan;
var tableGabung;
$(document).ready(function(){
	$('#btn-submit').prop('disabled', true);
	$('#no_pendaftaran').prop('disabled', true);
	$('#no_pendaftaran_tujuan').prop('disabled', true);
   loadTableTujuan(1);
   loadTableGabung(2);
	$(".dataTables_filter").hide();
	$("#no_rekam_medik").select2InfinityScroll({
		url: "/kasir/inf-gabung-billing/filters?type=no_rekam_medik",
		callbackData: (param) => {
			return {
				payload: {
					...param,
				}
			}
		}
	});
	$("#no_rekam_medik").on('change', function(){
		$('#no_pendaftaran').prop('disabled', false);
		$('#no_pendaftaran_tujuan').prop('disabled', false);
		$("#no_pendaftaran").val(null).trigger('change');
		$("#no_pendaftaran_tujuan").val(null).trigger('change');
      clearTable();
	});
	$("#no_pendaftaran").select2InfinityScroll({
		url: "/kasir/inf-gabung-billing/filters?type=no_pendaftaran",
		callbackData: (param) => {
			return {
				payload: {
					...param,
					pasien_id: $("#no_rekam_medik").val()
				}
			}
		}
	})
	$("#no_pendaftaran_tujuan").select2InfinityScroll({
		url: "/kasir/inf-gabung-billing/filters?type=no_pendaftaran_tujuan",
		callbackData: (param) => {
			return {
				payload: {
					...param,
					pasien_id: $("#no_rekam_medik").val()
				}
			}
		}
	});
	$('#btn-detail-transaksi').on('click', function(){
		var no_pendaftaran_tujuan = $('#no_pendaftaran_tujuan').select2('data');
		var pendaftaranIdTujuan = no_pendaftaran_tujuan[0].id;
		var penjaminTujuan = no_pendaftaran_tujuan[0].penjamin_id;

		var no_pendaftaran = $('#no_pendaftaran').select2('data');
		var pendaftaranId = no_pendaftaran[0].id;
		var penjamin = no_pendaftaran[0].penjamin_id;

		if(pendaftaranIdTujuan == pendaftaranId) {
			docoNotification('error', 'Peringatan', 'No Pendaftaran yang akan digabung tidak boleh sama dengan No Pendaftaran tujuan.');
			return false;
		}

		if(penjaminTujuan != penjamin) {
			var header = 'Tagihan Berbeda Penjamin';
			var message = 'Tagihan yang akan digabung berbeda Penjamin. Apakah tagihan tetap akan digabung ?';
			var label = {
				buttons: {
					'Yes': 'button-yes',
					'No': 'button-no'
				},
				hidden: true
			};
			$.showQuestionDialog(header, message, label, function (reaction) {
				if (reaction == 'Yes') {
					hideQuestionDialog();
					$('[data-popup="tooltip"]').tooltip();
					_loadData();
				} else {
					hideQuestionDialog();
					$('[data-popup="tooltip"]').tooltip();
				}
        	})
			return false;
		}
		_loadData();
	});
	$('#btn-submit').unbind();
	$('#btn-submit').bind('click', function(e){
		e.preventDefault();
		var header = 'Konfirmasi Penggabungan Tagihan';
		add = $('#confirm-form').clone().removeClass('hidden');
		add.find('.input-pemakai').removeAttr('readonly');
		add.find('.input-pemakai').attr('value', '');
		add.find('.input-pemakai').attr('id', 'pemakai-validasi');
		add.find('.input-pemakai').attr('placeholder', 'Username');
		add.find('.input-sandi').attr('id', 'sandi-validasi');
		add = add.html();
		var message = 'Konfirmasi Penggabungan Tagihan ' + add;
		var label = {
			buttons: {
				'Yes': 'button-yes',
				'No': 'button-no'
			},
			hidden: true
		};

		$.showQuestionDialog(header, message, label, function (reaction) {
			if (reaction == 'Yes') {
				var user = $('#pemakai-validasi').val();
				var pass = $('#sandi-validasi').val();
				$().docoForm('click', {
					url: baseUrl + 'kasir/end-point/check-authorization',
					skipConfirm: true,
					skipSuccessNotif: true,
					data: {
						nama_pemakai: user,
						katakunci_pemakai: pass,
						akses: 'simpan'
					},
					success: function (response) {
						setTimeout(function () {
							showLoader()
						}, 100);
						_simpan(true);
					}
				})
			} else {
				hideQuestionDialog();
				$('[data-popup="tooltip"]').tooltip();
			}
		})
	});

	var _loadData = function () {
		var no_pendaftaran_tujuan = $('#no_pendaftaran_tujuan').select2('data');
		var pendaftaranIdTujuan = no_pendaftaran_tujuan[0].id;
		var pendaftaranTujuanTanpaTanggal = no_pendaftaran_tujuan[0].no_pendaftaran;

		var no_pendaftaran = $('#no_pendaftaran').select2('data');
		var pendaftaranId = no_pendaftaran[0].id;
		var pendaftaranTanpaTanggal = no_pendaftaran[0].no_pendaftaran;

		$().docoForm("click",{
			url : "/kasir/inf-gabung-billing/detail-tagihan?no_pendaftaran=" + pendaftaranId + '&no_pendaftaran_tujuan=' + pendaftaranIdTujuan,
			method : "GET",
			skipConfirm: true,
			skipSuccessNotif: true,
			success : function (data) {
				var total_dijamin = data.total_dijamin;
				var total_dibayar = data.total_dibayar;
				var total_tagihan = data.total_tagihan;
            loadTableTujuan(1);
            loadTableGabung(2);
				$('.pendaftaran_tujuan').html('<strong>' + pendaftaranTujuanTanpaTanggal + '</strong>');
				$('.pendaftaran_gabung').html('<strong>' + pendaftaranTanpaTanggal + '</strong>');

				$('.total_dijamin').html("Rp. " + docoHelper.convertToRupiah(total_dijamin));
				$('.total_dibayar_pasien').html("Rp. " + docoHelper.convertToRupiah(total_dibayar));
				$('.total_tagihan').html("Rp. " + docoHelper.convertToRupiah(total_tagihan));
				$('#btn-submit').prop('disabled', false);
			}
		});
	}

	var _simpan = function(confirm = false) {
		var _form = $("#gabung-billing-form").serializeArray();
		$().docoForm("click",{
			url : baseUrl + 'kasir/inf-gabung-billing/simpan-gabung-billing',
			method : "POST",
			type : "json",
			data: _form,
			skipConfirm: confirm,
			success : function (data) {
				docoNotification("success", "Success!", "Data Berhasil Disimpan!")
				table.draw();
				$('#modal_backdrop').modal('toggle');
			}
		});
	}
})

function loadTableTujuan(type) {
   tableTujuan = $("#table-pendaftaran-tujuan").docoTabel({
		filter: false,
		sorting: [[4, "desc"]],
		paging: false,
		info: false,
		processing: true,
		serverSide: true,
		scrollX: true,
		scrollCollapse: true,
      destroy: true,
		ajax: baseUrl + "kasir/inf-gabung-billing/detail-pendaftaran?tipe=" + type,
		columns: [
			{
				data: null,
				searchable: false,
				orderable: false,
				render: (data, rowElement, rowData, rowAdditionalData) => {
					var tableInfo = tableTujuan.page.info()
					return tableInfo.start + rowAdditionalData.row + 1
				}
			},
			{
				data: "no_pendaftaran", 
				searchable: false,
				orderable: false,
			},
			{
				data: "tgl_pendaftaran", 
				searchable: false,
				orderable: false,
			},
			{
				data: "instalasi", 
				searchable: false,
				orderable: false,
			},
			{
				data: "tindakan", 
				searchable: false,
				orderable: false,
			},
			{
				data: "qty", 
				searchable: false,
				orderable: false,
				className: "text-right",
			},
			{
				data: "tarif_satuan",
				searchable: false,
				orderable: false,
				className: "text-right",
				render: $.fn.dataTable.render.number(".", ",", 0, "")
			},
			{
				data: "tarif_cyto",
				searchable: false,
				orderable: false,
				className: "text-right",
				render: $.fn.dataTable.render.number(".", ",", 0, "")
			},
			{
				data: "discount",
				searchable: false,
				orderable: false,
				className: "text-right",
				render: $.fn.dataTable.render.number(".", ",", 0, "")
			},
			{
				data: "sub_total",
				searchable: false,
				orderable: false,
				className: "text-right",
				render: $.fn.dataTable.render.number(".", ",", 0, "")
			},
			{
				data: "penjamin", 
				searchable: false,
				orderable: false,
			},
			{
				data: "dijamin",
				searchable: false,
				orderable: false,
				className: "text-right",
				render: $.fn.dataTable.render.number(".", ",", 0, "")
			},
			{
				data: "dibayar_pasien",
				searchable: false,
				orderable: false,
				className: "text-right",
				render: $.fn.dataTable.render.number(".", ",", 0, "")
			},
		],
		footerCallback: function(row, data, start, end, display) {
			var api = this.api(), data;
			var intVal = function ( i ) {
				return typeof i === "string" ?
				i.replace(/[\$,]/g, "")*1 :
				typeof i === "number" ?i : 0;
			};

			var dijamin = api
				.column( 11, { page: "current"} )
				.data()
				.reduce( function (a, b) {
					return intVal(a) + intVal(b);
				}, 0 );

			var dibayar = api
				.column( 12, { page: "current"} )
				.data()
				.reduce( function (a, b) {
					return intVal(a) + intVal(b);
				}, 0 );

			$("#dijamin_pendaftaran").html("Rp. " + docoHelper.convertToRupiah(Math.ceil(dijamin)));
			$("#dibayar_pendaftaran").html("Rp. " + docoHelper.convertToRupiah(Math.ceil(dibayar)));
			$("#total_pendaftaran").html("Rp. " + docoHelper.convertToRupiah(Math.ceil(dijamin + dibayar)));
		},
	});
}

function loadTableGabung(type) {
   tableGabung = $("#table-pendaftaran-gabung").docoTabel({
		filter: false,
		sorting: [[4, "desc"]],
		paging: false,
		info: false,
		processing: true,
		serverSide: true,
		scrollX: true,
		scrollCollapse: true,
      destroy: true,
		ajax: baseUrl + "kasir/inf-gabung-billing/detail-pendaftaran?tipe=" + type,
		columns: [
			{
				data: null,
				searchable: false,
				orderable: false,
				render: (data, rowElement, rowData, rowAdditionalData) => {
					var tableInfo = tableTujuan.page.info()
					return tableInfo.start + rowAdditionalData.row + 1
				}
			},
			{
				data: "no_pendaftaran", 
				searchable: false,
				orderable: false,
			},
			{
				data: "tgl_pendaftaran", 
				searchable: false,
				orderable: false,
			},
			{
				data: "instalasi", 
				searchable: false,
				orderable: false,
			},
			{
				data: "tindakan", 
				searchable: false,
				orderable: false,
			},
			{
				data: "qty", 
				searchable: false,
				orderable: false,
				className: "text-right",
			},
			{
				data: "tarif_satuan",
				searchable: false,
				orderable: false,
				className: "text-right",
				render: $.fn.dataTable.render.number(".", ",", 0, "")
			},
			{
				data: "tarif_cyto",
				searchable: false,
				orderable: false,
				className: "text-right",
				render: $.fn.dataTable.render.number(".", ",", 0, "")
			},
			{
				data: "discount",
				searchable: false,
				orderable: false,
				className: "text-right",
				render: $.fn.dataTable.render.number(".", ",", 0, "")
			},
			{
				data: "sub_total",
				searchable: false,
				orderable: false,
				className: "text-right",
				render: $.fn.dataTable.render.number(".", ",", 0, "")
			},
			{
				data: "penjamin", 
				searchable: false,
				orderable: false,
			},
			{
				data: "dijamin",
				searchable: false,
				orderable: false,
				className: "text-right",
				render: $.fn.dataTable.render.number(".", ",", 0, "")
			},
			{
				data: "dibayar_pasien",
				searchable: false,
				orderable: false,
				className: "text-right",
				render: $.fn.dataTable.render.number(".", ",", 0, "")
			},
		],
		footerCallback: function(row, data, start, end, display) {
			var api = this.api(), data;
			var intVal = function ( i ) {
				return typeof i === "string" ?
				i.replace(/[\$,]/g, "")*1 :
				typeof i === "number" ?i : 0;
			};

			var dijaminGabung = api
				.column( 11, { page: "current"} )
				.data()
				.reduce( function (a, b) {
					return intVal(a) + intVal(b);
				}, 0 );

			var dibayarGabung = api
				.column( 12, { page: "current"} )
				.data()
				.reduce( function (a, b) {
					return intVal(a) + intVal(b);
				}, 0 );

			$("#dijamin_pendaftaran_gabung").html("Rp. " + docoHelper.convertToRupiah(Math.ceil(dijaminGabung)));
			$("#dibayar_pendaftaran_gabung").html("Rp. " + docoHelper.convertToRupiah(Math.ceil(dibayarGabung)));
			$("#total_pendaftaran_gabung").html("Rp. " + docoHelper.convertToRupiah(Math.ceil(dijaminGabung + dibayarGabung)));
		},
	});
}

function clearTable() {
   if(tableTujuan.data().count()) {
      loadTableTujuan(3);
   }
   if(tableGabung.data().count()) {
      loadTableGabung(3);
   }
}
