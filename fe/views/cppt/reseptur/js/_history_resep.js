var tableHistoryResep
$(() => {
    tableHistoryResep = $('#table-history-resep').docoTabel({
        filter: false,
        // columnDefs: [ { dikomen dulu karna fitur multiple nya belom dibuat hehe
        //     orderable: false,
        //     className: 'select-checkbox',
        //     targets: 0
        // } ],
        // select: {
        //     style: 'os',
        //     selector: 'tr'
        // },
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: '/rajal/allow/get-list-history-resep?pasien_id=' + pasien_id + '&ruangan_id=' +$("#select_ruangan").val()+'&kelaspelayanan_id=' + kelaspelayanan_id +'&penjamin_id='+$("#penjamin_id").val(),
        columns: [
            // { dikomen dulu karna fitur multiple nya belom dibuat hehe
            //     data: null,
            //     orderable: false,
            //     searchable: false,
            //     render: () => {
            //         return ''
            //     }
            // },
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
            {
                title: "Aksi",
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData, metaData) => {
                    return `<button type="button" class="btn btn-labeled btn-info btn-xs btn-salin-resep" ${ !rowData.detail_resep.length ? `disabled title="Resep tidak dapat disalin karna resep tidak memiliki obat"` : ``} data-rows="${metaData.row}" data-nomor="${rowData.nomor}" style="margin-bottom: 10px !important">
                                <b><i class="fa fa-copy"></i></b> Salin Resep
                            </button>`
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
                            <td colspan="6" style="text-align:center">${headerNonRacikan}</td>
                        </tr>` : ''}
                        <tr style="background-color: #EAEAEA">
                            <td></td>
                            <td>${rsp.obatalkes_nama} </br></td>
                            <td>${rsp.signa_nama}</br></td>
                            <td>${rsp?.is_kronis==true?'Ya':'Tidak'} </br></td>
                            <td>${data.nama_pegawai}</br></td>
                            <td>${rsp.det_transaksi == null ? rsp.qty_transaksi : rsp.det_transaksi} ${rsp.satuan_kecil} </br></td>
                            <td colspan=2>${(rsp.etiket !=  null) ? rsp.etiket : ''}</td>
                        </tr>`;
                        headerNonRacikan1 = rsp.racikan_nama
                    } else if ( rsp.racikan_id == 1 ){
                        headerRacikan = rsp.racikan_nama+'-'+rsp.nama_racikan
                        generatedTable += 
                        `${headerRacikan1 != headerRacikan ? 
                            `<tr style="background-color: #EAEAEA">
                                <td colspan="6" style="text-align:center">${headerRacikan}</td>
                            </tr>` : ``}
                        <tr style="background-color: #EAEAEA">
                            <td>${rsp.racikan_nama} - ${rsp.rke}</td>
                            <td>${rsp.obatalkes_nama} </br></td> 
                            <td>${rsp.signa_nama}</br></td>
                            <td>${rsp?.is_kronis==true?'Ya':'Tidak'} </br></td>
                            <td>${data.nama_pegawai}</br></td>
                            <td>${rsp.det_transaksi == null ? rsp.qty_transaksi : rsp.det_transaksi} ${rsp.satuan_kecil} </br></td>
                            <td colspan=2>${(rsp.etiket !=  null) ? rsp.etiket : ''}</td>
                        </tr>`;
                        headerRacikan1 = rsp.racikan_nama+'-'+rsp.nama_racikan
                    }
                })
                myTable.row( row ).child( $(generatedTable).toArray() ).show();
            }
        },
        drawCallback: () => {
            var myTable = $('#table-history-resep').DataTable();
            $('.btn-salin-resep').bind('click', (event) => {
                event.preventDefault()
                const {delegateTarget} = event
                const {detail_resep} = myTable.rows( $(delegateTarget).attr('data-rows') ).data()[0]
                const _obatAlkesId = $.map(detail_resep, (_items) => { return _items.obatalkes_id })
                const _noResep = $(delegateTarget).attr('data-nomor')
                if ( list_temp_obat.length > 0 ) {
                    let _duplicateObat = []
                    $.each(list_temp_obat, (key, obatItem) => {
                        if ( $.inArray(parseInt(obatItem.obatalkes_id), _obatAlkesId) >= 0) {
                            _duplicateObat.push({
                                obatalkes_id: obatItem.obatalkes_id,
                                obatalkes_nama: obatItem.obatalkes_nama
                            })
                        }
                    })

                    if ( _duplicateObat.length ) {
                        let _text = '</br> <ul>'
                        $.each(_duplicateObat, ( key, duplicateItems ) => {
                            _text += `<li>${duplicateItems.obatalkes_nama}</li>`
                        })
                        _text += '</ul>'
                        docoNotification("warning", "Peringatan!", `Resep <strong>${_noResep}</strong> Tidak dapat dilakukan salin resep karena obat ${_text} sudah diinputkan`)
                        return false
                    }
                }
                
                $.ajax({
                    url: '/rajal/allow/get-medicine-details',
                    data: {
                        obatalkes_id: _obatAlkesId,
                        ruangan_id: $("#select_ruangan").val(),
                        penjamin_id: $("#penjamin_id").val(),
                        kelaspelayanan_id: kelaspelayanan_id,
                        kelastagihan_id: kelastagihan_id,
                    },
                    beforeSend: () => {
                        showLoader();
                    },
                    success: function (response) {
                        const {data} = response.data
                        console.log('detail_resep', detail_resep)
                        $.each(detail_resep, ( key, rspData) => {
                            if(rspData.is_kronis == 1 && enable_split_kronis == 1){
                                var hari = hari_resep_kronis;
                            }else{
                                var hari = null;
                            }
                            let _medicineDetails = data[data.findIndex( item => item.obatalkes_id == rspData.obatalkes_id)]
                            if (typeof _medicineDetails === 'undefined') {
                                docoNotification('error', 'Proses Gagal','Stok Obat '+rspData.obatalkes_nama+' Tidak ada');
                                return;
                            }
                            if ( rspData.racikan_id == 2) {
                                let _copyAttr = {
                                    detail_type: "non_racikan",
                                    racikan_id: "NR",
                                    obatalkes_id: rspData.obatalkes_id,
                                    obatalkes_nama: rspData.obatalkes_nama,
                                    rke: null,
                                    qty_reseptur: rspData.det_transaksi == null ? rspData.qty_transaksi : rspData.det_transaksi,
                                    qty_konversi: rspData.qty_konversi,
                                    hargasatuan_reseptur: _medicineDetails.hargaygdipakai,
                                    harganetto_reseptur: _medicineDetails.harganetto,
                                    satuankecil_id: rspData.satuankecil_id,
                                    satuankecil_text: rspData.satuan_kecil,
                                    satuaninput_id: rspData.satuaninput_id,
                                    satuaninput_text: rspData.satuan_input,
                                    signa: rspData.signa_nama,
                                    signa_id: rspData.signa_id,
                                    iterasi: rspData.signa_iterasi,
                                    etiket: rspData.etiket,
                                    is_kronis: rspData.is_kronis,
                                    hari: rspData.hari,
                                    additional_data: rspData.additional_data == null ? rspData.additional_reseptur : rspData.additional_data,
                                    racikan_text: "",
                                    qty_tersedia: _medicineDetails.qty_tersedia,
                                    qty_obat_signa: rspData.qty_obat_signa,
                                    stok_sisa: _medicineDetails.qty_tersedia
                                };
            
                                list_temp_obat.push(_copyAttr)
                                // appendObat(list_temp_obat)
                            } else if ( rspData.racikan_id == 1 ) {
                                let _copyAttr = {
                                    detail_type: "racikan_detail",
                                    racikan_id: "OR",
                                    obatalkes_id: rspData.obatalkes_id,
                                    obatalkes_nama: rspData.obatalkes_nama,
                                    rke: rspData.rke,
                                    qty_reseptur: rspData.det_transaksi == null ? rspData.qty_transaksi : rspData.det_transaksi,
                                    qty_konversi: rspData.qty_konversi,
                                    hargasatuan_reseptur: _medicineDetails.hargaygdipakai,
                                    harganetto_reseptur: _medicineDetails.harganetto,
                                    satuankecil_id: rspData.satuankecil_id,
                                    satuankecil_text: rspData.satuan_kecil,
                                    satuaninput_id: rspData.satuaninput_id,
                                    satuaninput_text: rspData.satuan_input,
                                    signa: rspData.signa_nama,
                                    signa_id: rspData.signa_id,
                                    iterasi: rspData.signa_iterasi,
                                    etiket: rspData.etiket,
                                    is_kronis: rspData.is_kronis,
                                    hari: rspData.hari,
                                    additional_data: rspData.additional_data == null ? rspData.additional_reseptur : rspData.additional_data,
                                    racikan_text: "",
                                    qty_racikan: rspData.qty_racikan,
                                    satuan_racikan_id: rspData.satuan_racikan_id,
                                    satuan_racikan_nama: rspData.satuan_racikan_nama,
                                    satuanracikan_id: rspData.satuanracikan_id,
                                    nama_racikan: rspData.racikan_nama,
                                    qty_tersedia: _medicineDetails.qty_tersedia,
                                    qty_obat_signa: rspData.qty_obat_signa,
                                    stok_sisa: _medicineDetails.qty_tersedia
                                };
            
                                list_temp_obat.push(_copyAttr)
                                // appendObat(list_temp_obat)
                            }
                        });
                        appendObat(list_temp_obat)
                        hideLoader()
                        docoNotification('success', 'Proses Berhasil', `Resep <strong>${$(delegateTarget).attr('data-nomor')}</strong> Berhasil Disalin!`)
                        $('.close-modal-riwayatresep').click()
                        $('#modal-reseptur').animate({
                            scrollTop: $("#tabel-reseptur").offset().top
                        }, 1000);
                    },
                    error: () => {
                        hideLoader()
                    }
                })
            })
        }
    });
})