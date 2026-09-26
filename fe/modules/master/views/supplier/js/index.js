/*
* @Author: Sigit
* @Date:   2018-06-06 09:33:02
* @Last Modified by:   Sigit
* @Last Modified time: 2018-06-06 16:31:26
*/

$(function(){
	// Global Var
	var table;

	// Event Ready
	$(document).ready(function() {
		// Generate Table
		table = $("#tb-supplier").docoTabel({
            sorting: [[2, "asc"]], 
			columnDefs: [ {
				orderable: false,
				className: "select-checkbox",
				targets: 0
			}],
			select: {
				style: "os",
				selector: "tr"
			},
			filter: true,
			displayLength: 10,
			processing: true,
			serverSide: true,
			ajax: baseUrl + "master/supplier/get-data",
			columns: [
				{title: "", data: null, defaultContent: "", searchable: false, orderable: false},
				{title: no, data: "row", searchable: false, orderable: false},
				{title: kodeSupplier, data: "supplier_kode"},
				{title: namaSupplier, data: "supplier_nama"},	
				{title: alamat, data: "supplier_alamat", searchable: true, orderable: true},
				{title: noTelphone, data: "no_tlp", searchable: true, orderable: true},	
			],
			scrollCollapse: true,
			language: {
				emptyTable: emptyTable,
				info: info,
				infoEmpty: infoEmpty,
				infoFiltered: infoFiltered,
				lengthMenu: lengthMenu,
				loadingRecords: loadingRecords,
				processing: processing,
				search: search,
				zeroRecords: zeroRecords,
				aria: {
					sortAscending: sortAscending,
					sortDescending: sortDescending
				}
			}
		});

		// Hide datatables filter form
		$(".dataTables_filter").hide();

		// Custom filter
		$(".filter-form").datatableBootstrapFilter(table , [
			[
                2,
                "<input type='text' class='form-control' name='supplier_kode' value='' placeholder='Kode Supplier' col-index='3'>"
            ],
            [
                3,
                "<input type='text' class='form-control' name='namaSupplier' value='' placeholder='Nama Supplier' col-index='3'>"
            ],
            [
                4,
                "<input type='text' class='form-control' name='supplier_alamat' value='' placeholder='Alamat' col-index='3'>"
            ],
            [
                5,
                "<input type='text' class='form-control' name='no_tlp' value='' placeholder='No Telepon' col-index='3'>"
            ],
		]);
	});

	// Event click
	$(document).on("click", ".data-reload", function() {
		table.draw();
	});

	// Event click
	$(document).on("click", "#tb-supplier tbody tr", function() {
		// Try catch
		try {
			// Get primary
			primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
		} catch (e) {
			// Make it false
			primaryKey = false;
		}

		// Assign to ubah
		$("#btn-edit").attr("action", updateUrl + primaryKey);

		// Check class selected
		if ($('#tb-supplier tr.selected').length == 0) {
			// Disable edit button
			$("#btn-edit").prop("disabled", true);
			$("#btn-delete").prop("disabled", true);
		}
		else {
			// Disable edit button
			$("#btn-edit").prop("disabled", false);
			$("#btn-delete").prop("disabled", false);
		}
	});

	// Print button clicked
	$('#btn-print').on('click', function(event) {
		event.preventDefault();
		window.open(
			'/rajal/pemeriksaan/cetak-tindakan?id=' + pendaftaranId, '_blank'
		);
	})
});