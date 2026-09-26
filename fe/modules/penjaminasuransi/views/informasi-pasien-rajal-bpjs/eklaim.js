/**
 * 
 * @author : Erlangga (erlangga@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

 var i = 0;
 var _proses = true
 var _deletedDiagnosa = [];
 var _klaimDetail = [];
 var _diagnosa10 = [];
 var _diagnosa9 = [];
 var _grouper = [];
 var _dokter = [];
 var _jaminan_klaim = [];
 var _addDetail = ['', '', '', '', '', '', '', '', '', '', '', '', ''];
 var _delete = false;
 var _nosep = '';
 var _dataBerkas = false;
 var _loadBerkas = true;
 const SPECIAL_PROCEDURE = 'Special Procedure';
 const SPECIAL_PROSTHESIS = 'Special Prosthesis';
 const SPECIAL_DRUG = 'Special Drug';
 const SPECIAL_INVESTIGATION = 'Special Investigation';
 const INVALID_PARAMETERS = 'INVALID';
 const ERROR_PARAMETERS = 'ERROR';
 const SALAH_PARAMETERS = 'SALAH';
 const MIN_TGL_PLG_COVID = '01-28-2020';
 let _getBerkas = false;
 let primerSudahKoresi = $('#klaiminacbgform-diagnosa_primer').val().split('#')
 let sekunderSudahKoreksi = $('#klaiminacbgform-diagnosa_sekunder').val().split('#')
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
 var _jaminan_klaim = [];
 var _no_pengajuan_covid = [];
 var _identitas_value_ = [];
 var _identitas_id = [];
 let _grandTotal = 0;
 var isPrimer
 let parseDiagnosa10 = JSON.parse(_diagnosaMapping10)
 let parseDiagnosa9 = JSON.parse(_diagnosaMapping9)
 let noRm = 0;
 let namaPasien = null;
 let arrayJaminan = [COVID, KIPI, JAMPERSAL]

 
 $(document).ready(function () {

    $('#files-upload-biaya').change(function(e) {
        e.preventDefault()
        console.log(e)
        appendFile()
        uploadFile(e)
    })

    function appendFile()
    {
        let valueArray = [1, 2]
        valueArray.map((value, index) => {
            let str = `string-${value}`
            $('#source').append(str);
        })
    }

    function uploadFile(e)
    {
        var formdata = new FormData()
        formdata.append('file', e.target.files[0])
        formdata.append('label', 'resume_medis')
        formdata.append('nosep', nosep)
        $.ajax({
            type: "POST",
            url: 'upload-berkas',
            cache: false,
            contentType: false,
            processData: false,
            data: formdata,
            success: function (response) {
                console.log(response)
            },
            error: function(jqXhr) {
                alert(jqXhr.responseText)
            }
        });
    }


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
 
     function checkEksekutif() {
         var jenis_kelasrawat = document.getElementById("klaiminacbgform-jenis_kelasrawat");
         let isEks = $(".tarif_poli_eks")
         let tarifEks = $('#klaiminacbgform-tarif_poli_eks')
 
         if (jenis_kelasrawat.checked == true) {
             tarifEks.empty().val('0')
             isEks.removeClass('hidden');
         } else {
             tarifEks.empty().val(null)
             isEks.addClass('hidden');
         }
     }

     function checkPasienTb() {
        var pasien_tb = document.getElementById("klaiminacbgform-pasien_tb");
        let isEks = $(".sitb-field")
        console.log(pasien_tb)

        if (pasien_tb.checked == true) {
            isEks.fadeIn(500).removeClass('hidden');
        } else {
            isEks.hide()
        }
    }

    definePatientData()
    function definePatientData()
    {
        noRm = $('#klaiminacbgform-no_rekam_medik').val()
        namaPasien = $('#klaiminacbgform-nama_pasien').val()
    }
 
     dataAppend(10, parseDiagnosa10)
     dataAppend(9, parseDiagnosa9)
 
     checkEksekutif();
     $('#klaiminacbgform-jenis_kelasrawat').change(function () {
         checkEksekutif();
     });

     checkPasienTb()
     $('#klaiminacbgform-pasien_tb').change(function(e) {
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
 
     if (_delete === false && _proses === true) {
         $('#btn-hapus-klaim').attr('disabled', true)
         $('#btn-proses').attr('disabled', false)
         $('#edit-koreksi').attr('disabled', false)
     } else if (_delete === true && _proses === false) {
         $('#btn-hapus-klaim').attr('disabled', false)
         $('#btn-proses').attr('disabled', true)
         $('#edit-koreksi').attr('disabled', true)

     }
     
     if (_diajukan == 1) {
         if (!$('.btn-edit-klaim').hasClass('hidden')) {
             $('.btn-edit-klaim').addClass('hidden')
         }
     }
 
     if (infoNoSep === nosep) {
         let _html = i18next.t('<b><i class="fa fa-search"></i></b> ' + 'Validasi SEP');
         $('#btn-search-sep').html(_html).attr('disabled', true);
     }
     if($('#klaiminacbgform-klaim_penjamin').val() == COVID) {
         if(typeof $('#no_klaimcovid').val() == 'undefined' || $('#no_klaimcovid').val() == ""){
             let dataPasien = {
                 'kunjunganId': kunjunganId,
                 'noKartu': '',
                 'nosep' : nosep,
                 'noRm' : noRm,
                 'namaPasien': namaPasien,
                 'tglLahir': $('#klaiminacbgform-tgl_lahir').val(),
                 'gender': $('#klaiminacbgform-jeniskelamin').val(),
                 'jenisIdentitas': $('#klaiminacbgform-identitas_id').val(),
                 'noIdentitas' : $('#identitas_value').val()
             }
             generateNoCovid(dataPasien)
         } else {
             _nosep =  $('#no_klaimcovid').val();
         }
         $('.covid-select').removeClass('hidden')
         $('.jkn-select').addClass('hidden')
         $('.jkn').removeClass('hidden')
         checkPulang()
     }
     appendJaminan();
     if (_final) {
         // $(':input').prop('disabled', true);
         $('#klaiminacbgform-jenis_kelasrawat').prop('disabled', true)
         $('#klaiminacbgform-tgl_masuk').prop('disabled', true)
         $('.group-tarif').attr('disabled', true)
         $('.select2').attr('disabled', true)
         $('#btn-hapus-klaim').attr('disabled', true)
         $('#btn-proses').attr('disabled', true)
         $('#edit-koreksi').attr('disabled', true)
         $('#nama_pasien').attr('disabled', true)
         $('#no_rekam_medik').attr('disabled', true)
         $('#no_sep').attr('disabled', true)
         $('#judul-grouper').empty().html('Hasil Grouper - Final')
         $('.hide-me').addClass('hidden')
     } else {
         $('#btn-hapus-klaim').attr('disabled', false)
         // $('#btn-hapus-klaim').attr('disabled', true)
     }
     getGrouper()
     loadDiagnosa()
     $('.select-diagnosa-10').select2({
         placeholder: '-',
         minimumInputLength: 3,
         ajax: {
             url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/get-diagnosa?type=10',
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
             var _append = '<tr><th style="border-right: 0px; width: 60%;">';
             var _state = true;
             var _value = '';
             let p = 0;
 
             _value = _diagnosa10[val];
 
             if (cekDuplicate(_value.diagnosa_kode) == false) {
                 _state = false;
                 docoNotification('warning', 'Peringatan', 'Diagnosa Sudah Pernah Di Inputkan!');
             }
             if (_state) {
                 deleteGroupper()
                 _append += _value.diagnosa_nama;
                 _append += '</th>';
 
                 _append += '<th class="dig-aksi"style="border-left: 0px; border-right: 0px;">'
                 _append += '<button id="set-' + _value.diagnosa_id + '" style="float:right; margin-top: -3px;" data-target="' + $(this).attr('data-target') + '" data-key="d' + p + '" dig-id="' + _value.diagnosa_id + '" dig-kode="' + _value.diagnosa_kode + '" class="btn btn-warning btn-md set-primer font-13">Set Primer</button></th>';
                 _append += '<th class="dig-info" style="width: 190px; border-left: 0px">';
                 _append += '<button id="del-' + _value.diagnosa_id + '" style="float:right; margin-top: -3px;" data-target="' + $(this).attr('data-target') + '" data-key="' + i + '" class="btn btn-danger btn-lg btn-remove-diagnosa hide-me" type="10" dig-id="' + _value.diagnosa_id + '" dig-kode="' + _value.diagnosa_kode + '" style="float:right"><i class="fa fa-trash"></i></button>';
                 _append += '<span class="badge badge-primary font-14" style="float:left; margin-right: 30px;">' + _value.diagnosa_kode + '</span>';
 
                 _append += '</th></tr>';
                 _target.find('tbody').append(_append)
                 $(this).closest('tr').find('select').val('').trigger('change')
                 var _newArr = {
                     diagnosa_id: _value.diagnosa_id,
                     nama_diagnosa: _value.diagnosa_nama,
                     kode_diagnosa: _value.diagnosa_kode,
                     diagnosa_type: _type,
                     dokterdpjp_id: dpjpId
                 };
 
                 var _newMapping = {
                     diagnosa_id: _value.diagnosa_id,
                     diagnosa_nama: _value.diagnosa_nama,
                     diagnosa_kode: _value.diagnosa_kode,
                 }
 
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
                         type_diagnosa: _type,
                         dokterdpjp_id: dpjpId
                     },
                     url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/add-diagnosa-tambahan',
                     success: function (data) {
                         parseDiagnosa10.push(_newMapping)
                         dataAppend(10, parseDiagnosa10)
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
             url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/get-diagnosa?type=9',
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
             var _append = '<tr><th style="border-right: 0px; width: 60%;">';
             var _state = true;
             var _value = '';
             let p = 0
 
             _value = _diagnosa9[val];
 
             if (cekDuplicate(_value.diagnosa_kode) == false) {
                 _state = false;
                 docoNotification('warning', 'Peringatan', 'Diagnosa Sudah Pernah Di Inputkan!');
             }
             if (_state) {
                 deleteGroupper()
                 _append += _value.diagnosa_nama;
                 _append += '</th>';
 
                 _append += '<th style="border-left: 0px; border-right: 0px;">'
                 _append += '<button style="float:right; margin-top: -3px;" data-target="' + $(this).attr('data-target') + '" data-key="d' + p + '" dig-id="' + _value.diagnosa_id + '" dig-kode="' + _value.diagnosa_kode + '" class="btn btn-warning btn-md hidden font-14">Set Primer</button></th>';
                 _append += '<th class="dig-info" width: 190px; style="border-left: 0px">';
                 _append += '<button id="del-' + _value.diagnosa_id + '" style="float:right; margin-top: -3px;" data-target="' + $(this).attr('data-target') + '" data-key="' + i + '" class="btn btn-danger btn-lg btn-remove-diagnosa hide-me"  type="10" dig-id="' + _value.diagnosa_id + '" dig-kode="' + _value.diagnosa_kode + '" style="float:right"><i class="fa fa-trash"></i></button>';
                 _append += '<span class="badge badge-primary font-14" style="float:right; margin-right: 30px">' + _value.diagnosa_kode + '</span>';
 
                 _append += '</th></tr>';
                 _target.find('tbody').append(_append)
                 $(this).closest('tr').find('select').val('').trigger('change')
                 var _newArr = {
                     diagnosa_id: _value.diagnosa_id,
                     nama_diagnosa: _value.diagnosa_nama,
                     kode_diagnosa: _value.diagnosa_kode,
                     diagnosa_type: _type,
                     dokterdpjp_id: dpjpId
                 };
 
                 var _newMapping = {
                     diagnosa_id: _value.diagnosa_id,
                     diagnosa_nama: _value.diagnosa_nama,
                     diagnosa_kode: _value.diagnosa_kode,
                 }
 
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
                         type_diagnosa: _type,
                         dokterdpjp_id: dpjpId
                     },
                     url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/add-diagnosa-tambahan',
                     success: function (data) {
                         $('.empty-icd9').hide();
                         parseDiagnosa9.push(_newMapping)
                         dataAppend(9, parseDiagnosa9)
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
             url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/get-dokter',
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
     $('#btn-back').on('click', function (e) {
         e.preventDefault();
         // history.go(-1)
         window.location.replace(baseUrl+`penjamin-asuransi/informasi-pasien-rajal-bpjs/proses?id=${idEnc}&state=${stateEnc}`);
     });
 
     _jaminan_klaim[0] = {
         name: 'KlaimInacbgForm[klaim_penjamin]',
         value: $('#klaiminacbgform-klaim_penjamin').val()
     }
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
             arrPos = 2
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
             arrPos = 3
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
             arrPos = 4
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
             arrPos = 5
             type = SPECIAL_PROSTHESIS
             break;
     }
 
     // kodeDiagnosa.push(specCode)
     if (arrPos == 2) {
         kodeDiagnosa[0] = specCode
     } else if (arrPos == 3) {
         kodeDiagnosa[1] = specCode
     } else if (arrPos == 4) {
         kodeDiagnosa[2] = specCode
     } else if (arrPos == 5) {
         kodeDiagnosa[3] = specCode
     }
 
     $.ajax({
         type: 'POST',
         url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/grouper',
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
 
             if (arrPos == 2) {
                 tarifSpecial[0] = itemVal.data(dataVal)
                 _addDetail[11] = {
                     name: 'Add[drug_code]',
                     value: code
                 }
                 _addDetail[12] = {
                     name: 'Add[drug_name]',
                     value: desc
                 }
             } else if (arrPos == 3) {
                 tarifSpecial[1] = itemVal.data(dataVal)
                 _addDetail[9] = {
                     name: 'Add[inv_code]',
                     value: code
                 }
                 _addDetail[10] = {
                     name: 'Add[inv_name]',
                     value: desc
                 }
             } else if (arrPos == 4) {
                 tarifSpecial[2] = itemVal.data(dataVal)
                 _addDetail[7] = {
                     name: 'Add[proc_code]',
                     value: code
                 }
                 _addDetail[8] = {
                     name: 'Add[proc_name]',
                     value: desc
                 }
             } else if (arrPos == 5) {
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
 
 function getGrouperOption() {
     var result = [];
     $.ajax({
         data: {
             no_sep: $('.no-sep').val(),
             code: '',
             stage: 1
         },
         type: 'POST',
         url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/grouper',
         success: function (res) {
             result = res;
         }
     });
     return result;
 }
 
 $(document).on('click', '.btn-add-diagnosa', function () {
     let pendaftaranId = $('.pendaftaran-id-txt').val()
     var _target = $('.' + $(this).attr('data-target'));
     var _type = $(this).attr('data-type');
     var _append = '<tr><th style="border-right: 0px; width: 250px;">';
     var _state = true;
     var _value = '';
     let p = 0
     if (!$('#proses-final-klaim').hasClass('hidden') || _delete == true) {
        let valueJaminan = $('#klaiminacbgform-klaim_penjamin').val() 
        if(arrayJaminan.includes(valueJaminan)){
           _sep = $('#no_klaimcovid').val()
        } else {
           _sep = $('.no-sep').val()
        }
    
        $().docoForm('click', {
            skipConfirm: true,
            data: {
                nosep: _sep,
                pendaftaranid: $('.pendaftaran-id-txt').val()
            },
            url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/hapus-klaim',
            success: function (data) {
                _delete = false
                $('#proses-final-klaim').addClass('hidden');
            }
        });
     }
 
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
         deleteGroupper()
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
             url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/add-diagnosa-tambahan',
             success: function (data) {
                 return true
             }
         });
     }
 })
 
 $(document).on('click', '.btn-remove-diagnosa', function () {
     deleteGroupper();
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
     let xz = 0;
     let zx = 0;
     var jenis_kelasrawat = document.getElementById("klaiminacbgform-jenis_kelasrawat").checked;
     var jenis_kelasrawat_nama = (jenis_kelasrawat == true) ? 'Eksekutif' : 'Reguler';
     let finalProc = 0;
     let finalPros = 0;
     let finalInv = 0;
     let finalDrug = 0;
     appendJaminan()
 
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
                 _diagnosaDeleted[xz] = {
                     name: 'DiagnosaDeleted[' + zx + '][' + key + ']',
                     value: val
                 };
                 xz++;
             })
         }
         zx++;
     })
 
     $.merge(_data, _diagnosaDeleted);
     $.merge(_data, _detail);
     // merge data covid
     $.merge(_data, _jaminan_klaim);
     $.merge(_data, _no_pengajuan_covid);
     $.merge(_data, _identitas_value_);
     $.merge(_data, _identitas_id);
 
     _data[_data.length + 1] = {
         name: 'primer',
         value: isPrimer
     }
     let arrData = cleanArr(_data)
     if($('#klaiminacbgform-klaim_penjamin').val() == COVID) {
         let pasienKeluar = new Date($('#klaiminacbgform-tgl_keluar').val())
         if(pasienKeluar < new Date(MIN_TGL_PLG_COVID)) {
             (new PNotify({
                 title: "Perhatian",
                 text: "Tanggal pulang pasien sebelum 28 Jan 2020 tidak berlaku untuk pengajuan klaim COVID-19.",
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
             return Error('Tanggal pulang pasien sebelum 28 Jan 2020 tidak berlaku untuk pengajuan klaim COVID-19.');
         }
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
     $().docoForm('click', {
         skipConfirm: false,
         data: arrData,
         url: $('#form-proses-klaim').attr('action'),
         success: function (data) {
             jenisRawat = instalasiNama + ' Kelas ' + jenis_kelasrawat_nama
             if (data != '') {
                 data = JSON.parse(data);
             }
             let _specCmg = [];
             let tarif = 0
             let _infoKelas = infoTxt + infoKelas($('#klaiminacbgform-tarif').val())
             if (typeof data.special_cmg_option !== 'undefined') {
                 _specCmg = data.special_cmg_option;
             }
 
             if (typeof data.response != 'undefined') {
                 _grandTotal = 0;
                 var _data = data.response.data;
 
                 if (!$('.btn-cetak-klaim').hasClass('hidden')) {
                     $('.btn-cetak-klaim').addClass('hidden')
                 }
                 if (!$('.btn-kirim-klaim').hasClass('hidden')) {
                     $('.btn-kirim-klaim').addClass('hidden')
                 }
                 if (!$('.btn-edit-klaim').hasClass('hidden')) {
 
                     $('.btn-edit-klaim').addClass('hidden')
                 }
                 $('hide-me').addClass('hidden');
                 $('#btn-proses').prop('disabled', true)
                 $('#edit-koreksi').prop('disabled', true);
                 $('#proses-final-klaim').removeClass('hidden');
 
 
                 if (data.response.adl_sub_acute != 0) {
                     let resAccute = data.response.adl_sub_acute
                     subAccute.empty().html(resAccute)
                 }
 
                 if (data.response.adl_chronic != 0) {
                     let resCronic = data.response.adl_chronic
                     cronic.empty().html(resCronic)
                 }
                 if (typeof data.response.sub_acute != 'undefined') {
                     var _subacute = data.response.sub_acute;
                     $('.subacute-detail').empty().append(_subacute.description)
                     $('.subacute-kode').empty().append(_subacute.code)
                     $('.subacute-harga').empty().append('<b>Rp. </b> ' + addCommas(_subacute.tariff))
                     _grandTotal += parseInt(_subacute.tariff)
                 }
                 if (typeof data.response.chronic != 'undefined') {
                     var _chronic = data.response.chronic;
                     $('.cronic-detail').empty().append(_chronic.description)
                     $('.cronic-kode').empty().append(_chronic.code)
                     $('.cronic-harga').empty().append('<b>Rp. </b> ' + addCommas(_chronic.tariff))
                     _grandTotal += parseInt(_chronic.tariff)
                 }
 
                 if (typeof data.response.cbg.tariff != 'undefined') {
                     tarif = data.response.cbg.tariff
                 }
 
                 // var _cbg = data.response.cbg;
                 // Spesial cmg option
                 if (!_specCmg) {
                     _specCmg = data.response.cbg;
 
                 }
                 _grandTotal += parseInt(tarif);
 
                 $('.harga-klaim').empty().append('<b>Rp. </b> ' + addCommas(tarif));
                 $('.penyakit-nama').empty().append(data.response.cbg.description)
                 $('.kode-penyakit').empty().append(data.response.cbg.code)
                 $('.kolom-nosep').empty().append('')
                 $('.info-txt').empty().append(_infoKelas);
                 $('.jenisrawat-txt').html(jenisRawat);
 
                 $('#sproc-kode').html('-');
                 $('#sproc-val').html('<b>Rp. </b> ' + 0);
                 $('#spros-kode').html('-');
                 $('#spros-val').html('<b>Rp. </b> ' + 0);
                 $('#inv-kode').html('-');
                 $('#inv-val').html('<b>Rp. </b> ' + 0);
                 $('#drug-kode').html('-');
                 $('#drug-val').html('<b>Rp. </b> ' + 0);
 
                 if (typeof data.special_cmg_option !== 'undefined' && data.special_cmg_option) {
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
                 } else {
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
                 }
 
                 if (arrSpecProc.length != 0) {
                     refreshOptionSelect2(ddSpecProc, arrSpecProc, {
                         id: 'code',
                         text: 'description'
                     });
 
                     let itemProc = arrSpecProc.find(({
                         code
                     }) => code === ddSpecProc.val());
                     ddSpecProc.attr('disabled', false);
                     if (arrSpecProc.length == 1) {
                         ddSpecProc.attr('disabled', true);
                     }
 
                     if (itemProc) {
                         $('#sproc-kode').empty().append(itemProc.code)
                         $('#sproc-val').empty().append('<b>Rp. </b> ' + addCommas(itemProc.tariff))
                         finalProc = parseInt(itemProc.tariff)
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
                     ddSpecPros.attr('disabled', false);
                     if (arrSpecPros.length == 1) {
                         ddSpecPros.attr('disabled', true);
                     }
 
                     if (itemPros) {
                         $('#sproc-kode').empty().append(itemPros.code)
                         $('#sproc-val').empty().append('<b>Rp. </b> ' + addCommas(itemPros.tariff))
                         finalPros = parseInt(itemPros.tariff)
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
                     ddSpecInv.attr('disabled', false);
                     if (arrSpecInv.length == 1) {
                         ddSpecInv.attr('disabled', true);
                     }
 
                     if (itemInv) {
                         $('#sproc-kode').empty().append(itemInv.code)
                         $('#sproc-val').empty().append('<b>Rp. </b> ' + addCommas(itemInv.tariff))
                         finalInv = parseInt(itemInv.tariff)
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
                     ddSpecDrug.attr('disabled', false);
                     if (arrSpecDrug.length == 1) {
                         ddSpecDrug.attr('disabled', true);
                     }
 
                     if (itemDrug) {
                         $('#sproc-kode').empty().append(itemDrug.code)
                         $('#sproc-val').empty().append('<b>Rp. </b> ' + addCommas(itemDrug.tariff))
                         finalDrug = parseInt(itemDrug.tariff)
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
 
                 $('.total-harga').empty().append('<b>Rp. </b> ' + addCommas(_grandTotal))
                 _addDetail[0] = {
                     name: 'Add[cbg_desc]',
                     value: data.response.cbg.description
                 }
                 _addDetail[1] = {
                     name: 'Add[cbg_code]',
                     value: data.response.cbg.code
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
                 _grouper[0] = {
                     name: 'Grouper[total]',
                     value: _grandTotal
                 };
                 _grouper[1] = {
                     name: 'Grouper[tambahan_biaya]',
                     value: 0
                 };
                 _grouper[2] = {
                     name: 'Grouper[spesial_drug]',
                     value: finalDrug
                 };
                 _grouper[3] = {
                     name: 'Grouper[spesial_investigation]',
                     value: finalInv
                 };
                 _grouper[4] = {
                     name: 'Grouper[spesial_procedure]',
                     value: finalProc
                 };
                 _grouper[5] = {
                     name: 'Grouper[spesial_prosthesis]',
                     value: finalPros
                 };
                 $('#btn-hapus-klaim').attr('disabled', false)
                 $('.kemenkes_status').empty().append('');
                 if (data.response.cbg.description.includes(INVALID_PARAMETERS) || data.response.cbg.description.includes(ERROR_PARAMETERS) || data.response.cbg.description.includes(SALAH_PARAMETERS)) {
                     $('#formfinal-btn-final-klaim').attr('disabled', true)
                 } else {
                     $('#formfinal-btn-final-klaim').attr('disabled', false)
                 }
                 
                 disabledInputan()
                 //covid Case
                  if($('#klaiminacbgform-klaim_penjamin').val() == COVID) {
                     let covid19 = data.response.covid19_data;
                     if(typeof covid19 != 'undefined') {
                         let total_rawat = covid19.top_up_rawat
                         let total_jenazah = covid19.top_up_jenazah
                         let total = parseInt(total_rawat)
                         let status_pasien = $('#klaiminacbgform-status_covid').val()
                         let komplikasi = (covid19.cc_ind == '1') ? 'DENGAN' : 'TANPA'  
                         $('.covid-header').empty().append(status_pasien + ' COVID-19 RAWAT JALAN')
                         $('.covid-harga').empty().append('<b>Rp. </b> ' + addCommas(total_rawat))
 
                         if($('#klaiminacbgform-carapulang_id').val() == 4){
                             total += parseInt(total_jenazah)
                             if($('.covid-meninggal').hasClass('hidden') == false){
                                 $('.covid-meninggal').addClass('hidden')
                             }
                             if (typeof covid19.pemulasaraan_jenazah !== 'undefined') {
                                 appenDetailPemulasaraan(covid19.pemulasaraan_jenazah);
                             }
                         } else {
                             $('.pemulasaran').addClass('hidden')
                         }
                         $('.total-harga').empty().append('<b>Rp. </b> ' + addCommas(total))
                     }
                 }
             } else {
                 (new PNotify({
                     title: "Perhatian",
                     text: "Tidak ada respon dari inacbgs",
                     addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                     type: "warning",
                     buttons: {
                         closer: false,
                         sticker: false
                     },
                     hide: true,
                     history: {
                         history: false
                     }
                 }));
             }
         }
     });
 });
 
 /* "A Product of PT Docotel Teknologi Powered by Sirs" */
 $(document).on('click', '.btn-final-klaim', function (e) {
     e.preventDefault()
     var _data = $('#form-proses-klaim').serializeArray();
     var _detail = [];
     var z = 0;
     var p = 0;
     var ns = [];
     var noSep = '';
     let valueJaminan = $('#klaiminacbgform-klaim_penjamin').val() 
    if(arrayJaminan.includes(valueJaminan)){
        noSep = $('#no_klaimcovid').val()
    } else {
        noSep = $('.no-sep').val()
    }
 
     var ns = [{
         name: 'nosep',
         value: noSep
     }];
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
     $.merge(_data, _detail);
     $.merge(_data, ns);
     $.merge(_data, _grouper);
     $.merge(_data, _addDetail);
     $().docoForm('click', {
         // skipConfirm: false,
         data: _data,
         url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/final-klaim',
         success: function (data) {
             $('.button-select').hide();
             $('.dz-message').hide();
             $('.dz-error-mark').hide();
             $(".dz-remove").hide();
             $('.dz-error-message').css('opacity', 0);
             $('#judul-grouper').empty().html('Hasil Grouper - Final')
             $('#klaiminacbgform-jenis_kelasrawat').prop('disabled', true)
             $('#klaiminacbgform-tgl_masuk').prop('disabled', true)
             $('.group-tarif').attr('disabled', true)
             $('.select2').attr('disabled', true)
             $('#btn-hapus-klaim').attr('disabled', true)
             $('#nama_pasien').attr('disabled', true)
             $('#no_rekam_medik').attr('disabled', true)
             $('#no_sep').attr('disabled', true)
             $('.btn-final-klaim').addClass('hidden');
             $('.hide-me').addClass('hidden');
             $('#btn-proses').attr('disabled', true);
             $('#edit-koreksi').prop('disabled', true);
             $('#btn-hapus-klaim').attr('disabled', true);
             $('.spesial-prosedur').attr('disabled', true);
             if ($('.btn-cetak-klaim').hasClass('hidden')) {
                 $('.btn-cetak-klaim').removeClass('hidden')
             }
             if ($('.btn-kirim-klaim').hasClass('hidden')) {
                 $('.btn-kirim-klaim').removeClass('hidden')
             }
             if ($('.btn-edit-klaim').hasClass('hidden')) {
 
                 $('.btn-edit-klaim').removeClass('hidden')
             }
             var kemenkes_status = 'Klaim belum terkirim ke Pusat Data Kementerian Kesehatan';
             $('.kemenkes_status').removeClass('text-success');
             $('.kemenkes_status').addClass('text-danger');
             $('.kemenkes_status').empty().append(kemenkes_status);
 
 
             $('.dz-preview').remove();
             $('.dz-button').css('display', 'none');
            //  getBerkas();
            setTimeout(() => {
                location.reload()
            }, 1000)
         }
     });
 })
 
 $(document).on('click', '#btn-hapus-klaim', function () {
     let _sep = null;
     let valueJaminan = $('#klaiminacbgform-klaim_penjamin').val() 
     if(arrayJaminan.includes(valueJaminan)){
        _sep = $('#no_klaimcovid').val()
     } else {
        _sep = $('.no-sep').val()
     }
 
     $().docoForm('click', {
         confirmMessage: 'Yakin akan menghapus data klaim?',
         data: {
             nosep: _sep,
             pendaftaranid: $('.pendaftaran-id-txt').val(),
             penjamin: $('#klaiminacbgform-klaim_penjamin').val(),
             no_pendaftaran: $('#klaiminacbgform-no_pendaftaran').val(),
         },
         url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/hapus-klaim',
         success: function (data) {
             _delete = false;
             $('#proses-final-klaim').addClass('hidden');
             $('#btn-proses').prop('disabled', false)
             $('#edit-koreksi').prop('disabled', false);
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
    let _sep = null;
    let valueJaminan = $('#klaiminacbgform-klaim_penjamin').val() 
    if(arrayJaminan.includes(valueJaminan)){
       _sep = $('#no_klaimcovid').val()
    } else {
       _sep = $('.no-sep').val()
    }

     $().docoForm('click', {
         skipConfirm: true,
         // confirmMessage: 'Yakin akan melakukan edit ulang klaim pada data ini?',
         data: {
             nosep: _sep,
             pendaftaranid: $('.pendaftaran-id-txt').val()
         },
         url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/edit-ulang-klaim',
         success: function (data) {
             _delete = true;
             $(".button-select").show();
             $('.dz-message').show();
             $('.dz-error-mark').show();
             $(".dz-remove").show();
             $('.dz-error-message').css('opacity', 1);
             // $('#proses-final-klaim').addClass('hidden');
             $('#btn-proses').prop('disabled', true)
             $('#edit-koreksi').prop('disabled', true);
             $('#btn-hapus-klaim').prop('disabled', false)
             $('.btn-final-klaim').removeClass('hidden');
             $('.btn-cetak-klaim').addClass('hidden')
             $('.btn-edit-klaim').addClass('hidden')
             $('.hide-me').removeClass('hidden')
 
             $('.spesial-prosedur').each(function (e) {
                 var id = $(this).attr('id');
                 var length = $('#' + id + ' option').length;
 
                 if (length > 1) {
                     $(this).attr('disabled', false);
                 }
             });
             location.reload();
 
         }
     });
 })
 
 $(document).on('click', '.btn-cetak-klaim', function () {
     var noSep = '';
     var type = '';
     let _penjamin = $('#klaiminacbgform-klaim_penjamin').val()
      
    if(arrayJaminan.includes(_penjamin)){
        type = 'covid';
        noSep = $('#no_klaimcovid').val()
    } else {
        type ='jkn';
        noSep = $('.no-sep').val();
    }
     window.open('/penjamin-asuransi/informasi-pasien-rajal-bpjs/cetak-klaim?sep=' + noSep +'&type=' +type);
 })
 
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
 
 function getGrouper() {
     let _sep, _claim
    _claim = $('#no_klaimcovid').val()
    _sep = $('.no-sep').val()

     $.ajax({
         data: {
            no_sep: _claim != '' ? _claim : _sep ,
            pendaftaran_id: $('.pendaftaran-id-txt').val()
         },
         type: 'POST',
         url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/get-klaim',
         success: function (res) {
             var _res = JSON.parse(res)
             console.log(_res)
             if(_res.metadata?.error_no == "E2004") {
                 if(_claim != '') {
                     updateNoKlaim()
                 }
             }
             if (_res && typeof _res.response !== 'undefined') {
                 var _data = _res.response.data;
                 var _options = [];
                 var klaimStatus = _data.klaim_status_cd;
                 var dbSpecialCmg = _data.db_special_cmg;
                 var kemenkesStatus = _data.kemenkes_dc_status_cd;
                 var _total = 0;
                 var finalProc = 0;
                 var finalPros = 0;
                 var finalInv = 0;
                 var finalDrug = 0;
                 var _specCmg1 = [];
                 var _spSelected = [];
                 let tarif = 0;
                 let jaminanSelected = _res.response.data?.payor_id
                 console.log(jaminanSelected)

 
                 if (typeof _res.response.special_cmg_option !== 'undefined' && _res.response.special_cmg_option.length > 0) {
                     _specCmg = _res.response.special_cmg_option;
                     if (typeof _data.grouper.response.special_cmg !== 'undefined') {
                         _spSelected = _data.grouper.response.special_cmg;
                     }
                 } else {
                     _specCmg = _data.grouper?.response?.special_cmg;
                 }
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
                 
                $("#klaiminacbgform-klaim_penjamin").select2().val(jaminanSelected).trigger('change');

                handlingGrouperNull(_res)

                handlingEksekutif(_res)

                disabledInputan()

                 var _cbg = _res.response.data.grouper?.response?.cbg;
                 let _infoKelas = infoTxt + infoKelas($('#klaiminacbgform-tarif').val())
                 $('.info-txt').empty().append(_infoKelas);
                 $('.jenisrawat-txt').empty().append(jenisRawat);
                 $('.penyakit-nama').empty().append(_cbg?.description)
                 $('.kode-penyakit').empty().append(_cbg?.code)
                 $('.kolom-nosep').empty().append('')
                 if (typeof _cbg?.tariff != 'undefined') {
                     tarif = _cbg?.tariff
                 }
 
                 if (kemenkesStatus.toLowerCase() == 'sent') {
                     kemenkesStatus = 'Terkirim';
                     $('.kemenkes_status').removeClass('text-danger');
                     $('.kemenkes_status').addClass('text-success');
 
                 } else {
                     kemenkesStatus = 'Klaim belum terkirim ke Pusat Data Kementerian Kesehatan';
                     $('.kemenkes_status').removeClass('text-success');
                     $('.kemenkes_status').addClass('text-danger');
                 }
                 $('.kemenkes_status').empty().append(kemenkesStatus);
                 if (_specCmg) {
                     if (_spSelected) {
                         for (var i = 0; i < _spSelected.length; i++) {
                             for (var j = 0; j < _specCmg.length; j++) {
                                 if ((_spSelected[i]['type'] == _specCmg[j]['type']) && (_spSelected[i]['code'] == _specCmg[j]['code'])) {
                                     _specCmg[j]['selected'] = true;
                                 } else {
                                     _specCmg[j]['selected'] = false;
                                 }
                             }
                         }
                     }
                     _specCmg.map((value) => {
                         var tarif = 0;
                         if (typeof value.tariff !== 'undefined' && value.tariff) {
                             tarif = value.tariff;
                         }
                         if (value.type == SPECIAL_PROCEDURE) {
                             arrSpecProc.push({
                                 description: value.description,
                                 code: value.code,
                                 tariff: tarif
                             })
                         }
                         if (value.type == SPECIAL_PROSTHESIS) {
                             arrSpecPros.push({
                                 description: value.description,
                                 code: value.code,
                                 tariff: tarif
                             })
                         }
                         if (value.type == SPECIAL_INVESTIGATION) {
                             arrSpecInv.push({
                                 description: value.description,
                                 code: value.code,
                                 tariff: tarif
                             })
                         }
                         if (value.type == SPECIAL_DRUG) {
                             arrSpecDrug.push({
                                 description: value.description,
                                 code: value.code,
                                 tariff: tarif
                             })
                         }
                     });
                 }
                 if (arrSpecProc.length != 0) {
                     refreshOptionSelect2(ddSpecProc, arrSpecProc, {
                         id: 'code',
                         text: 'description'
                     });
                     let itemProc = arrSpecProc.find(({
                         code
                     }) => code === ddSpecProc.val());
                     ddSpecProc.attr('disabled', false);
                     if (arrSpecProc.length > 1) {
                         if (klaimStatus == 'final') {
                             itemProc = arrSpecProc.find(({
                                 code
                             }) => code !== '-')
 
                             ddSpecProc.val(itemProc.code)
                             ddSpecProc.attr('disabled', true);
                         }
                     } else {
                         ddSpecProc.attr('disabled', true);
                     }
                     if (itemProc) {
                         $('#sproc-kode').empty().append(itemProc.code)
                         $('#sproc-val').empty().append('<b>Rp. </b> ' + addCommas(itemProc.tariff))
                         finalProc = parseInt(itemProc.tariff)
                         _total += parseInt(itemProc.tariff)
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
                     ddSpecPros.attr('disabled', false);
 
                     if (arrSpecPros.length > 1) {
                         if (klaimStatus == 'final') {
                             itemPros = arrSpecPros.find(({
                                 code
                             }) => code !== '-')
 
                             ddSpecPros.val(itemPros.code)
                             ddSpecPros.attr('disabled', true);
                         }
                     } else {
                         ddSpecPros.attr('disabled', true);
                     }
                     if (itemPros) {
                         $('#spros-kode').empty().append(itemPros.code)
                         $('#spros-val').empty().append('<b>Rp. </b> ' + addCommas(itemPros.tariff))
                         finalPros = parseInt(itemPros.tariff)
                         _total += parseInt(itemPros.tariff);
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
                     ddSpecInv.attr('disabled', false);
 
                     if (arrSpecInv.length > 1) {
                         if (klaimStatus == 'final') {
                             itemInv = arrSpecInv.find(({
                                 code
                             }) => code !== '-')
 
                             ddSpecInv.val(itemInv.code)
                             ddSpecInv.attr('disabled', true)
                         }
                     } else {
                         ddSpecInv.attr('disabled', true);
                     }
                     if (itemInv) {
                         $('#inv-kode').empty().append(itemInv.code)
                         $('#inv-val').empty().append('<b>Rp. </b> ' + addCommas(itemInv.tariff))
                         finalInv = parseInt(itemInv.tariff)
                         _total += parseInt(itemInv.tariff)
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
                     ddSpecDrug.attr('disabled', false);
 
                     if (arrSpecDrug.length > 1) {
                         if (klaimStatus == 'final' && dbSpecialCmg) {
                             itemDrug = arrSpecDrug.find(({
                                 code
                             }) => code !== '-')
 
                             ddSpecDrug.val(itemDrug.code)
                             ddSpecDrug.attr('disabled', true);
                         }
                     } else {
                         ddSpecDrug.attr('disabled', true);
                     }
                     if (itemDrug) {
                         $('#drug-kode').empty().append(itemDrug.code)
                         $('#drug-val').empty().append('<b>Rp. </b> ' + addCommas(itemDrug.tariff))
                         finalDrug = parseInt(itemDrug.tariff)
                         _total += parseInt(itemDrug.tariff);
                     }
                 }
 
                 $('.harga-klaim').empty().append('<b>Rp. </b> ' + addCommas(tarif))
                 _total += parseInt(tarif);
 
                 if (_res.response.data.adl_sub_acute != 0) {
                     let resAccute = _res.response.data.adl_sub_acute
                     subAccute.empty().html(resAccute)
                 }
 
                 if (_res.response.data.adl_chronic != 0) {
                     let resCronic = _res.response.data.adl_chronic
                     cronic.empty().html(resCronic)
                 }
 
                 if (typeof _res.response.data.grouper.response?.sub_acute != 'undefined') {
                     var _subacute = _res.response.data.grouper.response?.sub_acute;
                     $('.subacute-detail').empty().append(_subacute.description)
                     $('.subacute-kode').empty().append(_subacute.code)
                     $('.subacute-harga').empty().append('<b>Rp. </b> ' + addCommas(_subacute.tariff))
                     _total += parseInt(_subacute.tariff);
                 }
                 if (typeof _res.response.data.grouper.response?.chronic != 'undefined') {
                     var _chronic = _res.response.data.grouper.response?.chronic;
                     $('.cronic-detail').empty().append(_chronic.description)
                     $('.cronic-kode').empty().append(_chronic.code)
                     $('.cronic-harga').empty().append('<b>Rp. </b> ' + addCommas(_chronic.tariff))
                     _total += parseInt(_chronic.tariff);
                 }
                 $('.total-harga').empty().append('<b>Rp. </b> ' + addCommas(_total))
                 _grouper[0] = {
                     name: 'Grouper[total]',
                     value: _total
                 };
                 _grouper[1] = {
                     name: 'Grouper[tambahan_biaya]',
                     value: 0
                 };
                 _grouper[2] = {
                     name: 'Grouper[spesial_drug]',
                     value: finalDrug
                 };
                 _grouper[3] = {
                     name: 'Grouper[spesial_investigation]',
                     value: finalInv
                 };
                 _grouper[4] = {
                     name: 'Grouper[spesial_procedure]',
                     value: finalProc
                 };
                 _grouper[5] = {
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
                 if (_cbg?.description.includes(INVALID_PARAMETERS) || _cbg?.description.includes(ERROR_PARAMETERS) || _cbg?.description.includes(SALAH_PARAMETERS)) {
                     ddSpecialOption(1)
                     $('#formfinal-btn-final-klaim').attr('disabled', true)
                     // return Error('Terjadi Kesalahan');
                 }
 
                 // covid Case
                 if($('#klaiminacbgform-klaim_penjamin').val() == COVID) {
                     let covid19 = _res.response.data.grouper.response.covid19_data;
                     if(typeof covid19 != 'undefined') {
                         let total_rawat = covid19.top_up_rawat
                         let total_jenazah = covid19.top_up_jenazah
                         let total = parseInt(total_rawat) 
                         let status_pasien = $('#klaiminacbgform-status_covid').val()
                         let komplikasi = (covid19.cc_ind == '1') ? 'DENGAN' : 'TANPA'  
                         $('.covid-header').empty().append(status_pasien + ' COVID-19 RAWAT JALAN')
                         $('.covid-harga').empty().append('<b>Rp. </b> ' + addCommas(total_rawat))
                         
                         $('.dz-preview').remove();
                         $('.dz-button').css('display', 'none');
 
 
                         if($('#klaiminacbgform-carapulang_id').val() == 4){
                             total += parseInt(total_jenazah)
                             if($('.covid-meninggal').hasClass('hidden') == false){
                                 $('.covid-meninggal').addClass('hidden')
                             }
                             if (typeof covid19.pemulasaraan_jenazah !== 'undefined') {
                                 appenDetailPemulasaraan(covid19.pemulasaraan_jenazah);
                             }
                         } else {
                             $('.pemulasaran').addClass('hidden')
                         }
                         $('.total-harga').empty().append('<b>Rp. </b> ' + addCommas(total))
                     } else {
                         $('.pemulasaran').addClass('hidden')
                     }
                 }
             } 
             else {
                 $('#btn-proses').prop('disabled', false);
                 $('#edit-koreksi').prop('disabled', false);
                 $('#btn-hapus-klaim').prop('disabled', true);
                 if($('#klaiminacbgform-klaim_penjamin').val() == COVID) {
                     let dataPasien = {
                         'kunjunganId': kunjunganId,
                         'noKartu': '',
                         'nosep' : nosep,
                         'noRm' : noRm,
                         'namaPasien': namaPasien,
                         'tglLahir': $('#klaiminacbgform-tgl_lahir').val(),
                         'gender': $('#klaiminacbgform-jeniskelamin').val(),
                         'jenisIdentitas': $('#klaiminacbgform-identitas_id').val(),
                         'noIdentitas' : $('#identitas_value').val()
                     }
                     generateNoCovid(dataPasien)
                 }
             }
         },
         complete: function () {
             if (_updated == 1) {
                 if (!$('#proses-final-klaim').hasClass('hidden')) {
                     $().docoForm('click', {
                         skipConfirm: true,
                         data: {
                             nosep: $('.no-sep').val(),
                             pendaftaranid: $('.pendaftaran-id-txt').val()
                         },
                         url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/hapus-klaim',
                         success: function (data) {
                             _delete = false
                             // $('#btn-proses').attr('disabled', false)
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
 
 $(document).on('change', '.group-tarif', function () {
     deleteGroupper()
     let totalTarifRs = $('#klaiminacbgform-total_tarifrs')
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
         _append += '<button style="float:right; margin-top: -3px" data-target="tbl-icd-10" data-key="' + i + '" class="btn btn-danger btn-xs btn-remove-diagnosa hide-me ' + _hide + '" style="float:right"><i class="fa fa-trash"></i></button>';
         _append += '<span class="badge badge-primary" style="float:right;margin-right: 3px;">' + v.kode_diagnosa + '</span>';
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
     var _primer = $('#klaiminacbgform-diagnosa_primer').val().split('#')
     $.each(_primer, function (k, v) {
         if (v == kode) {
             _state = false;
             return false;
         }
     })
     var _sekunder = $('#klaiminacbgform-diagnosa_sekunder').val().split('#')
     $.each(_sekunder, function (k, v) {
         if (v == kode) {
             _state = false;
             return false;
         }
     })
     return _state;
 }
 
 $('.delete-on-edit').on('change', function () {
     deleteGroupper()
 })
 
 var deleteGroupper = function (data) {
     cleaning()
     let _sep
     let _penjamin = $('#klaiminacbgform-klaim_penjamin').val()
     if($('#klaiminacbgform-klaim_penjamin').val() != COVID) {
         _sep = $('.no-sep').val()
     } else {
         _sep = $('#no_klaimcovid').val()
     }
 
     if(typeof data != 'undefined') {
         if(arrayJaminan.includes(_penjamin)) {
             _sep = $('#no_klaimcovid').val()
             _penjamin = _penjamin
         } else {
             _sep = $('.no-sep').val()
             _penjamin = JKN
         }
     }
     if (!$('#proses-final-klaim').hasClass('hidden') || _delete == true) {
        let valueJaminan = $('#klaiminacbgform-klaim_penjamin').val() 
        if(arrayJaminan.includes(valueJaminan)){
           _sep = $('#no_klaimcovid').val()
        } else {
           _sep = $('.no-sep').val()
        }
     }
 }
 
 $(document).on('click', '#formfinal-btn-kirim-klaim', function () {
    let _sep = null;
    let valueJaminan = $('#klaiminacbgform-klaim_penjamin').val() 
    if(arrayJaminan.includes(valueJaminan)){
        _sep = $('#no_klaimcovid').val()
    } else {
       _sep = $('.no-sep').val()
    }

     $().docoForm('click', {
         data: {
             nosep: _sep,
             pendaftaranid: $('.pendaftaran-id-txt').val()
         },
         url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/kirim-klaim-online',
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
 
 $(document).on('change', '.date', function () {
     let tgl_masuk = $('#klaiminacbgform-tgl_masuk')
     let tgl_keluar = $('#klaiminacbgform-tgl_keluar')
 
     tgl_keluar.val(tgl_masuk.val())
 })
 
 $(document).on('click', '.set-primer', function (e) {
     e.preventDefault()
     deleteGroupper()
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
         url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/set-primer',
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
 
 $('#no_sep').on('keypress', function (e) {
     if (e.keyCode !== undefined) {
         if (e.which == 13 || e.keyCode == 13) {
             e.preventDefault();
             $('#btn-search-sep').click();
         }
     }
 });
 
 $('#no_sep').on('keyup', function (e) {
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
 
 $('#btn-search-sep').on('click', function () {
     var valid = true
     let nosep = $('#no_sep').val();
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
                         var message = resbpjs.message;
                         const notFoundSep = "No SEP yang diinputkan tidak tersedia, Mohon di cek kembali";
                         docoNotification('error', 'Kesalahan', notFoundSep)
                         // $('.err-no-sep').html(notFoundSep);
                     } else {
                         var response = res.response.response;
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
     deleteGroupper()
     $.ajax({
         type: 'POST',
         url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/update',
         data: {
             data: data
         },
         dataType: 'JSON',
         success: function (res) {
             location.reload();
         }
     });
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
 
 function cleaning() {
     ddSpecialOption(false)
     tarifSpecial = [0, 0, 0, 0, 0]
     newTotal = 0
     finalTotal = 0
     oldTotal = 0
     $('#spros-val').html('');
     $('#sproc-val').html('');
     $('#drug-val').html('');
     $('#inv-val').html('');
     $('.info-txt').html('');
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
 }
 /* "A Product of PT Docotel Teknologi Powered by Sirs" */
 
 $(document).on('change', '#klaiminacbgform-klaim_penjamin', function(e) {
     e.preventDefault()
     let jaminan = $('#klaiminacbgform-klaim_penjamin').val()

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
        
        if(typeof $('#no_klaimcovid').val() == 'undefined' || $('#no_klaimcovid').val() == ""){
            let dataPasien = {
                'kunjunganId': kunjunganId,
                'noKartu': '',
                'nosep' : nosep,
                'noRm' : noRm,
                'namaPasien': namaPasien,
                'tglLahir': $('#klaiminacbgform-tgl_lahir').val(),
                'gender': $('#klaiminacbgform-jeniskelamin').val(),
                'jenisIdentitas': $('#klaiminacbgform-identitas_id').val(),
                'noIdentitas' : $('#identitas_value').val()
            }
            generateNoCovid(dataPasien)
            appendJaminan()
        } else {
            _nosep = $('#no_klaimcovid').val();
            if (!_getBerkas) {
                getBerkas();
            }
        }
     }
     if(jaminan == JKN){
         $('.covid-select').addClass('hidden')
         $('.jkn-select').removeClass('hidden')
         if(infoNoSep == '') {
             $('.jkn').addClass('hidden')
         }
         hiddenSitb()  
     } 
 })

 $(document).on('change', '#klaiminacbgform-carapulang_id', function(e) {
     checkPulang()
 })

 function hiddenJaminanKipi(hide = false) 
 {
    if(hide == true) {
        $('.kipi-section').addClass('hidden')
    }else{
        $('.kipi-section').removeClass('hidden')
    }
 }
 
 function hiddenSitb(hide = false) { 
    if(hide == true) {
        $('.pasien_tb_field').addClass('hidden')
    }else{
        $('.pasien_tb_field').removeClass('hidden')
    }
 }

 function checkPulang() {
     if($('#klaiminacbgform-carapulang_id').val() == 4){
         $('.covid-meninggal').removeClass('hidden')
     } else {
         if($('.covid-meninggal').hasClass('hidden') == false){
             $('.covid-meninggal').addClass('hidden')
         }
     }
 }
 
 
 function generateNoCovid(data) {
     $.ajax({
         type: 'POST',
         url: '/penjamin-asuransi/informasi-pasien-rajal-bpjs/generate-no-covid',
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
 
 function dataAppend(numberId, data) {
     
     if (data.length > 0) {
         $(`#tbl-icd-${numberId} tr`).empty()
         data.map((value, index) => {
             const primary = value?.is_icdprimer ? '<span class="badge badge-warning" style="float:right">ICD Primary</span>' : '' 
             let body = $(`#tbl-icd-${numberId}`).append(
                 `<tr>
                     <th style="width: 260px; border-right: 0px;"> ${value.diagnosa_nama} </th>
                     <th width="2" style=" border-left: 0px;"> 
                         ${primary}
                         <span class="badge badge-primary" style="float:right;margin-right: 3px"> ${value.diagnosa_kode}</span>
                     </th>
                 </tr>`
             )
         })
     }
 
     if (numberId == 9 && data.length == 0) {
         $(`#tbl-icd-${numberId}`).append(`
             <tr>
                 <th colspan="2" style="color:red; padding: 10px">Tidak Ada diagnosa dengan ICD 9 yang dipilih</th>
                 <th></th>
             </tr>
         `)
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
                    $(file.previewElement).find(".dz-image img").addClass("custom-image")

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
                     $(file.previewElement).find(".dz-image img").addClass("custom-image");
                     $(file.previewElement).find(".dz-image img").css("display","block");
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
                     $(file.previewElement).find(".dz-image img").addClass("custom-image");
                     $(file.previewElement).find(".dz-image img").css("display","block");
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
                     $(file.previewElement).find(".dz-image img").addClass("custom-image");
                     $(file.previewElement).find(".dz-image img").css("display","block");
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
                     $(file.previewElement).find(".dz-image img").addClass("custom-image");
                     $(file.previewElement).find(".dz-image img").css("display","block");
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
                     $(file.previewElement).find(".dz-image img").addClass("custom-image");
                     $(file.previewElement).find(".dz-image img").css("display","block");
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
                     $(file.previewElement).find(".dz-image img").addClass("custom-image");
                     $(file.previewElement).find(".dz-image img").css("display","block");
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
                                 newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-rajal-bpjs/preview-file?filename=${val.file_name}`)
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

                                Dropzone.forElement("div#upload3").emit("addedfile", mockFile);    
                                var newNode = document.createElement('a');
                                newNode.setAttribute('data-target', '#modal-preview')
                                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                                newNode.setAttribute('type', 'button')
                                newNode.className = 'dz-remove margin-10  btn-cetak';
                                newNode.innerHTML = 'DETAIL';
                                mockFile.previewTemplate.appendChild(newNode);

                             });   
                             break;
                         case "radiologi":
                             $.each(value, function(idx, val){
                                var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id, previewElement: val.message, accepted: true};

                                Dropzone.forElement("div#upload4").emit("addedfile", mockFile);    
                                var newNode = document.createElement('a');
                                newNode.setAttribute('data-target', '#modal-preview')
                                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                                newNode.setAttribute('type', 'button')
                                newNode.className = 'dz-remove margin-10  btn-cetak';
                                newNode.innerHTML = 'DETAIL';
                                mockFile.previewTemplate.appendChild(newNode);

                             });   
                             break;
                         case "penunjang_lain":
                             $.each(value, function(idx, val){
                                var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id, previewElement: val.message, accepted: true};

                                Dropzone.forElement("div#upload5").emit("addedfile", mockFile);    
                                var newNode = document.createElement('a');
                                newNode.setAttribute('data-target', '#modal-preview')
                                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                                newNode.setAttribute('type', 'button')
                                newNode.className = 'dz-remove margin-10  btn-cetak';
                                newNode.innerHTML = 'DETAIL';
                                mockFile.previewTemplate.appendChild(newNode);

                             });   
                             break;
                         case "resep_obat":
                             $.each(value, function(idx, val){
                                var mockFile = { name: val.file_name, size: val.file_size, file_id: val.file_id, previewElement: val.message, accepted: true};

                                Dropzone.forElement("div#upload6").emit("addedfile", mockFile);    
                                var newNode = document.createElement('a');
                                newNode.setAttribute('data-target', '#modal-preview')
                                newNode.setAttribute('data-url', `/penjamin-asuransi/informasi-pasien-ranap-bpjs/preview-file?filename=${val.file_name}`)
                                newNode.setAttribute('type', 'button')
                                newNode.className = 'dz-remove margin-10  btn-cetak';
                                newNode.innerHTML = 'DETAIL';
                                mockFile.previewTemplate.appendChild(newNode);

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
 
 function cleanCovid() {
     $('.dz-preview').remove();
     $('.dz-button').css('display', 'none');
     _nosep = $('#no_klaimcovid').val();
     _getBerkas = false;
     $('#no_klaimcovid').empty()
     let dataPasien = {
         'kunjunganId': kunjunganId,
         'noKartu': '',
         'nosep' : nosep,
         'noRm' : noRm,
         'namaPasien': namaPasien,
         'tglLahir': $('#klaiminacbgranapform-tgl_lahir').val(),
         'gender': $('#klaiminacbgranapform-jeniskelamin').val(),
         'jenisIdentitas': $('#klaiminacbgform-identitas_id').val(),
         'noIdentitas' : $('#identitas_value').val()
     }
     generateNoCovid(dataPasien)
 }
 
 function appenDetailPemulasaraan(data) {
     var el = `
             <tr id="tr-jenazah-detail"> 
                 <td></td>
                 <td colspan="2">
                     <table id="detail-pemulasaran" width="100%">
                     <thead>
                         <tr>
                             <th class="text-left">item</th>
                             <th class="text-right" width="100">tarif</th>
                             <th class="text-right" width="100">pelayanan</th>
                             <th class="text-right" width="100">subtotal</th>
                         </tr>
                     </thead>
                     <tbody>`;
     var total = 0;
     $.each(data, function(index, value) {
         total += parseInt(value);
         var label = '';
         switch(index.toLowerCase()) {
             case 'pemulasaraan':
                 label = 'Pemulasaraan Jenazah';
                 break;
             case 'kantong':
                 label = 'Kantong Jenazah';
                 break;
             case 'peti':
                 label = 'Peti Jenazah';
                 break;
             case 'plastik':
                 label = 'Plastik Erat';
                 break;
             case 'desinfektan_jenazah':
                 label = 'Desinfektan Jenazah';
                 break;
             case 'mobil':
                 label = 'Transport Mobil Jenazah';
                 break;
             case 'desinfektan_mobil':
                 label = 'Desinfektan Mobil Jenazah';
                 break;
         }
 
         el += `
             <tr>
                 <td class="text-left">${label}</td>
                 <td class="text-right">${value > 0 ? addCommas(value) : '-'}</td>
                 <td class="text-right">${value > 0 ? 'Ya' : 'Tidak'}</td>
                 <td class="text-right">${addCommas(value)}</td>
             </tr>
         `;
     });
         
     el += `             <tr>
                             <td colspan="3" class="total-label">Total Net</td>
                             <td class="text-right">${addCommas(total)}</td>
                         </tr>
                     </tbody>
                 </table>
             </td>
             <td></td>
             <td></td>
         </tr>
     `;
 
     $('.pemulasaran').removeClass('hidden')
     $('.pemulasaran-header').empty().append('YA')
     $('.pemulasaran-harga').empty().append('<b>Rp. </b> ' + addCommas(total))
     $('.tr-jenazah').after(el);
 }
 
 $('.btn-pemulasaran').on('click', function() {
     $('#tr-jenazah-detail').toggle();
 });

 $('#edit-koreksi').click(function (e) { 
    e.preventDefault();
    $('.modal-content').empty()

 });


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

function updateNoKlaim()
{
    $.ajax({
        type: "GET",
        url: "/penjamin-asuransi/informasi-pasien-rajal-bpjs/update-noklaim",
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

function handlingGrouperNull(_res) {
    if(_res.response.data.grouper.response != null) {
        $('#proses-final-klaim').removeClass('hidden');
    }

    if(_res.response.data.grouper.response == null) {
        $('#btn-hapus-klaim').prop('disabled', true);
        $('#btn-proses').prop('disabled', false);
    }
}


function disabledInputan() {
    if (!$('#proses-final-klaim').hasClass('hidden') || _delete == true) {
        $(".delete-on-edit").attr('disabled', true)
    }else{
        $(".delete-on-edit").attr('disabled', false)
    }
}


function handlingEksekutif(_res) {
    let tarifPoli = _res.response.data.tarif_poli_eks
    console.log(tarifPoli)
    if(tarifPoli > 0) {
        var formatter = new Intl.NumberFormat('en-US', {
            currency: 'IDR',
            maximumFractionDigits: 0
          });
        var jenis_kelasrawat = document.getElementById("klaiminacbgform-jenis_kelasrawat");
        jenis_kelasrawat.checked = true
        let isEks = $(".tarif_poli_eks")
        let tarifEks = $('#klaiminacbgform-tarif_poli_eks')
        tarifEks.removeClass('hidden').val(formatter.format(tarifPoli).replace(",", "."));
        isEks.removeClass('hidden');
    }
}


 /* "A Product of PT Docotel Teknologi Powered by Sirs" */
 