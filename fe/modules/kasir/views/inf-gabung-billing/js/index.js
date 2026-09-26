var table;
$(() => {
	$('.data-batal-gabung').prop("disabled", true);
	$('.data-batal-gabung').removeClass('btn-info');
	$('.data-batal-gabung').addClass('btn-danger');
	$("#detail-gabung").prop("disabled", true);
	$("#cetak-rincian").prop('disabled', true);
	$('#cetak-detail-rincian-tagihan').prop("disabled", true);
	$('#cetak-detail-rincian-tagihan-designer').prop("disabled", true);
   $("#btn-cetak-invoice").prop("disabled", true);
	$("#cetak-invoice").prop("disabled", true);
	$('#btn-cetak-detail-invoice').prop("disabled", true);
	$("#cetak-kwitansi").prop("disabled", true);
	$('#print-detail-edit-tagihan').prop("disabled", true);

   table = $("#example").docoTabel({
      filter: false,
		columnDefs: [{
			orderable: false,
			className: "select-checkbox",
			targets:   0
		}],
		select: {
			style:    "os",
			selector: "tr"
		},
		sorting: [[3, "desc"]],
		displayLength: 10,
		processing: true,
		serverSide: true,
		stateSave: false,
		scrollX: true,
      scrollY: false,
      ajax: {
         url: "/kasir/inf-gabung-billing/get-data",
      },
		columns: [
			{
				data : null,
				render : function ( data, type, full, meta ) {
					return null;
				},
				searchable: false,
				orderable: false
			},
         {
            data: null,
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData, rowAdditionalData) => {
               var tableInfo = table.page.info()
               return tableInfo.start + rowAdditionalData.row + 1
            }
         },
			{
				title: "No Pendaftaran",
				data: "no_pendaftaran",
			},
			{
				data: "tgl_gabung",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD-MMM-YYYY")
            }
			},
			{
            data: "tgl_pulang",
            render: (data) => {
               return data == "" || data == null ? "-" : moment(data).format("DD-MMM-YYYY")
            }
			},
			{
				data: "nama_pasien",
				name:"nama_pasien",
            render: (data, rowElement, rowData) => {
               return `${rowData.nama_pasien != null ? rowData.nama_pasien : ''} (${rowData.jenis_kelamin_kode != null ? rowData.jenis_kelamin_kode : ''}) - ${rowData.no_rekam_medik != null ? rowData.no_rekam_medik : ''}`
            },
				orderable: false
			},
			{
				data: "instalasi",
				name:"instalasi",
            render: (data, rowElement, rowData) => {
               return `${rowData.instalasi != null ? rowData.instalasi : ''} - ${rowData.ruangan != null ? rowData.ruangan : ''}`
            },
				orderable: false
			},
			{
				data: "cara_bayar",
				name:"cara_bayar",
            render: (data, rowElement, rowData) => {
               return `${rowData.cara_bayar != null ? rowData.cara_bayar : ''} - ${rowData.penjamin != null ? rowData.penjamin : ''}`
            },
				orderable: false
			},
			{
				data: "no_sep",
				name:"no_sep",
			},
			{
				data: "status_bayar",
				name:"status_bayar",
			},
			{
				data: "total_tagihan",
				name:"total_tagihan",
				className: "text-right",
				orderable: false,
				render: (data, rowElement, rowData) => {
					return `<div id="${rowData.primary}">${$.fn.dataTable.render.number( ".", ",", 0, "" ).display(rowData.total_tagihan)}</div>`
				}
			},
			{
				data: "ref_no_pendaftaran",
				name:"ref_no_pendaftaran",
				orderable: false
			},
			{
				data: "ref_penjamin",
				name:"ref_penjamin",
				orderable: false
			},
		],
		drawCallback: function(settings)  {
            const dataRes = settings.jqXHR.responseJSON
            payload = [];
            dataRes.data.map((data) => {
                tmpData = {
                    primary : data.primary,
                    pendaftaranId : data.ref_pendaftaran_id,
                    penjaminId : data.ref_penjamin_id,
                    kelasPelayananId : data.ref_kelaspelayanan_id,
                    totalTagihan : docoHelper.convertToAngka(data.total_tagihan),
                    pasienAdmisiId : data.ref_pasienadmisi_id,
                }
                payload.push(tmpData)
            })
            getBulkAdmin(payload)
        },
      formFilters: [
         {
            fieldName: 'tgl_gabung',
            label: 'Tanggal Gabung Tagihan',
            type: {
               name: 'rangeDate',
            }
         },
         {
            fieldName: 'nama_pasien',
            label: 'Nama Pasien/No. Rekam Medik',
         },
         {
            fieldName: 'ref_no_pendaftaran',
            label: 'No Pendaftaran',
         },
         {
            fieldName: 'no_pendaftaran',
            label: 'Ref No Pendaftaran',
         },
         'penjamin',
      ],
   });

   $(document).on("click", "#example tbody tr", function(){
      var primary = null;
      var status_bayar_id = null;
		var pendaftaran_id_awal = null;
		var pendaftaran_id = null;
		var pembayaran_id = null;
		var instalasi_id = null;
		var pembayaran_deleted = null;
		var ref_pendaftaran_id = null;
		var _data = table.row(".selected").data();
      try {
         primary = _data.primary;
			status_bayar_id = _data.status_bayar_id;
			pendaftaran_id_awal = _data.pendaftaran_id;
			pendaftaran_id = _data.ref_pendaftaran_id_encrypt;
			pembayaran_id = _data.pembayaran_id_encrypt;
			instalasi_id = _data.instalasi_id_encrypt;
			pembayaran_deleted = _data.pembayaran_deleted;
			ref_pendaftaran_id = _data.ref_pendaftaran_id;
      }
      catch(e) {
         primary = null;
			status_bayar_id = null;
			pendaftaran_id_awal = null;
			pendaftaran_id = null;
			pembayaran_id = null;
			instalasi_id = null;
			ref_pendaftaran_id = null;
         pembayaran_deleted = null;
      }

		if(typeof _data !== 'undefined') {
			$("#detail-gabung").prop("disabled", false);
			
         if(pembayaran_id && pembayaran_deleted == false) {
				$("#detail-gabung").attr("action", "/kasir/inf-gabung-billing/detail-billing?pembayaran_id=" + pembayaran_id)

				//enable semua cetak invoice & kwitansi
				$("#cetak-invoice").prop('disabled', false); // invoice report designer
				$('#btn-cetak-detail-invoice').prop("disabled", false); // detail invoice report designer
				$("#btn-cetak-invoice").prop('disabled', false); // invoice non report designer
				$('#cetak-kwitansi').prop("disabled", false);
				
				//disable semua cetak rincian
				$("#cetak-rincian").prop('disabled', true); // rincian non report designer
				$('#cetak-detail-rincian-tagihan').prop("disabled", true); // detail rincian non report designer
				$('#cetak-detail-rincian-tagihan-designer').prop("disabled", true); // detail rincian report designer
				$('#print-detail-edit-tagihan').prop("disabled", true); // detail rincian report designer

				
				$("#cetak-invoice").attr('target', '_blank');
            $("#cetak-invoice").attr("data-target",cetakInvoice+ref_pendaftaran_id+"&invoice_id="+pembayaran_id);
         }
         else {
            if(ref_pendaftaran_id) {
					$("#detail-gabung").attr("action", "/kasir/inf-gabung-billing/detail-billing?id=" + pendaftaran_id_awal)

					//disable semua cetak invoice & kwitansi
					$("#cetak-invoice").prop('disabled', true); // invoice report designer
					$("#btn-cetak-invoice").prop('disabled', true); // invoice non report designer
					$('#btn-cetak-detail-invoice').prop("disabled", true); // detail invoice report designer
					$('#cetak-kwitansi').prop("disabled", true);

					//enable semua cetak rincian
					$("#cetak-rincian").prop('disabled', false); // rincian non report designer
					$('#cetak-detail-rincian-tagihan').prop("disabled", false); // detail rincian non report designer
					$('#cetak-detail-rincian-tagihan-designer').prop("disabled", false); // detail rincian report designer
					$('#print-detail-edit-tagihan').prop("disabled", false); // detail rincian report designer
					
					$("#cetak-rincian").attr("data-target",cetakRincian+ref_pendaftaran_id+"&instalasi_id="+instalasi_id);
					$("#cetak-detail-rincian-tagihan").attr("data-url",cetakDetailRincian+ref_pendaftaran_id+"&instalasi_id="+instalasi_id);
					$('#print-detail-edit-tagihan').attr("action", cetakDetailRincianReport+ + ref_pendaftaran_id+"&instalasi_id="+instalasi_id+"&isdetail=true"); // detail rincian report designer
            }
         }
			
			if(status_bayar_id != status_lunas) {
				$('.data-batal-gabung').prop("disabled", false);
			}
			else {
				$('.data-batal-gabung').prop("disabled", true);
			}
		}
		else {
			$('.data-batal-gabung').prop("disabled", true);
			$("#btn-cetak-invoice").prop("disabled", true);
			$('#cetak-kwitansi').prop("disabled", true);
			$("#cetak-rincian").prop('disabled', true);
			$('#btn-cetak-detail-invoice').prop("disabled", true);
			$('#cetak-detail-rincian-tagihan').prop("disabled", true);
			$('#cetak-detail-rincian-tagihan-designer').prop("disabled", true);
			$("#cetak-invoice").prop("disabled", true);
			$("#detail-gabung").prop("disabled", true);
			$('#print-detail-edit-tagihan').prop("disabled", true);
		}
   });
	
	$("#cetak-detail-rincian-tagihan-designer").click(function(e){
		e.preventDefault();
		var tableData = table.row(".selected").data();
		if(typeof tableData !== "undefined") {
			 var ref_pendaftaran_id = tableData.ref_pendaftaran_id;
			 var instalasi_id = tableData.instalasi_id;
			 var url = "/kasir/inf-pasien-belum-bayar/show-popup?id=" + ref_pendaftaran_id+"&instalasi_id="+instalasi_id;
			 window.open(url);
		}
  	});
  
   $('#btn-search__example').css('display', 'none');
   $('#btn-reset__example').css('display', 'none');
   $(document).on("click", ".btn-reset", function (e) {
      const tableId = "example";
      const element = $(`#filter-section__${tableId}`)
      const formWrapper = $(`#form-filter__${tableId}`)
      element.find('input').val('')
      element.find('select').val(null).trigger('change')
      element.find('#tgl_gabung-startDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      element.find('#tgl_gabung-endDate').val(moment().format("DD-MMM-YYYY")).trigger("change");
      const tableElement = $(`#${tableId}`).DataTable()
      showLoader()
      tableElement.context[0].ajax.data.advancedFilter = serializeArrayToJson(formWrapper)
      tableElement.ajax.reload()
   });
});

function getBulkAdmin(payload){
    $().docoForm("click", {
        url: "/kasir/inf-pasien-belum-bayar/get-bulk-biaya-admin",
        type: "POST",
        skipConfirm: true,
        skipSuccessNotif: true,
        data: {
            payload : payload,
        },
        success: function (data) {
			let response = data.response.data;
            for (const [key, value] of Object.entries(response)) {
                let tagihan =  parseFloat(docoHelper.convertToAngka($("#"+key).text()))
                tagihan = tagihan + parseFloat(value);
                $("#"+key).text(docoHelper.convertToRupiah(tagihan))

                if(key === response.length) {
                    hideLoader();
                }
            }
        },
    })}