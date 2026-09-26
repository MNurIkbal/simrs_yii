var tableSbar
var limitDefault = 5;
$(document).ready(function() {
    moment.locale("en");
    tableSbar = $('#tb-sbar').docoTabel({
        filter: true,
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        sorting: [[1, "desc"]],
        info: false,
        paging: false,
        ajax: {
            url: `/${modul}${url}/get-data-sbar?pendaftaran_id=${pendaftaranIdSbar}`,
        },
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                var tableInfo = tableSbar.page.info();
                return tableInfo.start + rowAdditionalData.row + 1;
                },
            },
            {
                name: 'tgl_sbar',
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    var date = new Date(row.tgl_sbar);
                    var formattedDate = date.toLocaleString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }).replace(/\./g, ':');
                    return formattedDate + ' <br/>---------------------------------<br/> ' + row.pegawai_input;
                }
            },
            {
                data: 'dokter_tujuan'
            },
            {
                data: 'situasi',
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div style="white-space: normal;">
                            <p><b>Situation :</b> ${row.situasi || ''}</p>
                            <p><b>Background :</b></p>
                            <table style="width:100%; border-collapse: collapse; font-size: 12px;">
                                <tr><td style="width:120px;">Sistol</td><td>: ${row.sistol || ''} mmHg</td><td>Berat Badan</td><td>: ${row.berat_badan || ''} Kg</td></tr>
                                <tr><td>Diastol</td><td>: ${row.diastol || ''} mmHg</td><td>SPO2</td><td>: ${row.spo2 || ''}%</td></tr>
                                <tr><td>Denyut Nadi</td><td>: ${row.nadi || ''} x/Menit</td><td>Suhu</td><td>: ${row.suhu || ''} °C</td></tr>
                                <tr><td>Frekuensi Nafas</td><td>: ${row.respirasi || ''} x/Menit</td><td>Lingkar Kepala</td><td>: ${row.lingkar_kepala || ''} cm</td></tr>
                                <tr><td>Tinggi Badan</td><td>: ${row.tinggi_badan || ''} cm</td><td></td><td></td></tr>
                            </table>
                            <br/>
                            <p><b>Assessment :</b> ${row.asesmen || ''}</p>
                            <p><b>Recommendation :</b> ${row.rekomendasi || ''}</p>
                        </div>
                    `;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    var buttons = '';
                    var hasAccess = '';
                    var created_by = row.created_by
                    if(created_by != createdBy) {
                        hasAccess = 'disabled'
                    }

                    if(!row.is_verifikasi) {
                        buttons += `<button 
                            class="btn btn-info btn-xs btn-verifikasi-sbar btn-labeled"
                            data-id="${row.sbar_id}"
                            data-dokter-id="${row.dokter_tujuan_id || ''}"
                        >
                            <b><i class="fa fa-check"></i></b>Verifikasi
                        </button> <br/>`;

                        buttons += `<button 
                            class="btn btn-info btn-xs btn-edit btn-labeled" 
                            data-id="${row.sbar_id}"
                            data-createdby="${row.created_by}"
                        >
                            <b><i class="fa fa-edit"></i></b>Edit
                        </button> <br/>`;
                    }

                    buttons += `<button 
                        class="btn btn-info btn-xs btn-copy-sbar btn-labeled"
                        data-id="${row.sbar_id}"
                        data-dokter-id="${row.dokter_tujuan_id || ''}"
                    >
                        <b><i class="fa fa-copy"></i></b>Copy
                    </button> <br/>`;

                    if(!row.is_verifikasi) {
                        buttons += `<button 
                            class="btn btn-danger btn-xs btn-delete-sbar btn-labeled"
                            data-id="${row.sbar_id}"
                            data-createdby="${row.created_by}"
                        >
                            <b><i class="fa fa-trash"></i></b>Delete
                        </button> <br/>`;
                    }

                    return buttons;
                }
            }
        ],
        drawCallback: (settings) => {
            if(settings.aoData.length > 5){
                $('.link-action-sbar-table[data-event="show"]').css("visibility", "visible");
                showSbarDatatable(settings.sTableId, 5);
            }else{
                $('.link-action-sbar-table[data-event="show"]').css("visibility", "hidden");
            }
            $('.link-action-sbar-table[data-event="hide"]').css("visibility", "hidden");
            $('#total-sbar-data').html(settings._iRecordsTotal);
        },
        formFilters: [
            {
                fieldName: "tgl_sbar",
                label: "Waktu Input",
                type: {
                    name: "rangeDate",
                },
            },
            {
                fieldName: "pegawai_input_id",
                label: "Pegawai Input",
                type: {
                    name: "select",
                    payload: [],
                },
            },
            {
                fieldName: "dokter_tujuan_id",
                label: "Dokter Tujuan",
                type: {
                    name: "select",
                    payload: [],
                },
            },
        ],
    });
    $(".dataTables_filter").hide();
    $("#btn-search__tb-sbar").css("display", "none");
    $("#btn-reset__tb-sbar").css("display", "none");
    $(".more-filter").css("display", "none");
    if (tableSbar) {
        var height = jsGetDataTableHeightPx() + 'px'
        $('.dataTables_scrollBody:has(#tb-sbar)').css('min-height', height)
    }
    $(".dataTables_filter").hide();
    $("#btn-search__tb-sbar").css("display", "none");
    $("#btn-reset__tb-sbar").css("display", "none");
    $(".more-filter").css("display", "none");
    
    $('#tb-sbar').on('click', '.btn-edit', function(event) {
        event.preventDefault();
        var sbarId = $(this).data('id');
        let $btn = $(this);
        let userInput = $btn.data('createdby');
        if(createdBy != userInput) {
            docoNotification('error', 'Proses Gagal!', 'Data SBAR hanya dapat di edit oleh user penginput SBAR.');
            return;
        }

        checkStatus(this, sbarId, () => {
            $('#input-sbar').attr({
                'action': `/${modul}${url}/input-sbar?sbar_id=${sbarId}&pendaftaran_id=${pendaftaran_id}`
            });
            $('#input-sbar').trigger('click');
        });
    });

    $('#tb-sbar').on('click', '.btn-verifikasi-sbar', function(e) {
        var sbarId = $(this).data('id');
        var $btn = $(this);
        let dokterTujuanId = $btn.data('dokter-id');
        if(createdBy != dokterTujuanId) {
            docoNotification('error', 'Proses Gagal!', 'Data SBAR hanya dapat di Verifikasi oleh dokter tujuan.');
            return;
        }

        checkStatus(this, sbarId, () => {
            setTimeout(() => {
                $btn.docoForm('click', {
                    url: `/${modul}${url}/verifikasi`,
                    type: 'POST',
                    data: { 'sbar_id': sbarId },
                    skipSuccessNotif: true,
                    confirmTitle: i18next.t("Konfirmasi"),
                    confirmMessage: i18next.t("Apakah anda yakin akan memverifikasi data ini ?"),
                    success: function () {
                        tableSbar.draw();
                        docoNotification('success', 'Proses Berhasil!', 'Data SBAR berhasil di Verifikasi.');
                    },
                    error: function (res) {
                        if(res.responseJSON?.response) {
                            const { title, message } = res.responseJSON.response;
                            docoNotification('error', title, message)
                        }
                    }
                })
            }, 100)
        });
    });

    $('#tb-sbar').on('click', '.btn-delete-sbar', function(e) {
        e.preventDefault();
        var sbarId = $(this).data('id');
        var $btn = $(this);
        let userInput = $btn.data('createdby');
        if(createdBy != userInput) {
            docoNotification('error', 'Proses Gagal!', 'Data SBAR hanya dapat di hapus oleh user penginput SBAR.');
            return;
        }

        checkStatus(this, sbarId, () => {
            setTimeout(() => {
                $btn.docoForm('click', {
                    url: `/${modul}${url}/delete-sbar`,
                    type: 'POST',
                    data: {
                        'sbar_id': sbarId
                    },
                    skipSuccessNotif: true,
                    confirmTitle: i18next.t("Konfirmasi"),
                    confirmMessage: i18next.t("Apakah anda yakin akan menghapus data ini ?"),
                    success: function () {
                        tableSbar.draw();
                        docoNotification('success', 'Proses Berhasil!', 'Data SBAR berhasil di Hapus.');
                    },
                    error: function (res) {
                        if(res.responseJSON?.response) {
                            const { title, message } = res.responseJSON.response;
                            docoNotification('error', title, message)
                        }
                    }
                })
            }, 100);
        });
    });

    $('#tb-sbar').on('click', '.btn-copy-sbar', function(event) {
        event.preventDefault();
        let $btn = $(this);
        let $row = $(this).closest('tr');
        let dokterTujuan = $row.find('td').eq(2).text().trim();
        let inputanSbar = $row.find('td').eq(3).text().trim();
        let vital = parseVitalSigns(inputanSbar);
        let dokterId = $btn.data('dokter-id');

        window._sbarCopyData = { ...vital, dokterId, dokterTujuan };
        
        $('#input-sbar').trigger('click')
    });

    $('#tb-sbar .link-action-sbar-table').on('click', function (e) {
        e.preventDefault();
        if ($(this).data('event') != null && ['show', 'hide'].includes($(this).data('event'))) {
            let limitIncrease = $(this).data('event') == 'show' ? limitDefault : -(limitDefault);
            let limitValue = $(`#tb-sbar tbody > tr.even:visible, tr.odd:visible`).length + limitIncrease;

            if (limitValue > tableSbar.context[0]._iRecordsTotal) {
                limitValue = tableSbar.context[0]._iRecordsTotal
                $('.link-action-sbar-table[data-event="show"]').css("visibility", "hidden");
                $('.link-action-sbar-table[data-event="hide"]').css("visibility", "visible");
            } else if (limitValue < limitDefault) {
                limitValue = limitDefault;
                $('.link-action-sbar-table[data-event="hide"]').css("visibility", "hidden");
                $('.link-action-sbar-table[data-event="show"]').css("visibility", "visible");
            } else {
                $('.link-action-sbar-table[data-event="show"]').css("visibility", "visible");
                $('.link-action-sbar-table[data-event="hide"]').css("visibility", "visible");
            }

            showSbarDatatable('tb-sbar', limitValue);
        }
    })

    $(document).off('click', '#btn-print-sbar').on('click', '#btn-print-sbar', function(e) {
        e.preventDefault();
        applySbarPrintWithCurrentFilter();
    });

    function applySbarPrintWithCurrentFilter() {
        // Get current filter values from the table
        const currentFilter = tableSbar.context[0].ajax.data.advancedFilter || {};
        
        let filterParams = `id=${pendaftaranIdDecrypt}`;
        
        if (currentFilter.tgl_sbar) {
            const dateRange = currentFilter.tgl_sbar.split(' - ');
            if (dateRange.length === 2) {
                filterParams += `&tgl_start=${dateRange[0]}&tgl_end=${dateRange[1]}`;
            } else {
                filterParams += `&tgl_start=&tgl_end=`;
            }
        } else {
            filterParams += `&tgl_start=&tgl_end=`;
        }
        
        const pegawaiInputId = currentFilter.pegawai_input_id && currentFilter.pegawai_input_id !== 'null' 
            ? currentFilter.pegawai_input_id 
            : '';
        filterParams += `&pegawai_input_id=${pegawaiInputId}`;
        
        const dokterTujuanId = currentFilter.dokter_tujuan_id && currentFilter.dokter_tujuan_id !== 'null' 
            ? currentFilter.dokter_tujuan_id 
            : '';
        filterParams += `&dokter_tujuan_id=${dokterTujuanId}`;
        
        window.open(`/reports/viewer/sbar?${filterParams}`);
    }

    function showSbarDatatable(tableId, limit) {
        $(`#${tableId} tbody > tr.even, #${tableId} tbody > tr.odd`).each(function(index, val){
            if (index >= limit) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    }

    function jsGetDataTableHeightPx() 
    {
        var retHeightPx = 1000
        var dataTable = document.getElementById('tb-sbar')
        if (!dataTable) {
            return retHeightPx
        }
        var pageHeight = $(window).height()
        if (pageHeight < 0) {
            return retHeightPx
        }

        var dataTableHeight = pageHeight - 320
        var dataTablePos = $('#tb-sbar').offset()
        if (dataTablePos != null && dataTablePos.top > 0) {
            dataTableHeight = pageHeight - dataTablePos.top - 120
            retHeightPx = Math.max(300, dataTableHeight)
        }
        return retHeightPx
    }
    
    function parseVitalSigns(text) {
        function extractNumber(regex) {
            let match = text.match(regex);
            return match ? parseInt(match[1], 10) : null;
        }

        function extractText(regex) {
            let match = text.match(regex);
            return match ? match[1].trim() : '';
        }

        return {
            situation:      extractText(/Situation\s*:\s*(.*?)\s*Background/i),
            assessment:     extractText(/Assessment\s*:\s*(.*?)\s*Recommendation/i),
            recommendation: extractText(/Recommendation\s*:\s*(.*)$/i),

            sistol:         extractNumber(/Sistol\s*:\s*(\d+)/i),
            diastol:        extractNumber(/Diastol\s*:\s*(\d+)/i),
            beratBadan:     extractNumber(/Berat Badan\s*:\s*(\d+)/i),
            spo2:           extractNumber(/SPO2\s*:\s*(\d+)/i),
            denyutNadi:     extractNumber(/Denyut Nadi\s*:\s*(\d+)/i),
            suhu:           extractNumber(/Suhu\s*:\s*(\d+)/i),
            frekuensiNafas: extractNumber(/Frekuensi Nafas\s*:\s*(\d+)/i),
            lingkarKepala:  extractNumber(/Lingkar Kepala\s*:\s*(\d+)/i),
            tinggiBadan:    extractNumber(/Tinggi Badan\s*:\s*(\d+)/i)
        };
    }

    function checkStatus(button, sbarId, onValid) {
        var $btn = $(button);
        $btn.docoForm('click', {
            url: `/${modul}${url}/check-status?id=${sbarId}`,
            type: 'GET',
            skipConfirm: true,
            skipSuccessNotif: true,
            success: function(data) {
                let message = '';
                let isVerifikasi = (data && data.is_verifikasi) ? data.is_verifikasi : false

                if (!data || data.length === 0 || data === null) {
                    message = 'Data SBAR telah dihapus.';
                } 
                
                if (isVerifikasi) {
                    message = 'Data SBAR telah diverifikasi.';
                }
                if (message) {
                    docoNotification('error', 'Proses Gagal!', message);
                    tableSbar.draw();
                    return;
                }

                if (typeof onValid === 'function') onValid();
            },
            error: function (res) {
                if(res.responseJSON?.response) {
                    const { title, message } = res.responseJSON.response;
                    docoNotification('error', title, message)
                } else {
                    docoNotification('error', 'Proses Gagal!', 'Tidak dapat memeriksa status data.');
                }
            }
        })
    }

    $('#modal_backdrop_sbar').on('hidden.bs.modal', function() {
        $('#input-sbar').attr('action', `/${modul}${url}/input-sbar?pendaftaran_id=${pendaftaranIdSbar}`);
    });

    $(document).on("click", ".btn-reset-sbar", function (e) {
        const tableId = "tb-sbar";
        const element = $(`#filter-section__${tableId}`);
        const formWrapper = $(`#form-filter__${tableId}`);
        element.find("input").val("");
        element.find("select").val(null).trigger("change");
        element
        .find("#tgl_sbar-startDate")
        .val(moment().format("DD-MMM-YYYY"))
        .trigger("change");
        element
        .find("#tgl_sbar-endDate")
        .val(moment().format("DD-MMM-YYYY"))
        .trigger("change");
        const tableElement = $(`#${tableId}`).DataTable();
        showLoader();
        tableElement.context[0].ajax.data.advancedFilter =
        serializeArrayToJson(formWrapper);
        tableElement.ajax.url(`/${modul}${url}/get-data-sbar?pendaftaran_id=${pendaftaranIdSbar}`).load();
    });

    $("#tb-sbar-dokter_tujuan_id--form").select2InfinityScroll({
		url: `/${modul}${url}/filters-sbar`,
		callbackData: (param) => {
			return {
				payload: {
					...param,
                    type: 'pegawai_input',
                    instalasi_id: instalasiId
				}
			}
		},
    });

    $("#tb-sbar-pegawai_input_id--form").select2InfinityScroll({
		url: `/${modul}${url}/filters-sbar`,
		callbackData: (param) => {
			return {
				payload: {
					...param,
                    type: 'pegawai_input',
                    instalasi_id: instalasiId
				}
			}
		},
    });
});

