/*
 * @Author: afil
 * @Date:   2018-01-12 15:50:34
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-14 13:43:00
 */

/**
 *
 * keperluan js index
 *
 */
var tableCppt
var tabel = $('#data-informasi').docoTabel({
    columns: [
        { data: 'rowNum', name: 'rowNum' },
        { data: 'antrian' },
        { data: 'tgl_pendaftaran' },
        { data: 'no_pendaftaran' },
        { data: 'no_rekam_medik' },
        { data: 'nama_pasien' },
        { data: 'jenis_kelamin' },
        { data: 'penjamin_nama' },
        { data: 'nama_pegawai' },
        { data: 'status_periksa1' },
        { data: 'aksi', name: 'aksi' },
    ],
    colNoOrder: [0, 10],
    scrollX: true,
})

var _test = function (bool) {
    if (bool) {
        tabel.reload(false)
    } else {
        tabel.reload()
    }
}

var _afterSave = function (bool) {
    tabel.reload()
    tabel_update.reload()
    tabel_create.reload()
}

var pageFormDataValues = [];
var pageFormId = null;

// status
var laboratorium = false
var radiologi = false
var rehabmedis = false
var bedahsentral = false
var rujukanpasien = false
var pembebasantarif = false
var konsulpoli = false
var permintaankonsul = false

$(document).on('click', '.data-delete', function (event) {
    event.preventDefault()
    $(this).docoForm('delete', {
        success: function (data) {
            _afterSave()
        },
    })
})

$(document).on('click', '.data-aktifasi', function (event) {
    $(this).docoForm('delete', {
        success: function (data) {
            _afterSave()
        },
    })
})

$('.reset-filter').on('click', function (e) {
    e.preventDefault()
    tabel.reset()
})

$('.select2', $('form.form-filter')).change(function (event) {
    event.preventDefault()
    tabel.reload()
})

$('form.form-filter').on('submit', function (e) {
    e.preventDefault()
    tabel.reload()
})

/**
 *
 * Tab load js
 *
 */

function fisikJs() {
    $('.select2').select2()
    $('.date').pickadate({
        applyClass: 'bg-slate-600',
        cancelClass: 'btn-default',
        locale: {
            format: 'DD/MMMM/YYYY',
        },
    })

    $('.input-tags').tagsinput()

    $('#form-fisik').docoForm('submit', {
        success: function (data) {
            if (data.status == 201) this.formInput[0].reset()
            table.draw()
        },
    })

    $('.tabel-fisik').docoTabel({
        filter: false,
        bLengthChange: false,
        bInfo: false,
    })

    $('.tabel-anggotatubuh').docoTabel({
        filter: false,
        bLengthChange: false,
        bInfo: false,
    })
}

function penunjangJs(jenis) {
    // $('.select2').select2();
    // var counter = 1;
    // var jenis = jenis;
    // $("#form-laboratorium").docoForm("submit",{
    //     success : function(data) {
    //         if (data.status == 201)
    //             this.formInput[0].reset();
    //         table.draw();
    //     }
    // });
    // $(".tabel-"+jenis).docoTabel({
    //     filter: false,
    //     bLengthChange: false,
    //     bInfo: false,
    // });
    // $("#selectTindakan-"+jenis).on("change", function (e) {
    //     let t = $(".tabel-"+jenis).docoTabel();
    //     t.row.add([
    //         counter,
    //         "17 January 2018",
    //         jenis + " " + counter,
    //         "Pemeriksaan " + counter,
    //         "20.000",
    //         "<input type='checkbox' id='cekCyto-"+ jenis +"' data-counter = '"+ counter +"'>",
    //         "<span id='tarifCyto-"+ jenis +"-"+ counter +"'>-</span>",
    //         "<input type='text'>",
    //         "<span id='jumlah-"+ jenis +"'>0</span>",
    //         "<a class='btn btn-danger btn-xs data-delete' href='#' data-popup='tooltip' data-placement='bottom' data-original-title='Hapus'><i class='fa fa-trash'></i></a>",
    //     ]).draw( false );
    //     counter++;
    //  });
    //  $("#cekCyto-"+jenis).on("change", function(e){
    //     if(this.checked) {
    //         $('"#tarifCyto-"'+jenis+"-"+counter).html("40.000");
    //     }else{
    //         $('"#tarifCyto-"'+jenis+"-"+counter).html("-");
    //     }
    //  });
}

/* Rujukan Pasien */
function rujukanpasienJs() {
    if (pasienpulang_id != "") {
        $(".non-aktif").prop("disabled", true);
    }
    $('.date').pickadate({
        applyClass: 'bg-slate-600',
        cancelClass: 'btn-default',
        format: 'dd/mm/yyyy',
        onSelect: function (dateText, inst) {
            var dateAsString = dateText;
            var newDateFormat = $.datepicker.parseDate('dd-mm-yyyy', dateAsString);
        }

    })
    $(' .select2 ').select2()
    $('#form-rujukanpasien').docoForm('submit', {
        success: function (data) {
            // if (data.status == 201)
            //     this.formInput[0].reset();
            // table.draw();
        },
    })
}

// tab load
$(document).ready(function () {
    // load pasien riwayat
    // $('#content-pasienriwayat').docoLoad({
    //     url: '/rajal/pemeriksaan/riwayat-pasien?id='+pendaftaran_id+'&pasien_id='+pasien_id,
    //     dataType: 'html',
    //     success : function(data) {
    //         $(" .select2 ").select2();
    //     }
    // });
    // // collapse panel riwayat pasien
    // $("#collapse-riwayatpasien").click();

    $('#tab-soap').on('click', function (e) {
        let tabUrlParams = new URLSearchParams({
            id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
            pasien_id: (typeof pasien_id != 'undefined' ? pasien_id : ''),
            pegawai_id: (typeof pegawai_id != 'undefined' ? pegawai_id : '')
        }).toString();
        
        $('#content-soap').docoLoad({
            url:
                '/rajal/pemeriksaan/soap?'+tabUrlParams,
            dataType: 'html',
            success: function (data) { },
        })
    })

    // $('#tab-cppt').on('click', function (e) {
    //     console.log('logging #01-#tab-cppt');
    //     $('#content-cppt').docoLoad({
    //         url: '/rajal/pemeriksaan/cppt?id=' + pendaftaran_id +'&state='+ statepulang+'&konsulpoli='+ konsulpoli_id,
    //         dataType: 'html',
    //         success: function (data) {
    //             jsRenderDataTable()
    //         },
    //     })
    // })

    $('#tab-tindakan').on('click', function (e) {
        let tabUrlParams = new URLSearchParams({
            id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
            pasien_id: (typeof pasien_id != 'undefined' ? pasien_id : ''),
            kelaspelayanan_id: (typeof kelaspelayanan_id != 'undefined' ? kelaspelayanan_id : '')
        }).toString();
        
        $('#content-tindakan').docoLoad({
            url:
                '/rajal/pemeriksaan/tindakan?'+tabUrlParams,
            dataType: 'html',
            success: function (data) {
                // $('.select2').select2();
            },
        })
    })

    $('#tab-reseptur').on('click', function (e) {
        let tabUrlParams = new URLSearchParams({
            id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
            pasien_id: (typeof pasien_id != 'undefined' ? pasien_id : ''),
        }).toString();
        
        $('#content-reseptur').docoLoad({
            url:
                '/rajal/pemeriksaan/reseptur?'+tabUrlParams,
            dataType: 'html',
            success: function (data) {
                $('.select2').select2()
            },
        })
    })

    $('#tab-penunjang').on('click', function (e) {
        let tabUrlParams = new URLSearchParams({
            id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
        }).toString();
        
        $('#content-penunjang').docoLoad({
            url: '/rajal/pemeriksaan/penunjang?'+tabUrlParams,
            dataType: 'html',
            success: function (data) {
                // penunjangJs('laboratorium');
            },
        })
    })

    $('#tab-radiologi').on('click', function (e) {
        if (!radiologi) {
            // radiologi = true;
            let tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
                jenis: 'radiologi',
            }).toString();
            
            $('#content-radiologi').docoLoad({
                url:
                    '/rajal/pemeriksaan/penunjang?'+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                    penunjangJs('radiologi')
                },
            })
        }
    })

    $('#tab-rehabmedis').on('click', function (e) {
        if (!rehabmedis) {
            // rehabmedis = true;
            let tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
                jenis: 'rehabmedis',
            }).toString();
            
            $('#content-rehabmedis').docoLoad({
                url:
                    '/rajal/pemeriksaan/penunjang?'+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                    penunjangJs('rehabmedis')
                },
            })
        }
    })

    $('#tab-bedahsentral').on('click', function (e) {
        if (!bedahsentral) {
            bedahsentral = true
            let tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
                jenis: 'bedahsentral',
            }).toString();
            
            $('#content-bedahsentral').docoLoad({
                url:
                    '/rajal/pemeriksaan/penunjang?'+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                    penunjangJs('bedahsentral')
                },
            })
        }
    })


    $('#tab-pembebasantarif').on('click', function (e) {
        if (!pembebasantarif) {
            pembebasantarif = true
            let tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
            }).toString();
            
            $('#content-pembebasantarif').docoLoad({
                url: '/rajal/pemeriksaan/pembebasan-tarif?'+tabUrlParams,
                dataType: 'html',
                success: function (data) { },
            })
        }
    })

    $('#tab-konsulpoli').on('click', function (e) {
        let tabUrlParams = new URLSearchParams({
            id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
        }).toString();

        $('#content-konsulpoli').docoLoad({
            url:
                '/rajal/pemeriksaan/konsulpoli?'+tabUrlParams,
            dataType: 'html',
            success: function (data) {
                $(' .select2 ').select2()
            },
        })
    })

    $('#tab-pulang').on('click', function (e) {
        let tabUrlParams = new URLSearchParams({
            id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
        }).toString();

        $('#content-pulang').docoLoad({
            url: '/rajal/pemeriksaan/pemulangan-pasien?'+tabUrlParams,
            dataType: 'html',
            success: function (data) {
                $('.autoJenisKasusPenyakit').select2()
            },
            error: function (res) {
                let _response = JSON.parse(res.responseText);
                docoNotification('error', 'Proses Gagal!', _response?.metadata?.message);
                hideLoader();
            }

        })
    })

    $(".nav-tabs.nav-tab-periksa a").bind("click", allowChangeMenu)

    if ( is_nurse != 0) {
        setTimeout(() => {
            $('#tab-anamnesa').trigger('click');
        }, 100);
    } else {
        setTimeout(() => {
            $('#tab-cppt').trigger('click');
        }, 100);
    }
})
$(document).on('click', '.delete-diagnosa', function (e) {
    e.preventDefault()
    $(this).docoForm('delete', {
        skipConfirm: true,
        success: function (data) {
            tabel_diagnosa.draw()
            let countdialog = $(document).find('#confirm-dialog').length
            let i
            if (countdialog > 0) {
                for (i = 0; i < countdialog; i++) {
                    $(document).find('#confirm-dialog').remove()
                }
            }
        },
    })
})
// Disabled view
$(document).ready(function () {
    // $("#form-anamnesa :input").prop("disabled", true);
    // jsRenderDataTable();

    // window.onresize = function() {
    //     jsRenderDataTable();
    // }

    $('body').on('click', '.btn-batal-tindakan, .btn-batal-bmhp', function () {
        var btn = $(this)
        var id_batal_tindakan = $(this).data('id')
        var tipe_batal_tindakan = $(this).data('tipe')

        btn.button('loading')
        $('#confirm-dialog-overlay').remove()
        var header = 'Perhatian !'
        var add = ''
        add = $('#confirm-form').clone().removeClass('hidden')
        add.find('.input-pemakai').attr('id', 'pemakai-validasi')
        add.find('.input-sandi').attr('id', 'sandi-validasi')
        add = add.html()
        var message = 'Apakah anda yakin untuk menghapus data ini ?' + add

        var label = {
            buttons: {
                No: 'btn button-no',
                Yes: 'btn button-yes',
            },
            hidden: true,
        }

        $.showQuestionDialog(header, message, label, function (reaction) {
            if (reaction == 'Yes') {
                var user = $('#pemakai-validasi').val()
                var pass = $('#sandi-validasi').val()
                $.ajax({
                    url: baseUrl + 'site/cek-user',
                    type: 'POST',
                    data: 'nama_pemakai=' + user + '&katakunci_pemakai=' + pass,
                    success: function (response) {
                        if (response == 'sukses') {
                            hideQuestionDialog()
                            $.ajax({
                                url:
                                    baseUrl +
                                    'rajal/pemeriksaan/batal-tindakan-bmhp?id=' +
                                    pendaftaran_id,
                                type: 'POST',
                                dataType: 'json',
                                async: true,
                                data: {
                                    id_batal: id_batal_tindakan,
                                    tipe: tipe_batal_tindakan,
                                },
                                beforeSend: function () {
                                    var overlayTemplate =
                                        '<div id="confirm-dialog-overlay" class="confirm-dialog-overlay"></div>'
                                    var dialogTemplate = '<div id="confirm-dialog">'
                                    dialogTemplate += '<div class="dialog-content">'
                                    dialogTemplate +=
                                        '<div class="row"><h2 class="confirm-value.no_antrianxt text-center"></h2></div><p class="confirm-message-text"></p>'
                                    dialogTemplate +=
                                        '<div class="row"><h2 class="confirm-value.aksixt text-center"></h2></div><p class="confirm-message-text"></p>'
                                    dialogTemplate += '</div>'
                                    dialogTemplate += '</div>'

                                    $('body').append(overlayTemplate)
                                    $('body').append(dialogTemplate)
                                    $('.confirm-header-text').html(
                                        '<i class="fa fa-gear fa-spin fa-3x fa-fw"></i>&nbsp;Sedang memproses . . .'
                                    )
                                },
                                success: function (data) {
                                    var title = 'Proses Berhasil !'
                                    var text = 'Data berhasil dihapus'

                                    try {
                                        title = data.response.title
                                        text = data.response.text

                                        if (typeof data.response.return != 'undefined') {
                                            eval('( ' + data.response.return + ' )')
                                        }
                                    } catch (e) {
                                        console.log('')
                                    }

                                    new PNotify({
                                        title: title,
                                        text: text,
                                        addclass:
                                            'alert alert-success alert-arrow-right alert-styled-right',
                                        type: 'success',
                                    })
                                    btn.button('reset')
                                    $('#content-tindakan').docoLoad({
                                        url:
                                            '/rajal/pemeriksaan/tindakan?id=' +
                                            pendaftaran_id +
                                            '&pasien_id=' +
                                            pasien_id +
                                            '&kelaspelayanan_id=' +
                                            kelaspelayanan_id,
                                        dataType: 'html',
                                        success: function (data) {
                                            // $('.select2').select2();
                                        },
                                    })
                                },
                                error: function (data) {
                                    $('html, body').animate({ scrollTop: 0 }, 'slow')
                                    // options.error(data);
                                    hideQuestionDialog()
                                    $('body').find('.confirm-dialog-overlay').remove()

                                    var data = data.responseJSON
                                    var title = 'Proses Gagal !'
                                    var text = 'Terjadi kesalahan pada sistem'

                                    try {
                                        title = data.response.title
                                        text = data.response.text
                                    } catch (e) {
                                        console.log('')
                                    }

                                    new PNotify({
                                        title: title,
                                        text: text,
                                        addclass:
                                            'alert alert-warning alert-arrow-right alert-styled-right',
                                        type: 'error',
                                    })
                                    btn.button('reset')
                                },
                            }).done(function () {
                                hideQuestionDialog()
                                $('body').find('.confirm-dialog-overlay').remove()
                                btn.button('reset')
                            })
                        } else {
                            new PNotify({
                                title: 'Terjadi Kesalahan',
                                text: response,
                                addclass:
                                    'alert alert-warning alert-arrow-right alert-styled-right',
                                type: 'error',
                            })
                        }
                    },
                })
            } else {
                hideQuestionDialog()
                btn.button('reset')
                $('[data-popup="tooltip"]').tooltip()
            }
        })
    })

    // $('body').on('click','.btn-batal-bmhp',function(){
    //     console.log(this);
    // });
})

function jsRenderDataTable() {
    // $("#tb-cppt").clear();


    tableCppt = $('#tb-cppt').DataTable({
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        info: false,
        paging: false,
        aaSorting: [],
        ajax: {
            url : baseUrl + 'rajal/pemeriksaan/get-data-cppt',
            data : function(data) {
                data.id = pendaftaran_id;
                data.source = 'cppt';
                data.ruangan_id = $('#filter-cppt-ruangan_id').val();
                data.pegawai_id = $('#filter-cppt-pegawai_id').val();
                data.kelompokpegawai_id = $('#filter-cppt-kelompokpegawai_id').val();
                data.limit = $('#filter-cppt-limit').val();
                var tgl_cppt = null;
                if($('.endDate1').val() != ''){
                    tgl_cppt = $('.startDate1').val()+'-'+$('.endDate1').val();
                }
                data.tanggal_cppt = tgl_cppt
            }
        },
        columns: [
            {
                title: 'No',
                name: 'no',
                data: 'no',
                searchable: false,
                orderable: false,
            },
            {
                title: 'Ruang',
                name: 'tgl_soaprj',
                data: 'ruangan',
                searchable: false,
                orderable: true,
                className: 'vtop',
            },
            {
                title: 'Hasil Asesmen Penatalaksanaan Pasien',
                name: 'penatalaksanaan',
                data: 'penatalaksanaan',
                className: 'wraptext',
                width: '50px',
                searchable: false,
                orderable: false,
            },
            {
                title: 'Instruksi',
                name: 'instruksi',
                data: 'instruksi',
                className: 'wraptext',
                width: '50px',
                searchable: false,
                orderable: false,
            },
            {
                title: 'Aksi',
                data: 'aksi',
                searchable: false,
                orderable: false,
                className: 'vtop',
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
                sortDescending: sortDescending,
            },
        },
        createdRow: (row, data, dataIndex, cells) => {
            if ( $('#soaprjform-soaprj_id').val() == data.rawData.soaprj_id.value) {
                $(row).find('.edit-cppt').addClass('hidden')
            } else {
                $(row).find('.batal-edit-cppt').addClass('hidden')
            }
            if (data.rawData.is_dokter.value) {
                for (var i = 0; i < row.childNodes.length; ++i) {
                    if (i == 1) {
                        $(row.childNodes[i]).css('background-color', '#A7F8FA')
                        $(row.childNodes[i]).css('color', 'black')
                      }
                  }
            }
        },
        drawCallback: (settings) => {
            if(settings.aoData.length > 5){
                $('.link-action-cppt-table[data-event="show"]').css("visibility", "visible");
                showCpptDatatable(settings.sTableId, 5);
            }else{
                $('.link-action-cppt-table[data-event="show"]').css("visibility", "hidden");
            }
            $('.link-action-cppt-table[data-event="hide"]').css("visibility", "hidden");
            $('#total-cppt-data').html(settings._iRecordsTotal);
            // if(settings._aiDisplay)
            $('.edit-cppt').unbind()
            $('.batal-edit-cppt').unbind()

            $('.edit-cppt').bind('click', ({delegateTarget}) => {
                skipTimeout = true;
                let _cpptIndex = tableCppt.data().toArray().findIndex(cpptData => cpptData.rawData.soaprj_id.value == $(delegateTarget).data('soaprj_id') )
                if ( _cpptIndex < 0) {
                    return null;
                }

                if($(delegateTarget).data('is_icd_x')){
                    $('#soaprjform-a_diag_utama_text').parent().hide()
                    $('#soaprjform-a_diag_utama').parent().show()
                    $('#soaprjform-is_icd_x').prop('checked',true).uniform('refresh');
                }else{
                    $('#soaprjform-a_diag_utama_text').parent().show()
                    $('#soaprjform-a_diag_utama').parent().hide()
                    $('#soaprjform-is_icd_x').prop('checked',false).uniform('refresh');
                }

                if ( $('.edit-cppt').hasClass('hidden') ) {
                    $('.edit-cppt').removeClass('hidden')
                }
                $('.batal-edit-cppt:not(.hidden)').addClass('hidden')

                if($('#soaprjform-soaprj_id').val() != $(delegateTarget).data('soaprj_id')){
                    $("#soaprjform-tgl_soaprj").val(moment(new Date()).format('DD/MM/YYYY HH:mm:00'))
                    $('#soaprjform-soaprj_id').val(null)
                    $('#soaprjform-subject').val(null)
                    $('#soaprjform-object').val(null)
                    $('#soaprjform-planning').val(null)
                    $('#soaprjform-a_diag_utama').val(null).trigger('change')
                    $('#soaprjform-a_diag_penyerta').empty().val(null).trigger('change')
                    $('#soaprjform-catatan_dokter').val(null)

                    const {rawData} = tableCppt.data().toArray()[_cpptIndex]
                        $.each(rawData, (index, cpptItem) => {
                            if ( cpptItem.formId != undefined) {
                                if ( index == 'diagnosa_utama' ) {
                                    if ( Object.keys(cpptItem.value).length ) {
                                        var selectedDiagnosa = `${cpptItem.value.id}_${cpptItem.value.text}`
                                        var newOption = new Option(cpptItem.value.text, selectedDiagnosa, false, false);
                                        $(`#${cpptItem.formId}`).append(newOption).val(selectedDiagnosa).trigger('change')
                                        $('#soaprjform-a_diag_utama_text').val(cpptItem.value.text)
                                    }
                                } else if( index == 'diagnosa_penyerta' ) {
                                    if ( cpptItem.value.length ) {
                                        var selectedOption = []
                                        $.each(cpptItem.value, (index, diagnose) => {
                                            var newOption = new Option(diagnose.text, `${diagnose.id != undefined ? diagnose.id + '_' : '' }${diagnose.text}`, false, false);
                                            $(`#${cpptItem.formId}`).append(newOption)
                                            selectedOption.push(`${diagnose.id != undefined ? diagnose.id + '_' : '' }${diagnose.text}`)
                                        })

                                        $(`#${cpptItem.formId}`).val(selectedOption).trigger('change')
                                    }
                                } else if(index == 'tgl_soaprj') {
                                    $(`#${cpptItem.formId}`).val(moment(cpptItem.value).format('DD/MM/YYYY H:m:00'))
                                }else {
                                    $(`#${cpptItem.formId}`).val(cpptItem.value)
                                }
                            }
                        })
                }
                $(`.edit-cppt[data-soaprj_id=${$(delegateTarget).data('soaprj_id')}]`).addClass('hidden')
                $(`.batal-edit-cppt[data-soaprj_id=${$(delegateTarget).data('soaprj_id')}]`).removeClass('hidden')
            })
            $('.copy-cppt').bind('click', ({delegateTarget}) => {
                skipTimeout = true;
                let _cpptIndex = tableCppt.data().toArray().findIndex(cpptData => cpptData.rawData.soaprj_id.value == $(delegateTarget).data('soaprj_id') )
                if ( _cpptIndex < 0) {
                    return null;
                }

                if($(delegateTarget).data('is_icd_x')){
                    $('#soaprjform-a_diag_utama_text').parent().hide()
                    $('#soaprjform-a_diag_utama').parent().show()
                    $('#soaprjform-is_icd_x').prop('checked',true).uniform('refresh');
                }else{
                    $('#soaprjform-a_diag_utama_text').parent().show()
                    $('#soaprjform-a_diag_utama').parent().hide()
                    $('#soaprjform-is_icd_x').prop('checked',false).uniform('refresh');
                }


                if($('#soaprjform-soaprj_id').val() != $(delegateTarget).data('soaprj_id')){
                    $("#soaprjform-tgl_soaprj").val(moment(new Date()).format('DD/MM/YYYY HH:mm:00'))
                    $('#soaprjform-soaprj_id').val(null)
                    $('#soaprjform-subject').val(null)
                    $('#soaprjform-object').val(null)
                    $('#soaprjform-planning').val(null)
                    $('#soaprjform-a_diag_utama').val(null).trigger('change')
                    $('#soaprjform-a_diag_penyerta').empty().val(null).trigger('change')
                    $('#soaprjform-catatan_dokter').val(null)

                    const {rawData} = tableCppt.data().toArray()[_cpptIndex]
                        $.each(rawData, (index, cpptItem) => {
                            if ( cpptItem.formId != undefined) {
                                if ( index == 'diagnosa_utama' ) {
                                    if ( Object.keys(cpptItem.value).length ) {
                                        var selectedDiagnosa = `${cpptItem.value.id}_${cpptItem.value.text}`
                                        var newOption = new Option(cpptItem.value.text, selectedDiagnosa, false, false);
                                        $(`#${cpptItem.formId}`).append(newOption).val(selectedDiagnosa).trigger('change')
                                        $('#soaprjform-a_diag_utama_text').val(cpptItem.value.text)
                                    }
                                } else if( index == 'diagnosa_penyerta' ) {
                                    if ( cpptItem.value.length ) {
                                        var selectedOption = []
                                        $.each(cpptItem.value, (index, diagnose) => {
                                            var newOption = new Option(diagnose.text, `${diagnose.id != undefined ? diagnose.id + '_' : '' }${diagnose.text}`, false, false);
                                            $(`#${cpptItem.formId}`).append(newOption)
                                            selectedOption.push(`${diagnose.id != undefined ? diagnose.id + '_' : '' }${diagnose.text}`)
                                        })

                                        $(`#${cpptItem.formId}`).val(selectedOption).trigger('change')
                                    }
                                } else if(index == 'tgl_soaprj') {
                                    $(`#${cpptItem.formId}`).val(moment(cpptItem.value).format('DD/MM/YYYY H:m:00'))
                                }else {
                                    $(`#${cpptItem.formId}`).val(cpptItem.value)
                                }
                            }
                        })
                }
                $('#soaprjform-soaprj_id').val(null)
                $("#soaprjform-tgl_soaprj").val(moment(new Date()).format('DD/MM/YYYY HH:mm:00'))

            })

            $('.batal-edit-cppt').bind('click', ({delegateTarget}) => {
                skipTimeout = true;
                $("#soaprjform-tgl_soaprj").val(moment(new Date()).format('DD/MM/YYYY HH:mm:00'))
                $('#soaprjform-soaprj_id').val(null)
                $('#soaprjform-subject').val(null)
                $('#soaprjform-object').val(null)
                $('#soaprjform-planning').val(null)
                $('#soaprjform-a_diag_utama').val(null).trigger('change')
                $('#soaprjform-a_diag_penyerta').val(null).trigger('change')
                $('#soaprjform-a_diag_utama_text').val(null)
                $('#soaprjform-catatan_dokter').val(null)
                $('#soaprjform-instruksi').val(null)
                $(`.edit-cppt[data-soaprj_id=${$(delegateTarget).data('soaprj_id')}]`).removeClass('hidden')
                $(`.batal-edit-cppt[data-soaprj_id=${$(delegateTarget).data('soaprj_id')}]`).addClass('hidden')
            })
        }
    })

    if (tableCppt) {
        var height = jsGetDataTableHeightPx() + 'px'
        $('.dataTables_scrollBody:has(#tb-cppt)').css('min-height', height)
    }
}

function showVerbalOrder() {
    $("#div-verbal-order").docoLoad({
        url: "/rajal/pemeriksaan/create-verbal-order?id=" + pendaftaran_id,
        dataType: 'html',
        success: function (data) {
            $("#div-verbal-order").prop("hidden", false);
            $("#div-cppt").prop("hidden", true);
        }
    });
}

function jsGetDataTableHeightPx() {
    // set default return height
    var retHeightPx = 1000

    // no nada if there is no dataTable (container) element
    var dataTable = document.getElementById('tb-cppt')
    if (!dataTable) {
        return retHeightPx
    }

    // do nada if we can't determine the browser height
    var pageHeight = $(window).height()
    if (pageHeight < 0) {
        return retHeightPx
    }

    // determine the data table height based upon the browser page height
    var dataTableHeight = pageHeight - 320 //default height
    var dataTablePos = $('#tb-cppt').offset()
    if (dataTablePos != null && dataTablePos.top > 0) {
        // the data table height is the page height minus the top of the data table,
        // minus space for any buttons at the bottom of the page
        dataTableHeight = pageHeight - dataTablePos.top - 120

        // clip height to min. value
        retHeightPx = Math.max(300, dataTableHeight)
    }
    return retHeightPx
}

function showCpptDatatable(tableId, limit)
{
    let indexShowed = $(`#${tableId} tbody > tr.even, tr.odd`).length - limit;

    $(`#${tableId} tbody > tr.even, tr.odd`).each(function(index, val){
        if( index>limit){
            $(this).hide();
        }else{
            $(this).show();
        }
    })
}

$(document).ready(function () {
    $("#terra-medik-soap").click(function () {
        $("#terra-medik-soap").attr("action", "/rajal/riwayat-pasien/modal-history-terra-medik?pasien_id=" + pasien_id);
    });

    // initial collapsed
    $('.can-expanded').each(function(){
        toggleExpandRiwayat($(this));
    })

    $('.expand-data').on('click', function(e){
        e.preventDefault();
        if(!$(this).attr('expanded')){
            $(this).html('Ringkaskan..');
            $(this).attr('expanded', true);
        }else{
            $(this).html($(this).attr('data-text'));
            $(this).removeAttr('expanded');
        }
        let elementExpand = $(this).siblings('.can-expanded');
        toggleExpandRiwayat(elementExpand);
    });

    function toggleExpandRiwayat(elementTarget){
        let expandElement = elementTarget.children();
        if(expandElement.length > 3){
            expandElement.each(function(index, element){
                if(index > 1){
                    if($(this).css('display') == 'none'){
                        $(this).show();
                    }else{
                        $(this).hide();
                    }
                }
            })
        }
    }
});

var offsetTopNavTabs = $("#nav-sticky").offset().top
var heightNav = $($('.navbar-position')[0]).height()
var patientHeight = $($('.patient-informations')[0]).height()
var patientOffset = $($('.patient-informations')[0]).offset().top
$(document).scroll(function () {
    var scrollTop = $(document).scrollTop()

    if ( (scrollTop + patientHeight) > patientOffset) {
        $('.patient-informations').addClass('floating-sticky')
    } else {
        $('.patient-informations').removeClass('floating-sticky')
    }

    if ((scrollTop + heightNav) >= offsetTopNavTabs) {
        $("#nav-sticky").addClass('nav-tabs__sticky')
    } else {
        $("#nav-sticky").removeClass('nav-tabs__sticky')
    }
})

/*
* Trigger docoload menu di periksa pelayanan
*/
function runPeriksaMenuAction (menuId) {
    console.log(menuId);
    let tabUrlParams;
    var _urlPeriksaFisik = '/rajal/pemeriksaan/periksa-fisik?id=' +
                    pendaftaran_id +
                    '&pasien_id=' +
                    pasien_id +
                    '&kelaspelayanan_id=' +
                    kelaspelayanan_id

    if(konsulpoli_id) {
        _urlPeriksaFisik = _urlPeriksaFisik + '&konsulpoli_id=' + konsulpoli_id
    }
    switch (menuId) {
        case 'tab-anamnesa':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
                pegawai_id: (typeof pegawai_id != 'undefined' ? pegawai_id : ''),
            }).toString();
            
            $('#content-anamnesa').docoLoad({
                url:
                    '/rajal/pemeriksaan/anamnesa?'+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                },
            })
            break;
        case 'tab-periksafisik':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
                pasien_id: (typeof pasien_id != 'undefined' ? pasien_id : ''),
                kelaspelayanan_id: (typeof kelaspelayanan_id != 'undefined' ? kelaspelayanan_id : ''),
            }).toString();
            
            $('#content-periksafisik').docoLoad({
                url:
                    '/rajal/pemeriksaan/periksa-fisik?'+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                    $('.select2').select2()
                },
            })
            break;
        case 'tab-nursingnote':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
            }).toString();
            
            $('#content-nursingnote').docoLoad({
                url:'/rajal/pemeriksaan/nursing-note?'+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                    $('.select2').select2()
                },
            })
            break;

        case 'tab-cppt':
              tabUrlParams = new URLSearchParams({
                  id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                  konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
                  state: (typeof statepulang != 'undefined' ? statepulang : '')
              }).toString();
              
            $('#content-cppt').docoLoad({
                url: '/rajal/pemeriksaan/cppt?'+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                    jsRenderDataTable()
                },
            })
            break;

        case 'tab-diagnosa':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
                pasien_id: (typeof pasien_id != 'undefined' ? pasien_id : '')
            }).toString();
            
            $('#content-diagnosa').docoLoad({
                url:
                    '/rajal/pemeriksaan/diagnosa?'+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                    $(' .select2 ').select2()
                },
            })
            break;

        case 'tab-resume':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
                pasien_id: (typeof pasien_id != 'undefined' ? pasien_id : '')
            }).toString();
            
            $('#content-resume').docoLoad({
                url:
                    '/rajal/pemeriksaan/resume-medis?'+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                },
            })
            break;

        case 'tab-upload-dokumen':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
            }).toString();
            
            $("#content-upload-dokumen").docoLoad({
                url:"/api/upload-dokumen/tab-upload-dokumen?"+tabUrlParams,
                dataType:"html",
                success: function(data){},
            });
            break;

        case 'tab-rujukanpasien':
            if (!rujukanpasien) {
                rujukanpasien = true
                tabUrlParams = new URLSearchParams({
                    id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                    konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
                    pasien_id: (typeof pasien_id != 'undefined' ? pasien_id : '')
                }).toString();
                
                $('#content-rujukanpasien').docoLoad({
                    url:
                        '/rajal/pemeriksaan/rujukan-pasien?'+tabUrlParams,
                    dataType: 'html',
                    success: function (data) {
                        rujukanpasienJs()
                    },
                })
            }
            break;

        case 'tab-permintaankonsul':
            if ($(this).hasClass('disabled')) {
                e.preventDefault()
                return false
            } else {
                $('#content-permintaankonsul').docoLoad({
                    url:
                        '/rajal/pemeriksaan/permintaan-konsul?id=' +
                        pendaftaran_id +
                        '&pasien_id=' +
                        pasien_id +
                        '&konsulpoli_id=' +
                        konsulpoli_id,
                    dataType: 'html',
                    success: function (data) { },
                })
            }
            break;

        case 'tab-pemeriksaan-mcu':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
                pasien_id: (typeof pasien_id != 'undefined' ? pasien_id : ''),
                ruangan_id: (typeof ruangan_id != 'undefined' ? ruangan_id: '')
            }).toString();
            
            $('#content-pemeriksaan-mcu').docoLoad({
                url: '/mcu/pemeriksaan/pemeriksaan-mcu?'+tabUrlParams,
                dataType: 'html',
                success: function (data) {

                },
            })
            break;

        case 'tab-permintaanmakan':
            tabUrlParams = new URLSearchParams({
                  id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                  konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
            }).toString();
            
            $('#content-permintaanmakan').docoLoad({
                url: '/rajal/pemeriksaan/permintaan-makan?'+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                    $(' .select2 ').select2()
                },
            })
            break;

        case 'tab-cathlab-koroangiografi':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
                tipe: 'koroangiografi'
            }).toString();
            
            $("#content-cathlab").docoLoad({
                url: "/rajal/pemeriksaan/cathlab?"+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

        case 'tab-cathlab-pci':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
                tipe: 'pci'
            }).toString();
            
            $("#content-cathlab").docoLoad({
                url: "/rajal/pemeriksaan/cathlab?"+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

        case 'tab-cathlab-dsa':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : ''),
                tipe: 'dsa'
            }).toString();
            
            $("#content-cathlab").docoLoad({
                url: "/rajal/pemeriksaan/cathlab?"+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

        case 'tab-rujuk-balik':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : '')
            }).toString();
            
            $("#content-rujuk-balik").docoLoad({
                url: "/rajal/pemeriksaan/rujuk-balik?"+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

        case 'tab-surat-keterangan':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
                konsulpoli_id: (typeof konsulpoli_id != 'undefined' ? konsulpoli_id : '')
            }).toString();
            
            $("#content-surat-keterangan").docoLoad({
                url: "/rajal/pemeriksaan/surat-keterangan?"+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

        case 'tab-monitoring-ttv':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            }).toString();
            
            $("#content-monitoring-ttv").docoLoad({
                url: "/rajal/pemeriksaan/monitoring-ttv?"+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

        case 'tab-monitoring-ews':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            }).toString();
            
            $("#content-monitoring-ews").docoLoad({
                url: "/rajal/pemeriksaan/observasi-ews?"+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

        case 'tab-sbar':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            }).toString();
            
            $("#content-sbar").docoLoad({
                url: "/rajal/pemeriksaan/sbar?"+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

      default:
  }
}

function allowChangeMenu(event) {
    let _this = $(this);
    let defaultData = pageFormDataValues;
    let existingData = (pageFormId != null && pageFormId.length > 0) ? pageFormId.serializeArray() : [];

    let isEqualData = isArrayEqual(defaultData, existingData);
    console.log(defaultData, existingData, isEqualData);
    let skipConfirmTabChange = event.data != undefined ? event.data.skipConfirmTabChange : false;
    if (pageFormDataValues.length == 0 || isEqualData || skipConfirmTabChange) {
        pageFormDataValues = [];
        pageFormId = null;
        runPeriksaMenuAction(_this.attr('id'));
    } else {
        event.stopPropagation();
        confirmationDialog("Apakah anda yakin untuk meninggalkan halaman ini?", function (condition) {
            if (condition) {
                pageFormDataValues = [];
                pageFormId = null;
                $(_this).trigger('click', {skipConfirmTabChange: true});
            } else {
                return false;
            }
        });
    }
}
