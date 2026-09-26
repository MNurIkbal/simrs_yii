let tableDiagnosaIdrg;

$(document).ready(function () {
    // Set localstorage
    localStorage.removeItem('koreksi-diagnosa-idrg');
    localStorage.removeItem('koreksi-procedure-idrg');
    localStorage.removeItem('koreksi-diagnosa-inacbgs');
    localStorage.removeItem('koreksi-procedure-inacbgs');
    localStorage.removeItem('koreksi-idrg-accpdx');
    localStorage.removeItem('koreksi-validasi-inacbgs');

    // Set Table
    tableDiagnosaIdrg = $('#idrgDiagnosaTable').DataTable({
        searching: false,
        pageLength: 5,
        lengthChange: false,
        columns: [
            { data: 'id', orderable: false},
            { data: 'name', orderable: false},
            { data: 'kode', orderable: false},
            { data: 'aksi', orderable: false}
        ]
    });

    tableProcedureIdrg = $('#idrgProcedureTable').DataTable({
        searching: false,
        pageLength: 5,
        lengthChange: false,
        columns: [
            { data: 'id', orderable: false},
            { data: 'name', orderable: false},
            { data: 'multiplicity', orderable: false},
            { data: 'kode', orderable: false},
            { data: 'aksi', orderable: false}
        ]
    });

    // Set Table
    tableDiagnosaInacbgs = $('#inacbgsDiagnosaTable').DataTable({
        searching: false,
        pageLength: 5,
        lengthChange: false,
        autoWidth: false,
        columns: [
            { data: 'id', width: '1%', orderable: false },
            { data: 'name', width: '70%', orderable: false },
            { data: 'kode', width: '15%', orderable: false },
            { data: 'aksi', width: '15%', orderable: false }
        ]
    });

    tableProcedureInacbgs = $('#inacbgsProcedureTable').DataTable({
        searching: false,
        pageLength: 5,
        lengthChange: false,
        autoWidth: false,
        columns: [
            { data: 'id', width: '1%', orderable: false },
            { data: 'name', width: '70%', orderable: false},
            { data: 'multiplicity', width: '15%', orderable: false, visible: false},
            { data: 'kode', width: '15%', orderable: false },
            { data: 'aksi', width: '15%', orderable: false }
        ]
    });

    // Generate Diagnosa if exist.
    generateDiagnosa()
    generateProcedure()
    generateDiagnosaInacbgs()
    generateProcedureInacbgs()

    // Init Diagnosa
    getDiagnosa(true)
    getDiagnosa(false)

    validateDiagnosaInacbgs()

    /**
     * Function for add diagnosa IDRG.
     */
    $('#btn-add-diagnosa-idrg').on('click', function () {
        let valueIdrg = $('#idrgDiagnosa').val()
        let selectedData = $('#idrgDiagnosa').select2('data')[0]
        let koreksiDiagnosaIdrg = localStorage.getItem('koreksi-diagnosa-idrg')
        koreksiDiagnosaIdrg = JSON.parse(koreksiDiagnosaIdrg)

        if (!valueIdrg) {
            docoNotification('error', 'Perhatian', 'Diagnosa belum dipilih')
            return false;
        }

        let newData = {
            uuid: parseInt(valueIdrg),
            id: parseInt(valueIdrg),
            name: selectedData.text,
            kode: selectedData.kode,
            isPrimer : koreksiDiagnosaIdrg == null || koreksiDiagnosaIdrg.length == 0 || koreksiDiagnosaIdrg == '' ? true : false
        }
        
        let duplikat = false
        if (koreksiDiagnosaIdrg != undefined || koreksiDiagnosaIdrg.length > 0) {
            let data = koreksiDiagnosaIdrg

            // Cek Data Duplikat
            data.forEach((item) => {
                if (item.id == valueIdrg) {
                    duplikat = true
                    return false;
                }
            })

            if (duplikat) {
                docoNotification('error', 'Perhatian', 'Diagnosa sudah pernah dipilih !')
                return false;
            }
            
            data.push(newData)
            localStorage.setItem('koreksi-diagnosa-idrg', JSON.stringify(data))
        } else {
            newData.isPrimer = true
            localStorage.setItem('koreksi-diagnosa-idrg', JSON.stringify([newData]))
        }

        if (duplikat) {
            docoNotification('error', 'Perhatian', 'Diagnosa sudah pernah dipilih !')
            return false;
        }
        
        let buttonPrimer = componentButtonPrimer(newData, 'btn-set-primer-diagnosa-idrg');

        clearSelect()
        hideAndDisabledSectionIdrg()
        
        tableDiagnosaIdrg.row.add({
            id: tableDiagnosaIdrg.rows().count() + 1,
            name: selectedData.text,
            kode: buttonPrimer,
            aksi: `
                <div>
                    <button class="btn btn-danger btn-custom-delete btn-remove-idrg" dig-id="${valueIdrg}">Hapus</button>
                </div>
            `
        }).draw()
    })

    /**
     * Function for add procedure IDRG.
     */
    $('#btn-add-procedure-idrg').on('click', function () {
        let valueIdrg = $('#idrgProcedure').val()
        let selectedData = $('#idrgProcedure').select2('data')[0]
        let koreksiProcedureIdrg = localStorage.getItem('koreksi-procedure-idrg')
        koreksiProcedureIdrg = JSON.parse(koreksiProcedureIdrg)

        if (!valueIdrg) {
            docoNotification('error', 'Perhatian', 'Diagnosa belum dipilih')
            return false;
        }

        let countProcedure = 1;
        let newData = {
            uuid: tableProcedureIdrg.rows().count() + 1,
            id: parseInt(valueIdrg),
            name: selectedData.text,
            kode: selectedData.kode,
            multiplicity: 1,
            isPrimer : koreksiProcedureIdrg == null || koreksiProcedureIdrg == '' || koreksiProcedureIdrg.length == 0 ? true : false
        }
        
        if (koreksiProcedureIdrg != undefined || koreksiProcedureIdrg.length > 0) {
            let data = koreksiProcedureIdrg;
            data.push(newData)
            localStorage.setItem('koreksi-procedure-idrg', JSON.stringify(data))
        } else {
            localStorage.setItem('koreksi-procedure-idrg', JSON.stringify([newData]))
        }
        
        clearSelect(false)
        refreshProcedure(true)
    })

    /**
     * Function for add diagnosa INACBGS.
     */
    $('#btn-add-diagnosa-inacbgs').on('click', function () {
        let valueIdrg = $('#inacbgsDiagnosa').val()
        let selectedData = $('#inacbgsDiagnosa').select2('data')[0]
        let koreksiDiagnosaInacbgs = localStorage.getItem('koreksi-diagnosa-inacbgs')
        koreksiDiagnosaInacbgs = JSON.parse(koreksiDiagnosaInacbgs)

        if (!valueIdrg) {
            docoNotification('error', 'Perhatian', 'Diagnosa belum dipilih')
            return false;
        }

        let newData = {
            uuid: parseInt(valueIdrg),
            id: parseInt(valueIdrg),
            name: selectedData.text,
            kode: selectedData.kode,
            isPrimer : koreksiDiagnosaInacbgs == null || koreksiDiagnosaInacbgs == '' ? true : false
        }
        
        if (koreksiDiagnosaInacbgs != undefined || koreksiDiagnosaInacbgs.length > 0) {
            let data = koreksiDiagnosaInacbgs
            let duplikat = false
            data.forEach((item) => {
                if (item.id == newData.id) {
                    duplikat = true
                }
            })

            if (duplikat) {
                docoNotification('error', 'Perhatian', 'Diagnosa sudah pernah dipilih !')
                return false;
            }

            data.push(newData)
            localStorage.setItem('koreksi-diagnosa-inacbgs', JSON.stringify(data))
        } else {
            newData.isPrimer = true
            localStorage.setItem('koreksi-diagnosa-inacbgs', JSON.stringify([newData]))
        }
        
        let buttonPrimer = componentButtonPrimer(newData, 'btn-set-primer-diagnosa-inacbgs');
        let indexNumber = tableDiagnosaInacbgs.rows().count() + 1

        clearSelect()
        hideAndDisabledSectionInacbgs()

        tableDiagnosaInacbgs.row.add({
            id: tableDiagnosaInacbgs.rows().count() + 1,
            name: `
                <div>
                    ${selectedData.text}
                    <br/>
                    <span class="text-danger set-error-diagnosa-inacbgs test-error error-inacbgs-diagnosa-kode-${indexNumber}" data-kode="${selectedData.kode}"></span>
                </div>
            `,
            kode: buttonPrimer,
            aksi: `
                <div class="m-2">
                    <button class="btn btn-danger btn-custom-delete btn-remove-inacbgs" dig-id="${valueIdrg}" >Hapus</button>
                </div>
            `
        }).draw()
    })

    /**
     * Function for add procedure INACBGS.
     */
    $('#btn-add-procedure-inacbgs').on('click', function () {
        let valueIdrg = $('#inacbgsProcedure').val()
        let selectedData = $('#inacbgsProcedure').select2('data')[0]
        let koreksiProcedureInacbgs = localStorage.getItem('koreksi-procedure-inacbgs')
        koreksiProcedureInacbgs = JSON.parse(koreksiProcedureInacbgs)

        if (!valueIdrg) {
            docoNotification('error', 'Perhatian', 'Procedure belum dipilih')
            return false;
        }

        let newData = {
            uuid: parseInt(valueIdrg),
            id: parseInt(valueIdrg),
            name: selectedData.text,
            kode: selectedData.kode,
            multiplicity: 1,
            isPrimer : koreksiProcedureInacbgs == null || koreksiProcedureInacbgs == '' ? true : false
        }
        
        if (koreksiProcedureInacbgs != undefined || koreksiProcedureInacbgs.length > 0) {
            let data = koreksiProcedureInacbgs
            data.push(newData)
            localStorage.setItem('koreksi-procedure-inacbgs', JSON.stringify(data))
        } else {
            localStorage.setItem('koreksi-procedure-inacbgs', JSON.stringify([newData]))
        }

        clearSelect(false)
        refreshProcedure(false)
    });

    $('#btn-grouping-idrg').on('click', function (e) {
        let dataDiagnosa = localStorage.getItem('koreksi-diagnosa-idrg')
        let dataProcedure = localStorage.getItem('koreksi-procedure-idrg')

        $().docoForm('click', {
            method: "POST",
            url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/add-diagnosa-idrg',
            skipConfirm: true,
            data: {
                dataDiagnosa: dataDiagnosa,
                dataProcedure: dataProcedure,
                kunjunganId: $('#klaiminacbgranapform-kunjungan_id').val()
            },
            success: function (res) {
                getGrouper()
            }
        })
    })

    $('#btn-final-idrg').on('click', function (e) {
        $().docoForm('click', {
            url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/final-idrg',
            skipConfirm: true,
            data: {
                kunjunganId: $('#klaiminacbgranapform-kunjungan_id').val()
            },
            success: function (res) {
                getGrouper()
                validateDiagnosaInacbgs()
            }
        })
    })

    $('#btn-edit-idrg').on('click', function (e) {
        $().docoForm('click', {
            url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/edit-idrg',
            skipConfirm: true,
            data: {
                kunjunganId: $('#klaiminacbgranapform-kunjungan_id').val()
            },
            success: function (res) {
                location.reload()
            }
        })
    })
});

/**
 * Start Function iDRG
 */

$(document).on('click', '.btn-set-primer-diagnosa-idrg', function (e) {
    let uuid = $(this).attr('data-uuid')
    let data = localStorage.getItem('koreksi-diagnosa-idrg')

    // Change primer data & change position to top
    let newData = JSON.parse(data)
    newData.forEach((item) => {
        if (item.uuid == uuid) {
            item.isPrimer = true
        } else {
            item.isPrimer = false
        }
    })

    primaryData = newData.filter((item) => {
        return item.uuid == uuid
    })

    let isNotValid = validasiPrimaryDiagnosa(primaryData)

    if (isNotValid) {
        return false;
    }

    newData = newData.filter((item) => {
        return item.uuid != uuid
    })

    newData.unshift(primaryData[0])
    localStorage.setItem('koreksi-diagnosa-idrg', JSON.stringify(newData))

    // Re Adjust Table 
    tableDiagnosaIdrg.clear().draw()
    renderTableDiagnosa()
})

$(document).on('click', '.btn-set-primer-procedure-idrg', function (e) {
    let uuid = $(this).attr('data-uuid')
    let data = localStorage.getItem('koreksi-procedure-idrg')

    // Change primer data & change position to top
    let newData = JSON.parse(data)
    newData.forEach((item) => {
        if (item.uuid == uuid) {
            item.isPrimer = true
        } else {
            item.isPrimer = false
        }
    })

    primaryData = newData.filter((item) => {
        return item.uuid == uuid
    })

    newData = newData.filter((item) => {
        return item.uuid != uuid
    })

    newData.unshift(primaryData[0])
    localStorage.setItem('koreksi-procedure-idrg', JSON.stringify(newData))

    // Re Adjust Table 
    tableProcedureIdrg.clear().draw()
    renderTableProcedure()
})

$(document).on('change', '.input-multiplicity-idrg', function (e) {
    let value = $(this).val()
    let uuid = $(this).attr('data-uuid')

    if (value == 0) {
        docoNotification('warning', 'Peringatan', 'Multiplicity tidak boleh 0');
        $(this).val(1)
        return false;
    }
    
    data = localStorage.getItem('koreksi-procedure-idrg')
    data = JSON.parse(data)
    data = data.map((item) => {
        if (item.uuid == uuid) {
            item.multiplicity = parseInt(value)
        }
        return item
    })
    localStorage.setItem('koreksi-procedure-idrg', JSON.stringify(data))

    setTimeout(() => {
        renderTableProcedure()
    }, 20);
})

$(document).on('click', '.btn-remove-idrg', function () {
    let data = localStorage.getItem('koreksi-diagnosa-idrg')
    let newData = JSON.parse(data)
    let countData = newData?.length
    let nonPrimaryDiagnosa = localStorage.getItem('koreksi-idrg-accpdx')
    nonPrimaryDiagnosa = JSON.parse(nonPrimaryDiagnosa)

    if (countData == 2) {
        if (nonPrimaryDiagnosa != undefined) {
            let dataPositionTwo = newData[1]

            let resultData = nonPrimaryDiagnosa.filter((item) => {
                return item.id == dataPositionTwo.id && $(this).attr('dig-id') != item.id
            })

            if (resultData.length > 0) {
                alert('Sekunder tidak dapat dipromosikan menjadi primer. Penghapusan gagal.')
                return false;
            }
        }
    }

    newData = newData.filter((item) => {
        return item.id != $(this).attr('dig-id')
    })

    newData.forEach((item, index) => {
        if (index == 0) {
            item.isPrimer = true
        }
    })

    localStorage.setItem('koreksi-diagnosa-idrg', JSON.stringify(newData))
    setTimeout(() => {
        renderTableDiagnosa()
    }, 20);
});

$(document).on('click', '.btn-remove-idrg-procedure', function () {
    let data = localStorage.getItem('koreksi-procedure-idrg')
    let newData = JSON.parse(data)
    newData = newData.filter((item) => {
        return item.uuid != $(this).attr('data-uuid')
    })

    newData.forEach((item, index) => {
        if (index == 0) {
            item.uuid = index + 1
            item.isPrimer = true
        }
    })

    localStorage.setItem('koreksi-procedure-idrg', JSON.stringify(newData))
    setTimeout(() => {
        renderTableProcedure()
    }, 20);
});

/**
 * End Function iDRG
 */


/**
 * Start Function INACBGS
 */
$(document).on('change', '.input-multiplicity-inacbgs', function (e) {
    let value = $(this).val()
    let uuid = $(this).attr('data-uuid')

    if (value == 0) {
        docoNotification('warning', 'Peringatan', 'Multiplicity tidak boleh 0');
        $(this).val(1)
        return false;
    }
    
    data = localStorage.getItem('koreksi-procedure-inacbgs')
    data = JSON.parse(data)
    data = data.map((item) => {
        if (item.uuid == uuid) {
            item.multiplicity = parseInt(value)
        }
        return item
    })
    localStorage.setItem('koreksi-procedure-inacbgs', JSON.stringify(data))

    setTimeout(() => {
        renderTableProcedureInacbgs()
    }, 20);
})

$(document).on('click', '.btn-set-primer-diagnosa-inacbgs', function (e) {
    let uuid = $(this).attr('data-uuid')
    let data = localStorage.getItem('koreksi-diagnosa-inacbgs')

    // Change primer data & change position to top
    let newData = JSON.parse(data)
    newData.forEach((item) => {
        if (item.uuid == uuid) {
            item.isPrimer = true
        } else {
            item.isPrimer = false
        }
    })

    primaryData = newData.filter((item) => {
        return item.uuid == uuid
    })

    newData = newData.filter((item) => {
        return item.uuid != uuid
    })

    newData.unshift(primaryData[0])
    localStorage.setItem('koreksi-diagnosa-inacbgs', JSON.stringify(newData))

    setTimeout(() => {
        renderTableDiagnosaInacbgs()
    }, 20);
})

$(document).on('click', '.btn-set-primer-procedure-inacbgs', function (e) {
    let uuid = $(this).attr('data-uuid')
    let data = localStorage.getItem('koreksi-procedure-inacbgs')

    // Change primer data & change position to top
    let newData = JSON.parse(data)
    newData.forEach((item) => {
        if (item.uuid == uuid) {
            item.isPrimer = true
        } else {
            item.isPrimer = false
        }
    })

    primaryData = newData.filter((item) => {
        return item.uuid == uuid
    })

    newData = newData.filter((item) => {
        return item.uuid != uuid
    })

    newData.unshift(primaryData[0])
    localStorage.setItem('koreksi-procedure-inacbgs', JSON.stringify(newData))

    setTimeout(() => {
        renderTableProcedureInacbgs()
    }, 20);
})

$(document).on('click', '.btn-remove-inacbgs', function () {
    let data = localStorage.getItem('koreksi-diagnosa-inacbgs')
    let newData = JSON.parse(data)
    newData = newData.filter((item) => {
        return item.id != $(this).attr('dig-id')
    })

    newData.forEach((item, index) => {
        if (index == 0) {
            item.isPrimer = true
        }
    })

    localStorage.setItem('koreksi-diagnosa-inacbgs', JSON.stringify(newData))

    // let row = tableDiagnosaInacbgs.row($(this).closest('tr'));
    
    // // hapus row dari datatable
    // row.remove().draw();
    setTimeout(() => {
        renderTableDiagnosaInacbgs()
    }, 20);
});

$(document).on('click', '.btn-remove-inacbgs-procedure', function () {
    let data = localStorage.getItem('koreksi-procedure-inacbgs')
    let newData = JSON.parse(data)
    newData = newData.filter((item) => {
        return item.uuid != $(this).attr('data-uuid')
    })

    newData.forEach((item, index) => {
        if (index == 0) {
            item.uuid = index + 1
            item.isPrimer = true
        }
    })

    localStorage.setItem('koreksi-procedure-inacbgs', JSON.stringify(newData))
    setTimeout(() => {
        renderTableProcedureInacbgs()
    }, 20);
});

$('#btn-grouping-inacbgs').on('click', function (e) {
    let dataDiagnosa = localStorage.getItem('koreksi-diagnosa-inacbgs')
    let dataProcedure = localStorage.getItem('koreksi-procedure-inacbgs')

    $().docoForm('click', {
        method: "POST",
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/add-diagnosa-inacbgs',
        skipConfirm: true,
        data: {
            dataDiagnosa: dataDiagnosa,
            dataProcedure: dataProcedure,
            kunjunganId: $('#klaiminacbgranapform-kunjungan_id').val()
        },
        success: function (res) {
            let responseProcedure = res?.response?.response_procedure
            let responseDiagnosa = res?.response?.response_diagnosa
            
            generateResponseErrorInacbgs(responseDiagnosa, responseProcedure)
            getGrouper()
        }
    })
})

$('#btn-final-inacbgs').on('click', function (e) {
    $().docoForm('click', {
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/final-inacbgs',
        skipConfirm: true,
        data: {
            kunjunganId: $('#klaiminacbgranapform-kunjungan_id').val()
        },
        success: function (res) {
            getGrouper()
        }
    })
})

$('#btn-edit-inacbgs').on('click', function (e) {
    $().docoForm('click', {
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/edit-inacbgs',
        skipConfirm: true,
        data: {
            kunjunganId: $('#klaiminacbgranapform-kunjungan_id').val()
        },
        success: function (res) {
            location.reload()
        }
    })
})

$('#btn-import-koding').on('click', function (e) {
    $().docoForm('click', {
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/import-koding',
        data: {
            kunjunganId: $('#klaiminacbgranapform-kunjungan_id').val(),
            nosep: $('#no_sep').val()
        },
        skipConfirm: true,
        before: function () {
            importKoding()
        },
        success: function (res) {
            let responseProcedure = res?.response?.response_procedure?.data
            let responseDiagnosa = res?.response?.response_diagnosa?.data

            let responseError = {
                response_procedure: responseProcedure,
                response_diagnosa: responseDiagnosa
            }

            let isError = generateResponseErrorInacbgs(responseDiagnosa, responseProcedure)
            
            if (isError) {
                localStorage.setItem('koreksi-validasi-inacbgs', JSON.stringify(responseError))
                return false
            }
            
            location.reload()
        }
    })
})

$('#btn-validate-diagnosa').on('click', function (e) {
    $().docoForm('click', {
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/validasi-diagnosa-inacbgs',
        data: {
            kunjunganId: $('#klaiminacbgranapform-kunjungan_id').val(),
            nosep: $('#no_sep').val()
        },
        skipConfirm: true,
        skipSuccessNotif: true,
        skipErrorNotif: true,
        skipScrollUp: true,
        success: function (res) {
            let responseProcedure = res?.response?.response_procedure?.data
            let responseDiagnosa = res?.response?.response_diagnosa?.data

            let responseError = {
                response_procedure: responseProcedure,
                response_diagnosa: responseDiagnosa
            }

            localStorage.setItem('koreksi-validasi-inacbgs', JSON.stringify(responseError))
            generateResponseErrorInacbgs(responseDiagnosa, responseProcedure)     
        }
    })
})

/**
 * End Function INACBGS
 */

function generateDiagnosa() {
    let diagnosaIdrg = idrgDiagnosa
    let data = JSON.parse(diagnosaIdrg)
    let mappingData = []
    let mappingNonPrimary= []
    
    data.forEach((item) => {
        let namaDiagnosa = `${item.diagnosa_kode} - ${item.diagnosa_nama}`

        let newData = {
            uuid: item.diagnosa_id, // Tidak ada multiplicity
            id: item.diagnosa_id,
            name: namaDiagnosa,
            kode: item.diagnosa_kode,
            isPrimer : item?.is_icdprimer
        }

        if (item.accpdx == 'N') {
            let newNonPrimary = {
                id: newData.id,
                kode: newData.kode,
                accpdx: item.accpdx
            }
            mappingNonPrimary.push(newNonPrimary)
        }

        mappingData.push(newData)
    })

    mappingData = mappingData.sort((a, b) => {
        const aPrimer = a.isPrimer === true || a.isPrimer === "true";
        const bPrimer = b.isPrimer === true || b.isPrimer === "true";
        return bPrimer - aPrimer;
    });

    localStorage.setItem('koreksi-diagnosa-idrg', JSON.stringify(mappingData))

    localStorage.setItem('koreksi-idrg-accpdx', JSON.stringify(mappingNonPrimary))

    setTimeout(() => {
        renderTableDiagnosa()
    }, 20);
}

function generateDiagnosaInacbgs() {
    let diagnosa = inacbgsDiagnosa
    let data = JSON.parse(diagnosa)
    let mappingData = []
    
    data.forEach((item) => {
        let namaDiagnosa = `${item.diagnosa_kode} - ${item.diagnosa_nama}`

        let newData = {
            uuid: item.diagnosa_id, // Tidak ada multiplicity
            id: item.diagnosa_id,
            name: namaDiagnosa,
            kode: item.diagnosa_kode,
            isPrimer : item?.is_icdprimer
        }

        mappingData.push(newData)
    })

    mappingData = mappingData.sort((a, b) => {
        const aPrimer = a.isPrimer === true || a.isPrimer === "true";
        const bPrimer = b.isPrimer === true || b.isPrimer === "true";
        return bPrimer - aPrimer; 
    });

    localStorage.setItem('koreksi-diagnosa-inacbgs', JSON.stringify(mappingData))

    setTimeout(() => {
        renderTableDiagnosaInacbgs()
    }, 20);
}

/**
 * Generate Procedure Data
 * 
 * This function is used to generate the procedure data
 * from the localstorage and render it to the table
 */
function generateProcedure(local = false) {
    let procedureIdrg = idrgProcedure
    if (local) {
        procedureIdrg = localStorage.getItem('koreksi-procedure-idrg')
    } 

    let data = JSON.parse(procedureIdrg)
    let mappingData = []

    data.forEach((item, index) => {
        let namaProcedure = `${item.diagnosa_kode} - ${item.diagnosa_nama}`
        let primer = item?.is_icdprimer
        if (local) {
            primer = item.isPrimer
        } else {
            if (index == 0) {
                primer = true
            }
        }

        let newData = {
            uuid: index + 1, // Tidak ada multiplicity
            id: local ? item.id : item.diagnosa_id,
            name: local ? item.name : namaProcedure,
            kode: local ? item.kode : item.diagnosa_kode,
            multiplicity: item.multiplicity,
            isPrimer : primer
        }

        mappingData.push(newData)
    })

    localStorage.setItem('koreksi-procedure-idrg', JSON.stringify(mappingData))

    setTimeout(() => {
        renderTableProcedure()
    }, 20);
}


/**
 * Generate Procedure Data INACBGS
 * 
 * This function is used to generate the procedure data
 * from the localstorage and render it to the table
 */
function generateProcedureInacbgs(local = false) {
    let procedure = inacbgsProcedure

    if (local) {
        procedure = localStorage.getItem('koreksi-procedure-inacbgs')
    }

    let data = JSON.parse(procedure)
    let mappingData = []
    
    data.forEach((item, index) => {
        let namaProcedure = `${item.diagnosa_kode} - ${item.diagnosa_nama}`

        let newData = {
            uuid: index + 1,
            id: local ? item.id : item.diagnosa_id,
            name: local ? item.name : namaProcedure,
            kode: local ? item.kode : item.diagnosa_kode,
            multiplicity: item.multiplicity,
            isPrimer : local ? item.isPrimer : item?.is_icdprimer
        }

        mappingData.push(newData)
    })

    localStorage.setItem('koreksi-procedure-inacbgs', JSON.stringify(mappingData))

    setTimeout(() => {
        renderTableProcedureInacbgs()
    }, 20);
}

function refreshProcedure(isDrg = true) {
    if (isDrg) {
        generateProcedure(true)
    } else {
        generateProcedureInacbgs(true)
    }
}


/**
 * Render Table Diagnosa iDRG from localstorage
 * @param {object} data - Data from localstorage
 */
function renderTableDiagnosa() {
    let diagnosaIdrg = localStorage.getItem('koreksi-diagnosa-idrg')
    let data = JSON.parse(diagnosaIdrg)
    hideAndDisabledSectionIdrg()

    tableDiagnosaIdrg.clear().draw()
    if (data) {
        data.forEach((item) => {
            let buttonPrimer = componentButtonPrimer(item, 'btn-set-primer-diagnosa-idrg');
            tableDiagnosaIdrg.row.add({
                id: tableDiagnosaIdrg.rows().count() + 1,
                name: item?.name,
                kode: buttonPrimer,
                aksi: `
                    <div class="m-2">
                        <button class="btn btn-danger btn-custom-delete btn-remove-idrg" dig-id="${item.id}">Hapus</button>
                    </div>
                `
            }).draw()
        })
    }
}


/**
 * Render Table Procedure iDRG from localstorage
 * @param {object} data - Data from localstorage
 */
function renderTableProcedure() {
    let procedureIdrg = localStorage.getItem('koreksi-procedure-idrg')
    let data = JSON.parse(procedureIdrg)
    hideAndDisabledSectionIdrg()

    tableProcedureIdrg.clear().draw()
    if (data) {
        let countProcedure = []
        data.forEach((item) => {
            countProcedure[item.kode] = countProcedure[item.kode] ? countProcedure[item.kode] + 1 : 1
        })
        
        data.forEach((item) => {
            let buttonPrimer = componentButtonPrimer(item, 'btn-set-primer-procedure-idrg');

            tableProcedureIdrg.row.add({
                id: tableProcedureIdrg.rows().count() + 1,
                name: `
                    <div>
                        ${item?.name}
                        ${countProcedure[item.kode] > 1? `<span class="badge badge-danger">x${countProcedure[item.kode]}</span>` : ''}
                    </div>
                `,
                multiplicity: `
                    <div class="m-2" style="text-align: center; display: flex; justify-content: center">
                        <input type="number" class="form-control input-multiplicity-idrg" min="1" value="${item.multiplicity}" data-uuid="${item.uuid}" style="width: 70px">
                    </div>
                `,
                kode: buttonPrimer,
                aksi: `
                    <div>
                        <button class="btn btn-danger btn-custom-delete btn-remove-idrg-procedure" data-uuid="${item.uuid}" style="width: font-size: 10px">Hapus</button>
                    </div>
                `
            }).draw()
        })
    }
}


/**
 * Render Table Diagnosa Inacbg from localstorage
 * @param {object} data - Data from localstorage
 */
function renderTableDiagnosaInacbgs() {
    let diagnosa = localStorage.getItem('koreksi-diagnosa-inacbgs')
    let data = JSON.parse(diagnosa)
    hideAndDisabledSectionInacbgs()
    
    tableDiagnosaInacbgs.clear().draw()
    if (data) {
        data.forEach((item) => {
            let buttonPrimer = componentButtonPrimer(item, 'btn-set-primer-diagnosa-inacbgs');
            let index = tableDiagnosaInacbgs.rows().count() + 1

            tableDiagnosaInacbgs.row.add({
                id: tableDiagnosaInacbgs.rows().count() + 1,
                name: `
                    <div>
                        ${item?.name}
                        <br/>
                        <span class="text-danger set-error-diagnosa-inacbgs test-error error-inacbgs-diagnosa-kode-${index}" data-kode="${item.kode}"></span>
                    </div>
                `,
                kode: buttonPrimer,
                aksi: `
                    <div class="m-2">
                        <button class="btn btn-danger btn-custom-delete btn-remove-inacbgs" dig-id="${item.id}">Hapus</button>
                    </div>
                `
            }).draw()
        })

        revalidateDiagnosa()
    }
}


/**
 * Render Table Procedure Inacbg from localstorage
 * @param {object} data - Data from localstorage
 */
function renderTableProcedureInacbgs() {
    let procedure = localStorage.getItem('koreksi-procedure-inacbgs')
    let data = JSON.parse(procedure)
    hideAndDisabledSectionInacbgs()

    tableProcedureInacbgs.clear().draw()
    if (data) {
        let countProcedure = []
        data.forEach((item, index) => {
            countProcedure[item.kode] = countProcedure[item.kode] ? countProcedure[item.kode] + 1 : 1
        })

        data.forEach((item, index) => {
            let buttonPrimer = componentButtonPrimer(item, 'btn-set-primer-procedure-inacbgs');
            let kode = item.kode

            /**
             * Set penanda untuk tiap diagnosa
             */
            if (item.multiplicity > 1) {
                kode = `${item.kode}+${item.multiplicity}`
            }
            
            tableProcedureInacbgs.row.add({
                id: tableProcedureInacbgs.rows().count() + 1,
                name: `
                    <div>
                        <span class="inacbgs-procedure-kode-${index+1}" data-uuid="${item.uuid}" data-mutliplicity="${item.multiplicity}" data-kode="${kode}">
                            ${item?.name}
                            ${countProcedure[item.kode] > 1? `<span class="badge badge-danger">x${countProcedure[item.kode]}</span>` : ''}
                        </span>
                        <br/>
                        <span class="text-danger set-error-procedure-inacbgs test-error error-inacbgs-procedure-kode-${index+1}" data-kode="${item.kode}"></span>
                    </div>
                `,
                multiplicity: `
                    <div class="m-2" style="text-align: center; display: flex; justify-content: center">
                        <input type="number" class="form-control input-multiplicity-inacbgs" min="1" value="${item.multiplicity}" data-uuid="${item.uuid}" style="width: 70px">
                    </div>
                `,
                kode: buttonPrimer,
                aksi: `
                    <div>
                        <button class="btn btn-danger btn-custom-delete btn-remove-inacbgs-procedure" data-uuid="${item.uuid}">Hapus</button>
                    </div>
                `
            }).draw()
        })

        revalidateDiagnosa()
    }
}

/**
 * Component button primer
 * @param {object} newData - Data from localstorage
 * @param {string} element - Element name
 * @return {string} - HTML string for button primer
 */
function componentButtonPrimer(newData, element) {
    if (newData.isPrimer) {
        return `
            <span class="badge badge-primary">Primer</span>
        `;
    } else {
        return `
            <button class="btn btn-info btn-set-primary ${element}" data-uuid="${newData.uuid}">
                Set Primary
            </button>
        `;
    }
}

function countDataProcedure(data) {
    let count = []
    data.forEach((item) => {
        count[item.kode] = count[item.kode] ? count[item.kode] + 1 : 1
    })
    return count
}

/**
 * Generate response error for inacbgs
 * @param {object} responseDiagnosa - Data diagnosa from server
 * @param {object} responseProcedure - Data procedure from server
 * @return {void}
 */
function generateResponseErrorInacbgs(responseDiagnosa, responseProcedure) {
    let isError = false

    if (responseProcedure != null) {
        let mappingError = []
        let stringProcedure = responseProcedure?.string
        let dataProcedure = responseProcedure?.expanded
        stringProcedure = stringProcedure.split("#")

        if (Array.isArray(stringProcedure)) {
            stringProcedure.map((item, index) => {
                /**
                 * Cek apabila data procedure memiliki validcode 0
                 */
                let data = dataProcedure[index]
                if (data?.metadata?.error_no == 'E2101') {
                    mappingError.push({
                        data: dataProcedure[index],
                        string: item
                    })
                }
            })
        }

        if (mappingError.length > 0) {
            isError = true
            mappingError.map((item) => {
                $('#inacbgsProcedureTable tbody tr').each(function () {
                    let row = $(this);
                    // if (row.find(`.error-inacbgs-procedure-kode-${item?.data?.no}`).length > 0) {
                    //     row.addClass('row-error');
                    // }

                    if (row.find(`.test-error`).length > 0) {
                        let attributKode = row.find(`.test-error`).attr('data-kode')
                        let itemKode = item?.data?.code
                        if (attributKode == itemKode) {
                            row.find(`.test-error`).html(`${item?.data?.metadata?.message}`)
                            row.addClass('row-error');

                        }
                    }
                });
            })
        }else {
            $('.set-error-procedure-inacbgs').html('')
            $('#inacbgsProcedureTable tbody tr').removeClass('row-error');
        }
    }

    if (responseDiagnosa != null) {
        let mappingErrorDiagnosa = []
        let stringDiagnosa = responseDiagnosa?.string
        let dataDiagnosa = responseDiagnosa?.expanded
        stringDiagnosa = stringDiagnosa.split("#")

        if (Array.isArray(stringDiagnosa)) {
            stringDiagnosa.map((item, index) => {
                /**
                 * Cek apabila data procedure memiliki validcode 0
                 */
                let data = dataDiagnosa[index]
                if (data?.metadata?.error_no == 'E2101') {
                    mappingErrorDiagnosa.push({
                        data: dataDiagnosa[index],
                        string: item
                    })
                }
            })
        }

        if (mappingErrorDiagnosa.length > 0) {
            isError = true
            mappingErrorDiagnosa.map((item) => {

                $('#inacbgsDiagnosaTable tbody tr').each(function () {
                    let row = $(this);
                    // if (row.find(`.error-inacbgs-diagnosa-kode-${item?.data?.no}`).length > 0) {
                    //     row.addClass('row-error');
                    // }

                    if (row.find(`.test-error`).length > 0) {
                        let attributKode = row.find(`.test-error`).attr('data-kode')
                        let itemKode = item?.data?.code
                        if (attributKode == itemKode) {
                            row.find(`.test-error`).html(`${item?.data?.metadata?.message}`)
                            row.addClass('row-error');
                        }
                    }
                });
            })
        }else {
            $('.set-error-diagnosa-inacbgs').html('')
            $('#inacbgsDiagnosaTable tbody tr').removeClass('row-error');
        }
    }

    return isError
}

function importKoding() {
    let idrgDiagnosa = localStorage.getItem('koreksi-diagnosa-idrg')
    let idrgProcedure = localStorage.getItem('koreksi-procedure-idrg')

    if (idrgDiagnosa != null) {
        localStorage.setItem('koreksi-diagnosa-inacbgs', idrgDiagnosa)
        renderTableDiagnosaInacbgs()
    }

    if (idrgProcedure != null) {
        let doubleDiagnosa = [];
        let procedure = JSON.parse(idrgProcedure);
        let newProcedure = [];

        procedure.forEach((item) => {
            let kode = item.kode;
            if (doubleDiagnosa.indexOf(kode) == -1) {
                newProcedure.push(item);
                doubleDiagnosa.push(kode);
            }
        })

        
        localStorage.setItem('koreksi-procedure-inacbgs', JSON.stringify(newProcedure))
        renderTableProcedureInacbgs()
    }
}

function getDiagnosa(isDiagnosa = true) {
    let domId = $('.koreksi-diagnosa')
    let placeholder = 'Pilih Diagnosa'
    if (isDiagnosa == false) {
        domId = $('.koreksi-procedure')
        placeholder = 'Pilih Procedure'
    }

    domId.select2({
        placeholder: placeholder,
        minimumInputLength: 3,
        ajax: {
            url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-icd',
            dataType: 'json',
            quietMillis: 250,
            data: function (params) {
                var notIn = [];
                let attributeInacbg = $(this).attr('data-inacbg');
                let typeIna = $(this).attr('data-type-ina');
                let localData = localStorage.getItem('koreksi-diagnosa-idrg')
                let unik = $(this).attr('data-unik');

                if (attributeInacbg == 1) {
                    localData = localStorage.getItem('koreksi-diagnosa-inacbgs')
                }

                if (unik == 1) {
                    localData = localStorage.getItem('koreksi-procedure-inacbgs')
                }

                if(localData != null) {
                    idrgLocal = JSON.parse(localData)
                    idrgLocal.forEach(element => {
                        notIn.push(element.id)
                    });
                }
                
                params.type_icd = $(this).attr('data-type');
                params.type_ina = typeIna
                params.not_in = notIn;
                var query = {
                    search: params,
                }
                return params;
            },
            processResults: function (data) {
                return {
                    results: data.result
                };
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) {
                return m;
            },
        },
    }).on("select2:selecting", function (event) {
        const data = event.params?.args?.data;
        const validCode = data?.validcode
        const validCodeIdrg = data?.validcode_idrg
        const accpdx = data?.accpdx
        const asterik = data?.asterik
        const typeIna = $(this).attr('data-type-ina')

        if (typeIna == 1) {
            let countDiagnosa = 0;
            let localData = localStorage.getItem('koreksi-diagnosa-idrg')
            localData = JSON.parse(localData)

            if (localData != null) {
                countDiagnosa = localData.length
            }
            
            if (accpdx == 'N') {
                setDiagnosaIdrgNonPrimary(data)
            }
            
            if (accpdx == 'N' && countDiagnosa == 0) {
                alert(`Kode ${data?.kode} ini tidak dapat menjadi diagnosis utama`)
                return false;
            }

            if(validCodeIdrg == 0) {
              alert(`Kode diagnosis ${data?.kode} ini tidak valid untuk grouping`)
              return false;
            }
        } else {
            if(validCode == 0) {
              alert(`Kode diagnosis ${data?.kode} ini tidak valid untuk grouping`)
              return false;
            }
        }
        
        return true;
    }).on("select2:select", function (event) {
        const buttonId = $(this).attr('data-button')
        $(`#${buttonId}`).trigger('click')
    })
}

/**
 * Clear select2 value.
 * @param {boolean} isDiagnosa - If true, clear select2 value for diagnosa, otherwise for procedure.
 */
function clearSelect(isDiagnosa = true) {
    let domId = $('.koreksi-diagnosa')
    if (isDiagnosa == false) {
        domId = $('.koreksi-procedure')
    }
    
    domId.val(null).trigger('change')
}

function validateDiagnosaInacbgs() {
    $("#btn-validate-diagnosa").trigger('click')
}

function setDiagnosaIdrgNonPrimary(newData) {
    let localData = localStorage.getItem('koreksi-idrg-accpdx')
    localData = JSON.parse(localData)
    
    if (localData != undefined) {
        let data = localData;
        data.push({
            id: newData.id,
            kode: newData.kode,
            accpdx: newData.accpdx
        })
        localStorage.setItem('koreksi-idrg-accpdx', JSON.stringify(data))
    } else {
        localStorage.setItem('koreksi-idrg-accpdx', JSON.stringify([newData]))
    }
}

/**
 * Validate primary diagnosis.
 * @param {Array} primaryData - Primary diagnosis data.
 * @returns {boolean} True if primary diagnosis is not valid, false otherwise.
 */
function validasiPrimaryDiagnosa(primaryData) {
    let nonPrimaryDiagnosa = localStorage.getItem('koreksi-idrg-accpdx')
    nonPrimaryDiagnosa = JSON.parse(nonPrimaryDiagnosa)

    if (nonPrimaryDiagnosa != undefined) {
        let dataPositionTwo = primaryData[0]

        let resultData = nonPrimaryDiagnosa.filter((item) => {
            return item.id == dataPositionTwo.id
        })

        if (resultData.length > 0) {
            alert(`Kode ${dataPositionTwo?.kode} ini tidak dapat menjadi diagnosis utama`)
            return true;
        }
    }
}

function revalidateDiagnosa() {
    let responseError = localStorage.getItem('koreksi-validasi-inacbgs')
    if (responseError != null) {
        responseError = JSON.parse(responseError)
        let responseDiagnosa = responseError?.response_diagnosa
        let responseProcedure = responseError?.response_procedure

        generateResponseErrorInacbgs(responseDiagnosa, responseProcedure)
    }
}