 // Event Reload
 $(document).on('click', '.data-reload', function() {
    table.draw();
});

//menghapus checkbox table yang di clone
$(document).ready(function(){
    $('.DTFC_Cloned').remove();
});

$(document).ready(function(){
    table = $('#example').docoTabel({
        'createdRow': function(row, data, index) {
            // formatting total tagihan dalam rupiah
            $('td', row).eq(10).attr('align', 'right');
            $('td', row).eq(10).html(`<span class='total_tagihan'>`+docoHelper.convertToRupiah(data['totaltagihan'])+`</span>`);
        },
        filter: true,
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets: 0
        }, {
            targets: 3,
            render: function(data, type, row) {
                var status;
                switch(data) {
                    case belum_proses:
                        status = `<span class='badge belum-proses'> Belum Proses </span>`;
                        break;
                    case dalam_proses:
                        status = `<span class='badge dalam-proses'> Dalam Proses </span>`;
                        break;
                    case diserahkan:
                        status = `<span class='badge diserahkan'> Diserahkan </span>`;
                        break;
                    case dibatalkan:
                        status = `<span class='badge dibatalkan'> Batal Reseptur </span>`;
                        break;
                    default:
                        break;
                }

                if(row['status_bayar'] == 'Sudah Bayar') {
                    status += ' <span class=\"badge lunas\">' + row['status_bayar'] + '</span>';
                    return status;
                } else {
                    status += ' <span class=\"badge dibatalkan\">' + row['status_bayar'] + '</span>';
                    return status;
                }
            }
        }],
        select: {
            style:    'os',
            selector: 'tr'
        },
        sorting: [[4, 'desc'],[2, 'asc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: true,
        stateDuration: -1,
        ajax: baseUrl+'apotek/informasi-reseptur/get-data',
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                width: '50px',
                defaultContent: ''
            },
            {
                width: '50px',
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false,
                class: 'text-center'
            },
            {title: 'No. Antrian', data: 'no_antrian', searchable: false, visible: false},
            {title: 'Status', data: 'status_reseptur_id'},
            {title: 'Tanggal', data: 'tgl_resep_dibuat'},
            {title: 'No Reseptur', data: 'no_reseptur', visible: false},
            {title: 'No Resep', data: 'no_resep', visible: false},
            {
                title: 'No. Reseptur / No. Resep',
                data: null,
                searchable: false,
                render: function(data, type, row) {
                    return data.no_reseptur + ' / ' + data.no_resep;
                }
            },
            {title: 'Instalasi - Ruangan', data: 'ruanganreseptur_id', visible: false},
            {
                title: 'Instalasi - Ruangan',
                data: 'instalasi_ruangan',
                searchable: false,
            },
            {title: 'Nama Dokter', data: 'nama_pegawai'},
            {title: 'Nama Pasien', data: 'nama', visible: false},
            {title: 'No. RM', data: 'no_rekam_medik', visible: false},
            {
                width: '400px',
                title: 'Nama Pasien / No. RM',
                data: null,
                searchable: false,
                render: function(data, type, row) {
                    if(data.no_rekam_medik != null) {
                        return data.nama + ' / ' + data.no_rekam_medik;
                    } else {
                        return data.nama;
                    }
                }
            },
            {title: 'No. Pendaftaran', data: 'no_pendaftaran'},
            {
                width: '200px',
                title: 'Cara Bayar - Penjamin',
                data: null,
                searchable: false,
                render: function(data, type, row) {
                    return data.carabayar_nama + ' - ' + row['penjamin_nama'];
                }
            },
            {title: 'Total Tagihan (Rp.)', data: 'totaltagihan', searchable: false},
            {title: 'Jumlah Obat Alkes', data: 'jml_obat', searchable: false},
            {
                title: 'Detail',
                data: 'detail',
                searchable: false,
                orderable: false,
                width:'1%',
                class: 'text-center'
            },
        ],
        rowCallback: (rowElement, data) => {
            if (data.carabayar_kode_warna != null) {
                $($(rowElement).find('td')[9]).css('background-color', data.carabayar_kode_warna)
                $($(rowElement).find('td')[9]).css('color', invertColor(data.carabayar_kode_warna, true))
            }
            if(data.is_udd == true){
                $($(rowElement).find('td')[4]).css('background-color', '#05bbbe')
            }
        },
        stateSaveCallback: function(settings,data) {
            localStorage.setItem( 'DataTables_' + settings.sInstance, JSON.stringify(data) )
        },
        stateLoadCallback: function(settings) {
            return JSON.parse( localStorage.getItem( 'DataTables_' + settings.sInstance ) )
        },
        stateSaveCallback: function(settings,data) {
            localStorage.setItem( 'DataTables_' + settings.sInstance, JSON.stringify(data) )
        },
        stateLoadCallback: function(settings) {
            return JSON.parse( localStorage.getItem( 'DataTables_' + settings.sInstance ) )
        },
    });
    $('.dataTables_filter').hide();

    $("#example tbody").on("click", "tr", function(){
        var dt = typeof table.rows('.selected').data()[0] != 'undefined' ? table.rows('.selected').data()[0] : [];
        var is_udd = typeof dt['is_udd'] != 'undefined' ? dt['is_udd'] : null;
        if(is_udd == true){
            $(".data-lihat").attr('disabled', true);
            $(".btn-retur").attr('disabled', true);
            $(".data-edit").attr('disabled', true);
        }else{
            $(".data-lihat").attr('disabled', false);
            $(".btn-retur").attr('disabled', false);
            $('.data-edit').attr('disabled', false);
        }
    });

    $('.filter-form').datatableBootstrapFilter(table, [
        [
            4,
            filterDate
        ],
        [
            3,
            filterStatusResep
        ],
        [
            8,
            filterInstalasi
        ]
    ], {0:4, 1:5, 2:8}, true);

    dateRangeHelper('.startDate','.endDate','.targetDate');
    $('.carabayar').select2({
        placeholder: '— Pilih Cara Bayar —',
    });

    $('.penjamin').select2({
        placeholder: '— Pilih Penjamin —',
    });

    $('#btn-retur').attr('disabled', disableRetur);
    $('#btn-serahkan').attr('disabled', true);
    $('#btn-worklist').attr('disabled', true);
    $('#btn-batal-proses').css('display', 'none');
    $('#btn-edit-reseptur').css('display', disableEditReseptur);
    $('#btn-edit-resep').css('display', disableEditResep);
    $('#edit-resep').css('display', 'none');

    $(document).on('click', '#example tr', function(){
        var _data = table.row('.selected').data();
        if (konfig_approval) {
            $('#edit-resep').attr('disabled', false);
        }
        if(typeof _data !== 'undefined'){
            $('#btn-worklist').attr('disabled', false);
            if(_data.status_bayar == 'Belum Lunas'){
                if(_data.no_resep == '-') {
                    disableRetur = true;
                } else {
                    disableRetur = false;
                    // resep dibatalkan atau reseptur
                    if(_data.status_reseptur_id == dibatalkan || _data.status_reseptur == 'Belum Proses') {
                        disableRetur = true;
                        disableEditReseptur = 'none';
                        disableEditResep = 'none';
                    } else if(_data.status_reseptur_id != diserahkan) {
                        disableRetur = true;
                    } else {
                        disableRetur = false;
                    }
                }

                if(_data.status_reseptur_id == diserahkan) {
                    if (konfig_approval) {
                        $('#lihat-resep').css('display','inline-block');
                        $('#edit-resep').css('display', 'none');
                    }
                    disableEditResep = 'none';
                    disableEditReseptur = 'none';
                } else if(_data.status_reseptur_id == dibatalkan) {
                    if (konfig_approval) {
                        $('#lihat-resep').css('display','inline-block');
                        disableEditReseptur = 'none';
                        disableEditResep = 'none';
                    }
                } else {
                    if(_data.status_reseptur_id == dibatalkan) {
                        if (konfig_approval) {
                            $('#lihat-resep').css('display','inline-block');
                        }
                        disableEditReseptur = 'none';
                        disableEditResep = 'none';
                    } else if(_data.status_worklist == '674'){
                        // untuk non aproval
                        if (!konfig_approval) {
                            if(_data.no_reseptur == '-'){
                                disableEditReseptur = 'none';
                                disableEditResep = 'inline-block';
                            } else {
                                disableEditReseptur = 'inline-block';
                                disableEditResep = 'none';
                            }
                        }
                    } else {
                        // untuk non aproval
                        if (!konfig_approval) {
                            if(_data.reseptur_id == null) {
                                disableEditReseptur = 'none';
                                disableEditResep = 'inline-block';
                            } else {
                                disableEditReseptur = 'inline-block';
                                disableEditResep = 'none';
                            }
                        }
                    }
                }
                if(_data.is_stopakomodasi) {	
                    disableRetur = true;	
                }
            }else {
                disableRetur = true;
                disableEditReseptur = 'none';
                disableEditResep = 'none';
                if (konfig_approval) {
                    $('#lihat-resep').css('display','inline-block');	
                    $('#edit-resep').css('display', 'none').attr('disabled', true);
                }
            }

            if(_data.status_reseptur_id == dalam_proses){
                $('#btn-serahkan').attr('disabled',false);
                if (konfig_approval) {
                    disableEditReseptur = 'none';
                    disableEditResep = 'none';
                    $('#lihat-resep').css('display','inline-block');
                    $('#edit-resep').css('display', _data.no_reseptur == "-" ? 'inline-block' : 'none');
                }
            }else{
                $('#btn-serahkan').attr('disabled',true);
            }
            //untuk flow approval
            if(konfig_approval && _data.status_reseptur_id == belum_proses){
                $('#lihat-resep').css('display','none');
                if(_data.jenis == 'resep') {
                    disableEditReseptur = 'none';
                    disableEditResep = 'inline-block';
                } else {
                    disableEditReseptur = 'inline-block';
                    disableEditResep = 'none';
                    $('#edit-resep').css('display', 'none');
                }
            }

            if(_data.is_udd && (_data.status_reseptur_id == diserahkan || _data.status_reseptur_id == dalam_proses)) {    
                $('#btn-batal-proses').css('display', 'inline-block');
            } else {
                $('#btn-batal-proses').css('display', 'none');
            }

            $('#btn-retur').attr('disabled', disableRetur);
            $('#btn-edit-reseptur').css('display', disableEditReseptur);
            $('#btn-edit-resep').css('display', disableEditResep);
        }else{
            $('#btn-worklist').attr('disabled', true);
            $('#btn-batal-proses').css('display', 'none');
            $('.data-retur').attr('disabled', true);
            $('.data-retur').removeClass('btn-toolbar');
            $('.data-retur').removeAttr('href');
            $('#btn-retur').attr('disabled', true);
            $('#btn-serahkan').attr('disabled', true);
            disableEditReseptur = 'none';
            disableEditResep = 'none';
            $('#btn-edit-reseptur').css('display', disableEditReseptur);
            $('#btn-edit-resep').css('display', disableEditResep);
            if (konfig_approval) $('#edit-resep').css('display', 'none');
        }
    });

    $(document).on('click','#btn-serahkan',function(){
        var tableData = table.row('.selected').data();
        if(typeof tableData !== 'undefined' && ('no_reseptur' in tableData || 'no_resep' in tableData)){
            if(tableData.no_reseptur !== '-' || tableData.no_resep !== '-'){
                var url = '/apotek/informasi-reseptur/serahkan-obat';
                var nomor;
                if(tableData.no_resep != '-') {
                    nomor = tableData.no_resep;
                } else {
                    nomor = tableData.nomor;
                }
                if(tableData.status_reseptur_id == dalam_proses){
                    $(this).docoForm('click',{
                        url: url,
                        title:'Sukses',
                        method:'POST',
                        data: {
                            nomor: nomor,
                            ruangan_id: tableData.ruangan_id,
                            instalasiasal_id: tableData.instalasi_reseptur_id,
                            is_udd: tableData.is_udd,
                            worklist: tableData.status_reseptur_id
                        },
                        type:'json',
                        success:function(){
                            table.ajax.reload(null, false);
                        }
                    });
                }else{
                   docoNotification('warning','Terjadi Kesalahan','Hanya resep dengan status Dalam Proses yang dapat diserahkan!');
                }
            }else{
                docoNotification('warning','Terjadi Kesalahan','Reseptur belum diproses!');
            }
        }else{
            docoNotification('warning','Terjadi Kesalahan','Belum ada data yang dipilih');
        }
    });
    
    $('.instalasi_ruangan_search').select2({
        language: {
            errorLoading: function () { return 'Searching...' }
        },
        placeholder: '',
        minimumInputLength: 3,
        allowClear: true,
        ajax: {
            url: '/apotek/informasi-reseptur/get-list-instalasi-ruangan',
            dataType: 'json',
            quietMillis: 250,
            data: function(term, page){
                return{
                    q: term,
                    page: page
                }
            },
            processResults: function (res) {
                var arr = []
                $.each(res.result, function (index, value) {
                    arr.push({
                        id: value.ruangan_id,
                        text: value.instalasi_ruangan
                    })
                })
                return {
                    results: arr
                };
            }
        },
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) { return m; },
    });

    $(document).on('click',".btn-worklist", function() {
        var tableData = table.row('.selected').data();
        if(tableData.no_resep == null || tableData.no_resep == '-'){
            nomor = tableData.no_reseptur;
        }else{
            nomor = tableData.no_resep;
        }
        
        if (nomor == null) {
            return false;
        }
        
        getWorklist(nomor);
    });

    function getWorklist(nomor) {
        var url = '/apotek/worklist-farmasi?';
        params_ruangan = $.param({ term: nomor });
        var params = params_ruangan
        window.open(url+params.toString(), '_blank');
    }

    $(document).on('click','.data-reset',function(){
        location.reload();
        var table = $('#example').DataTable();
        table.state.clear();
        table.draw();
        table = $("#example").dataTable();
        table.fnDraw();
        return false;
    });

    $(document).on('click', '#btn-batal-proses', function(){
        let tableData = table.row('.selected').data();
        let no_transaksi = tableData.nomor;
        $(this).docoForm('click', {
            url: '/apotek/unit-dose-dispensing/batal-proses',
            confirmMessage: 'Apakah Anda yakin ingin melakukan batal proses untuk nomor transaksi '+no_transaksi+'?',
            skipErrorNotif: true,
            skipSuccessNotif: true,
            method: 'POST',
            data: {
                no_transaksi: no_transaksi
            },
            type: 'json',
            success: function(response){
                let data = response.data;
                docoNotification('success', 'Proses Berhasil !', data.message);
                table.draw();
            }
        });
    })
});
