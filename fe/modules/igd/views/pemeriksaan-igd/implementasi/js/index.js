// Tabel
var tabel_implementasi;
function formattingChild ( d ) {
	if(d.data_implementasi == null || d.data_implementasi.length < 1){
		return 'Tidak ada data implementasi';
	}else{
        // var count = 
        var tableHtml = '<div class="table-responsive">' 
        tableHtml += '<table class="table table-bordered">';
        tableHtml += '<thead>';
        tableHtml += '<tr class ="bg-inverse">';
        tableHtml += '<th>No</th>';
        tableHtml += '<th>Tanggal/Pukul</th>';
        tableHtml += '<th>Implementasi</th>';
        tableHtml += '<th>Petugas 1</th>';
        tableHtml += '<th>Petugas 2</th>';
        tableHtml += '<th>Catatan Implementasi</th>';
        tableHtml += '</tr>';
        tableHtml += '</thead>';
        tableHtml += '<body>';
        var detailImplemen = d.data_implementasi;
        $(detailImplemen).each(function(index){
            tableHtml += '<tr>';
            tableHtml += "<td>"+(parseInt(index)+1)+"</td>";
            tableHtml += "<td>"+detailImplemen[index].tgl_implementasi+"</td>";
            tableHtml += "<td>"+detailImplemen[index].implementasi;
            if(detailImplemen[index].paket != null && detailImplemen[index].daftar_paket != null){
                var objPaket = JSON.parse(detailImplemen[index].daftar_paket);
                tableHtml += '<ul>';
                $(objPaket).each(function(index){
                    tableHtml += '<li>';
                    tableHtml += objPaket[index];
                    tableHtml += '</li>';
                });
                tableHtml += '</ul>';
            }
            tableHtml += "</td>";
            if(detailImplemen[index].perawat_1){
                tableHtml += "<td>"+detailImplemen[index].perawat_1+"</td>";
            }else{
                tableHtml += "<td>-</td>";
            }
            if(detailImplemen[index].perawat_2){
                tableHtml += "<td>"+detailImplemen[index].perawat_2+"</td>";
            }else{
                tableHtml += "<td>-</td>";
            }
            if(detailImplemen[index].catatan_implementasi){
                tableHtml += "<td>"+detailImplemen[index].catatan_implementasi+"</td>";
            }else{
                tableHtml += "<td>-</td>";
            }

            tableHtml += '</tr>';
        });
        tableHtml += '</body>';
        tableHtml += '</table>';
        tableHtml += '</div>';
        tableHtml += '<br>';
        return tableHtml;
    }
}
// Initiate page
$(document).ready(function() {
	// Generate Table
	tabel_implementasi = $("#tb-implementasi").DataTable({
        columnDefs: [ {
            sortable: false,
            className: "select-checkbox",
            targets:   0
        }],
        select: {
            style:    "os",
            selector: "td:first-child"
        },
		filter: false,
		ordering:false,
		displayLength: 10,
		processing: true,
		serverSide: true,
		scrollX: true,
		ajax: baseUrl+"igd/pemeriksaan-igd/implementasi-get-data?id="+pendaftaran_id,
		columns: [
            {data: null, searchable: false, sortable: false, defaultContent:""},
			{
				title: "No",
				data: "dataNumber",
				searchable: false,
				orderable: false,
                render:function(data, type, row){
                    var is_transaction = 0;
                    if(data.grouping_tipe != undefined){
                        if(data.grouping_tipe == "TINDAKANBMHP"){
                            is_transaction = 1;
                        }
                    }
                    return '<a type="button" class="btn-idx-instruksi" data-is_transaction="'+is_transaction+'" data-target="id='
                    +data.pendaftaran_id+'&pasien_id='+data.pasien_id+'&instruksi_id='+data.instruksi_id
                    +'&cppt_id='+data.cppt_id+'">' + data.rowNum + '</a>';
                }
			},
            {
            	title: "Detail implementasi".split(" ").join("<br/>"),
                className:      'details-control text-center',
                orderable:      false,
                data:           "grouping_tipe",
                width:100,
                defaultContent: '<i class="fa fa-plus-square fa-2x bg-teal"></i>',
            },
            {
                title: dokter, 
                data: "dokter_instruksi", 
                searchable: false, 
                orderable: false
            },
            {
                title: instruksiDokter, 
                data: "list_instruksi", 
                searchable: false, 
                orderable: false,
            },
            {
                title: status, 
                data: "list_instruksi_status", 
                searchable: false, 
                orderable: false,
            },
		],
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
		},
        fnRowCallback: function(nRow,data,iDisplayIndex, iDisplayIndexFull) {
        
            // if(data.instruksi_deleted == true){
            //     $(nRow).addClass("strikeout");
            // }
        }
	});

	// Hide filter
	$(".dataTables_filter").hide();

	$('#tb-implementasi tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = tabel_implementasi.row(tr);
        if ( row.child.isShown() ) {
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            row.child( formattingChild(row.data()) ).show();
            tr.addClass('shown');
        }
    });

    $('#tb-implementasi tbody').on('click','.btn-idx-instruksi',function() {
        var tr = $(this).closest('tr');
        var row = tabel_implementasi.row(tr);
        var dataRow = row.data();
        if(dataRow.instruksi_deleted == false){
            if(dataRow.instruksi_implemented == true){
                if(dataRow.grouping_tipe == 'RESEPTUR'){
                    docoNotification('warning', 'Perhatian', 'Resep sudah diapprove oleh Apotek!');
                }else if(dataRow.grouping_tipe == 'PENUNJANG'){
                    docoNotification('warning', 'Perhatian', 'Orderan sudah diapprove oleh Unit Tujuan!');
                }else{
                    docoNotification('warning', 'Perhatian', 'Instruksi sudah diimplementasi!');
                }
            }else{
                if($(this).data('is_transaction') == '1'){
                    $('#content-implementasi').docoLoad({
                        url: '/igd/pemeriksaan-igd/implementasi-transaksi?'+$(this).data('target'),
                        dataType: 'html',
                        success : function(data) {
                        }
                    });
                }else{
                    docoNotification('warning', 'Perhatian', 'Transaksi hanya tersedia untuk Tindakan & BMHP!');
                }
            }
        }else{
            docoNotification('warning', 'Perhatian', 'Data Instruksi Telah Dihapus!');
        }
    });

    // $('#tb-implementasi tbody').on('mousedown','tr', function (e) {
    //     if( e.button == 2 ) { 
    //         console.log('cek');
    //     }
    // });
    $('#btn-edit-instruksi').on('click',function(){
        var tableData = tabel_implementasi.row(".selected").data();
        if (typeof tableData !== "undefined") {
            if(tableData.instruksi_deleted == false){
                if(tableData.is_batal_penunjang == true){
                    docoNotification('warning', 'Perhatian', 'Penunjang Telah Dibatalkan!');
                }else if(tableData.instruksi_implemented != true && tableData.is_verifikasi_dpjp != true){
                    if ("dataNumber" in tableData) {
                        var v_pendaftaran_id = tableData.dataNumber.pendaftaran_id;
                        var v_pasien_id = tableData.dataNumber.pasien_id;
                        var v_instruksi_id = tableData.dataNumber.instruksi_id;
                        var v_cppt_id = tableData.dataNumber.cppt_id;
                        var v_tipe_instruksi = tableData.dataNumber.grouping_tipe;

                        if(v_tipe_instruksi != undefined){
                            $('.nav-tabs a[href="#view-asesmen-dpjp"]').tab('show');
                            $('#content-asesmen-dpjp').docoLoad({
                                url: '/igd/pemeriksaan-igd/ubah-instruksi?'+
                                'id='+v_pendaftaran_id+
                                '&cppt_id='+v_cppt_id+
                                '&instruksi_id='+v_instruksi_id+
                                '&jns_instruksi='+v_tipe_instruksi+
                                '&from=implementasi',
                                dataType: 'html',
                                success : function(data) {
                                }
                            });
                        }
                    }
                }else{
                    if(tableData.grouping_tipe == 'RESEPTUR'){
                        if(tableData.is_verifikasi_dpjp == true){
                            docoNotification('warning', 'Perhatian', 'Instruksi sudah diapprove DPJP!');
                        }else{
                            docoNotification('warning', 'Perhatian', 'Resep sudah diapprove oleh Apotek!');
                        }
                    }else if(tableData.grouping_tipe == 'PENUNJANG'){
                        if(tableData.is_verifikasi_dpjp == true){
                            docoNotification('warning', 'Perhatian', 'Instruksi sudah diapprove DPJP!');
                        }else{
                            docoNotification('warning', 'Perhatian', 'Orderan sudah diapprove oleh Unit Tujuan!');
                        }
                    }else{
                        if(tableData.is_verifikasi_dpjp == true){
                            docoNotification('warning', 'Perhatian', 'Instruksi sudah diapprove DPJP!');
                        }else{
                            docoNotification('warning', 'Perhatian', 'Instruksi sudah diimplementasi!');
                        }
                    }
                }
            }else{
                docoNotification('warning', 'Terjadi Kesalahan', 'Data Instruksi Telah Dihapus!');
            }
        }else{
            docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');
        }
    });

    $('#btn-hapus-instruksi').on('click',function(){

        var tableData = tabel_implementasi.row(".selected").data();
        if (typeof tableData !== "undefined") {
            if(tableData.instruksi_deleted == false){
                if(tableData.is_batal_penunjang == true){
                    docoNotification('warning', 'Perhatian', 'Penunjang Telah Dibatalkan!');
                }else if(tableData.instruksi_implemented != true && tableData.is_verifikasi_dpjp != true){
                    if ("dataNumber" in tableData) {
                        var v_pendaftaran_id = tableData.dataNumber.pendaftaran_id;
                        var v_pasien_id = tableData.dataNumber.pasien_id;
                        var v_instruksi_id = tableData.dataNumber.instruksi_id;
                        var v_cppt_id = tableData.dataNumber.cppt_id;
                        var v_tipe_instruksi = tableData.dataNumber.grouping_tipe;

                        if(v_tipe_instruksi != undefined){
                            $(this).docoForm("delete", {
                                url: "/igd/pemeriksaan-igd/hapus-terapi"+
                                "?id="+v_pendaftaran_id+
                                "&cppt_id="+v_cppt_id+
                                "&instruksi_id="+v_instruksi_id+
                                "&tipeinstruksi="+v_tipe_instruksi,
                                additional: 'data-rm',
                                success : function(data) {
                                    // Draw tabel
                                    tabel_implementasi.draw();
                                }
                            });
                        }
                    }
                }else{
                    if(tableData.grouping_tipe == 'RESEPTUR'){
                        if(tableData.is_verifikasi_dpjp == true){
                            docoNotification('warning', 'Perhatian', 'Instruksi sudah diapprove DPJP!');
                        }else{
                            docoNotification('warning', 'Perhatian', 'Resep sudah diapprove oleh Apotek!');
                        }
                    }else if(tableData.grouping_tipe == 'PENUNJANG'){
                        if(tableData.is_verifikasi_dpjp == true){
                            docoNotification('warning', 'Perhatian', 'Instruksi sudah diapprove DPJP!');
                        }else{
                            docoNotification('warning', 'Perhatian', 'Orderan sudah diapprove oleh Unit Tujuan!');
                        }
                    }else{
                        if(tableData.is_verifikasi_dpjp == true){
                            docoNotification('warning', 'Perhatian', 'Instruksi sudah diapprove DPJP!');
                        }else{
                            docoNotification('warning', 'Perhatian', 'Instruksi sudah diimplementasi!');
                        }
                    }
                }
            }else{
                docoNotification('warning', 'Terjadi Kesalahan', 'Data Instruksi Telah Dihapus!');
            }
        }else{
            docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');
        }
    });
    $('#btn-implementasi').on('click',function(){
        var tableData = tabel_implementasi.row(".selected").data();
        if (typeof tableData !== "undefined") {
            if(tableData.instruksi_deleted == false){
                if(tableData.instruksi_implemented == true){
                    if(tableData.grouping_tipe == 'RESEPTUR'){
                        docoNotification('warning', 'Perhatian', 'Resep sudah diapprove oleh Apotek!');
                    }else if(tableData.grouping_tipe == 'PENUNJANG'){
                        docoNotification('warning', 'Perhatian', 'Orderan sudah diapprove oleh Unit Tujuan!');
                    }else{
                        docoNotification('warning', 'Perhatian', 'Instruksi sudah diimplementasi!');
                    }
                }else{
                    if ("dataNumber" in tableData) {
                        var v_pendaftaran_id = tableData.dataNumber.pendaftaran_id;
                        var v_pasien_id = tableData.dataNumber.pasien_id;
                        var v_instruksi_id = tableData.dataNumber.instruksi_id;
                        var v_cppt_id = tableData.dataNumber.cppt_id;
                        var v_tipe_instruksi = tableData.dataNumber.grouping_tipe;

                        if(v_tipe_instruksi != undefined){
                            if(v_tipe_instruksi == "TINDAKANBMHP"){                 
                                $('#content-implementasi').docoLoad({
                                    url: '/igd/pemeriksaan-igd/implementasi-transaksi?id='+v_pendaftaran_id+'&pasien_id='+v_pasien_id+'&instruksi_id='+v_instruksi_id+'&cppt_id='+v_cppt_id,
                                    dataType: 'html',
                                    success : function(data) {
                                    }
                                });
                            }else{
                                docoNotification('warning', 'Perhatian', 'Transaksi hanya tersedia untuk Tindakan & BMHP!');
                            }
                        }
                    }
                }
            }else{
                docoNotification('warning', 'Terjadi Kesalahan', 'Data Instruksi Telah Dihapus!');
            }
        }else{
            docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');
        }
    });

});