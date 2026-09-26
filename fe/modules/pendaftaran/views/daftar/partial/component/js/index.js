/** Const Multipayer */
const firstCbG = 'add_carabayargroup_1';
const secondCbG = 'add_carabayargroup_2';
const tujuanKunjTrue = 1;
const tujuanKunReset = 0;
const tujuanKunNormal = 0;
const tujuanKunProsedur = 1;
const tujuanKunKonsul = 2;
var _ruanganBpjs = 0;
const JenisPencarianRujukan = 1
const JenisPencarianManual = 2
const PARAM_INS_IGD = 'igd';
var tbl;
let jk = '';
let dataBayi = {}
const dataDropDownPegawai = []
var alamatBy = {}
let isIntegrasi = false

$(".steps-basic").steps({
    headerTag: "h6",
    bodyTag: "fieldset",
    transitionEffect: "slide",
    titleTemplate: '<span class="number">#index#</span> #title#',
    onStepChanged: function (event, currentIndex, priorIndex) {
        var _parent = $('fieldset[aria-hidden="false"]');
        _parent.find('[data-urutan="1"]').focus();
        _formPendaftaran.indexActive = currentIndex
    },
    onStepChanging: function (event, index, newIndex) {
        var referal = $("#referal");
        if(referal.length > 0) {
            referal.select2();
        }

        setValueReservasi();
        let _isMultiPayer = false;
        var _isNew = $('input[name="chk-statuspasien"]:checked').val();
        if (_formPendaftaran.params == 'penunjang' && $("input[name='TipePasienForm[is_aps]']")[2]['checked']) {
            var _pasienId = $('#pasienrs_pasien_id_hidden').val();
            var _caraBayar = $('#pasienrs_carabayar_id_hidden').val();
            var _asalRujukan = "0";
        } else {
            /** inputan step awal */
            var _caraBayar = $('#selectCarabayar').val();
            var _asalRujukan = $('#asalrujukan_id').val();
            var _pasienId = $('#no_rekam_medik').val();
            /** Group Cara Bayar */
            var _groupCaraBayar = $('.selectCarabayar').find(':selected').attr('data-id');
        }

        if ($('input[name="TipePasienForm[is_multi_payer]"]:checked').val()) {
            _isMultiPayer = true
        }
        /**  get current li */
        var _currentLi = $('ul[role="tablist"] > li.current > a').attr("aria-controls");
        /** get content field */
        var _contentStep = $('#' + _currentLi);
        var _childContent = _contentStep.children('div');
        var _idContent = _childContent.attr("id");

        _isNew = (typeof _isNew != 'undefined' ? _isNew : null);

        /** Kondisi step awal jika berubah */
        /** WIP kondisi pasien asuransi lama -> umum lama -> asuransi lama */
        if (_formPendaftaran.tipePasien.carabayar_id != _caraBayar
            || _formPendaftaran.tipePasien.asalrujukan_id != _asalRujukan
            || _formPendaftaran.tipePasien.tipe_pasien != _isNew
            || _formPendaftaran.tipePasien.no_rekam_medik != _pasienId) {
            validationTipePasien()
            return false;
        }

        /** Kondisi Untuk Cara bayar Asuransi Non BPJS */
        if (_groupCaraBayar == docoHelper.groupJaminan
            && (_formPendaftaran.tipePasien.no_asuransi != _formPendaftaran.tmpAsuransi.no_asuransi
                || _formPendaftaran.tmpAsuransi.prevPasien != _formPendaftaran.tmpAsuransi.pasien_id)) {
            validationTipePasien()
            return false;
        }

        if (_groupCaraBayar == docoHelper.groupBPJS) {
            let condition = conditionResetStepBpjs()
            if (!condition) {
                return false;
            }
        }

        if (_isMultiPayer) {
            let _addCarabayar = $('#addSelectCarabayar2').val();
            let _addCarabayarGroup = $('#addSelectCarabayar2').find(':selected').attr('data-id');
            let _isAddPayer = false

            if (_formPendaftaran.listTmp.is_add_payer != 'undefined' && _formPendaftaran.listTmp.is_add_payer == true) {
                _isAddPayer = true
            }

            if (_formPendaftaran.listTmp.add_carabayar_id_1 != 'undefined' && _formPendaftaran.listTmp.add_carabayar_id_1 != _addCarabayar) {
                validationTipePasien()
                return false;
            }

            if (_addCarabayarGroup == docoHelper.groupJaminan
                && (_formPendaftaran.listTmp.add_no_asuransi_1 != _formPendaftaran.firstTmpAsuransi.no_asuransi)) {
                validationTipePasien()
                return false;
            }

            if (_addCarabayarGroup == docoHelper.groupBPJS) {
                let condition = conditionResetStepBpjs()
                if (!condition) {
                    return false;
                }
            }

            if (_isAddPayer) {
                _addCarabayar = $('#addSelectCarabayar3').val();
                _addCarabayarGroup = $('#addSelectCarabayar3').find(':selected').attr('data-id');

                if (_formPendaftaran.listTmp.add_carabayar_id_2 != 'undefined' && _formPendaftaran.listTmp.add_carabayar_id_2 != _addCarabayar) {
                    validationTipePasien()
                    return false;
                }
                if (_addCarabayarGroup == docoHelper.groupJaminan
                    && (_formPendaftaran.listTmp.add_no_asuransi_2 != _formPendaftaran.secondTmpAsuransi.no_asuransi)) {
                    validationTipePasien()
                    return false;
                }

                if (_addCarabayarGroup == docoHelper.groupBPJS) {
                    let condition = conditionResetStepBpjs()
                    if (!condition) {
                        return false;
                    }
                }
            }
        }

        //jika bpjs, auto select ruangan
        if(_ruanganBpjs !== 0) {
            $('#ruangan_id').val(_ruanganBpjs).trigger('depdrop:change');
            $('#ruangan_id').val(_ruanganBpjs).trigger('change');
        }

        if (index < newIndex) {
            bindCheckboxRadio(newIndex)
            switch (_idContent) {
                case 'form-pasien-content':
                    return _formPendaftaran.validatePasien(_contentStep);
                    break;
                case 'form-rujukan-content':
                    return _formPendaftaran.validateRujukan(_contentStep);
                    break;
                case 'form-bpjs-error-content':
                    /**  WIP */
                    var _bpjsEr = $('input[name="chk-statuspasien-bpjs"]:checked').val();
                    var _noRm = $('#no_rekam_medik_bpjs').val();
                    var _content = $('#form-pasien-content');
                    if (typeof _bpjsEr != 'undefined') {
                        if ($('#no_rekam_medik_bpjs').val() != "") {
                            _formPendaftaran.getInfoPasien("", _noRm);
                            if (_content.length) {
                                $('.steps-basic').steps("remove", 2);
                            }
                        } else {
                            docoNotification('error', 'Proses Gagal!', "No Rekam Medik Harus diisi");
                            return false;
                        }
                    } else {
                        if (!_content.length) {
                            _formPendaftaran.formPasien(2);
                        }
                    }
                    break;
                case 'form-kunjungan-content':
                    return _formPendaftaran.validKunjugan(_contentStep);
                    break;
                case 'form-asuransi-content':
                    return _formPendaftaran.validateAsuransi(_contentStep);
                    break;
                case 'form-first-asuransi-content':
                    return _formPendaftaran.validateAsuransi(_contentStep, true);
                    break;
                case 'form-second-asuransi-content':
                    return _formPendaftaran.validateAsuransi(_contentStep, true);
                    break;
                case 'form-bpjs-content':
                    return _formPendaftaran.validateBpjs(_contentStep);
                    break;
                case 'form-multi-carabayar-content':
                    return _formPendaftaran.validateMultiCarabayar(_contentStep);
                    break;
                default:
                    break;
            }
        }
        return true;
    },
    labels: {
        finish: 'Submit',
        previous: 'Kembali',
        next: 'Selanjutnya',
    },
    onFinished: function (event, currentIndex) {
        var _jenisPendaftaran;
        let _isAddPayer = false

        if (_formPendaftaran.listTmp.is_add_payer != 'undefined' && _formPendaftaran.listTmp.is_add_payer == true) {
            _isAddPayer = true
        }

        if (_formPendaftaran.params == 'penunjang' && $("input[name='TipePasienForm[is_aps]']")[2]['checked']) {
            _jenisPendaftaran = 0;
            var no_rekam_medik_pasien = $('#pasienrs_pasien_id_hidden').val();
            var _carabayar = $('#pasienrs_carabayar_id_hidden').val();
            var _groupCaraBayar = $('#pasienrs_group_carabayar_hidden').val();
        } else {
            _jenisPendaftaran = 1;
            var no_rekam_medik_pasien = $("#no_rekam_medik").val();
            var no_rekam_medik_asuransi = _formPendaftaran.tmpAsuransi.no_rekam_medik;
            var _pasienId = _formPendaftaran.tmpAsuransi.pasien_id;
            var _carabayar = $("#selectCarabayar").val();

            if (_carabayar != 5 && _carabayar != 6) {
                if (_pasienId != null) {
                    if (no_rekam_medik_pasien != no_rekam_medik_asuransi) {
                        confirmationDialog(`Data asuransi telah dipakai pada pasien ${_formPendaftaran.tmpAsuransi.nama_pasien} (NO. RM ${_formPendaftaran.tmpAsuransi.no_rekam_medik}), lanjutkan proses pendaftaran?`, (isConfirm) => {
                            if (isConfirm) {
                                _formPendaftaran.tmpAsuransi.no_rekam_medik = no_rekam_medik_pasien;
                                $('.steps-basic').steps("next");
                                $('a[href="#finish"]').trigger('click');
                            }
                        })
                        return false;
                    }
                }
            }

            var _groupCaraBayar = $('.selectCarabayar').find(':selected').attr('data-id');
        }
        if (currentIndex == 0) {
            _formPendaftaran.generateForm();
        } else {
            var i = 0;
            var o = 0;
            var dataTarif = [];
            var _data = $('#tipe-pasien').serializeArray();
            $.each($('.check-aksi'), function () {
                if ($(this).is(':checked')) {
                    var _ke = $(this).attr('data-key');
                    if (typeof _formPendaftaran.listTarif[_ke] != 'undefined') {
                        dataTarif.push(_formPendaftaran.listTarif[_ke].daftartindakan_id);
                    }
                }
            });
            _data.push({
                name: 'list_tindakan',
                value: JSON.stringify(dataTarif)
            });

            if (_formPendaftaran.dataAntrian) {
                _data.push({
                    name: 'antrian_id',
                    value: _formPendaftaran.dataAntrian.antrian_id
                });
            }else {
                if(typeof _penOl !== 'undefined') {
                    if(typeof _penOl.all !== 'undefined') {
                        if(typeof _penOl.all.antrian_id !== 'undefined') {
                            _data.push({
                                name: 'antrian_id',
                                value: _penOl.all.antrian_id
                            });
                        }
                    }
                }
            }

            /** Push No Asuransi */
            if (_formPendaftaran.tmpAsuransi.no_asuransi) {
                _data.push({
                    name: 'no_asuransi',
                    value: _formPendaftaran.tmpAsuransi.no_asuransi
                });
                if (Object.keys(_formPendaftaran.pendaftaranOl).length) {
                    _formPendaftaran.tmpAsuransi.no_rekam_medik = _formPendaftaran.pendaftaranOl.no_rekam_medik;
                }
                _data.push({
                    name: 'no_rekam_medik',
                    value: _formPendaftaran.tmpAsuransi.no_rekam_medik
                });

            }

            _data.push({
                name: 'eligible_pasien',
                value: _formPendaftaran.eligibleAsuransi
            });

            _data.push({
                name: 'refresh_asuransi',
                value: _formPendaftaran.referensiAsuransi
            });
            
            if (_groupCaraBayar == docoHelper.groupBPJS && _jenisPendaftaran != 0) {
                if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined' && _formPendaftaran.pendaftaranOl.status_janji == true) {

                } else {
                    /** Flow Normal Pendaftaran BPJS */

                    if (!_formPendaftaran.dataBpjs.noKartu || _formPendaftaran.dataBpjs.noKartu == 'undefined') { //handle unauth
                        _formPendaftaran.dataBpjs.noKartu = _formPendaftaran.tmpBpjs.no_kartu

                        _data.push({
                            name: 'allow_bpjs',
                            value: 1,
                        })
                    }

                    _data.push({
                        name: 'is_bpjs',
                        value: 1
                    });

                    _data.push({
                        name: 'BpjsNewForm[no_kartu]',
                        value: _formPendaftaran.dataBpjs.noKartu
                    });

                    if (typeof _formPendaftaran.dataReturnBpjs.rujukan !== 'undefined') {
                        _data.push({
                            name: 'BpjsNewForm[ppk_rujukan]',
                            value: _formPendaftaran.dataReturnBpjs.rujukan.provPerujuk.kode
                        });
                    }
                    _data.push({
                        name: 'BpjsNewForm[nama_dpjp_melayani]',
                        value: _formPendaftaran.tmpBpjs.dpjpServeText
                    })

                    _data.push({
                        name: 'BpjsNewForm[kode_ppk_perujuk]',
                        value: _formPendaftaran.tmpBpjs.ppkPerujukId
                    })

                    _data.push({
                        name: 'BpjsNewForm[nama_ppk_perujuk]',
                        value: _formPendaftaran.tmpBpjs.ppkPerujukNama
                    })

                    _data.push({
                        name: 'BpjsNewForm[info_response]',
                        value: JSON.stringify(_formPendaftaran.dataReturnBpjs)
                    })
                }
            }

            var _penunjang = Object.keys(_formPendaftaran.listPenunjang).length;
            if (_penunjang) {
                _data.push({
                    name: 'list_penunjang',
                    value: JSON.stringify(_formPendaftaran.generateKunjungan(_formPendaftaran.listPenunjang))
                });
            }

            if ($("#tipepasienform-is_bbl").is(':checked')) {
                _data = _data.concat({ name: 'is_bbl', value: true })
            }

            if (Object.keys(_formPendaftaran.pendaftaranOl).length) {
                _data.push({
                    name: 'pendaftaranol_id',
                    value: _formPendaftaran.pendaftaranOl.pendaftaranol_id
                });
            }
            /* register permintaan konsul */
            if (Object.keys(_formPendaftaran.pendaftaranOl).length) {
                _data.push({
                    name: 'buatjanjipoli_id',
                    value: _formPendaftaran.pendaftaranOl.buatjanjipoli_id
                }, {
                    name: 'status_janji',
                    value: _formPendaftaran.pendaftaranOl.status_janji
                });
            }

            if (_formPendaftaran.dataPendaftaran) {
                if (_formPendaftaran.dataPendaftaran.jeniskasuspenyakit_id) {
                    _data.push({
                        name: 'KunjunganForm[jeniskasuspenyakit_id]',
                        value: _formPendaftaran.dataPendaftaran.jeniskasuspenyakit_id
                    });
                }

                if (_formPendaftaran.dataPendaftaran.kelaspelayanan_id) {
                    _data.push({
                        name: 'KunjunganForm[kelaspelayanan_id]',
                        value: _formPendaftaran.dataPendaftaran.kelaspelayanan_id
                    });
                }
            }

            if ($('[name="KunjunganForm[dokter_id]"]').val() != null) {
                _data.push({
                    name: 'KunjunganForm[jadwaldokter_id]',
                    value: $('[name="KunjunganForm[dokter_id]"]').find(":selected").data('jadwaldokter_id')
                });
            }

            /** Push list pasien mcu kolektif */
            if (instalasi == instalasiMcu) {
                if ($("input[name='TipePasienForm[is_kolektif]']")[2]['checked']) {
                    _data.push({
                        name: 'listPasienMcu',
                        value: JSON.stringify(dataPasienMcu)
                    });
                }
            }

            if ($('input[name="TipePasienForm[is_multi_payer]"]:checked').val()) {
                _data.push({
                    name: 'MultiCarabayarForm[is_add_payer]',
                    value: _isAddPayer
                });
            }

            var _fisioterapi = Object.keys(_formPendaftaran.listFisio).length;
            if (_fisioterapi) {
                _data.push({
                    name: 'list_fisioterapi',
                    value: JSON.stringify(_formPendaftaran.listFisio)
                });
            }

            const simpanPendaftaran = function (dataPost, extra = {}) {
                var attr = {
                    url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/simpan-kunjungan?params=' + _formPendaftaran.params,
                    data: dataPost,
                    success: function (data) {
                        _formPendaftaran.dataAntrian = null;
                        const pendaftaranol_id = _formPendaftaran?.pendaftaranOl?.pendaftaranol_id
                        _formPendaftaran.resetForm();
                        if (_groupCaraBayar == docoHelper.groupBPJS) {
                            _formPendaftaran.resetBpjs();
                            var _responseData = typeof data.response != 'undefined' ? data.response : {};
                            if (_responseData.is_bpjs) {
                                window.open(`/pendaftaran/end-point/print-sep?pendaftaran_id=${_responseData.id}`);
                            }
                        }
                        if (typeof instalasi_id !== 'undefined' && instalasi_id == instalasiMcu) {
                            _formPendaftaran.resetMcu()
                            $("#file-upload").val('');
                            dataPasienMcu = []
                            $("#asalrujukan_id").val(_formPendaftaran.tipePasien.asalrujukan_id).trigger('change');
                            // $("#asalrujukan_id").prop('disabled', true)
                        }

                        // reset state tipe pasien penunjang
                        if (_formPendaftaran.params == 'penunjang') {
                            let pasienRs = $("input[name='TipePasienForm[is_aps]']")[2]['checked']
                            let valChecked = 1
                            if (pasienRs) {
                                valChecked = 0
                            }
                            setTimeout(function () {
                                $("input[name='TipePasienForm[is_aps]']").val(valChecked).trigger('reset')
                            }, 500)
                        }
                        if(pendaftaranol_id != undefined) {
                            setTimeout(() => {
                                window.close()
                            }, 1200);
                            return false;
                        }
                        location.reload();
                    }, error(data) {
                        var _res = data.responseJSON.response;
                        if (_res.flag) {
                            setTimeout(function () {
                                var _tittle = `<b>${_res.title}</b><br>${_res.text}<br>Apakah anda yakin untuk meneruskan penyimpanan data ini ?`;
                                confirmationDialog(_tittle, function (reaction) {
                                    if (reaction) {
                                        var newData = dataPost
                                        if(_res.flag=='antrol_error'){
                                            newData.push({
                                                name: 'allow_antrol',
                                                value: 1,
                                            })
                                        }else{
                                            newData.push({
                                                name: 'allow_bpjs',
                                                value: 1,
                                            })
                                        }
                                        simpanPendaftaran(newData, { skipConfirm: true })
                                    }
                                })
                            }, 500)
                            return true;
                        }else if(_res.flag=='antrol_error'){
                        }else if(_res.data){
                            let errorMessages = _res.data;
                            for (let field in errorMessages) {
                                if(field.includes('PjpasienForm')){
                                    _formPendaftaran.indexActive= 1;
                                    $('.steps-basic').steps('previous');
                                    break;
                                }
                            }
                        
                        }
                    },
                };

                var mergeObj = $.extend({}, attr, extra);
                $().docoForm('click', mergeObj);
            }
            simpanPendaftaran(_data)
        }
    }
});
const bindCheckboxRadio = (indexPage) => {
    $(`#steps-uid-0-p-${indexPage}`).find('input[type="checkbox"], input[type="radio"]')
        .not('.notUniform').uniform({ radioClass: 'choice' });
    bindRadioWithSpace($(`#steps-uid-0-p-${indexPage}`))
    bindCheckboxWithSpace($(`#steps-uid-0-p-${indexPage}`))
}

var loadpemeriksaan = function (_obj) {
    var no = 0;
    var row = "";
    if (Object.keys(_obj).length > 0) {
        $.each(_obj, function (k, v) {
            let cyto
            let hargacyto
            if (v.is_cyto == 'true') {
                cyto = 'checked'
                hargacyto = parseInt(v.harga_tariftindakan) * (parseInt(v.persencyto_tindakan) / 100)
            } else {
                cyto = ''
                hargacyto = 0
            }
            if (typeof v.qty === 'undefined'){
                v.qty = 1
            }
            no++
            row += '<tr class="row-data">';
            row += '<td>' + no + '</td>';
            row += '<td>' + v.jenispemeriksaan + '</td>';
            row += '<td>' + v.namapemeriksaan + '</td>';
            //row += '<td>1</td>';
            row += '<td><input type="text" class="form-control qty-tindakan docoNumberOnly" style="width:50px;" value="' + v.qty + '" data-key="' + k + '"></td>';
            row += '<td><input type="checkbox" class="check-cyto" ' + cyto + ' data-key="' + k + '"></td>';
            row += '<td> Rp. ' + docoHelper.convertToRupiah(parseInt(v.harga_tariftindakan) + hargacyto) + '</td>';
            row += '<td><a class="btn btn-danger btn-sm btn-remove-pemeriksaan" data-key="' + k + '"><i class="fa fa-trash"></i></a></td>';
            row += '</tr>';
        })
    } else {
        row += '<tr class="row-default"> <td class="text-center" colspan="7">Belum ada data yang ditambahkan</td> </tr>'
    }
    sumHarga(_formPendaftaran.listPenunjang)
    $('#table-pemeriksaan').find('tbody tr').remove()
    $('#table-pemeriksaan').find('tbody').append(row)
}

var loadpaketmcu = function (_obj) {
    var no = 0;
    var row = "";
    if (Object.keys(_obj).length > 0) {
        $.each(_obj, function (k, v) {
            no++
            row += '<tr class="row-data">';
            row += '<td>' + no + '</td>';
            row += '<td>' + v.namapakettindakan + '</td>';
            row += '<td>1</td>';
            row += '<td> Rp. ' + docoHelper.convertToRupiah(parseInt(v.harga_tariftindakan)) + '</td>';
            row += '<td><a class="btn btn-danger btn-sm btn-remove-paket-mcu" data-key="' + k + '"><i class="fa fa-trash"></i></a></td>';
            row += '</tr>';
        })
    } else {
        row += '<tr class="row-default"> <td class="text-center" colspan="7">Belum ada data yang ditambahkan</td> </tr>'
    }
    sumHargaPaketMcu(_formPendaftaran.listPenunjang)
    $('#table-paket-mcu').find('tbody tr').remove()
    $('#table-paket-mcu').find('tbody').append(row)
}

var loadprogramfisio = function (_obj) {
    var no = 0;
    var row = "";
    if (Object.keys(_obj).length > 0) {
        $.each(_obj, function (k, v) {
            no++
            row += `<tr class="row-data">
                        <td>${no}</td>
                        <td>${v.dokterperujuk_nama}</td>
                        <td>${v.terapi}</td>
                        <td>${v.kunjungan_ke}</td>
                        <td><a class="btn btn-danger btn-sm btn-remove-program-fisio" data-key="${k}"><i class="fa fa-trash"></i></a></td>
                    </tr>`;
        })
    } else {
        row += `<tr class="row-default">
                    <td class="text-center" colspan="5">Belum ada data yang ditambahkan</td>
                </tr>`;
    }
    $('#table-paket-fisioterapi').find('tbody tr').remove()
    $('#table-paket-fisioterapi').find('tbody').append(row)
}

var sumHarga = function (_obj) {
    var total = 0;
    $.each(_obj, function (k, v) {
        if (v.is_cyto == 'true') {
            cyto = 'checked'
            hargacyto = parseInt(v.harga_tariftindakan) * (parseInt(v.persencyto_tindakan) / 100)
        }
        else {
            cyto = ''
            hargacyto = 0
        }
        if (typeof v.qty === 'undefined'){
            v.qty = 1
        }

        total += (parseInt(v.harga_tariftindakan) + hargacyto) * v.qty
    })
    $('.total-pemeriksaan').empty().html('<b>Rp. ' + docoHelper.convertToRupiah(total) + '</b>');
    total = 0;
}

var sumHargaPaketMcu = function (_obj) {
    var total = 0;
    $.each(_obj, function (k, v) {
        total += parseInt(v.harga_tariftindakan)
    })
    $('.total-paket-mcu').empty().html('<b>Rp. ' + docoHelper.convertToRupiah(total) + '</b>');
}

$(document).on('change', '.check-cyto', function () {
    var key = $(this).data('key')
    var is_cyto = 'false'
    if ($(this).prop('checked')) {
        is_cyto = 'true'
    }
    if (typeof _formPendaftaran.listPenunjang[key] !== 'undefined') {
        _formPendaftaran.listPenunjang[key].is_cyto = is_cyto
    }
    loadpemeriksaan(_formPendaftaran.listPenunjang)

});

$(document).on('change', '.qty-tindakan', function () {
    var key = $(this).data('key')
    var qty = $(this).val()
    _formPendaftaran.listPenunjang[key].qty = qty
    loadpemeriksaan(_formPendaftaran.listPenunjang)
});

/** Class Form Pendaftaran */
var _formPendaftaran = {
    params: '',
    isForeignAsuransi: false,
    isConfirmedForeignAsuransi: false,
    first_search: null,
    pendaftaranOl: {},
    dataAntrian: null,
    dataPendaftaran: null,
    historyKunjugan: null,
    formErrorBpjs: null,
    formInputKunjugan: null,
    formInputBpjs: null,
    formInputPj: null,
    formInputPasien: null,
    formInputAsuransi: null,
    formFirstInputAsuransi: null, //multi payer
    formSecondInputAsuransi: null,
    formInputRujukan: null,
    formPasienMcu: null,
    formInputMultiCarabayar: null,
    listTarif: [],
    listPenunjang: {},
    listFisio: {},
    totalTarif: 0,
    tmpAsuransi: {
        no_rekam_medik: null,
        pasien_id: null,
        no_asuransi: null,
        namapemilikasuransi: null,
        nomorpokokperusahaan: null,
        namaperusahaan: null,
        kelastanggunganasuransi_id: null,
        penjamingrade_id: null,
        prevPasien: null,
    },
    firstTmpAsuransi: { // tmp asuransi multi payer
        no_rekam_medik: null,
        pasien_id: null,
        no_asuransi: null,
        namapemilikasuransi: null,
        nomorpokokperusahaan: null,
        namaperusahaan: null,
        kelastanggunganasuransi_id: null,
        penjamingrade_id: null,
        prevPasien: null,
    },
    secondTmpAsuransi: { // tmp asuransi multi payer
        no_rekam_medik: null,
        pasien_id: null,
        no_asuransi: null,
        namapemilikasuransi: null,
        nomorpokokperusahaan: null,
        namaperusahaan: null,
        kelastanggunganasuransi_id: null,
        penjamingrade_id: null,
        prevPasien: null,
    },
    eligibleAsuransi: null,
    referensiAsuransi: null,
    ignoreAsuransi: false,
    resetAsuransi: function () {
        _formPendaftaran.tmpAsuransi = {
            no_rekam_medik: null,
            pasien_id: null,
            no_asuransi: null,
            namapemilikasuransi: null,
            nomorpokokperusahaan: null,
            namaperusahaan: null,
            kelastanggunganasuransi_id: null,
            penjamingrade_id: null,
            prevPasien: null
        };
        $('.field-tipepasienform-no_asuransi span.input-group-addon').trigger('click');
    },
    tipePasien: {
        carabayar_id: null,
        penjamin_id: null,
        asalrujukan_id: null,
        groupcarabayar_id: null,
        no_rekam_medik: null,
        tipe_pasien: 1,
        no_asuransi: null,
        no_bpjs: null,
        is_rujuk: null
    },
    tmpBpjs: {
        no_kartu: null,
        asal_rujukan: null,
        jenis_pencarian: null,
        jenis_kartu: null,
        jenis_pelayanan: null,
        no_rujukan_f: null,
        dpjpId: null,
        dpjpServeText: null,
        ppkPerujukId: null,
        ppkPerujukNama: null,
        poliTujuan: null
    },
    dataBpjs: {},
    dataPostRanap: {},
    dataReturnBpjs: {},
    dataRujukanBpjs: {},
    dataPasienMcu: [],
    tmpDataMcu: {
        dataComplete: null,
        dataIncomplete: null,
        dataFile: null,
        dataDoubleRm: null,
    },
    tmpDataPj: {},
    listTmp: {},
    resetBpjs: function () {
        _formPendaftaran.tmpBpjs = {
            no_kartu: null,
            jenis_pencarian: null,
            jenis_kartu: null,
            jenis_pelayanan: null
        };
        $("#no_kartu").val("");
        $("input[name='BpjsNewForm[jenis_rujukan]']").prop("checked", false);
        $("input[name='BpjsNewForm[jenis_kartu]']").prop("checked", false);
        /** Remove span checked */
        $("input[name='BpjsNewForm[jenis_rujukan]']").closest("span").removeClass("checked");
        $("input[name='BpjsNewForm[jenis_kartu]']").closest("span").removeClass("checked");
        $("#asal_rujukan_1").val('').trigger('change');
        var date = new Date();
        var picker = $('#tanggal_sep_1').pickadate('picker');
        picker.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
        $('.selectCarabayar').trigger("change");
    },
    init: function () {
        $("input[name=chk-statuspasien]").change(function () {
            stateNextStep('next')
            if (this.checked) {
                $('#no_rekam_medik').prop("disabled", false);
                $(".field-no_rekam_medik").addClass('required');
            } else {
                //$('#no_rekam_medik').val('').trigger('change');
                $('#no_rekam_medik').prop("disabled", true);
                $(".field-no_rekam_medik").removeClass('required');
            }
        });
        bindCheckboxRadio(_formPendaftaran.indexActive)
        $('.field-no_rekam_medik').hide();
        $('.field-tipepasienform-no_asuransi').hide();
        $('.field-multicarabayarform-add_no_asuransi_1').hide();
        $('#selectCarabayar').focus();
        $(document).on('click', '.field-tipepasienform-no_asuransi span.input-group-addon', function (event) {
            event.preventDefault();
            _formPendaftaran.tipePasien.no_asuransi = null;
            _formPendaftaran.tmpAsuransi = {
                kelastanggunganasuransi_id: null,
                namapemilikasuransi: null,
                namaperusahaan: null,
                no_asuransi: null,
                nomorpokokperusahaan: null,
                pasien_id: null,
                no_rekam_medik: null,
                prevPasien: _formPendaftaran.tmpAsuransi.prevPasien
            };
            $('#tipepasienform-no_asuransi').val('');
            $('#tipepasienform-no_asuransi').prop('readonly', false);
            $('.field-tipepasienform-no_asuransi').find("#note-asuransi").html("");
        });
        $(document).on('change', '#tipepasienform-no_asuransi', function (obj, item) {
            var _data = obj.data;
            var _value = $(this).val();
            _formPendaftaran.ignoreAsuransi = false;

            _formPendaftaran.tipePasien.no_asuransi = "";
            _formPendaftaran.tipePasien.no_asuransi = _value;

            if (!_formPendaftaran.tmpAsuransi.pasien_id) {
                _formPendaftaran.tmpAsuransi.no_asuransi = null;
                $('.field-tipepasienform-no_asuransi').find("#note-asuransi").html("");
            } else {
                _formPendaftaran.tipePasien.no_asuransi = null;
                _formPendaftaran.tmpAsuransi.no_asuransi = _value
                $('#tipepasienform-no_asuransi').prop('readonly', false);
            }

            if (_formPendaftaran.tmpAsuransi.no_rekam_medik == null) {
                _formPendaftaran.tmpAsuransi.no_rekam_medik = $("#no_rekam_medik").val();
            }
        });
        $(document).on('change', '#multicarabayarform-add_no_asuransi_1', function (obj, item) { //multi payer
            var _data = obj.data;
            let _value = $(this).val();

            _formPendaftaran.listTmp.add_no_asuransi_1 = "";
            _formPendaftaran.listTmp.add_no_asuransi_1 = _value;

            if (!_formPendaftaran.firstTmpAsuransi.pasien_id && _formPendaftaran.firstTmpAsuransi.pasien_id != null) {
                _formPendaftaran.firstTmpAsuransi.no_asuransi = null;
                $('.field-multicarabayarform-add_no_asuransi_1').find("#first-note-asuransi").html("");
            } else {
                _formPendaftaran.listTmp.add_no_asuransi_1 = null;
                _formPendaftaran.firstTmpAsuransi.no_asuransi = _value
                $('#multicarabayarform-add_no_asuransi_1').prop('readonly', false);
            }

            if (_formPendaftaran.firstTmpAsuransi.no_rekam_medik == null) {
                _formPendaftaran.firstTmpAsuransi.no_rekam_medik = $("#no_rekam_medik").val();
            }
        });
        $(document).on('change', '#multicarabayarform-add_no_asuransi_2', function (obj, item) {
            var _data = obj.data;
            let _value = $(this).val();

            _formPendaftaran.listTmp.add_no_asuransi_2 = "";
            _formPendaftaran.listTmp.add_no_asuransi_2 = _value;

            if (!_formPendaftaran.secondTmpAsuransi.pasien_id && _formPendaftaran.secondTmpAsuransi.pasien_id != null) {
                _formPendaftaran.secondTmpAsuransi.no_asuransi = null;
                $('.field-multicarabayarform-add_no_asuransi_2').find("#second-note-asuransi").html("");
            } else {
                _formPendaftaran.listTmp.add_no_asuransi_2 = null;
                _formPendaftaran.secondTmpAsuransi.no_asuransi = _value
                $('#multicarabayarform-add_no_asuransi_2').prop('readonly', false);
            }

            if (_formPendaftaran.secondTmpAsuransi.no_rekam_medik == null) {
                _formPendaftaran.secondTmpAsuransi.no_rekam_medik = $("#no_rekam_medik").val();
            }
        });
        $('#no_rekam_medik').select2({
            allowClear: false,
            ajax: {
                url: '/pendaftaran/end-point/norm',
                dataType: 'json',
                data: function (params) {
                    var _isAps = $('input[name="TipePasienForm[is_aps]"]:checked').val();
                    return {
                        q: params.term,
                        isRanap: 0,
                        isBbl: $("#tipepasienform-is_bbl").is(':checked'),
                        isAps: _isAps
                    };
                },
            },
            placeholder: 'No Rm / Nama pasien / Tanggal lahir',
            minimumInputLength: 3,
            templateResult: function (noRm) {
                return noRm.text;
            },
            templateSelection: function (noRm) {
                $(".pendaftaran-id").val(noRm.pendaftaran_id);
                if(typeof noRm.pasien_id !== 'undefined'){
                    $("#pasien_id_hidden").val(noRm.pasien_id);
                }
                return noRm.text;
            }
        });
        $("#no_rekam_medik").on("change", function ({delegateTarget}) {
            var no_rekam_medik = $(this).val();
            var carabayar_id = $(".selectCarabayar").val();
            var penjamin_id = $(".selectPenjamin").val();
            var pasien_id = $("#pasien_id_hidden").val();
            // if (no_rekam_medik != "") {
            //     var validasi = validasiKunjungan(no_rekam_medik);
            // }
            if ($(delegateTarget).val() != "") {
                var validasi = validasiKunjungan(no_rekam_medik);
            }

            /** case konsul pake var pendaftaranOl */
            if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined' && typeof _formPendaftaran.pendaftaranOl.penjamin_id !== 'undefined') {
                penjamin_id = _formPendaftaran.pendaftaranOl.penjamin_id
            }

            if ((carabayar_id != '' && carabayar_id != 5 && carabayar_id != 6) && no_rekam_medik != "") {
                if (typeof penjamin_id != 'undefined' || penjamin_id != "") {
                    $.ajax({
                        url: '/pendaftaran/end-point/get-no-asuransi?no_rekam_medik=' + no_rekam_medik + "&carabayar_id=" + carabayar_id + "&penjamin_id=" + penjamin_id,
                        type: 'GET',
                        dataType: 'JSON',
                        success: function (res) {
                            if (res.response != "") {
                                var response = res.response;
                                if (response !== null) {
                                    var _data_asuransi = response.data_asuransi;
                                    var _nama_pasien = response.nama_pasien;
                                    if (_data_asuransi != null) {
                                        $("#tipepasienform-no_asuransi").val(_data_asuransi.nokartuasuransi).trigger("change");
                                        $("#tipepasienform-no_asuransi").prop("readonly", false);
                                        _formPendaftaran.tmpAsuransi.no_rekam_medik = no_rekam_medik;
                                        _formPendaftaran.tmpAsuransi.pasien_id = pasien_id;
                                        _formPendaftaran.tmpAsuransi.no_asuransi = _data_asuransi.nokartuasuransi;
                                        _formPendaftaran.tmpAsuransi.namapemilikasuransi = _data_asuransi.namapemilikasuransi;
                                        _formPendaftaran.tmpAsuransi.nomorpokokperusahaan = _data_asuransi.nomorpokokperusahaan;
                                        _formPendaftaran.tmpAsuransi.namaperusahaan = _data_asuransi.namaperusahaan;
                                        _formPendaftaran.tmpAsuransi.kelastanggunganasuransi_id = _data_asuransi.kelastanggunganasuransi_id;

                                        $('.field-tipepasienform-no_asuransi').find('#note-asuransi').html('<i>Atas Nama : ' + _formPendaftaran.tmpAsuransi.namapemilikasuransi + '<br>Pasien Pengguna Asuransi : ' + _nama_pasien + '<br> Nomor Rekam Medik Pasien : ' + _formPendaftaran.tmpAsuransi.no_rekam_medik);
                                    }
                                }
                            }
                        },
                        error: function (err) {
                            console.log("error get no asuransi");
                            console.log(err);
                        }
                    });
                }
            }

        });
        _formPendaftaran.historyKunjugan = $('#kunjungan').clone(true);
        _formPendaftaran.formInputRujukan = $('#form-rujukan').clone(true);
        _formPendaftaran.formInputKunjugan = $('#form-input-kunjugan').clone(true);
        _formPendaftaran.formInputPj = $('#form-input-pj').clone(true);
        _formPendaftaran.formInputPasien = $('#form-input-pasien').clone(true);
        _formPendaftaran.formInputAsuransi = $('#form-input-asuransi').clone(true);
        _formPendaftaran.formInputBpjs = $('#form-input-bpjs').clone(true);
        _formPendaftaran.formErrorBpjs = $('#form-bpjs-error').clone(true);
        _formPendaftaran.formPasienMcu = $('#form-pasien-mcu').clone(true);
        _formPendaftaran.formFirstInputAsuransi = $('#form-input-first-asuransi').clone(true);
        _formPendaftaran.formSecondInputAsuransi = $('#form-input-second-asuransi').clone(true);
        $('#form-input-kunjugan > .select2').select2("destroy");
        $('#form-input-pj > .select2').select2("destroy");
        $('#form-input-pasien > .select2').select2("destroy");
        $('#form-multi-carabayar > .select2').select2("destroy");
        $('#list-history').remove();
        $('#form-input-kunjugan').remove();
        $('#form-rujukan').remove();
        $('#form-input-pj').remove();
        $('#form-input-pasien').remove();
        $('#form-input-asuransi').remove();
        $('#form-input-bpjs').remove();
        $('#form-bpjs-error').remove();
        $('#form-pasien-mcu').remove();
        $('#form-input-first-asuransi').remove();
        $('#form-input-second-asuransi').remove();
        /** Untuk BPJS */
        $('.styled, .multiselect-container input').uniform({
            radioClass: 'choice'
        });
        $('#jenis_rujukan').change(function () {
            var base = $("input:radio[name='BpjsNewForm[jenis_rujukan]']:checked").val();
            if(instalasi == 1) {
                if (base == 1) {
                    $('#base-rujukan').show();
                    $('#base-rujukan-manual').hide();
                } else {
                    $('#base-rujukan').hide();
                    $('#base-rujukan-manual').show();
                    $('.field-jenis_pelayanan').hide()
                    $('#asal_rujukan_manual').show()
                }
            } else {
                if (base == 1) {
                    $('#base-rujukan').show();
                    $('#base-rujukan-manual').hide();
                } else {
                    $('#base-rujukan').hide();
                    $('#base-rujukan-manual').show();
                }
            }
        });
        var date = new Date();
        $('#tanggal_sep_1, #tanggal_sep, #tanggal_rujukan, #tanggal_kejadian').pickadate({
            format: 'dd mmm yyyy',
            formatSubmit: 'yyyy-mm-dd',
            max: [date.getFullYear(),date.getMonth(),date.getDate()],
            onStart: function () {
                this.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
            }
        });
        $("#tanggal_sep_1").prop("readonly", false);
        $(document).on('change', '.selectCarabayar', function () {
            var carabayar = $('.selectCarabayar').val();
            var carabayar_group = $('.selectCarabayar').find(':selected').attr('data-id');
            $("#carabayar_id_hidden").val(carabayar);
            _formPendaftaran.listTmp['carabayar_utama'] = carabayar
            _formPendaftaran.listTmp['carabayargroup_utama'] = carabayar_group
            if (instalasi == instalasiMcu) {
                if ($("input[name='TipePasienForm[is_kolektif]']")[1]['checked']) {
                    stateCaraBayar(carabayar_group)
                } else {
                    $('.field-no_rekam_medik').hide();
                }
            } else {
                stateCaraBayar(carabayar_group)
            }

            if (carabayar != 2) {
                enableButtonPencarianAsuransi(false)
            }

            var _pasienIdOl = $('#pasien_ol_status').val();
            if (_pasienIdOl != "" && _pasienIdOl == "310") { //status pasien baru
                $('input[name="chk-statuspasien"]').prop("checked", false);
                //$('#no_rekam_medik').val('').trigger('change');
                $('#no_rekam_medik').prop("disabled", true);
                $(".field-no_rekam_medik").removeClass('required');
            } else {
                $('input[name="chk-statuspasien"]').prop("checked", true);
            }


            $(".field-no_rekam_medik").addClass('required');
            //$('#no_rekam_medik').val('').trigger('change');
            $("#note-asuransi").html("");
            $("#tipepasienform-no_asuransi").val("").trigger("change");
        });
        $(document).on("change", "#penjamin_id", function () {
            //$('#no_rekam_medik').val('').trigger('change');
            var valuePenjamin = parseInt($(this).val());
            if (typeof _penjaminIntegrasi != 'undefined') {
                if (_penjaminIntegrasi != null) {
                    var cekArray = _penjaminIntegrasi.includes(valuePenjamin)
                    enableButtonPencarianAsuransi(cekArray)
                }
            }

            $("#note-asuransi").html("");
            $("#tipepasienform-no_asuransi").val("").trigger("change");
        });
        $(document).on("change", "#add_penjamin_id_1", function () {
            $("#first-note-asuransi").html("");
            $("#multicarabayarform-add_no_asuransi_1").val("").trigger("change");
        });
        $(document).on("change", "#add_penjamin_id_2", function () {
            $("#second-note-asuransi").html("");
            $("#multicarabayarform-add_no_asuransi_2").val("").trigger("change");
        });
        $(document).on('select2:close', '.select2-hidden-accessible', ({ currentTarget }) => {
            $(currentTarget).focus()
        })
        $(document).on('change', '#tipepasienform-is_kolektif', function () {
            _formPendaftaran.resetForm()
            _formPendaftaran.resetMcu()
            if ($("input[name='TipePasienForm[is_kolektif]']")[2]['checked']) {
                $('#row-template').prop('hidden', false)
                $('.chkbox-multi').hide()
            } else {
                $('#row-template').prop('hidden', true)
                $('.chkbox-multi').show()
            }
        })
        $('#btn-edit-info-pasien').click(function(e){
            e.preventDefault();
            window.open($(this).attr('data-target'), '_blank');
            var getSession = setInterval(function() {
                if (localStorage.getItem("isPasienUpdated")) {
                    localStorage.removeItem("isPasienUpdated");
                    clearInterval(getSession);
                    var _noRm = _formPendaftaran.tipePasien.no_rekam_medik;
                    _formPendaftaran.getInfoPasien("", _noRm);
                }
            },2000);
        });
        $('#informasi-data-kunjungan').click(function (e) {
            e.preventDefault();
            if ($(this).attr('aria-expanded') == 'false') {
                _formPendaftaran.getInfoKunjungan();
            }
        });
        _formPendaftaran.additional();
        if (_formPendaftaran.params == 'penunjang') {
            $('.chkbox-multi').closest('.col-md-10').addClass('col-md-12')
        }
        $('#tipe-pasien').on('change', '#no_rujukan,#no_kartu,#no_rujukan_1,#no_telp,#no_surat_kontrol', function() {
            this.value=this.value.trim().toUpperCase();
        })

        localStorage.removeItem("eligiblePesertaAsuransi")
    },
    additional: function () {

    },
    checkEligiblePatient: function (data, tmpAsuransi) {

        let numberAsuransi = data.no_asuransi
        let penjaminId = data.penjamin_id
        let noRekamMedik = data.no_rekam_medik
    
        if(tmpAsuransi.hasOwnProperty('nokartuasuransi')) { 
            if(tmpAsuransi.no_asuransi != null && tmpAsuransi.no_asuransi.length < 20) {
                numberAsuransi = tmpAsuransi.no_asuransi
            } else {
                numberAsuransi = tmpAsuransi.nokartuasuransi
            }
        }
    
        if (tmpAsuransi.hasOwnProperty('no_rekam_medik')) {
          noRekamMedik = tmpAsuransi.no_rekam_medik
        }
        
        $.ajax({
            type: 'GET',
            url: '/pendaftaran/daftar/cek-eligible-peserta',
            data: {
                no_asuransi: numberAsuransi,
                penjamin_id: penjaminId
            },
            async: true,
            beforeSend: function () {
                showLoader();
            },
            success: function (response) {
                /**
                 * Handling apabila integrasi belum tersedia
                 */
                if(typeof response.isIntegrasi != 'undefined') {
                    isIntegrasi = response.isIntegrasi
                }

                if(isIntegrasi == false) {
                    _formPendaftaran.ignoreAsuransi = true
                    _formPendaftaran.generateForm()
                    return false;
                } 

                if(response?.response?.status !== 0) {
                    docoNotification("warning", "Informasi", response?.response?.message);
                    return false;
                }

                _formPendaftaran.ignoreAsuransi = false
                _formPendaftaran.eligibleAsuransi = JSON.stringify(response?.response?.data)
                var pasienLama =  $('input[name=chk-statuspasien]').is(':checked')

                if(! pasienLama) {
                    let responsePeserta = response?.response?.data
                    localStorage.setItem("eligiblePesertaAsuransi", JSON.stringify(responsePeserta))

                    _formPendaftaran.tmpAsuransi.namapemilikasuransi = responsePeserta.dataPeserta?.namapeserta
                    _formPendaftaran.tmpAsuransi.namaperusahaan = responsePeserta.dataPeserta?.namaperusahaan
                }

                $('.eligible-peserta').attr('data-width', '900px')
                $('.eligible-peserta').attr('action',`/pendaftaran/daftar/confirm-eligible-peserta?no_asuransi=${numberAsuransi}&penjamin_id=${penjaminId}&no_rm=${noRekamMedik}&is_pasienbaru=${pasienLama ? 1 : 0}`);
                $('.eligible-peserta').click();
            },
            error: function (response) {
                docoNotification("warning", "Proses Gagal !", response?.responseJSON.message);
            }
        });
    },
    indexActive: 0,
    generateForm: function () { //sini
        var _jenisPendaftaran;
        var isKolektif = false //mcu
        var isMultiPayer = false
        var _isBpjs = false;

        if (_formPendaftaran.params == 'penunjang' && $("input[name='TipePasienForm[is_aps]']")[2]['checked'] === true) {
            _jenisPendaftaran = 0;
        } else {
            _jenisPendaftaran = 1;
        }

        if ($('input[name="TipePasienForm[is_multi_payer]"]:checked').val()) {
            isMultiPayer = true
        }

        if (_jenisPendaftaran == 1) {
            var _pasienId = $('#no_rekam_medik').val();
            var _caraBayar = $('#selectCarabayar').val();
            var _penjamin = $('#penjamin_id').val();
            var _asalRujukan = $('#asalrujukan_id').val();
            var _groupCaraBayar = $('.selectCarabayar').find(':selected').attr('data-id');
            var _tipeId = $('input[name=chk-statuspasien]:checked').val();
        } else {
            var _pasienId = $('#pasienrs_pasien_id_hidden').val();
            var _caraBayar = $('#pasienrs_carabayar_id_hidden').val();
            var _penjamin = $('#pasienrs_penjamin_id_hidden').val();
            var _asalRujukan = "0";
            var _groupCaraBayar = $('#pasienrs_group_carabayar_hidden').val();
            var _noPendaftaran = $('#pasienrs_no_pendaftaran_hidden').val();
            var _tipeId = 1;
        }

        var _noAsuransi = _formPendaftaran.tmpAsuransi.no_asuransi
            ? _formPendaftaran.tmpAsuransi.no_asuransi : $('#tipepasienform-no_asuransi').val();
        /** Kebutuhan Untuk BPJS */
        var _bpjs = {};
        var _pencarianBpjs = $("input[name='BpjsNewForm[jenis_rujukan]']:checked").val();
        var _jenisKartu = $("input[name='BpjsNewForm[jenis_kartu]']:checked").val();
        var _tglSep = $("input[name='BpjsNewForm[tanggal_sep]']").val();
        var _jenisPelayanan = $("select[name='BpjsNewForm[jenis_pelayanan]']").val();
        var _noKartu = $("input[name='BpjsNewForm[no_kartu]']").val();
        var _noRujukan = $("input[name='BpjsNewForm[no_rujukan_f]']").val();
        var _asalRujukanBpjs = $("select[name='BpjsNewForm[asal_rujukan]']").val();
        /** end Bpjs */
        _tipeId = (typeof _tipeId != 'undefined' ? _tipeId : null);
        $('#ket-bpjs').hide();

        var _data = {};
        let isKonsul = false
        /** Case Daftar Konsul BPJS Hari sama */
        if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined' && _formPendaftaran.pendaftaranOl.status_janji == true) {
            isKonsul = true
        }

        if (_jenisPendaftaran == 1) {
            _data = {
                carabayar_id: _caraBayar,
                penjamin_id: _penjamin,
                asalrujukan_id: _asalRujukan,
                groupcarabayar_id: _groupCaraBayar,
                no_rekam_medik: _pasienId,
                tipe_pasien: _tipeId,
                no_asuransi: _noAsuransi,
                is_multi_payer: isMultiPayer
            };
        } else {
            _data = {
                carabayar_id: _caraBayar,
                penjamin_id: _penjamin,
                no_rekam_medik: _pasienId,
                asalrujukan_id: _asalRujukan,
                groupcarabayar_id: _groupCaraBayar,
                tipe_pasien: _tipeId,
                jenis_pendaftaran: 'pasien-rs',
                no_pendaftaran: _noPendaftaran
                // no_asuransi: _noAsuransi,
            };
        }

        if (_groupCaraBayar == docoHelper.groupBPJS) {
            _bpjs = {
                is_bpjs: true,
                no_kartu: _noKartu,
                jenis_pencarian: _pencarianBpjs,
                jenis_kartu: _jenisKartu,
                jenis_pelayanan: _jenisPelayanan,
                tanggal_sep: _tglSep,
                asal_rujukan: _asalRujukanBpjs,
                no_rujukan_f: _noRujukan,
                asalrujukan_id: 1
            };
            _isBpjs = true
        }

        if (isKonsul) {
            _data.penjamin_id = _formPendaftaran.pendaftaranOl.penjamin_id
            _bpjs.jenis_pendaftaran = 'pasien-rs'
        }

        if (instalasi == instalasiMcu) {
            if ($("input[name='TipePasienForm[is_kolektif]']")[2]['checked']) {
                isKolektif = true
                _data.is_kolektif = isKolektif
            }
        }

        if (isMultiPayer) {
            let carabayar = $('#addSelectCarabayar2').val();
            let carabayar_group = $('#addSelectCarabayar2').find(':selected').attr('data-id');
            let add_noAsuransi_1 = _formPendaftaran.firstTmpAsuransi.no_asuransi
                ? _formPendaftaran.firstTmpAsuransi.no_asuransi : $('#multicarabayarform-add_no_asuransi_1').val();
            let add_noAsuransi_2 = _formPendaftaran.secondTmpAsuransi.no_asuransi
                ? _formPendaftaran.secondTmpAsuransi.no_asuransi : $('#multicarabayarform-add_no_asuransi_2').val();

            _formPendaftaran.listTmp.add_carabayar_id_1 = carabayar
            _formPendaftaran.listTmp[firstCbG] = carabayar_group

            if (_formPendaftaran.listTmp.is_add_payer != 'undefined' && _formPendaftaran.listTmp.is_add_payer == true) {
                carabayar = $('#addSelectCarabayar3').val();
                carabayar_group = $('#addSelectCarabayar3').find(':selected').attr('data-id');
                _formPendaftaran.listTmp.add_carabayar_id_2 = carabayar
                _formPendaftaran.listTmp[secondCbG] = carabayar_group
            }

            _formPendaftaran.listTmp.add_penjamin_id_1 = $('#add_penjamin_id_1').val()
            _formPendaftaran.listTmp.add_penjamin_id_2 = $('#add_penjamin_id_2').val()
            // _formPendaftaran.listTmp['firstAsalrujukan_id'] = $('#firstAsalrujukan_id').val()
            // _formPendaftaran.listTmp['secondAsalrujukan_id'] = $('#secondAsalrujukan_id').val()
            _formPendaftaran.listTmp.add_no_asuransi_1 = add_noAsuransi_1
            _formPendaftaran.listTmp.add_no_asuransi_2 = add_noAsuransi_2

            if (!_isBpjs) { // pengecekan carabayar bpjs
                for (const key in _formPendaftaran.listTmp) {
                    if (key == firstCbG && typeof _formPendaftaran.listTmp[key] != 'undefined' && _formPendaftaran.listTmp[key] == docoHelper.groupBPJS) {
                        _isBpjs = true
                    } else if (key == secondCbG && typeof _formPendaftaran.listTmp[key] != 'undefined' && _formPendaftaran.listTmp[key] == docoHelper.groupBPJS) {
                        _isBpjs = true
                    }
                    if (_isBpjs) {
                        _bpjs = {
                            is_bpjs: true,
                            no_kartu: _noKartu,
                            jenis_pencarian: _pencarianBpjs,
                            jenis_kartu: _jenisKartu,
                            jenis_pelayanan: _jenisPelayanan,
                            tanggal_sep: _tglSep,
                            asal_rujukan: _asalRujukanBpjs,
                            no_rujukan_f: _noRujukan,
                            asalrujukan_id: 1
                        };
                    }
                }
            }
            _data.list_tmp = _formPendaftaran.listTmp
        }
        _data.instalasi_workspace = instalasiId;

        var _dataPost = $.extend({}, _data, _bpjs);

        if(!_formPendaftaran.ignoreAsuransi && instalasi != instalasiMcu && $(".selectCarabayar").val() == 2) {
            _formPendaftaran.checkEligiblePatient(_data, _formPendaftaran.tmpAsuransi);
            return false;
        } else {
            _formPendaftaran.ignoreAsurasi = true
        }

        $().docoForm('click', {
            url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-tipe-pasien',
            data: _dataPost,
            skipConfirm: true,
            skipSuccessNotif: true,
            success: function (data) {
                var _findPasien = false;
                var _noRmBpjs = null;
                _formPendaftaran.tipePasien = _data;
                _formPendaftaran.tmpAsuransi.no_asuransi = _data.no_asuransi;
                _formPendaftaran.tmpAsuransi.prevPasien = _formPendaftaran.tmpAsuransi.pasien_id;
                var _noRm = _formPendaftaran.tipePasien.no_rekam_medik;
                var _pasienId;
                
                let showKunjungan = true;
                if (_jenisPendaftaran == 1) {
                    _pasienId = $("#pasien_id_hidden").val();
                } else {
                    _pasienId = "";
                }
                _pasienId = (_pasienId == "") ? null : _pasienId;

                /** Bypass semua validasi MCU Multiple */
                if(!isKolektif){
                    if(isMultiPayer) {
                        generateStep(_groupCaraBayar, _noRm, _asalRujukan, _jenisPendaftaran, _pasienId, _findPasien, _bpjs, data, isKonsul, _pencarianBpjs)

                        for (const property in _formPendaftaran.listTmp) {
                            if (!_isBpjs) { //cek case bpjs
                                if (property == firstCbG && typeof _formPendaftaran.listTmp[property] != 'undefined' && _formPendaftaran.listTmp[property] == docoHelper.groupBPJS) {
                                    _isBpjs = true

                                } else if (property == secondCbG && typeof _formPendaftaran.listTmp[property] != 'undefined' && _formPendaftaran.listTmp[property] == docoHelper.groupBPJS) {
                                    _isBpjs = true
                                }
                            }

                            if (_isBpjs) {
                                if (property == firstCbG && typeof _formPendaftaran.listTmp[property] != 'undefined' && _formPendaftaran.listTmp[property] == docoHelper.groupBPJS) {
                                    generateStepBpjs(_groupCaraBayar, _bpjs, _pencarianBpjs, data, _asalRujukan, _noRm, isKonsul)
                                } else if (property == secondCbG && typeof _formPendaftaran.listTmp[property] != 'undefined' && _formPendaftaran.listTmp[property] == docoHelper.groupBPJS) {
                                    generateStepBpjs(_groupCaraBayar, _bpjs, _pencarianBpjs, data, _asalRujukan, _noRm, isKonsul)
                                }
                            } else {
                                if (property == firstCbG && typeof _formPendaftaran.listTmp[property] != 'undefined' && _formPendaftaran.listTmp[property] == docoHelper.groupJaminan && _jenisPendaftaran != 0) {
                                    _formPendaftaran.formFirstAsuransi(_formPendaftaran.firstTmpAsuransi);

                                } else if (property == secondCbG && typeof _formPendaftaran.listTmp[property] != 'undefined' && _formPendaftaran.listTmp[property] == docoHelper.groupJaminan && _jenisPendaftaran != 0) {
                                    _formPendaftaran.formSecondAsuransi(_formPendaftaran.secondTmpAsuransi);
                                }
                            }
                        }
                        if (!_isBpjs) {
                            generateFormKunjungan()
                        }
                    } else {
                        if (_isBpjs && _jenisPendaftaran == 1) {
                            showKunjungan = false;
                        } else {
                            showKunjungan = true;
                        }

                        if (is_validasipendaftaran == true) {
                            //check kunjungan
                            if (data.response.pasien_bpjs !== null) {
                                if (typeof data.response.pasien_bpjs.peserta != 'undefined') {
                                    _noRm = typeof data.response.pasien_bpjs.peserta.mr.noMR != 'undefined'
                                        ? data?.response?.pasien_bpjs?.peserta.mr?.noMR 
                                        : data?.response?.pasien_bpjs?.peserta?.noMR;
                                }
                            }
                            var getKunjungan = function (_noRm) {
                                new Promise((resolve, reject) => {
                                    $.ajax({
                                        url: '/pendaftaran/daftar/get-kunjungan?no_rekam_medik=' + _noRm,
                                        type: 'GET',
                                        dataType: 'JSON',
                                        success: function (res) {
                                            if (res.results != "") {
                                                var response = res.results;
                                                if (response !== null) {
                                                    if (response.status_periksa == 4 || response.status_periksa == 433 || response.status_ranap == 487) {
                                                        confirmationDialog( 'Pasien '+response.nama_pasien+' Sudah Berkunjung dan Dipulangkan Dari Ruangan ' + response.ruangan_nama + '<br/>Pendaftaran Masih Bisa Tetap Dilanjutkan. Lanjutkan pendaftaran?', (isConfirm) => {
                                                                if (isConfirm) {
                                                                    generateStep(_groupCaraBayar,
                                                                        _isBpjs ? null :_noRm,
                                                                        _asalRujukan,
                                                                        _jenisPendaftaran,
                                                                        _pasienId,
                                                                        _findPasien,
                                                                        _bpjs,
                                                                        data,
                                                                        isKonsul,
                                                                        _pencarianBpjs,
                                                                        showKunjungan)
                                                                }
                                                        });
                                                    } else {
                                                        confirmationDialog( 'Pasien '+response.nama_pasien+' Sudah Terdaftar di Ruangan ' + response.ruangan_nama + '<br/>Pendaftaran Masih Bisa Tetap Dilanjutkan. Lanjutkan pendaftaran?', (isConfirm) => {
                                                            if (isConfirm) {
                                                                if (isConfirm) {
                                                                    generateStep(
                                                                        _groupCaraBayar,
                                                                        _isBpjs ? null :_noRm,
                                                                        _asalRujukan,
                                                                        _jenisPendaftaran,
                                                                        _pasienId,
                                                                        _findPasien,
                                                                        _bpjs,
                                                                        data,
                                                                        isKonsul,
                                                                        _pencarianBpjs,
                                                                        showKunjungan)
                                                                }
                                                            }
                                                        });
                                                    }
                                                }
                                            } else {
                                                generateStep(
                                                    _groupCaraBayar,
                                                    _isBpjs ? null :_noRm,
                                                    _asalRujukan,
                                                    _jenisPendaftaran,
                                                    _pasienId,
                                                    _findPasien,
                                                    _bpjs,
                                                    data,
                                                    isKonsul,
                                                    _pencarianBpjs,
                                                    showKunjungan)
                                            }

                                        },
                                        error: function (err) {
                                            console.log("error cek kunjungan");
                                            console.log(err);
                                        }
                                    });
                                });
                            }

                            getKunjungan(_noRm);

                        } else {
                            generateStep(_groupCaraBayar, _noRm, _asalRujukan, _jenisPendaftaran, _pasienId, _findPasien, _bpjs, data, isKonsul, _pencarianBpjs, showKunjungan)
                        }
                    }

                    // generateFormKunjungan()
                } else {
                    _formPendaftaran.formPreviewMcu()
                }
            },
            error: function (err) {
                console.log(err);
                console.log("error validation tipe pasien");
            }
        });
    },
    getInfoKunjungan: function () {
        let noRekamMedik = $('[name="TipePasienForm[no_rekam_medik]"]').val() != '' ? $('[name="TipePasienForm[no_rekam_medik]"]').val() : $('[name="BpjsNewForm[no_rekam_medik]"]').val();
        $.ajax({
            type: 'GET',
            url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/get-kunjungan-by-no-rm?no_rekam_medik=' + noRekamMedik,
            dataType: 'JSON',
            beforeSend: function () {
                $("#loading-image").show();
                let loader = '<div class="text-center">';
                loader += '<h5><i class="icon-spinner6 spinner position-center"></i>&nbsp;&nbsp;<b>' + i18next.t("memuat") + ' . . . </b></h5>';
                loader += '</div>';
                $('#kunjungan').html(loader);
            },
            success: function (res) {
                var _response = res.response;
                var _html = "";
                var countKunjungan = _response.length;
                if (countKunjungan === 0) {
                    $("#kunjungan").html('<p class="text-center">Pasien Belum Memiliki Riwayat</p>')
                    _formPendaftaran.tmpDataPj = {}
                } else {
                    let pasienRs = false
                    if (_formPendaftaran.params == 'penunjang') {
                        pasienRs = $("input[name='TipePasienForm[is_aps]']")[2]['checked']
                    }
                    $.each(_response, function (key, data) {
                        /** check last kunjungan */
                        if (key === 0) {
                            if (data.penanggungjawab_id !== 'undefined' && data.penanggungjawab_id != null && !pasienRs) {
                                _formPendaftaran.tmpDataPj = {
                                    idPj: data.penanggungjawab_id
                                }
                            } else {
                                _formPendaftaran.tmpDataPj = {}
                            }
                        }
                        var _clone = _formPendaftaran.historyKunjugan;
                        _clone.find('.history-pendaftaran-id').text(data.no_pendaftaran);
                        _clone.find('.history-instalasi').text(': ' + (data.instalasi_nama ? data.instalasi_nama : '-'));
                        _clone.find('.history-ruangan').text(': ' + (data.ruangan_nama ? data.ruangan_nama : '-'));
                        _clone.find('.history-dokter').text(': ' + (data.nama_pegawai ? data.nama_pegawai : '-'));
                        _clone.find('.history-tgl-pendaftaran').text(': ' + (data.tgl_pendaftaran ? convertDateByFormat(data.tgl_pendaftaran, 'd m Y - h:i:s') : '-'));
                        _clone.find('.history-keluar').text(': ' + (data.tglpasienpulang ? convertDateByFormat(data.tglpasienpulang, 'd m Y - h:i') : '-'));
                        _clone.find('.history-cara-keluar').text(': ' + (data.carakeluar_nama ? data.carakeluar_nama : '-'));
                        _html += _clone.html();
                    });
                    if (countKunjungan >= 3) {
                        var _action = $('.btn-detailss').attr('action');
                        $('.btn-detailss').show();
                        _html += '<hr><div align="center"><button type="button" class="btn btn-detailss btn-info btn-labeled btn-xs" action="/pendaftaran/daftar-igd/detail-kunjungan?no_rekam_medik=' + noRekamMedik + '" data-toggle="modal" data-target="#modal_backdrop" data-width="75%"><b><i class="fa fa-eye"></i></b>Detail Kunjungan</button></div>';
                    }
                    $('#kunjungan').html(_html);
                }
            },
            error: function (err) {
                console.log(err);
                $("#kunjungan").html('<p class="text-center text-danger">Terjadi kesalahan pada server</p>')
            }
        });
    },
    findRmBpjs: function () {
        var _target = $('#btn-pencarian-lanjutan');
        var _originAction = _target.attr('action');
        _target.attr('action', `${_originAction}?is_bpjs=MQ`)
        $('#btn-pencarian-lanjutan').trigger('click')
        _target.attr('action', `${_originAction}`)
    },
    generateBpjs: function (_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm) {
        _formPendaftaran.formBpjs(_pencarianBpjs);
        _formPendaftaran.resetAsuransi();
        _pasienId = ""
        _findPasien = false
        if (typeof _formPendaftaran.dataBpjs.mr != 'undefined' && _formPendaftaran.dataBpjs.mr != null) {
            _findPasien = true;
            _noRm = _formPendaftaran.dataBpjs.mr.noMR;
        }

        if (_formPendaftaran.dataReturnBpjs.pasien_baru === true) {
            _formPendaftaran.formPasien();
            generateFormPenanggungJawab(true);
        }

        /** kondisi pasien baru atau pasien lama */
        if ((_formPendaftaran.tipePasien.tipe_pasien && _noRm) || _findPasien) {
            if (Object.keys(_formPendaftaran.pendaftaranOl).length == 0) {
                if (_formPendaftaran.dataReturnBpjs.pasien_baru === false) {
                    _formPendaftaran.getInfoPasien(_pasienId, _noRm);
                }else{
                    setTimeout(function () {
                        if (_formPendaftaran.dataBpjs.nik) {
                            $('#frm-pasien-jenisidentitas').val(94).trigger('change');
                            $('#frm-pasien-no_identitas_pasien').val(_formPendaftaran.dataBpjs.nik).trigger('blur');
                        }else if(_formPendaftaran.dataBpjs.noKartu){
                            $('#frm-pasien-jenisidentitas').val(601).trigger('change');
                            $('#frm-pasien-no_identitas_pasien').val(_formPendaftaran.dataBpjs.noKartu).trigger('blur');
                        }
                        $('#frm-pasien-nama_pasien').val(_formPendaftaran.dataBpjs.nama);
                        $('#frm-pasien-no_telepon_pasien').val(_formPendaftaran.dataBpjs.mr.noTelepon);
                    }, 1000);
                }
            } else if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined') {
                if (_formPendaftaran.dataReturnBpjs.pasien_baru === false) {
                    _formPendaftaran.getInfoPasien(_pasienId, _noRm);
                }
            }
        } else {
            if (Object.keys(_formPendaftaran.pendaftaranOl).length == 0) {
                $('#form-parent-info').hide();
                $('#form-parent').removeClass("col-md-9").addClass("col-md-12");
            }
        }

        if (_asalRujukan != 1 && _groupCaraBayar != docoHelper.groupBPJS) {
            _formPendaftaran.formRujukan();
        }

        if ($('input[name="TipePasienForm[is_multi_payer]"]:checked').val()) {
            if (_formPendaftaran.listTmp[firstCbG] == docoHelper.groupJaminan) {
                _formPendaftaran.formFirstAsuransi(_formPendaftaran.firstTmpAsuransi);
            }

            if (_formPendaftaran.listTmp.is_add_payer != 'undefined' && _formPendaftaran.listTmp.is_add_payer == true) {
                if (_formPendaftaran.listTmp[secondCbG] == docoHelper.groupJaminan) {
                    _formPendaftaran.formSecondAsuransi(_formPendaftaran.secondTmpAsuransi);
                }
            }
        }

        generateFormKunjungan()

    },
    errorBpjs: function (_bpjs) {
        var _clone = _formPendaftaran.formErrorBpjs;
        /** dari BPJS */
        $('.steps-basic').steps("add", {
            title: "BPJS",
            content: _clone.html()
        });
        var _content = $('#form-bpjs-error-content');
        _content.find('span.select2').remove();
        _content.find('.select2').select2();
        _content.find('#error-bpjs-msg').html(_formPendaftaran.dataReturnBpjs.messages);
        $('#no_rekam_medik_bpjs').select2({
            ajax: {
                url: '/pendaftaran/end-point/norm',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term,
                        isRanap: 0,
                        isAps: null
                    };
                }
            },
            placeholder: 'No Rm / Nama pasien / Tanggal lahir',
            minimumInputLength: 3,
            templateResult: function (noRm) {
                return noRm.text;
            },
            templateSelection: function (noRm) {
                return noRm.text;
            }
        });

        $("input[name=chk-statuspasien-bpjs]").change(function () {
            if (this.checked) {
                $('#no_rekam_medik_bpjs').prop("disabled", false);
            } else {
                $('#no_rekam_medik_bpjs').val('').trigger('change');
                $('#no_rekam_medik_bpjs').prop("disabled", true);
            }
        });
        _formPendaftaran.tmpBpjs = _bpjs
    },

    // akan muncul form bayi disini
    formPasien: function (steps) {
        var _clone = _formPendaftaran.formInputPasien;
        if (typeof steps != 'undefined') {
            /** dari BPJS */
            $('.steps-basic').steps("insert", steps, {
                title: "Pasien",
                content: _clone.html()
            });

            if (typeof _formPendaftaran.dataBpjs.mr != 'undefined') {
                setTimeout(function () {
                    if (_formPendaftaran.dataBpjs.nik) {
                        $('#frm-pasien-jenisidentitas').val(94).trigger('change');
                    }
                    $('#frm-pasien-no_identitas_pasien').val(_formPendaftaran.dataBpjs.nik).trigger('blur');
                    $('#frm-pasien-nama_pasien').val(_formPendaftaran.dataBpjs.nama);
                    $('#frm-pasien-no_mobile_pasien').val(_formPendaftaran.dataBpjs.mr.noTelepon);
                    $('#frm-pasien-no_telepon_pasien').val(_formPendaftaran.dataBpjs.mr.noTelepon);
                }, 1000);
            }
        } else {
            $('.steps-basic').steps("add", {
                title: "Pasien",
                content: _clone.html()
            });

            if (typeof _formPendaftaran.tipePasien.no_rekam_medik == 'undefined' || (typeof _formPendaftaran.tipePasien.no_rekam_medik != 'undefined' && _formPendaftaran.tipePasien.no_rekam_medik == '')) {
                $('#frm-pasien-no_telepon_pasien').parent().addClass('required');
                $('[name="PasienForm[is_pasienbaru]"]').val(1).change(); //set payload is pasien baru = true
            }

            if ($("#tipepasienform-is_bbl").is(':checked')) {
                $.ajax({
                    url: '/pendaftaran/end-point/bayi-by-pasien',
                    data: {
                      noRekamMedik: $("#no_rekam_medik").val()
                    },
                    success: (res) => {
                        // Append table bayi
                        $("#form-pasien-content").prepend(`
                            <div class="col-sm-12 table-responsive" style="margin-bottom:12px;">
                            <input type="hidden" name="kelahiran_id" id="kelahiranIdInput">
                            <input type="hidden" name="pendaftaran_ibu_id" id="pendaftaranIbuInput">
                            <h4 class="text-center">Pilih bayi yang akan didaftarkan</h4>
                            <table class="table table-hover table-row-clickable table-striped table-condensed" id="tableBayi">
                                <thead>
                                <tr>
                                    <th>Bayi</th>
                                    <th>Berat Badan (Gram)</th>
                                    <th>Panjang Badan (Cm)</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Kondisi Bayi</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            </div>
                        `)
                        if (res.data.length > 0) {
                            res.data.map((eachBayi) => {
                            $("#tableBayi tbody").append(`
                                <tr data-id="${eachBayi.kelahiranbayi_id}" data-registered="${eachBayi.is_registered ? '1' : '0'}" ${eachBayi.is_registered ? `class="row-registered" data-toggle="tooltip" data-placement="top" title="Bayi sudah didaftarkan dengan no pendaftaran ${eachBayi.no_pendaftaranbayi}"` : ''}>
                                <td>${eachBayi.nomor_bayi}</td>
                                <td>${eachBayi.berat_badan}</td>
                                <td>${eachBayi.tinggi_badan}</td>
                                <td>${eachBayi.jenis_kelamin === 15 ? 'Laki-Laki' : 'Perempuan'}</td>
                                <td>${eachBayi.kondisi_bayi}</td>
                                </tr>
                            `)
                            Object.assign(dataBayi, {
                                [eachBayi.kelahiranbayi_id]: eachBayi
                            })
                            })
                        } else {
                            dataBayi = {}
                        }
                        //end data table pilih bayi

                        $("#form-pasien-content").children('.col-md-6').hide();
                        $(".div-pasien").hide();
                        $("#tableBayi tbody tr").bind('click', ({ delegateTarget }) => {
                            // parsing data bayi to form
                            if ($(delegateTarget).data('registered') === 0) {
                                parseDataBayiToForm($(delegateTarget).data('id'))
                            }
                        })

                        $('#frm-pasien-tanggal_lahir').trigger('change')
                        $('#frm-pasien-namadepan').val(206).trigger('change')
                        $('#frm-pasien-jenisidentitas').val(9999)
                        $('#frm-pasien-no_identitas_pasien').val(9999)
                        $('.field-frm-pasien-no_identitas_pasien').hide()
                        $('.field-frm-pasien-jenisidentitas').hide()
                        $('.field-frm-pasien-namadepan').hide()
                        $('.tambah-jenis').hide()
                        $('.field-frm-pasien-statusperkawinan').hide()
                        $('.field-frm-pasien-warga_negara').hide()
                        $('.field-frm-pasien-suku_id').hide()
                        $('.field-frm-pasien-pekerjaan_id').hide()
                        $('.field-frm-pasien-pendidikan_id').hide()
                    }
                })
                generateFormPenanggungJawab();
            }
        }

        var dataAsuransi = localStorage.getItem("eligiblePesertaAsuransi");

        if(typeof dataAsuransi !== 'undefined' && dataAsuransi !== null) {    
            dataAsuransi = JSON.parse(dataAsuransi)
            if(dataAsuransi.dataPeserta !== undefined) {
                var birthdayDate = new Date(dataAsuransi.dataPeserta.tanggallahir);

                // Mengambil hari, bulan, dan tahun
                var day = birthdayDate.getDate();
                var month = birthdayDate.getMonth() + 1;
                var year = birthdayDate.getFullYear();

                var formattedDate = `${day.toString().padStart(2, '0')}-${month.toString().padStart(2, '0')}-${year}`;

                $('#frm-pasien-nama_pasien').val(dataAsuransi.dataPeserta.namapeserta);
                $('#frm-pasien-tanggal_lahir').val(formattedDate).trigger('change');
                $('#frm-pasien-no_telepon_pasien').val(dataAsuransi.dataPeserta.phone);
                $('#frm-pasien-alamatemail').val(dataAsuransi.dataPeserta.email);

                if (dataAsuransi.dataPeserta.jeniskelamin.toLowerCase() == 'm') {
                    $("input[name='PasienForm[jeniskelamin]'][value='15']").prop('checked', true);
                } else {
                    $("input[name='PasienForm[jeniskelamin]'][value='16']").prop('checked', true);
                }
            }
        }

        /** Event after generate form */
        var _content = $('#form-pasien-content');
        _content.find('span.select2').remove();
        _content.find('.select2').select2();
        _content.find('input').attr("autocomplete", "off");
        /** binding region */
        bindingRegionPdftrn('/pendaftaran/end-point/region-list', {
            province: $("#frm-pasien-propinsi_id"),
            city: $("#kabupatenForm"),
            district: $("#frm-pasien-kecamatan_id"),
            village: $("#frm-pasien-kelurahan_id"),
        })

        // setRegionData('/pendaftaran/end-point/region-list', {
        //     province: {
        //       element: $("#frm-pasien-propinsi_id"),
        //       value: propinsi_id
        //     },
        //     city: {
        //       element: $("#kabupatenForm"),
        //       value: kabupaten_id
        //     },
        //     district: {
        //       element: $("#frm-pasien-kecamatan_id"),
        //       value: ''
        //     },
        //     village: {
        //       element: $("#frm-pasien-kelurahan_id"),
        //       value: ''
        //     },
        // })

        var $input_date = $('#frm-pasien-tanggal_lahir').pickadate({
            editable: true,
            format: 'dd-mm-yyyy',
            formatSubmit: 'dd-mm-yyyy',
            selectMonths: true,
            selectYears: true,
            min: [1900, 01, 01],
            max: true,
            onClose: function () {
                $('.datepicker').focus();
            }
        });
        var picker_date = $input_date.pickadate('picker');
        $('#frm-pasien-tanggal_lahir').parent().children('.input-group-addon').on('click', function (event) {
            if (picker_date.get('open')) {
                picker_date.close();
            } else {
                picker_date.open();
            }
            event.stopPropagation();
        });

        // -- pasien baru
        $(document).on('change', '#frm-pasien-tanggal_lahir', function () {
            var umur = '';
            if ($(this).val() != '') {
                const splitDate = $(this).val().split('-')

                var myDate = new Date(splitDate[2], splitDate[1] - 1, splitDate[0]);
                var today = new Date();
                if (myDate > today) {
                    $(this).pickadate('picker').set('select', new Date())
                    return true
                }
                umur = generateUmur($(this).val());
            }

            $('#frm-pasien-umur').val(umur);
            $("input[name='PasienForm[tanggal_lahir]_submit']").val($('#frm-pasien-tanggal_lahir').val());
        });
        $('#frm-pasien-no_identitas_pasien').on('blur change', function () {
            if ($(this).val().length < 16) {
                return false;
            }
            function padZero(num) {
                str = num;
                return str < 10 ? "0" + num : num;
            }
            var kodeProv = $(this).val().substring(0, 2);
            var kodeKab = $(this).val().substring(2, 4);
            var kodeKec = $(this).val().substring(4, 6);

            var jk = 15;
            /** Bulan */
            if (parseInt($(this).val().substring(8, 10)) > 12 || parseInt($(this).val().substring(6, 8)) > 31) {
                jk = 16;
            }
            var day_date_s = parseInt($(this).val().substring(6, 8)) > 31
                ? parseInt($(this).val().substring(6, 8)) - 40 : parseInt($(this).val().substring(6, 8));
            var day_date = padZero(day_date_s);
            var month_date = parseInt($(this).val().substring(8, 10)) > 12
                ? parseInt($(this).val().substring(8, 10)) - 40 : parseInt($(this).val().substring(8, 10));
            month_date = padZero(month_date);
            // var jk = parseInt($(this).val().substring(6, 8)) - 40 >= 0 ? 16 : 15;
            // var day_date_s = (jk == 16 ? $(this).val().substring(6, 8) - 40 : parseInt($(this).val().substring(6, 8)));
            var year_date_s = $(this).val().substring(10, 12);
            var year_date = year_date_s > 24 ? '19' + year_date_s : '20' + year_date_s;
            var tglLahir = day_date + '-' + month_date + '-' + year_date;
            var _kabupaten = $('#kabupatenForm')
            var _kecamatan = $('#frm-pasien-kecamatan_id')
            _kabupaten.attr('data-code', '')
            _kecamatan.attr('data-code', '')
            if ($('#frm-pasien-jenisidentitas').val() == '94') {
                // set propinsi
                _kabupaten.attr('data-code', kodeKab)
                _kecamatan.attr('data-code', kodeKec)
                $('#frm-pasien-propinsi_id option').each(function (id, el) {
                    if (String($(el).data('kode')) == kodeProv) {
                        // $(el).attr('selected', true);
                        $('#frm-pasien-propinsi_id').val($(el).attr('value')).trigger('change').trigger('depdrop:change');
                    }
                })
                // set jenis kelamin
                $("input[name='PasienForm[jeniskelamin]']").closest('span').removeClass('checked')
                $("input[name='PasienForm[jeniskelamin]']").closest('div').removeClass('checked')
                $("input[name='PasienForm[jeniskelamin]'][value=" + jk + "]").prop('checked', true).trigger('change');
                $("input[name='PasienForm[jeniskelamin]'][value=" + jk + "]").closest('span').addClass('checked')
                $("input[name='PasienForm[jeniskelamin]'][value=" + jk + "]").closest('div').addClass('checked')
                $('#frm-pasien-tanggal_lahir').val(tglLahir).trigger('change');
                bindingRegionPdftrn('/pendaftaran/end-point/region-list', {
                    province: $("#frm-pasien-propinsi_id"),
                    city: $("#kabupatenForm"),
                    district: $("#frm-pasien-kecamatan_id"),
                    village: $("#frm-pasien-kelurahan_id"),
                })
            }

        });
        $("input[name='PasienForm[jeniskelamin]']").on('change', function () {
            if ($('#frm-pasien-namadepan').val().length < 1) {
                var jk = $("input[name='PasienForm[jeniskelamin]']:checked").val();
                if (jk == 15) {
                    $('#frm-pasien-namadepan').val(201).trigger('change');
                } else if (jk == 16) {
                    $('#frm-pasien-namadepan').val(202).trigger('change');
                } else {
                    $('#frm-pasien-namadepan').val('').trigger('change');
                }
            }
        });

        $('#form-parent-info').hide();
        $('#form-parent').removeClass("col-md-9").addClass("col-md-12");
    },
    formRujukan: function () {
        var _clone = _formPendaftaran.formInputRujukan;
        _clone.find('input').attr("autocomplete", "off");
        $('.steps-basic').steps("add", {
            title: "Rujukan",
            content: _clone.html()
        });
        let dataRujukanDari = []
        if (typeof _rujukanDari[_formPendaftaran.tipePasien.asalrujukan_id] != "undefined") {
            _rujukanDari[_formPendaftaran.tipePasien.asalrujukan_id].map((item) => {
                dataRujukanDari.push({
                    id: item.id,
                    text: item.value
                })
            })
        }

        $('#rujukanform-rujukandari_id').select2({
          data: dataRujukanDari,
        });

        // if (dataRujukanDari.length <= 1 && dataRujukanDari.length >= 1) {
        //   $('#rujukanform-rujukandari_id').val(1).trigger('change');
        // }

        // refreshOptionSelect2($('#rujukanform-rujukandari_id'), dataRujukanDari)
        $("#rujukanform-diagnosa_id").select2({
            placeholder: "Pilih Diagnosa",
            minimumInputLength: 3,
            ajax: {
                url: "/pendaftaran/daftar-igd/get-diagnosa?type=10",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                    var query = {
                        search: params.term
                    }

                    return query;
                },
            },
        });
        renderPickadate($('input[name="RujukanForm[tanggal_rujukan]"]'), {
            dependElementPicker: $('.btn_addon_tglrujuk').parent(),
            defaultValue: new Date()
        })
    },
    formAsuransi: function () {
        var _clone = _formPendaftaran.formInputAsuransi;
        $('.steps-basic').steps("add", {
            title: "Asuransi",
            content: _clone.html()
        });
        /** Re init */
        var _content = $('#form-asuransi-content');
        renderPickadate($(_content.find('#tgl_konfirmasi')), {
            dependElementPicker: _content.find('#btn_addon_tgllahir').parent(),
            defaultValue: typeof _formPendaftaran.tmpAsuransi.tgl_konfirmasi !== 'undefined' && _formPendaftaran.tmpAsuransi.tgl_konfirmasi !== null ? _formPendaftaran.tmpAsuransi.tgl_konfirmasi : new Date()
        });

        $('#asuransiform-kelastanggungan_id').select2();
        $('#asuransiform-kelastanggungan_id').on('select2:close', ({ delegateTarget }) => {
            $(delegateTarget).focus()
        })
        _content.find('input').attr("autocomplete", "off");
        _content.find('#asuransiform-namapemilikasuransi').val(_formPendaftaran.tmpAsuransi.namapemilikasuransi);
        _content.find('#asuransiform-nomorpokokperusahaan').val(_formPendaftaran.tmpAsuransi.nomorpokokperusahaan);
        _content.find('#asuransiform-kelastanggungan_id')
            .val(_formPendaftaran.tmpAsuransi.kelastanggunganasuransi_id)
            .trigger('change');
        _content.find('#asuransiform-namaperusahaan')
            .val(_formPendaftaran.tmpAsuransi.namaperusahaan);
        $('.field-asuransiform-kelastanggungan_id').attr("style", "display: none")
        $("#asuransiform-penjamingrade_id").select2({
            placeholder: "-",
            ajax: {
                url: "/pendaftaran/end-point/get-grade-penjamin",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                    let query = {
                        search: params.term,
                        penjamin_id: _formPendaftaran.tipePasien.penjamin_id,
                    }

                    return query;
                },
            },
        });

        console.log(_formPendaftaran.tmpAsuransi, "DISINI")
        $("#asuransiform-benefit_id").select2({
            placeholder: "-",
            ajax: {
                url: "/pendaftaran/daftar/get-referensi-benefit",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                    let query = {
                        search: params.term,
                        penjamin_id: _formPendaftaran.tipePasien.penjamin_id,
                        no_asuransi: _formPendaftaran.tmpAsuransi.no_asuransi
                    }

                    return query;
                },
                processResults: function (data) {
                    $.each(data.results, function (index, value) {
                        if (_formPendaftaran.pendaftaranOl.pendaftaranol_id != null) {
                            var benefitCode = _formPendaftaran.pendaftaranOl.all?.benefit_code

                            /**
                             * Setting benefit code on open
                             */
                            if (benefitCode != undefined && benefitCode != null) {
                                if (benefitCode == value.id) {
                                    var option = new Option(value.text, value.id, true, true);
                                    $('#asuransiform-benefit_id').append(option).trigger('change');
                                } 
                            }
                        }
                    })

                    return {
                        results: data.results
                    };
                }
            },
            cache: true,
        }).on('select2:select', function (e) {
            _formPendaftaran.referensiAsuransi = e.params.data?.id            
        });

        if(isIntegrasi) {
            $('.field-asuransiform-benefit_id').attr("style", "display: block")
        }
        else {
            $('.field-asuransiform-benefit_id').attr("style", "display: none")
        }

        if (_formPendaftaran.pendaftaranOl.pendaftaranol_id) {
            $("#asuransiform-benefit_id").select2('open')

            setTimeout(() => {
                $("#asuransiform-benefit_id").select2('close')
            }, 500);
        }
    },
    formFirstAsuransi: function (data) { //multi payer
        var _clone = _formPendaftaran.formFirstInputAsuransi;
        $('.steps-basic').steps("add", {
            title: "Asuransi",
            content: _clone.html()
        });
        /** Re init */
        var _content = $('#form-first-asuransi-content');
        renderPickadate($(_content.find('#add_tgl_konfirmasi_1')), {
            dependElementPicker: _content.find('#btn_addon_add_tgl_konfirmasi_1').parent(),
            defaultValue: typeof data.tgl_konfirmasi !== 'undefined' && data.tgl_konfirmasi !== null ? data.tgl_konfirmasi : new Date()
        });

        $('#multicarabayarform-add_kelastanggungan_id_1').select2();
        $('#multicarabayarform-add_kelastanggungan_id_1').on('select2:close', ({ delegateTarget }) => {
            $(delegateTarget).focus()
        })
        if (instalasiId != '10') { // bukan instalasi pendaftaran atau bukan dococonstant ws igd
            $('.field-multicarabayarform-add_namapemilikasuransi_1').addClass('required');
        }
        _content.find('input').attr("autocomplete", "off");
        _content.find('#multicarabayarform-add_namapemilikasuransi_1').val(data.namapemilikasuransi);
        _content.find('#multicarabayarform-add_nomorpokokperusahaan_1').val(data.nomorpokokperusahaan);
        _content.find('#multicarabayarform-add_kelastanggungan_id_1')
            .val(data.kelastanggunganasuransi_id)
            .trigger('change');
        _content.find('#multicarabayarform-add_namaperusahaan_1')
            .val(data.namaperusahaan);
        _content.find('#multicarabayarform-add_asuransipasien_id_1')
            .val(data.asuransipasien_id);
        $('.field-multicarabayarform-add_kelastanggungan_id_1').attr("style", "display: none")
        $("#multicarabayarform-add_penjamingrade_id_1").select2({
            placeholder: "-",
            ajax: {
                url: "/pendaftaran/end-point/get-grade-penjamin",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                    let query = {
                        search: params.term,
                        penjamin_id: $('#add_penjamin_id_1').val()
                    }

                    return query;
                },
            },
        });
    },
    formSecondAsuransi: function (data) { //multi payer
        var _clone = _formPendaftaran.formSecondInputAsuransi;
        $('.steps-basic').steps("add", {
            title: "Asuransi",
            content: _clone.html()
        });
        /** Re init */
        var _content = $('#form-second-asuransi-content');
        renderPickadate($(_content.find('#add_tgl_konfirmasi_2')), {
            dependElementPicker: _content.find('#btn_addon_add_tgl_konfirmasi_2').parent(),
            defaultValue: typeof data.tgl_konfirmasi !== 'undefined' && data.tgl_konfirmasi !== null ? data.tgl_konfirmasi : new Date()
        });

        $('#multicarabayarform-add_kelastanggungan_id_2').select2();
        $('#multicarabayarform-add_kelastanggungan_id_2').on('select2:close', ({ delegateTarget }) => {
            $(delegateTarget).focus()
        })
        if (instalasiId != '10') { // bukan instalasi pendaftaran atau bukan dococonstant ws igd
            $('.field-multicarabayarform-add_namapemilikasuransi_2').addClass('required');
        }
        _content.find('input').attr("autocomplete", "off");
        _content.find('#multicarabayarform-add_namapemilikasuransi_2').val(data.namapemilikasuransi);
        _content.find('#multicarabayarform-add_nomorpokokperusahaan_2').val(data.nomorpokokperusahaan);
        _content.find('#multicarabayarform-add_kelastanggungan_id_2')
            .val(data.kelastanggunganasuransi_id)
            .trigger('change');
        _content.find('#multicarabayarform-add_namaperusahaan_2')
            .val(data.namaperusahaan);
        _content.find('#multicarabayarform-add_asuransipasien_id_2')
            .val(data.asuransipasien_id);
        $('.field-multicarabayarform-add_kelastanggungan_id_2').attr("style", "display: none")
        $("#multicarabayarform-add_penjamingrade_id_2").select2({
            placeholder: "-",
            ajax: {
                url: "/pendaftaran/end-point/get-grade-penjamin",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                    let query = {
                        search: params.term,
                        penjamin_id: $('#add_penjamin_id_2').val()
                    }

                    return query;
                },
            },
        });
    },
    formPreviewMcu: function () {
        var tableMcuPreview;
        var _clone = _formPendaftaran.formPasienMcu;

        if (typeof dataPasienMcu !== 'undefined' && dataPasienMcu.length != 0) {
            $('.steps-basic').steps("add", {
                title: "Preview Pasien MCU",
                content: _clone.html()
            });

            var _groupCaraBayar = $('.selectCarabayar').find(':selected').attr('data-id');
            $("#info-lengkap").empty().html(_formPendaftaran.tmpDataMcu.dataComplete + " Data Lengkap");
            $("#info-tidak-lengkap").empty().html(_formPendaftaran.tmpDataMcu.dataIncomplete + " Data Tidak Lengkap");
            $("#info-file").empty().html("Sumber : " + _formPendaftaran.tmpDataMcu.dataFile);
            $("#info-double-rm").empty().html(_formPendaftaran.tmpDataMcu.dataDoubleRm + " No Rekam Medik Salah / Duplikasi");
            $("#info-multiple-rm").empty().html(_formPendaftaran.tmpDataMcu.dataMultipleRm + " Data Ditemukan > 1 No Rekam Medik");

            tableMcuPreview = $('#tbl-pasien-mcu').DataTable({
                data: dataPasienMcu,
                scrollX: true,
                searching: false,
                order: [[0, "desc"]],
                columns: [
                    {
                        title: "Group",
                        data: "group",
                        orderable: true,
                        searching: false,
                        visible: false
                    },
                    {
                        title: "No",
                        data: "no",
                        orderable: false,
                        searching: false,
                    },
                    {
                        title: "No Asuransi",
                        data: "nokartuasuransi",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "Nama Pemilik Asuransi",
                        data: "namapemilikasuransi",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "Jenis Identitas",
                        data: "jenisidentitas",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "No Identitas",
                        data: "no_identitas_pasien",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "No Rekam Medik",
                        data: "no_rekam_medik",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "Nama Pasien",
                        data: "nama_pasien",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "Tempat Lahir",
                        data: "tempat_lahir",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "Tanggal Lahir",
                        data: "tanggal_lahir",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "Jenis Kelamin",
                        data: "jenis_kelamin",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "Golongan Darah",
                        data: "golongandarah",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "Status Perkawinan",
                        data: "statusperkawinan",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "Alamat",
                        data: "alamat_pasien",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "No Telepon",
                        data: "no_telepon_pasien",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "No Rujukan",
                        data: "no_rujukan",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "Rujukan Dari",
                        data: "asalrujukan",
                        orderable: false,
                        searching: false
                    },
                    {
                        title: "Nama Perujuk",
                        data: "nama_perujuk",
                        orderable: false,
                        searching: false
                    },
                ],
                "initComplete": function (settings, json) {
                    // $("#tbl-pasien-mcu").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
                    $(".dataTables_scrollHeadInner").css("width", "100%");
                    $(".list-mcu-pasien").css("width", "100%");
                },
                "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    if (aData.status == 'incomplete') {
                        $(nRow).css("background", "#FF9999");
                    } else if (aData.status == 'duplikasi') {
                        $(nRow).css("background", "#fde4a8");
                    } else if (aData.multiple_rm == true) {
                        $(nRow).css("background", "#66cfff");
                    } else {
                        $(nRow).css("background-color", "#99FFCC");
                    }
                }
            })

            tableMcuPreview.on('order.dt search.dt', function () {
                tableMcuPreview.column(1, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
                if (_groupCaraBayar != docoHelper.groupJaminan) {
                    $('#tbl-pasien-mcu').DataTable().columns([2, 3]).visible(false);
                }
            }).draw();

            // if ($.fn.DataTable.isDataTable( '#tbl-pasien-mcu' ) ) {
            //     $('#tbl-pasien-mcu').DataTable().draw('page');
            //     $.fn.dataTable.tableMcuPreview.columns.adjust();
            //   }

            $('#tbl-pasien-mcu').on('length.dt', function () {
                setTimeout(function () {
                    //draw('page') redraws your DataTable and preserves the page where it was
                    $('#tbl-pasien-mcu').DataTable().draw('page');
                }, 100);
            });

            $('#tbl-pasien-mcu').DataTable().columns.adjust().draw();
            // $('#tbl-pasien-mcu').DataTable().page('last').draw('page');
            generateFormKunjungan()
            // $('#tbl-pasien-mcu').DataTable().page.len(50).draw();

        } else {
            docoNotification('error', 'Perhatian!', 'Data Pasien Belum Di Unggah!')
            window.stop()
            return false;
        }
    },
    validateAsuransi: function (object, _isMultiPayer = false) {
        var _data = object.serializeArray();
        _data.push({name:'instalasi_workspace', value: instalasiId});
        _data.push({name:'isIntegrasi', value: isIntegrasi});
        var _result = false;
        $().docoForm('click', {
            url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-asuransi?isMultiPayer=' + _isMultiPayer,
            data: _data,
            skipConfirm: true,
            skipSuccessNotif: true,
            async: false,
            success: function (data) {
                _result = true;
            }
        });
        return _result;
    },
    validateBpjs: function (object) {
        var poli_tujuan = $('#poli_tujuan').val();
        let asalRujukan = $("input[name='BpjsNewForm[jenis_rujukan]']:checked").val();
        _formPendaftaran.tmpBpjs.poliTujuan = poli_tujuan

        // Skip confirmation tujuan kunjungan
        if (_formPendaftaran.params == 'igd') {
            $('#is_tujuan_kunj').val(tujuanKunjTrue);
        } else if (asalRujukan == 1 && !_formPendaftaran.dataReturnBpjs.lastPoli) {  // dari klinik rujukan baru
            $('#is_tujuan_kunj').val(tujuanKunjTrue);
        }

        var _data = object.serializeArray();
        if (typeof _formPendaftaran.dataReturnBpjs.rujukan !== 'undefined' && _formPendaftaran.dataReturnBpjs.rujukan !== false) {
            _data.push({
                name: 'BpjsNewForm[asal_rujukan]',
                value: _formPendaftaran.dataReturnBpjs.asalFaskes != undefined ? _formPendaftaran.dataReturnBpjs.asalFaskes : $('#asal_rujukan_1').val()
            });
            _data.push({
                name: 'BpjsNewForm[ppk_rujukan]',
                value: _formPendaftaran.dataReturnBpjs.rujukan.provPerujuk.kode
            });

            if (poli_tujuan != _formPendaftaran.dataReturnBpjs.rujukan.poliRujukan.kode) {
                _data.push({
                    name: 'is_beda_poli_rujukan',
                    value: 1
                });
            }
        } else if (typeof _formPendaftaran.dataReturnBpjs.post_ranap !== 'undefined' && _formPendaftaran.dataReturnBpjs.post_ranap !== false) {
            _data.push({
                name: 'asal_rujukan_hidden',
                value: _formPendaftaran.dataPostRanap.asal_rujukan
            });
            _data.push({
                name: 'ppk_rujukan_hidden',
                value: _formPendaftaran.dataPostRanap.ppkrujukan_kode
            });
        } else {
            _data.push({
                name: 'BpjsNewForm[asal_rujukan]',
                value: $('#asal_rujukan_1').val()
            });
        }

        _data.push({
            name: 'post_ranap',
            value: _formPendaftaran.dataReturnBpjs.post_ranap ? 1 : 0
        });
        _data.push({
            name: 'no_kartu',
            value: _formPendaftaran.dataBpjs.noKartu
        });
        var _result = false;
        // console.table(_data)
        $().docoForm('click', {
            url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-bpjs',
            data: _data,
            skipConfirm: true,
            skipSuccessNotif: true,
            async: false,
            success: function (data) {
                var kunjungan = data.response.data_kunjungan;
                var lastPoli;
                var tanggal_sep = data.response.tgl_sep;
                var is_tujuan_kunj = data.response.is_tujuan_kunj;
                var poli_tujuan = $('#poli_tujuan').val();
                var url = '';
                var rbpjs = data.response.ruangan_bpjs;

                _ruanganBpjs = rbpjs;

                if (is_tujuan_kunj == tujuanKunjTrue || kunjungan.length === 0 ) {
                    _result = true;
                    // Reset is tujuan kunjungan
                    $('#is_tujuan_kunj').val(tujuanKunReset);
                } else {
                    lastPoli = data.response.data_rujukan.lastPoli;
                    poliAsalRujuk = data.response.data_rujukan.rujukan.poliRujukan ? data.response.data_rujukan.rujukan.poliRujukan.kode : null;

                    if (tanggal_sep == kunjungan[0].tglSep) {
                        if (poli_tujuan == poliAsalRujuk) {
                            $("#tujuan_kunjungan").val(tujuanKunNormal);
                            url = '/pendaftaran/daftar-rajal/tujuan-prosedur-bpjs';
                            modalTujuanKunjunganBpjs(url);
                        } else {
                            $("#tujuan_kunjungan").val(tujuanKunNormal);
                            _result = true;
                        }
                    } else {
                        if (poli_tujuan == poliAsalRujuk) {
                            url = '/pendaftaran/daftar-rajal/tujuan-kunjungan-bpjs';
                        } else {
                            $("#tujuan_kunjungan").val(tujuanKunNormal);
                            url = '/pendaftaran/daftar-rajal/assesment-pelayanan-bpjs?bedaPoli=true';
                            // if (poli_tujuan == _formPendaftaran.dataReturnBpjs.rujukan.poliRujukan.kode) {
                            //     $("#tujuan_kunjungan").val(tujuanKunNormal);
                            //     url = '/pendaftaran/daftar-rajal/assesment-pelayanan-bpjs';
                            // }
                            // else {
                            //     $("#tujuan_kunjungan").val(tujuanKunNormal);
                            //     url = '/pendaftaran/daftar-rajal/assesment-pelayanan-bpjs';
                            // }
                        }
                        modalTujuanKunjunganBpjs(url);
                    }
                    if(lastPoli) {
                        docoNotification("warning", "Peringatan!", "Anda Sudah melakukan kunjungan konsultasi dokter pada poli " + lastPoli + " pada tanggal " + kunjungan[0].tglSep);
                    }
                }
            }
        });
        _formPendaftaran.defaultKunjunganBpjs();
        return _result;
    },
    validateMultiCarabayar: function (steps) {
        var _clonePj = _formPendaftaran.formInputPj;
        // if (typeof steps != 'undefined') {
        //     $('.steps-basic').steps("insert", steps, {
        //         title: "Penanggung Jawab",
        //         content: _clonePj.html()
        //     });
        //     /** Re init */
        //     var _content = $('#form-pj-content');
        //     $("#pjpasienform-pj_jenis_identitas,#pjpasienform-pj_hubungan,#pjpasienform-pj_pengantar").select2()
        //     var $input_date = $('#pjpasienform-pj_tanggal_lahir').pickadate({
        //         editable: true,
        //         format: 'dd-mm-yyyy',
        //         formatSubmit: 'dd-mm-yyyy',
        //         selectMonths: true,
        //         selectYears: true,
        //         min: [1900, 01, 01],
        //         max: true,
        //         onClose: function () {
        //             $('.datepicker').focus();
        //         }
        //     });
        //     var picker_date = $input_date.pickadate('picker');
        //     $('#pj-date').parent().on('click', function (event) {
        //         if (picker_date.get('open')) {
        //             picker_date.close();
        //         } else {
        //             picker_date.open();
        //         }
        //         event.stopPropagation();
        //     });

        //     $('#pjpasienform-pj_tanggal_lahir').on('change', function () {
        //         var umur = '';
        //         if ($(this).val() != '') {
        //             const splitDate = $(this).val().split('-')
        //             const date = `${splitDate[2]}-${splitDate[1]}-${splitDate[0]}`

        //             var myDate = new Date(date);
        //             var today = new Date();
        //             if (myDate > today) {
        //                 $(this).pickadate('picker').set('select', new Date())
        //                 return true
        //             }
        //             umur = generateUmur($(this).val());
        //         }
        //         $('.umurtext').val(umur);
        //         $("input[name='PjpasienForm[pj_tanggal_lahir]_submit']").val($('#pjpasienform-pj_tanggal_lahir').val());
        //     })
        //     $('#form-pj-content').find('.styled, .multiselect-container input').uniform({
        //         radioClass: 'choice'
        //     });
        //     $('select').removeAttr('tabindex');
        //     var _parent = $('fieldset[aria-hidden="false"]');
        // } else {
        //     $('.steps-basic').steps("add", {
        //         title: "Penanggung Jawab",
        //         content: _clonePj.html()
        //     });
        // }
    },
    formBpjs: function (jenisRujukan) {
        // mengambil data dari form tipe pasien
        var _inputBpjs = _formPendaftaran.tmpBpjs

        // mengambil jenis ruangan
        var getRoom = _formPendaftaran.params
        var _clone = _formPendaftaran.formInputBpjs;
        var peserta = _formPendaftaran.dataBpjs; // data peserta bpjs
        var dataPostRanap = _formPendaftaran.dataPostRanap;
        var post_ranap = _formPendaftaran.dataReturnBpjs.post_ranap;
        var asal_rujukan = _inputBpjs.asal_rujukan;
        var lastRujukan = _formPendaftaran.dataReturnBpjs.lastRujukan
        var rencanaKontrol = _formPendaftaran.dataReturnBpjs?.rencana_kontrol;
        if (post_ranap) {
            if (dataPostRanap && Object.keys(dataPostRanap).length) {
                asal_rujukan = dataPostRanap.asal_rujukan
            } else {
                if(post_ranap.asal_rujukan) {
                    asal_rujukan = post_ranap.asal_rujukan
                }
            }
        }

        var no_rujukan = (post_ranap) ? dataPostRanap.no_rujukan : null;
        var ppkrujukan_kode = (post_ranap) ? dataPostRanap.ppkrujukan_kode : null;
        var ppkrujukan_nama = (post_ranap) ? dataPostRanap.ppkrujukan_nama : null;
        var rujukan = {};
        _formPendaftaran.first_search = null;

        if (jenisRujukan == JenisPencarianRujukan) {
            rujukan = _formPendaftaran.dataReturnBpjs.rujukan;
            no_rujukan = _formPendaftaran.tmpBpjs.no_rujukan_f; //case tgl 28 sep 2021 (norujukan kosong dan disabled)
        }
        if (Object.keys(_formPendaftaran.dataBpjs).length) {
            $('#ket-bpjs').show();
            $('.steps-basic').steps("add", {
                title: "Bpjs",
                content: _clone.html()
            });
            var _content = $('#form-bpjs-content');

            _content.find('.select2Bpjs').select2();
            // poli tujuan otomatis terisi sesuai kondisi = IGD
            if (_inputBpjs.jenis_pencarian === '2' && getRoom === 'igd') {
                refreshOptionSelect2($('#poli_tujuan'), [{ "id": "IGD", "text": "INSTALASI GAWAT DARURAT" }])
                $('#asal_rujukan').val(2)
                const ppkPelayanan = _formPendaftaran.dataReturnBpjs.ppkPelayanan;
                setTimeout(() => {
                    $("#ppk_rujukan").append("<option value='"+(ppkPelayanan.kode ?? '')+"' selected>"+(ppkPelayanan.nama ?? '')+"</option>");
                    $('#ppk_rujukan').trigger('change');
                }, [1])
            }
            _content.find('input').attr("autocomplete", "off");
            $(".select2AsalRujukan").select2();
            $(".select2KasusKecelakaan").select2();
            $("#asal_rujukan").on("change", function () {
                $("#ppk_rujukan").val(null).trigger("change");
            });
            $("#ppk_rujukan").select2({
                placeholder: "PPK Rujukan",
                minimumInputLength: 3,
                ajax: {
                    url: "/api/bpjs/referensi-faskes-new",
                    dataType: "json",
                    quietMillis: 250,
                    data: function (params) {
                        var query = {
                            search: params.term,
                            asal_rujukan: $('#asal_rujukan').val(),
                            type: 'public'
                        }

                        return query;
                    },
                },
                templateSelection: function (res) {
                    _formPendaftaran.tmpBpjs.ppkPerujukId = res.id
                    _formPendaftaran.tmpBpjs.ppkPerujukNama = res.text
                    return res.text;
                }
            });
            $("#ppk_rujukan").on('select2:close', ({ delegateTarget }) => {
                $(delegateTarget).focus()
            })

            $("#diagnosa_awal").select2({
                placeholder: "Pilih Diagnosa Awal",
                minimumInputLength: 3,
                ajax: {
                    url: "/api/bpjs/referensi-diagnosa-new",
                    dataType: "json",
                    quietMillis: 250,
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }

                        return query;
                    },
                },
            });

            // let url = window.location.origin + '/api/bpjs/referensi-dokter?jnsPelayanan=' + jnsPelayanan + '&tglSep=' + tglSep + '&poliTujuan=' + poliTujuan;
            $(".select2Dpjp").select2({
                placeholder: "Pilih Dokter DPJP",
                ajax: {
                    url: "/api/bpjs/referensi-dpjp",
                    dataType: "json",
                    quietMillis: 250,
                    data: function (params) {
                        let _poli_tujuan = $('#poli_tujuan').val();
                        let _jenis_pelayanan = $('#jenis_pelayanan').val();
                        let _tlg_sep = $('#tanggal_sep').val();
                        var query = {
                            search: params.term,
                            type: 'public',
                            pelayanan: _jenis_pelayanan,
                            tgl: _tlg_sep,
                            poli: _poli_tujuan
                        }

                        return query;
                    },
                },
            });

            $(".select2DpjpServe").select2({
                placeholder: "Pilih Dokter DPJP Melayani",
                ajax: {
                    url: "/api/bpjs/referensi-dpjp",
                    dataType: "json",
                    quietMillis: 250,
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public',
                            first_search: _formPendaftaran.first_search,
                            cacheId: 'dpjp-melayani',
                        }

                        return query;
                    },
                    success: function (res) {
                        _formPendaftaran.first_search = true;
                    }
                },
                templateSelection: function (res) {
                    _formPendaftaran.tmpBpjs.dpjpServeText = res.text
                    _formPendaftaran.tmpBpjs.dpjpId = res.id
                    return res.text;
                }
            });

            /** Auto Suggest Dokter DPJP */
            if((typeof (_penOl) !== "undefined")){
                if(_penOl?.all?.kode_dokter_bpjs != undefined){
                    let kode_dokter_bpjs = _penOl?.all?.kode_dokter_bpjs
                    let title = `Tenaga Medis ${kode_dokter_bpjs}`
                    let option_dpjp = '<option id="option_dpjp" value="'+kode_dokter_bpjs+'" selected="">'+title+'</option>'
                    $("#kode_dpjp_melayani").append(option_dpjp);
                    $('#kode_dpjp_melayani').val(kode_dokter_bpjs).trigger('change');
                }
            }

            $(".select2KelasRawat").select2({
                placeholder: "Pilih Kelas Rawat",
                ajax: {
                    url: "/api/bpjs/referensi-kelas-rawat",
                    dataType: "json",
                    quietMillis: 250,
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }

                        return query;
                    },
                },
            });
            bindingRegionBpjs({
                province: $("#kode_provinsi"),
                city: $("#kode_kabupaten"),
                district: $("#kode_kecamatan")
            })

            // select poli tujuan di daftar bpjs
            $(".select2Poli").select2({
                placeholder: "Pilih Poli Tujuan",
                minimumInputLength: 3,
                ajax: {
                    url: "/api/bpjs/referensi-poli-new",
                    dataType: "json",
                    quietMillis: 250,
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public'
                        }

                        return query;
                    },
                },
                templateSelection: function (res) {
                    _formPendaftaran.tmpBpjs.poliTujuan = res.id
                    return res.text;
                }
            });

            $("select").on('select2:close', ({ delegateTarget }) => {
                $(delegateTarget).focus()
            })

            if (jenisRujukan != JenisPencarianRujukan) {
                renderPickadate($('#tanggal_sep_1'), {
                    dependElementPicker: $('#tanggal_sep_1').parent().find('.input-group-addon'),
                    lowerThanToday: true,
                    defaultValue: new Date()
                })
                renderPickadate($('#tanggal_sep'), {
                    dependElementPicker: $('#tanggal_sep').parent().find('.input-group-addon'),
                    lowerThanToday: true,
                    defaultValue: $("#tanggal_sep_1").pickadate().val(),
                    readOnly: true
                })
                renderPickadate($('#tanggal_rujukan'), {
                    dependElementPicker: $('#tanggal_rujukan').parent().find('.input-group-addon'),
                    lowerThanToday: true,
                    defaultValue: new Date()
                })
            } else {
                var tanggal_rujukan = new Date(rujukan.tglKunjungan);

                renderPickadate($('#tanggal_sep_1'), {
                    lowerThanToday: true,
                    defaultValue: new Date()
                })
                renderPickadate($('#tanggal_sep'), {
                    lowerThanToday: true,
                    defaultValue: $("#tanggal_sep_1").pickadate().val(),
                    readOnly: true
                })
                renderPickadate($('#tanggal_rujukan'), {
                    lowerThanToday: true,
                    defaultValue: tanggal_rujukan
                })
            }
            $("#tanggal_sep").attr("readonly", true);
            renderPickadate($('#tanggal_kejadian'), {
                dependElementPicker: $('#tanggal_kejadian').parent().find('.input-group-addon'),
                lowerThanToday: true,
                defaultValue: new Date()
            })
            $('#no_surat_kontrol').on('change', function () {
                var no_sk = $('#no_surat_kontrol').val();
                let url = window.location.origin + '/api/bpjs/cari-surat-kontrol?no_surat_kontrol=' + no_sk;
                if (no_sk != null && no_sk.trim() != '') {
                    $.ajax({
                        type: "GET",
                        url: url,
                        dataType: "JSON",
                        success: function (responsen) {
                            let responsebpjs = responsen.response;
                            $("#option_dpjp").remove();
                            $("#option_dpjp_pemberi").remove();

                            if (responsebpjs.kodeDokter != null) {
                                let option_dpjp = '<option id="option_dpjp" value="' + responsebpjs.kodeDokter + '" selected="">' + responsebpjs.namaDokter + '</option>'
                                $("#kode_dpjp_melayani").append(option_dpjp);
                                $('#kode_dpjp_melayani').val(responsebpjs.kodeDokter).trigger('change');
                            }
                            if (responsebpjs.kodeDokterPembuat) {
                                let option_dpjp_pemberi = '<option id="option_dpjp_pemberi" value="' + responsebpjs.kodeDokterPembuat + '" selected="">' + responsebpjs.namaDokterPembuat + '</option>'
                                $("#kode_dpjp").append(option_dpjp_pemberi);
                                $('#kode_dpjp').val(responsebpjs.kodeDokterPembuat).trigger('change');
                            } else {
                                let option_dpjp_pemberi = '<option id="option_dpjp_pemberi" value="' + responsebpjs.kodeDokter + '" selected="">' + responsebpjs.namaDokter + '</option>'
                                $("#kode_dpjp").append(option_dpjp_pemberi);
                                $('#kode_dpjp').val(responsebpjs.kodeDokter).trigger('change');
                            }
                            if (responsebpjs.poliTujuan) {
                                let option_poli_tujuan = '<option id="option_poli_tujuan" value="' + responsebpjs.poliTujuan + '" selected="">' + responsebpjs.namaPoliTujuan + '</option>'
                                $("#poli_tujuan").append(option_poli_tujuan);
                                $('#poli_tujuan').val(responsebpjs.poliTujuan).trigger('change');
                            }
                            if (responsebpjs.noSuratKontrol != null) {
                                $('#no_surat_kontrol').val(responsebpjs.noSuratKontrol)
                            }
                        },
                        error: function (res) {
                            docoNotification("warning", "Peringatan!", res.responseJSON.response.text);
                        }
                    });
                }
            })

            // hide form rujukan when poli tujuan = IGD
            $('#poli_tujuan').change(function () {
                if ($(this).val() == 'IGD') {
                    $('.frm-rujukan').hide();
                    // meminimalisasi form jika poli IGD
                    $(".asal_rujukan").hide();
                    $(".ppk_rujukan").hide();
                    $(".tanggal_rujukan").hide();
                    $(".no_rujukan").hide();
                    $(".kelas_rawat").hide();
                    $(".katarak").hide();
                    $(".dpjp_form").hide();
                } else {
                    $('.frm-rujukan').show();
                    // menormalkan form ketika bukan IGD
                    $(".asal_rujukan").show();
                    $(".ppk_rujukan").show();
                    $(".tanggal_rujukan").show();
                    $(".no_rujukan").show();
                    // $(".kelas_rawat").show();
                    $(".katarak").show();
                    // $(".dpjp_form").show();
                }

                if (jenisRujukan != JenisPencarianRujukan) { // dpjp untuk rujukan, tidak boleh diubah. harus tetap ambil dari rujukan sebelumnya
                    _formPendaftaran.setdpjp($('#jenis_pelayanan').val(), $('#tanggal_sep').val(), $(this).val());
                }

                if (typeof _formPendaftaran.dataReturnBpjs.rujukan !== 'undefined') {
                    // ketika beda poli dengan poli rujukan awal
                    if ($(this).val() != _formPendaftaran.dataReturnBpjs.rujukan.poliRujukan.kode) {
                        if(post_ranap) {
                            $('.field-kode_dpjp').removeClass('hidden');
                            $('.field-kode_dpjp').addClass('required');
                            $('.field-no_surat_kontrol').removeClass('hidden');
                            $('.field-no_surat_kontrol').addClass('required');
                        } else {
                            $('.field-kode_dpjp').removeClass('required');
                            $('.field-kode_dpjp').addClass('hidden');
                            $('.field-no_surat_kontrol').removeClass('required');
                            $('.field-no_surat_kontrol').addClass('hidden');
                        }
                    } else {
                        $('.field-kode_dpjp').removeClass('hidden');
                        $('.field-kode_dpjp').addClass('required');
                        $('.field-no_surat_kontrol').removeClass('hidden');
                        $('.field-no_surat_kontrol').addClass('required');
                    }
                }
            });

            // hide beberapa field ketika kondisi Rujukan Manual / IGD
            if (_inputBpjs.jenis_pencarian === '2' && getRoom === 'igd') {
                $(".asal_rujukan").hide();
                // $(".ppk_rujukan").hide();
                $(".tanggal_rujukan").hide();
                $(".no_rujukan").hide();
                $(".kelas_rawat").hide();
                $(".katarak").hide();
            } else {
                $(".asal_rujukan").show();
                $(".ppk_rujukan").show();
                $(".tanggal_rujukan").show();
                $(".no_rujukan").show();
                $(".kelas_rawat").show();
                $(".katarak").show();
            }

            // hide beberapa field untuk form RANAP
            if (_inputBpjs.jenis_pencarian === '2' && getRoom === 'ranap') {
                $(".form-poli_tujuan").hide();
            }

            $('#kasus_kecelakaan').change(function () {
                var base = $(this).val();
                if (base == 0) {
                    $('.kasus_kecelakaan_form').hide();
                    $('#status_suplesi').val('0');
                } else if (base == 3) {
                    $('.kasus_kecelakaan_form').show();
                    $('.suplesi_form').hide();
                    $('#status_suplesi').val('0');
                } else {
                    swal({
                        title: "Perhatian!",
                        text: "Apakah ini merupakan kasus kecelakaan lalu lintas baru?",
                        type: "info",
                        showCancelButton: true,
                        cancelButtonText: "Tidak",
                        confirmButtonText: "Ya",
                    }, function (i) {
                        if (i) {
                            $('.kasus_kecelakaan_form').show();
                            $('.suplesi_form').hide();
                            $('#status_suplesi').val('0');
                            $("#tanggal_kejadian").focus()
                        } else {
                            $('.suplesi_form').show();
                            $('.kasus_kecelakaan_form').hide();
                            $('#status_suplesi').val('1');

                            $('#button-list-sep').click();
                        }
                    });
                }
            });

            $('#bpjsnew_detail_nik').html('<strong>:</strong> ' + peserta.nik);
            $('#bpjsnew_detail_tgl_lahir').html('<strong>:</strong> ' + peserta.tglLahir);
            $('#bpjsnew_detail_jenis_peserta').html('<strong>:</strong> ' + peserta.jenisPeserta.keterangan);
            $('#bpjsnew_detail_hak_kelas').html('<strong>:</strong> ' + peserta.hakKelas.keterangan);
            $('#bpjsnew_detail_nokartu').html('<strong>:</strong> ' + peserta.noKartu);
            $('.btn-detail-bpjs').attr('action', `/pendaftaran/daftar-igd/detail-history-bpjs?no_kartu=${peserta.noKartu}`)
            $('.btn-detail-bpjs').show();

            if ($('#asal_rujukan').val() == 2) {
                $('#no_surat_kontrol').val('');
                if(getRoom !== PARAM_INS_IGD) {
                    $('.dpjp_form').show();
                }
            } else {
                $('#no_surat_kontrol').val('');
                $('.dpjp_form').hide();
            }

            /** Auto Suggest No Surat Kontrol */
            if((typeof (_penOl) !== "undefined")){
                if(_penOl?.all?.nomorreferensi != undefined && _penOl?.all?.jeniskunjungan != undefined){
                    let noSuratKontrol = _penOl?.all?.nomorreferensi
                    if(_penOl?.all?.jeniskunjungan == '1098' || _penOl?.all?.jeniskunjungan == '1097'){
                        $('#no_surat_kontrol').val(noSuratKontrol).change()
                    }
                } else if (_formPendaftaran.params == 'rajal') {
                    let noSuratKontrol = rencanaKontrol?.length == 1 ? rencanaKontrol[0]?.noSuratKontrol : ''
                    $('#no_surat_kontrol').val(noSuratKontrol).change();
                }else{
                    $('#no_surat_kontrol').val('')
                }
            }

            if (jenisRujukan == JenisPencarianRujukan) { // rujukan
                // poli Tujuan
                var poliRujukan = rujukan.poliRujukan;
                var option = new Option(poliRujukan.nama, poliRujukan.kode, true, true);
                var peserta = _formPendaftaran.dataReturnBpjs.peserta;
                $("#poli_tujuan").append(option);
                $('#poli_tujuan').val(poliRujukan.kode).trigger('change');
                // form rujukan
                $('.frm-rujukan').show();
                $('#asal_rujukan').val($('#asal_rujukan_1').val()).trigger('change');

                // asal rujukan
                var provPerujuk = rujukan.provPerujuk;
                _formPendaftaran.tmpBpjs.ppkPerujukNama = provPerujuk.nama
                var option = new Option(provPerujuk.nama, provPerujuk.kode, true, true);
                ppkrujukan_nama = provPerujuk.nama
                ppkrujukan_kode = provPerujuk.kode
                $("#ppk_rujukan").append(option);
                $('#ppk_rujukan').val(provPerujuk.kode).trigger('change');

                // tgl rujukan set from tanggal kunjungan pertama
                // var date = new Date(rujukan.tglKunjungan);
                // var picker = $('#tanggal_rujukan').pickadate('picker');
                // picker.set('select', finalDate);

                if (_formPendaftaran.dataReturnBpjs.lastPoli) {
                    new PNotify({
                        title: '',
                        text: 'Peserta ini merupakan peserta terindikasi sebagai Kontrol Ulang/Rujuk Internal.<br>Kunjungan ke- 2 Dengan Rujukan yang sama.',
                        addclass: 'alert alert-info alert-arrow-right alert-styled-right',
                        type: 'info'
                    });

                    if (_formPendaftaran.params != 'penunjang') {
                        $(".dpjp_form").show();
                    }

                    _formPendaftaran.setdpjp($('#jenis_pelayanan').val(), $('#tanggal_sep').val(), _formPendaftaran.dataReturnBpjs.lastPoli);

                } else {
                    $(".dpjp_form").hide();
                    // $("#is_skdp").val('0');
                }

                if (typeof _formPendaftaran.dataReturnBpjs.rujukan.rujukan_khusus != 'undefined' && _formPendaftaran.dataReturnBpjs.rujukan.rujukan_khusus != null ) {
                    let tgl_rujukan_khusus = new Date(_formPendaftaran.dataReturnBpjs.rujukan.rujukan_khusus.tglrujukan_berakhir);
                    let diagnosa_rujukan_khusus = _formPendaftaran.dataReturnBpjs.rujukan.rujukan_khusus.diagnosa;

                    new PNotify({
                        title: '<i class="fa fa-exclamation-circle"></i> Perhatian!!',
                        text: 'Pasien rujukan khusus pastikan diagnosa dan tanggal rujukan sesuai.',
                        addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
                        type: 'warning'
                    });

                    $('#tanggal_rujukan').off('change');
                    $('#tanggal_rujukan').pickadate('picker').stop();
                    $('#tanggal_rujukan').pickadate({
                        format: 'dd-mm-yyyy',
                        min: [1900, 01, 01],
                        max: false,
                        onClose: function () {
                            element.focus();
                        },
                    });

                    let tgl_rujukan_picker = $('#tanggal_rujukan').pickadate('picker');

                    setTimeout(() => {
                        tgl_rujukan_picker.set('select', new Date(tgl_rujukan_khusus));
                    }, 1000);

                    if (diagnosa_rujukan_khusus != null) {
                        let option = new Option(diagnosa_rujukan_khusus.diagnosa_nama, diagnosa_rujukan_khusus.diagnosa_kode, true, true);
                        $("#diagnosa_awal").append(option);
                        $('#diagnosa_awal').val(diagnosa_rujukan_khusus.diagnosa_kode).trigger('change');
                    }

                } else {
                    // diagnosaawal
                    var diagnosa = rujukan.diagnosa;
                    var option = new Option(diagnosa.nama, diagnosa.kode, true, true);
                    $("#diagnosa_awal").append(option);
                    $('#diagnosa_awal').val(diagnosa.kode).trigger('change');
                }

                $('#no_kartu').val(peserta.noKartu);
                $('#jenis_pelayanan').val(rujukan.pelayanan.kode).trigger('change');
                $('#no_rujukan_1').val(rujukan.noKunjungan);
                $('#kelas_rawat').val(peserta.hakKelas.kode);
                $('#nomr').val(peserta.mr.noMR);
                $('#no_telp').val(peserta.mr.noTelepon ? (peserta.mr.noTelepon).toString().replace(/\s/g, '') : null);

                $('#hide-pelayanan').val(rujukan.pelayanan.kode);
            }

            if(jenisRujukan == JenisPencarianManual && lastRujukan && _formPendaftaran.params != PARAM_INS_IGD) {
                let optionPoli = new Option(lastRujukan.poliNama, lastRujukan.poli, true, true);
                let optionDiagnosa = new Option(lastRujukan.diagnosaNama, lastRujukan.diagnosa, true, true);
                let optionPpk = new Option(lastRujukan.ppkNama, lastRujukan.ppk, true, true);
                $("#poli_tujuan").append(optionPoli);
                $('#poli_tujuan').val(lastRujukan.poli).trigger('change');
                $("#diagnosa_awal").append(optionDiagnosa);
                $('#diagnosa_awal').val(lastRujukan.diagnosa).trigger('change');
                setTimeout(function () {
                    $('#asal_rujukan').val(lastRujukan.asalFaskes).trigger('change');
                }, 1000);
                setTimeout(function () {
                    $('#no_rujukan_1').val(lastRujukan.noRujukan);
                    $("#ppk_rujukan").append(optionPpk);
                    $('#ppk_rujukan').val(lastRujukan.ppk).trigger('change');
                }, 2000);
            }

            var _disabled = (post_ranap || jenisRujukan == JenisPencarianRujukan) ? true : false;
            var _disabledNoRujukan = (post_ranap || jenisRujukan == JenisPencarianRujukan) ? false : false; //case tgl 28 sep 2021 (norujukan kosong dan disabled)

            $("#asal_rujukan").prop("disabled", _disabled);
            if ($("#asal_rujukan").prop('disabled')) {
                $("#asal_rujukan_hidden").val(asal_rujukan);
            }
            $("#asal_rujukan").val(asal_rujukan).trigger('change');

            // ppk rujukan
            var option = new Option(ppkrujukan_nama, ppkrujukan_kode, true, true);
            $("#ppk_rujukan").prop("disabled", _disabled);
            if ($("#ppk_rujukan").prop('disabled')) {
                $("#ppk_rujukan_hidden").val(ppkrujukan_kode);
            }
            $("#ppk_rujukan").append(option);
            $('#ppk_rujukan').val(ppkrujukan_kode).trigger('change');

            if (post_ranap) {
                $(".dpjp_form").show();
            }

            if (typeof _formPendaftaran.dataBpjs.mr != 'undefined' && _formPendaftaran.dataBpjs.mr != null) {
                if(typeof _formPendaftaran.dataReturnBpjs.no_rekam_medik != 'undefined') {
                    $("input[name='BpjsNewForm[no_rekam_medik]']").val(_formPendaftaran.dataReturnBpjs.no_rekam_medik);
                } else {
                    $("input[name='BpjsNewForm[no_rekam_medik]']").val(peserta.mr.noMR);
                }
            } else if (typeof _formPendaftaran.dataReturnBpjs.no_rekam_medik != 'undefined') {
                $("input[name='BpjsNewForm[no_rekam_medik]']").val(_formPendaftaran.dataReturnBpjs.no_rekam_medik);
            } else {
                $("input[name='BpjsNewForm[no_rekam_medik]']").val(peserta.mr.noMR);
            }

            $("#no_rujukan_1").prop("readonly", _disabledNoRujukan);
            $("#no_rujukan_1").val(no_rujukan);
            $("#no_asuransi").val(peserta.noKartu);
            $("#kelas_rawat").val(peserta.hakKelas.kode).trigger('change');
            var tmt = peserta.tglTMT;
            var tat = peserta.tglTAT;
            $('#bpjsnew_detail_tmt_tat').html('<strong>:</strong> ' + tmt + ' - ' + tat);
            var kdProv = peserta.provUmum.kdProvider;
            var nmProv = peserta.provUmum.nmProvider;
            $('#bpjsnew_detail_ppk_rujukan').html('<strong>:</strong> ' + kdProv + " - " + nmProv);
            var statusPeserta = peserta.statusPeserta.keterangan;
            $('#bpjsnew_detail_status_peserta').html('<strong>:</strong> ' + statusPeserta);

            var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
            $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu=' + peserta.noKartu + '&tgl_sep=' + tglSep);
            $("#no_telp").val(peserta.mr.noTelepon ? (peserta.mr.noTelepon).toString().replace(/\s/g, '') : null);

            if(peserta.informasi.prolanisPRB != null) {
                $('#group_prb').show();
                $('#content_prb').html(peserta.informasi.prolanisPRB);
            }

            // if(_formPendaftaran.dataReturnBpjs.skdp != null) {
            //     let date = new Date();
            //     _formPendaftaran.dataReturnBpjs.skdp.forEach(function (item, index) {
            //         if(item.jnsKontrol == 2 && item.terbitSEP.toLowerCase() == 'belum') {
            //             $("#no_surat_kontrol").val(item.noSuratKontrol);
            //         }
            //     });
            // }
        }
    },
    setdpjp: function (jnsPelayanan, tglSep, poliTujuan) {
        let url = window.location.origin + '/api/bpjs/referensi-dokter?jnsPelayanan=' + jnsPelayanan + '&tglSep=' + tglSep + '&poliTujuan=' + poliTujuan;
        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'JSON',
            beforeSend: function () {
                $('#kode_dpjp').empty();
            },
            success: function (res) {
                $("#kode_dpjp").attr("data-placeholder", "--Pilih Dokter DPJP--");
                // foreach
                if (res.results) {
                    $.each(res.results, function (index, value) {
                        var newOption = new Option(value.text, value.id, false, false);
                        $('#kode_dpjp').append(newOption);
                    });
                    $('#kode_dpjp').trigger('change');
                }
            }
        });
    },
    validatePasien: function (object) {
        var _data = object.serializeArray();
        var _result = false;
        var next = true;
        if ($("#tipepasienform-is_bbl").is(':checked')) {
            $().docoForm('click', {
                url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-pasien?is_bbl=true',
                data: _data,
                skipConfirm: true,
                skipSuccessNotif: true,
                async: false,
                success: function (data) {
                  _result = true;
                },
                error: ({ responseJSON }) => {
                  if (typeof responseJSON !== 'undefined' && typeof responseJSON.meta !== 'undefined' && typeof responseJSON.meta.message !== 'undefined') {
                    docoNotification('error', responseJSON.meta.message, '')
                  }
                }
              });
        } else {
            $().docoForm('click', {
                url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-pasien',
                data: _data,
                skipConfirm: true,
                skipSuccessNotif: true,
                async: false,
                success: function (data) {
                    _result = true;
                },
                error: function (error) {
                    if(error.responseJSON.response.text !== undefined) {
                        var msg_error = error.responseJSON.response.text;
                        docoNotification('error', 'Proses Gagal !', msg_error);
                    }

                    if(error.responseJSON.response.data !== undefined) {
                        var error = error.responseJSON.response.data;
                        var msg_error = '';
                        for (var key in error) {
                            msg_error = error[key][0];
                        }

                        docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan. ');
                    }

                    $('.no_identitas_pasien').each(function (key, obj) {
                        if (!$(this).val()) {
                            next = false;

                            $(this).parent().addClass('has-error');
                            $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;No Identitas Pasien cannot be blank.');
                            $(this).parent().find('.fa').addClass('fa-exclamation-circle');

                            docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
                        }
                    });

                    $('.jenis_identitas').each(function (key, obj) {
                        if (!$(this).val()) {
                            next = false;

                            $(this).parent().addClass('has-error');
                            $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Jenis Identitas cannot be blank.');
                            $(this).parent().find('.fa').addClass('fa-exclamation-circle');

                            docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
                        }

                        arrayJenisIdentitas.push($(this).val());
                    });
                }
            });
        }

        return _result;
    },
    validateRujukan: function (object) {
        var _data = object.serializeArray();
        _data.push({
            name: 'asalrujukan_id',
            value: _formPendaftaran.tipePasien.asalrujukan_id
        });
        var _result = false;
        $().docoForm('click', {
            url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-rujukan',
            data: _data,
            skipConfirm: true,
            skipSuccessNotif: true,
            async: false,
            success: function (data) {
                _result = true;
            }
        });
        return _result;
    },
    formKunjugan: function () {
        var _clone = _formPendaftaran.formInputKunjugan;
        $('.steps-basic').steps("add", {
            title: "Kunjungan",
            content: _clone.html()
        });

        var selectCaraBayar = $('.selectCarabayar').find(':selected').attr('data-id')

        // if (_formPendaftaran.params == 'penunjang') {
        //     let pasienRs = $("input[name='TipePasienForm[is_aps]']")[2]['checked']
        //     if (!pasienRs) {
        //         generatePj(_formPendaftaran)
        //     }
        // } else {
        //     generatePj(_formPendaftaran)
        // }

        /** Re init */
        var _content = $('#form-kunjungan-content');
        _content.find('span.select2').remove();
        _content.find('.select2').select2();

        console.log(_formPendaftaran.tmpBpjs.dpjpId)
        if ($('#kunjunganform-jeniskasuspenyakit_id').data('depdrop')) {
            $('#kunjunganform-jeniskasuspenyakit_id').depdrop('destroy');
        }

        if (_formPendaftaran.params == 'penunjang') {
            if (_formPendaftaran.tipePasien.jenis_pendaftaran == 'pasien-rs') {
                $('#kunjunganform-is_pj').attr("type", "hidden");
                $('#uniform-kunjunganform-is_pj').attr("style", "display: none");
                $("label[for='kunjunganform-is_pj']").attr("style", "display: none");

                if (_formPendaftaran.dataPendaftaran) {
                    if (_formPendaftaran.dataPendaftaran.jeniskasuspenyakit_id) {
                        $('#kunjunganform-jeniskasuspenyakit_id').attr("disabled", "true");
                        $('#kunjunganform-jeniskasuspenyakit_id').depdrop({
                            allowClear: true,
                            depends: ["ruangan_id"],
                            placeholder: "-- Pilih --",
                            selected: 1,
                            url: "/pendaftaran/daftar/get-jenis-kasus-penyakit?selected=" + _formPendaftaran.dataPendaftaran.jeniskasuspenyakit_id + "&asalrujukan_id=" + true
                        });
                    }
                    if (_formPendaftaran.dataPendaftaran.kelaspelayanan_id) {
                        if ($('#kunjunganform-kelaspelayanan_id').data('depdrop')) { $('#kunjunganform-kelaspelayanan_id').depdrop('destroy'); }
                        $('#kunjunganform-kelaspelayanan_id').attr('disabled', 'true');
                        $('#kunjunganform-kelaspelayanan_id').depdrop({
                            allowClear: true,
                            depends: ["ruangan_id"],
                            placeholder: "-- Pilih --",
                            url: "/pendaftaran/daftar/get-kelas-pelayanan?selected=" + _formPendaftaran.dataPendaftaran.kelaspelayanan_id
                        });
                    }
                    if (_formPendaftaran.dataPendaftaran.keadaanmasuk_id) {
                        $('#kunjunganform-keadaan_masuk').val(_formPendaftaran.dataPendaftaran.keadaanmasuk_id).trigger('change');
                    }
                    if (_formPendaftaran.dataPendaftaran.transportasi_id) {
                        $('#kunjunganform-transportasi').val(_formPendaftaran.dataPendaftaran.transportasi_id).trigger('change');
                    }
                    if (_formPendaftaran.dataPendaftaran.keterangan_pendaftaran) {
                        $('#kunjunganform-keterangan').val(_formPendaftaran.dataPendaftaran.keterangan_pendaftaran);
                    }

                    _formPendaftaran.populateDokterDropdown(_formPendaftaran.dataPendaftaran, '#kunjunganform-pegawai_id');
                }
                $('#kunjunganform-keadaan_masuk').attr("disabled", "true");
                $('#kunjunganform-transportasi').attr("disabled", "true");
                $('#kunjunganform-keterangan').attr("disabled", "true");
                if ($('#kunjunganform-pegawai_id').data('depdrop')) { $('#kunjunganform-pegawai_id').depdrop('destroy'); }
                $('#kunjunganform-pegawai_id').depdrop({
                    allowClear: true,
                    depends: ["ruangan_id"],
                    placeholder: "-- Pilih --",
                    url: "/pendaftaran/daftar/get-dokter?param=penunjang&asalrujukan_id=" + _formPendaftaran.tipePasien.asalrujukan_id
                });
            } else {
                $('#kunjunganform-jeniskasuspenyakit_id').depdrop({
                    allowClear: true,
                    depends: ["ruangan_id"],
                    placeholder: "-- Pilih --",
                    selected: 1,
                    url: "/pendaftaran/daftar/get-jenis-kasus-penyakit?selected=" + default_jenis_penyakit
                });

                $('#kunjunganform-jeniskasuspenyakit_id').on('depdrop:afterChange', function (event, id, value) {
                    if ($('#selectCarabayar').is(':focus')) {
                        $('#kunjunganform-jeniskasuspenyakit_id').focus();
                    }
                });

                if ($('#kunjunganform-kelaspelayanan_id').data('depdrop')) { $('#kunjunganform-kelaspelayanan_id').depdrop('destroy'); }
                $('#kunjunganform-kelaspelayanan_id').depdrop({
                    allowClear: true,
                    depends: ["ruangan_id"],
                    placeholder: "-- Pilih --",
                    url: "/pendaftaran/daftar/get-kelas-pelayanan"
                });

                if ($('#kunjunganform-pegawai_id').data('depdrop')) { $('#kunjunganform-pegawai_id').depdrop('destroy'); }
                $('#kunjunganform-pegawai_id').depdrop({
                    allowClear: true,
                    depends: ["ruangan_id"],
                    placeholder: "-- Pilih --",
                    url: "/pendaftaran/daftar/get-dokter?param=penunjang"
                });


                $('#kunjunganform-keadaan_masuk').val('159').trigger('change');
            }

            $('#instalasi_id').on('change', function() {
                var _instalasi_id = $(this).val();
                $('#kunjunganform-ruangan_id').closest('.form-group').removeClass('has-error');
                $('#kunjunganform-ruangan_id').closest('.col-md-7').removeClass('has-error');
                $('#kunjunganform-ruangan_id').closest('.form-group').find('.help-block').html('');

                $('#kunjunganform-jeniskasuspenyakit_id').closest('.form-group').removeClass('has-error');
                $('#kunjunganform-jeniskasuspenyakit_id').closest('.col-md-7').removeClass('has-error');
                $('#kunjunganform-jeniskasuspenyakit_id').closest('.form-group').find('.help-block').html('');

                $('#kunjunganform-kelaspelayanan_id').closest('.form-group').removeClass('has-error');
                $('#kunjunganform-kelaspelayanan_id').closest('.col-md-7').removeClass('has-error');
                $('#kunjunganform-kelaspelayanan_id').closest('.form-group').find('.help-block').html('');

                $('#kunjunganform-pegawai_id').closest('.form-group').removeClass('has-error');
                $('#kunjunganform-pegawai_id').closest('.col-md-7').removeClass('has-error');
                $('#kunjunganform-pegawai_id').closest('.form-group').find('.help-block').html('');

                // Case Fisioterapi
                if (_instalasi_id != 'undefined' && _instalasi_id == 7) {
                    $('.paket-mcu').hide();
                    $('.paket-fisioterapi').show();
                } else {
                    $('.paket-mcu').show();
                    $('.paket-fisioterapi').hide();
                }
            });
        } else {
            $('#kunjunganform-jeniskasuspenyakit_id').depdrop({
                allowClear: true,
                depends: ["ruangan_id"],
                placeholder: "-- Pilih --",
                selected: 1,
                url: "/pendaftaran/daftar/get-jenis-kasus-penyakit?selected=" + default_jenis_penyakit
            });

            $('#kunjunganform-jeniskasuspenyakit_id').on('depdrop:afterChange', function (event, id, value) {
                if ($('#selectCarabayar').is(':focus')) {
                    $('#kunjunganform-jeniskasuspenyakit_id').focus();
                }
            });

            if ($('#kunjunganform-kelaspelayanan_id').data('depdrop')) { $('#kunjunganform-kelaspelayanan_id').depdrop('destroy'); }
            $('#kunjunganform-kelaspelayanan_id').depdrop({
                allowClear: true,
                depends: ["ruangan_id"],
                placeholder: "-- Pilih --",
                url: "/pendaftaran/daftar/get-kelas-pelayanan"
            });

            if ($('#kunjunganform-dokter_id').data('depdrop')) { $('#kunjunganform-dokter_id').depdrop('destroy'); }

            // perubahan jika yg di pilih adalah reservasi akan ambil data dokter reservasi
            var type_kunjungan_dokter = true;
            if ((typeof (_penOl) !== "undefined")) {
                if (_penOl.status_pasien != undefined) {
                    type_kunjungan_dokter = false;
                    let jenisReservasi = _penOl.all?.jenis_reservasi 
                    if (_penOl.all != undefined && _penOl.all.jadwaldokter_id != undefined) {
                        $('#kunjunganform-dokter_id').depdrop({
                            allowClear: true,
                            depends: ["datetime", "ruangan_id"],
                            placeholder: "-- Pilih --",
                            url: "/pendaftaran/reservasi-poliklinik/get-dokter?is_from_reservasi=1&with_kuota=true&booked_jadwaldokter_id="+_penOl.all.jadwaldokter_id+"&jenis_reservasi="+jenisReservasi+""
                        });
                    } else {
                        $('#kunjunganform-dokter_id').depdrop({
                            allowClear: true,
                            depends: ["datetime", "ruangan_id"],
                            placeholder: "-- Pilih --",
                            url: "/pendaftaran/reservasi-poliklinik/get-dokter?is_from_reservasi=1&dokter_id="+_penOl.dokter_id+""
                        });
                    }

                }
            }
            if (type_kunjungan_dokter == true) {
                var isBpjs = false
                if(selectCaraBayar == '418'){
                    isBpjs = true
                }
                var paramBpjs = "&is_bpjs="+isBpjs
                if (instalasi != instalasiMcu && _formPendaftaran.params != PARAM_INS_IGD){
                    if (_formPendaftaran.dataAntrian) {
                        $('#kunjunganform-dokter_id').depdrop({
                            allowClear: true,
                            depends: ["ruangan_id"],
                            placeholder: "-- Pilih --",
                            url: "/pendaftaran/daftar/get-dokter?param="+_formPendaftaran.params+"&with_kuota=true&booked_jadwaldokter_id="+_formPendaftaran.dataAntrian.jadwaldokter_id+paramBpjs,
                        });
                    } else {
                        $('#kunjunganform-dokter_id').depdrop({
                          allowClear: true,
                          depends: ["ruangan_id"],
                          placeholder: "-- Pilih --",
                          url: "/pendaftaran/daftar/get-dokter?param="+_formPendaftaran.params+"&with_kuota=true"+paramBpjs,
                        });
                    }

                } else {
                    $('#kunjunganform-dokter_id').depdrop({
                        allowClear: true,
                        depends: ["ruangan_id"],
                        placeholder: "-- Pilih --",
                        url: "/pendaftaran/daftar/get-dokter?param=penunjang"+paramBpjs,
                    });
                }

            }


            $('#kunjunganform-keadaan_masuk').val('159').trigger('change');
        }

        $('#kunjunganform-kelaspelayanan_id').on('depdrop:afterChange', function (event, id, value) {
            _formPendaftaran.listTarif = [];
            _formPendaftaran.getKarcis();
            $('.btn-pemeriksaan-clear').trigger('click');
        });

        if ($('#ruangan_id').data('depdrop')) { $('#ruangan_id').depdrop('destroy'); }
        $('#ruangan_id').depdrop({
            class: "select2",
            depends: ["instalasi_id"],
            placeholder: "",
            url: "/pendaftaran/daftar/get-ruangan"
        });

        if (_content.find('#instalasi_id').val()) {
            $('#instalasi_id').trigger('depdrop:change');
            var _instalasi_id = $('#instalasi_id').val();
            // Case Fisioterapi
            if (_instalasi_id != 'undefined' && _instalasi_id == 7) {
                $('.paket-mcu').hide();
                $('.paket-fisioterapi').show();
            } else {
                $('.paket-mcu').show();
                $('.paket-fisioterapi').hide();
            }
        }

        $('.btn-buat-program').on('click', function(){
            var _action = "/pendaftaran/daftar/modal-buat-program-fisio?ruangan_id=";
            var _ruangan_id = $('#ruangan_id').val();

            $('.btn-buat-program').attr('action', _action+_ruangan_id);
            return true;
        });

        if (_formPendaftaran.dataAntrian) {
            _content.find('#instalasi_id').val(_formPendaftaran.dataAntrian.instalasi_id).trigger("change");
            _content.find('#instalasi_id').trigger("depdrop:change");
            _content.find('#instalasi_id').val(_formPendaftaran.dataAntrian.instalasi_id).trigger("change");
            _content.find('#ruangan_id').val(_formPendaftaran.dataAntrian.ruangan_id).trigger("change");
            _content.find('#ruangan_id').trigger("depdrop:change");
        }

        $('#kunjunganform-dokter_id').on('depdrop:afterChange', function (event, id, value) {
            if (_formPendaftaran.dataAntrian) {
                setTimeout(function () {
                    if (_formPendaftaran.dataAntrian.jadwaldokter_id) {
                        $('#kunjunganform-dokter_id').find("[value="+_formPendaftaran.dataAntrian.pegawai_id+"]").each(function(){
                            if ($(this).data('jadwaldokter_id') == _formPendaftaran.dataAntrian.jadwaldokter_id) {
                                $(this).prop('selected', true).change()
                            }
                        });
                    } else {
                        $('#kunjunganform-dokter_id').val(_formPendaftaran.dataAntrian.pegawai_id).trigger("change");
                    }
                    $('#kunjunganform-dokter_id').trigger("depdrop:change");
                }, 2000);
            }else{
                $('#kunjunganform-dokter_id option').each(function(){
                    if ($(this).data('jadwaldokter_id') == _penOl?.all?.jadwaldokter_id) {
                        $(this).prop('selected', true).change()
                    }
                });
            }
        });

        $('#kunjunganform-nomor_urut').on('depdrop:afterChange', function (event, id, value) {
            $('.field-kunjunganform-nomor_urut').removeClass('has-error');
            $('.field-kunjunganform-nomor_urut .help-block').html('');
        });

        if (Object.keys(_formPendaftaran.pendaftaranOl).length) {
            _content.find('#ruangan_id').val(_formPendaftaran.pendaftaranOl.ruangan_id).trigger("change");
            _content.find('#ruangan_id').trigger("depdrop:change");
            setTimeout(function () {
                if (_penOl.all != undefined && _penOl.all.jadwaldokter_id != undefined) {
                    $('#kunjunganform-dokter_id').find("[value="+_penOl.dokter_id+"]").each(function(){
                        if ($(this).data('jadwaldokter_id') == _penOl?.all?.jadwaldokter_id) {
                            $(this).prop('selected', true).change()
                        }
                    });
                } else {
                    $('#kunjunganform-dokter_id').val(_penOl.dokter_id).trigger('change');
                }
                var date = new Date();
                // this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
                $('input[name="KunjunganForm[tgl_pendaftaran]"]').val(convertDateByFormat(date, 'Y-M-d  h:i:s')).trigger('change');
            }, 1000);
        } else {
            $('.selectDokter').on('depdrop:afterChange', function (event, id, value) {
                $(this).val('').trigger('change');
            });
        }

        $("#datetime").AnyTime_noPicker();
        $("#datetime").AnyTime_picker({
            format: "%d-%m-%Y %H:%i",
        });

        $("#datetime").val(function () {
            var d = new Date();
            if (Object.keys(_formPendaftaran.pendaftaranOl).length) {
                if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined') {
                    return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);
                }
                return _formPendaftaran.pendaftaranOl.tgl_pendaftaranol;
            }
            return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);
        });
        $('.selectKp').on('change', function () {
            _formPendaftaran.listTarif = [];
            _formPendaftaran.getKarcis();
        });
        $('.selectDokter').on('depdrop:afterChange change', function () {
            _formPendaftaran.listTarif = [];
            _formPendaftaran.getKarcis();
        });
        $(document).on('click', '.btn-remove-pemeriksaan', function () {
            var key = $(this).data('key')
            if (typeof _formPendaftaran.listPenunjang[key] !== 'undefined') {
                delete _formPendaftaran.listPenunjang[key];
                loadpemeriksaan(_formPendaftaran.listPenunjang);
            }
        });
        $(document).on('click', '.btn-remove-paket-mcu', function () {
            var key = $(this).data('key')
            if (typeof _formPendaftaran.listPenunjang[key] !== 'undefined') {
                delete _formPendaftaran.listPenunjang[key];
                loadpaketmcu(_formPendaftaran.listPenunjang);
            }
        });
        $(document).on('click', '.btn-remove-program-fisio', function () {
            var key = $(this).data('key')
            if (typeof _formPendaftaran.listFisio[key] !== 'undefined') {
                delete _formPendaftaran.listFisio[key];
                loadprogramfisio(_formPendaftaran.listFisio);
            }
        });
        $('.btn-pemeriksaan-clear').on('click', function () {
            _formPendaftaran.listPenunjang = {};
            loadpemeriksaan(_formPendaftaran.listPenunjang);
            loadpaketmcu(_formPendaftaran.listPenunjang);
        })

        if ($('#kunjunganform-nomor_urut').data('depdrop')) {
            $('#kunjunganform-nomor_urut').depdrop('destroy');
        }

        // after
        if ((typeof (_penOl) !== "undefined")) {
            if(typeof _penOl.all !== "undefined"){
                if (typeof _penOl.all.no_antrian !== "undefined") {
                    $('#kunjunganform-nomor_urut').depdrop({
                        class: 'select2',
                        depends: ['kunjunganform-dokter_id'],
                        params: ['ruangan_id','selectCarabayar'],
                        url: '/pendaftaran/daftar/get-nomor-urut?is_nomor_urut='+_penOl.all.no_antrian,
                        placeholder: false
                    });
                }
            } else {
                $('#kunjunganform-nomor_urut').depdrop({
                    class: 'select2',
                    depends: ['kunjunganform-dokter_id'],
                    params: ['ruangan_id','selectCarabayar'],
                    url: '/pendaftaran/daftar/get-nomor-urut',
                    placeholder: false
                });
            }
        }else{
            // before
            $('#kunjunganform-nomor_urut').depdrop({
                class: 'select2',
                depends: ['kunjunganform-dokter_id'],
                params: ['ruangan_id','selectCarabayar'],
                url: '/pendaftaran/daftar/get-nomor-urut',
                placeholder: false
            });
        }

        if (_formPendaftaran.params == 'ranap') {
            $('.kelas_rawat').show();
        } else {
            $('.kelas_rawat').hide();
        }
    },
    generateKunjungan: function (data) {
        var _penunjang = {
            paket: [],
            tindakan: []
        };
        $.each(data, function (x, y) {
            var _tmp = {
                is_cyto: y.is_cyto == 'false' ? 0 : 1,
                tariftindakan_id: y.tariftindakan_id,
                qty: y.qty !== 'undefined' ? y.qty : 1,
            };
            if (y.tipepaket_id != null) {
                _tmp.id = y.tipepaket_id;
                _penunjang.paket.push(_tmp);
            } else {
                _tmp.id = y.daftartindakan_id;
                _penunjang.tindakan.push(_tmp);
            }
        });
        return _penunjang;
    },
    populateDokterDropdown: function (_data, id) {
        var pegawai_id;
        var pegawai_nama;
        switch (_data.instalasi_id) {
            default:
                pegawai_id = _data.dokterrj_id;
                pegawai_nama = _data.nama_dok_rj_rd;
                break;
            case 1: //RJ
                pegawai_id = _data.dokterrj_id;
                pegawai_nama = _data.nama_dok_rj_rd;
                break;
            case 2: //RD
                pegawai_id = _data.dokterrj_id;
                pegawai_nama = _data.nama_dok_rj_rd;
                break;
            case 3: // RI
                pegawai_id = _data.dokterri_id;
                pegawai_nama = _data.nama_dok_ri;
                break;
        }

        var data = {
            id: pegawai_id,
            text: pegawai_nama
        }

        var newOption = new Option(data.text, data.id, true, false);
        $(id).append(newOption).trigger('change');
        $(id).val(pegawai_id).trigger('change');
    },
    validKunjugan: function (object) {
        var _data = object.serializeArray();
        var _penunjang = Object.keys(_formPendaftaran.listPenunjang).length;
        if (_penunjang) {
            _data.push({
                name: 'list_penunjang',
                value: JSON.stringify(_formPendaftaran.generateKunjungan(_formPendaftaran.listPenunjang))
            });
        }
        var _result = false;
        $().docoForm('click', {
            url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-kunjungan',
            data: _data,
            skipConfirm: true,
            skipSuccessNotif: true,
            async: false,
            success: function (data) {
                _result = true;
            }
        });
        return _result;
    },
    getInfoPasien: function (id, noRm) {
        $.ajax({
            type: 'GET',
            url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/get-info-pasien?id=' + id + '&no_rm=' + noRm,
            dataType: 'JSON',
            success: function (res) {
                var _caraBayar = $('.selectCarabayar').val();
                var _response = res.response;
                var _infoPasien = _response.info_pasien;
                if (!_infoPasien) {
                    _formPendaftaran.formPasien(2);
                    $('select').removeAttr('tabindex');
                    return true;
                }

                // info piutang pasien
                var info_piutang = document.getElementById("info-piutang");
                if (_infoPasien.total_sisapiutang > 0) {
                    info_piutang.style.display = "block";
                    $("#val-piutang").text(docoHelper.convertToRupiah(_infoPasien.total_sisapiutang));
                }

                var info_catatan = document.getElementById("info-catatan");
                if (_infoPasien.catatanpenting_pasien) {
                    info_catatan.style.display = "block";
                    $("#val-catatan").text(_infoPasien.catatanpenting_pasien);
                }

                $('.nama-pasien').text(_infoPasien.nama_pasien);
                $('.rm-pasien').text(_infoPasien.no_rekam_medik);
                $('.kelamin-pasien').text(': ' + (_infoPasien.jenis_kelamin ? _infoPasien.jenis_kelamin : '-'));
                $('.tempat-lahir-pasien').text(': ' + (_infoPasien.tempat_lahir ? _infoPasien.tempat_lahir : '-'));
                $('.tanggal-lahir-pasien').text(': ' + (_infoPasien.tanggal_lahir ? convertTanggalView(_infoPasien.tanggal_lahir) : '-'));
                $('.darah-pasien').text(': ' + (_infoPasien.golongan_darah ? _infoPasien.golongan_darah : '-'));
                $('.ibu-pasien').text(': ' + (_infoPasien.nama_ibu ? _infoPasien.nama_ibu : '-'));
                $('.tlp-pasien').text(': ' + (_infoPasien.no_telepon_pasien ? _infoPasien.no_telepon_pasien : '-'));
                $('.alamat-pasien').text(': ' + (_infoPasien.alamat_pasien ? _infoPasien.alamat_pasien : '-'));
                var _html = "";
                var countKunjungan = _response.kunjugan.length;
                if (countKunjungan === 0) {
                    $("#kunjungan").html('<p class="text-center">Pasien Belum Memiliki Riwayat</p>')
                    _formPendaftaran.tmpDataPj = {}
                } else {
                    let pasienRs = false
                    if (_formPendaftaran.params == 'penunjang') {
                        pasienRs = $("input[name='TipePasienForm[is_aps]']")[2]['checked']
                    }
                    $.each(_response.kunjugan, function (key, data) {
                        /** check last kunjungan */
                        if (key === 0) {
                            if (data.penanggungjawab_id !== 'undefined' && data.penanggungjawab_id != null && !pasienRs) {
                                _formPendaftaran.tmpDataPj = {
                                    idPj: data.penanggungjawab_id
                                }
                            } else {
                                _formPendaftaran.tmpDataPj = {}
                            }
                        }
                        var _clone = _formPendaftaran.historyKunjugan;
                        _clone.find('.history-pendaftaran-id').text(data.no_pendaftaran);
                        _clone.find('.history-instalasi').text(': ' + (data.instalasi_nama ? data.instalasi_nama : '-'));
                        _clone.find('.history-ruangan').text(': ' + (data.ruangan_nama ? data.ruangan_nama : '-'));
                        _clone.find('.history-dokter').text(': ' + (data.nama_pegawai ? data.nama_pegawai : '-'));
                        _clone.find('.history-tgl-pendaftaran').text(': ' + (data.tgl_pendaftaran ? convertDateByFormat(data.tgl_pendaftaran, 'd m Y - h:i:s') : '-'));
                        _clone.find('.history-keluar').text(': ' + (data.tglpasienpulang ? convertDateByFormat(data.tglpasienpulang, 'd m Y - h:i') : '-'));
                        _clone.find('.history-cara-keluar').text(': ' + (data.carakeluar_nama ? data.carakeluar_nama : '-'));
                        _html += _clone.html();
                    });
                    if (countKunjungan >= 3) {
                        var _action = $('.btn-detailss').attr('action');
                        $('.btn-detailss').show();
                        _html += '<hr><div align="center"><button type="button" class="btn btn-detailss btn-info btn-labeled btn-xs" action="/pendaftaran/daftar-igd/detail-kunjungan?no_rekam_medik=' + _infoPasien.no_rekam_medik + '" data-toggle="modal" data-target="#modal_backdrop" data-width="75%"><b><i class="fa fa-eye"></i></b>Detail Kunjungan</button></div>';
                    }
                    $('#kunjungan').html(_html);
                }

                //PJ Pasien Tera
                if (_infoPasien.penanggungjawabtera_nama !== 'undefined' && _infoPasien.penanggungjawabtera_nama != null) {
                    _formPendaftaran.tmpDataPj.penanggungjawabtera_nama = _infoPasien.penanggungjawabtera_nama
                    _formPendaftaran.tmpDataPj.penanggungjawabtera_hubungan = _infoPasien.penanggungjawabtera_hubungan
                    _formPendaftaran.tmpDataPj.penanggungjawabtera_alamat = _infoPasien.penanggungjawabtera_alamat
                }

                $('#form-parent-info').show();
                $('#form-parent').removeClass("col-md-12").addClass("col-md-9");

                if ($('input[name="TipePasienForm[is_multi_payer]"]:checked').val()) {
                    $('.tambah-cara-bayar').addClass('tambah-cara-bayar-pasien')
                }
                var url_edit_pasien = '/pendaftaran/informasi-pencarian-pasien/update?id='+_infoPasien.encrypted_pasien_id+'&is_close=true';
                $('#btn-edit-info-pasien').attr('data-target', url_edit_pasien);
            },
            error: function (err) {
                console.log(err);
            }
        });
    },
    getKarcis: function () {
        if([$('.selectDokter').val(), $('.selectRuangan').val()].includes('Loading ...')){
            return false;
        }
        var _params = {
            ruangan_id: $('.selectRuangan').val(),
            kp_id: $('.selectKp').val(),
            status: _formPendaftaran.tipePasien.tipe_pasien ? 0 : 1,
            penjamin_id: _formPendaftaran.tipePasien.penjamin_id,
            dokter_id: $('.selectDokter').val()
        };
        _params = $.param(_params);
        if (tbl) {
            tbl.clear();
        }
        tbl = $('#tbl-karcis').docoTabel({
            filter: true,
            destroy: true,
            paging: false,
            sorting: [[0, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl + 'pendaftaran/daftar-' + _formPendaftaran.params + '/get-karcis?' + _params,
            initComplete: function (row, data) {
                var api = this.api();
                $.each(api.rows().data(), function (key, val) {
                    _formPendaftaran.listTarif.push(val);
                });
                $('.check-aksi').on('click', function (event) {
                    var _this = $(this);
                    var isChecked = this.checked;
                    var rowData = api.rows($(this).closest("tr"));
                    var data = rowData.data()[0];
                    var allData = api.rows().data();
                    var cancelChecked = false;

                    if (data.is_konsultasi) {
                        if (isChecked) {
                            allData.each(function (value, index) {
                                if (value.is_konsultasi == true && value.checked == true && value.daftartindakan_id != data.daftartindakan_id) {
                                    cancelChecked = true;
                                }
                            });

                            if (cancelChecked) {
                                _this.parent().removeClass("checked");
                                _this.prop("checked", false);
                                rowData.data()[0].checked = false;
                                docoNotification("warning", "Peringatan!", "Tidak boleh memilih " + title_karcis + " konsultasi lebih dari satu.");
                            } else {
                                rowData.data()[0].checked = true;
                            }
                        } else {
                            rowData.data()[0].checked = false;
                        }
                    }

                    if (cancelChecked === false) {
                        var key = _this.attr('data-key');
                        if (_this.is(':checked')) {
                            total_tarif = parseInt(_formPendaftaran.totalTarif) + parseInt(_formPendaftaran.listTarif[key].harga_tariftindakan);
                        } else {
                            total_tarif = _formPendaftaran.totalTarif - (_formPendaftaran.listTarif[key].harga_tariftindakan);
                        }
                        _formPendaftaran.totalTarif = total_tarif;
                        $('.kolom-total-tarif').html('Rp. ' + docoHelper.convertToRupiah(total_tarif));
                    }
                });
            },
            drawCallback: () => {
                $('#tbl-karcis').find('.styled, .check-aksi input').uniform({
                    radioClass: 'choice'
                });
                bindCheckboxWithSpace($('#tbl-karcis'))
            },
            fnFooterCallback: function (row, data, start, end, display) {
                var api = this.api();
                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                            i : 0;
                };
                _formPendaftaran.totalTarif = api
                    .column(5)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                $(api.column(2).footer()).addClass('kolom-total-tarif').html('Rp. ' + docoHelper.convertToRupiah(_formPendaftaran.totalTarif));
            },
            columns: [
                {
                    title: 'No',
                    data: 'number',
                    searchable: false,
                    orderable: false
                },
                {
                    title: title_karcis,
                    data: 'daftartindakan_nama',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Konsultasi',
                    data: 'konsultasi',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Harga',
                    data: 'tmp_view',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '',
                    data: 'aksi',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'tmp_total',
                    visible: false,
                    searchable: false,
                    orderable: false
                }
            ],

        });
        $('.dataTables_filter').hide();
    },
    resetForm: function () {
        resetStep()
        // resetMultiPayer()
        var _currentLi = $('ul[role="tablist"] > li.current > a').attr("aria-controls");
        $('#' + _currentLi + ' select').val('').trigger('change');
        tableDaftarTerakhir.draw();
        // if (_formPendaftaran.params === 'igd') {
        //     tableDaftarTerakhirIgd.draw();
        // } else {
        //     tableDaftarTerakhir.draw();
        // }
        _formPendaftaran.resetAsuransi();
        $('.field-multicarabayarform-add_no_asuransi_1 span.input-group-addon').trigger('click');
        $('.field-multicarabayarform-add_no_asuransi_2 span.input-group-addon').trigger('click');
        $('.field-tipepasienform-no_asuransi').hide();
        $('.field-multicarabayarform-add_no_asuransi_1').hide();
        $('.field-multicarabayarform-add_no_asuransi_2').hide();
        $('#selectCarabayar').focus();
        $('input[name="no_antrian"]').val("");
        if (Object.keys(_formPendaftaran.pendaftaranOl).length) {
            var uri = window.location.toString();
            if (uri.indexOf("?") > 0) {
                var clean_uri = uri.substring(0, uri.indexOf("?"));
                window.history.replaceState({}, document.title, clean_uri);
            }
            _formPendaftaran.pendaftaranOl = {};
        }
    },
    resetMcu: function () {
        resetStep()
        $("#file-upload").val('');
        $("#label-file").empty('').html('Unggah Berkas');
        dataPasienMcu = []
    },
    formPenanggungJawab: function() {
        /** Re init */
        var _content = $('#form-pj-content');
        $("#pjpasienform-pj_jenis_identitas,#pjpasienform-pj_hubungan,#pjpasienform-pj_pengantar").select2()
        var $input_date = $('#pjpasienform-pj_tanggal_lahir').pickadate({
            editable: true,
            format: 'dd-mm-yyyy',
            formatSubmit: 'dd-mm-yyyy',
            selectMonths: true,
            selectYears: true,
            min: [1900, 01, 01],
            max: true,
            onClose: function () {
                $('.datepicker').focus();
            }
        });
        var picker_date = $input_date.pickadate('picker');
        $('#pj-date').parent().on('click', function (event) {
            if (picker_date.get('open')) {
                picker_date.close();
            } else {
                picker_date.open();
            }
            event.stopPropagation();
        });

        $('#pjpasienform-pj_tanggal_lahir').on('change', function () {
            var umur = '';
            if ($(this).val() != '') {
                const splitDate = $(this).val().split('-')
                const date = `${splitDate[2]}-${splitDate[1]}-${splitDate[0]}`
                var myDate = new Date(date);
                var today = new Date();
                if (myDate > today) {
                    $(this).pickadate('picker').set('select', new Date())
                    return true
                }
                umur = generateUmur($(this).val());
            }
            $('.usiapjtext').val(umur);
            $("input[name='PjpasienForm[pj_tanggal_lahir]_submit']").val($('#pjpasienform-pj_tanggal_lahir').val());
        })

        /**
         * Penambahan Suggestion apabila memilihi diri sendiri.
         */
        $("#pjpasienform-pj_pengantar").on("change", function () {
          let val = $(this).val();
          let noRm = _formPendaftaran?.tipePasien?.no_rekam_medik;

          if (noRm && val == 245) {
            $.ajax({
              type: "GET",
              url:
                "/pendaftaran/daftar-" +
                _formPendaftaran.params +
                "/get-info-pasien?id=&no_rm=" +
                noRm,
              dataType: "JSON",
              success: function (res) {
                var _caraBayar = $(".selectCarabayar").val();
                var _response = res.response;
                var _infoPasien = _response.info_pasien;

                var additionalPasien = _infoPasien?.additional_pasien;
                if (additionalPasien != undefined) {
                    additionalPasien = JSON.parse(additionalPasien);
                    if (additionalPasien.length > 0) {
                        let additionalDataObj = additionalPasien[0];
                        if (additionalDataObj) {
                            let jenisidentitas = additionalDataObj?.jenisidentitas;
                            let noIdentitas = additionalDataObj?.no_identitas_pasien;
                            $('#pjpasienform-pj_jenis_identitas').val(jenisidentitas).trigger('change')
                            $('#pjpasienform-pj_no_identitas').val(noIdentitas).trigger('change')
            
                        }
                    }
                } else {
                    $('#pjpasienform-pj_jenis_identitas').val(_infoPasien.jenisidentitas).trigger('change')
                    $('#pjpasienform-pj_no_identitas').val(_infoPasien.no_identitas_pasien).trigger('change')
                }

                var idBtnJk = $('#pj_jk_' + parseInt(_infoPasien.jeniskelamin))
                idBtnJk.prop('checked', true).trigger('click');

                $('#pjpasienform-pj_nama').val(_infoPasien.nama_pasien).trigger('change')
                $('#pjpasienform-pj_tempat_lahir').val(_infoPasien.tempat_lahir).trigger('change')
                $('#pjpasienform-pj_tanggal_lahir').val(convertDateByFormat(_infoPasien.tanggal_lahir, 'd-M-Y')).trigger('change')
                $('#pjpasienform-pj_no_telepon').val(_infoPasien.no_telepon_pasien).trigger('change')
                $('#pjpasienform-pj_alamat').val(_infoPasien.alamat_pasien).trigger('change')
                $('#pjpasienform-pj_hubungan').val('').trigger('change')
              },
              error: function (err) {
                console.log(err);
              },
            });
          } else {
            $('#pjpasienform-pj_jenis_identitas').val('').trigger('change')
            $('#pjpasienform-pj_no_identitas').val('').trigger('change')
            $('#pjpasienform-pj_nama').val('').trigger('change')
            $('#pjpasienform-pj_tempat_lahir').val('').trigger('change')
            $('#pjpasienform-pj_tanggal_lahir').val('').trigger('change')
            $('#pjpasienform-pj_no_telepon').val('').trigger('change')
            $('#pjpasienform-pj_alamat').val('').trigger('change')
            $('#pjpasienform-pj_hubungan').val('').trigger('change')

            let jenisKelamin = [15, 16, 728]
            jenisKelamin.map((jk) => {
                let jkCheck = $('#pj_jk_' + jk)
                if (jkCheck.parent().hasClass('checked')) {
                    jkCheck.prop('checked', false).trigger('click').parent().removeClass('checked');
                }
            })
          }
        });

        $('#form-pj-content').find('.styled, .multiselect-container input').uniform({
            radioClass: 'choice'
        });
        $('select').removeAttr('tabindex');
        var _parent = $('fieldset[aria-hidden="false"]');
        // _parent.find('[data-urutan="1"]').focus();
    },
    defaultKunjunganBpjs: function(){
        if(_formPendaftaran.tmpBpjs.poliTujuan){
            $('#ruangan_id option').each(function (id, val) {
                if (String($(val).data('kode-bpjs')) == _formPendaftaran.tmpBpjs.poliTujuan) {
                    $('#ruangan_id').val($(val).attr('value')).trigger('change').trigger('depdrop:change');
                }
            })
        }
        if ($('#kunjunganform-dokter_id').data('depdrop')) {
            $('#kunjunganform-dokter_id').on('depdrop:afterChange', function (event, id, value) {
                var dokter = $('#kunjunganform-dokter_id').depdrop('getAjaxResults').output;
                $('#kunjunganform-dokter_id option').each(function(){
                    if ($(this).data('jadwaldokter_id') == _penOl.all.jadwaldokter_id) {
                        $(this).prop('selected', true).change()
                    }
                });
                // $.each(dokter, function(val,data){
                //     if(_formPendaftaran.tmpBpjs.dpjpId == data.kode_bpjs){
                //         $('#kunjunganform-dokter_id').val(data.id).trigger('change').trigger('depdrop:change');
                // })
            });
        }
    }
};

function validasiKunjungan(data) {
    let validate = true

    if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined') {
        validate = false
    }

    if ($("#tipepasienform-is_bbl").is(':checked')) {
        validate = false;
    }

    if (validate) {
        $.ajax({
            type: 'POST',
            url: '/pendaftaran/end-point/validate',
            data: {
                noRm: data,
                params: _formPendaftaran.params
            },
            dataType: 'JSON',
            success: function (res) {
                if (typeof res.text != 'undefined') {
                    docoNotification('warning', 'Perhatian!', res.text)
                } else {
                    stateNextStep('next')
                }

                return true;
            },
            error: function (res) {
                stateNextStep('stop')
                docoNotification('error', 'Perhatian!', res.responseJSON.text)
                return false;
            }
        });
    } else {
        if ($("#tipepasienform-is_bbl").is(':checked')) {
            return 'Pendaftaran Bayi'
        } else {
            return 'Pendaftaran Konsul Poli'
        }
    }
}

const stateNextStep = function (state) {
    let btnNxt = $(".wizard").find('a[href="#next"]');
    let btnFnsh = $(".wizard").find('a[href="#finish"]');
    btnNxt.attr("href", '#next');
    btnFnsh.attr("href", '#finish');

    if (state == 'next') {
        btnNxt.parent().removeClass("hidden")
        btnFnsh.parent().removeClass("hidden")
    } else if (state == 'stop') {
        btnNxt.parent().addClass("hidden")
        btnFnsh.parent().addClass("hidden")
    }
}

const stateCaraBayar = function (carabayar_group, multi = false, scd = false) {
    let _isBpjs = false;
    let main_carabayar_group = $('.selectCarabayar').find(':selected').attr('data-id');
    let add_carabayar_group_1 = $('#addSelectCarabayar2').find(':selected').attr('data-id');
    let add_carabayar_group_2 = $('#addSelectCarabayar3').find(':selected').attr('data-id');

    if (main_carabayar_group == docoHelper.groupBPJS || add_carabayar_group_1 == docoHelper.groupBPJS || add_carabayar_group_2 == docoHelper.groupBPJS) {
        _isBpjs = true
    }

    if (multi) {
        let inputAsuCls = $('.field-multicarabayarform-add_no_asuransi_1')
        // let inputRjkCls =  $('.field-firstAsalrujukan_id')
        let inputRmCls = $('.field-no_rekam_medik')
        let hintNoteNoasuId = $("#first-note-asuransi")
        let hintNoasuId = $("#multicarabayarform-add_no_asuransi_1")

        if (scd != false) {
            inputAsuCls = $('.field-multicarabayarform-add_no_asuransi_2')
            // inputRjkCls =  $('.field-secondAsalrujukan_id')
            inputRmCls = $('.field-no_rekam_medik')
            hintNoteNoasuId = $("#second-note-asuransi")
            hintNoasuId = $("#multicarabayarform-add_no_asuransi_2")
        }

        if (_isBpjs) {
            if (carabayar_group == docoHelper.groupUmum) {
                inputAsuCls.hide()
            } else if (carabayar_group == docoHelper.groupJaminan) {
                inputAsuCls.show()
            } else if (carabayar_group == docoHelper.groupBPJS) {
                inputAsuCls.hide()
            }

            if (_formPendaftaran.params == 'penunjang') {
                $('#jenis_rujukan').addClass('bpjs-penunjang')
            } else {
                $('#jenis_rujukan').removeClass('bpjs-penunjang')
            }
            $('#form-bpjs').show();
            $('#jenis_pelayanan').val(2).trigger("change");
            inputRmCls.hide();
        } else {
            if (carabayar_group == docoHelper.groupUmum) {
                inputAsuCls.hide()
                // inputRjkCls.show();
                $('#form-bpjs').hide();
                inputRmCls.show();
            } else if (carabayar_group == docoHelper.groupJaminan) {
                inputAsuCls.show()
                $('#form-bpjs').hide();
                // inputRjkCls.show();
                inputRmCls.show();
            } else if (carabayar_group == docoHelper.groupBPJS) {
                if (_formPendaftaran.params == 'penunjang') {
                    $('#jenis_rujukan').addClass('bpjs-penunjang')
                } else {
                    $('#jenis_rujukan').removeClass('bpjs-penunjang')
                }
                $('#form-bpjs').show();
                $('#jenis_pelayanan').val(2).trigger("change");
                // inputRjkCls.hide();
                inputAsuCls.hide();
                inputRmCls.hide();
            }
            hintNoteNoasuId.html("");
            hintNoasuId.val("").trigger("change");
        }
    } else {
        if (_isBpjs) {
            if (carabayar_group == docoHelper.groupUmum) {
                $('.field-tipepasienform-no_asuransi').hide();
                $('.field-asalrujukan_id').show();
            } else if (carabayar_group == docoHelper.groupJaminan) {
                $('.field-tipepasienform-no_asuransi').show();
                $('.field-asalrujukan_id').show();
            } else if (carabayar_group == docoHelper.groupBPJS) {
                $('.field-tipepasienform-no_asuransi').hide();
                $('.field-asalrujukan_id').hide();
            }

            if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined' && _formPendaftaran.pendaftaranOl.status_janji == true) {

            } else {
                if (_formPendaftaran.params == 'penunjang') {
                    $('#jenis_rujukan').addClass('bpjs-penunjang')
                } else {
                    $('#jenis_rujukan').removeClass('bpjs-penunjang')
                }
                $('#form-bpjs').show();
                $('#jenis_pelayanan').val(2).trigger("change");
                // $('.field-asalrujukan_id').hide();
                $('.field-no_rekam_medik').hide();
            }
        } else {
            if (carabayar_group == docoHelper.groupUmum) {
                $('.field-no_rekam_medik').show();
                $('.field-tipepasienform-no_asuransi').hide();
                $('.field-asalrujukan_id').show();
                $('#form-bpjs').hide();
            } else if (carabayar_group == docoHelper.groupJaminan) {
                $('.field-tipepasienform-no_asuransi').show();
                $('.field-no_rekam_medik').show();
                $('.field-asalrujukan_id').show();
                $('#form-bpjs').hide();
            } else if (carabayar_group == docoHelper.groupBPJS) {
                if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined' && _formPendaftaran.pendaftaranOl.status_janji == true) {

                } else {
                    if (_formPendaftaran.params == 'penunjang') {
                        $('#jenis_rujukan').addClass('bpjs-penunjang')
                    } else {
                        $('#jenis_rujukan').removeClass('bpjs-penunjang')
                    }
                    $('#form-bpjs').show();
                }
                $('#jenis_pelayanan').val(2).trigger("change");
                $('.field-asalrujukan_id').hide();
                $('.field-tipepasienform-no_asuransi').hide();
                $('.field-no_rekam_medik').hide();
            } else {
                $('#form-bpjs').hide();
                $('.field-tipepasienform-no_asuransi').hide();
                $('.field-no_rekam_medik').hide();
                $('.field-asalrujukan_id').show();
            }
            $('input[name="chk-statuspasien"]').prop("checked", true);
            $(".field-no_rekam_medik").addClass('required');
            //$('#no_rekam_medik').val('').trigger('change');
            $("#note-asuransi").html("");
            $("#tipepasienform-no_asuransi").val("").trigger("change");
        }
    }
}

const resetStep = function () {
    var stepsLength = $('ul[role="tablist"] > li:not(.first)').length;
    for (x = stepsLength; x > 0; x--) {
        $('.steps-basic').steps('previous');
    }
    setTimeout(function () {
        for (x = stepsLength; x > 0; x--) {
            $('.steps-basic').steps("remove", x);
        }
    }, 300);

    $('#form-parent-info').hide();
    $('#form-parent').removeClass("col-md-9").addClass("col-md-12");
}

const generateFormKunjungan = function () {
    _formPendaftaran.formKunjugan();
    $('select').removeAttr('tabindex');
    $('.steps-basic').steps("next");
}

$(document).on("click", "#unduh-template", function (e) {
    e.preventDefault();
    window.open(baseUrl + "pendaftaran/daftar/download-template");
    return false;
});

$("#file-upload").change(function (e) {
    e.preventDefault();
    let formData = new FormData();
    var _groupCaraBayar = $('.selectCarabayar').find(':selected').attr('data-id');

    formData.append("TipePasienForm[upload_file]", $("#file-upload")[0].files[0]);
    $.ajax({
        type: "post",
        dataType: false,
        cache: false,
        contentType: false,
        processData: false,
        url: "/pendaftaran/daftar/upload-template?group_carabayar=" + _groupCaraBayar,
        data: formData,
        success: function (res) {
            $("#file-upload").val('');
            var data = res.response.data;
            if (res.response.status == 422) {
                docoNotification("warning", i18next.t("Proses Gagal"), i18next.t("File gagal di upload"));
            } else {
                _formPendaftaran.resetMcu()
                let nama_file = res.response.file;
                let complete = res.response.info.dataComplete
                let incomplete = res.response.info.dataIncomplete
                let dobleRm = res.response.info.dataDouble
                let multipleRm = res.response.info.dataMultipleRM
                dataPasienMcu = data
                _formPendaftaran.tmpDataMcu.dataComplete = complete
                _formPendaftaran.tmpDataMcu.dataIncomplete = incomplete
                _formPendaftaran.tmpDataMcu.dataFile = nama_file
                _formPendaftaran.tmpDataMcu.dataDoubleRm = dobleRm
                _formPendaftaran.tmpDataMcu.dataMultipleRm = multipleRm
                $("#label-file").html(nama_file);
                $(".lihat_file").attr('data-file', nama_file);
                docoNotification("success", i18next.t("Proses Berhasil"), i18next.t("File Berhasil di upload."));
            }
        },
        error: function (res) {
            docoNotification("warning", i18next.t("Proses Gagal"), i18next.t("File yang di upload tidak sesuai dengan format contoh, xls dan xlsx"));
        }
    });
});

var inject_pasien = false;
function setValueReservasi() {
    if ((typeof (_penOl) !== "undefined")) {
        if (_penOl.status_pasien != undefined) {
            if (_penOl.status_pasien != '' && _penOl.status_pasien == 310 && inject_pasien == false) { //pasien baru
                setTimeout(function () {
                    let tgl_lahir_ol = new Date(_penOl.all.tanggal_lahir_ol);
                    let date_tgl_lahir_ol = tgl_lahir_ol.getDate();
                    if (date_tgl_lahir_ol <= 10) {
                        date_tgl_lahir_ol = "0" + date_tgl_lahir_ol.toString();
                    }

                    let date_bln_lahir = tgl_lahir_ol.getMonth() + 1;
                    if (date_bln_lahir <= 10) {
                        date_bln_lahir = "0" + date_bln_lahir.toString();
                    }

                    let formatted_date = date_tgl_lahir_ol + "-" + date_bln_lahir + "-" + tgl_lahir_ol.getFullYear();
                    var umur = generateUmur(formatted_date);
                    $('#frm-pasien-jenisidentitas').val(_penOl.all.jenisidentitas_id_ol).trigger('change');
                    $('#frm-pasien-no_identitas_pasien').val(_penOl.all.no_identitas_pasien_ol);
                    $('#frm-pasien-namadepan').val(_penOl.all.namadepan_ol).trigger('change');
                    $('#frm-pasien-nama_pasien').val(_penOl.all.nama_pasien_ol);
                    $('#frm-pasien-tempat_lahir').val(_penOl.all.tempat_lahir_ol);
                    $('#frm-pasien-tanggal_lahir').val(formatted_date);
                    $('#frm-pasien-umur').val(umur);
                    $("input[name='PasienForm[tanggal_lahir]_submit']").val($('#frm-pasien-tanggal_lahir').val());
                    $('#frm-pasien-no_telepon_pasien').val(_penOl.all.no_telepon_pasien_ol);
                    $('#frm-pasien-alamat_pasien').val(_penOl.all.alamat_pasien_ol);
                    $("input[name='PasienForm[jeniskelamin]'][value=" + _penOl.all.jeniskelamin_ol + "]").prop('checked', true).trigger('change');
                    $(":radio[value=" + _penOl.all.jeniskelamin_ol + "]").parent().addClass('checked');
                    inject_pasien = true;
                }, 1000);
            }
        }
    }
}

function bindingRegionPdftrn(urlApi, objectElement) {
    // object element must be 4 element -> province,city,district,village
    const { province, city, district, village } = objectElement
    const defaultValue = [
        {
            id: '',
            text: '--Pilih--'
        }
    ]

    existVillage = typeof village !== 'undefined'
    if (province.val() !== '' && province.val() !== null) {
        $.ajax({
            url: urlApi,
            method: 'GET',
            data: {
                type: 'city',
                foreignId: province.val()
            },
            success: (res) => {
                const { data } = res
                refreshOptionSelect2(city, data)
                city.prop('disabled', false)
                // city.val(kabupaten_id).trigger('change')
                resetDropdownRegion(district)
                resetDropdownRegion(village)
            }
        })
    } else {
        city.val('').trigger('change')
        city.prop('disabled', true)
    }

    city.bind('change', () => {
        if (city.val() !== '' && city.val() !== null) {
            $.ajax({
                url: urlApi,
                method: 'GET',
                data: {
                    type: 'district',
                    foreignId: city.val()
                },
                success: (res) => {
                    const { data } = res
                    refreshOptionSelect2(district, data)
                    district.prop('disabled', false)
                    resetDropdownRegion(village)
                }
            })
        } else {
            district.val('').trigger('change')
            district.prop('disabled', true)
        }
    })
    if (existVillage) {
      district.bind('change', () => {
            if (district.val() !== '' && district.val() !== null) {
                $.ajax({
                    url: urlApi,
                    method: 'GET',
                    data: {
                        type: 'village',
                        foreignId: district.val()
                    },
                    success: (res) => {
                        const { data } = res
                        refreshOptionSelect2(village, data)
                        village.prop('disabled', false)
                    }
                })
            } else {
                village.val('').trigger('change')
                village.prop('disabled', true)
            }
        })
        village.bind('change', () => {
            if (village.val() !== '' && village.val() !== null) {
                village.prop('disabled', false)
            }
        })
    }
    if (province.val() == null || province.val() == '') {
        city.prop('disabled', true)
    }
    if (city.val() == null || city.val() == '') {
        district.prop('disabled', true)
    }
    if ((district.val() == null || district.val() == '') && existVillage) {
        village.prop('disabled', true)
    }
}

$(document).on('select2:close', '#frm-pasien-propinsi_id', function (e) {
e.preventDefault()
    bindingRegionPdftrn('/pendaftaran/end-point/region-list', {
        province: $("#frm-pasien-propinsi_id"),
        city: $("#kabupatenForm"),
        district: $("#frm-pasien-kecamatan_id"),
        village: $("#frm-pasien-kelurahan_id"),
    })
})

function getPj(idPj = null, idPasien = null) {
    $.ajax({
        type: 'GET',
        url: '/pendaftaran/end-point/get-penanggung-jawab?idPj=' + idPj + '&pasienId=' + idPasien,
        dataType: 'JSON',
        success: function (data) {
            if (typeof data != 'undefined' && data != null) {
                let idBtnJk = $('#pj_jk_' + parseInt(data.penanggungjawab_jeniskelamin))
                $('#pjpasienform-pj_pengantar').val(data.pengantar).trigger('change')
                $('#pjpasienform-pj_nama').val(data.penanggungjawab_nama).trigger('change')
                // $("#pjpasienform-pj_jk").find(`input[value=${data.penanggungjawab_jeniskelamin}]`).trigger('click')
                idBtnJk.prop('checked', true).trigger('click');
                $('#pjpasienform-pj_jenis_identitas').val(data.jenisidentitas).trigger('change')
                $('#pjpasienform-pj_no_identitas').val(data.no_identitas).trigger('change')
                $('#pjpasienform-pj_hubungan').val(data.hubungankeluarga).trigger('change')
                $('#pjpasienform-pj_tempat_lahir').val(data.penanggungjawab_tempatlahir).trigger('change')
                $('#pjpasienform-pj_tanggal_lahir').val(convertDateByFormat(data.penanggungjawab_tgllahir, 'd-M-Y')).trigger('change')
                $('#pjpasienform-pj_no_telepon').val(data.penanggungjawab_notelp).trigger('change')
                $('#pjpasienform-pj_alamat').val(data.penanggungjawab_alamat).trigger('change')
            }
        },
        error: function (res) {
            console.log(res)
        }
    });
}

function generatePj(_formPendaftaran) {
    setTimeout(function () {
        if (Object.keys(_formPendaftaran.tmpDataPj).length) {
            // $("input[name='KunjunganForm[is_pj]']").closest('span').addClass('checked')
            // $("input[name='KunjunganForm[is_pj]']").prop("checked", true).trigger('change')
            if (_formPendaftaran.tmpDataPj.idPj != null) {
                getPj(_formPendaftaran.tmpDataPj.idPj)
            } else {
                $('#pjpasienform-pj_nama').val(_formPendaftaran.tmpDataPj.penanggungjawabtera_nama).trigger('change')
                $('#pjpasienform-pj_alamat').val(_formPendaftaran.tmpDataPj.penanggungjawabtera_alamat).trigger('change')
            }
        } else {
            let pasienId = $("#pasien_id_hidden").val();
            getPj(null,pasienId)
        }
    }, 1);
}

$(document).on('click', '.tambah-cara-bayar', function (e) {
    $(".form-multi-carabayar-second").show()
    $(this).prop('disabled', true)
    $(".field-addSelectCarabayar3").addClass("required")
    $(".field-add_penjamin_id_2").addClass("required")
    // $(".field-secondAsalrujukan_id").addClass("required")
    _formPendaftaran.listTmp.is_add_payer = true
})

$(document).on('click', '.hapus-cara-bayar', function (e) {
    resetMultiPayer()
})

function resetMultiPayer() {
    if ($('#addSelectCarabayar3').find(':selected').attr('data-id') == docoHelper.groupBPJS) {
        $('#form-bpjs').hide()
    }
    $(".form-multi-carabayar-second").hide()
    $('.tambah-cara-bayar').prop('disabled', false)
    $("#second-note-asuransi").html("");
    $("#multicarabayarform-add_no_asuransi_2").trigger("change");
    $('#addSelectCarabayar3').val("").trigger('change')
    $('#add_penjamin_id_2').val("").trigger('change')
    // $('.field-secondAsalrujukan_id').hide()
    $('.field-multicarabayarform-add_no_asuransi_2').hide()
    packDeleteGroup2()
    removeStepList()
    // stateCaraBayar(docoHelper.groupUmum)
}

const packDeleteGroup2 = function () {
    delete _formPendaftaran.listTmp.is_add_payer
    delete _formPendaftaran.listTmp[secondCbG]
    delete _formPendaftaran.listTmp.add_carabayar_id_2
}

$(document).on('change', '#tipepasienform-is_multi_payer', function (e) {
    e.preventDefault()
    _formPendaftaran.resetForm();
    $('.field-multicarabayarform-add_no_asuransi_1').hide();

    if ($(this).prop("checked")) {
        $('.form-multi-carabayar').show()
    } else {
        $('.form-multi-carabayar').hide()
        resetMultiPayer()
    }
})

$(document).on('change', '#addSelectCarabayar2', function () {
    let carabayar_group = $('#addSelectCarabayar2').find(':selected').attr('data-id');
    stateCaraBayar(carabayar_group, true)
    $("#first-note-asuransi").html("");
    $("#multicarabayarform-add_no_asuransi_1").val("").trigger("change");
});

$(document).on('change', '#addSelectCarabayar3', function () {
    let carabayar_group = $('#addSelectCarabayar3').find(':selected').attr('data-id');
    stateCaraBayar(carabayar_group, true, true)
    $("#second-note-asuransi").html("");
    $("#multicarabayarform-add_no_asuransi_2").val("").trigger("change");
});

$(document).on('click', '.field-multicarabayarform-add_no_asuransi_1 span.input-group-addon', function (event) {
    event.preventDefault();
    // _formPendaftaran.tipePasien.no_asuransi = null;
    _formPendaftaran.firstTmpAsuransi = {
        kelastanggunganasuransi_id: null,
        namapemilikasuransi: null,
        namaperusahaan: null,
        no_asuransi: null,
        nomorpokokperusahaan: null,
        pasien_id: null,
        no_rekam_medik: null,
        prevPasien: _formPendaftaran.firstTmpAsuransi.prevPasien
    };
    $('#multicarabayarform-add_no_asuransi_1').val('');
    $('#multicarabayarform-add_no_asuransi_1').prop('readonly', false);
    $('.field-multicarabayarform-add_no_asuransi_1').find("#first-note-asuransi").html("");
});

$(document).on('click', '.field-multicarabayarform-add_no_asuransi_2 span.input-group-addon', function (event) {
    event.preventDefault();
    // _formPendaftaran.tipePasien.no_asuransi = null;
    _formPendaftaran.secondTmpAsuransi = {
        kelastanggunganasuransi_id: null,
        namapemilikasuransi: null,
        namaperusahaan: null,
        no_asuransi: null,
        nomorpokokperusahaan: null,
        pasien_id: null,
        no_rekam_medik: null,
        prevPasien: _formPendaftaran.secondTmpAsuransi.prevPasien
    };
    $('#multicarabayarform-add_no_asuransi_2').val('');
    $('#multicarabayarform-add_no_asuransi_2').prop('readonly', false);
    $('.field-multicarabayarform-add_no_asuransi_2').find("#second-note-asuransi").html("");
});


// validasi tampilan after submit tipe pasien
const generateStep = function (_groupCaraBayar, _noRm, _asalRujukan, _jenisPendaftaran, _pasienId = null, _findPasien, _bpjs, data, isKonsul, _pencarianBpjs, _isGenerateKunjungan = false) {
    if (_groupCaraBayar == docoHelper.groupJaminan && _jenisPendaftaran != 0) {
        _formPendaftaran.formAsuransi();

        // _asalRujukan = 0 >> jenis pendaftaran pasien rs
        if (_asalRujukan != 1 && _asalRujukan != "0") {
            _formPendaftaran.formRujukan();
        }

        if (/*!_formPendaftaran.tmpAsuransi.pasien_id*/_pasienId == null) {
            if (Object.keys(_formPendaftaran.pendaftaranOl).length == 0) {
                _formPendaftaran.formPasien();
            }
        }

        // pengecekan kondisi untuk reservasi online
        if ((typeof (_penOl) !== "undefined")) {
            if (_penOl.status_pasien != undefined) {
                if (_penOl.status_pasien != '' && _penOl.status_pasien == 310) {
                    _formPendaftaran.formPasien();
                }
            }
        }
    } else if (_groupCaraBayar == docoHelper.groupBPJS && _jenisPendaftaran != 0) {
        generateStepBpjs(_groupCaraBayar, _bpjs, _pencarianBpjs, data, _asalRujukan, _noRm, isKonsul, true)
    } else if (_groupCaraBayar == docoHelper.groupUmum) {
        _pasienId = "";
        // _noRmBpjs = _noRm;
        if (_asalRujukan != 1 && _asalRujukan != "0") {
            _formPendaftaran.formRujukan();
        }

        if (!_formPendaftaran.tipePasien.tipe_pasien) {
            _formPendaftaran.formPasien();
        }
        _formPendaftaran.resetAsuransi();
    }

    if ($("#tipepasienform-is_bbl").is(':checked')) {
        _formPendaftaran.formPasien();
      }

    /** kondisi pasien baru atau pasien lama */
    if ((_formPendaftaran.tipePasien.tipe_pasien && _noRm) || _findPasien) {
        if (Object.keys(_formPendaftaran.pendaftaranOl).length == 0) {
            _formPendaftaran.getInfoPasien(_pasienId, _noRm);
            if (_formPendaftaran.params == 'penunjang') {
                let pasienRs = $("input[name='TipePasienForm[is_aps]']")[2]['checked']
                if (!pasienRs) {
                    // generatePj(_formPendaftaran)
                    generateFormPenanggungJawab(false)
                }
            } else {
                // generatePj(_formPendaftaran)
                generateFormPenanggungJawab(false)
            }
        } else if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined') {
            _formPendaftaran.getInfoPasien(_pasienId, _noRm);
            if (_formPendaftaran.params == 'penunjang') {
                let pasienRs = $("input[name='TipePasienForm[is_aps]']")[2]['checked']
                if (!pasienRs) {
                    // generatePj(_formPendaftaran)
                    generateFormPenanggungJawab(false)
                }
            } else {
                generateFormPenanggungJawab(false)
                // generatePj(_formPendaftaran)
            }
        }
    } else {
        generateFormPenanggungJawab(true);
        if (Object.keys(_formPendaftaran.pendaftaranOl).length == 0) {
            $('#form-parent-info').hide();
            $('#form-parent').removeClass("col-md-9").addClass("col-md-12");
        }
    }

    if (_isGenerateKunjungan) {
        generateFormKunjungan()
    }
}

const generateStepBpjs = function (_groupCaraBayar, _bpjs, _pencarianBpjs, data, _asalRujukan, _noRm, isKonsul, notMultipayer = false) {
    _formPendaftaran.tmpBpjs = _bpjs;
    let _statRujukan = false;
    _formPendaftaran.dataReturnBpjs = data.response.pasien_bpjs;
    if (!isKonsul) {
        if (typeof _formPendaftaran.dataReturnBpjs.post_ranap != null) {
            var post_ranap = _formPendaftaran.dataReturnBpjs.post_ranap;
            if (post_ranap) {
                if (typeof _formPendaftaran.dataReturnBpjs.data_post_ranap != 'undefined') {
                    var data_post_ranap = _formPendaftaran.dataReturnBpjs.data_post_ranap;
                    _formPendaftaran.dataPostRanap.asal_rujukan = 2;
                    _formPendaftaran.dataPostRanap.no_rujukan = data_post_ranap.noSep;
                    _formPendaftaran.dataPostRanap.ppkrujukan_kode = data_post_ranap.ppkPelayanan_kode;
                    _formPendaftaran.dataPostRanap.ppkrujukan_nama = data_post_ranap.ppkPelayanan_nama;
                }
            }
        }

        if (typeof data.response.pasien_bpjs.peserta != 'undefined') {
            _formPendaftaran.dataBpjs = data.response.pasien_bpjs.peserta;
            if (typeof _formPendaftaran.dataBpjs.statusPeserta != 'undefined') {
                if (_formPendaftaran.dataBpjs.statusPeserta.kode > 0) {
                    docoNotification('warning', 'Proses BPJS Gagal !', _formPendaftaran.dataBpjs.statusPeserta.keterangan)
                    return false;
                }
            }
            if (typeof _formPendaftaran.dataReturnBpjs.messages != 'undefined') {
                _formPendaftaran.errorBpjs(_bpjs);
                _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm);
                return true;
            }
        } else { // rujukan manual / IGD

        }
        if (typeof _formPendaftaran.dataReturnBpjs.messages != 'undefined') {
            _formPendaftaran.errorBpjs(_bpjs);
            _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm);
            return true;
        }


        if (_formPendaftaran.dataReturnBpjs.pasien_baru === false) {
            _findPasien = true;
            if (_formPendaftaran.dataReturnBpjs.pasien_sesuai === false) {
                setTimeout(function () {
                    var _title = `<b>Peringatan !</b><br>
                        Nama Pasien yang diinputkan berbeda dengan Nama BPJS<br>
                        Nama Pasien BPJS : <strong>${_formPendaftaran.dataReturnBpjs.peserta.nama}</strong><br>
                        Pasien yang terdaftar: <strong>${_formPendaftaran.dataReturnBpjs.nama_pasien}</strong><br>
                        Apakah Anda yakin akan melanjutkan proses?`;
                    confirmationDialog(_title, function (cond) {
                        if (cond) {
                            _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm)
                        }
                        else {
                            removeStepList()
                            // _formPendaftaran.findRmBpjs()
                        }
                    })
                }, 100)
                return true
            }
        } else {
            setTimeout(function () {
                confirmationDialog("Data pasien tidak ditemukan, Apakah anda ingin melanjutkan dengan pasien baru ?", function (cond) {
                    if (cond) {
                        _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm)
                    } else {
                        _formPendaftaran.findRmBpjs()
                    }
                })
            }, 100)
            return true
        }

        _formPendaftaran.formBpjs(_pencarianBpjs);
        if (notMultipayer) {
            _formPendaftaran.resetAsuransi();
        }
        if (typeof _formPendaftaran.dataBpjs.mr != 'undefined' && _formPendaftaran.dataBpjs.mr != null) {
            _findPasien = true;
            if(typeof _formPendaftaran.dataReturnBpjs.no_rekam_medik != 'undefined') {
                _noRm =_formPendaftaran.dataReturnBpjs.no_rekam_medik;
            } else {
                _noRm = _formPendaftaran.dataBpjs.mr.noMR;
            }
            _pasienId = "";
        } else if (typeof _formPendaftaran.dataReturnBpjs.no_rekam_medik != 'undefined') {
            _findPasien = true;
            _noRm =_formPendaftaran.dataReturnBpjs.no_rekam_medik;
        } else {
            _formPendaftaran.formPasien();
        }

        if(_findPasien == true) {
            _formPendaftaran.getInfoPasien("null", _noRm);
        }

        if ($('input[name="TipePasienForm[is_multi_payer]"]:checked').val()) {
            if (_formPendaftaran.listTmp[firstCbG] == docoHelper.groupJaminan) {
                _formPendaftaran.formFirstAsuransi(_formPendaftaran.firstTmpAsuransi);
            }

            if (_formPendaftaran.listTmp.is_add_payer != 'undefined' && _formPendaftaran.listTmp.is_add_payer == true) {
                if (_formPendaftaran.listTmp[secondCbG] == docoHelper.groupJaminan) {
                    _formPendaftaran.formSecondAsuransi(_formPendaftaran.secondTmpAsuransi);
                }
            }
        }

        generateFormKunjungan()
    }
}

const validationTipePasien = function () {
    removeStepList()
    $('a[href="#finish"]').trigger('click');
}

const removeStepList = function () {
    $.each($('ul[role="tablist"] > li:not(.first)'), function () {
        $('.steps-basic').steps("remove", 1);
    });
}

const conditionResetStepBpjs = function () {
    let _execute = false;
    /** Input BPJS */
    var _jenisPencarian = $('input[name="BpjsNewForm[jenis_rujukan]"]:checked').val();
    var _jenisPelayanan = $('select[name="BpjsNewForm[jenis_pelayanan]"]').val();
    var _jenisKartu = $('input[name="BpjsNewForm[jenis_kartu]"]:checked').val();
    var _noKartu = $('input[name="BpjsNewForm[no_kartu]"]').val();

    /** BPJS Rujukan */
    var _asalRujukanBpjs = $('select[name="BpjsNewForm[asal_rujukan]"]').val();
    var _noRujukan = $('input[name="BpjsNewForm[no_rujukan_f]"]').val();

    if (_formPendaftaran.tmpBpjs.jenis_pencarian == 2) {
        if (_formPendaftaran.tmpBpjs.jenis_pelayanan != _jenisPelayanan
            || _formPendaftaran.tmpBpjs.jenis_kartu != _jenisKartu
            || _formPendaftaran.tmpBpjs.no_kartu != _noKartu
            || _formPendaftaran.tmpBpjs.jenis_pencarian != _jenisPencarian) {
            _execute = true;
        }
    } else {
        if (_formPendaftaran.tmpBpjs.asal_rujukan != _asalRujukanBpjs
            || _formPendaftaran.tmpBpjs.no_rujukan_f != _noRujukan
            || _formPendaftaran.tmpBpjs.jenis_pencarian != _jenisPencarian) {
            _execute = true;
        }
    }
    if (_execute) {
        validationTipePasien()
        return false;
    } else {
        return true;
    }
}

function modalTujuanKunjunganBpjs(url) {
    $('#modal_backdrop').modal('hide');

    setTimeout(function () {
        $('.btn-tujuan-kunjungan').attr('action', url)
        $('.btn-tujuan-kunjungan').trigger('click');
    }, 1000)
}

function generateFormPenanggungJawab(isNew = true) {
    let _clonePj = _formPendaftaran.formInputPj;
    if(isNew) {
        $('#pasienform-is_pj').on("change", function () {
            if (this.checked) {
                let _totalStep = $('ul[role="tablist"] > li').length;
                let formPjIdx = _totalStep - 1

                $('.steps-basic').steps("insert", formPjIdx, {
                    title: "Penanggung Jawab",
                    content: _clonePj.html()
                });
                _formPendaftaran.formPenanggungJawab();
            } else {
                let _totalStep = $('ul[role="tablist"] > li').length;
                let formPjIdx = _totalStep - 2
                $('.steps-basic').steps("remove", formPjIdx);
            }
        });
    } else {
        $('.steps-basic').steps("add", {
            title: "Penanggung Jawab",
            content: _clonePj.html()
        });
        _formPendaftaran.formPenanggungJawab();
        generatePj(_formPendaftaran)
    }
}

// form daftar bayi
function parseDataBayiToForm(idKelahiran) {
    getProfile()
    $("#form-pasien-content").children('.col-md-6').hide();
    $(".div-pasien").show();
    $("#tableBayi").find('.selected-row').removeClass('selected-row')
    $("#tableBayi").find(`tr[data-id="${idKelahiran}"]`).addClass('selected-row')
    $("#kelahiranIdInput").val(idKelahiran)
    const selectedBayi = dataBayi[idKelahiran]
    let goldrhby = (selectedBayi.goldarah_by !== null) ? lookGoldrh(selectedBayi.goldarah_by.toLowerCase()) : lookGoldrh(0);
    jk = selectedBayi.jeniskelamin_id_bayi
    $("#pendaftaranIbuInput").val(selectedBayi.pendaftaran_id)
    $("#frm-pasien-jeniskelamin").find(`input[value=${selectedBayi.jeniskelamin_id_bayi}]`).trigger('click')
    $("#frm-pasien-nama_ayah").val(selectedBayi.penanggungjawab_nama)
    $("#frm-pasien-no_telepon_pasien").val(selectedBayi.no_telepon_pasien)
    console.log($("#frm-pasien-jeniskelamin").find(`input[value=${selectedBayi.jeniskelamin_id_bayi}]`).trigger('click')) // set value by console log
    $("#frm-pasien-nama_pasien").val('By.Ny ' + selectedBayi.nama_ibu)
    $("#frm-pasien-nama_ibu").val(selectedBayi.nama_ibu)
    $("#frm-pasien-agama").val(selectedBayi.agama).trigger('change')
    $("#frm-pasien-namadepan").val(206).trigger('change')
    destroySelect2IfExist($("#frm-pasien-statusperkawinan"))
    $("#frm-pasien-statusperkawinan").css('pointer-events', 'none').addClass('form-control')
    $('#frm-pasien-tanggal_lahir').val(convertDateByFormat(selectedBayi.tgl_lahir_by, 'd-M-Y')).trigger('change')
    $("#frm-pasien-golongandarah").val(goldrhby).trigger('change')
    $("#frm-pasien-statusperkawinan").val(303).trigger('change')
    $("#frm-pasien-propinsi_id").val(selectedBayi.propinsi_id).trigger('change')
    $("#frm-pasien-alamat_pasien").val(selectedBayi.alamat_pasien !== null ? selectedBayi.alamat_pasien : '')
    $("#frm-pasien-rt").val(selectedBayi.rt !== null ? selectedBayi.rt : '')
    $("#frm-pasien-rw").val(selectedBayi.rw !== null ? selectedBayi.rw : '')
    setAlamat(selectedBayi.propinsi_id, selectedBayi.kabupaten_id, selectedBayi.kecamatan_id, selectedBayi.kelurahan_id)
    $("#form-pasien-content").children('.col-md-6').show()
    $("#frm-pasien-nama_pasien").focus()
    setKunjunganBbl(selectedBayi)
  }

  function getProfile() {
    $.ajax({
      type: 'GET',
      url: '/pendaftaran/end-point/get-profile-rs',
      dataType: 'JSON',
      success: function (res) {
        $('#frm-pasien-tempat_lahir').val(res.response.kota)
      },
    });
  }

  function setAlamat(propinsiId, kabupatenId, kecamatanId, kelurahanId) {
    alamatBy = {
      province: propinsiId,
      city: kabupatenId,
      district: kecamatanId,
      village: kelurahanId,
    }
    // $("#frm-pasien-propinsi_id").val(propinsiId).change()
    bindingRegionPdftrn('/pendaftaran/end-point/region-list', {
      province: $("#frm-pasien-propinsi_id"),
      city: $("#frm-pasien-kabupaten_id"),
      district: $("#frm-pasien-kecamatan_id"),
      village: $("#frm-pasien-kelurahan_id"),
    })
  }

function setKunjunganBbl(selectedBayi) {
    $(document).find('#nokamar').val(selectedBayi.kamarruangan_nokamar + ' - ' + selectedBayi.no_tempattidur)
    $(document).find('#ruanganLabelValue').html("");
    $(document).find('#ruanganLabelValue').html(selectedBayi.ruangan_by + ' - ' + selectedBayi.kelas_nama_by)
    $("#kamartempattidur_id").val(selectedBayi.kamartempattidur_id)
    $("#kamarruangan_id").val(selectedBayi.kamarruangan_id)
    $("#jeniskasuspenyakit_id").val(selectedBayi.jeniskasuspenyakit_id).trigger('change')
    $("#kelaspelayanan_id").val(selectedBayi.kelas_id_by).trigger('change')
    $("#ruanganIdHidden").val(selectedBayi.ruangan_id_by).trigger('change')
    $("#kelasPelayananSelected").val(selectedBayi.kelas_id_by)
    setTimeout(() => {
      _formPendaftaran.getKarcis(selectedBayi.kelas_id_by);
      getListDokter(selectedBayi.ruangan_id_by);
    }, 3000);
}

function lookGoldrh(val) {
    let lookId = '';
    switch (val) {
        case 'a':
        lookId = 23
        break;
        case 'ab':
        lookId = 24
        break;
        case 'o':
        lookId = 25
        break;
        case 'b':
        lookId = 26
        break;
        default:
        lookId = 600;
        break;
    }
    return lookId
}

function getListDokter(ruanganId) {
    if ($('#pegawai_id').hasClass("select2-hidden-accessible")) {
      $('#pegawai_id').select2('destroy')
    }

    $.ajax({
      url: '/ranap/end-point/get-dokter-by-ruangan',
      data: {
        ruangan_id: ruanganId
      },
      method: 'GET',
      success: ({ data }) => {
        Object.keys(data).map((itemOutput) => {
          dataDropDownPegawai.push({
            id: itemOutput,
            text: data[itemOutput]
          })
        })
        refreshOptionSelect2($('#pegawai_id'), dataDropDownPegawai)
      },
      complete: () => {
        $('#pegawai_id').prop('disabled', false)
      }
    })
}

$(document).on('click', '#btn-confirm-peserta-asuransi', function () {
    $('#modal_backdrop').modal('hide');
    _formPendaftaran.ignoreAsuransi = true
    _formPendaftaran.generateForm()
});
setTimeout(() => {
    hideButtonPencarianAsuransi()
}, 500);

function hideButtonPencarianAsuransi() {
    if (_formPendaftaran.params == 'rajal' && instalasi != instalasiMcu || _formPendaftaran.params == 'igd') {
        return false;
    }

    $('#btn-pencarian-asuransi').hide();
}

function enableButtonPencarianAsuransi(isEnable = false) {
    if (_formPendaftaran.params == 'rajal' || _formPendaftaran.params == 'igd') {
        if (isEnable) {    
            $('#btn-pencarian-asuransi').prop('disabled', false);
        } else {
            $('#btn-pencarian-asuransi').prop('disabled', true);
        }
    }
}