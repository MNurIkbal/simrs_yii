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
const INVALID_PARAMETERS = 'INVALID';
const ERROR_PARAMETERS = 'ERROR';
let primerSudahKoresi = $('#klaiminacbgranapform-diagnosa_primer').val()
let sekunderSudahKoreksi = $('#klaiminacbgranapform-diagnosa_sekunder').val().split('#')
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

$(document).ready(function () {
    let lama_rawat = date_diff_indays($('#klaiminacbgranapform-tgl_masuk').val(), $('#klaiminacbgranapform-tgl_keluar').val())
    $('#klaiminacbgranapform-los').val(lama_rawat)
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
    if (_valuekelas == 1) {
        $('#naikkelas-1').prop("checked", false);
        $('#naikkelas-2').prop("disabled", false);
        $('#naikkelas-3').prop("disabled", false);
        $('#naikkelas-4').prop("disabled", false);
        $('#naikkelas-5').prop("disabled", false);
    } else if (_valuekelas == 2) {
        $('#naikkelas-1').prop("disabled", false);
        $('#naikkelas-2').prop("checked", false);
        $('#naikkelas-3').prop("disabled", false);
        $('#naikkelas-4').prop("disabled", true);
        $('#naikkelas-5').prop("disabled", true);
    } else if (_valuekelas == 3) {
        $('#naikkelas-1').prop("disabled", true);
        $('#naikkelas-2').prop("disabled", false);
        $('#naikkelas-3').prop("checked", false);
        $('#naikkelas-4').prop("disabled", true);
        $('#naikkelas-5').prop("disabled", true);
    }
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
        } else {
            isEks.hide()
        }
    }

    checkPasienTb()
    $('#klaiminacbgranapform-pasien_tb').change(function(e) {
       checkPasienTb()
    })

    $('.search-sitb').click(function(e) {
       e.preventDefault()
       let value = $('#sitb').val()

       if(value == '' || value == undefined) {
           alert('Nomer SITB belum di isi!')
           return false
       }
       
       $.ajax({
           type: "POST",
           url: `/penjamin-asuransi/informasi-pasien-rajal-bpjs/validasi-sitb`,
           data: {
               nosep: infoNoSep,
               nomer_sitb: value
           },
           success: function (response) {
               if(response != undefined) {
                   console.log(response)
                   if(response.code == 400) {
                       docoNotification('warning', 'Peringatan', response?.message);
                   }

                   if(response.code == 200) {
                       if(response?.response.status == "INVALID") {
                           docoNotification('warning', 'Oops', response?.response?.detail);
                       }else{
                           docoNotification('success', 'Berhasil', response?.response?.detail);
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
        'minView': 2
    })

    $('#klaiminacbgranapform-tgl_keluar').datetimepicker({
        'startDate': $('#klaiminacbgranapform-tgl_masuk').val(),
        'endDate': dateNow,
        'autoclose': true,
        'format': 'dd-M-yyyy hh:ii',
        'tabindex': 4,
        'minView': 2
    })

    $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]').on('change', function () {
        var _valuekelas = $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]:checked').val();
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
            $('#naikkelas-1').prop("disabled", false);
            $('#naikkelas-2').prop("disabled", false);
            $('#naikkelas-4').prop("disabled", false);
            $('#naikkelas-5').prop("disabled", false);
        }
        // disabledKelas(_valuekelas);
    })

    $('#btn-back').on('click', function (e) {
        e.preventDefault();
        // history.go(-1);
        window.location.replace(baseUrl+`penjamin-asuransi/informasi-pasien-ranap-bpjs/proses?id=${idEnc}&admisi=undefined&state=${stateEnc}`);
    });

    console.log(_final)

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
        $('.group-tarif').attr('disabled', true)
        $('.select2').attr('disabled', true)
        $('#btn-hapus-klaim').attr('disabled', true)
        $('#klaiminacbgranapform-berat_lahir').attr('disabled', true)
        $('#nama_pasien').attr('disabled', true)
        $('#no_rekam_medik').attr('disabled', true)
        $('#no_sep').attr('disabled', true)
    } else if (_delete == false && _proses == true) {
        $('#btn-hapus-klaim').attr('disabled', false)
        $('#btn-proses').attr('disabled', true)
    } else if (_delete == true && _proses == false) {
        $('#btn-hapus-klaim').attr('disabled', true)
        $('#btn-proses').attr('disabled', false)
    }
    hideAddRanap();
    getGrouper();
    loadDiagnosa();
    let los = date_diff_indays($('#klaiminacbgranapform-tgl_masuk').val(), $('#klaiminacbgranapform-tgl_keluar').val())
    if (los >= MIN_ACCUTE) {
        subAccute.empty().append(ACCUTE)
    }
    if (los >= MIN_CRONIC) {
        cronic.empty().append(CRONIC)
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
                // _dokter = data.rawData;
                return {
                    results: data.result.result
                };
            }
        },
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) {
            return m;
        },
    });
})

spesialProsedur.change(function () {
    let data = $(this).attr('id')
    let arr = []
    let itemChange, itemCode, itemVal, grouperName, arrPos, specCode, type
    let stage = 2;
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
    if (arrPos == 5) {
        kodeDiagnosa[0] = specCode
    } else if (arrPos == 6) {
        kodeDiagnosa[1] = specCode
    } else if (arrPos == 7) {
        kodeDiagnosa[2] = specCode
    } else if (arrPos == 8) {
        kodeDiagnosa[3] = specCode
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
        success: function (res) {
            let total = parseInt(res.response.cbg.tariff)
            var resSelected = []
            let resOption
            let code, desc = ''
            let tarif = 0
            let finalTotal = 0

            if (typeof res.response.special_cmg != 'undefined') {
                tmpRes = res.response.special_cmg
                if (res.response.special_cmg.length > 1) {
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
                        code: res.response.special_cmg[0].code,
                        description: res.response.special_cmg[0].description,
                        tariff: res.response.special_cmg[0].tariff,
                        type: res.response.special_cmg[0].type
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

$(document).on('click', '.btn-add-diagnosa', function (e) {
    e.preventDefault()
    let pendaftaranId = $('.pendaftaran-id-txt').val()
    var _target = $('.' + $(this).attr('data-target'));
    var _type = $(this).attr('data-type');
    var _append = '<tr><th style="width: 350px; border-right: 0px;">';
    var _state = true;
    var _value = '';
    let p = 0
    if (!_target.find('.row-null').hasClass('hidden')) {
        _target.find('.row-null').addClass('hidden')
    }
    if ($(this).closest('tr').find('select').val() === null) {
        _state = false;
        docoNotification('warning', 'Peringatan', 'Kolom Diagnosa Tidak Boleh Kosong!');
    } else {
        if ($(this).closest('tr').find('select').hasClass('select-diagnosa-10')) {
            _value = _diagnosa10[$(this).closest('tr').find('select').val()];
        } else {
            _value = _diagnosa9[$(this).closest('tr').find('select').val()];
        }
    }
    if (cekDuplicate(_value.diagnosa_kode) == false) {
        _state = false;
        docoNotification('warning', 'Peringatan', 'Diagnosa Sudah Pernah Di Inputkan!');
    }
    if (_state) {
        deleteGrouper()
        _append += _value.diagnosa_nama;
        _append += '</th>';
        if ($(this).closest('tr').find('select').hasClass('select-diagnosa-10')) {
            _append += '<th class="dig-aksi"style="border-left: 0px; border-right: 0px;">'
            _append += '<button id="set-' + _value.diagnosa_id + '" style="float:right; margin-top: -3px;" data-target="' + $(this).attr('data-target') + '" data-key="d' + p + '" dig-id="' + _value.diagnosa_id + '" dig-kode="' + _value.diagnosa_kode + '" class="btn btn-warning btn-md set-primer">Set Primer</button></th>';
            _append += '<th class="dig-info" style="width: 225px; border-left: 0px">';
            _append += '<button id="del-' + _value.diagnosa_id + '" style="float:right; margin-top: -3px;" data-target="' + $(this).attr('data-target') + '" data-key="' + i + '" class="btn btn-danger btn-lg btn-remove-diagnosa hide-me" type="10" dig-id="' + _value.diagnosa_id + '" dig-kode="' + _value.diagnosa_kode + '" style="float:right"><i class="fa fa-trash"></i></button>';
            _append += '<span class="badge badge-primary" style="float:left; margin-right: 30px;">' + _value.diagnosa_kode + '</span>';
        } else {
            _append += '<th style="border-left: 0px; border-right: 0px;">'
            _append += '<button style="float:right; margin-top: -3px;" data-target="' + $(this).attr('data-target') + '" data-key="d' + p + '" dig-id="' + _value.diagnosa_id + '" dig-kode="' + _value.diagnosa_kode + '" class="btn btn-warning btn-md hidden">Set Primer</button></th>';
            _append += '<th class="dig-info" width: 225px; style="border-left: 0px">';
            _append += '<button id="del-' + _value.diagnosa_id + '" style="float:right; margin-top: -3px;" data-target="' + $(this).attr('data-target') + '" data-key="' + i + '" class="btn btn-danger btn-lg btn-remove-diagnosa hide-me"  type="10" dig-id="' + _value.diagnosa_id + '" dig-kode="' + _value.diagnosa_kode + '" style="float:right"><i class="fa fa-trash"></i></button>';
            _append += '<span class="badge badge-primary" style="float:right; margin-right: 20px">' + _value.diagnosa_kode + '</span>';
        }
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
            $('.radiocheck-kelas_2').prop('checked', true)
        }
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

$(document).on('click', '.btn-remove-diagnosa', function (e) {
    e.preventDefault()
    
    deleteGrouper();
    var _target = $('.' + $(this).attr('data-target'));
    if (_target.find('.row-null').hasClass('hidden') && _target.find('tr').length == 2) {
        _target.find('.row-null').removeClass('hidden');
    }
    if ($(this).attr('dig-id')) {
        let hapus
        if ($(this).attr('type') == '10') {
            hapus = removeTarget(primerSudahKoresi, $(this).attr('dig-kode'))
            primerSudahKoresi = hapus
        } else {
            hapus = removeTarget(sekunderSudahKoreksi, $(this).attr('dig-kode'))
            sekunderSudahKoreksi = hapus
        }
        let _diagnosaKoreksi = {
            diagnosa_id: $(this).attr('dig-id'),
            kode_diagnosa: $(this).attr('dig-kode'),
            diagnosa_type: $(this).attr('type')
        }
        _deletedDiagnosa[i] = _diagnosaKoreksi;
        i++;
        delete _deletedDiagnosa[$(this).attr('data-key')];
    }
    delete _klaimDetail[$(this).attr('data-key')];
    $(this).closest('tr').remove();
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

    let _noKlaimCovid = 
    [
        {
            name: "KlaimInacbgRanapForm[no_klaimcovid]",
            value : $('#no_klaimcovid').val()
        },
        {
            name: "KlaimInacbgRanapForm[jenis_kelasrawat]",
            value : $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]:checked').val()
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
            console.log('success1');
            if (data != '') {
                data = JSON.parse(data);
            }
            if (typeof data.response != 'undefined') {
                if (!$('.btn-cetak-klaim').hasClass('hidden')) {
                    $('.btn-cetak-klaim').addClass('hidden')
                }
                if (!$('.btn-kirim-klaim').hasClass('hidden')) {
                    $('.btn-kirim-klaim').addClass('hidden')
                }
                if (!$('.btn-edit-klaim').hasClass('hidden')) {
                    $('.btn-edit-klaim').addClass('hidden')
                }
                // _delete = true;
                $('hide-me').addClass('hidden');
                $('#btn-hapus-klaim').prop('disabled', false)
                $('#btn-proses').attr('disabled', true)
                $('#proses-final-klaim').removeClass('hidden');
                $('#klaiminacbgranapform-los').val(lama_rawat)


                var _kelasBaru = '';
                var _tarifalt;
                var _kelasawal = ''; //kelas awal sebelum naik kelas
                var _biayaalt = 0; //biaya tambahan dari naik kelas
                var _biayatambahan = 0; //total biaya tambahan
                var _biayaawal = 0;
                var _tambahan = '' //string buat tambahan
                let _infoKelas = infoTxt + infoKelas($('#klaiminacbgranapform-tarif').val())
                var _kelasnaik = '';
                var is_naikkelas = $('input[name="KlaimInacbgRanapForm[is_naikkelas]"]:checked').val();
                if (is_naikkelas == 1) {
                    _kelasnaik = $('input[name="KlaimInacbgRanapForm[naik_kelas]"]:checked').val();
                }
                var _isNaik = getIsNaik(_kelasnaik);
                var _isnaikkelas = getNaikKelas(_kelasnaik);
                var _kelasKlaim = getKelasKlaim(_kelasnaik);
                var _keterangaNaikKelas = getKeteranganNaikKelas(_isNaik, _kelasKlaim);
                var _kelasAwalBaru = data.response.kelas;

                if (_kelasAwalBaru == 'kelas_1') {
                    _kelasBaru = 1;
                } else if (_kelasAwalBaru == 'kelas_2') {
                    _kelasBaru = 2;
                } else {
                    _kelasBaru = 3;
                }
                jenisRawat = instalasi_nama + ' Kelas ' + _kelasBaru + ' ( ' + lama_rawat + ' Hari)';
                if (_isNaik == true) {
                    _tarifalt = data.tarif_alt;
                    _kelasawal = data.response.kelas;
                    _kelasnaik2 = 'kelas_' + _kelasnaik;

                    $.each(_tarifalt, function (k, v) {
                        var kelas = v.kelas;
                        if (kelas == _kelasnaik2) {
                            _biayaalt = v.tarif_inacbg;
                        }
                        if (kelas == _kelasawal) {
                            _biayaawal = v.tarif_inacbg;
                        }
                        if (_kelasnaik2 == 'kelas_4' && kelas == 'kelas_1') {
                            _biayaalt = (v.tarif_inacbg != null) ? v.tarif_inacbg : 0;
                        }
                        if (_kelasnaik2 == 'kelas_5' && kelas == 'kelas_1') {
                            _biayaalt = (v.tarif_inacbg != null) ? v.tarif_inacbg : 0;
                        }
                    })
                    _biayatambahan = parseInt(_biayaalt) - parseInt(_biayaawal);
                    if (_kelasnaik2 == 'kelas_4' || _kelasnaik2 == 'kelas_5') {
                        _tambahan += '<b style="float:left;margin-top:10px;padding-right:3px">Rp. ' + addCommas(_biayaalt) + ' - ' + ' Rp. ' + addCommas(_biayaawal) + '</b>';
                        _tambahan += " <b style='float: left;margin-top:10px;padding-right:3px'> + ( Rp. " + addCommas(_biayaalt) + " x </b>"
                        _tambahan += ' <div style="float:left" class="input-group"><input type="number" id="persen-vip" class="form-control" style="width: 70px;" min="1" max="75" step="0.5" name="KlaimInacbgRanapForm[add_payment_pct]"><span class="input-group-addon" id="basic-addon1">%</span></div><b style="float:left;margin-left: 3px; margin-top:9px"> )</b>'
                    } else {
                        _tambahan += '<b style="margin-top:2px;">Rp. ' + addCommas(_biayaalt) + ' - ' + ' Rp. ' + addCommas(_biayaawal) + '</b>';
                    }
                    $('.tambahanbiaya-txt').empty().append('<b>Rp. </b>' + addCommas(_biayatambahan))
                    $('.str-tambahan').empty().append(_tambahan)
                    $('#total-tambahan').val(_biayatambahan)
                    $('#total-naikkelas').val(_biayaalt)
                    $('#total-kelaspelayanan').val(_biayaawal)
                    $(".keterangan-naik-kelas").empty().append('<h4>Tambahan Biaya yang Dibayar Pasien ' + _keterangaNaikKelas + '</h4>')
                }

                disabledInputan()

                var _cbg = data.response.cbg;
                var _kelas = data.response.kelas;
                var _total = 0;
                let tarif = 0
                let _specCmg = [];
                if (typeof data.special_cmg_option != 'undefined') {
                    _specCmg = data.special_cmg_option
                }
                if (!_specCmg) {
                    _specCmg = data.response.cbg;

                }
                if (typeof _cbg.tariff != 'undefined') {
                    tarif = _cbg.tariff
                }

                $('.penyakit-nama').empty().append(_cbg.description)
                $('.kode-penyakit').empty().append(_cbg.code)
                $('.kolom-nosep').empty().append('')
                if (typeof tarif != 'undefined') {
                    $('.harga-klaim').empty().append('<b>Rp. </b> ' + addCommas(tarif));
                } else if (typeof tarif == 'undefined') {
                    $('.harga-klaim').empty().append('<b>Rp. </b> ' + addCommas(0));
                }
                $('.info-txt').empty().append(_infoKelas);
                $('.jenisrawat-txt').empty().append(jenisRawat);
                $('.tambahanbiaya-txt').empty().append('<b>Rp. </b>' + addCommas(_biayatambahan))
                $('.str-tambahan').empty().append(_tambahan);

                _total += parseInt(tarif)

                if (data.response.adl_sub_acute != 0) {
                    let resAccute = data.response.adl_sub_acute
                    // subAccute.empty().html(resAccute)
                }

                if (data.response.adl_chronic != 0) {
                    let resCronic = data.response.adl_chronic
                    // cronic.empty().html(resCronic)
                }

                if (typeof data.response.sub_acute != 'undefined') {
                    var _subacute = data.response.sub_acute;
                    $('.subacute-detail').empty().append(_subacute.description)
                    $('.subacute-kode').empty().append(_subacute.code)
                    $('.subacute-harga').empty().append('<b>Rp. </b> ' + addCommas(_subacute.tariff))
                    _total += parseInt(_subacute.tariff)
                }
                if (typeof data.response.chronic != 'undefined') {
                    var _chronic = data.response.chronic;
                    $('.cronic-detail').empty().append(_chronic.description)
                    $('.cronic-kode').empty().append(_chronic.code)
                    $('.cronic-harga').empty().append('<b>Rp. </b> ' + addCommas(_chronic.tariff))
                    _total += parseInt(_chronic.tariff)
                }
                if (_specCmg.length != 0) {
                    _specCmg.map((value) => {
                        if (value.type == SPECIAL_PROCEDURE) {
                            arrSpecProc.push({
                                description: value.description,
                                code: value.code,
                                tariff: 0
                            })
                        }
                        if (value.type == SPECIAL_PROSTHESIS) {
                            arrSpecPros.push({
                                description: value.description,
                                code: value.code,
                                tariff: 0
                            })
                        }
                        if (value.type == SPECIAL_INVESTIGATION) {
                            arrSpecInv.push({
                                description: value.description,
                                code: value.code,
                                tariff: 0
                            })
                        }
                        if (value.type == SPECIAL_DRUG) {
                            arrSpecDrug.push({
                                description: value.description,
                                code: value.code,
                                tariff: 0
                            })
                        }
                    });
                    if (arrSpecProc.length != 0 && arrSpecProc.length > 1) {
                        refreshOptionSelect2(ddSpecProc, arrSpecProc, {
                            id: 'code',
                            text: 'description'
                        });
                        let itemProc = arrSpecProc.find(({
                            code
                        }) => code === ddSpecProc.val())
                        if (itemProc) {
                            $('#sproc-kode').empty().append(itemProc.code)
                            $('#sproc-val').empty().append('<b>Rp. </b> ' + addCommas(itemProc.tariff))
                            finalProc = parseInt(itemProc.tariff);
                        }
                    }
                    if (arrSpecPros.length != 0 && arrSpecPros.length > 1) {
                        refreshOptionSelect2(ddSpecPros, arrSpecPros, {
                            id: 'code',
                            text: 'description'
                        });
                        let itemPros = arrSpecPros.find(({
                            code
                        }) => code === ddSpecPros.val())
                        if (itemPros) {
                            $('#spros-kode').empty().append(itemPros.code)
                            $('#spros-val').empty().append('<b>Rp. </b> ' + addCommas(itemPros.tariff))
                            finalPros = parseInt(itemPros.tariff);
                        }
                    }

                    if (arrSpecDrug.length != 0 && arrSpecDrug.length > 1) {
                        refreshOptionSelect2(ddSpecDrug, arrSpecDrug, {
                            id: 'code',
                            text: 'description'
                        });
                        let itemDrug = arrSpecDrug.find(({
                            code
                        }) => code === ddSpecDrug.val())
                        if (itemDrug) {
                            $('#drug-kode').empty().append(itemDrug.code)
                            $('#drug-val').empty().append('<b>Rp. </b> ' + addCommas(itemDrug.tariff))
                            finalDrug = parseInt(itemDrug.tariff);
                        }
                    }
                    if (arrSpecInv.length != 0 && arrSpecInv.length > 1) {
                        refreshOptionSelect2(ddSpecInv, arrSpecInv, {
                            id: 'code',
                            text: 'description'
                        });
                        let itemInv = arrSpecInv.find(({
                            code
                        }) => code === ddSpecInv.val())
                        if (itemInv) {
                            $('#inv-kode').empty().append(itemInv.code)
                            $('#inv-val').empty().append('<b>Rp. </b> ' + addCommas(itemInv.tariff))
                            finalInv = parseInt(itemInv.tariff);
                        }
                    }
                }
                if (arrSpecProc.length == 1) {
                    refreshOptionSelect2(ddSpecProc, arrSpecProc, {
                        id: 'code',
                        text: 'description'
                    });
                    $('#sproc-kode').empty().append("-")
                    $('#sproc-combo').attr('disabled', true)
                    $('#sproc-val').empty().append('Rp.0')
                }
                if (arrSpecPros.length == 1) {
                    refreshOptionSelect2(ddSpecPros, arrSpecPros, {
                        id: 'code',
                        text: 'description'
                    });
                    $('#spros-kode').empty().append("-")
                    $('#spros-combo').attr('disabled', true)
                    $('#spros-val').empty().append('Rp.0')
                }
                if (arrSpecDrug.length == 1) {
                    refreshOptionSelect2(ddSpecDrug, arrSpecDrug, {
                        id: 'code',
                        text: 'description'
                    });
                    $('#drug-kode').empty().append("-")
                    $('#drug-combo').attr('disabled', true)
                    $('#drug-val').empty().append('Rp.0')
                }
                if (arrSpecInv.length == 1) {
                    refreshOptionSelect2(ddSpecInv, arrSpecInv, {
                        id: 'code',
                        text: 'description'
                    });
                    $('#inv-kode').empty().append("-")
                    $('#inv-combo').attr('disabled', true)
                    $('#inv-val').empty().append('Rp.0')
                }
                _addDetail[0] = {
                    name: 'Add[cbg_desc]',
                    value: _cbg.description
                }
                _addDetail[1] = {
                    name: 'Add[cbg_code]',
                    value: _cbg.code
                }
                _addDetail[2] = {
                    name: 'Add[cbg_tarif]',
                    value: tarif
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
                $('.total-harga').empty().append('<b>Rp. </b> ' + addCommas(_total))
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

                $('.kemenkes_status').empty().append('');
                if (_cbg.description.includes(INVALID_PARAMETERS) || _cbg.description.includes(ERROR_PARAMETERS)) {
                    $('#formfinal-btn-final-klaim').attr('disabled', true)
                } else {
                    $('#formfinal-btn-final-klaim').attr('disabled', false)
                }
            }
            console.log('success end');
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
        name: "KlaimInacbgRanapForm[no_klaimcovid]",
        value : $('#no_klaimcovid').val()
    }]

    $.merge(_data, _detail);
    $.merge(_data, ns);
    $.merge(_data, _grouper);
    $.merge(_data, _addDetail);
    $.merge(_data, _noKlaimCovid);
    
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

            var kemenkes_status = 'Klaim belum terkirim ke Pusat Data Kementerian Kesehatan';
            $('.kemenkes_status').removeClass('text-success');
            $('.kemenkes_status').addClass('text-danger');
            $('.kemenkes_status').empty().append(kemenkes_status);
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
            pendaftaranid: $('.pendaftaran-id-txt').val()
        },
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/hapus-klaim',
        success: function (data) {
            // _delete = false;
            $('#proses-final-klaim').addClass('hidden');
            $('#btn-proses').prop('disabled', false)
            $('#btn-hapus-klaim').prop('disabled', true)
            $('.btn-final-klaim').removeClass('hidden');
            cleaning()
            setTimeout(() => {
                location.reload()
            }, 1000)
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
            admisi: $('#klaiminacbgranapform-pasienadmisi_id').val()
        },
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/edit-ulang-klaim',
        success: function (data) {
            _delete = true;
            $('#btn-proses').prop('disabled', true)
            $('#btn-hapus-klaim').prop('disabled', false)
            $('.btn-final-klaim').removeClass('hidden');
            $('.btn-cetak-klaim').addClass('hidden')
            $('.btn-edit-klaim').addClass('hidden')
            $('.btn-kirim-klaim').addClass('hidden')
            $('.hide-me').removeClass('hidden')
            if ($('#persen-vip').length) {
                $('#persen-vip').attr('readonly', false);
            }

            $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]').prop('disabled', false)
            if ($('input[name="KlaimInacbgRanapForm[is_naikkelas]"]').is(':checked') == true) {
                $('input[name="KlaimInacbgRanapForm[is_naikkelas]"]').prop('disabled', false);
                $('input[name="KlaimInacbgRanapForm[naik_kelas]"]').prop('disabled', false)
                $('#klaiminacbgranapform-lama_rawatkelas').prop('disabled', false)
            } else {
                $('input[name="KlaimInacbgRanapForm[is_naikkelas]"]').prop('disabled', false);
            }
            if ($('input[name="KlaimInacbgRanapForm[is_rawatintensif]"]').is(':checked') == true) {
                $('input[name="KlaimInacbgRanapForm[is_rawatintensif]"]').prop('disabled', false)
                $('#klaiminacbgranapform-lama_rawatintensif').prop('disabled', false);
                $('#klaiminacbgranapform-ventilator').prop('disabled', false);
            } else {
                $('input[name="KlaimInacbgRanapForm[is_rawatintensif]"]').prop('disabled', false)
            }
            location.reload();
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

$('input[name="KlaimInacbgRanapForm[naik_kelas]"]').on('change', function () {
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
    console.log($('#no_klaimcovid').val())
    $.ajax({
        data: {
            no_sep: noClaim != '' ? noClaim : noSep ,
            pendaftaran_id: $('.pendaftaran-id-txt').val()
        },
        type: 'POST',
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-klaim',
        success: function (res) {
            var _res = JSON.parse(res)
            if(_res.metadata?.error_no == "E2004") {
                if(noClaim != '') {
                    updateNoKlaim()
                }
            }
            if (typeof _res.response != 'undefined') {
                var _data = _res.response.data;
                let _option = _data.db_special_opt
                let _db_total = _data.db_total
                let _infoKelas = infoTxt + infoKelas($('#klaiminacbgranapform-tarif').val())
                var klaimStatus = _data.klaim_status_cd;
                var dbSpecialCmg = _data.db_special_cmg;
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
                var _keterangaNaikKelas = getKeteranganNaikKelas(_isNaik, _kelasKlaim);
                var _kelasAwalBaru = _res.response.data.grouper?.response?.kelas;
                let finalProc = 0;
                let finalPros = 0;
                let finalInv = 0;
                let finalDrug = 0;
                let jaminanSelected = _res.response.data?.payor_id
                if (_kelasAwalBaru == 'kelas_1') {
                    _kelasBaru = 1;
                } else if (_kelasAwalBaru == 'kelas_2') {
                    _kelasBaru = 2;
                } else {
                    _kelasBaru = 3;
                }

                defaultJenisRawat(1)


                $("#klaiminacbgranapform-klaim_penjamin").select2().val(jaminanSelected).trigger('change');

                if (klaimStatus == 'normal') {
                    $('#btn-proses').prop('disabled', true);
                    $('#edit-koreksi').prop('disabled', true);
                    $('#btn-hapus-klaim').prop('disabled', false);
                }
                else if(klaimStatus == 'final') {
                    $('.select2').attr('disabled', true)
                    $('#btn-proses').prop('disabled', true);
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

                jenisRawat = instalasi_nama + ' Kelas ' + _kelasBaru + ' ( ' + lama_rawat + ' Hari)';

                if (_isNaik == true) {
                    var _kelasnaik = $('input[name="KlaimInacbgRanapForm[naik_kelas]"]:checked').val(); //kelas setelah naik kelas
                    _tarifalt = _res.response.data.grouper.tarif_alt;
                    _kelasawal = _res.response.data.grouper.response.kelas;
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
                        _tambahan = '<b style="float:left;margin-top:10px;padding-right:3px">Rp. ' + addCommas(_biayaalt) + ' - ' + ' Rp. ' + addCommas(_biayaawal) + '</b>';
                        _tambahan += " <b style='float: left;margin-top:10px;padding-right:3px'> + ( Rp. " + addCommas(_biayaalt) + " x </b>"
                        _tambahan += ' <div style="float:left" class="input-group"><input type="number" id="persen-vip" class="form-control" style="width: 70px;" min="1" max="75" step="0.5" name="KlaimInacbgRanapForm[add_payment_pct]"><span class="input-group-addon" id="basic-addon1">%</span></div><b style="float:left;margin-left: 3px; margin-top:9px"> )</b>'
                    } else {
                        _tambahan = '<b style="margin-top:2px;">Rp. ' + addCommas(_biayaalt) + ' - ' + ' Rp. ' + addCommas(_biayaawal) + '</b>';
                    }
                    $('.tambahanbiaya-txt').empty().append('<b>Rp. </b>' + addCommas(_biayatambahan))
                    $('.str-tambahan').empty().append(_tambahan)
                    $('#total-tambahan').val(_biayatambahan)
                    $('#total-naikkelas').val(_biayaalt)
                    $('#total-kelaspelayanan').val(_biayaawal)
                    $(".keterangan-naik-kelas").empty().append('<h4>Tambahan Biaya yang Dibayar Pasien ' + _keterangaNaikKelas + '</h4>')
                }

                // Fungsi untuk handle grouping null
                handlingGrouperNull(_res)

                disabledInputan()

                var _cbg = _res.response.data.grouper?.response?.cbg;
                var kemenkes_status = 'Klaim belum terkirim ke Pusat Data Kementerian Kesehatan';

                let _specCmg = [];
                if (_option != undefined) {
                    _specCmg = _option
                } else {
                    _specCmg = _res.response.data.grouper?.response?.special_cmg;
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
                $('.harga-klaim').empty().append('<b>Rp. </b> ' + ((typeof _cbg?.tariff !== 'undefined') ? addCommas(_cbg?.tariff) : 0));
                $('.tambahanbiaya-txt').empty().append('<b>Rp. </b>' + addCommas(_biayatambahan))

                _total += (typeof _cbg?.tariff != 'undefined') ? parseInt(_cbg?.tariff) : 0;
                if (_specCmg) {
                    _specCmg.map((value) => {
                        if (value.type == SPECIAL_PROCEDURE) {
                            arrSpecProc.push({
                                description: value.description,
                                code: value.code,
                                tariff: value.tariff
                            })
                        }
                        if (value.type == SPECIAL_PROSTHESIS) {
                            arrSpecPros.push({
                                description: value.description,
                                code: value.code,
                                tariff: value.tariff
                            })
                        }
                        if (value.type == SPECIAL_INVESTIGATION) {
                            arrSpecInv.push({
                                description: value.description,
                                code: value.code,
                                tariff: value.tariff
                            })
                        }
                        if (value.type == SPECIAL_DRUG) {
                            arrSpecDrug.push({
                                description: value.description,
                                code: value.code,
                                tariff: value.tariff
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
                            if (klaimStatus == 'final' && dbSpecialCmg) {
                                itemProc = arrSpecProc.find(({
                                    code
                                }) => code !== '-')

                                ddSpecProc.val(itemProc.code)
                            }
                        } else {
                            ddSpecProc.attr('disabled', true);
                        }
                        if (itemProc) {
                            $('#sproc-kode').empty().append(itemProc.code)
                            $('#sproc-val').empty().append('<b>Rp. </b> ' + addCommas(itemProc.tariff))
                            finalProc = parseInt(itemProc.tariff)
                            if (klaimStatus == 'final') {
                                ddSpecDrug.attr('disabled', true);
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
                            if (klaimStatus == 'final' && dbSpecialCmg) {
                                itemPros = arrSpecPros.find(({
                                    code
                                }) => code !== '-')

                                ddSpecPros.val(itemPros.code)
                            }
                        } else {
                            ddSpecPros.attr('disabled', true);
                        }
                        if (itemPros) {
                            $('#spros-kode').empty().append(itemPros.code)
                            $('#spros-val').empty().append('<b>Rp. </b> ' + addCommas(itemPros.tariff))
                            finalPros = parseInt(itemPros.tariff)
                            if (klaimStatus == 'final') {
                                ddSpecDrug.attr('disabled', true);
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
                            if (klaimStatus == 'final' && dbSpecialCmg) {
                                itemInv = arrSpecInv.find(({
                                    code
                                }) => code !== '-')

                                ddSpecInv.val(itemInv.code)
                            }
                        } else {
                            ddSpecInv.attr('disabled', true);
                        }
                        if (itemInv) {
                            $('#inv-kode').empty().append(itemInv.code)
                            $('#inv-val').empty().append('<b>Rp. </b> ' + addCommas(itemInv.tariff))
                            finalInv = parseInt(itemInv.tariff)
                            if (klaimStatus == 'final') {
                                ddSpecDrug.attr('disabled', true);
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
                            if (klaimStatus == 'final' && dbSpecialCmg) {
                                itemDrug = arrSpecDrug.find(({
                                    code
                                }) => code !== '-')

                                ddSpecDrug.val(itemDrug.code)
                            }
                        } else {
                            ddSpecDrug.attr('disabled', true);
                        }
                        if (itemDrug) {
                            $('#drug-kode').empty().append(itemDrug.code)
                            $('#drug-val').empty().append('<b>Rp. </b> ' + addCommas(itemDrug.tariff))
                            finalDrug = parseInt(itemDrug.tariff)
                            if (klaimStatus == 'final') {
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

                if (typeof _res.response.data.grouper.response?.sub_acute != 'undefined') {
                    var _subacute = _res.response.data.grouper.response.sub_acute;
                    $('.subacute-detail').empty().append(_subacute.description)
                    $('.subacute-kode').empty().append(_subacute.code)
                    $('.subacute-harga').empty().append('<b>Rp. </b> ' + addCommas(_subacute.tariff))
                    _total += parseInt(_subacute.tariff);
                }
                if (typeof _res.response.data.grouper.response?.chronic != 'undefined') {
                    var _chronic = _res.response.data.grouper.response.chronic;
                    $('.cronic-detail').empty().append(_chronic.description)
                    $('.cronic-kode').empty().append(_chronic.code)
                    $('.cronic-harga').empty().append('<b>Rp. </b> ' + addCommas(_chronic.tariff))
                    _total += parseInt(_chronic.tariff);
                }
                if (klaimStatus == 'final') {
                    totalHarga.empty().append('<b>Rp. </b> ' + addCommas(_db_total));
                } else {
                    totalHarga.empty().append('<b>Rp. </b> ' + addCommas(_total));
                }
                $('#persen-vip').val((typeof _res.response.data.add_payment_pct != 'undefined') ? _res.response.data.add_payment_pct : null)
                $('.total-harga').empty().append('<b>Rp. </b> ' + addCommas(_total))
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
                    value: _cbg?.tariff
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
                $('#btn-hapus-klaim').prop('disabled', true);
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

$('#btn-proses').bind("mouseover mouseenter", function (e) {
    let isNaik = $('#klaiminacbgranapform-is_naikkelas')
    let radioSelected = $('input[name="KlaimInacbgRanapForm[naik_kelas]"]')

    if (isNaik.is(':checked') == true) {
        if (radioSelected.is(':checked') == false) {
            flag = false
        } else {
            flag = true
        }
    }

    if (flag == false) {
        $('#btn-proses').attr('disabled', true)
        docoNotification('warning', 'Peringatan', 'Kelas Pelayanan Tidak Boleh Kosong!');
    } else if (flag == true) {
        $('#btn-proses').attr('disabled', false)
    }

});

$(document).on('change', '#klaiminacbgranapform-naik_kelas', function () {
    let radioSelected = $('input[name="KlaimInacbgRanapForm[naik_kelas]"]')
    if (radioSelected.is(':checked') == true) {
        flag = true
    } else {
        flag = false
    }

    if (flag == false) {
        $('#btn-proses').attr('disabled', true)
    } else if (flag == true) {
        $('#btn-proses').attr('disabled', false)
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
    } else if (flag == true) {
        $('#btn-proses').attr('disabled', false)
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
        hidePilKelas($('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]:checked').val(), false)
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

function disabledInputan() {
    if (!$('#proses-final-klaim').hasClass('hidden') || _delete == true) {
        $(".delete-on-edit").attr('disabled', true)
    }else{
        $(".delete-on-edit").attr('disabled', false)

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
        ket = ' Untuk Turun Kelas' + kelas
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
    let convert_masuk = convertDateByFormat(tgl_masuk.val(), "M-d-y")
    let convert_keluar = convertDateByFormat(tgl_keluar.val(), "M-d-y")

    tgl_masuk.datetimepicker("setEndDate", tgl_keluar.val())
    tgl_keluar.datetimepicker("setStartDate", tgl_masuk.val())
    let c = date_diff_indays(convert_masuk, convert_keluar)
    $('#klaiminacbgranapform-los').val(c)
    $('#los').empty().append(': ' + c)
    if (c >= MIN_ACCUTE) {
        subAccute.empty().append(ACCUTE)
        $('#klaiminacbgranapform-adl_subacute').val(ACCUTE)
    } else {
        subAccute.empty().append(0)
    }
    if (c >= MIN_CRONIC) {
        cronic.empty().append(CRONIC)
        $('#klaiminacbgranapform-adl_cronic').val(CRONIC)
    } else {
        cronic.empty().append(0)
    }
})

$('#klaiminacbgranapform-tgl_keluar').on('change', function (e) {
    e.preventDefault()
    let d = new Date()
    let tgl_keluar = $('#klaiminacbgranapform-tgl_keluar')
    let keluarSlice = tgl_keluar.val().slice(0, -5)
    tgl_keluar.val(keluarSlice + d.getHours() + ":" + (d.getMinutes() < 10 ? '0' : '') + d.getMinutes())
})

$('#klaiminacbgranapform-tgl_masuk').on('change', function (e) {
    e.preventDefault()
    let d = new Date()
    let tgl_masuk = $('#klaiminacbgranapform-tgl_masuk')
    let masukSlice = tgl_masuk.val().slice(0, -5)
    tgl_masuk.val(masukSlice + d.getHours() + ":" + (d.getMinutes() < 10 ? '0' : '') + d.getMinutes())
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
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/kirim-klaim-online',
        success: function (data) {
            if (data) {
                kemenkes_status = 'Terkirim';
                $('.kemenkes_status').removeClass('text-danger');
                $('.kemenkes_status').addClass('text-success');
                $('.kemenkes_status').empty().append(kemenkes_status);
            }
        }
    });
});

$(document).on('click', '.set-primer', function (e) {
    e.preventDefault()
    deleteGrouper()
    let target = $(this)
    let digId = target.attr('dig-id')
    let pendaftaranId = $('.pendaftaran-id-txt').val()
    let newPrimary = $("#set-" + digId)
    let oldPrimary = $("#label-primary")
    let oldBtnRemove = oldPrimary.closest('tr').find('.btn-remove-diagnosa')
    let oldBtnPrimer = oldPrimary.closest('tr').find('.set-primer')
    let btnRemove = $("#del-" + digId)
    $.ajax({
        type: 'POST',
        url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/set-primer',
        data: {
            id: pendaftaranId,
            diagnosa: digId
        },
        dataType: 'JSON',
        success: function (res) {
            newPrimary.addClass("hidden")
            btnRemove.addClass("hidden")
            let idOld = oldPrimary.attr('dig-id')
            $("#set-" + idOld).removeClass("hidden")
            $("#del-" + idOld).removeClass("hidden")
            oldPrimary.remove()
            oldBtnRemove.removeClass('hidden')
            oldBtnPrimer.removeClass('hidden')
            let _labelPrim = '<span id="label-primary" class="badge badge-warning" type="10" style="float:right;">ICD Primer</span>'
            target.closest('tr').find('.dig-info').append(_labelPrim)
            isPrimer = target.attr('dig-kode')
            docoNotification('success', res.response.title, res.response.text)
        }
    });
})

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
                // console.log('res', res);
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

$(document).on('change', 'input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]:checked', function () {
    let _valuekelas = $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]:checked').val()
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
    $(".keterangan-naik-kelas").empty().append('<h4>Tambahan Biaya yang Dibayar Pasien</h4>')
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

        hiddenSitb(true)

        if(jaminan == KIPI) {
            hiddenJaminanKipi(false)
        }else{
            hiddenJaminanKipi(true)
        }
        defaultJenisRawat(1)

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
        $('.jkn-select').removeClass('hidden')
        if(infoNoSep == '') {
            $('.jkn').addClass('hidden')
        }
        hiddenSitb(false)
        setHakKelas(JKN)
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
            $('input[name="KlaimInacbgRanapForm[jenis_kelasrawat]"]').prop('checked', false)
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
    let igd = $('#jenis-3')
    if(jenis == 1) {
        ranap.prop('checked', true)    
    }else{
        igd.prop('checked', true)    
    }
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
        value: $('#klaiminacbgform-klaim_penjamin').val()
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
        value: $('#klaiminacbgform-identitas_id').val()
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

$('#modal-preview').on('shown.bs.modal', function () {
    $("#preview-content").attr('src', $('#modal-preview').data('url'))
})


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
    if(_res.response.data.grouper.response != null) {
        $('#proses-final-klaim').removeClass('hidden');
    }

    if(_res.response.data.grouper.response == null) {
        $('#btn-hapus-klaim').prop('disabled', true);
        $('#btn-proses').prop('disabled', false);
    }
}

function updateNoKlaim()
{
    $.ajax({
        type: "GET",
        url: "/penjamin-asuransi/informasi-pasien-ranap-bpjs/update-noklaim",
        data: { "kunjungan_id": kunjunganId},
        success: function (response) {
            console.log(response)
            if(response?.status == 200) {
                $('#no_klaimcovid').val(null) 
            }
        },
        error: function(error) {
            docoNotification('warning', 'Peringatan', error);
        }
    });
}


/* "A Product of PT Docotel Teknologi Powered by Sirs" */
