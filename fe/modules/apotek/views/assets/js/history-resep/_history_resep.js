var tableHistoryResep
$(() => {
    tableHistoryResep = $('#table-history-resep').docoTabel({
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: '/apotek/informasi-reseptur/get-list-history-resep?pasien_id=' + pasien_id +'&no_resep=' + no_resep,
        columns: [
            {
                title: "Tanggal Resep",
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData) => {
                    let tgl_update = (rowData.last_update != null) ? '<br><br> Revisi Farmasi <br>' + moment(rowData.last_update).format('DD/MM/YYYY HH:mm:00') : '';

                    return moment(rowData.tgl_resep).format('DD/MM/YYYY HH:mm:00') + tgl_update;
                }
            },
            {
                title: "No. Resep",
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData) => {
                    return `<b>${(rowData.nomor)}</b> </br>
                    ${rowData.instalasi_reseptur} / ${rowData.ruangan_reseptur}`
                }
            },
            {
                title: "Tipe",
                data: 'status_racikan',
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData) => {
                    return (rowData.kategori_resep_kode != null) ? `<b title="`+rowData.kategori_resep_nama+`">${rowData.kategori_resep_kode}</b>` : (rowData?.reseptur_detail_racikan?.length != 0 ? 'Racikan' : 'Non Racikan');
                }
            },
            {
                title: "Kronis",
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData) => {
                    return '';
                }

            },
            {
                title: "Dokter Resep",
                data: 'nama_pegawai',
                searchable: false,
                orderable: false,
            },
            {
                title: "Instalasi / Ruangan Tujuan",
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData) => {
                    return `${rowData.instalasi_resep} / ${rowData.ruangan_tujuan}`
                }
            },
        ],
        createdRow: (row, data, index) => {
            var myTable = $('#table-history-resep').DataTable();
            if ( data.detail_resep.length ) {
                var generatedTable = ``
                var headerNonRacikan = headerNonRacikan1 = headerRacikan = headerRacikan1 = null;
                $.each( data.detail_resep, (key, rsp) => {
                    if ( rsp.racikan_id == 2 ) {
                        headerNonRacikan = rsp.racikan_nama
                        generatedTable += 
                        `${headerNonRacikan1 != headerNonRacikan ? 
                        `<tr style="background-color: #EAEAEA">
                            <td colspan="6" style="font-weight: bold;text-align:center">${headerNonRacikan}</td>
                        </tr>` : ''}
                        <tr style="background-color: #EAEAEA">
                            <td></td>
                            <td>${rsp.obatalkes_nama} </br></td>
                            <td>${rsp.signa_nama}</br></td>
                            <td>${rsp?.is_kronis==true?'Ya':'Tidak'} </br></td>
                            <td>${data.nama_pegawai == null ? '-' : data.nama_pegawai}</br></td>
                            <td>${rsp.det_transaksi == null ? rsp.qty_transaksi : rsp.det_transaksi} ${rsp.satuan_kecil} </br></td>
                        </tr>`;
                        headerNonRacikan1 = rsp.racikan_nama
                    } else if ( rsp.racikan_id == 1 ){
                        headerRacikan = rsp.racikan_nama+'-'+rsp.nama_racikan
                        generatedTable += 
                        `${headerRacikan1 != headerRacikan ? 
                            `<tr style="background-color: #EAEAEA">
                                <td colspan="6" style="font-weight: bold;text-align:center">${headerRacikan}</td>
                            </tr>` : ``}
                        <tr style="background-color: #EAEAEA">
                            <td>${rsp.racikan_nama} - ${rsp.rke}</td>
                            <td>${rsp.obatalkes_nama} </br></td> 
                            <td>${rsp.signa_nama}</br></td>
                            <td>${rsp?.is_kronis==true?'Ya':'Tidak'} </br></td>
                            <td>${data.nama_pegawai == null ? '-' : data.nama_pegawai}</br></td>
                            <td>${rsp.det_transaksi == null ? rsp.qty_transaksi : rsp.det_transaksi} ${rsp.satuan_kecil} </br></td>
                        </tr>`;
                        headerRacikan1 = rsp.racikan_nama+'-'+rsp.nama_racikan
                    }
                })
                myTable.row( row ).child( $(generatedTable).toArray() ).show();
            }
        }
    });
})