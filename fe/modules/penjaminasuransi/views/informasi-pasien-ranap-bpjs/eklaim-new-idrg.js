/**
 * 
 * @author : Erlangga (erlangga@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

const SPECIAL_PROCEDURE = 'Special Procedure';
const SPECIAL_PROSTHESIS = 'Special Prosthesis';
const SPECIAL_DRUG = 'Special Drug';
const SPECIAL_INVESTIGATION = 'Special Investigation';
const MIN_ACCUTE = 43;
const MIN_CRONIC = 104;
const ACCUTE = 12;
const CRONIC = 12;
const MAX_ACCUTE = 60;
const MAX_CRONIC = 60;
const INVALID_PARAMETERS = 'INVALID';
const ERROR_PARAMETERS = 'ERROR';
let primerSudahKoresi = $('#klaiminacbgranapform-diagnosa_primer').val()
let sekunderSudahKoreksi = $('#klaiminacbgranapform-diagnosa_sekunder').val()
let ddSpecProc = $('#sproc-combo');
let ddSpecPros = $('#spros-combo');
let ddSpecInv = $('#inv-combo');
let ddSpecDrug = $('#drug-combo');
let cronic = $('#cronic');
let subAccute = $('#sub-acute');
let totalHarga = $('#total-harga');
let arrSpecProc = [{
    description: 'None',
    code: '-',
    tariff: 0
}];
let arrSpecPros = [{
    description: 'None',
    code: '-',
    tariff: 0
}];
let arrSpecDrug = [{
    description: 'None',
    code: '-',
    tariff: 0
}];
let arrSpecInv = [{
    description: 'None',
    code: '-',
    tariff: 0
}];
let _total = 0;
let _specProc = 0;
let _specPros = 0;
let _specInv = 0;
let _specDrug = 0;
let nosep = $('#no_sep').val();
let spesialProsedur = $('.spesial-prosedur');
let tarifSpecial = [0, 0, 0, 0, 0]
let kodeDiagnosa = []
var i = 0;
var _proses = true
var _deletedDiagnosa = [];
var _klaimDetail = [];
var _diagnosa10 = [];
var _diagnosa9 = [];
var _grouper = [];
var _dokter = [];
var _addDetail = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
var _delete = false;
var lama_rawat = date_diff_indays($('#klaiminacbgranapform-tgl_masuk').val(), $('#klaiminacbgranapform-tgl_keluar').val())
var flag = true
var isPrimer
var _jaminan_klaim = [];
var _no_pengajuan_covid = [];
var _identitas_value_ = [];
var _identitas_id = [];
let _getBerkas = false;
let noSep = $('.no-sep').val()
let noClaim = $('#no_klaimcovid').val() 
let arrayJaminan = [COVID, KIPI, JAMPERSAL, COINSIDENSE, BAYIBARULAHIR, PERPANJANGANMASARAWAT]
let jenisRawatRanap = 1;
let jenisRawatRajal = 2;
let jenisRawatIgd = 3;

$(document).ready(function () {
    let lama_rawat = date_diff_indays($('#klaiminacbgranapform-tgl_masuk').val(), $('#klaiminacbgranapform-tgl_keluar').val())
    $('#klaiminacbgranapform-los').val(lama_rawat)
    $('#tab-unu').trigger('click')
    ddSpecProc.select2({
        data: ''
    });
    ddSpecPros.select2({
        data: ''
    });
    ddSpecInv.select2({
        data: ''
    });
    ddSpecDrug.select2({
        data: ''
    });
    var _valuekelas = $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]:checked').val();
    // lookupkelas(_valuekelas)
    var disabledKelas = function (_valuekelas) {
        $('#naikkelas-' + _valuekelas).checked == false;
        $('#naikkelas-' + _valuekelas).prop("disabled", true);
        // $('#naikkelas-' + _valuekelas).prop("checked", true);
    }

    function checkPasienTb() {
        var pasien_tb = document.getElementById("klaiminacbgranapform-pasien_tb");
        let isEks = $(".sitb-field")

        if (pasien_tb.checked == true) {
            isEks.fadeIn(500).removeClass('hidden');
            $('#sitb').val(numberPasientb)
            hideValidasiSitb()
        } else {
            isEks.hide()
        }
    }

    formDokterMultiple()
    checkValidateSitb()

    checkPasienTb()
    $('#klaiminacbgranapform-pasien_tb').change(function(e) {
       checkPasienTb()
    })

    checkEksekutif()
    $('#klaiminacbgranapform-kelas_eksekutif').change(function(e){
        let jaminan = $('#klaiminacbgranapform-klaim_penjamin').val()
        if(jaminan == JKN) {
            checkEksekutif()
        }
    })
    
    $('.search-sitb').click(function(e) {
       e.preventDefault()
       let value = $('#sitb').val()
       let no_rekammedik = $('#no_rekam_medik').val()
       let nomer_peserta = $('#nomer_peserta').val()
       let nama_pasien = $('#nama_pasien').val()
       let tanggal_lahir = $('#klaiminacbgranapform-tgl_lahir').val()
       let jeniskelamin = $('#klaiminacbgranapform-jeniskelamin').val()

       if(value == '' || value == undefined) {
           alert('Nomer SITB belum di isi!')
           return false
       }
       
       $.ajax({
           type: "POST",
           url: `/penjamin-asuransi/informasi-pasien-ranap-bpjs/validasi-sitb`,
           data: {
               nosep: infoNoSep,
               nomer_sitb: value,
               no_rekammedik: no_rekammedik,
               nomer_peserta: nomer_peserta,
               nama_pasien: nama_pasien,
               tanggal_lahir: tanggal_lahir,
               jenis_kelamin: jeniskelamin,
               kunjungan_id: kunjunganId
           },
           success: function (response) {
               if(response != undefined) {
                   if(response.code == 400) {
                       docoNotification('warning', 'Peringatan', response?.message);
                   }

                   if(response.code == 200) {
                       if(response?.response.status == "INVALID") {
                           docoNotification('warning', 'Oops', response?.response?.detail);
                       }else{
                           docoNotification('success', 'Berhasil', response?.response?.detail);
                            showKonfirmasi(response?.response)
                       }
                   }
               }
           },
           error: function(jqXhr) {
               alert(jqXhr.responseText)
           }
       });
    })


    if (isCheck == true) {
        $('#klaiminacbgranapform-is_naikkelas').prop("checked", true)
    } else {
        $('#klaiminacbgranapform-is_naikkelas').prop("checked", false)
    }

    if(isVentilator == 1) {
        $("#klaiminacbgranapform-ventilator").prop("checked", true)
        $('#date-ventilator').removeClass('hidden');
    } else {
        $("#klaiminacbgranapform-ventilator").prop("checked", false)
    }

    if (infoNoSep === nosep) {
        let _html = i18next.t('<b><i class="fa fa-search"></i></b> ' + 'Validasi SEP');
        $('#btn-search-sep').html(_html).attr('disabled', true);
    }

    $('#klaiminacbgranapform-tgl_masuk').datetimepicker({
        'startDate': '0d',
        'readonly': true,
        'endDate': $('#klaiminacbgranapform-tgl_keluar').val(),
        'autoclose': true,
        'format': 'dd-M-yyyy hh:ii',
        'tabindex': 4,
        'use24hours': true
    })

    $('#klaiminacbgranapform-tgl_keluar').datetimepicker({
        'startDate': $('#klaiminacbgranapform-tgl_masuk').val(),
        'endDate': dateNow,
        'autoclose': true,
        'format': 'dd-M-yyyy hh:ii',
        'tabindex': 4,
        'use24hours': true
    })

    $('#klaiminacbgranapform-intubasi').datetimepicker({
        'startDate': '0d',
        'endDate': $('#klaiminacbgranapform-ekstubasi').val(),
        'autoclose': true,
        'format': 'dd-M-yyyy hh:ii',
        'tabindex': 4,
        'use24hours': true
    })
    

    $('#klaiminacbgranapform-ekstubasi').datetimepicker({
        'startDate': $('#klaiminacbgranapform-intubasi').val(),
        'endDate': dateNow,
        'autoclose': true,
        'format': 'dd-M-yyyy hh:ii',
        'tabindex': 4,
        'use24hours': true
    })

    $('#klaiminacbgranapform-jenis_kelasrawat').on('change', function () {
        var _valuekelas = $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]:checked').val()
        $('#naikkelas-' + _valuekelas).checked == false;
        $('#naikkelas-' + _valuekelas).prop("disabled", true);
        if (_valuekelas == 1) {
            $('#naikkelas-2').prop("disabled", false);
            $('#naikkelas-3').prop("disabled", false);
            $('#naikkelas-4').prop("disabled", false);
            $('#naikkelas-5').prop("disabled", false);
        } else if (_valuekelas == 2) {
            $('#naikkelas-1').prop("disabled", false);
            $('#naikkelas-3').prop("disabled", false);
            $('#naikkelas-4').prop("disabled", false);
            $('#naikkelas-5').prop("disabled", false);
        } else if (_valuekelas == 3) {
            $('#naikkelas-1').prop("disabled", true);
            $('#naikkelas-2').prop("disabled", true);
            $('#naikkelas-3').prop("checked", true);
            $('#naikkelas-4').prop("disabled", true);
            $('#naikkelas-5').prop("disabled", false);
        }
        disabledKelas(_valuekelas);
    })

    $('#btn-back').on('click', function (e) {
        e.preventDefault();
        window.location.replace(baseUrl+`penjamin-asuransi/informasi-pasien-ranap-bpjs`);
    });

    disabledKelas(_valuekelas);
    if (_final) {
        $('#judul-grouper').empty().html('Hasil Grouper - Final')
        $('#klaiminacbgranapform-tgl_masuk').prop('disabled', true)
        $('#klaiminacbgranapform-tgl_keluar').prop('disabled', true)
        $('.hide-me').addClass('hidden')
        spesialProsedur.attr('disabled', true)
        $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]').prop('disabled', true)
        if ($('input[name="KlaimInacbgRanapForm[is_naikkelas]"]').is(':checked') == true) {
            $('input[name="KlaimInacbgRanapForm[is_naikkelas]"]').prop('disabled', true);
            $('input[name="KlaimInacbgRanapForm[naik_kelas]"]').prop('disabled', true)
            $('#klaiminacbgranapform-lama_rawatkelas').prop('disabled', true)
        } else {
            $('input[name="KlaimInacbgRanapForm[is_naikkelas]"]').prop('disabled', true);
        }
        if ($('input[name="KlaimInacbgRanapForm[is_rawatintensif]"]').is(':checked') == true) {
            $('input[name="KlaimInacbgRanapForm[is_rawatintensif]"]').prop('disabled', true)
            $('#klaiminacbgranapform-lama_rawatintensif').prop('disabled', true);
            $('#klaiminacbgranapform-ventilator').prop('disabled', true);
        } else {
            $('input[name="KlaimInacbgRanapForm[is_rawatintensif]"]').prop('disabled', true)
        }
        $('#btn-hapus-klaim').prop('disabled', true);
    } else {
        $('#btn-hapus-klaim').prop('disabled', false);
    }
    if (_diajukan == 1) {
        if (!$('.btn-edit-klaim').hasClass('hidden')) {
            $('.btn-edit-klaim').addClass('hidden')
        }
    }

    if (_final) {
        $('.select2').attr('disabled', true)
        $('#dokter_new_multiple').prop('disabled', true)
        $('#btn-hapus-klaim').attr('disabled', true)
        $('#klaiminacbgranapform-berat_lahir').attr('disabled', true)
        $('#nama_pasien').attr('disabled', true)
        $('#no_rekam_medik').attr('disabled', true)
        $('#no_sep').attr('disabled', true)
    } else if (_delete == false && _proses == true) {
        $('#btn-hapus-klaim').attr('disabled', false)
        $('.btn-remove-diagnosa').attr('disabled', true)
        $('#edit-koreksi').attr('disabled', true)
    } else if (_delete == true && _proses == false) {
        $('#btn-hapus-klaim').attr('disabled', true)
        $('.btn-remove-diagnosa').attr('disabled', false)
        $('#edit-koreksi').attr('disabled', false)
    }
    hideAddRanap();
    getGrouper();
    // loadDiagnosa();
    let los = date_diff_indays($('#klaiminacbgranapform-tgl_masuk').val(), $('#klaiminacbgranapform-tgl_keluar').val())

    $("#adl_subacute").attr("disabled", true); 
    $("#adl_cronic").attr("disabled", true); 

    $('#adl_subacute').on('input', function () {
        var value = $(this).val();
        if ((value !== '') && (value.indexOf('.') === -1)) {
            $(this).val(Math.max(Math.min(value, MAX_ACCUTE), 0));
        }
    });

    $('#adl_cronic').on('input', function () {
        var value = $(this).val();
        if ((value !== '') && (value.indexOf('.') === -1)) {
            $(this).val(Math.max(Math.min(value, MAX_CRONIC), 0));
        }
    });

    if (los >= MIN_CRONIC) {
        subAccute.empty().append(ACCUTE)
        $("#adl_subacute").attr("disabled", false);
        $('#adl_subacute').val(ACCUTE)
    }
    if (los >= MIN_CRONIC) {
        cronic.empty().append(CRONIC)
        $("#adl_cronic").attr("disabled", false);
        $('#adl_cronic').val(CRONIC)
    }
    $('#los').empty().append(': ' + los)
    $('.select-diagnosa-10').select2({
        placeholder: '-',
        minimumInputLength: 3,
        ajax: {
            url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-diagnosa?type=10',
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    q: term,
                    page: page
                }
            },
            processResults: function (data) {
                _diagnosa10 = data.rawData;
                return {
                    results: data.result
                };
            }
        },
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) {
            return m;
        },
    }).on('select2:select', function (event) {
        var val = $(this).val();
        if (val) {
            let pendaftaranId = $('.pendaftaran-id-txt').val()
            var _target = $('.' + $(this).attr('data-target'));
            var _type = $(this).attr('data-type');
            var _append = '<tr><th style="width: 60%; border-right: 0px;">';
            var _state = true;
            var value = '';
            let p = 0;

            _value = _diagnosa10[val];

            if (cekDuplicate(_value.diagnosa_kode) == false) {
                _state = false;
                docoNotification('warning', 'Peringatan', 'Diagnosa Sudah Pernah Di Inputkan!');
            }

            if (_state) {
                deleteGrouper()
                _append += _value.diagnosa_nama;
                _append += '</th>';

                _append += '<th class="dig-aksi"style="border-left: 0px; border-right: 0px;">'
                _append += '<button id="set-' + _value.diagnosa_id + '" style="float:right; margin-top: -3px;" data-target="' + $(this).attr('data-target') + '" data-key="d' + p + '" dig-id="' + _value.diagnosa_id + '" dig-kode="' + _value.diagnosa_kode + '" class="btn btn-warning btn-md set-primer font-13">Set Primer</button></th>';
                _append += '<th class="dig-info" style="width: 225px; border-left: 0px">';
                _append += '<button id="del-' + _value.diagnosa_id + '" style="float:right; margin-top: -3px;" data-target="' + $(this).attr('data-target') + '" data-key="' + i + '" class="btn btn-danger btn-lg btn-remove-diagnosa hide-me" type="10" dig-id="' + _value.diagnosa_id + '" dig-kode="' + _value.diagnosa_kode + '" style="float:right"><i class="fa fa-trash"></i></button>';
                _append += '<span class="badge badge-primary font-14" style="float:left; margin-right: 30px;">' + _value.diagnosa_kode + '</span>';

                _append += '</th></tr>';
                _target.find('tbody').append(_append)
                $(this).closest('tr').find('select').val('').trigger('change')
                var _newArr = {
                    diagnosa_id: _value.diagnosa_id,
                    nama_diagnosa: _value.diagnosa_nama,
                    kode_diagnosa: _value.diagnosa_kode,
                    diagnosa_type: _type
                };
                _klaimDetail[i] = _newArr;
                i++;
                p++;

                $().docoForm('click', {
                    skipConfirm: true,
                    data: {
                        id: pendaftaranId,
                        diagnosa_id: _value.diagnosa_id,
                        nama_diagnosa: _value.diagnosa_nama,
                        kode_diagnosa: _value.diagnosa_kode,
                        type_diagnosa: _type
                    },
                    url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/add-diagnosa-tambahan',
                    success: function (data) {
                        return true
                    }
                });
            }
        }
    });

    $('.select-diagnosa-9').select2({
        placeholder: '-',
        minimumInputLength: 3,
        ajax: {
            url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-diagnosa?type=9',
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    q: term,
                    page: page
                }
            },
            processResults: function (data) {
                $.each(data.rawData, function (i, k) {
                    _diagnosa9[i] = k;
                })
                return {
                    results: data.result
                };
            }
        },
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) {
            return m;
        },
    }).on('select2:select', function (event) {
        var val = $(this).val();
        if (val) {
            let pendaftaranId = $('.pendaftaran-id-txt').val()
            var _target = $('.' + $(this).attr('data-target'));
            var _type = $(this).attr('data-type');
            var _append = '<tr><th style="width: 60%; border-right: 0px;">';
            var _state = true;
            var value = '';
            let p = 0;

            _value = _diagnosa9[val];

            if (cekDuplicate(_value.diagnosa_kode) == false) {
                _state = false;
                docoNotification('warning', 'Peringatan', 'Diagnosa Sudah Pernah Di Inputkan!');
            }

            if (_state) {
                deleteGrouper()
                _append += _value.diagnosa_nama;
                _append += '</th>';

                _append += '<th style="border-left: 0px; border-right: 0px;">'
                _append += '<button style="float:right; margin-top: -3px;" data-target="' + $(this).attr('data-target') + '" data-key="d' + p + '" dig-id="' + _value.diagnosa_id + '" dig-kode="' + _value.diagnosa_kode + '" class="btn btn-warning btn-md hidden">Set Primer</button></th>';
                _append += '<th class="dig-info" width: 225px; style="border-left: 0px">';
                _append += '<button id="del-' + _value.diagnosa_id + '" style="float:right; margin-top: -3px;" data-target="' + $(this).attr('data-target') + '" data-key="' + i + '" class="btn btn-danger btn-lg btn-remove-diagnosa hide-me"  type="10" dig-id="' + _value.diagnosa_id + '" dig-kode="' + _value.diagnosa_kode + '" style="float:right"><i class="fa fa-trash"></i></button>';
                _append += '<span class="badge badge-primary font-14" style="float:right; margin-right: 30px">' + _value.diagnosa_kode + '</span>';

                _append += '</th></tr>';
                _target.find('tbody').append(_append)
                $(this).closest('tr').find('select').val('').trigger('change')
                var _newArr = {
                    diagnosa_id: _value.diagnosa_id,
                    nama_diagnosa: _value.diagnosa_nama,
                    kode_diagnosa: _value.diagnosa_kode,
                    diagnosa_type: _type
                };
                _klaimDetail[i] = _newArr;
                i++;
                p++;

                $().docoForm('click', {
                    skipConfirm: true,
                    data: {
                        id: pendaftaranId,
                        diagnosa_id: _value.diagnosa_id,
                        nama_diagnosa: _value.diagnosa_nama,
                        kode_diagnosa: _value.diagnosa_kode,
                        type_diagnosa: _type
                    },
                    url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/add-diagnosa-tambahan',
                    success: function (data) {
                        $('.empty-icd9').hide();
                        return true
                    }
                });
            }
        }
    });
    $('.select-dokter').select2({
        placeholder: '-',
        minimumInputLength: 3,
        ajax: {
            url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-dokter',
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    q: term,
                    page: page
                }
            },
            processResults: function (data) {
                return {
                    results: data.result
                };
            }
        },
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) {
            return m;
        },
    }).on('select2:select', function(e){
        var val = $(this).val();
        var nameDokter = e.params.data?.text
        $('#klaiminacbgranapform-dokterdpjp_id').val(val)
        $('#klaiminacbgranapform-nama_dokter').val(nameDokter)
    });
    $('#edit-koreksi').click(function(e) {
        $('.modal-content').empty()
    })
    $('#reset-grouping').click(function(e) {
        if(is_prosesklaim == '1'){
            docoNotification('warning','Peringatan','Sudah Proses Klaim')
        }else{
            $(this).docoForm('click', {
                skipConfirm:true,
                skipSuccessNotif:true,
                url: $(this).data('url'),
                success:function(data){
                    resetTarifGrouping(data);
                }
            });
        }
    });
})

spesialProsedur.change(function () {
    let data = $(this).attr('id')
    let arr = []
    let itemChange, itemCode, itemVal, grouperName, arrPos, specCode, type
    let stage = 2;

    if(statusKunjungan == statusFinalKlaim) {
        return false;
    }
    switch (data) {
        case 'drug-combo':
            itemChange = arrSpecDrug.find(({
                code
            }) => code === ddSpecDrug.val())
            specCode = itemChange.code
            itemCode = $('#drug-kode')
            itemVal = $('#drug-val')
            dataVal = 'value-drug'
            grouperName = 'Grouper[spesial_drug]'
            arrPos = 5
            type = SPECIAL_DRUG
            break;
        case 'inv-combo':
            itemChange = arrSpecInv.find(({
                code
            }) => code === ddSpecInv.val())
            specCode = itemChange.code
            itemCode = $('#inv-kode')
            itemVal = $('#inv-val')
            dataVal = 'value-inv'
            grouperName = 'Grouper[spesial_investigation]'
            arrPos = 6
            type = SPECIAL_INVESTIGATION
            break;
        case 'sproc-combo':
            itemChange = arrSpecProc.find(({
                code
            }) => code === ddSpecProc.val())
            specCode = itemChange.code
            itemCode = $('#sproc-kode')
            itemVal = $('#sproc-val')
            dataVal = 'value-sproc'
            grouperName = 'Grouper[spesial_procedure]'
            arrPos = 7
            type = SPECIAL_PROCEDURE
            break;
        case 'spros-combo':
            itemChange = arrSpecPros.find(({
                code
            }) => code === ddSpecPros.val())
            specCode = itemChange.code
            itemCode = $('#spros-kode')
            itemVal = $('#spros-val')
            dataVal = 'value-spros'
            grouperName = 'Grouper[spesial_prosthesis]'
            arrPos = 8
            type = SPECIAL_PROSTHESIS
            break;
    }

    // kodeDiagnosa.push(specCode)
    // kode spesial cmg 4 digit tanpa '-'
    specCode4 = specCode
    specCode4 = specCode4.replace('-', '');
    if (arrPos == 5) {
        kodeDiagnosa[0] = specCode4
    } else if (arrPos == 6) {
        kodeDiagnosa[1] = specCode4
    } else if (arrPos == 7) {
        kodeDiagnosa[2] = specCode4
    } else if (arrPos == 8) {
        kodeDiagnosa[3] = specCode4
    }

    _addDetail[12] = {
        name: 'Add[diagnosa_kode]',
        value: kodeDiagnosa
    }

    $.ajax({
        type: 'POST',
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/grouper',
        data: {
            kodeDiagnosa,
            stage,
            nosep
        },
        dataType: 'JSON',
        async: true,
        success: function (res) {
            let total = parseInt(res.response_inacbg?.base_tariff)
            var resSelected = []
            let resOption
            let code, desc = ''
            let tarif = 0
            let finalTotal = 0

            if (typeof res.response_inacbg.special_cmg != 'undefined') {
                tmpRes = res.response_inacbg.special_cmg
                if (res.response_inacbg.special_cmg.length > 1) {
                    resSelected = [{
                        code: "-",
                        description: "",
                        tariff: 0,
                        type: '-'
                    }]
                    for (let i = 0; i < tmpRes.length; i++) {
                        resSelected.push({
                            code: tmpRes[i].code,
                            description: tmpRes[i].description,
                            tariff: tmpRes[i].tariff,
                            type: tmpRes[i].type
                        })
                    }
                } else {
                    resSelected.push({
                        code: "-",
                        description: "",
                        tariff: 0,
                        type: '-'
                    }, {
                        code: res.response_inacbg.special_cmg[0].code,
                        description: res.response_inacbg.special_cmg[0].description,
                        tariff: res.response_inacbg.special_cmg[0].tariff,
                        type: res.response_inacbg.special_cmg[0].type
                    })
                }
                resOption = res.special_cmg_option
            } else {
                resSelected = {
                    code: "-",
                    description: "",
                    tariff: 0
                }
                resOption = res.special_cmg_option
            }
            if (resSelected.length >= 1) {
                resSelected.map((value) => {
                    if (value.type == type) {
                        tarif = parseInt(value.tariff)
                        code = value.code
                        desc = value.description
                    }
                })
            } else {
                tarif = parseInt(resSelected.tariff)
                code = resSelected.code
                desc = resSelected.description
            }

            itemCode.empty().append(code)
            itemVal.empty().append('<b>Rp. </b> ' + addCommas(tarif))
            itemVal.data(dataVal, tarif)

            if (arrPos == 5) {
                tarifSpecial[0] = itemVal.data(dataVal)
                _addDetail[13] = {
                    name: 'Add[drug_code]',
                    value: code
                }
                _addDetail[14] = {
                    name: 'Add[drug_name]',
                    value: desc
                }
            } else if (arrPos == 6) {
                tarifSpecial[1] = itemVal.data(dataVal)
                _addDetail[9] = {
                    name: 'Add[inv_code]',
                    value: code
                }
                _addDetail[10] = {
                    name: 'Add[inv_name]',
                    value: desc
                }
            } else if (arrPos == 7) {
                tarifSpecial[2] = itemVal.data(dataVal)
                _addDetail[7] = {
                    name: 'Add[proc_code]',
                    value: code
                }
                _addDetail[8] = {
                    name: 'Add[proc_name]',
                    value: desc
                }
            } else if (arrPos == 8) {
                tarifSpecial[3] = itemVal.data(dataVal)
                _addDetail[5] = {
                    name: 'Add[pros_code]',
                    value: code
                }
                _addDetail[6] = {
                    name: 'Add[pros_name]',
                    value: desc
                }
            }
            
            tarifSpecial[4] = total
            finalTotal = summer(tarifSpecial)

            _grouper[0] = {
                name: 'Grouper[total]',
                value: finalTotal
            }
            _grouper[arrPos] = {
                name: grouperName,
                value: tarif
            }

            totalHarga.empty().append('<b>Rp. </b> ' + addCommas(finalTotal));
        }
    })
})


$('#klaiminacbgranapform-is_naikkelas').on('click', function () {
    // deleteGrouper()
    if ($(this).is(':checked')) {
        $('.hide-naik-kelas').removeClass('hidden');
        var _valuekelas = $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]:checked').val();
        if (_valuekelas == '2') {
            $('.radio-kelas_2').addClass('hidden');
            $('.radiocheck-kelas_1').prop('checked', true)
        } else if (_valuekelas == '1') {
            $('.radio-kelas_2').addClass('hidden');
            $('.radio-kelas_1').addClass('hidden');
            $('.radiocheck-vip').prop('checked', true)
        } else if (_valuekelas == '3') {
            $('.radio-kelas_2').removeClass('hidden');
            $('.radio-kelas_1').removeClass('hidden');
            $('#naikkelas-3').prop('checked', true)
        }
        $('#klaiminacbgranapform-jenis_kelasrawat').trigger("change")
    } else {
        $('input[name="KlaimInacbgRanapForm[naik_kelas]"]').prop('checked', false)
        $('.hide-naik-kelas').addClass('hidden');
    }
})


var hidePilKelas = function (_valuekelas, state = true) {
    if (_valuekelas == '2') {
        $('.radio-kelas_2').addClass('hidden');
        if ($('.radio-kelas_1').hasClass('hidden')) {
            $('.radio-kelas_1').removeClass('hidden');
        }
        if (state) {
            $('.radiocheck-kelas_1').prop('checked', true)
        } else {
            var _kelas = $('#set-kelas').val();
            $('.radiocheck-' + _kelas).prop('checked', true)
        }

    } else if (_valuekelas == '1') {
        $('.radio-kelas_2').addClass('hidden');
        $('.radio-kelas_1').addClass('hidden');
        if (state) {
            $('.radiocheck-vip').prop('checked', true)
        } else {
            var _kelas = $('#set-kelas').val();
            $('.radiocheck-' + _kelas).prop('checked', true)
        }
    } else if (_valuekelas == '3') {
        $('.radio-kelas_2').removeClass('hidden');
        $('.radio-kelas_1').removeClass('hidden');
        if (state) {
            $('.radiocheck-kelas_2').prop('checked', true)
        } else {
            var _kelas = $('#set-kelas').val();
            $('.radiocheck-' + _kelas).prop('checked', true)
        }
    }
}

$('#klaiminacbgranapform-is_rawatintensif').on('click', function () {
    // deleteGrouper()
    if ($(this).is(':checked')) {
        $('.hide-rawat-intensif').removeClass('hidden');
    } else {
        $('.hide-rawat-intensif').addClass('hidden');
    }
})

$('#klaiminacbgranapform-ventilator').on('click', function () {
    if ($(this).is(':checked')) {
        $('#date-ventilator').removeClass('hidden');
    } else {
        $('#date-ventilator').addClass('hidden');
    }
})


$(document).on('click', '#btn-proses', function (e) {
    e.preventDefault()
    var _data = $('#form-proses-klaim').serializeArray();
    var _detail = [];
    var _diagnosaDeleted = [];
    var z = 0;
    var p = 0;
    let finalProc = 0;
    let finalPros = 0;
    let finalInv = 0;
    let finalDrug = 0;
    let lama_rawat = date_diff_indays($('#klaiminacbgranapform-tgl_masuk').val(), $('#klaiminacbgranapform-tgl_keluar').val())
    let jaminan = $('#klaiminacbgranapform-klaim_penjamin').val()
    var jenis_kelasrawat = document.getElementById("klaiminacbgranapform-jenis_kelasrawat").checked;
    var is_naikkelas = document.getElementById("klaiminacbgranapform-is_naikkelas").checked; 
    var pasiensitb = document.getElementById("klaiminacbgranapform-pasien_tb").checked; 
    var validasiPasientb = $('#klaiminacbgranapform-is_pasiensitb').val()
    var jenis_kelasrawat_nama = (jenis_kelasrawat == true) ? 'Eksekutif' : 'Reguler';
    $.each(_klaimDetail, function (k, v) {
        if (typeof _klaimDetail[k] !== 'undefined') {
            $.each(_klaimDetail[k], function (key, val) {
                _detail[p] = {
                    name: 'KlaimInacbgDetail[' + z + '][' + key + ']',
                    value: val
                };
                p++;
            })
        }
        z++;
    })

    $.each(_deletedDiagnosa, function (k, v) {
        if (typeof _deletedDiagnosa[k] !== 'undefined') {
            $.each(_deletedDiagnosa[k], function (key, val) {
                _diagnosaDeleted[p] = {
                    name: 'DiagnosaDeleted[' + z + '][' + key + ']',
                    value: val
                };
                p++;
            })
        }
        z++;
    })
    

    if(arrayJaminan.includes(jaminan)) {
        if($('#identitas_value').val() == '' ) {
            (new PNotify({
                title: "Perhatian",
                text: "No identitas pasien tidak boleh kosong",
                addclass: "alert alert-danger alert-arrow-right alert-styled-right",
                type: "error",
                buttons: {
                    closer: true,
                    sticker: true
                },
                hide: true,
                history: {
                    history: false
                }
            }));
            return Error('No identitas pasien tidak boleh kosong');
        }
    }

    if(is_naikkelas == true) {
        var lamaRawat = $('#klaiminacbgranapform-lama_rawatkelas').val()
        if(lamaRawat == 0) {
            (new PNotify({
                title: "Perhatian",
                text: "Lama (Hari) Tidak boleh kosong!",
                addclass: "alert alert-danger alert-arrow-right alert-styled-right",
                type: "error",
                buttons: {
                    closer: true,
                    sticker: true
                },
                hide: true,
                history: {
                    history: false
                }
            }));
            return Error('Lama (Hari) Tidak boleh kosong!');
        }   
    }

    if(pasiensitb == true) {
        if(validasiPasientb != 'validate') {
            (new PNotify({
                title: "Perhatian",
                text: "Pasien SITB belum tervalidasi!",
                addclass: "alert alert-danger alert-arrow-right alert-styled-right",
                type: "error",
                buttons: {
                    closer: true,
                    sticker: true
                },
                hide: true,
                history: {
                    history: false
                }
            }));
            return Error('Pasien SITB belum tervalidasi!');
        }   
    }
    

    let _noKlaimCovid = 
    [
        {
            name: "KlaimInacbgRanapForm[no_klaimcovid]",
            value : $('#no_klaimcovid').val()
        },
        {
            name: "klaiminacbgranapform[jenis_kelasrawat]",
            value : $('input[name="klaiminacbgranapform[jenis_kelasrawat]"]:checked').val()
        }
    ]
    
    $.merge(_data, _diagnosaDeleted);
    $.merge(_data, _detail);
    $.merge(_data, _noKlaimCovid)
    
    _data[_data.length + 1] = {
        name: 'primer',
        value: isPrimer
    }
    let arrData = cleanArr(_data)
    $().docoForm('click', {
        skipConfirm: true,
        data: arrData,
        url: $('#form-proses-klaim').attr('action'),
        success: function (data) {
            console.log(data);
            if (typeof data.response != 'undefined') {
                setTimeout(() => {
                    location.reload()
                }, 500); 
            }
            
            $(".has-error").removeClass('has-error');
            $(".help-block").hide();
        },
        error: function(error) {
            let errorIncbgs = error?.responseJSON?.response?.title?.title
            let errorText = error?.responseJSON?.response?.title?.text
            if(errorIncbgs != undefined && errorIncbgs.length != 0) {
                (new PNotify({
                    title: "Perhatian",
                    text: errorText,
                    addclass: "alert alert-danger alert-arrow-right alert-styled-right",
                    type: "error",
                    buttons: {
                        closer: true,
                        sticker: true
                    },
                    hide: true,
                    history: {
                        history: false
                    }
                }));
                setTimeout(() => {
                    location.reload()
                }, 1300);   
                return Error(`${errorText}`);
            }
        }
    });
})
$(document).on('click', '.btn-final-klaim', function (e) {
    e.preventDefault()
    var _data = $('#form-proses-klaim').serializeArray();
    var _detail = [];
    var z = 0;
    var p = 0;
    var ns = [];

    let jaminan = $('#klaiminacbgranapform-klaim_penjamin').val()

    if(arrayJaminan.includes(jaminan)) {
        var ns = [{
            name: 'nosep',
            value: $('#no_klaimcovid').val()
        }];
    }else{
        var ns = [{
            name: 'nosep',
            value: $('.no-sep').val()
        }];
    }
    
    var add_payment_pct = $("#persen-vip").val();
    let finalInfo = $('.info-txt').html()

    _addDetail[3] = {
        name: 'Add[info]',
        value: finalInfo
    }
    $.each(_klaimDetail, function (k, v) {
        if (typeof _klaimDetail[k] !== 'undefined') {
            $.each(_klaimDetail[k], function (key, val) {
                _detail[p] = {
                    name: 'KlaimInacbgDetail[' + z + '][' + key + ']',
                    value: val
                };
                p++;
            })
        }
        z++;
    })


    let _noKlaimCovid = [{
        name: "klaiminacbgranapform[no_klaimcovid]",
        value : $('#no_klaimcovid').val()
    }]

    let is_naikkelas = 0;
    if($("#klaiminacbgranapform-is_naikkelas:checked").val() != undefined) {
        is_naikkelas = 1;
    }

    let _isNaikKelas = [{
        name: 'is_naik_kelas',
        value: is_naikkelas
    }, {
        name: 'naik_kelas',
        value: $("input[name='KlaimInacbgRanapForm[naik_kelas]']:checked").val()
    }]

    let _pembayarSelisihBiaya = [{
        name: 'KlaimInacbgRanapForm[pembayar_selisih_biaya]',
        value: $("input[name='KlaimInacbgRanapForm[pembayar_selisih_biaya]']:checked").val()
    }]

    let urlParams = new URLSearchParams(window.location.search);
    let urlId = urlParams.get('id');
    let urlAdmisi = urlParams.get('admisi');

    let _urlId = [{
        name: 'id',
        value: urlId
    }];

    let _urlAdmisi = [{
        name: 'admisi',
        value: urlAdmisi
    }];
    
    let _transfusiDarah = [{
        name: 'KlaimInacbgRanapForm[transfusi_darah]',
        value: $("input[name='KlaimInacbgRanapForm[transfusi_darah]']").val()
    }];

    let _dializer = [{
        name: 'KlaimInacbgRanapForm[dializer]',
        value: $("input[name='KlaimInacbgRanapForm[dializer]']:checked").val()
    }];

    let _jenisRawat = [{
        name: 'jenis',
        value: $("input[name='KlaimInacbgRanapForm[jenis]']:checked").val()
    }]

    let _kelasRawat = [{
        name: 'kelas_rawat',
        value: $("input[name='KlaimInacbgRanapForm[jenis_kelasrawat]']:checked").val()
    }]

    let _tglRawat = [{
        name: 'KlaimInacbgRanapForm[tgl_masuk]',
        value: $("input[name='KlaimInacbgRanapForm[tgl_masuk]']").val()
    }, {
        name: 'KlaimInacbgRanapForm[tgl_keluar]',
        value: $("input[name='KlaimInacbgRanapForm[tgl_keluar]']").val()
    }]

    $.merge(_data, _detail);
    $.merge(_data, ns);
    $.merge(_data, _grouper);
    $.merge(_data, _addDetail);
    $.merge(_data, _noKlaimCovid);
    $.merge(_data, _isNaikKelas);
    $.merge(_data, _urlId);
    $.merge(_data, _urlAdmisi);
    $.merge(_data, _pembayarSelisihBiaya);
    $.merge(_data, _dializer);
    $.merge(_data, _transfusiDarah);
    $.merge(_data, _jenisRawat);
    $.merge(_data, _kelasRawat);
    $.merge(_data, _tglRawat);
    $.merge(_data, kunjunganId);

    if (typeof add_payment_pct !== 'undefined') {
        $.merge(_data, add_payment_pct);
    }

    $().docoForm('click', {
        skipConfirm: true,
        data: _data,
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/final-klaim',
        success: function (data) {
            $('.group-tarif').attr('disabled', true)
            $('.select2').attr('disabled', true)
            $('#dokter_new_multiple').prop('disabled', true)
            $('#klaiminacbgranapform-tgl_masuk').prop('disabled', true)
            $('#klaiminacbgranapform-tgl_keluar').prop('disabled', true)
            $('#btn-hapus-klaim').attr('disabled', true)
            $('#nama_pasien').attr('disabled', true)
            $('#no_rekam_medik').attr('disabled', true)
            $('#no_sep').attr('disabled', true)
            $('#klaiminacbgranapform-berat_lahir').attr('disabled', true)
            spesialProsedur.attr('disabled', true)
            $('#judul-grouper').empty().html('Hasil Grouper - Final')
            $('.btn-final-klaim').addClass('hidden');
            $('.hide-me').addClass('hidden');
            $('#btn-proses').attr('disabled', true)
            $('.btn-remove-diagnosa').attr('disabled', true)
            $('.klaiminacbgranapform-dializer').attr('disabled', true)
            $('#klaiminacbgranapform-transfusi_darah').attr('disabled', true)
            $('#edit-koreksi').attr('disabled', true)
            $('#btn-hapus-klaim').attr('disabled', true)
            if ($('.btn-cetak-klaim').hasClass('hidden')) {
                $('.btn-cetak-klaim').removeClass('hidden')
            }
            if ($('.btn-edit-klaim').hasClass('hidden')) {
                $('.btn-edit-klaim').removeClass('hidden')
            }
            if ($('.btn-kirim-klaim').hasClass('hidden')) {
                $('.btn-kirim-klaim').removeClass('hidden')
            }
            if ($('#persen-vip').length) {
                $('#persen-vip').attr('readonly', true);
            }
            $('input[name="klaiminacbgranapform[jenis_kelasrawat]"]').prop('disabled', true)
            if ($('input[name="klaiminacbgranapform[is_naikkelas]"]').is(':checked') == true) {
                $('input[name="klaiminacbgranapform[is_naikkelas]"]').prop('disabled', true);
                $('input[name="klaiminacbgranapform[naik_kelas]"]').prop('disabled', true)
                $('#klaiminacbgranapform-lama_rawatkelas').prop('disabled', true)
            } else {
                $('input[name="klaiminacbgranapform[is_naikkelas]"]').prop('disabled', true);
            }
            if ($('input[name="klaiminacbgranapform[is_rawatintensif]"]').is(':checked') == true) {
                $('input[name="klaiminacbgranapform[is_rawatintensif]"]').prop('disabled', true)
                $('#klaiminacbgranapform-lama_rawatintensif').prop('disabled', true);
                $('#klaiminacbgranapform-ventilator').prop('disabled', true);
            } else {
                $('input[name="klaiminacbgranapform[is_rawatintensif]"]').prop('disabled', true)
            }

            var kemenkes_status = 'Klaim belum terkirim ke Pusat Data Kementerian Kesehatan';
            $('.kemenkes_status').removeClass('text-success');
            $('.kemenkes_status').addClass('text-danger');
            $('.kemenkes_status').empty().append(kemenkes_status);

            $('.btn-edit-inacbgs').prop('disabled', true)
            $('#btn-edit-idrg').prop('disabled', true)
            $('#status-klaim-section').removeClass('hidden')
        },   
        error: function(error) {
            let errorIncbgs = error?.responseJSON?.response?.title?.title
            let errorText = error?.responseJSON?.response?.title?.text
            if(errorIncbgs != undefined && errorIncbgs.length != 0) {
                (new PNotify({
                    title: "Perhatian",
                    text: errorText,
                    addclass: "alert alert-danger alert-arrow-right alert-styled-right",
                    type: "error",
                    buttons: {
                        closer: true,
                        sticker: true
                    },
                    hide: true,
                    history: {
                        history: false
                    }
                }));
                setTimeout(() => {
                    location.reload()
                }, 1300);
            }
        }
    });
})
$(document).on('click', '#btn-hapus-klaim', function () {
    let jaminan = $('#klaiminacbgranapform-klaim_penjamin').val()
    let no_sep = null
    if(arrayJaminan.includes(jaminan)) {
        no_sep = $('#no_klaimcovid').val()

    }else{
        no_sep = $('.no-sep').val()
    }
    $().docoForm('click', {
        confirmMessage: 'Yakin akan menghapus data klaim?',
        data: {
            nosep: no_sep,
            pendaftaranid: $('.pendaftaran-id-txt').val(),
            kunjungan_id: kunjunganId
        },
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/hapus-klaim',
        success: function (data) {
            // _delete = false;
            $('#proses-final-klaim').addClass('hidden');
            $('#btn-proses').prop('disabled', false)
            $('.btn-remove-diagnosa').attr('disabled', false)
            $('#edit-koreksi').attr('disabled', false)
            $('#btn-hapus-klaim').prop('disabled', true)
            $('.btn-final-klaim').removeClass('hidden');
            cleaning()
            setTimeout(() => {
                location.reload()
            }, 1000)
        },
        error: function(error) {
            let errorIncbgs = error?.responseJSON?.response?.title?.title
            let errorText = error?.responseJSON?.response?.title?.text
            if(errorIncbgs != undefined && errorIncbgs.length != 0) {
                (new PNotify({
                    title: "Perhatian",
                    text: errorText,
                    addclass: "alert alert-danger alert-arrow-right alert-styled-right",
                    type: "error",
                    buttons: {
                        closer: true,
                        sticker: true
                    },
                    hide: true,
                    history: {
                        history: false
                    }
                }));
                setTimeout(() => {
                    location.reload()
                }, 1300);
            }
        }
    });
})
$(document).on('click', '.btn-edit-klaim', function () {
    let jaminan = $('#klaiminacbgranapform-klaim_penjamin').val()
    let no_sep = null
    if(arrayJaminan.includes(jaminan)) {
        no_sep = $('#no_klaimcovid').val()
    }else{
        no_sep = $('.no-sep').val()
    }
    
    $().docoForm('click', {
        skipConfirm: true,
        // confirmMessage: 'Yakin akan melakukan edit ulang klaim pada data ini?',
        data: {
            nosep: no_sep,
            pendaftaranid: $('.pendaftaran-id-txt').val(),
            admisi: $('#klaiminacbgranapform-pasienadmisi_id').val(),
            kunjungan_id: kunjunganId
        },
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/edit-ulang-klaim',
        success: function (data) {
            _delete = true;
            $('#btn-proses').prop('disabled', true)
            $('.btn-remove-diagnosa').attr('disabled', true)
            $('#edit-koreksi').attr('disabled', true)
            $('#btn-hapus-klaim').prop('disabled', false)
            $('.btn-final-klaim').removeClass('hidden');
            $('.btn-cetak-klaim').addClass('hidden')
            $('.btn-edit-klaim').addClass('hidden')
            $('.btn-kirim-klaim').addClass('hidden')
            $('.hide-me').removeClass('hidden')
            if ($('#persen-vip').length) {
                $('#persen-vip').attr('readonly', false);
            }

            $('input[name="klaiminacbgranapform[jenis_kelasrawat]"]').prop('disabled', false)
            if ($('input[name="klaiminacbgranapform[is_naikkelas]"]').is(':checked') == true) {
                $('input[name="klaiminacbgranapform[is_naikkelas]"]').prop('disabled', false);
                $('input[name="klaiminacbgranapform[naik_kelas]"]').prop('disabled', false)
                $('#klaiminacbgranapform-lama_rawatkelas').prop('disabled', false)
            } else {
                $('input[name="klaiminacbgranapform[is_naikkelas]"]').prop('disabled', false);
            }
            if ($('input[name="klaiminacbgranapform[is_rawatintensif]"]').is(':checked') == true) {
                $('input[name="klaiminacbgranapform[is_rawatintensif]"]').prop('disabled', false)
                $('#klaiminacbgranapform-lama_rawatintensif').prop('disabled', false);
                $('#klaiminacbgranapform-ventilator').prop('disabled', false);
            } else {
                $('input[name="klaiminacbgranapform[is_rawatintensif]"]').prop('disabled', false)
            }
            
            getGrouper();
        },
        error: function(error) {
            let errorIncbgs = error?.responseJSON?.response?.title?.title
            let errorText = error?.responseJSON?.response?.title?.text
            if(errorIncbgs != undefined && errorIncbgs.length != 0) {
                (new PNotify({
                    title: "Perhatian",
                    text: errorText,
                    addclass: "alert alert-danger alert-arrow-right alert-styled-right",
                    type: "error",
                    buttons: {
                        closer: true,
                        sticker: true
                    },
                    hide: true,
                    history: {
                        history: false
                    }
                }));
                setTimeout(() => {
                    location.reload()
                }, 1300);
            }
        }
    });
})

$(document).on('click', '.btn-cetak-klaim', function () {
    let jaminan = $('#klaiminacbgranapform-klaim_penjamin').val()
    let no_sep = null;
    if(arrayJaminan.includes(jaminan)) {
        no_sep = $('#no_klaimcovid').val()

    }else{
        no_sep = $('.no-sep').val()
    }

    window.open('/penjamin-asuransi/informasi-pasien-ranap-bpjs/cetak-klaim?sep=' + no_sep);
})

$('input[name="klaiminacbgranapform[naik_kelas]"]').on('change', function () {
    // deleteGrouper()
})

$('.delete-on-edit').on('change', function () {
    deleteGrouper()
})

$(document).on('change', '.group-tarif', function (e) {
    e.preventDefault()
    deleteGrouper()
    let totalTarifRs = $('#klaiminacbgranapform-total_tarifrs')
    let getValue = mapperToInt($('.group-tarif'))
    let nilai = summer(getValue)
    totalTarifRs.val(addCommas(nilai))
})

function summer(array) {
    let sum = 0
    for (var i = 0; i < array.length; i++) {
        sum += array[i]
    }
    return sum
}

function mapperToInt(kelas) {
    let finder = kelas.map(function () {
        let toInt = $(this).val().replace(/[^0-9]/g, '');
        return toInt
    }).get();

    let clean = finder.map(function (v) {
        return parseFloat(v, 15);
    });

    return clean
}

function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }
    return x1 + x2;
}
/* "A Product of PT Docotel Teknologi Powered by Sirs" */
function getGrouper() {
    $.ajax({
        data: {
            no_sep: noClaim != '' ? noClaim : noSep ,
            pendaftaran_id: $('.pendaftaran-id-txt').val(),
            kunjungan_id: kunjunganId
        },
        type: 'POST',
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-klaim',
        success: function (res) {
            var _res = JSON.parse(res)
            if(_res.metadata?.error_no == "E2004") {
                updateNoKlaim()
            }
            if (typeof _res.response != 'undefined') {
                var _data = _res.response.data;
                let _option = _data.db_special_opt
                let _db_total = _data.grouper?.response_inacbg?.tariff
                let _infoKelas = infoTxt + infoKelas($('#klaiminacbgranapform-tarif').val())
                var statusInacbg = _data.grouper?.response_inacbg?.status_cd;
                var statusIdrg = _data.grouper?.response_idrg?.status_cd;
                var klaimStatus = _data.klaim_status_cd;
                var dbSpecialCmg = _data.grouper?.response_inacbg?.special_cmg
                var _kelasBaru = '';
                var _total = 0;
                var _tarifalt;
                var _kelasawal = ''; //kelas awal sebelum naik kelas
                var _biayaalt = 0; //biaya tambahan dari naik kelas
                var _biayatambahan = 0; //total biaya tambahan
                var _biayaawal = 0;
                var _tambahan = '' //string buat tambahan
                var _kelasnaik = $('input[name="KlaimInacbgRanapForm[naik_kelas]"]:checked').val();
                var is_naikkelas = $('input[name="KlaimInacbgRanapForm[is_naikkelas]"]:checked').val();
                var _isNaik = getIsNaik(_kelasnaik);
                var _isnaikkelas = getNaikKelas(_kelasnaik);
                var _kelasKlaim = getKelasKlaim(_kelasnaik);
                var _keterangaNaikKelas = getKeteranganNaikKelas(is_naikkelas, _kelasKlaim);
                var _kelasAwalBaru = _res.response.data.grouper?.response_inacbg?.kelas;
                let finalProc = 0;
                let finalPros = 0;
                let finalInv = 0;
                let finalDrug = 0;
                let jaminanSelected = _res.response.data?.payor_id
                let jenisRawatId = _res.response.data?.jenis_rawat
                let hakKelasKlaim = _res.response.data?.kelas_rawat
                let icuIndikator = _res.response.data?.icu_indikator
                let icuLos = _res.response.data?.icu_los
                let sistole = _res.response.data?.sistole
                let diastole = _res.response.data?.diastole
                let tarifPoliEksekutif = _res.response.data.tarif_poli_eks
                let inaGrouper = _res.response.data?.grouper;
                let addPaymentAmt = _res.response.data?.add_payment_amt;
                let upgradeClassPayor = _res.response.data?.upgrade_class_payor;
                let grouperInacbg = _res.response.data?.grouper?.response_inacbg;

                if (_kelasAwalBaru == 'kelas_1') {
                    _kelasBaru = 1;
                } else if (_kelasAwalBaru == 'kelas_2') {
                    _kelasBaru = 2;
                } else {
                    _kelasBaru = 3;
                }

                $("#klaiminacbgranapform-klaim_penjamin").select2().val(jaminanSelected).trigger('change');

                if (statusIdrg == 'final') {
                    $('#btn-proses').prop('disabled', true);
                    $('#btn-hapus-klaim').prop('disabled', true);
                }

                if (klaimStatus == 'normal') {
                    $('.btn-remove-diagnosa').attr('disabled', true)
                    $('#edit-koreksi').prop('disabled', true);

                    /**
                     * Status response INACBG
                     */
                    if (klaimStatus == 'normal' && statusInacbg == 'normal') {
                        $('.btn-final-klaim').addClass('hidden')
                    }

                    if (klaimStatus == 'normal' && statusInacbg == 'final') {
                        $('.btn-final-klaim').removeClass('hidden')
                    }
                } else if(klaimStatus == 'final') {
                    $('.select2').attr('disabled', true)
                    $('#dokter_new_multiple').prop('disabled', true)
                    $('#btn-proses').prop('disabled', true);
                    $('.btn-remove-diagnosa').attr('disabled', true)
                    $('#edit-koreksi').prop('disabled', true);
                    $('#btn-hapus-klaim').prop('disabled', true);
                    $('.btn-final-klaim').addClass('hidden')
                    if ($('.btn-cetak-klaim').hasClass('hidden')) {
                        $('.btn-cetak-klaim').removeClass('hidden')
                    }
                    if ($('.btn-kirim-klaim').hasClass('hidden')) {
                        $('.btn-kirim-klaim').removeClass('hidden')
                    }
                    if ($('.btn-edit-klaim').hasClass('hidden')) {
                        $('.btn-edit-klaim').removeClass('hidden')
                    }
                }

                if(jenisRawatId == jenisRawatIgd) {
                    jenisRawat = instalasi_igd + ' Kelas ' + _kelasBaru + ' ( ' + lama_rawat + ' Hari)';
                }

                if(jenisRawatId == jenisRawatRajal) {
                    if(tarifPoliEksekutif > 0) {
                        jenisRawat = instalasi_rajal + ' Eksekutif'

                    } else {
                        jenisRawat = instalasi_rajal + ' Regular'
                    }
                    jenisRawat = instalasi_rajal + ' Kelas ' + _kelasBaru + ' ( ' + lama_rawat + ' Hari)';
                }

                if(jenisRawatId == jenisRawatRanap) {
                    jenisRawat = instalasi_nama + ' Kelas ' + _kelasBaru + ' ( ' + lama_rawat + ' Hari)';
                }

                if(is_naikkelas && addPaymentAmt > 0) {
                    var _kelasnaik = $('input[name="KlaimInacbgRanapForm[naik_kelas]"]:checked').val(); //kelas setelah naik kelas
                    _tarifalt = _res.response.data.grouper.tarif_alt;
                    _kelasawal = _res.response.data.grouper.response_inacbg.kelas;
                    _kelasnaik2 = 'kelas_' + _kelasnaik;
                    $.each(_tarifalt, function (k, v) {
                        var kelas = v.kelas;
                        if (kelas == _kelasnaik2) {
                            _biayaalt = (v.tarif_inacbg != null) ? v.tarif_inacbg : 0;
                        }
                        if (kelas == _kelasawal) {
                            _biayaawal = (v.tarif_inacbg != null) ? v.tarif_inacbg : 0;
                        }
                        if (_kelasnaik2 == 'kelas_4' && kelas == 'kelas_1') {
                            _biayaalt = (v.tarif_inacbg != null) ? v.tarif_inacbg : 0;
                        }
                        if (_kelasnaik2 == 'kelas_5' && kelas == 'kelas_1') {
                            _biayaalt = (v.tarif_inacbg != null) ? v.tarif_inacbg : 0;
                        }
                    })

                    if (_kelasnaik2 == 'kelas_4' || _kelasnaik2 == 'kelas_5') {
                        _biayatambahan = tambahanBiaya;
                    } else {
                        _biayatambahan = parseInt(_biayaalt) - parseInt(_biayaawal);
                    }

                    if (_kelasnaik2 == 'kelas_4' || _kelasnaik2 == 'kelas_5') {
                        _tambahan = '<span style="float:left;margin-top:10px;padding-right:3px">Rp. ' + addCommas(_biayaalt) + ' - ' + ' Rp. ' + addCommas(_biayaawal) + '</span>';
                        _tambahan += " <span style='float: left;margin-top:10px;padding-right:3px'> + ( Rp. " + addCommas(_biayaalt) + " x </span>"
                        _tambahan += ' <div style="float:left" class="input-group"><input type="number" id="persen-vip" class="form-control" style="width: 70px;" min="1" max="75" step="0.5" name="klaiminacbgranapform[add_payment_pct]"><span class="input-group-addon" id="basic-addon1">%</span></div><b style="float:left;margin-left: 3px; margin-top:9px"> )</b>'
                    } else {
                        _tambahan = '<span style="margin-top:2px;">Rp. ' + addCommas(_biayaalt) + ' - ' + ' Rp. ' + addCommas(_biayaawal) + '</span>';
                    }
                    $('.tbl-tambahan-biaya').removeClass('naik-turun-kelas-hide');
                    $('.tambahanbiaya-txt').empty().append('Rp. ' + addCommas(_biayatambahan))
                    $('.str-tambahan').empty().append(_tambahan)
                    $('#total-tambahan').val(_biayatambahan)
                    $('#total-naikkelas').val(_biayaalt)
                    $('#total-kelaspelayanan').val(_biayaawal)
                    $(".keterangan-naik-kelas").empty().append('<h4>Tambahan Biaya yang Dibayar Pasien ' + _keterangaNaikKelas + '</h4>')
                } else if(is_naikkelas && addPaymentAmt == 0) {
                    $('.tbl-tambahan-biaya').removeClass('naik-turun-kelas-hide');
                    $('.str-tambahan').empty().append('tidak berlaku (turun kelas)')
                    $(".keterangan-naik-kelas").empty().append('<h4>Tambahan Biaya yang Dibayar Pasien ' + _keterangaNaikKelas + '</h4>')
                }
                $("#pembayarselisih-" + upgradeClassPayor).prop('checked', true);

                var _cbg = _res.response.data.grouper?.response_inacbg?.cbg;

                // Fungsi untuk handle grouping null
                handlingGrouperNull(_res)

                handlingTindakanTarif(_res)
                
                disabledInputan(statusIdrg)
                
                defaultJenisRawat(jenisRawatId)
                
                handlingEksekutif(_res)

                handingRawatIntensif(icuIndikator, icuLos)

                handlingGetKlaimHakKelas(hakKelasKlaim)

                handlingInagrouper(inaGrouper)

                componentSwitchEnabledInacbgs(statusInacbg, klaimStatus, _cbg, statusIdrg)

                $('.status-klaim').html(statusInacbg)

                $("#klaiminacbgranapform-sistole").val(sistole)
                $("#klaiminacbgranapform-diastole").val(diastole)
                
                if(jenisRawatId == jenisRawatRajal) {
                    hiddenJenisKelasRawat(true)
                }

                var kemenkes_status = 'Klaim belum terkirim ke Pusat Data Kementerian Kesehatan';
                if(_cbg?.code == 'N-3-15-0'){   
                    disablekategoriHemodialysis(true)
                    let dializer = _data?.dializer_single_use
                    let persentase = '85%';
                    if (dializer == 1) {
                        persentase = '100%'
                    }

                    $('.persentase-darah').empty().append(persentase)
                    $('.nominal-darah').empty().append(addCommas(_res.response.data.grouper?.response_inacbg?.tariff))
                }else{    
                    disablekategoriHemodialysis(false)
                    $('.persentase-darah').empty().append('-')
                    $('.nominal-darah').empty().append('Rp.0')
                }

                let _specCmg = [];
                let _selectedCmg = [];
                if (_option != undefined) {
                    _specCmg = _option
                } else {
                    _specCmg =_res.response.data.grouper?.response?.option_special_cmg ?? _res.response.data.grouper?.response?.special_cmg
                }

                if (klaimStatus == 'final' || statusInacbg == 'final') {
                    _specCmg = inaGrouper.response_inacbg?.special_cmg
                    $('.total-harga').html('<b>Rp. </b>' + addCommas(_res.response.data.grouper?.response_inacbg?.tariff))
                }

                if (_specCmg == undefined) {
                    ddSpecProc.empty()
                    ddSpecPros.empty()
                    ddSpecInv.empty()
                    ddSpecDrug.empty()
                }

                /**
                 * Set apbila sudah pernah memilih SPECIAL CMG
                 */
                if (inaGrouper?.response_inacbg?.hasOwnProperty('special_cmg') && statusInacbg == 'normal') {
                    let dataSpesialCmg = inaGrouper?.response_inacbg?.special_cmg
                    dataSpesialCmg.map((data) => {
                        _selectedCmg[data.type] = data
                    })
                }

                if (_res.response.data.kemenkes_dc_status_cd == 'sent') {
                    kemenkes_status = 'Terkirim';
                    $('.kemenkes_status').removeClass('text-danger');
                    $('.kemenkes_status').addClass('text-success');
                }
                $('.kemenkes_status').empty().append(kemenkes_status);

                $('.info-txt').empty().append(_infoKelas);
                $('.jenisrawat-txt').empty().append(jenisRawat);
                $('.penyakit-nama').empty().append(_cbg?.description)
                $('.kode-penyakit').empty().append(_cbg?.code)
                $('.kolom-nosep').empty().append('')
                $('.harga-klaim').empty().append('<b>Rp. </b> ' + ((typeof grouperInacbg?.base_tariff !== 'undefined') ? addCommas(grouperInacbg?.base_tariff) : 0));
                $('.tambahanbiaya-txt').empty().append('<b>Rp. </b>' + addCommas(_biayatambahan))                

                $('#sproc-kode').empty().append('-')
                $('#spros-kode').empty().append('-')
                $('#sinv-kode').empty().append('-')
                $('#sdrug-kode').empty().append('-')

                _total = (typeof grouperInacbg?.base_tariff != 'undefined') ? parseInt(grouperInacbg?.base_tariff) : 0;

                if (_specCmg) {
                    _specCmg.map((value) => {
                        if (value.type == SPECIAL_PROCEDURE) {
                            arrSpecProc.push({
                                description: value.description,
                                code: value.code,
                                tariff: value.tariff ?? 0
                            })
                        }
                        if (value.type == SPECIAL_PROSTHESIS) {
                            arrSpecPros.push({
                                description: value.description,
                                code: value.code,
                                tariff: value.tariff ?? 0
                            })
                        }
                        if (value.type == SPECIAL_INVESTIGATION) {
                            arrSpecInv.push({
                                description: value.description,
                                code: value.code,
                                tariff: value.tariff ?? 0
                            })
                        }
                        if (value.type == SPECIAL_DRUG) {
                            arrSpecDrug.push({
                                description: value.description,
                                code: value.code,
                                tariff: value.tariff ?? 0
                            })
                        }
                    });

                    if (arrSpecProc.length != 0) {
                        refreshOptionSelect2(ddSpecProc, arrSpecProc, {
                            id: 'code',
                            text: 'description'
                        });
                        let itemProc = arrSpecProc.find(({
                            code
                        }) => code === ddSpecProc.val());

                        if (arrSpecProc.length > 1) {
                            if (klaimStatus == 'final' || statusInacbg == 'final' && dbSpecialCmg) {
                                itemProc = arrSpecProc.find(({
                                    code
                                }) => code !== '-')

                                ddSpecProc.val(itemProc.code).trigger("change")
                            }

                            /**
                             * Cek apabila pernah ada data selected
                             */
                            if (statusInacbg == 'normal' && _selectedCmg.hasOwnProperty(SPECIAL_PROCEDURE)) {
                                itemProc = arrSpecProc.find(({
                                    description
                                }) => {
                                    return description.toLowerCase() == _selectedCmg[SPECIAL_PROCEDURE].description.toLowerCase()
                                })

                                ddSpecProc.val(itemProc.code).trigger("change")
                            }
                        } else {
                            ddSpecProc.attr('disabled', true);
                        }
                        
                        if (itemProc) {
                            $('#sproc-kode').empty().append(itemProc.code)
                            $('#sproc-val').empty().append('<b>Rp. </b> ' + addCommas(itemProc.tariff))
                            finalProc = parseInt(itemProc.tariff)
                            if (klaimStatus == 'final' || statusInacbg == 'final') {
                                ddSpecProc.attr('disabled', true);
                            }
                        }
                    }
                    
                    if (arrSpecPros.length != 0) {
                        refreshOptionSelect2(ddSpecPros, arrSpecPros, {
                            id: 'code',
                            text: 'description'
                        });
                        let itemPros = arrSpecPros.find(({
                            code
                        }) => code === ddSpecPros.val());
                        if (arrSpecPros.length > 1) {
                            if (klaimStatus == 'final' || statusInacbg == 'final' && dbSpecialCmg) {
                                itemPros = arrSpecPros.find(({
                                    code
                                }) => code !== '-')

                                ddSpecPros.val(itemPros.code).trigger("change")
                            }

                            /**
                             * Cek apabila pernah ada data selected
                             */
                            if (statusInacbg == 'normal' && _selectedCmg.hasOwnProperty(SPECIAL_PROSTHESIS)) {
                                itemPros = arrSpecPros.find(({
                                    description
                                }) => {
                                    return description.toLowerCase() == _selectedCmg[SPECIAL_PROSTHESIS].description.toLowerCase()
                                })

                                ddSpecPros.val(itemPros.code).trigger("change")
                            }
                        } else {
                            ddSpecPros.attr('disabled', true);
                        }
                        
                        if (itemPros) {
                            $('#spros-kode').empty().append(itemPros.code)
                            $('#spros-val').empty().append('<b>Rp. </b> ' + addCommas(itemPros.tariff))
                            finalPros = parseInt(itemPros.tariff)
                            if (klaimStatus == 'final' || statusInacbg == 'final') {
                                ddSpecPros.attr('disabled', true);
                            }
                        }
                    }

                    if (arrSpecInv.length != 0) {
                        refreshOptionSelect2(ddSpecInv, arrSpecInv, {
                            id: 'code',
                            text: 'description'
                        });
                        let itemInv = arrSpecInv.find(({
                            code
                        }) => code === ddSpecInv.val());
                        if (arrSpecInv.length > 1) {
                            if (klaimStatus == 'final' || statusInacbg == 'final' && dbSpecialCmg) {
                                itemInv = arrSpecInv.find(({
                                    code
                                }) => code !== '-')

                                ddSpecInv.val(itemInv.code).trigger("change")
                            }

                            /**
                             * Cek apabila pernah ada data selected
                             */
                            if (statusInacbg == 'normal' && _selectedCmg.hasOwnProperty(SPECIAL_INVESTIGATION)) {
                                itemInv = arrSpecInv.find(({
                                    description
                                }) => {
                                    return description.toLowerCase() == _selectedCmg[SPECIAL_INVESTIGATION].description.toLowerCase()
                                })

                                ddSpecInv.val(itemInv.code).trigger("change")
                            }
                        } else {
                            ddSpecInv.attr('disabled', true);
                        }
                        
                        if (itemInv) {
                            $('#inv-kode').empty().append(itemInv.code)
                            $('#inv-val').empty().append('<b>Rp. </b> ' + addCommas(itemInv.tariff))
                            finalInv = parseInt(itemInv.tariff)
                            if (klaimStatus == 'final' || statusInacbg == 'final') {
                                ddSpecInv.attr('disabled', true);
                            }
                        }
                    }
                    
                    if (arrSpecDrug.length != 0) {
                        refreshOptionSelect2(ddSpecDrug, arrSpecDrug, {
                            id: 'code',
                            text: 'description'
                        });
                        let itemDrug = arrSpecDrug.find(({
                            code
                        }) => code === ddSpecDrug.val());
                        
                        if (arrSpecDrug.length > 1) {
                            if (klaimStatus == 'final' || statusInacbg == 'final' && dbSpecialCmg) {
                                itemDrug = arrSpecDrug.find(({
                                    code
                                }) => code !== '-')

                                ddSpecDrug.val(itemDrug.code).trigger("change")
                            }

                            /**
                             * Cek apabila pernah ada data selected
                             */
                            if (statusInacbg == 'normal' && _selectedCmg.hasOwnProperty(SPECIAL_DRUG)) {
                                itemDrug = arrSpecDrug.find(({
                                    description
                                }) => {
                                    return description.toLowerCase() == _selectedCmg[SPECIAL_DRUG].description.toLowerCase()
                                })

                                ddSpecDrug.val(itemDrug.code).trigger("change")
                            }
                        } else {
                            ddSpecDrug.attr('disabled', true);
                        }
                        
                        if (itemDrug) {
                            $('#drug-kode').empty().append(itemDrug.code)
                            $('#drug-val').empty().append('<b>Rp. </b> ' + addCommas(itemDrug.tariff))
                            finalDrug = parseInt(itemDrug.tariff)
                            if (klaimStatus == 'final' || statusInacbg == 'final') {
                                ddSpecDrug.attr('disabled', true);
                            }
                        }
                    }
                }

                if (arrSpecProc.length == 1) {
                    ddSpecProc.attr('disabled', true);
                }
                if (arrSpecPros.length == 1) {
                    ddSpecPros.attr('disabled', true);
                }
                if (arrSpecDrug.length == 1) {
                    ddSpecDrug.attr('disabled', true);
                }
                if (arrSpecInv.length == 1) {
                    ddSpecInv.attr('disabled', true);
                }
                if (_res.response.data.adl_sub_acute != 0) {
                    let resAccute = _res.response.data.adl_sub_acute
                    subAccute.empty().html(resAccute)
                }

                if (_res.response.data.adl_chronic != 0) {
                    let resCronic = _res.response.data.adl_chronic
                    cronic.empty().html(resCronic)
                }

                if (typeof _res.response.data.grouper.response_inacbg?.sub_acute != 'undefined') {
                    var _subacute = _res.response.data.grouper.response_inacbg.sub_acute;
                    $('.subacute-detail').empty().append(_subacute.description)
                    $('.subacute-kode').empty().append(_subacute.code)
                    $('.subacute-harga').empty().append('<b>Rp. </b> ' + addCommas(_subacute.tariff))
                    _total += parseInt(_subacute.tariff);
                }
                if (typeof _res.response.data.grouper.response_inacbg?.chronic != 'undefined') {
                    var _chronic = _res.response.data.grouper.response_inacbg.chronic;
                    $('.cronic-detail').empty().append(_chronic.description)
                    $('.cronic-kode').empty().append(_chronic.code)
                    $('.cronic-harga').empty().append('<b>Rp. </b> ' + addCommas(_chronic.tariff))
                    _total += parseInt(_chronic.tariff);
                }

                /**
                 * Override total tarif
                 */
                if (_specCmg != undefined && _specCmg.length != 0 || _cbg?.code == 'N-3-15-0') {
                    _total = _res.response.data.grouper?.response_inacbg?.tariff
                }

                if (klaimStatus == 'final') {
                    totalHarga.empty().append('<b>Rp. </b> ' + addCommas(_db_total));
                } else {
                    totalHarga.empty().append('<b>Rp. </b> ' + addCommas(_total));
                }

                $('#persen-vip').val((typeof _res.response.data.add_payment_pct != 'undefined') ? _res.response.data.add_payment_pct : null)
                _grouper[0] = {
                    name: 'Grouper[total]',
                    value: _total
                };
                _grouper[1] = {
                    name: 'Grouper[tambahan_biaya]',
                    value: $('#total-tambahan').val()
                };
                _grouper[2] = {
                    name: 'Grouper[persen_tambahan]',
                    value: $('#persen-vip').val()
                };
                _grouper[3] = {
                    name: 'Grouper[total_naikkelas]',
                    value: $('#total-naikkelas').val()
                };
                _grouper[4] = {
                    name: 'Grouper[total_kelaspelayanan]',
                    value: $('#total-kelaspelayanan').val()
                };
                _grouper[5] = {
                    name: 'Grouper[spesial_drug]',
                    value: finalDrug
                };
                _grouper[6] = {
                    name: 'Grouper[spesial_investigation]',
                    value: finalInv
                };
                _grouper[7] = {
                    name: 'Grouper[spesial_procedure]',
                    value: finalProc
                };
                _grouper[8] = {
                    name: 'Grouper[spesial_prosthesis]',
                    value: finalPros
                };
                _addDetail[0] = {
                    name: 'Add[cbg_desc]',
                    value: _cbg?.description
                }
                _addDetail[1] = {
                    name: 'Add[cbg_code]',
                    value: _cbg?.code
                }
                _addDetail[2] = {
                    name: 'Add[cbg_tarif]',
                    value: _res.response.data.grouper?.response_inacbg?.tariff
                }
                _addDetail[3] = {
                    name: 'Add[info]',
                    value: infoTxt
                }
                _addDetail[4] = {
                    name: 'Add[jenis_rawat]',
                    value: jenisRawat
                }
                _addDetail[11] = {
                    name: 'Add[kelas_awal]',
                    value: jenisKelasRawatAwal
                }
                if (_final) {
                    $('#persen-vip').attr('readonly', true)
                }
                if (_cbg?.description.includes(INVALID_PARAMETERS) || _cbg?.description.includes(ERROR_PARAMETERS)) {
                    ddSpecialOption(1)
                    $('#formfinal-btn-final-klaim').attr('disabled', true)
                }
            } else {
                $('#btn-proses').prop('disabled', false);
                $('.btn-remove-diagnosa').attr('disabled', false)
                $('#edit-koreksi').attr('disabled', false)
                $('#btn-hapus-klaim').prop('disabled', true);
                $('#btn-grouping-idrg').prop('disabled', true)
                componentSwitchEnableIdrg('', true)
                $("#klaiminacbgranapform-klaim_penjamin").select2().val(JKN).trigger('change');
            }
            
            if (klaimStatus == 'final' || statusInacbg == 'final') {
                $('.total-harga').html('<b>Rp. </b>' + addCommas(_res.response.data.grouper?.response_inacbg?.tariff))
            }
        },
        complete: function () {
            if (_updated == 1) {
                if (!$('#proses-final-klaim').hasClass('hidden')) {
                    $().docoForm('click', {
                        skipConfirm: true,
                        data: {
                            nosep: noClaim != '' ? noClaim : noSep ,
                            pendaftaranid: $('.pendaftaran-id-txt').val(),
                            admisi: $('#klaiminacbgranapform-pasienadmisi_id').val()
                        },
                        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/hapus-klaim',
                        success: function (data) {
                            _delete = false
                            $('#btn-proses').attr('disabled', false)
                            $('.btn-remove-diagnosa').attr('disabled', false)
                            $('#edit-koreksi').attr('disabled', false)
                            $('#btn-hapus-klaim').attr('disabled', true)
                            $('#proses-final-klaim').addClass('hidden');
                            $('.btn-remove-diagnosa').click()
                        }
                    });
                }
            }
        }
    })
}

$(document).on('change', '#klaiminacbgranapform-naik_kelas', function () {
    let radioSelected = $('input[name="KlaimInacbgRanapForm[naik_kelas]"]')
    if (radioSelected.is(':checked') == true) {
        flag = true
    } else {
        flag = false
    }

    if (flag == false) {
        $('#btn-proses').attr('disabled', true)
        $('.btn-remove-diagnosa').attr('disabled', true)
        $('#edit-koreksi').attr('disabled', true)
    } else if (flag == true) {
        $('#btn-proses').attr('disabled', false)
        $('.btn-remove-diagnosa').attr('disabled', false)
        $('#edit-koreksi').attr('disabled', false)
    }
})

$(document).on('click', '#klaiminacbgranapform-is_naikkelas', function () {
    let radioSelected = $('input[name="KlaimInacbgRanapForm[naik_kelas]"]')
    let isNaik = $('#klaiminacbgranapform-is_naikkelas')
    let kelas_1 = $('#naikkelas-1')
    let kelas_2 = $('#naikkelas-2')
    let kelas_3 = $('#naikkelas-3')
    let kelas_vip = $('#naikkelas-4')
    let kelas_vvip = $('#naikkelas-5')
    if (isNaik.is(':checked') == true) {
        if (radioSelected.is(':checked') == false) {
            _proses = false
            flag = false
        } else {
            _proses = true
            flag = true
        }
    } else {
        _proses = true
        flag = true
        kelas_3.prop("checked", false)
        kelas_2.prop("checked", false)
        kelas_1.prop("checked", false)
        kelas_vip.prop("checked", false)
        kelas_vvip.prop("checked", false)
    }

    if (flag == false) {
        $('#btn-proses').attr('disabled', true)
        $('.btn-remove-diagnosa').attr('disabled', true)
        $('#edit-koreksi').attr('disabled', true)
    } else if (flag == true) {
        $('#btn-proses').attr('disabled', false)
        $('.btn-remove-diagnosa').attr('disabled', false)
        $('#edit-koreksi').attr('disabled', false)
    }
})

var loadDiagnosa = function () {
    var _append = '';
    var _hide = '';
    if (_final) {
        _hide = 'hidden';
    }
    $.each(_detailDiagnosa, function (k, v) {
        _append += '<tr><th style="border-right: 0px; width:350px">';
        _append += v.nama_diagnosa;
        _append += '</th><th width=2 style="border-left: 0px">';
        _append += '<button style="float:right; margin-top: -3px" data-target="tbl-icd-10" data-key="' + i + '" class="btn btn-danger btn-lg btn-remove-diagnosa hide-me ' + _hide + '" style="float:right"><i class="fa fa-trash"></i></button>';
        _append += '<span class="badge badge-primary" style="float:right;margin-right: 35px;">' + v.kode_diagnosa + '</span>';
        _append += '</th></tr>';
        _diagnosa10[v.kode_diagnosa];
        var _newArr = {
            diagnosa_id: v.diagnosa_id,
            nama_diagnosa: v.nama_diagnosa,
            kode_diagnosa: v.kode_diagnosa,
            diagnosa_type: v.icd_versi
        };
        _klaimDetail[i] = _newArr;
        i++;
        if (v.icd_versi == 10) {
            $('.tbl-icd-10').find('tbody').append(_append)
        } else {
            $('.tbl-icd-9').find('tbody').append(_append)
        }
        _append = '';
    })
}
var cekDuplicate = function (kode) {
    var _state = true;

    $.each(_klaimDetail, function (k, v) {
        if (typeof _klaimDetail[k] !== 'undefined') {
            if (v.kode_diagnosa == kode) {
                _state = false;
                return false;
            }
        }
    })
    var _primer = $('#klaiminacbgranapform-diagnosa_primer').val().split('#')
    $.each(primerSudahKoresi, function (k, v) {
        if (v == kode) {
            _state = false;
            return false;
        }
    })

    var _sekunder = $('#klaiminacbgranapform-diagnosa_sekunder').val().split('#')
    $.each(sekunderSudahKoreksi, function (k, v) {
        if (v == kode) {
            _state = false;
            return false;
        }
    })

    return _state;
}
var hideAddRanap = function () {
    if ($('#klaiminacbgranapform-is_naikkelas').is(':checked')) {
        $('.hide-naik-kelas').removeClass('hidden');
        hidePilKelas($('input[name="klaiminacbgranapform[jenis_kelasrawat]"]:checked').val(), false)
    } else {
        if (!$('.hide-naik-kelas').hasClass('hidden')) {
            $('.hide-naik-kelas').addClass('hidden');
        }
    }
    if ($('#klaiminacbgranapform-is_rawatintensif').is(':checked')) {
        $('.hide-rawat-intensif').removeClass('hidden');
    } else {
        if (!$('.hide-rawat-intensif').hasClass('hidden')) {
            $('.hide-rawat-intensif').addClass('hidden');
        }
    }
}

var deleteGrouper = function () {
    let jaminan = $('#klaiminacbgranapform-klaim_penjamin').val()

    cleaning()
    if (!$('#proses-final-klaim').hasClass('hidden') || _delete == true) {
    }
}

function disabledInputan(statusIdrg) {
    if (statusIdrg == 'final') {
        $(".delete-on-edit").attr('disabled', true)
        $('.group-tarif').attr('disabled', true)
    }else{
        $(".delete-on-edit").attr('disabled', false)
        $('.group-tarif').attr('disabled', false)
    }
}

$(document).on('mouseover mouseenter keyup keypress blur change', '#persen-vip', function () {
    var _val = $(this).val();
    var _biayaawal = parseInt($('#total-kelaspelayanan').val());
    var _biayaalt = parseInt($('#total-naikkelas').val());
    var _tambahanAwal = _biayaalt - _biayaawal;
    if (_val > 75) {
        $(this).val(75);
    }
    var _tambahanvip = Math.round((_val * _biayaalt) / 100);
    var _totaltambahan = _tambahanAwal + _tambahanvip;
    $('#total-tambahan').val(_totaltambahan);
    $('.tambahanbiaya-txt').empty().append('<b>Rp. </b>' + addCommas(_totaltambahan));
    _grouper[1] = {
        name: 'Grouper[tambahan_biaya]',
        value: _totaltambahan
    };
    _grouper[2] = {
        name: 'Grouper[persen_tambahan]',
        value: _val
    };
})

$(document).on('keyup', '.validate-minus', function () {
    var _id = $(this).attr('id');
    var _val = $('#' + _id).val();
    if (_val < 0) {
        $('#' + _id).val(0);
    }
})

function getNaikKelas(_kelasnaik) {
    var _isnaikkelas = false;
    var _hakkelas = $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]:checked').val();
    if (_hakkelas == 1) {
        if (_kelasnaik == 2) {
            isNaik = false;
            _isnaikkelas = false
        } else if (_kelasnaik == 3) {
            isNaik = false;
            _isnaikkelas = false
        } else if (_kelasnaik == 4) {
            isNaik = true;
            _isnaikkelas = true
        } else if (_kelasnaik == 5) {
            isNaik = true;
            _isnaikkelas = true
        }
    } else if (_hakkelas == 2) {
        if (_kelasnaik == 1) {
            isNaik = true;
            _isnaikkelas = true
        } else if (_kelasnaik == 3) {
            isNaik = false;
            _isnaikkelas = false
        } else if (_kelasnaik == 4) {
            isNaik = true;
            _isnaikkelas = true
        } else if (_kelasnaik == 5) {
            isNaik = true;
            _isnaikkelas = true
        }
    } else if (_hakkelas == 3) {
        if (_kelasnaik == 3) {
            isNaik = false;
            _isnaikkelas = false
        } else if (_kelasnaik == 2) {
            isNaik = true
            _isnaikkelas = true
        } else {
            isNaik = false
            _isnaikkelas = false
        }
    }

    return _isnaikkelas;
}

function getKelasKlaim(_kelasnaik) {
    var _kelasKlaim = '';

    if (_kelasnaik == 4) {
        _kelasKlaim = 'VIP';
    } else if (_kelasnaik == 5) {
        _kelasKlaim = 'VVIP';
    } else {
        _kelasKlaim = _kelasnaik;
    }

    return _kelasKlaim;
}

function getKeteranganNaikKelas(a, b) {
    let ket = '';
    let cekNaik = a
    let kelas = b
    if (cekNaik == true) {
        ket = ' Untuk Naik Kelas ' + kelas
    } else if (cekNaik == false) {
        ket = ' Untuk Turun Kelas ' + kelas
    } else if (typeof kelas === 'undefined') {
        kelas = ''
        ket = ''
    }

    return ket;
}

function getIsNaik(_kelasnaik) {
    var isNaik = false;
    var _hakkelas = $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]:checked').val();
    if (_hakkelas == 1) {
        if (_kelasnaik == 2) {
            isNaik = false;
        } else if (_kelasnaik == 3) {
            isNaik = false;
        } else if (_kelasnaik == 4) {
            isNaik = true;
        } else if (_kelasnaik == 5) {
            isNaik = true;
        }
    } else if (_hakkelas == 2) {
        if (_kelasnaik == 1) {
            isNaik = true;
        } else if (_kelasnaik == 3) {
            isNaik = false;
        } else if (_kelasnaik == 4) {
            isNaik = true;
        } else if (_kelasnaik == 5) {
            isNaik = true;
        }
    } else if (_hakkelas == 3) {
        if (_kelasnaik == 3) {
            isNaik = false;
        } else if (_kelasnaik == 2) {
            isNaik = true
        } else {
            isNaik = false
        }
    }

    return isNaik;
}

$(document).on('change', '.date', function (e) {
    let tgl_masuk = $('#klaiminacbgranapform-tgl_masuk')
    let tgl_keluar = $('#klaiminacbgranapform-tgl_keluar')
    var jenisRawat = $('input[name="KlaimInacbgRanapForm[jenis]"]:checked').val();
    let convert_masuk = convertDateByFormat(tgl_masuk.val(), "M-d-y")
    var convert_keluar = ''
    if(jenisRawat == jenisRawatRajal) {
        var masuk = $('#klaiminacbgranapform-tgl_masuk').val(); 
        $('#klaiminacbgranapform-tgl_keluar').val(masuk);
        var convert_keluar = convertDateByFormat(tgl_masuk.val(), "M-d-y")
    }else{
        var convert_keluar = convertDateByFormat(tgl_keluar.val(), "M-d-y")
    }

    tgl_masuk.datetimepicker("setEndDate", tgl_keluar.val())
    tgl_keluar.datetimepicker("setStartDate", tgl_masuk.val())
    let c = date_diff_indays(convert_masuk, convert_keluar)
    $('#klaiminacbgranapform-los').val(c)
    $('#los').empty().append(': ' + c)
    if (c >= MIN_ACCUTE) {
        subAccute.empty().append(ACCUTE)
        $("#adl_subacute").attr("disabled", false);
        $('#adl_subacute').val(ACCUTE)
    } else {
        subAccute.empty().append(0)
    }

    if (c >= MIN_CRONIC) {
        cronic.empty().append(CRONIC)
        $("#adl_cronic").attr("disabled", false);
        $('#adl_cronic').val(CRONIC)
    } else {
        cronic.empty().append(0)
    }

    if(c < MIN_CRONIC){
        $("#adl_cronic").attr("disabled", true);
        $('#adl_cronic').val(0)
    }

    if (c < MIN_ACCUTE){
        $("#adl_subacute").attr("disabled", true);
        $('#adl_subacute').val(0)
    }
})

$(document).on('change', '.date-intubasi', function (e) {
    let tgl_masuk = $('#klaiminacbgranapform-intubasi')
    let tgl_keluar = $('#klaiminacbgranapform-ekstubasi')
    let convert_masuk = convertDateByFormat(tgl_masuk.val(), "M-d-y")
    let convert_keluar = convertDateByFormat(tgl_keluar.val(), "M-d-y")

    tgl_masuk.datetimepicker("setEndDate", tgl_keluar.val())
    tgl_keluar.datetimepicker("setStartDate", tgl_masuk.val())
    let c = date_diff_indays(convert_masuk, convert_keluar)
})

$(document).on('click', '#formfinal-btn-kirim-klaim', function () {
    let jaminan = $('#klaiminacbgranapform-klaim_penjamin').val()
    let no_sep = null
    if(arrayJaminan.includes(jaminan)) {
        no_sep = $('#no_klaimcovid').val()

    }else{
        no_sep = $('.no-sep').val()
    }
    $().docoForm('click', {
        data: {
            nosep: no_sep,
            pendaftaranid: $('.pendaftaran-id-txt').val()
        },
        skipSuccessNotif: true,
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/kirim-klaim-online',
        success: function (data) {
            if (data) {
                let titleError = data?.title
                let jenisError = 'Proses Gagal !'
                if(titleError != undefined && titleError.toLowerCase() == jenisError.toLowerCase()) {
                    docoNotification('warning', titleError, data?.text)
                    return false
                }
                kemenkes_status = 'Terkirim';
                $('.kemenkes_status').removeClass('text-danger');
                $('.kemenkes_status').addClass('text-success');
                $('.kemenkes_status').empty().append(kemenkes_status);
                docoNotification('success', 'Berhasil!', 'Proses berhasil disimpan !')
            }
        }
    });
});


$(document).on('keypress', '#no_sep', function (e) {
    if (e.keyCode !== undefined) {
        if (e.which == 13 || e.keyCode == 13) {
            e.preventDefault()
            $('#btn-search-sep').click();
        }
    }
});

$(document).on('keypress', '.form-control', function (e) {
    if (e.keyCode !== undefined) {
        if (e.which == 13 || e.keyCode == 13) {
            e.preventDefault()
        }
    }
})

$(document).on('keypress', 'input[type="checkbox"]', function (e) {
    e.preventDefault()
})

$(document).on('keypress', 'input[type="radio"]', function (e) {
    e.preventDefault()
})

$('#no_sep').on('keyup', function (e) {
    e.preventDefault()
    let nosep = $('#no_sep').val();
    if (infoNoSep === nosep) {
        let _html = i18next.t('<b><i class="fa fa-search"></i></b> ' + 'Validasi SEP');
        $('#btn-search-sep').html(_html).attr('disabled', true);
    } else {
        _html = i18next.t('<b><i class="fa fa-search"></i></b> ' + 'Validasi SEP');
        $('#btn-search-sep').html(_html).attr('disabled', false);
        $('.err-no-sep').html('');
    }
});

function date_diff_indays(date1, date2) {
    let addDays = 1;
    let dt1 = new Date(date1);
    let dt2 = new Date(date2);
    let dtRes = Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate())) / (1000 * 60 * 60 * 24));
    return dtRes + addDays;
}
$('#btn-search-sep').on('click', function () {
    var valid = true
    var nosep = $('#no_sep').val();
    if (!nosep) {
        $('.err-no-sep').html("No SEP tidak boleh kosong");
        valid = false;
    }
    if (valid) {
        $.ajax({
            type: 'POST',
            url: window.location.origin + '/api/bpjs/peserta',
            data: {
                nosep: nosep
            },
            dataType: 'JSON',
            beforeSend: function () {
                var _html = i18next.t('Memuat...');
                $('#btn-search-sep').html(_html).attr('disabled', true);
                $('.err-no-sep').html('');
            },
            success: function (res) {
                if (res.response == "") {
                    // $('.err-no-sep').html("Bridging BPJS gagal. Silakan coba lain kali.");
                    docoNotification('error', 'Kesalahan', 'Bridging BPJS gagal. Silakan coba lain kali.')
                } else {
                    var resbpjs = res.response.metaData;
                    if (resbpjs.code != "200") {
                        var code = resbpjs.code;
                        const notFoundSep = "No SEP yang diinputkan tidak tersedia, Mohon di cek kembali";
                        // $('.err-no-sep').html(notFoundSep);
                        docoNotification('error', 'Kesalahan', notFoundSep)
                    } else {
                        var response = res.response.response
                        if (response.duplikasi) {
                            let duplikasi = true
                            let namaDupli = response.duplikasi.nama_pasien
                            let rmDupli = response.duplikasi.no_rekammedik
                            let idDupli = response.duplikasi.no_pendaftaran
                            if (duplikasi) {
                                (new PNotify({
                                    title: "Duplikasi SEP",
                                    text: "No SEP <b>" + nosep + "</b> sudah digunakan oleh : <br>" +
                                        " Nama Pasien : <b>" + namaDupli + "</b><br>" +
                                        " No Pendaftaran : <b>" + idDupli + " </b><br>" +
                                        " No RM : <b>" + rmDupli +
                                        "</b><br>" +
                                        "Silahkan input No SEP yang sesuai.",
                                    addclass: "alert alert-error alert-arrow-right alert-styled-right",
                                    type: "error",
                                    buttons: {
                                        closer: true,
                                        sticker: true
                                    },
                                    hide: true,
                                    history: {
                                        history: false
                                    }
                                }))
                            }
                        } else {
                            let peserta, noRmBpjs, noSep, noKartu, kelasKode, namaPasienBpjs
                            if (response.islive == 'true') {
                                peserta = response.pesertasep
                                noKartu = peserta.noKartuBpjs
                            } else {
                                peserta = response.peserta;
                                noKartu = peserta.noKartu
                            }
                            noRmBpjs = peserta.noMr
                            noSep = response.noSep
                            kelasKode = peserta.kelasKode
                            namaPasienBpjs = peserta.nama.toLowerCase();
                            if (typeof noSep == 'undefined') {
                                // docoNotification('warning', 'peringatan', 'no SEP tidak tersedia')
                                // return '';
                            }
                            if (namaPasienBpjs !== namaPasien) {
                                (new PNotify({
                                    title: "Peringatan",
                                    text: "Nama Pasien berbeda dengan Nama yang terdaftar di BPJS <br>" +
                                        " Nama Pasien BPJS : <strong>" + namaPasienBpjs + " - " + noRmBpjs +
                                        " </strong><br> Nama Pasien yang terdaftar : <strong>" + namaPasien + " - " + noRm,
                                    addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                                    type: "warning",
                                    buttons: {
                                        closer: true,
                                        sticker: true
                                    },
                                    hide: true,
                                    confirm: {
                                        confirm: true,
                                        buttons: [{
                                                text: 'Ya',
                                                addClass: 'btn btn-xs btn-success',
                                            },
                                            {
                                                text: 'Tidak',
                                                addClass: 'btn btn-xs btn-warning',
                                            }
                                        ]
                                    },
                                    history: {
                                        history: false
                                    }
                                })).get().on('pnotify.confirm', function () {
                                    let item = {
                                        'kunjunganId': kunjunganId,
                                        'noSep': nosep,
                                        'noKartu': noKartu,
                                        'kelasKode': kelasKode
                                    };
                                    konfirmasiPasien(item);
                                }).on('pnotify.cancel', function () {
                                    location.reload();
                                });
                            } else if (noRm !== noRmBpjs) {
                                (new PNotify({
                                    title: "Peringatan",
                                    text: "Nomor Rekam Medik Pasien berbeda dengan yang terdaftar di BPJS <br>" +
                                        " Nama Pasien BPJS : <strong>" + namaPasienBpjs + " - " + noRmBpjs +
                                        " </strong><br> Rekam Medik Pasien yang terdaftar : <strong>" + namaPasien + " - " + noRm,
                                    addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                                    type: "warning",
                                    buttons: {
                                        closer: true,
                                        sticker: true
                                    },
                                    hide: true,
                                    confirm: {
                                        confirm: true,
                                        buttons: [{
                                                text: 'Ya',
                                                addClass: 'btn btn-xs btn-success',
                                            },
                                            {
                                                text: 'Tidak',
                                                addClass: 'btn btn-xs btn-warning',
                                            }
                                        ]
                                    },
                                    history: {
                                        history: false
                                    }
                                })).get().on('pnotify.confirm', function () {
                                    let item = {
                                        'kunjunganId': kunjunganId,
                                        'noSep': nosep,
                                        'noKartu': noKartu,
                                        'kelasKode': kelasKode
                                    };
                                    konfirmasiPasien(item);
                                }).on('pnotify.cancel', function () {
                                    location.reload();
                                });
                            } else {
                                (new PNotify({
                                    title: "Informasi",
                                    text: "Nama Pasien terdaftar di BPJS <br>" +
                                        " Nama Pasien : <strong>" + namaPasienBpjs + " - " + noRmBpjs +
                                        " </strong>",
                                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                                    type: "success",
                                    buttons: {
                                        closer: true,
                                        sticker: true
                                    },
                                    hide: true,
                                    history: {
                                        history: false
                                    }
                                }));
                                let item = {
                                    'kunjunganId': kunjunganId,
                                    'noSep': nosep,
                                    'noKartu': noKartu,
                                    'kelasKode': kelasKode
                                };
                                konfirmasiPasien(item);
                            }
                        }
                    }
                }
            },
            complete: function () {
                var _html = i18next.t('<b><i class="fa fa-search"></i></b> ' + 'Validasi SEP');
                $('#btn-search-sep').html(_html).attr('disabled', true);
            }
        });
    }
});

function konfirmasiPasien(data) {
    deleteGrouper()
    $.ajax({
        type: 'POST',
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/update',
        data: {
            data: data
        },
        dataType: 'JSON',
        success: function (res) {
            location.reload();
        }
    });
}

function lookupkelas(valuekelas) {
    let kelas_1 = $('#naikkelas-1')
    let kelas_2 = $('#naikkelas-2')
    let kelas_3 = $('#naikkelas-3')
    let kelas_vip = $('#naikkelas-4')
    let kelas_vvip = $('#naikkelas-5')

    if (valuekelas == 3) {
        kelas_2.prop("disabled", false)
        kelas_1.prop("disabled", true)
        kelas_vip.prop("disabled", true)
        kelas_vvip.prop("disabled", true)
        kelas_3.prop("disabled", true)
        kelas_3.prop("checked", false)
        kelas_2.prop("checked", false)
        kelas_1.prop("checked", false)
        kelas_vip.prop("checked", false)
        kelas_vvip.prop("checked", false)
    } else if (valuekelas == 2) {
        kelas_1.prop("disabled", false)
        kelas_3.prop("disabled", false)
        kelas_vip.prop("disabled", true)
        kelas_vvip.prop("disabled", true)
        kelas_1.prop("checked", false)
        kelas_2.prop("checked", false)
        kelas_3.prop("checked", false)
        kelas_vip.prop("checked", false)
        kelas_vvip.prop("checked", false)
    } else {
        kelas_3.prop("disabled", false)
        kelas_2.prop("disabled", false)
        kelas_vip.prop("disabled", false)
        kelas_vvip.prop("disabled", false)
        kelas_1.prop("checked", false)
        kelas_2.prop("checked", false)
        kelas_3.prop("checked", false)
        kelas_vip.prop("checked", false)
        kelas_vvip.prop("checked", false)
    }
}

$(document).on('change', 'input[name="klaiminacbgranapform[jenis_kelasrawat]"]:checked', function () {
    let _valuekelas = $('input[name="klaiminacbgranapform[jenis_kelasrawat]"]:checked').val()
    lookupkelas(_valuekelas)
})

function cekTanggal(val, val2) {
    let masuk = convertDateByFormat(val, "M-d-y")
    let keluar = convertDateByFormat(val2, "M-d-y")
    if (masuk > keluar) {
        (new PNotify({
            title: "Perhatian",
            text: "Tanggal masuk tidak boleh lebih dari tanggal keluar",
            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
            type: "warning",
            buttons: {
                closer: true,
                sticker: true
            },
            hide: true,
            history: {
                history: false
            }
        }));
        $('#btn-proses').addClass('hidden')
    } else {
        $('#btn-proses').removeClass('hidden')
    }
}

function ddSpecialOption(state) {
    ddSpecProc.attr('disabled', state);
    ddSpecPros.attr('disabled', state);
    ddSpecInv.attr('disabled', state);
    ddSpecDrug.attr('disabled', state);
}

function infoKelas(val) {
    let kelas = ''
    if (val == 'AP') {
        kelas = 'TARIF RS KELAS A PEMERINTAH'
    } else if (val == 'AS') {
        kelas = 'TARIF RS KELAS A SWASTA'
    } else if (val == 'BP') {
        kelas = 'TARIF RS KELAS B PEMERINTAH'
    } else if (val == 'BS') {
        kelas = 'TARIF RS KELAS B SWASTA'
    } else if (val == 'CP') {
        kelas = 'TARIF RS KELAS C PEMERINTAH'
    } else if (val == 'CS') {
        kelas = 'TARIF RS KELAS C SWASTA'
    } else if (val == 'DP') {
        kelas = 'TARIF RS KELAS D PEMERINTAH'
    } else if (val == 'DS') {
        kelas = 'TARIF RS KELAS D SWASTA'
    }

    return kelas
}

function removeTarget(arr, value) {

    isArray = Array.isArray(arr)
    if(! isArray) {
        arr = arr.split("#")
    }  
    
    return arr.filter(function (ele) {
        return ele != value;
    });
}

function cleanArr(arr) {
    let index = -1,
        arr_length = arr ? arr.length : 0,
        resIndex = -1,
        result = [];

    while (++index < arr_length) {
        let vv = arr[index];

        if (vv) {
            result[++resIndex] = vv;
        }
    }

    return result;
}

function lengthCounter(inputs) {
    // inputs.reduce((counter, {status}) => status == 0 ? counter + 1 : counter, 0);
    let counter = 0;
    for (const input of inputs) {
        if (input.description !== "None") counter += 1;
    }
    return counter;
}

function cleaning() {
    ddSpecialOption(false)
    newTotal = 0
    finalTotal = 0
    oldTotal = 0
    tarifSpecial = [0, 0, 0, 0, 0]
    $('#spros-val').html('');
    $('#sproc-val').html('');
    $('#drug-val').html('');
    $('#inv-val').html('');
    // clear all data on array
    arrSpecProc.length = 0;
    arrSpecPros.length = 0;
    arrSpecDrug.length = 0;
    arrSpecInv.length = 0;
    // reasign default data to array
    arrSpecProc = [{
        description: 'None',
        code: '-',
        tariff: 0
    }];
    arrSpecPros = [{
        description: 'None',
        code: '-',
        tariff: 0
    }];
    arrSpecDrug = [{
        description: 'None',
        code: '-',
        tariff: 0
    }];
    arrSpecInv = [{
        description: 'None',
        code: '-',
        tariff: 0
    }];
    $('#formfinal-btn-final-klaim').attr('disabled', false)
    $('#klaiminacbgranapform-lama_rawatkelas').empty()
}

$(document).on('change', '#klaiminacbgranapform-klaim_penjamin', function(e) {
    e.preventDefault()

    let jaminan = $('#klaiminacbgranapform-klaim_penjamin').val()

    if(arrayJaminan.includes(jaminan)) {
        
        $('.covid-select').removeClass('hidden')
        $('.jkn-select').addClass('hidden')
        $('.jkn').removeClass('hidden')
        $('.radio-jenis-rawat-3').removeClass('hidden')

        hiddenSitb(true)

        if(jaminan == KIPI) {
            hiddenJaminanKipi(false)
        }else{
            hiddenJaminanKipi(true)
        }

        // Kondisi IGD hanya untuk COVID
        if(jaminan == COVID) {
            $('.radio-jenis-rawat-3').removeClass('hidden')
        }else {
            $('.radio-jenis-rawat-3').addClass('hidden')
        }
        defaultJenisRawat(jenisRawatRanap)

        if(typeof $('#no_klaimcovid').val() == 'undefined' || $('#no_klaimcovid').val() == ""){
            let dataPasien = {
                'kunjunganId': kunjunganId,
                'noKartu': '',
                'nosep' : nosep,
                'noRm' : noRm,
                'namaPasien': namaPasien,
                'tglLahir': $('#klaiminacbgranapform-tgl_lahir').val(),
                'gender': $('#klaiminacbgranapform-jeniskelamin').val(),
                'jenisIdentitas': $('#klaiminacbgranapform-identitas_id').val(),
                'noIdentitas' : $('#identitas_value').val()
            }
            generateNoCovid(dataPasien)
            appendJaminan()
        }else {
           _nosep = $('#no_klaimcovid').val();
            if (!_getBerkas) {
                getBerkas();
            }
        }
        setHakKelas(COVID)
    }

    if(jaminan == KIPI) {
        hiddenJaminanKipi(false)
    }
    
    if(jaminan == JKN) {
        $('.covid-select').addClass('hidden')
        $('.radio-jenis-rawat-3').addClass('hidden')
        $('.jkn-select').removeClass('hidden')
        if(infoNoSep == '') {
            $('.jkn').addClass('hidden')
        }

        // Suggest data awal
        if(jenisPerawatan.lentgh != 0) {
            defaultJenisRawat(jenisPerawatan)
            hideTabInaGrouper(jenisPerawatan)
        } else {
            defaultJenisRawat(jenisRawatRanap)
        }
        hiddenSitb(false)
        hideAddRanap()
        setHakKelas(JKN)
    }
})

$(document).on('change', '#klaiminacbgranapform-jenis', function(e) {
    var jenisRawat = $('input[name="KlaimInacbgRanapForm[jenis]"]:checked').val();
    if(jenisRawat == jenisRawatRajal) {
        disableTanggalKeluar(true)
        hiddenEksekutifJkn(false)
        hiddenNaikturunKelas(true)
        hiddenRawatIntensif(true)
        hiddenJenisKelasRawat(true)
        $("#adl_subacute").attr("disabled", true); 
        $("#adl_cronic").attr("disabled", true); 
        $("#adl_subacute").val(0); 
        $("#adl_cronic").val(0); 
        $('#klaiminacbgranapform-los').val('1')
        $('#los').empty().append(': 1')
    }else{
        disableTanggalKeluar(false)
        hiddenEksekutifJkn(true)
        hiddenNaikturunKelas(false)
        hiddenRawatIntensif(false)
        hiddenJenisKelasRawat(false)
    }
})

function setHakKelas(penjamin) {
    let jeniskelas_1 = $('#hakkelas-1')
    let jeniskelas_2 = $('#hakkelas-2')
    let jeniskelas_3 = $('#hakkelas-3')
    if(penjamin != COVID) {
        $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]').prop("disabled", false)
        if(hakKelasBpjs == 1) {
            jeniskelas_1.prop('checked', true)
            jeniskelas_2.prop('checked', false)
            jeniskelas_3.prop('checked', false)
        } else if (hakKelasBpjs == 2) {
            jeniskelas_1.prop('checked', false)
            jeniskelas_2.prop('checked', true)
            jeniskelas_3.prop('checked', false)
        } else if (hakKelasBpjs == 3) {
            jeniskelas_1.prop('checked', false)
            jeniskelas_2.prop('checked', false)
            jeniskelas_3.prop('checked', true)
        } else {
            if(hakKelas == 1) {
                jeniskelas_1.prop('checked', true)
                jeniskelas_2.prop('checked', false)
                jeniskelas_3.prop('checked', false)
            } else if (hakKelas == 2) {
                jeniskelas_1.prop('checked', false)
                jeniskelas_2.prop('checked', true)
                jeniskelas_3.prop('checked', false)
            } else if (hakKelas == 3) {
                jeniskelas_1.prop('checked', false)
                jeniskelas_2.prop('checked', false)
                jeniskelas_3.prop('checked', true)
            } else {
                jeniskelas_1.prop('checked', false)
                jeniskelas_2.prop('checked', false)
                jeniskelas_3.prop('checked', true)
            }
        }
    } else {
        jeniskelas_1.prop('checked', false)
        jeniskelas_2.prop('checked', false)
        jeniskelas_3.prop('checked', true)
        $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]').prop("disabled", true)
    }
}

function defaultJenisRawat(jenis) {
    let ranap = $('#jenis-1')
    let rajal = $('#jenis-2')
    let igd = $('#jenis-3')
    if(jenis == 1) {
        ranap.prop('checked', true)
    }else if(jenis == 2) {
        rajal.prop('checked', true)    
    }else{
        igd.prop('checked', true)    
    }

    // Trigger change ketika hit function ini
    $("#klaiminacbgranapform-jenis").trigger('change')
}


function generateNoCovid(data) {
    $.ajax({
        type: 'POST',
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/generate-no-covid',
        data: data,
        dataType: 'JSON',
        success: function (res) {
            let data = res.response;
            $('#no_klaimcovid').val(data.no_klaimcovid)
            _nosep = data.no_klaimcovid;
            if (!_getBerkas) {
                getBerkas();
            }
        },
    });
}

function appendJaminan() {
    _jaminan_klaim[0] = {
        name: 'KlaimInacbgForm[klaim_penjamin]',
        value: $('#klaiminacbgranapform-klaim_penjamin').val()
    }
    _no_pengajuan_covid[0] = {
        name: 'KlaimInacbgForm[no_klaimcovid]',
        value: $('#no_klaimcovid').val()
    }
    _identitas_value_[0] = {
        name: 'KlaimInacbgForm[identitas_value]',
        value: $('#identitas_value').val()
    }
    _identitas_id[0] = {
        name: 'KlaimInacbgForm[identitas_id]',
        value: $('#klaiminacbgranapform-identitas_id').val()
    }
}

 
function getBerkas() {
    $.ajax({
        url: 'get-berkas',
        type: 'get',
        data: {nosep: _nosep},
        dataType: 'json',
        success: function(response){
            _getBerkas = true;
            $.each(response, function(index, value) {
                switch(index) {
                    case "resume_medis":
                        $.each(value, function(idx, val){
                            var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id, previewElement: val.message, accepted: true};
                            
                            Dropzone.forElement("div#upload1").emit("addedfile", mockFile);   
                            
                            var newNode = document.createElement('a');
                            newNode.setAttribute('data-target', '#modal-preview')
                            newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                            newNode.setAttribute('type', 'button')
                            newNode.className = 'dz-remove margin-10  btn-cetak';
                            newNode.innerHTML = 'DETAIL';
                            mockFile.previewTemplate.appendChild(newNode);
                            
                            $(mockFile.previewElement).find('.dz-error-message span').text(val.message);
                        });
                        break;
                    case "ruang_rawat":
                        $.each(value, function(idx, val){
                            var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id, previewElement: val.message, accepted: true};
                            Dropzone.forElement("div#upload2").emit("addedfile", mockFile);    

                            var newNode = document.createElement('a');
                            newNode.setAttribute('data-target', '#modal-preview')
                            newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                            newNode.setAttribute('type', 'button')
                            newNode.className = 'dz-remove margin-10  btn-cetak';
                            newNode.innerHTML = 'DETAIL';
                            mockFile.previewTemplate.appendChild(newNode);

                        });   
                        break;
                    case "laboratorium":
                        $.each(value, function(idx, val){
                            var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id, previewElement: val.message, accepted: true};

                            var newNode = document.createElement('a');
                            newNode.setAttribute('data-target', '#modal-preview')
                            newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                            newNode.setAttribute('type', 'button')
                            newNode.className = 'dz-remove margin-10  btn-cetak';
                            newNode.innerHTML = 'DETAIL';
                            mockFile.previewTemplate.appendChild(newNode);

                            Dropzone.forElement("div#upload3").emit("addedfile", mockFile);    
                        });   
                        break;
                    case "radiologi":
                        $.each(value, function(idx, val){
                            var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id, previewElement: val.message, accepted: true};

                            var newNode = document.createElement('a');
                            newNode.setAttribute('data-target', '#modal-preview')
                            newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                            newNode.setAttribute('type', 'button')
                            newNode.className = 'dz-remove margin-10  btn-cetak';
                            newNode.innerHTML = 'DETAIL';
                            mockFile.previewTemplate.appendChild(newNode);

                            Dropzone.forElement("div#upload4").emit("addedfile", mockFile);    
                        });   
                        break;
                    case "penunjang_lain":
                        $.each(value, function(idx, val){
                            var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id, previewElement: val.message, accepted: true};

                            var newNode = document.createElement('a');
                            newNode.setAttribute('data-target', '#modal-preview')
                            newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                            newNode.setAttribute('type', 'button')
                            newNode.className = 'dz-remove margin-10  btn-cetak';
                            newNode.innerHTML = 'DETAIL';
                            mockFile.previewTemplate.appendChild(newNode);

                            Dropzone.forElement("div#upload5").emit("addedfile", mockFile);    
                        });   
                        break;
                    case "resep_obat":
                        $.each(value, function(idx, val){
                            var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id, previewElement: val.message, accepted: true};

                            var newNode = document.createElement('a');
                            newNode.setAttribute('data-target', '#modal-preview')
                            newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                            newNode.setAttribute('type', 'button')
                            newNode.className = 'dz-remove margin-10  btn-cetak';
                            newNode.innerHTML = 'DETAIL';
                            mockFile.previewTemplate.appendChild(newNode);

                            Dropzone.forElement("div#upload6").emit("addedfile", mockFile);    
                        });   
                        break;
                    case "tagihan":
                        $.each(value, function(idx, val){
                            var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id, previewElement: val.message, accepted: true};
                            Dropzone.forElement("div#upload7").emit("addedfile", mockFile);    
                            
                            var newNode = document.createElement('a');
                            newNode.setAttribute('data-target', '#modal-preview')
                            newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                            newNode.setAttribute('type', 'button')
                            newNode.className = 'dz-remove margin-10  btn-cetak';
                            newNode.innerHTML = 'DETAIL';
                            mockFile.previewTemplate.appendChild(newNode);

                        });   
                        break;
                    case "kartu_identitas":
                        $.each(value, function(idx, val){
                            var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id, previewElement: val.message, accepted: true};
                            Dropzone.forElement("div#upload8").emit("addedfile", mockFile);    

                            var newNode = document.createElement('a');
                            newNode.setAttribute('data-target', '#modal-preview')
                            newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                            newNode.setAttribute('type', 'button')
                            newNode.className = 'dz-remove margin-10  btn-cetak';
                            newNode.innerHTML = 'DETAIL';
                            mockFile.previewTemplate.appendChild(newNode);

                        });   
                        break;
                    case "lain_lain":
                        $.each(value, function(idx, val){
                            var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id, previewElement: val.message, accepted: true};
                            Dropzone.forElement("div#upload9").emit("addedfile", mockFile);    

                            var newNode = document.createElement('a');
                            newNode.setAttribute('data-target', '#modal-preview')
                            newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                            newNode.setAttribute('type', 'button')
                            newNode.className = 'dz-remove margin-10  btn-cetak';
                            newNode.innerHTML = 'DETAIL';
                            mockFile.previewTemplate.appendChild(newNode);

                        });   
                        break;
                    case "bebas_biaya":
                        $.each(value, function(idx, val){
                            var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id , accepted: true};
                            Dropzone.forElement("div#upload-bebas-biaya").emit("addedfile", mockFile);    

                            var newNode = document.createElement('a');
                            newNode.setAttribute('data-target', '#modal-preview')
                            newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                            newNode.setAttribute('type', 'button')
                            newNode.className = 'dz-remove margin-10  btn-cetak';
                            newNode.innerHTML = 'DETAIL';
                            mockFile.previewTemplate.appendChild(newNode);
                            
                        });   
                        break;
                    case "dokumen_kipi":
                        $.each(value, function(idx, val){
                            var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id , accepted: true};
                            Dropzone.forElement("div#upload-dokumen-kipi").emit("addedfile", mockFile);       

                            var newNode = document.createElement('a');
                            newNode.setAttribute('data-target', '#modal-preview')
                            newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                            newNode.setAttribute('type', 'button')
                            newNode.className = 'dz-remove margin-10  btn-cetak';
                            newNode.innerHTML = 'DETAIL';
                            mockFile.previewTemplate.appendChild(newNode);
                            
                        });   
                        break;
                    default:
                    // code block
                }
                _loadBerkas = false;
                if (_final) {
                    $('.button-select').hide();
                    $('.dz-message').hide();
                    $('.dz-error-mark').hide();
                    $(".dz-remove").hide();
                    $('.dz-error-message').css('opacity', 0);
                }
            });
        }
    });
}


function hiddenJaminanKipi(hide = false) 
{
   if(hide == true) {
       $('.kipi-section').addClass('hidden')
   }else{
       $('.kipi-section').removeClass('hidden')
   }
}

function hiddenSitb(hide = false) 
{
   if(hide == true) {
       $('.sitb-section').addClass('hidden')
   }else{
       $('.sitb-section').removeClass('hidden')
   }
}

 
Dropzone.autoDiscover = false;

window.onload = function() {
    var upload1 = new Dropzone("#upload1", { // Make the whole body a dropzone
        url: 'upload-berkas',
        method:'post',
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        addRemoveLinks: true,
        dictDefaultMessage: 'Silakan seret dan jatuhkan berkas disini',
        dictRemoveFile: 'HAPUS',
        autoQueue: true,
        clickable: ".fileinput-1",
        acceptedFiles: 'application/pdf',
        params: {label:'resume_medis'},
        maxFilesize: 3,
        timeout: 300000, //5 Menit
        parallelUploads:1,
        uploadMultiple: false,
        init: function() {
            this.on('addedfile', function(file) {
                var ext = file.name.split('.').pop();
                if (_final && !_loadBerkas) {
                    x = alert('Status klaim sudah final. Unggah berkas gagal. ');
                    file.previewElement.remove();
                }
                if (ext == "pdf") {
                    $(file.previewElement).find(".dz-image img").attr("src","/media/img/icon-app/pdf.png");
                    $(file.previewElement).find(".dz-image img").css("display","block");
                    $(file.previewElement).find(".dz-image img").addClass("custom-image");
                    $('#upload1').find('.dz-button').css('display', 'block');
                }
                
            });
            this.on("totaluploadprogress", function(progress) {
            document.querySelector(".dz-progress .dz-upload").style.width = progress + "%";
            });
            this.on("processing", function (file) {
                // this.options.url = lastUrl;
            });

            this.on("sending", function(file, xhr, data) {
                document.querySelector(".dz-progress").style.opacity = "1";
                data.append("nosep", _nosep);
            });

            this.on("success", function (file, response) {
                var res = JSON.parse(response);
                file.file_id = res.id;
                if (res.code !== 200) {
                    $(file.previewElement).find('.dz-error-message span').text(res.message);
                    file.previewElement.classList.add("dz-error");    
                    $(file.previewElement).find(".dz-image").css("background", "none !important");
                } else {
                    file.previewElement.classList.remove("dz-error"); 
                }
            });

            this.on("complete", function (file, response) {

                var newNode = document.createElement('a');
                newNode.setAttribute('data-target', '#modal-preview')
                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${file.name}`)
                newNode.setAttribute('type', 'button')
                newNode.className = 'dz-remove margin-10 btn-cetak';
                newNode.innerHTML = 'DETAIL';
                file.previewTemplate.appendChild(newNode);

                $('.dz-success-mark').css('opacity', '0');
                $('.dz-progress').css('opacity','0');
            });

            this.on("error", function (file, error, xhr) {
                $(file.previewElement).find(".dz-image").css("background", "linear-gradient(to bottom, #eee, #ddd)");

                if (typeof file.xhr !== 'undefined') {
                    $(file.previewElement).find('.dz-error-message span').text(file.xhr.statusText);
                } else {
                    var ext = file.name.split('.').pop();
                    if (ext !== "pdf") {
                        $(file.previewElement).find('.dz-error-message span').text('Berkas tipe ini tidak berlaku');
                        return false;
                    } 
                    if (file.size > 3*1024*1024) {
                        var size = (file.size / 1024 /1024).toFixed(2);
                        $(file.previewElement).find('.dz-error-message span').text('Ukuran berkas terlalu besar ('+ size +'MB). Maksimum: 3MB.');
                    }
                }
            });
        },
        removedfile: function (file) {
            x = confirm('Anda akan menghapus berkas ' + file.name);
            if(!x)  return false;

            if(file.status == "canceled") {
                file.previewElement.remove();
            }

            if (file.accepted) {
                $.ajax({
                    url: 'remove-berkas',
                    type: 'GET',
                    data: { 'file_id': file.file_id, nosep: _nosep},
                    success: function(data) {
                        var {error_no, message} = JSON.parse(data)
                        var _resJson = JSON.parse(data)
                        if(error_no == 'E2009') {
                            docoNotification("error","Proses Gagal !", message);
                            return false
                        }
                        
                        file.previewElement.remove();
                        $('#upload1').find('.dz-button').css('display', 'none');
                    }
                });
            } else {
                file.previewElement.remove();
            }
        }
    });
 
    var upload8New = new Dropzone("#upload8", { // Make the whole body a dropzone
        url: 'upload-berkas',
        method:'post',
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        addRemoveLinks: true,
        dictDefaultMessage: 'Silakan seret dan jatuhkan berkas disini',
        dictRemoveFile: 'HAPUS',
        autoQueue: true,
        clickable: ".fileinput-8",
        acceptedFiles: 'application/pdf',
        params: {label:'kartu_identitas'},
        maxFilesize: 3,
        timeout: 300000, //5 Menit
        parallelUploads:1,
        uploadMultiple: false,
        init: function() {
            this.on('addedfile', function(file) {
                var ext = file.name.split('.').pop();
                if (_final && !_loadBerkas) {
                    x = alert('Status klaim sudah final. Unggah berkas gagal. ');
                    file.previewElement.remove();
                }
                if (ext == "pdf") {
                    $(file.previewElement).find(".dz-image img").attr("src","/media/img/icon-app/pdf.png");
                    $(file.previewElement).find(".dz-image img").css("display","block");
                    $(file.previewElement).find(".dz-image img").addClass("custom-image");
                    $('#upload8').find('.dz-button').css('display', 'block');
                }
                
            });
            this.on("totaluploadprogress", function(progress) {
              document.querySelector(".dz-progress .dz-upload").style.width = progress + "%";
            });
            this.on("processing", function (file) {
                // this.options.url = lastUrl;
            });

            this.on("sending", function(file, xhr, data) {
                document.querySelector(".dz-progress").style.opacity = "1";
                data.append("nosep", _nosep);
            });

            this.on("success", function (file, response) {
                var res = JSON.parse(response);
                file.file_id = res.id;
                if (res.code !== 200) {
                    $(file.previewElement).find('.dz-error-message span').text(res.message);
                    file.previewElement.classList.add("dz-error");    
                    $(file.previewElement).find(".dz-image").css("background", "none !important");
                } else {
                    file.previewElement.classList.remove("dz-error"); 
                }
            });

            this.on("complete", function (file, response) {
                var newNode = document.createElement('a');
                newNode.setAttribute('data-target', '#modal-preview')
                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${file.name}`)
                newNode.setAttribute('type', 'button')
                newNode.className = 'dz-remove margin-10 btn-cetak';
                newNode.innerHTML = 'DETAIL';
                file.previewTemplate.appendChild(newNode);

                $('.dz-success-mark').css('opacity', '0');
                $('.dz-progress').css('opacity','0');
            });

            this.on("error", function (file, error, xhr) {
                $(file.previewElement).find(".dz-image").css("background", "linear-gradient(to bottom, #eee, #ddd)");

                if (typeof file.xhr !== 'undefined') {
                    $(file.previewElement).find('.dz-error-message span').text(file.xhr.statusText);
                } else {
                    var ext = file.name.split('.').pop();
                    if (ext !== "pdf") {
                        $(file.previewElement).find('.dz-error-message span').text('Berkas tipe ini tidak berlaku');
                        return false;
                    } 
                    if (file.size > 3*1024*1024) {
                        var size = (file.size / 1024 /1024).toFixed(2);
                        $(file.previewElement).find('.dz-error-message span').text('Ukuran berkas terlalu besar ('+ size +'MB). Maksimum: 3MB.');
                    }
                }
            });
        },
        removedfile: function (file) {
            x = confirm('Anda akan menghapus berkas ' + file.name);
            if(!x)  return false;

            if(file.status == "canceled") {
                file.previewElement.remove();
            }

            if (file.accepted) {
                $.ajax({
                    url: 'remove-berkas',
                    type: 'GET',
                    data: { 'file_id': file.file_id, nosep: _nosep},
                    success: function(data) {
                        var {error_no, message} = JSON.parse(data)
                        var _resJson = JSON.parse(data)
                        if(error_no == 'E2009') {
                            docoNotification("error","Proses Gagal !", message);
                            return false
                        }
                        
                        file.previewElement.remove();
                        $('#upload1').find('.dz-button').css('display', 'none');
                    }
                });
            } else {
                file.previewElement.remove();
            }
        }
    });


    var bebasbBiaya = new Dropzone("#upload-bebas-biaya", { // Make the whole body a dropzone
        url: 'upload-berkas',
        method:'post',
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        addRemoveLinks: true,
        dictDefaultMessage: 'Silakan seret dan jatuhkan berkas disini',
        dictRemoveFile: 'HAPUS',
        autoQueue: true,
        clickable: ".bebas-biaya-input",
        acceptedFiles: 'application/pdf',
        params: {label:'bebas_biaya'},
        maxFilesize: 3,
        timeout: 300000, //5 Menit
        parallelUploads:1,
        uploadMultiple: false,
        init: function() {
            this.on('previewTemplate', function(file) { 
                alert(22)
            });
            this.on('addedfile', function(file) {
                var ext = file.name.split('.').pop();
                if (_final && !_loadBerkas) {
                    x = alert('Status klaim sudah final. Unggah berkas gagal. ');
                    file.previewElement.remove();
                }
                if (ext == "pdf") {
                    $(file.previewElement).find(".dz-image img").attr("src","/media/img/icon-app/pdf.png");
                    $(file.previewElement).find(".dz-image img").css("display","block");
                    $(file.previewElement).find(".dz-image img").addClass("custom-image");
                    $('#upload-bebas-biaya').find('.dz-button').css('display', 'block');
                }
            });
            this.on("totaluploadprogress", function(progress) {
              document.querySelector(".dz-progress .dz-upload").style.width = progress + "%";
            });
            this.on("processing", function (file) {
                // this.options.url = lastUrl;
            });

            this.on("sending", function(file, xhr, data) {
                document.querySelector(".dz-progress").style.opacity = "1";
                data.append("nosep", _nosep);
            });

            this.on("success", function (file, response) {
                var res = JSON.parse(response);
                file.file_id = res.id;
                if (res.code !== 200) {
                    $(file.previewElement).find('.dz-error-message span').text(res.message);
                    file.previewElement.classList.add("dz-error");    
                    $(file.previewElement).find(".dz-image").css("background", "none !important");
                } else {
                    file.previewElement.classList.remove("dz-error"); 
                }
            });

            this.on("complete", function (file, response) {

                var newNode = document.createElement('a');
                newNode.setAttribute('data-target', '#modal-preview')
                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${file.name}`)
                newNode.setAttribute('type', 'button')
                newNode.className = 'dz-remove margin-10 btn-cetak';
                newNode.innerHTML = 'DETAIL';
                file.previewTemplate.appendChild(newNode);

                $('.dz-success-mark').css('opacity', '0');
                $('.dz-progress').css('opacity','0');
            });

            this.on("error", function (file, error, xhr) {
                $(file.previewElement).find(".dz-image").css("background", "linear-gradient(to bottom, #eee, #ddd)");

                if (typeof file.xhr !== 'undefined') {
                    $(file.previewElement).find('.dz-error-message span').text(file.xhr.statusText);
                } else {
                    var ext = file.name.split('.').pop();
                    if (ext !== "pdf") {
                        $(file.previewElement).find('.dz-error-message span').text('Berkas tipe ini tidak berlaku');
                        return false;
                    } 
                    if (file.size > 3*1024*1024) {
                        var size = (file.size / 1024 /1024).toFixed(2);
                        $(file.previewElement).find('.dz-error-message span').text('Ukuran berkas terlalu besar ('+ size +'MB). Maksimum: 3MB.');
                    }
                }
            });
        },
        removedfile: function (file) {
            x = confirm('Anda akan menghapus berkas ' + file.name);
            if(!x)  return false;

            if(file.status == "canceled") {
                file.previewElement.remove();
            }

            if (file.accepted) {
                $.ajax({
                    url: 'remove-berkas',
                    type: 'GET',
                    data: { 'file_id': file.file_id, nosep: _nosep},
                    success: function(data) {
                        var {error_no, message} = JSON.parse(data)
                        var _resJson = JSON.parse(data)
                        if(error_no == 'E2009') {
                            docoNotification("error","Proses Gagal !", message);
                            return false
                        }

                        file.previewElement.remove();
                        $('#upload1').find('.dz-button').css('display', 'none');
                    }
                });
            } else {
                file.previewElement.remove();
            }
        }
    });

    var dokumenKipi = new Dropzone("#upload-dokumen-kipi", { // Make the whole body a dropzone
        url: 'upload-berkas',
        method:'post',
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        addRemoveLinks: true,
        dictDefaultMessage: 'Silakan seret dan jatuhkan berkas disini',
        dictRemoveFile: 'HAPUS',
        autoQueue: true,
        clickable: ".fileinput-kipi",
        acceptedFiles: 'application/pdf',
        params: {label:'dokumen_kipi'},
        maxFilesize: 3,
        timeout: 300000, //5 Menit
        parallelUploads:1,
        uploadMultiple: false,
        disablePreviews: true,
        init: function() {
            this.on('addedfile', function(file) {
                var ext = file.name.split('.').pop();
                if (_final && !_loadBerkas) {
                    x = alert('Status klaim sudah final. Unggah berkas gagal. ');
                    file.previewElement.remove();
                }
                if (ext == "pdf") {
                    $(file.previewElement).find(".dz-image img").attr("src","/media/img/icon-app/pdf.png");
                    $(file.previewElement).find(".dz-image img").css("display","block");
                    $(file.previewElement).find(".dz-image img").addClass("custom-image");
                    $('#upload-file-kipi').find('.dz-button').css('display', 'block');
                }
                
            });
            this.on("totaluploadprogress", function(progress) {
              document.querySelector(".dz-progress .dz-upload").style.width = progress + "%";
            });
            this.on("processing", function (file) {
                // this.options.url = lastUrl;
            });

            this.on("sending", function(file, xhr, data) {
                document.querySelector(".dz-progress").style.opacity = "1";
                data.append("nosep", _nosep);
            });

            this.on("success", function (file, response) {
                var res = JSON.parse(response);
                file.file_id = res.id;
                if (res.code !== 200) {
                    $(file.previewElement).find('.dz-error-message span').text(res.message);
                    file.previewElement.classList.add("dz-error");    
                    $(file.previewElement).find(".dz-image").css("background", "none !important");
                } else {
                    file.previewElement.classList.remove("dz-error"); 
                }
            });

            this.on("complete", function (file, response) {
                var newNode = document.createElement('a');
                newNode.setAttribute('data-target', '#modal-preview')
                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${file.name}`)
                newNode.setAttribute('type', 'button')
                newNode.className = 'dz-remove margin-10 btn-cetak';
                newNode.innerHTML = 'DETAIL';
                file.previewTemplate.appendChild(newNode);
                
                $('.dz-success-mark').css('opacity', '0');
                $('.dz-progress').css('opacity','0');
            });

            this.on("error", function (file, error, xhr) {
                $(file.previewElement).find(".dz-image").css("background", "linear-gradient(to bottom, #eee, #ddd)");

                if (typeof file.xhr !== 'undefined') {
                    $(file.previewElement).find('.dz-error-message span').text(file.xhr.statusText);
                } else {
                    var ext = file.name.split('.').pop();
                    if (ext !== "pdf") {
                        $(file.previewElement).find('.dz-error-message span').text('Berkas tipe ini tidak berlaku');
                        return false;
                    } 
                    if (file.size > 3*1024*1024) {
                        var size = (file.size / 1024 /1024).toFixed(2);
                        $(file.previewElement).find('.dz-error-message span').text('Ukuran berkas terlalu besar ('+ size +'MB). Maksimum: 3MB.');
                    }
                }
            });
        },
        removedfile: function (file) {
            x = confirm('Anda akan menghapus berkas ' + file.name);
            if(!x)  return false;

            if(file.status == "canceled") {
                file.previewElement.remove();
            }

            if (file.accepted) {
                $.ajax({
                    url: 'remove-berkas',
                    type: 'GET',
                    data: { 'file_id': file.file_id, nosep: _nosep},
                    success: function(data) {
                        var {error_no, message} = JSON.parse(data)
                        var _resJson = JSON.parse(data)
                        if(error_no == 'E2009') {
                            docoNotification("error","Proses Gagal !", message);
                            return false
                        }

                        file.previewElement.remove();
                        $('#upload1').find('.dz-button').css('display', 'none');
                    }
                });
            } else {
                file.previewElement.remove();
            }
        }
    });


     var upload2 = new Dropzone("#upload2", { // Make the whole body a dropzone
         url: 'upload-berkas',
         method:'post',
         thumbnailWidth: 80,
         thumbnailHeight: 80,
         addRemoveLinks: true,
         dictDefaultMessage: 'Silakan seret dan jatuhkan berkas disini',
         dictRemoveFile: 'HAPUS',
         autoQueue: true,
         clickable: ".fileinput-2",
         acceptedFiles: 'application/pdf',
         params: {label:'ruang_rawat'},
         maxFilesize: 3,
         timeout: 300000,
         parallelUploads:1,
         uploadMultiple: false,
         init: function() {
             this.on('addedfile', function(file) {
                 var ext = file.name.split('.').pop();
                 if (_final && !_loadBerkas) {
                     x = alert('Status klaim sudah final. Unggah berkas gagal. ');
                     file.previewElement.remove();
                 }
                 if (ext == "pdf") {
                     $(file.previewElement).find(".dz-image img").attr("src","/media/img/icon-app/pdf.png");
                     $(file.previewElement).find(".dz-image img").css("display","block");
                     $(file.previewElement).find(".dz-image img").addClass("custom-image");
                     $('#upload2').find('.dz-button').css('display', 'block');
                 } 
             });
 
             this.on("processing", function (file) {
                 // this.options.url = lastUrl;
             });
 
             this.on("sending", function(file, xhr, data) {
                 data.append("nosep", _nosep);
             });
 
             this.on("success", function (file, response) {
                 var res = JSON.parse(response);
                 file.file_id = res.id;
                 if (res.code !== 200) {
                     $(file.previewElement).find('.dz-error-message span').text(res.message);
                     file.previewElement.classList.add("dz-error");    
                 }
             });
 
             this.on("complete", function (file, response) {
                var newNode = document.createElement('a');
                newNode.setAttribute('data-target', '#modal-preview')
                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${file.name}`)
                newNode.setAttribute('type', 'button')
                newNode.className = 'dz-remove margin-10 btn-cetak';
                newNode.innerHTML = 'DETAIL';
                file.previewTemplate.appendChild(newNode);

                 $('.dz-success-mark').css('opacity', '0');
                 $('.dz-progress').css('opacity','0');
             });
 
             this.on("error", function (file, error, xhr) {
                 $(file.previewElement).find(".dz-image").css("background", "linear-gradient(to bottom, #eee, #ddd)");
 
                 if (typeof file.xhr !== 'undefined') {
                     $(file.previewElement).find('.dz-error-message span').text(file.xhr.statusText);
                 } else {
                     var ext = file.name.split('.').pop();
                     if (ext !== "pdf") {
                         $(file.previewElement).find('.dz-error-message span').text('Berkas tipe ini tidak berlaku');
                         return false;
                     } 
                     if (file.size > 3*1024*1024) {
                         var size = (file.size / 1024 /1024).toFixed(2);
                         $(file.previewElement).find('.dz-error-message span').text('Ukuran berkas terlalu besar ('+ size +'MB). Maksimum: 3MB.');
                     }
                 }
             });
         },
         removedfile: function (file) {
             x = confirm('Anda akan menghapus berkas ' + file.name);
             if(!x)  return false;

            if(file.status == "canceled") {
                file.previewElement.remove();
            }

             if (file.accepted) {
                 $.ajax({
                     url: 'remove-berkas',
                     type: 'GET',
                     data: { 'file_id': file.file_id, nosep: _nosep},
                     success: function(data) {
                        var {error_no, message} = JSON.parse(data)
                        var _resJson = JSON.parse(data)
                        if(error_no == 'E2009') {
                            docoNotification("error","Proses Gagal !", message);
                            return false
                        }
                         
                        file.previewElement.remove();
                        $('#upload2').find('.dz-button').css('display', 'none');
                     }
                 });
             } else {
                 file.previewElement.remove();
             }
         }
     });
 
     var upload3 = new Dropzone("#upload3", { // Make the whole body a dropzone
         url: 'upload-berkas',
         method:'post',
         thumbnailWidth: 80,
         thumbnailHeight: 80,
         addRemoveLinks: true,
         dictDefaultMessage: 'Silakan seret dan jatuhkan berkas disini',
         dictRemoveFile: 'HAPUS',
         autoQueue: true,
         clickable: ".fileinput-3",
         acceptedFiles: 'application/pdf',
         params: {label:'laboratorium'},
         maxFilesize: 3,
         timeout: 300000,
         parallelUploads:1,
         uploadMultiple: false,
         init: function() {
             this.on('addedfile', function(file) {
                 var ext = file.name.split('.').pop();
                 if (_final && !_loadBerkas) {
                     x = alert('Status klaim sudah final. Unggah berkas gagal. ');
                     file.previewElement.remove();
                 }
                 if (ext == "pdf") {
                     $(file.previewElement).find(".dz-image img").attr("src","/media/img/icon-app/pdf.png");
                     $(file.previewElement).find(".dz-image img").css("display","block");
                     $(file.previewElement).find(".dz-image img").addClass("custom-image");
                     $('#upload3').find('.dz-button').css('display', 'block');
                 } 
             });
 
             this.on("processing", function (file) {
                 // this.options.url = lastUrl;
             });
 
             this.on("sending", function(file, xhr, data) {
                 data.append("nosep", _nosep);
             });
 
             this.on("success", function (file, response) {
                 var res = JSON.parse(response);
                 file.file_id = res.id;
                 if (res.code !== 200) {
                     $(file.previewElement).find('.dz-error-message span').text(res.message);
                     file.previewElement.classList.add("dz-error");    
                 }
             });
 
             this.on("complete", function (file, response) {
                var newNode = document.createElement('a');
                newNode.setAttribute('data-target', '#modal-preview')
                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${file.name}`)
                newNode.setAttribute('type', 'button')
                newNode.className = 'dz-remove margin-10 btn-cetak';
                newNode.innerHTML = 'DETAIL';
                file.previewTemplate.appendChild(newNode);

                $('.dz-success-mark').css('opacity', '0');
                $('.dz-progress').css('opacity','0');
             });
 
             this.on("error", function (file, error, xhr) {
                 $(file.previewElement).find(".dz-image").css("background", "linear-gradient(to bottom, #eee, #ddd)");
 
                 if (typeof file.xhr !== 'undefined') {
                     $(file.previewElement).find('.dz-error-message span').text(file.xhr.statusText);
                 } else {
                     var ext = file.name.split('.').pop();
                     if (ext !== "pdf") {
                         $(file.previewElement).find('.dz-error-message span').text('Berkas tipe ini tidak berlaku');
                         return false;
                     } 
                     if (file.size > 3*1024*1024) {
                         var size = (file.size / 1024 /1024).toFixed(2);
                         $(file.previewElement).find('.dz-error-message span').text('Ukuran berkas terlalu besar ('+ size +'MB). Maksimum: 3MB.');
                     }
                 }
             });
         },
         removedfile: function (file) {
              x = confirm('Anda akan menghapus berkas ' + file.name);
             if(!x)  return false;
             
             if(file.status == "canceled") {
                file.previewElement.remove(); 
             }

             if (file.accepted) {
                 $.ajax({
                     url: 'remove-berkas',
                     type: 'GET',
                     data: { 'file_id': file.file_id, nosep: _nosep},
                     success: function(data) {
                        var {error_no, message} = JSON.parse(data)
                        var _resJson = JSON.parse(data)
                        if(error_no == 'E2009') {
                            docoNotification("error","Proses Gagal !", message);
                            return false
                        }

                        file.previewElement.remove();
                        $('#upload3').find('.dz-button').css('display', 'none');
                     }
                 });
             } else {
                 file.previewElement.remove();
             }
         }
     });
 
     var upload4 = new Dropzone("#upload4", { // Make the whole body a dropzone
         url: 'upload-berkas',
         method:'post',
         thumbnailWidth: 80,
         thumbnailHeight: 80,
         addRemoveLinks: true,
         dictDefaultMessage: 'Silakan seret dan jatuhkan berkas disini',
         dictRemoveFile: 'HAPUS',
         autoQueue: true,
         clickable: ".fileinput-4",
         acceptedFiles: 'application/pdf',
         params: {label:'radiologi'},
         maxFilesize: 3,
         timeout: 300000,
         parallelUploads:1,
         uploadMultiple: false,
         init: function() {
             this.on('addedfile', function(file) {
                 var ext = file.name.split('.').pop();
                 if (_final && !_loadBerkas) {
                     x = alert('Status klaim sudah final. Unggah berkas gagal. ');
                     file.previewElement.remove();
                 }
                 if (ext == "pdf") {
                     $(file.previewElement).find(".dz-image img").attr("src","/media/img/icon-app/pdf.png");
                     $(file.previewElement).find(".dz-image img").css("display","block");
                     $(file.previewElement).find(".dz-image img").addClass("custom-image");
                     $('#upload4').find('.dz-button').css('display', 'block');
                 } 
             });
 
             this.on("processing", function (file) {
                 // this.options.url = lastUrl;
             });
 
             this.on("sending", function(file, xhr, data) {
                 data.append("nosep", _nosep);
             });
 
             this.on("success", function (file, response) {
                 var res = JSON.parse(response);
                 file.file_id = res.id;
                 if (res.code !== 200) {
                     $(file.previewElement).find('.dz-error-message span').text(res.message);
                     file.previewElement.classList.add("dz-error");    
                 }
             });
 
             this.on("complete", function (file, response) {
                var newNode = document.createElement('a');
                newNode.setAttribute('data-target', '#modal-preview')
                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${file.name}`)
                newNode.setAttribute('type', 'button')
                newNode.className = 'dz-remove margin-10 btn-cetak';
                newNode.innerHTML = 'DETAIL';
                file.previewTemplate.appendChild(newNode);

                $('.dz-success-mark').css('opacity', '0');
                $('.dz-progress').css('opacity','0');
             });
 
             this.on("error", function (file, error, xhr) {
                 $(file.previewElement).find(".dz-image").css("background", "linear-gradient(to bottom, #eee, #ddd)");
 
                 if (typeof file.xhr !== 'undefined') {
                     $(file.previewElement).find('.dz-error-message span').text(file.xhr.statusText);
                 } else {
                     var ext = file.name.split('.').pop();
                     if (ext !== "pdf") {
                         $(file.previewElement).find('.dz-error-message span').text('Berkas tipe ini tidak berlaku');
                         return false;
                     } 
                     if (file.size > 3*1024*1024) {
                         var size = (file.size / 1024 /1024).toFixed(2);
                         $(file.previewElement).find('.dz-error-message span').text('Ukuran berkas terlalu besar ('+ size +'MB). Maksimum: 3MB.');
                     }
                 }
             });
         },
         removedfile: function (file) {
              x = confirm('Anda akan menghapus berkas ' + file.name);
             if(!x)  return false;

             if(file.status == "canceled") {
                file.previewElement.remove();
             }

             if (file.accepted) {
                 $.ajax({
                     url: 'remove-berkas',
                     type: 'GET',
                     data: { 'file_id': file.file_id, nosep: _nosep},
                     success: function(data) {
                        var {error_no, message} = JSON.parse(data)
                        var _resJson = JSON.parse(data)
                        if(error_no == 'E2009') {
                            docoNotification("error","Proses Gagal !", message);
                            return false
                        }
                         
                        file.previewElement.remove();
                        $('#upload4').find('.dz-button').css('display', 'none');
                     }
                 });
             } else {
                 file.previewElement.remove();
             }
         }
     });
 
     var upload5 = new Dropzone("#upload5", { // Make the whole body a dropzone
         url: 'upload-berkas',
         method:'post',
         thumbnailWidth: 80,
         thumbnailHeight: 80,
         addRemoveLinks: true,
         dictDefaultMessage: 'Silakan seret dan jatuhkan berkas disini',
         dictRemoveFile: 'HAPUS',
         autoQueue: true,
         clickable: ".fileinput-5",
         acceptedFiles: 'application/pdf',
         params: {label:'penunjang_lain'},
         maxFilesize: 3,
         timeout: 300000,
         parallelUploads:1,
         uploadMultiple: false,
         init: function() {
             this.on('addedfile', function(file) {
                 var ext = file.name.split('.').pop();
                 if (_final && !_loadBerkas) {
                     x = alert('Status klaim sudah final. Unggah berkas gagal. ');
                     file.previewElement.remove();
                 }
                 if (ext == "pdf") {
                     $(file.previewElement).find(".dz-image img").attr("src","/media/img/icon-app/pdf.png");
                     $(file.previewElement).find(".dz-image img").css("display","block");
                     $(file.previewElement).find(".dz-image img").addClass("custom-image");
                     $('#upload5').find('.dz-button').css('display', 'block');
                 } 
             });
 
             this.on("processing", function (file) {
                 // this.options.url = lastUrl;
             });
 
             this.on("sending", function(file, xhr, data) {
                 data.append("nosep", _nosep);
             });
 
             this.on("success", function (file, response) {
                 var res = JSON.parse(response);
                 file.file_id = res.id;
                 if (res.code !== 200) {
                     $(file.previewElement).find('.dz-error-message span').text(res.message);
                     file.previewElement.classList.add("dz-error");    
                 }
             });
 
             this.on("complete", function (file, response) {
                var newNode = document.createElement('a');
                newNode.setAttribute('data-target', '#modal-preview')
                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${file.name}`)
                newNode.setAttribute('type', 'button')
                newNode.className = 'dz-remove margin-10 btn-cetak';
                newNode.innerHTML = 'DETAIL';
                file.previewTemplate.appendChild(newNode);

                 $('.dz-success-mark').css('opacity', '0');
                 $('.dz-progress').css('opacity','0');
             });
 
             this.on("error", function (file, error, xhr) {
                 $(file.previewElement).find(".dz-image").css("background", "linear-gradient(to bottom, #eee, #ddd)");
 
                 if (typeof file.xhr !== 'undefined') {
                     $(file.previewElement).find('.dz-error-message span').text(file.xhr.statusText);
                 } else {
                     var ext = file.name.split('.').pop();
                     if (ext !== "pdf") {
                         $(file.previewElement).find('.dz-error-message span').text('Berkas tipe ini tidak berlaku');
                         return false;
                     } 
                     if (file.size > 3*1024*1024) {
                         var size = (file.size / 1024 /1024).toFixed(2);
                         $(file.previewElement).find('.dz-error-message span').text('Ukuran berkas terlalu besar ('+ size +'MB). Maksimum: 3MB.');
                     }
                 }
             });
         },
         removedfile: function (file) {
              x = confirm('Anda akan menghapus berkas ' + file.name);
             if(!x)  return false;

             if(file.status == "canceled") {
                file.previewElement.remove();
             }

             if (file.accepted) {
                 $.ajax({
                     url: 'remove-berkas',
                     type: 'GET',
                     data: { 'file_id': file.file_id, nosep: _nosep},
                     success: function(data) {
                        var {error_no, message} = JSON.parse(data)
                        var _resJson = JSON.parse(data)
                        if(error_no == 'E2009') {
                            docoNotification("error","Proses Gagal !", message);
                            return false
                        }
                         
                        file.previewElement.remove();
                        $('#upload5').find('.dz-button').css('display', 'none');
                     }
                 });
             } else {
                 file.previewElement.remove();
             }
         }
     });
 
     var upload6 = new Dropzone("#upload6", { // Make the whole body a dropzone
         url: 'upload-berkas',
         method:'post',
         thumbnailWidth: 80,
         thumbnailHeight: 80,
         addRemoveLinks: true,
         dictDefaultMessage: 'Silakan seret dan jatuhkan berkas disini',
         dictRemoveFile: 'HAPUS',
         autoQueue: true,
         clickable: ".fileinput-6",
         acceptedFiles: 'application/pdf',
         params: {label:'resep_obat'},
         maxFilesize: 3,
         timeout: 300000,
         parallelUploads:1,
         uploadMultiple: false,
         init: function() {
             this.on('addedfile', function(file) {
                 var ext = file.name.split('.').pop();
                 if (_final && !_loadBerkas) {
                     x = alert('Status klaim sudah final. Unggah berkas gagal. ');
                     file.previewElement.remove();
                 }
                 if (ext == "pdf") {
                     $(file.previewElement).find(".dz-image img").attr("src","/media/img/icon-app/pdf.png");
                     $(file.previewElement).find(".dz-image img").css("display","block");
                     $(file.previewElement).find(".dz-image img").addClass("custom-image");
                     $('#upload6').find('.dz-button').css('display', 'block');
                 } 
             });
 
             this.on("processing", function (file) {
                 // this.options.url = lastUrl;
             });
 
             this.on("sending", function(file, xhr, data) {
                 data.append("nosep", _nosep);
             });
 
             this.on("success", function (file, response) {
                 var res = JSON.parse(response);
                 file.file_id = res.id;
                 if (res.code !== 200) {
                     $(file.previewElement).find('.dz-error-message span').text(res.message);
                     file.previewElement.classList.add("dz-error");    
                 }
             });
 
             this.on("complete", function (file, response) {
                var newNode = document.createElement('a');
                newNode.setAttribute('data-target', '#modal-preview')
                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${file.name}`)
                newNode.setAttribute('type', 'button')
                newNode.className = 'dz-remove margin-10 btn-cetak';
                newNode.innerHTML = 'DETAIL';
                file.previewTemplate.appendChild(newNode);

                 $('.dz-success-mark').css('opacity', '0');
                 $('.dz-progress').css('opacity','0');
             });
 
             this.on("error", function (file, error, xhr) {
                 $(file.previewElement).find(".dz-image").css("background", "linear-gradient(to bottom, #eee, #ddd)");
 
                 if (typeof file.xhr !== 'undefined') {
                     $(file.previewElement).find('.dz-error-message span').text(file.xhr.statusText);
                 } else {
                     var ext = file.name.split('.').pop();
                     if (ext !== "pdf") {
                         $(file.previewElement).find('.dz-error-message span').text('Berkas tipe ini tidak berlaku');
                         return false;
                     } 
                     if (file.size > 3*1024*1024) {
                         var size = (file.size / 1024 /1024).toFixed(2);
                         $(file.previewElement).find('.dz-error-message span').text('Ukuran berkas terlalu besar ('+ size +'MB). Maksimum: 3MB.');
                     }
                 }
             });
         },
         removedfile: function (file) {
              x = confirm('Anda akan menghapus berkas ' + file.name);
             if(!x)  return false;

             if(file.status == "canceled") {
                file.previewElement.remove();
             }

             if (file.accepted) {
                 $.ajax({
                     url: 'remove-berkas',
                     type: 'GET',
                     data: { 'file_id': file.file_id, nosep: _nosep},
                     success: function(data) {
                        var {error_no, message} = JSON.parse(data)
                        var _resJson = JSON.parse(data)
                        if(error_no == 'E2009') {
                            docoNotification("error","Proses Gagal !", message);
                            return false
                        }
                        
                        file.previewElement.remove();
                        $('#upload6').find('.dz-button').css('display', 'none');
                     }
                 });
             } else {
                 file.previewElement.remove();
             }
         }
     });
 
     var upload7 = new Dropzone("#upload7", { // Make the whole body a dropzone
         url: 'upload-berkas',
         method:'post',
         thumbnailWidth: 80,
         thumbnailHeight: 80,
         addRemoveLinks: true,
         dictDefaultMessage: 'Silakan seret dan jatuhkan berkas disini',
         dictRemoveFile: 'HAPUS',
         autoQueue: true,
         clickable: ".fileinput-7",
         acceptedFiles: 'application/pdf',
         params: {label:'tagihan'},
         maxFilesize: 3,
         timeout: 300000,
         parallelUploads:1,
         uploadMultiple: false,
         init: function() {
             this.on('addedfile', function(file) {
                 var ext = file.name.split('.').pop();
                 if (_final && !_loadBerkas) {
                     x = alert('Status klaim sudah final. Unggah berkas gagal. ');
                     file.previewElement.remove();
                 }
                 if (ext == "pdf") {
                     $(file.previewElement).find(".dz-image img").attr("src","/media/img/icon-app/pdf.png");
                     $(file.previewElement).find(".dz-image img").css("display","block");
                     $(file.previewElement).find(".dz-image img").addClass("custom-image");
                     $('#upload7').find('.dz-button').css('display', 'block');
                 } 
             });
 
             this.on("processing", function (file) {
                 // this.options.url = lastUrl;
             });
 
             this.on("sending", function(file, xhr, data) {
                 data.append("nosep", _nosep);
             });
 
             this.on("success", function (file, response) {
                 var res = JSON.parse(response);
                 file.file_id = res.id;
                 if (res.code !== 200) {
                     $(file.previewElement).find('.dz-error-message span').text(res.message);
                     file.previewElement.classList.add("dz-error");    
                 }
             });
 
             this.on("complete", function (file, response) {
                var newNode = document.createElement('a');
                newNode.setAttribute('data-target', '#modal-preview')
                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${file.name}`)
                newNode.setAttribute('type', 'button')
                newNode.className = 'dz-remove margin-10 btn-cetak';
                newNode.innerHTML = 'DETAIL';
                file.previewTemplate.appendChild(newNode);

                 $('.dz-success-mark').css('opacity', '0');
                 $('.dz-progress').css('opacity','0');
             });
 
             this.on("error", function (file, error, xhr) {
                 $(file.previewElement).find(".dz-image").css("background", "linear-gradient(to bottom, #eee, #ddd)");
 
                 if (typeof file.xhr !== 'undefined') {
                     $(file.previewElement).find('.dz-error-message span').text(file.xhr.statusText);
                 } else {
                     var ext = file.name.split('.').pop();
                     if (ext !== "pdf") {
                         $(file.previewElement).find('.dz-error-message span').text('Berkas tipe ini tidak berlaku');
                         return false;
                     } 
                     if (file.size > 3*1024*1024) {
                         var size = (file.size / 1024 /1024).toFixed(2);
                         $(file.previewElement).find('.dz-error-message span').text('Ukuran berkas terlalu besar ('+ size +'MB). Maksimum: 3MB.');
                     }
                 }
             });
         },
         removedfile: function (file) {
              x = confirm('Anda akan menghapus berkas ' + file.name);
             if(!x)  return false;

             if(file.status == "canceled") {
                file.previewElement.remove();
             }

             if (file.accepted) {
                 $.ajax({
                     url: 'remove-berkas',
                     type: 'GET',
                     data: { 'file_id': file.file_id, nosep: _nosep},
                     success: function(data) {
                        var {error_no, message} = JSON.parse(data)
                        var _resJson = JSON.parse(data)
                        if(error_no == 'E2009') {
                            docoNotification("error","Proses Gagal !", message);
                            return false
                        }
                         
                        file.previewElement.remove();
                        $('#upload7').find('.dz-button').css('display', 'none');
                     }
                 });
             } else {
                 file.previewElement.remove();
             }
         }
     });
 

     var upload9 = new Dropzone("#upload9", { // Make the whole body a dropzone
         url: 'upload-berkas',
         method:'post',
         thumbnailWidth: 80,
         thumbnailHeight: 80,
         addRemoveLinks: true,
         dictDefaultMessage: 'Silakan seret dan jatuhkan berkas disini',
         dictRemoveFile: 'HAPUS',
         autoQueue: true,
         clickable: ".fileinput-9",
         acceptedFiles: 'application/pdf',
         params: {label:'lain_lain'},
         maxFilesize: 3,
         timeout: 300000,
         parallelUploads:1,
         uploadMultiple: false,
         init: function() {
             this.on('addedfile', function(file) {
                 var ext = file.name.split('.').pop();
                 if (_final && !_loadBerkas) {
                     x = alert('Status klaim sudah final. Unggah berkas gagal. ');
                     file.previewElement.remove();
                 }
                 if (ext == "pdf") {
                     $(file.previewElement).find(".dz-image img").attr("src","/media/img/icon-app/pdf.png");
                     $(file.previewElement).find(".dz-image img").css("display","block");
                     $(file.previewElement).find(".dz-image img").addClass("custom-image");
                     $('#upload9').find('.dz-button').css('display', 'block');
                 } 
             });
 
             this.on("processing", function (file) {
                 // this.options.url = lastUrl;
             });
 
             this.on("sending", function(file, xhr, data) {
                 data.append("nosep", _nosep);
             });
 
             this.on("success", function (file, response) {
                 var res = JSON.parse(response);
                 file.file_id = res.id;
                 if (res.code !== 200) {
                     $(file.previewElement).find('.dz-error-message span').text(res.message);
                     file.previewElement.classList.add("dz-error");    
                 }
             });
 
             this.on("complete", function (file, response) {
                var newNode = document.createElement('a');
                newNode.setAttribute('data-target', '#modal-preview')
                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${file.name}`)
                newNode.setAttribute('type', 'button')
                newNode.className = 'dz-remove margin-10 btn-cetak';
                newNode.innerHTML = 'DETAIL';
                file.previewTemplate.appendChild(newNode);

                 $('.dz-success-mark').css('opacity', '0');
                 $('.dz-progress').css('opacity','0');
             });
 
             this.on("error", function (file, error, xhr) {
                 $(file.previewElement).find(".dz-image").css("background", "linear-gradient(to bottom, #eee, #ddd)");
 
                 if (typeof file.xhr !== 'undefined') {
                     $(file.previewElement).find('.dz-error-message span').text(file.xhr.statusText);
                 } else {
                     var ext = file.name.split('.').pop();
                     if (ext !== "pdf") {
                         $(file.previewElement).find('.dz-error-message span').text('Berkas tipe ini tidak berlaku');
                         return false;
                     } 
                     if (file.size > 3*1024*1024) {
                         var size = (file.size / 1024 /1024).toFixed(2);
                         $(file.previewElement).find('.dz-error-message span').text('Ukuran berkas terlalu besar ('+ size +'MB). Maksimum: 3MB.');
                     }
                 }
             });
         },
         removedfile: function (file) {
              x = confirm('Anda akan menghapus berkas ' + file.name);
             if(!x)  return false;

             if(file.status == "canceled") {
                file.previewElement.remove();
             }

             if (file.accepted) {
                 $.ajax({
                     url: 'remove-berkas',
                     type: 'GET',
                     data: { 'file_id': file.file_id, nosep: _nosep},
                     success: function(data) {
                        var {error_no, message} = JSON.parse(data)
                        var _resJson = JSON.parse(data)
                        if(error_no == 'E2009') {
                            docoNotification("error","Proses Gagal !", message);
                            return false
                        }

                        file.previewElement.remove();
                        $('#upload9').find('.dz-button').css('display', 'none');
                     }
                 });
             } else {
                 file.previewElement.remove();
             }
         }
     });
};


$(document).on('click', '.btn-cetak', ({ currentTarget }) => {
    const dataBtn = $(currentTarget).data()
    if (typeof dataBtn.url != 'undefined' && dataBtn.url != null && dataBtn != '') {
        // $("#modal_riwayat").css('z-index', '1040')
        $('#modal-preview').data('url', dataBtn.url)
        $("#modal-preview").modal({
            backdrop: 'static',
            keyboard: false
        })
    }
})

$('#klaiminacbgranapform-transfusi_darah').on('change', function (e) {
    koreksiTransfusiDarah()
})

$('#klaiminacbgranapform-dializer').on('change', function (e) {
    koreksiTransfusiDarah()
})

$('#modal-preview').on('shown.bs.modal', function () {
    $("#preview-content").attr('src', $('#modal-preview').data('url'))
})

$('#edit-koreksi').click(function (e) { 
    e.preventDefault();
    $('.modal-content').empty()
 });

function formDokterMultiple(){
    if (isDokterMultiple == 'true') {
        $('#dokter_new').prop('disabled', true)
        $('#dokter_new_multiple').prop('disabled', false)
        $('div').remove('#dokter-single')
    } else {
        $('#dokter_new').prop('disabled', false)
        $('#dokter_new_multiple').prop('disabled', true)
        $('div').remove('#dokter-multiple')
    }
}

function cleanCovid() {
    $('.dz-preview').remove();
    $('.dz-button').css('display', 'none');
    _nosep = $('#no_klaimcovid').val();
    _getBerkas = false;

    $('#no_klaimcovid').empty()
    _klaimCovidEps = []

    let dataPasien = {
        'kunjunganId': kunjunganId,
        'noKartu': '',
        'nosep' : nosep,
        'noRm' : noRm,
        'namaPasien': namaPasien,
        'tglLahir': $('#klaiminacbgranapform-tgl_lahir').val(),
        'gender': $('#klaiminacbgranapform-jeniskelamin').val(),
        'jenisIdentitas': $('#klaiminacbgranapform-identitas_id').val(),
        'noIdentitas' : $('#identitas_value').val()
    }
    generateNoCovid(dataPasien)
}
/**
 * Function untuk menghandle ketika grouping null 
 * @param {*} _res 
 */
function handlingGrouperNull(_res) {
    let status_db_inacbg = _res.response?.status_inacbg
    if(_res.response.data.grouper.response_inacbg != null && status_db_inacbg != 2502) {
        $('#proses-final-klaim').removeClass('hidden');
        $('.pasien_tb').prop('disabled', true)
    }

    if(_res.response.data.grouper.response_inacbg == null) {
        $('#edit-koreksi').attr('disabled', false)
    }
}

function updateNoKlaim()
{
    let jaminan = $('#klaiminacbgranapform-klaim_penjamin').val()
    let isJaminan = false
    if(arrayJaminan.includes(jaminan)) {
        isJaminan = true
    }

    $.ajax({
        type: "GET",
        url: "/penjamin-asuransi/informasi-pasien-ranap-bpjs/update-noklaim",
        data: { "kunjungan_id": kunjunganId, "is_jaminan": isJaminan },
        success: function (response) {
            if(response?.status == 200) {
                $('#no_klaimcovid').val(null) 
            }
        },
        error: function(error) {
            docoNotification('warning', 'Peringatan', error);
        }
    });
}

function disableTanggalKeluar(condition) {
    if(condition == true) {
        var masuk = $('#klaiminacbgranapform-tgl_masuk').val();
        $('#klaiminacbgranapform-tgl_keluar').addClass('disabled-keluar')
        $('#klaiminacbgranapform-tgl_keluar').val(masuk);

    }else{
        $('#klaiminacbgranapform-tgl_keluar').removeClass('disabled-keluar')
    }
}

function disablekategoriHemodialysis(condition) {
    if(condition == true) {
        $('.kategoriHemodialysis').removeClass('hidden')
    }else {
        $('.kategoriHemodialysis').addClass('hidden')
    }
}

function hiddenEksekutifJkn(condition) {
    if(condition == true) {
        $('.section-eksekutif').addClass('hidden')
        $('#klaiminacbgranapform-kelas_eksekutif').prop('checked', false)
        $('#klaiminacbgranapform-kelas_eksekutif').trigger('change')
    }else{
        $('.section-eksekutif').removeClass('hidden')

    }
}

function hiddenRawatIntensif(condition) {
    if(condition == true) {
        $('.section-rawat-intensif').addClass('hidden')
        $('#klaiminacbgranapform-is_rawatintensif').prop('checked', false)
        $('.hide-rawat-intensif').addClass('hidden');
    }else {
        $('.section-rawat-intensif').removeClass('hidden')

    }
}

function hiddenNaikturunKelas(condition) {
    if(condition == true) {
        $('.section-naik-turunkelas').addClass('hidden')
        $('#klaiminacbgranapform-is_naikkelas').prop('checked', false)
        $('.hide-naik-kelas').addClass('hidden');
    }else{
        $('.section-naik-turunkelas').removeClass('hidden')

    }
}

function hiddenJenisKelasRawat(condition) {
    if(condition == true) {
        $('.section-jenis-kelas-rawat').addClass('hidden')
    }else {
        $('.section-jenis-kelas-rawat').removeClass('hidden')

    }
}
/**
 * Fungsi untuk melakukan handle mapping data, source data berasal dari e-klaim langsung.
 * @param {JSON} _res 
 */
function handlingTindakanTarif(_res) {
    let prosedurBedah = _res.response.data?.tarif_rs?.prosedur_bedah
    let alkes = _res.response.data?.tarif_rs?.alkes
    let bmhp = _res.response.data?.tarif_rs?.bmhp
    let keperawatan = _res.response.data?.tarif_rs?.keperawatan
    let konsultasi = _res.response.data?.tarif_rs?.konsultasi
    let laboratorium = _res.response.data?.tarif_rs?.laboratorium
    let obat = _res.response.data?.tarif_rs?.obat
    let obat_kemoterapi = _res.response.data?.tarif_rs?.obat_kemoterapi
    let obat_kronis = _res.response.data?.tarif_rs?.obat_kronis
    let pelayanan_darah = _res.response.data?.tarif_rs?.pelayanan_darah
    let penunjang = _res.response.data?.tarif_rs?.penunjang
    let prosedur_non_bedah = _res.response.data?.tarif_rs?.prosedur_non_bedah
    let radiologi = _res.response.data?.tarif_rs?.radiologi
    let rawat_intensif = _res.response.data?.tarif_rs?.rawat_intensif
    let rehabilitasi = _res.response.data?.tarif_rs?.rehabilitasi
    let sewa_alat = _res.response.data?.tarif_rs?.sewa_alat
    let tenaga_ahli = _res.response.data?.tarif_rs?.tenaga_ahli
    let kamar = _res.response.data?.tarif_rs?.kamar

    if(prosedurBedah > 0) {
        prosedurBedah = convertRupiah(prosedurBedah)
        $("#klaiminacbgranapform-prosedur_bedah").val(prosedurBedah)
    }

    if(tenaga_ahli > 0) {
        tenaga_ahli = convertRupiah(tenaga_ahli)
        $("#klaiminacbgranapform-tenaga_ahli").val(tenaga_ahli)
    }

    if(radiologi > 0) {
        radiologi = convertRupiah(radiologi)
        $("#klaiminacbgranapform-radiologi").val(radiologi)
    }

    if(rehabilitasi > 0) {
        radiologi = convertRupiah(radiologi)
        $("#klaiminacbgranapform-rehabilitasi").val(rehabilitasi)
    }

    if(obat > 0) {
        radiologi = convertRupiah(radiologi)
        $("#klaiminacbgranapform-obat").val(obat)
    }

    if(sewa_alat > 0) {
        radiologi = convertRupiah(radiologi)
        $("#klaiminacbgranapform-sewa_alat").val(sewa_alat)
    }

    if(prosedur_non_bedah > 0) {
        prosedur_non_bedah = convertRupiah(prosedur_non_bedah)
        $("#klaiminacbgranapform-prosedur_nonbedah").val(prosedur_non_bedah)
    }

    if(keperawatan > 0) {
        keperawatan = convertRupiah(keperawatan)
        $("#klaiminacbgranapform-keperawatan").val(keperawatan)
    }

    if(laboratorium > 0) {
        laboratorium = convertRupiah(laboratorium)
        $("#klaiminacbgranapform-laboratorium").val(laboratorium)
    }

    if(kamar > 0) {
        kamar = convertRupiah(kamar)
        $("#klaiminacbgranapform-kamar_akomodasi").val(kamar)
    }

    if(alkes > 0) {
        alkes = convertRupiah(alkes)
        $("#klaiminacbgranapform-alkes").val(alkes)
    }

    if(obat_kemoterapi > 0) {
        obat_kemoterapi = convertRupiah(obat_kemoterapi)
        $("#klaiminacbgranapform-obat_kemoterapi").val(obat_kemoterapi)
    }

    if(konsultasi > 0) {
        konsultasi = convertRupiah(konsultasi)
        $("#klaiminacbgranapform-konsultasi").val(konsultasi)
    }

    if(penunjang > 0) {
        penunjang = convertRupiah(penunjang)
        $("#klaiminacbgranapform-penunjang").val(penunjang)
    }

    if(pelayanan_darah > 0) {
        pelayanan_darah = convertRupiah(pelayanan_darah)
        $("#klaiminacbgranapform-pelayanan_darah").val(pelayanan_darah)
    }

    if(rawat_intensif > 0) {
        rawat_intensif = convertRupiah(rawat_intensif)
        $("#klaiminacbgranapform-rawat_intensif").val(rawat_intensif)
    }

    if(bmhp > 0) {
        bmhp = convertRupiah(bmhp)
        $("#klaiminacbgranapform-bmhp").val(bmhp)
    }

    if(obat_kronis > 0) {
        obat_kronis = convertRupiah(obat_kronis)
        $("#klaiminacbgranapform-obat_kronis").val(obat_kronis)
    }
}

function convertRupiah(tarif) {
    var formatter = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
    return formatter.format(tarif)
}

/**
 * Fungsi untuk melakukan reset tarif, source data berasal dari sirs
 * @param {JSON} _res 
 */
function resetTarifGrouping(_res) {
    let prosedurBedah = _res.data?.prosedur_bedah
    let alkes = _res.data?.alkes
    let bmhp = _res.data?.bmhp
    let keperawatan = _res.data?.keperawatan
    let konsultasi = _res.data?.konsultasi
    let laboratorium = _res.data?.laboratorium
    let obat = _res.data?.obat
    let obat_kemoterapi = _res.data?.obat_kemoterapi
    let obat_kronis = _res.data?.obat_kronis
    let pelayanan_darah = _res.data?.pelayanan_darah
    let penunjang = _res.data?.penunjang
    let prosedur_non_bedah = _res.data?.prosedur_non_bedah
    let radiologi = _res.data?.radiologi
    let rawat_intensif = _res.data?.rawat_intensif
    let rehabilitasi = _res.data?.rehabilitasi
    let sewa_alat = _res.data?.sewa_alat
    let tenaga_ahli = _res.data?.tenaga_ahli
    let kamar = _res.data?.kamar_akomodasi

    if(prosedurBedah > 0) {
        prosedurBedah = docoHelper.convertToRupiah(prosedurBedah)
        $("#klaiminacbgranapform-prosedur_bedah").val(prosedurBedah)
    }else{
        $("#klaiminacbgranapform-prosedur_bedah").val(0)
    }

    if(tenaga_ahli > 0) {
        tenaga_ahli = docoHelper.convertToRupiah(tenaga_ahli)
        $("#klaiminacbgranapform-tenaga_ahli").val(tenaga_ahli)
    }else{
        $("#klaiminacbgranapform-tenaga_ahli").val(0)
    }

    if(radiologi > 0) {
        radiologi = docoHelper.convertToRupiah(radiologi)
        $("#klaiminacbgranapform-radiologi").val(radiologi)
    }else{
        $("#klaiminacbgranapform-radiologi").val(0)
    }

    if(rehabilitasi > 0) {
        rehabilitasi = docoHelper.convertToRupiah(rehabilitasi)
        $("#klaiminacbgranapform-rehabilitasi").val(rehabilitasi)
    }else{
        $("#klaiminacbgranapform-rehabilitasi").val(0)
    }

    if(obat > 0) {
        obat = docoHelper.convertToRupiah(obat)
        $("#klaiminacbgranapform-obat").val(obat)
    }else{
        $("#klaiminacbgranapform-obat").val(0)
    }

    if(sewa_alat > 0) {
        sewa_alat = docoHelper.convertToRupiah(sewa_alat)
        $("#klaiminacbgranapform-sewa_alat").val(sewa_alat)
    }else{
        $("#klaiminacbgranapform-sewa_alat").val(0)
    }

    if(prosedur_non_bedah > 0) {
        prosedur_non_bedah = docoHelper.convertToRupiah(prosedur_non_bedah)
        $("#klaiminacbgranapform-prosedur_nonbedah").val(prosedur_non_bedah)
    }else{
        $("#klaiminacbgranapform-prosedur_nonbedah").val(0)
    }

    if(keperawatan > 0) {
        keperawatan = docoHelper.convertToRupiah(keperawatan)
        $("#klaiminacbgranapform-keperawatan").val(keperawatan)
    }else{
        $("#klaiminacbgranapform-keperawatan").val(0)
    }

    if(laboratorium > 0) {
        laboratorium = docoHelper.convertToRupiah(laboratorium)
        $("#klaiminacbgranapform-laboratorium").val(laboratorium)
    }else{
        $("#klaiminacbgranapform-laboratorium").val(0)
    }

    if(kamar > 0) {
        kamar = docoHelper.convertToRupiah(kamar)
        $("#klaiminacbgranapform-kamar_akomodasi").val(kamar)
    }else{
        $("#klaiminacbgranapform-kamar_akomodasi").val(0)
    }

    if(alkes > 0) {
        alkes = docoHelper.convertToRupiah(alkes)
        $("#klaiminacbgranapform-alkes").val(alkes)
    }else{
        $("#klaiminacbgranapform-alkes").val(0)
    }

    if(obat_kemoterapi > 0) {
        obat_kemoterapi = docoHelper.convertToRupiah(obat_kemoterapi)
        $("#klaiminacbgranapform-obat_kemoterapi").val(obat_kemoterapi)
    }else{
        $("#klaiminacbgranapform-obat_kemoterapi").val(0)
    }

    if(konsultasi > 0) {
        konsultasi = docoHelper.convertToRupiah(konsultasi)
        $("#klaiminacbgranapform-konsultasi").val(konsultasi)
    }else{
        $("#klaiminacbgranapform-konsultasi").val(0)
    }

    if(penunjang > 0) {
        penunjang = docoHelper.convertToRupiah(penunjang)
        $("#klaiminacbgranapform-penunjang").val(penunjang)
    }else{
        $("#klaiminacbgranapform-penunjang").val(0)
    }

    if(pelayanan_darah > 0) {
        pelayanan_darah = docoHelper.convertToRupiah(pelayanan_darah)
        $("#klaiminacbgranapform-pelayanan_darah").val(pelayanan_darah)
    }else{
        $("#klaiminacbgranapform-pelayanan_darah").val(0)
    }

    if(rawat_intensif > 0) {
        rawat_intensif = docoHelper.convertToRupiah(rawat_intensif)
        $("#klaiminacbgranapform-rawat_intensif").val(rawat_intensif)
    }else{
        $("#klaiminacbgranapform-rawat_intensif").val(0)
    }

    if(bmhp > 0) {
        bmhp = docoHelper.convertToRupiah(bmhp)
        $("#klaiminacbgranapform-bmhp").val(bmhp)
    }else{
        $("#klaiminacbgranapform-bmhp").val(0)
    }

    if(obat_kronis > 0) {
        obat_kronis = docoHelper.convertToRupiah(obat_kronis)
        $("#klaiminacbgranapform-obat_kronis").val(obat_kronis)
    }else{
        $("#klaiminacbgranapform-obat_kronis").val(0)
    }
    $(".group-tarif").trigger('change');
}

function handlingEksekutif(_res) {
    let tarifPoli = _res.response.data.tarif_poli_eks

    if(tarifPoli > 0) {
        var formatter = new Intl.NumberFormat('en-US', {
            currency: 'IDR',
            maximumFractionDigits: 0
          });
        var jenis_kelasrawat = document.getElementById("klaiminacbgranapform-kelas_eksekutif");
        jenis_kelasrawat.checked = true
        let isEks = $(".tarif_poli_eks")
        let tarifEks = $('#klaiminacbgranapform-tarif_poli_eks')
        tarifEks.removeClass('hidden').val(formatter.format(tarifPoli).replace(",", "."));
        isEks.removeClass('hidden');
        $("#klaiminacbgranapform-jenis").trigger('change');
    }
}

function checkEksekutif() {
    var jenis_kelasrawat = document.getElementById("klaiminacbgranapform-kelas_eksekutif");
    let isEks = $(".tarif_poli_eks")
    let tarifEks = $('#klaiminacbgranapform-tarif_poli_eks')

    if (jenis_kelasrawat.checked == true) {
        tarifEks.empty().val('0')
        isEks.removeClass('hidden');
    } else {
        tarifEks.empty().val(null)
        isEks.addClass('hidden');
    }
}

function handingRawatIntensif(icuIndikator, icuLos) {
    if(icuIndikator == true) {
        var rawatIntensif = document.getElementById("klaiminacbgranapform-is_rawatintensif");
        rawatIntensif.checked = true
        $('.hide-rawat-intensif').removeClass('hidden');
        $("#klaiminacbgranapform-lama_rawatintensif").val(icuLos)
    }
}

/**
 * 
 * @param {int} hakKelasBpjs
 * Fungsi ini untuk mengambil hak kelas tetapi dari getGrouper 
 */
function handlingGetKlaimHakKelas(hakKelasBpjs) {
    let jeniskelas_1 = $('#hakkelas-1')
    let jeniskelas_2 = $('#hakkelas-2')
    let jeniskelas_3 = $('#hakkelas-3')
    if(hakKelasBpjs == 1) {
        jeniskelas_1.prop('checked', true)
        jeniskelas_2.prop('checked', false)
        jeniskelas_3.prop('checked', false)
    } else if (hakKelasBpjs == 2) {
        jeniskelas_1.prop('checked', false)
        jeniskelas_2.prop('checked', true)
        jeniskelas_3.prop('checked', false)
    } else if (hakKelasBpjs == 3) {
        jeniskelas_1.prop('checked', false)
        jeniskelas_2.prop('checked', false)
        jeniskelas_3.prop('checked', true)
    } else {
        if(hakKelas == 1) {
            jeniskelas_1.prop('checked', true)
            jeniskelas_2.prop('checked', false)
            jeniskelas_3.prop('checked', false)
        } else if (hakKelas == 2) {
            jeniskelas_1.prop('checked', false)
            jeniskelas_2.prop('checked', true)
            jeniskelas_3.prop('checked', false)
        } else if (hakKelas == 3) {
            jeniskelas_1.prop('checked', false)
            jeniskelas_2.prop('checked', false)
            jeniskelas_3.prop('checked', true)
        } else {
            jeniskelas_1.prop('checked', false)
            jeniskelas_2.prop('checked', false)
            jeniskelas_3.prop('checked', true)
        }
    }
}

/**
 * @author Maulana
 * @param {object} value
 * Fungsi untuk befungsi untuk mapping data inagrouper 
 */
function handlingInagrouper(value) {
    let jenisrawat = $('input[name="KlaimInacbgRanapForm[jenis]"]:checked').val();
    let textJenisRawat = 'Rawat Jalan';
    if(jenisrawat == jenisRawatRanap) {
        textJenisRawat = 'Rawat Inap'
    }

    let responseGrouper = value?.response_idrg
    if(responseGrouper != undefined || responseGrouper != null) {
        let warningMdc = 'Ungroupable or Unrelated'
        let warningDrg = 'Unrelated OR Procedure'
        let textDangerMdc = '';
        let textDangerDrg = '';
        let notValid = false;
        if(responseGrouper.mdc_description.toLowerCase() == warningMdc.toLowerCase()) {
            textDangerMdc = 'text-danger'
            notValid = true
        }

        if(responseGrouper.drg_description.toLowerCase() == warningDrg.toLowerCase()) {
            textDangerDrg = 'text-danger'
            notValid = true
        }

        $('.inagrouper').removeClass('hidden')
        $('.info-ina-txt').empty().append(`${infoInaGrouperTxt} - ${value?.response?.script_version}`)
        $('.info-idrg-txt').empty().append(`${infoInaGrouperTxt} - ${responseGrouper?.script_version} / ${responseGrouper?.logic_version}`)
        $('.jenisrawat-ina-txt').empty().append(`${textJenisRawat} (${lama_rawat} Hari)   `)
        if (!notValid) {
            $('.mdc-ina-txt').empty().append(`${responseGrouper.mdc_description}`).removeClass('text-danger')
        } else {
            $('.mdc-ina-txt').empty().append(`${responseGrouper.mdc_description}`).addClass(textDangerMdc)
        }

        $('.mdcnumber-ina-txt').empty().append(`${responseGrouper.mdc_number}`)

        if (!notValid) {
            $('.drg-ina-txt').empty().append(`${responseGrouper.drg_description}`).removeClass('text-danger')
        } else {
            $('.drg-ina-txt').empty().append(`${responseGrouper.drg_description}`).addClass(textDangerDrg)
        }

        $('.drgnumber-ina-txt').empty().append(`${responseGrouper.drg_code}`)
        $('.status-ina-txt').empty().append(`${responseGrouper.status_cd}`)
        $('.nbr-ina-txt').empty().append(`${responseGrouper.nbr}`)
        $('.cost-ina-txt').empty().append(`${responseGrouper.total_cost_weight}`)

        if (responseGrouper.hasOwnProperty('status_cd')) {
            // Cek status final klaim IDRG
            let section = $('#section-final-idrg')
            section.removeClass('hidden')
            
            if (responseGrouper.status_cd.toLowerCase() == 'final') {
                $('#btn-final-idrg').prop('disabled', true)
                $('#btn-grouping-idrg').prop('disabled', true)
                $('#btn-edit-idrg').prop('disabled', false)
                $('#inacbgs-section').removeClass('hidden')
            } else {
                if (notValid) {
                    $('#btn-final-idrg').prop('disabled', true)
                } else {
                    $('#btn-final-idrg').prop('disabled', false)
                }
                
                $('#btn-grouping-idrg').prop('disabled', false)
                $('#btn-edit-idrg').prop('disabled', true)
                $('#inacbgs-section').addClass('hidden')
                $('#proses-final-klaim').addClass('hidden')
            }
    
            componentSwitchEnableIdrg(responseGrouper.status_cd)
        }
    }
}

/**
 * 
 * @param {object} data 
 */
function showKonfirmasi(data)
{
    $.ajax({
        type: "GET",
        url: "/penjamin-asuransi/informasi-pasien-ranap-bpjs/validasi-data-sitb",
        data: data,
        success: function (response) {
            $(".content-konfirmasi").empty().append(response)
            $("#modal-konfirmasi").modal('show')
        }
    });
}

function hideValidasiSitb() 
{
    let validasi = $("#klaiminacbgranapform-is_pasiensitb").val()
    if(validasi != 'validate') {
        $('.search-sitb').removeClass('hidden')
        $('.ubah-sitb').addClass('hidden')
    } else {
        $('.search-sitb').addClass('hidden')
        $('.ubah-sitb').removeClass('hidden')
    }
}

function checkValidateSitb()
{
    if(numberPasientb != '') {
        $('#klaiminacbgranapform-is_pasiensitb').val('validate')
        $('.pasien_tb').prop('disabled', true)
    }
}

function batalSitb()
{
    const nosep = $('#klaiminacbgranapform-no_sep').val()
    const kunjungan_number = $('#klaiminacbgranapform-kunjungan_id').val()
    $.ajax({
        type: "POST",
        url: "/penjamin-asuransi/informasi-pasien-ranap-bpjs/batal-validasi-sitb",
        data: {
            kunjungan_id: kunjungan_number,
            nosep: noSep
        },
        success: function (response) {
            location.reload()
        }
    });
}

function hideTabInaGrouper(jenisPerawatan)
{
    if(jenisPerawatan != 1) {
        // $('#tab-ina').hide()
    }
}

function componentSwitchEnableIdrg(statusIdrg, allDisable = false) {
    let prop = false;
    if (statusIdrg.toLowerCase() == 'final' || allDisable == true) {
        prop = true
    }

    $('#idrgProcedure').prop('disabled', prop)
    $('#idrgDiagnosa').prop('disabled', prop)
    $('.btn-remove-idrg').prop('disabled', prop)
    $('.btn-remove-idrg-procedure').prop('disabled', prop)
    $('.btn-set-primer-diagnosa-idrg').prop('disabled', prop)
    $('.btn-set-primer-procedure-idrg').prop('disabled', prop)
    $('.input-multiplicity-idrg').prop('disabled', prop)
    $('#btn-add-diagnosa-idrg').prop('disabled', prop)
    $('#btn-add-procedure-idrg').prop('disabled', prop)
}

function componentSwitchEnabledInacbgs(statusInacbgs, statusKlaim, _cbg, statusIdrg) {
    let code = _cbg?.code
    let prop = false;
    if (statusInacbgs?.toLowerCase() == 'final' || statusIdrg?.toLowerCase() == 'normal' && statusInacbgs == undefined) {
        prop = true
    }

    $('#status-klaim-section').addClass('hidden')
    $('#inacbgsProcedure').prop('disabled', prop)
    $('#inacbgsDiagnosa').prop('disabled', prop)
    $('.btn-set-primer-diagnosa-inacbgs').prop('disabled', prop)
    $('.btn-set-primer-procedure-inacbgs').prop('disabled', prop)
    $('.input-multiplicity-inacbgs').prop('disabled', prop)
    $('.btn-remove-inacbgs').prop('disabled', prop)
    $('.btn-remove-inacbgs-procedure').prop('disabled', prop)
    $('#btn-add-diagnosa-inacbgs').prop('disabled', prop)
    $('#btn-add-procedure-inacbgs').prop('disabled', prop)

    if (statusInacbgs == undefined) {
        $('.btn-final-inacbgs').prop('disabled', true)
        return false
    }

    /**
     * Case diagnosa tidak berlaku
     */
    code = code?.slice(0, 3)
    if (code == 'X-0') {
        $('.btn-grouping-inacbgs').prop('disabled', false)
        $('.btn-import-koding').prop('disabled', false)
        $('.btn-edit-inacbgs').prop('disabled', true)
        $('.btn-final-inacbgs').prop('disabled', true)
    }
    
    /**
     * Kondisi status INACBGS Normal
     */
    if (statusInacbgs == 'normal' && code != 'X-0') {
        $('.btn-edit-inacbgs').prop('disabled', true)
        $('.btn-final-inacbgs').prop('disabled', false)
        $('.btn-grouping-inacbgs').prop('disabled', false)
        $('.btn-import-koding').prop('disabled', false)
    }

    /**
     * Kondisi status INACBGS Final
     */
    if (statusInacbgs == 'final' && code != 'X-0') {
        $('.btn-grouping-inacbgs').prop('disabled', true)
        $('.btn-final-inacbgs').prop('disabled', true)
        $('.btn-import-koding').prop('disabled', true)
        $('.btn-edit-inacbgs').prop('disabled', false)
    }
    
    /**
     * Kondisi status status INACBGS & Klaim Final
     */
    if (statusInacbgs == 'final' && statusKlaim == 'final' && code != 'X-0') {
        $('.btn-edit-inacbgs').prop('disabled', true)
        $('#btn-edit-idrg').prop('disabled', true)
        $('#status-klaim-section').removeClass('hidden')
    }
}

function hideAndDisabledSectionIdrg() {
    let section = $('#section-final-idrg')
    section.addClass('hidden')

    $('#btn-final-idrg').prop('disabled', true)
}

function hideAndDisabledSectionInacbgs() {
    let section = $('#proses-final-klaim')
    section.addClass('hidden')

    $('#btn-final-inacbgs').prop('disabled', true)
}

function koreksiTransfusiDarah() {
    $().docoForm('click', {
        type: 'POST',
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/koreksi-transfusi-darah',
        skipConfirm: true,
        data: {
            kunjungan_id: kunjunganId,
            dializer: $("input[name='KlaimInacbgRanapForm[dializer]']:checked").val(),
            kantong_darah: $('#klaiminacbgranapform-transfusi_darah').val()
        },
        success: function (data) {
            getGrouper()
        }
    })
}

