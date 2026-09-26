const DEFAULT_D_LAR = [423, 427];//ruangan_id
const R_IGD = 428; // dulu 211
const WS_IGD = 10;
const I_RDRJ = [1]
const I_RI = [3]
const I_LAB = [4]
const I_LUAR = [73,85]
const CB_EXCLUDE_FARMASI = [45] // carabayar
const R_EXCLUDE_FARMASI = [71,77,78,79,80,81,82] // ruangan_id
const R_INCLUDE_DIAGNOSA = [89,72, 75] // instalasi
const PARAM_PENUNJANG = 'penunjang';
const PARAM_IGD = 'igd';
const PARAM_RAJAL = 'rajal';
const tujuanKunjTrue = 1;
const tujuanKunReset = 0;
const tujuanKunNormal = 0;
const tujuanKunProsedur = 1;
const tujuanKunKonsul = 2;
const rujukDatangSendiri = 1;
const extension = 'sty';
var defaultPenBiaya = [];
var pasienBaruBpjs = false;
var temp_pasien_id;
var temp_no_rekam_medik;

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
        setValueReservasi();
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
        /**  get current li */
        var _currentLi = $('ul[role="tablist"] > li.current > a').attr("aria-controls");
        /** get content field */
        var _contentStep = $('#' + _currentLi);
        var _childContent = _contentStep.children('div');
        var _idContent = _childContent.attr("id");

        /** Input BPJS */
        var _jenisPencarian = $('input[name="BpjsNewForm[jenis_rujukan]"]:checked').val();
        var _jenisPelayanan = $('select[name="BpjsNewForm[jenis_pelayanan]"]').val();
        var _jenisKartu = $('input[name="BpjsNewForm[jenis_kartu]"]:checked').val();
        var _noKartu = $('input[name="BpjsNewForm[no_kartu]"]').val();

        /** BPJS Rujukan */
        var _asalRujukanBpjs = $('select[name="BpjsNewForm[asal_rujukan]"]').val();
        var _noRujukan = $('input[name="BpjsNewForm[no_rujukan_f]"]').val();

        _isNew = (typeof _isNew != 'undefined' ? _isNew : null);

        /** Kondisi step awal jika berubah */
        /** WIP kondisi pasien asuransi lama -> umum lama -> asuransi lama */
        if (_formPendaftaran.tipePasien.carabayar_id != _caraBayar
            || _formPendaftaran.tipePasien.asalrujukan_id != _asalRujukan
            || _formPendaftaran.tipePasien.tipe_pasien != _isNew
            || _formPendaftaran.tipePasien.no_rekam_medik != _pasienId) {
            $.each($('ul[role="tablist"] > li:not(.first)'), function () {
                $('.steps-basic').steps("remove", 1);
            });
            $('a[href="#finish"]').trigger('click');
            return false;
        }

        /** Kondisi Untuk Cara bayar Asuransi Non BPJS */
        if (_groupCaraBayar == docoHelper.groupJaminan
            && (_formPendaftaran.tipePasien.no_asuransi != _formPendaftaran.tmpAsuransi.no_asuransi
                || _formPendaftaran.tmpAsuransi.prevPasien != _formPendaftaran.tmpAsuransi.pasien_id)) {
            $.each($('ul[role="tablist"] > li:not(.first)'), function () {
                $('.steps-basic').steps("remove", 1);
            });
            $('a[href="#finish"]').trigger('click');
            return false;
        }

        if (_groupCaraBayar == docoHelper.groupBPJS && skipBpjs == false) {
            var _execute = false;
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
                $.each($('ul[role="tablist"] > li:not(.first)'), function () {
                    $('.steps-basic').steps("remove", 1);
                });
                $('a[href="#finish"]').trigger('click');
                return false;
            }
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
                case 'form-bpjs-content':
                    return _formPendaftaran.validateBpjs(_contentStep);
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

            if (_groupCaraBayar == docoHelper.groupBPJS && _jenisPendaftaran != 0) {
                if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined' && _formPendaftaran.pendaftaranOl.status_janji == true) {

                } else {
                    /** Flow Normal Pendaftaran BPJS */
                    _data.push({
                        name: 'is_bpjs',
                        value: 1
                    });
    
                    if (typeof _formPendaftaran.dataReturnBpjs.rujukan !== 'undefined') {
                        _data.push({
                            name: 'BpjsNewForm[ppk_rujukan]',
                            value: _formPendaftaran.dataReturnBpjs.rujukan.provPerujuk.kode
                        });
                    }

                    _data.push({
                        name: 'BpjsNewForm[info_response]',
                        value: JSON.stringify(_formPendaftaran.dataReturnBpjs)
                    })

                    _data.push({
                        name: 'BpjsNewForm[nama_dpjp_melayani]',
                        value: _formPendaftaran.tmpBpjs.dpjpServeText
                    })
                   
                    _data.push({
                        name: 'BpjsNewForm[jenis_peserta]',
                        value: $('#jenis_peserta').val()
                    });

                    if(skipBpjs == true) {
                        _data.push({
                            name: 'allow_bpjs',
                            value: 1,
                        })
                    } else {
                        _data.push({
                            name: 'BpjsNewForm[no_kartu]',
                            value: _formPendaftaran.dataBpjs.noKartu
                        });
                    }
                }
            }

            var _penunjang = Object.keys(_formPendaftaran.listPenunjang).length;
            if (_penunjang) {
                _data.push({
                    name: 'list_penunjang',
                    value: JSON.stringify(_formPendaftaran.generateKunjungan(_formPendaftaran.listPenunjang))
                });
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

            /** Push list pasien mcu kolektif */
            if (instalasi == instalasiMcu) {
                if ($("input[name='TipePasienForm[is_kolektif]']")[2]['checked']) {
                    _data.push({
                        name: 'listPasienMcu',
                        value: JSON.stringify(dataPasienMcu)
                    });
                }
            }

            if(_formPendaftaran.params == PARAM_PENUNJANG) {
                let dokterPengganti = $('#kunjunganform-dokter_pengganti_id').val()
                let dokter = $('#kunjunganform-pegawai_id').val()

                if(!dokter && !dokterPengganti) {
                    return new PNotify({
                        title: 'Perhatian',
                        text: 'Dokter melayani belum dipilih!',
                        addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
                        type: 'warning'
                    });
                }

            } else {
                let dokterDpjp = $('#kunjunganform-dokter_id').val()
                let dokterPengganti = $('#kunjunganform-dokter_pengganti_id').val()

                if(!dokterDpjp && !dokterPengganti) {
                    return new PNotify({
                        title: 'Perhatian',
                        text: 'Dokter melayani belum dipilih!',
                        addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
                        type: 'warning'
                    });
                }
            }

            const simpanPendaftaran = function (dataPost, extra = {}) {
                var pendaftaran_id_hidden = $('#pendaftaran_id_hidden').val();
                var urlSimpan = '';
                if (pendaftaran_id_hidden != '') {
                    urlSimpan = '/pendaftaran/pendaftaran-' + _formPendaftaran.params + '/update-kunjungan-v2?params=' + _formPendaftaran.params + '&id=' + pendaftaran_id_hidden + '&param_rs=st-yusup';
                } else {
                    urlSimpan = '/pendaftaran/pendaftaran-' + _formPendaftaran.params + '/simpan-kunjungan-v2?params=' + _formPendaftaran.params;
                }

                console.log(urlSimpan);
                // var attr = {
                //     url: urlSimpan,
                //     data: dataPost,
                //     success: function (data) {
                //         _formPendaftaran.dataAntrian = null;
                //         _formPendaftaran.resetForm();

                //         var _responseData = typeof data.response != 'undefined' ? data.response : {};
                //         if (_responseData.is_bpjs) {
                //             // Fix bugs Tombol print SEP disable, tombol print SEP enable kalau pasien BPJS #3749
                //             _formPendaftaran.resetBpjs();
                //             $("#btn-print-sep").attr("disabled", false);
                //             window.open(`/pendaftaran/end-point/print-sep?pendaftaran_id=${_responseData.id}`);
                //         }

                //         if (typeof instalasi_id !== 'undefined' && instalasi_id == instalasiMcu) {
                //             _formPendaftaran.resetMcu()
                //             $("#file-upload").val('');
                //             dataPasienMcu = []
                //             $("#asalrujukan_id").val(_formPendaftaran.tipePasien.asalrujukan_id).trigger('change');
                //             // $("#asalrujukan_id").prop('disabled', true)
                //         }
                //         var cetakanAcion = $("#btn-modal-cetakan").attr("action");
                //         $("#btn-modal-cetakan").attr("action", cetakanAcion + '&pendaftaran_id=' + data.response.id + '&is_bpjs=' + data.response.is_bpjs).click();
                //         // location.reload();
                //     }, error(data) {
                //         var _res = data.responseJSON.response
                //         if (_res.flag) {
                //             setTimeout(function () {
                //                 var _tittle = `<b>${_res.title}</b><br>${_res.text}<br>Apakah anda yakin untuk meneruskan penyimpanan data ini ?`;
                //                 confirmationDialog(_tittle, function (reaction) {
                //                     if (reaction) {
                //                         var newData = dataPost
                //                         newData.push({
                //                             name: 'allow_bpjs',
                //                             value: 1,
                //                         })
                //                         simpanPendaftaran(newData, { skipConfirm: true })
                //                     }
                //                 })
                //             }, 500)
                //             return true;
                //         }
                //     },
                // };

                // var mergeObj = $.extend({}, attr, extra);
                // $().docoForm('click', mergeObj);
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
            no++
            row += '<tr class="row-data">';
            row += '<td>' + no + '</td>';
            row += '<td>' + v.jenispemeriksaan + '</td>';
            row += '<td>' + v.namapemeriksaan + '</td>';
            row += '<td>1</td>';
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

        total += parseInt(v.harga_tariftindakan) + hargacyto
    })
    $('.total-pemeriksaan').empty().html('<b>Rp. ' + docoHelper.convertToRupiah(total) + '</b>');
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

/** Class Form Pendaftaran */
var _formPendaftaran = {
    params: '',
    isForeignAsuransi: false,
    isConfirmedForeignAsuransi: false,
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
    formInputRujukan: null,
    formPasienMcu: null,
    listTarif: [],
    listPenunjang: {},
    totalTarif: 0,
    is_retensi: null,
    wilayahIndex: null,
    tmpAsuransi: {
        no_rekam_medik: null,
        pasien_id: null,
        no_asuransi: null,
        namapemilikasuransi: null,
        nomorpokokperusahaan: null,
        namaperusahaan: null,
        kelastanggunganasuransi_id: null,
        masaberlakukartu: null,
        nama_asuransi: null,
        prevPasien: null,
    },
    resetAsuransi: function () {
        _formPendaftaran.tmpAsuransi = {
            no_rekam_medik: null,
            pasien_id: null,
            no_asuransi: null,
            namapemilikasuransi: null,
            nomorpokokperusahaan: null,
            namaperusahaan: null,
            kelastanggunganasuransi_id: null,
            masaberlakukartu: null,
            nama_asuransi: null,
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
    tmpPasien: {
        namadepan: null,
        nama_pasien: null,
        propinsi_id: null,
        kabupaten_id: null,
        kecamatan_id: null,
        kelurahan_id: null,
        rt: null,
        rw: null,
        alamat_pasien: null,
        no_telepon_pasien: null,
        pekerjaan_id: null,
    },
    tmpKunjungan: {
        ruangan_id: null,
        jeniskasuspenyakit_id: null,
        kelaspelayanan_id: null,
        pegawai_id: null,
        keadaan_masuk: null,
        tgl_pendaftaran: null,
        keterangan_pendaftaran: null,
    },
    resetPasien: function () {
        _formPendaftaran.tmpPasien = {
            namadepan: null,
            nama_pasien: null,
            propinsi_id: null,
            kabupaten_id: null,
            kecamatan_id: null,
            kelurahan_id: null,
            rt: null,
            rw: null,
            alamat_pasien: null,
            no_telepon_pasien: null,
            pekerjaan_id: null,
            pt: null
        };
    },
    tmpBpjs: {
        no_kartu: null,
        asal_rujukan: null,
        jenis_pencarian: null,
        jenis_kartu: null,
        jenis_pelayanan: null,
        no_rujukan_f: null,
        dpjpServeText: null
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

        $("input[name='BpjsNewForm[jenis_rujukan]'][value=1]").closest("label").removeClass("hidden");
        $("input[name='BpjsNewForm[jenis_kartu]'][value=2]").closest("label").removeClass("hidden");

        $("#asal_rujukan_1").val('').trigger('change');
        var date = new Date();
        var picker = $('#tanggal_sep_1').pickadate('picker');
        picker.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
        $('.selectCarabayar').trigger("change");
    },
    init: function () {
        $('input[type=text]').keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });

        $('textarea').keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });

        $("input[name=chk-statuspasien]").change(function () {
            stateNextStep('next')
            if (this.checked) {
                $('#no_rekam_medik').prop("disabled", false);
                $(".field-no_rekam_medik").addClass('required');
                $('#no_rekam_medik').trigger("change");
            } else {
                //$('#no_rekam_medik').val('').trigger('change');
                $('#no_rekam_medik').prop("disabled", true);
                $(".field-no_rekam_medik").removeClass('required');
                _formPendaftaran.is_retensi = 0;
            }
        });
        bindCheckboxRadio(_formPendaftaran.indexActive)
        //$('.field-no_rekam_medik').hide();
        $('.field-tipepasienform-no_asuransi').hide();
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
                masaberlakukartu: null,
                nama_asuransi: null,
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

            var _content = $('#form-asuransi');

            renderPickadate($(_content.find('#masaberlakukartu')), {
                dependElementPicker: _content.find('#btn_addon_tgllahir').parent(),
                defaultValue: typeof _formPendaftaran.tmpAsuransi.masaberlakukartu !== 'undefined' && _formPendaftaran.tmpAsuransi.masaberlakukartu !== null ? _formPendaftaran.tmpAsuransi.masaberlakukartu : new Date()
            });

            $('#asuransiform-kelastanggungan_id').select2();
            $('#asuransiform-kelastanggungan_id').on('select2:close', ({ delegateTarget }) => {
                $(delegateTarget).focus()
            })
            _content.find('input').attr("autocomplete", "off");

            if (_formPendaftaran.tipePasien.no_asuransi != "") {
                if (_formPendaftaran.tmpAsuransi.namapemilikasuransi != null)
                    _content.find('#asuransiform-namapemilikasuransi').val(_formPendaftaran.tmpAsuransi.namapemilikasuransi);
                if (_formPendaftaran.tmpAsuransi.nama_asuransi != null)
                    _content.find('#asuransiform-nama_asuransi').val(_formPendaftaran.tmpAsuransi.nama_asuransi);
            } else {
                _content.find('#asuransiform-namapemilikasuransi').val(_formPendaftaran.tmpAsuransi.namapemilikasuransi);
                _content.find('#asuransiform-nama_asuransi').val(_formPendaftaran.tmpAsuransi.nama_asuransi);
            }

            _content.find('#asuransiform-nomorpokokperusahaan').val(_formPendaftaran.tmpAsuransi.nomorpokokperusahaan);
            _content.find('#asuransiform-kelastanggungan_id')
                .val(_formPendaftaran.tmpAsuransi.kelastanggunganasuransi_id)
                .trigger('change');
            _content.find('#asuransiform-namaperusahaan')
                .val(_formPendaftaran.tmpAsuransi.namaperusahaan);
            _content.find('#asuransiform-masaberlakukartu')
                .val(_formPendaftaran.tmpAsuransi.masaberlakukartu)
                .trigger('change');
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
                $("#pasien_id_hidden").val(noRm.pasien_id);
                return noRm.text;
            }
        });
        $("#no_rekam_medik").on("change", function () {
            var no_rekam_medik = $(this).val();
            temp_no_rekam_medik = no_rekam_medik;
            var carabayar_id = $(".selectCarabayar").val();
            var penjamin_id = $(".selectPenjamin").val();
            var pasien_id = $("#pasien_id_hidden").val();
            temp_pasien_id = pasien_id;
            _formPendaftaran.tipePasien.no_rekam_medik = no_rekam_medik;

            var today = new Date();
            var month = today.getMonth() + 1;
            var day = today.getDate();
            today = today.getFullYear() + '-' +
                (month < 10 ? '0' : '') + month + '-' +
                (day < 10 ? '0' : '') + day;

            if(no_rekam_medik != ""){
                _formPendaftaran.getInfoPasien('', no_rekam_medik, true);
                var validasi = validasiKunjungan(no_rekam_medik);
            } else {
                $('#form-parent-info').hide();
                $('#form-parent').removeClass("col-md-9").addClass("col-md-12");
            }

            /** case konsul pake var pendaftaranOl */
            if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined' && typeof _formPendaftaran.pendaftaranOl.penjamin_id !== 'undefined') {
                penjamin_id = _formPendaftaran.pendaftaranOl.penjamin_id
            }

            if (carabayar_id != 5 && carabayar_id != 6 && carabayar_id != 41 && carabayar_id != 44 && carabayar_id != 45 && carabayar_id != '' && carabayar_id != null && carabayar_id != 'null' && no_rekam_medik != "") {
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
                                        _formPendaftaran.tmpAsuransi.masaberlakukartu = _data_asuransi.masaberlakukartu;
                                        _formPendaftaran.tmpAsuransi.nama_asuransi = _data_asuransi.nama_asuransi;

                                        $('.field-tipepasienform-no_asuransi').find('#note-asuransi').html('<i>Atas Nama : ' + _formPendaftaran.tmpAsuransi.namapemilikasuransi + '<br>Pasien Pengguna Asuransi : ' + _nama_pasien + '<br> Nomor Rekam Medik Pasien : ' + _formPendaftaran.tmpAsuransi.no_rekam_medik);

                                        var _content = $('#form-asuransi');

                                        _content.find('#asuransiform-namapemilikasuransi').val(_formPendaftaran.tmpAsuransi.namapemilikasuransi);
                                        _content.find('#asuransiform-nomorpokokperusahaan').val(_formPendaftaran.tmpAsuransi.nomorpokokperusahaan);
                                        _content.find('#asuransiform-kelastanggungan_id')
                                            .val(_formPendaftaran.tmpAsuransi.kelastanggunganasuransi_id)
                                            .trigger('change');
                                        _content.find('#asuransiform-namaperusahaan')
                                            .val(_formPendaftaran.tmpAsuransi.namaperusahaan);
                                        _content.find('#asuransiform-masaberlakukartu')
                                            .val(_formPendaftaran.tmpAsuransi.masaberlakukartu);
                                        _content.find('#asuransiform-nama_asuransi')
                                            .val(_formPendaftaran.tmpAsuransi.nama_asuransi);
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

            if (no_rekam_medik != "") {
                $.ajax({
                    url: '/pendaftaran/pendaftaran-rajal/get-pendaftaran-umum?q=' + no_rekam_medik + '&tgl_pendaftaran=' + today,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function (res) {
                        $("#pendaftaran_id_hidden").val('');
                        if (res.response != "") {
                            var response = res.results;
                            if (response !== null) {
                                if (response.primaryPendaftaran != null) {
                                    var confirmPendaftaran = $("#btn-confirm-pendaftaran").attr("action");
                                    $("#btn-confirm-pendaftaran").attr("action", confirmPendaftaran + '&pendaftaran_id=' + response.primaryPendaftaran + '&instalasi_id=' + response.instalasi_id).click();
                                }
                            }
                        }
                    },
                    error: function (err) {
                        console.log("error get no rm");
                        console.log(err);
                    }
                });

                $.ajax({
                    url: '/pendaftaran/pendaftaran-rajal/cek-retensi?no_rekam_medik=' + no_rekam_medik,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function (res) {
                        if (res.results != "") {
                            var response = res.results;
                            if (response !== null) {
                                if (response.NORM != null) {
                                    docoNotification('error', 'Pasien sudah diretensi dan tidak bisa melakukan pendaftaran', '')
                                    _formPendaftaran.is_retensi = 1;
                                } else {
                                    _formPendaftaran.is_retensi = 0;
                                }
                            } else {
                                _formPendaftaran.is_retensi = 0;
                            }
                        } else {
                            _formPendaftaran.is_retensi = 0;
                        }
                    },
                    error: function (err) {
                        console.log("error cek retensi pasien");
                        console.log(err);
                    }
                });

                // if( _formPendaftaran.is_retensi == 0) {
                    // $.ajax({
                    //     url: '/pendaftaran/daftar-rajal/get-info-pasien?id='+pasien_id+'&no_rm=' + no_rekam_medik,
                    //     type: 'GET',
                    //     dataType: 'JSON',
                    //     success: function(res) {
                    //         var kunjungan = res.response.kunjugan[parseInt(res.response.kunjugan.length) - 1];
                    //         var asal_rujukan_id;

                    //         if (res.response.kunjugan !== undefined) {
                    //             var allKunjungan = res.response.kunjugan;
                    //             for (var x = 0; x < allKunjungan.length; x++) {
                    //                 if (defaultPenBiaya[allKunjungan[0].carabayar_id] === undefined) {
                    //                     defaultPenBiaya[allKunjungan[0].carabayar_id] = {
                    //                         penjamin_id: allKunjungan[0].penjamin_id,
                    //                         asal_rujukan_id: allKunjungan[0].asal_rujukan_id,
                    //                         namapemilik_asuransi: allKunjungan[0].namapemilik_asuransi,
                    //                         no_asuransi: allKunjungan[0].no_asuransi,
                    //                         nopokokperusahaan: allKunjungan[0].nopokokperusahaan,
                    //                         namaperusahaan: allKunjungan[0].namaperusahaan,
                    //                         penanggungbiaya_nama: allKunjungan[0].penanggungbiaya_nama,
                    //                         ruangcarabayar_id: allKunjungan[0].ruangcarabayar_id,
                    //                         noindukkaryawan: allKunjungan[0].noindukkaryawan,
                    //                         instansi: allKunjungan[0].instansi,
                    //                         namabagian: allKunjungan[0].namabagian,
                    //                     };
                    //                 }
                    //             }
                    //         }
                            
                    //         if (kunjungan.asalrujukan_id != null) {
                    //             asal_rujukan_id = kunjungan.asalrujukan_id;
                    //         }else {
                    //             asal_rujukan_id =1;
                    //         }
                            
                    //         if(kunjungan.carabayar_id != null) {
                    //             $('#selectCarabayar').val(kunjungan.carabayar_id).trigger('change').trigger('depdrop:change');
                    //         }
                            
                    //         $.ajax({
                    //             url: '/pendaftaran/daftar/get-penjamin',
                    //             data: {
                    //                 depdrop_parents: [
                    //                     $("#selectCarabayar").val()
                    //                 ]
                    //             },
                    //             method: 'POST',
                    //             success: (res) => {
                    //                 $('#penjamin_id').prop('disabled', false);
                    //                 $("#penjamin_id").select2('destroy')
                    //                 $("#penjamin_id").html('')
                    //                 res.output.map(({ id, name }) => {
                    //                     $("#penjamin_id").append(`<option value="${id}">${name}</option>`)
                    //                 })
                    //                 $("#penjamin_id").select2()
                    //                 if (kunjungan.penjamin_id) {
                    //                     $('#penjamin_id').val(kunjungan.penjamin_id).trigger('change').trigger('depdrop:change');
                    //                     kunjungan.penjamin_id = false;
                    //                 }
                    //             }
                    //         });
                    //         $('#asalrujukan_id').val(asal_rujukan_id).trigger('change').trigger('depdrop:change');
                            // $('#penjamin_id').val(kunjungan.penjamin_id).trigger('change').trigger('depdrop:change');
                            
                    //     },
                    //     error: function(err) {
                    //         console.log("error cek no rekam medik atau id pasien");
                    //         console.log(err);
                    //     }
                    // });
                // }
            }

            /*if(carabayar_id == 5 && no_rekam_medik != "") {
                    $.ajax({
                        url: '/pendaftaran/pendaftaran-rajal/get-pendaftaran-umum?q=' + no_rekam_medik,
                        type: 'GET',
                        dataType: 'JSON',
                        success: function(res) {
                            if(res.response != "") {
                                var response = res.results;
                                if(response !== null) {
                                        var _content = $('#form-pasien');
                                        
                                        _content.find('#form-tipepasien-nama_pasien')
                                            .val(response.nama_pasien_penanggung);
                                        _content.find('#form-tipepasien-namadepan')
                                            .val(response.namadepan_penanggung)
                                            .trigger('change');
                                        _content.find('#form-tipepasien-propinsi_id')
                                            .val(response.propinsi_id_penanggung)
                                            .trigger('change');
                                        _content.find('#form-tipepasien-kabupaten_id')
                                            .val(response.kabupaten_id_penanggung)
                                            .trigger('change');
                                        _content.find('#form-tipepasien-kecamatan_id')
                                            .val(response.kecamatan_id_penanggung)
                                            .trigger('change');
                                        _content.find('#form-tipepasien-kelurahan_id')
                                            .val(response.kelurahan_id_penanggung)
                                            .trigger('change');
                                        _content.find('#form-tipepasien-rt')
                                            .val(response.rt_penanggung);
                                        _content.find('#form-tipepasien-rw')
                                            .val(response.rw_penanggung);
                                        _content.find('#form-tipepasien-kode_pos')
                                            .val(response.kode_pos_penanggung);
                                        _content.find('#form-tipepasien-alamat_pasien')
                                            .val(response.alamat_pasien_penanggung);
                                        _content.find('#form-tipepasien-no_telepon_pasien')
                                            .val(response.no_telepon_pasien_penanggung);
                                        _content.find('#form-tipepasien-pt')
                                            .val(response.pt_penanggung);
                                        _content.find('#form-tipepasien-pekerjaan_id')
                                            .val(response.pekerjaan_id_penanggung)
                                            .trigger('change');
                                }
                            }
                        },
                        error: function(err) {
                            console.log("error get no asuransi");
                            console.log(err);
                        }
                    });
            }*/

            if ((carabayar_id == 41 || carabayar_id == 44 || carabayar_id == 45) && no_rekam_medik != "") {
                $.ajax({
                    url: '/pendaftaran/pendaftaran-rajal/get-penanggung-biaya?q=' + pasien_id + "&carabayar_id=" + carabayar_id,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function (res) {
                        if (res.response != "") {
                            var response = res.results;
                            if (response !== null) {
                                var _content = $('#form-penanggung');

                                _content.find('#form-tipepasien-penanggungbiaya_nama')
                                    .val(response.penanggungbiaya_nama);
                                _content.find('#form-tipepasien-instansi')
                                    .val(response.instansi);
                                _content.find('#form-tipepasien-namabagian')
                                    .val(response.namabagian);
                                _content.find('#form-tipepasien-noindukkaryawan')
                                    .val(response.noindukkaryawan);
                                _content.find('#form-tipepasien-jpkm')
                                    .val(response.jpkm);
                            }
                        }
                    },
                    error: function (err) {
                        console.log("error get pasien id");
                        console.log(err);
                    }
                });
            }
        });

        $(document).on('change', '#pendaftaran_id_hidden', function () {
            var pendaftaran_id_hidden = $(this).val();
            if (pendaftaran_id_hidden != "") {
                $.ajax({
                    url: '/pendaftaran/pendaftaran-rajal/get-update-data?id=' + pendaftaran_id_hidden,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function (res) {
                        if (res.data_update != "") {
                            var data_update = res.data_update;
                            $('#selectCarabayar').val(data_update.carabayar_id).trigger('change').trigger('depdrop:change');
                            //$('#penjamin_id').val(data_update.penjamin_id).trigger('change');
                            $('#penjamin_id').on('depdrop:afterChange', function (event, id, value) {
                                $('#penjamin_id').val(data_update.penjamin_id).trigger("change")
                                $('#penjamin_id').trigger("depdrop:change");
                            });
                            if (data_update.asalrujukan_id == null) {
                                data_update.asalrujukan_id = '1';
                            }
                            $('#asalrujukan_id').val(data_update.asalrujukan_id).trigger('change');

                            _formPendaftaran.tmpKunjungan.ruangan_id = data_update.ruangan_id;
                            _formPendaftaran.tmpKunjungan.jeniskasuspenyakit_id = data_update.jeniskasuspenyakit_id;
                            _formPendaftaran.tmpKunjungan.kelaspelayanan_id = data_update.kelaspelayanan_id;
                            _formPendaftaran.tmpKunjungan.pegawai_id = data_update.pegawai_id;
                            _formPendaftaran.tmpKunjungan.tgl_pendaftaran = data_update.tgl_pendaftaran;
                            _formPendaftaran.tmpKunjungan.keadaan_masuk = data_update.keadaan_masuk;
                            _formPendaftaran.tmpKunjungan.keterangan_pendaftaran = data_update.keterangan_pendaftaran;
                        }
                        if (res.data_penanggungbiaya != null) {
                            var data_penanggungbiaya = res.data_penanggungbiaya;
                            $('#form-tipepasien-penanggungbiaya_nama').val(data_penanggungbiaya.penanggungbiaya_nama);
                            $('#form-tipepasien-instansi').val(data_penanggungbiaya.instansi);
                            $('#form-tipepasien-namabagian').val(data_penanggungbiaya.namabagian);
                            $('#form-tipepasien-noindukkaryawan').val(data_penanggungbiaya.noindukkaryawan);
                            $('#form-tipepasien-jpkm').val(data_penanggungbiaya.jpkm);
                        }
                        if (res.data_asuransi != null) {
                            var data_asuransi = res.data_asuransi;
                            $('#tipepasienform-no_asuransi').val(data_asuransi.nokartuasuransi).trigger('change');
                            $('#asuransiform-namapemilikasuransi').val(data_asuransi.namapemilikasuransi);
                            $('#asuransiform-nomorpokokperusahaan').val(data_asuransi.nomorpokokperusahaan);
                            $('#asuransiform-namaperusahaan').val(data_asuransi.namaperusahaan);
                            $('#masaberlakukartu').val(data_asuransi.masaberlakukartu).trigger('change');
                            $('#asuransiform-nama_asuransi').val(data_asuransi.nama_asuransi);
                        }
                    },
                    error: function (err) {
                        console.log("error get pendaftaran");
                        console.log(err);
                    }
                });
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
        $('#form-input-kunjugan > .select2').select2("destroy");
        $('#form-input-pj > .select2').select2("destroy");
        $('#form-input-pasien > .select2').select2("destroy");
        $('#list-history').remove();
        $('#form-input-kunjugan').remove();
        $('#form-rujukan').remove();
        $('#form-input-pj').remove();
        $('#form-input-pasien').remove();
        $('#form-input-asuransi').remove();
        $('#form-input-bpjs').remove();
        $('#form-bpjs-error').remove();
        $('#form-pasien-mcu').remove();
        /** Untuk BPJS */
        $('.styled, .multiselect-container input').uniform({
            radioClass: 'choice'
        });
        $('#jenis_rujukan').change(function () {
            var base = $("input:radio[name='BpjsNewForm[jenis_rujukan]']:checked").val();
            if (base == 1) {
                $('#base-rujukan').show();
                $('#base-rujukan-manual').hide();
            } else {
                $('#base-rujukan').hide();
                $('#base-rujukan-manual').show();
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
            $("#kunjunganform-group_carabayar").val(carabayar_group);
            $("#carabayar_id_hidden").val(carabayar);

            if (instalasi == instalasiMcu) {
                if ($("input[name='TipePasienForm[is_kolektif]']")[1]['checked']) {
                    stateCaraBayar(carabayar_group)
                } else {
                    $('.field-no_rekam_medik').hide();
                }
            } else {
                stateCaraBayar(carabayar_group)
            }

            var _pasienIdOl = $('#pasien_ol_status').val();
            if (_pasienIdOl != "" && _pasienIdOl == "310") { //status pasien baru
                //$('input[name="chk-statuspasien"]').prop("checked", false);
                //$('#no_rekam_medik').val('').trigger('change');
                //$('#no_rekam_medik').prop("disabled", true);
                //$(".field-no_rekam_medik").removeClass('required');
            } else {
                //$('#no_rekam_medik').prop("disabled", false);
                //$('input[name="chk-statuspasien"]').prop("checked", true);
            }

            _formPendaftaran.resetAsuransi();
            //$(".field-no_rekam_medik").addClass('required');
            //$('#no_rekam_medik').val('').trigger('change');
            $("#note-asuransi").html("");
            $("#tipepasienform-no_asuransi").val("").trigger("change");

            $('#form-tipepasien-namadepan').val("").trigger("change");
            $('#form-tipepasien-nama_pasien').val("");
            $('#form-tipepasien-kecamatan_id').val("").trigger("change");
            $('#form-tipepasien-kelurahan_id').val("").trigger("change");
            $('#form-tipepasien-rt').val("");
            $('#form-tipepasien-rw').val("");
            $('#form-tipepasien-alamat_pasien').val("");
            $('#form-tipepasien-no_telepon_pasien').val("");
            $('#form-tipepasien-pekerjaan_id').val("").trigger("change");
            $('#form-tipepasien-penanggungbiaya_nama').val("");
            $('#form-tipepasien-instansi').val("");
            $('#form-tipepasien-namabagian').val("");
            $('#form-tipepasien-noindukkaryawan').val("");
            $('#form-tipepasien-jpkm').val("");
            $("input[name='PjpasienForm[pj_pengantar]']").closest('span').removeClass('checked')
            $("input[name='PjpasienForm[pj_pengantar]']").closest('div').removeClass('checked')
            $("input[name='PjpasienForm[pj_pengantar]'][value=990]").prop('checked', true).trigger('change');
            $("input[name='PjpasienForm[pj_pengantar]'][value=990]").closest('span').addClass('checked')
            $("input[name='PjpasienForm[pj_pengantar]'][value=990]").closest('div').addClass('checked')
            _formPendaftaran.resetPasien();

            if (defaultPenBiaya[carabayar] !== undefined) {
                $('#penjamin_id').val(defaultPenBiaya[carabayar].penjamin_id)
                    .trigger('change')
                    .trigger('depdrop:change');

                var defAsalRujukan = defaultPenBiaya[carabayar].asal_rujukan_id;
                if (defaultPenBiaya[carabayar].asal_rujukan_id == null) {
                    defAsalRujukan = rujukDatangSendiri;
                }
                $('#asalrujukan_id').val(defAsalRujukan)
                    .trigger('change')
                    .trigger('depdrop:change');

                if(defaultPenBiaya[carabayar].namapemilik_asuransi != null){
                    $('#asuransiform-namapemilikasuransi')
                        .val(defaultPenBiaya[carabayar].namapemilik_asuransi)
                        .trigger('change');
                }
        
                if(defaultPenBiaya[carabayar].no_asuransi != null){
                    $('#tipepasienform-no_asuransi')
                        .val(defaultPenBiaya[carabayar].no_asuransi)
                        .trigger('change');
                }
        
                if(defaultPenBiaya[carabayar].nopokokperusahaan != null){
                    $('#asuransiform-nomorpokokperusahaan')
                        .val(defaultPenBiaya[carabayar].nopokokperusahaan)
                        .trigger('change');
                }
        
                if(defaultPenBiaya[carabayar].namaperusahaan != null){
                    $('#asuransiform-namaperusahaan')
                        .val(defaultPenBiaya[carabayar].namaperusahaan)
                        .trigger('change');
                }
        
                if(defaultPenBiaya[carabayar].penanggungbiaya_nama != null){
                    $('#form-tipepasien-penanggungbiaya_nama')
                        .val(defaultPenBiaya[carabayar].penanggungbiaya_nama)
                        .trigger('change');
                }
        
                $('#ruangcarabayar_id').on('depdrop:afterChange', function (event, id, value) {
                    $('#ruangcarabayar_id')
                        .val(defaultPenBiaya[carabayar].ruangcarabayar_id)
                        .trigger('change');
                    $('#ruangcarabayar_id').trigger('depdrop:change');
                });
        
                if(defaultPenBiaya[carabayar].noindukkaryawan != null){
                    $('#form-tipepasien-noindukkaryawan')
                        .val(defaultPenBiaya[carabayar].noindukkaryawan)
                        .trigger('change');
                }
        
                if(defaultPenBiaya[carabayar].instansi != null){
                    $('#form-tipepasien-instansi')
                        .val(defaultPenBiaya[carabayar].instansi).trigger('change');
                }
        
                if(defaultPenBiaya[carabayar].namabagian != null){
                    $('#form-tipepasien-namabagian')
                        .val(defaultPenBiaya[carabayar].namabagian);
                }
            }
        });
        $(document).on("change", "#penjamin_id", function () {
            //$('#no_rekam_medik').val('').trigger('change');
            //$("#note-asuransi").html("");
            //$("#tipepasienform-no_asuransi").val("").trigger("change");
        });
        $(document).on("change", "#form-pj-pengantar", function () {
            var pj_pengantar = $("input[name='PjpasienForm[pj_pengantar]']:checked").val();
            if (pj_pengantar == 992) {
                $('#form-pasien').show();
            } else {
                $('#form-pasien').hide();
            }
        });
        $(document).on('select2:close', '.select2-hidden-accessible', ({ currentTarget }) => {
            $(currentTarget).focus()
        })
        $(document).on('change', '#tipepasienform-is_kolektif', function () {
            _formPendaftaran.resetForm()
            _formPendaftaran.resetMcu()
            if ($("input[name='TipePasienForm[is_kolektif]']")[2]['checked']) {
                $('#row-template').prop('hidden', false)
            } else {
                $('#row-template').prop('hidden', true)
            }
        })
        $('#btn-edit-info-pasien').click(function (e) {
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
        _formPendaftaran.additional();

        $("input[name='BpjsNewForm[jenis_kartu]']").on('change', function() {
            var jenisKartu = $("input[name='BpjsNewForm[jenis_kartu]']:checked").val();
            if (jenisKartu == '1') {
                $("#no_kartu").attr('maxlength','13');
                $("#no_kartu").val('');
            } else {
                $("#no_kartu").attr('maxlength','16');
                $("#no_kartu").val('');
            }
        });
        $('#tipe-pasien').on('change', '#no_rujukan,#no_kartu,#no_rujukan_1,#no_telp,#no_surat_kontrol', function() {
            this.value=this.value.trim().toUpperCase();
        })
    },
    additional: function () {

    },
    indexActive: 0,
    generateForm: function () { //sini
        var _jenisPendaftaran;
        var isKolektif = false //mcu
        if (_formPendaftaran.params == 'penunjang' && $("input[name='TipePasienForm[is_aps]']")[2]['checked'] === true) {
            _jenisPendaftaran = 0;
        } else {
            _jenisPendaftaran = 1;
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
        var _namapemilikasuransi = $('#asuransiform-namapemilikasuransi').val();
        var _nomorpokokperusahaan = $('#asuransiform-nomorpokokperusahaan').val();
        var _kelastanggungan_id = $('#asuransiform-kelastanggungan_id').val();
        var _namaperusahaan = $('#asuransiform-namaperusahaan').val();
        var _masaberlakukartu = $('#masaberlakukartu').val();
        var _status_konfirmasi = $('#asuransiform-status_konfirmasi').val();
        var _nama_asuransi = $('#asuransiform-nama_asuransi').val();
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
        /** Kebutuhan untuk data pasien */
        /*var _namadepan = $('#form-tipepasien-namadepan').val();
        var _nama_pasien = $('#form-tipepasien-nama_pasien').val();
        var _propinsi_id = $('#form-tipepasien-propinsi_id').val();
        var _kabupaten_id = $('#form-tipepasien-kabupaten_id').val();
        var _kecamatan_id = $('#form-tipepasien-kecamatan_id').val();
        var _kelurahan_id = $('#form-tipepasien-kelurahan_id').val();
        var _rt = $('#form-tipepasien-rt').val();
        var _rw = $('#form-tipepasien-rw').val();
        var _alamat_pasien = $('#form-tipepasien-alamat_pasien').val();
        var _no_telepon_pasien = $('#form-tipepasien-no_telepon_pasien').val();
        var _pekerjaan_id = $('#form-tipepasien-pekerjaan_id').val();*/
        /** end data pasien */

        _tipeId = (typeof _tipeId != 'undefined' ? _tipeId : null);
        $('#ket-bpjs').hide();

        var _data = {};
        let isKonsul = false
        /** Case Daftar Konsul BPJS Hari sama */
        if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined' && _formPendaftaran.pendaftaranOl.status_janji == true) {
            isKonsul = true
        }
        var _noindukkaryawan = $("input[name='PenanggungBiayaForm[noindukkaryawan]']").val();
        var _penanggungbiaya_nama = $("input[name='PenanggungBiayaForm[penanggungbiaya_nama]']").val();
        var _namabagian = $('#form-tipepasien-namabagian').val();
        var _instansi = $("input[name='PenanggungBiayaForm[instansi]']").val();
        var _ruangcarabayar_id = $("#ruangcarabayar_id").val();
        if (_jenisPendaftaran == 1) {
            _data = {
                carabayar_id: _caraBayar,
                penjamin_id: _penjamin,
                asalrujukan_id: _asalRujukan,
                groupcarabayar_id: _groupCaraBayar,
                no_rekam_medik: _pasienId,
                tipe_pasien: _tipeId,
                no_asuransi: _noAsuransi,
                noindukkaryawan: _noindukkaryawan,
                namapemilikasuransi: _namapemilikasuransi,
                nomorpokokperusahaan: _nomorpokokperusahaan,
                kelastanggungan_id: _kelastanggungan_id,
                namaperusahaan: _namaperusahaan,
                masaberlakukartu: _masaberlakukartu,
                status_konfirmasi: _status_konfirmasi,
                nama_asuransi: _nama_asuransi,
                penanggungbiaya_nama: _penanggungbiaya_nama,
                namabagian: _namabagian,
                instansi: _instansi,
                ruangcarabayar_id: _ruangcarabayar_id,
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
                asalrujukan_id: 1,
                skipBpjs: skipBpjs
            };
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

        _data.is_retensi = _formPendaftaran.is_retensi

        var _dataPost = $.extend({}, _data, _bpjs);
        $().docoForm('click', {
            url: '/pendaftaran/pendaftaran-' + _formPendaftaran.params + '/validation-tipe-pasien-v2',
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
                if (_jenisPendaftaran == 1) {
                    _pasienId = $("#pasien_id_hidden").val();
                } else {
                    _pasienId = "";
                }
                $('#btn-edit-info-pasien').prop('disabled', false)
                _pasienId = (_pasienId == "") ? null : _pasienId;

                /** Bypass semua validasi MCU Multiple */
                if (!isKolektif) {
                    // _asalRujukan = 0 >> jenis pendaftaran pasien rs
                    /*if ((_asalRujukan != 1 && _groupCaraBayar != docoHelper.groupBPJS) && _asalRujukan != "0") {
                        _formPendaftaran.formRujukan();
                    }*/

                    if (_groupCaraBayar == docoHelper.groupJaminan && _jenisPendaftaran != 0) {
                        /*_formPendaftaran.formAsuransi();*/
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
                    } else if (_groupCaraBayar == docoHelper.groupBPJS && _jenisPendaftaran != 0 && skipBpjs == false) {
                        _formPendaftaran.tmpBpjs = _bpjs;
                        let _statRujukan = false;
                        pasienBaruBpjs = false;
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
                                if (_formPendaftaran.dataBpjs.statusPeserta.kode > 0) {
                                    docoNotification('warning', 'Proses BPJS Gagal !', _formPendaftaran.dataBpjs.statusPeserta.keterangan)
                                    return false;
                                }
                            }
                            if (_pencarianBpjs == 1) { // rujukan
                                _formPendaftaran.dataRujukanBpjs = data.response.pasien_bpjs.rujukan;
                                _formPendaftaran.tmpBpjs.no_kartu = _formPendaftaran.dataRujukanBpjs.noKunjungan;
                            } else { // rujukan manual / IGD

                            }
                            if (typeof _formPendaftaran.dataReturnBpjs.messages != 'undefined') {
                                _formPendaftaran.errorBpjs();
                                _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm);
                                return true;
                            }


                            if (_formPendaftaran.dataReturnBpjs.pasien_baru === false) {
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
                                            // else {
                                            //     _formPendaftaran.findRmBpjs()
                                            // }
                                        })
                                    }, 100)
                                    return true
                                }
                            } else {
                                setTimeout(function () {
                                    confirmationDialog("Data pasien tidak ditemukan, Apakah anda ingin melanjutkan dengan pasien baru ?", function (cond) {
                                        if (cond) {
                                            pasienBaruBpjs = true
                                            _formPendaftaran.generateBpjs(_pencarianBpjs, _asalRujukan, _groupCaraBayar, _noRm)
                                        } else {
                                            _formPendaftaran.findRmBpjs()
                                        }
                                    })
                                }, 100)
                                return true
                            }

                            _formPendaftaran.formBpjs(_pencarianBpjs);
                            _formPendaftaran.resetAsuransi();
                            if (typeof _formPendaftaran.dataBpjs.mr != 'undefined' && _formPendaftaran.dataBpjs.mr != null) {
                                _findPasien = true;
                                _noRm = _formPendaftaran.dataBpjs.mr.noMR;
                                _pasienId = "";
                            } else {
                                _formPendaftaran.formPasien();
                            }
                        }
                    } else if (_groupCaraBayar == docoHelper.groupUmum) {
                        _pasienId = "";
                        // _noRmBpjs = _noRm;
                        if (!_formPendaftaran.tipePasien.tipe_pasien) {
                            /*_formPendaftaran.tmpPasien.namadepan = _namadepan;
                            _formPendaftaran.tmpPasien.nama_pasien = _nama_pasien;
                            _formPendaftaran.tmpPasien.propinsi_id =  _propinsi_id;
                            _formPendaftaran.tmpPasien.kabupaten_id = _kabupaten_id;
                            _formPendaftaran.tmpPasien.kecamatan_id = _kecamatan_id;
                            _formPendaftaran.tmpPasien.kelurahan_id = _kelurahan_id;
                            _formPendaftaran.tmpPasien.rt = _rt;
                            _formPendaftaran.tmpPasien.rw = _rw;
                            _formPendaftaran.tmpPasien.alamat_pasien = _alamat_pasien;
                            _formPendaftaran.tmpPasien.no_telepon_pasien = _no_telepon_pasien;
                            _formPendaftaran.tmpPasien.pekerjaan_id = _pekerjaan_id;*/

                            _formPendaftaran.formPasien();
                        }

                        _formPendaftaran.resetAsuransi();
                    } else {
                        _pasienId = "";
                        // _noRmBpjs = _noRm;
                        if (!_formPendaftaran.tipePasien.tipe_pasien) {
                            _formPendaftaran.formPasien();
                        }
                        _formPendaftaran.resetAsuransi();
                    }

                    /** kondisi pasien baru atau pasien lama */
                    if ((_formPendaftaran.tipePasien.tipe_pasien && _noRm) || _findPasien) {
                        if (Object.keys(_formPendaftaran.pendaftaranOl).length == 0) {
                            _formPendaftaran.getInfoPasien(_pasienId, _noRm);
                        } else if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined') {
                            _formPendaftaran.getInfoPasien(_pasienId, _noRm);
                        }
                    } else {
                        if (Object.keys(_formPendaftaran.pendaftaranOl).length == 0) {
                            $('#form-parent-info').hide();
                            $('#form-parent').removeClass("col-md-9").addClass("col-md-12");
                        }
                    }

                    generateFormKunjungan()
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
        }

        /** kondisi pasien baru atau pasien lama */
        if ((_formPendaftaran.tipePasien.tipe_pasien && _noRm) || _findPasien) {
            if (Object.keys(_formPendaftaran.pendaftaranOl).length == 0) {
                if (_formPendaftaran.dataReturnBpjs.pasien_baru === false) {
                    _formPendaftaran.getInfoPasien(_pasienId, _noRm);
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

        generateFormKunjungan()

    },
    errorBpjs: function () {
        var _clone = _formPendaftaran.formErrorBpjs;
        /** dari BPJS */
        $('.steps-basic').steps("add", {
            title: "Data BPJS",
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
    },
    formPasien: function (steps) {
        var _clone = _formPendaftaran.formInputPasien;
        if (typeof steps != 'undefined') {
            /** dari BPJS */
            $('.steps-basic').steps("insert", steps, {
                title: "Data Pasien",
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
                title: "Data Pasien",
                content: _clone.html()
            });
        }
        /** Event after generate form */
        var _content = $('#form-pasien-content');
        _content.find('span.select2').remove();
        _content.find('.select2').select2();
        _content.find('input').attr("autocomplete", "off");
        //Add required to NIK
        $('.field-frm-pasien-no_identitas_pasien').addClass('required');
        /** binding region */
        setRegionData('/pendaftaran/end-point/region-list', {
            province: {
                element: $("#frm-pasien-propinsi_id"),
                value: propinsi_id
            },
            city: {
                element: $("#kabupatenForm"),
                value: kabupaten_id
            },
            district: {
                element: $("#frm-pasien-kecamatan_id"),
                value: ''
            },
            village: {
                element: $("#frm-pasien-kelurahan_id"),
                value: ''
            },
        })
        var kodeKab = '73';
        var _kabupaten = $('#kabupatenForm');
        _kabupaten.data('code', kodeKab);
        $("#frm-pasien-propinsi_id").trigger('change');

        $('input[type=text]').keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });

        $('textarea').keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });

        var ctrlDown = false;
        var ctrlKey;
        var iKey;

        $('#form-keluarga-propinsi_id').val(null).trigger('change');
        $('#form-keluarga-kabupaten_id').val(null).trigger('change');
        $('#form-keluarga-kecamatan_id').val(null).trigger('change');
        $('#form-keluarga-kelurahan_id').val(null).trigger('change');
        // When focus in keluarga pasien nama
        $('#keluargapasienform-keluarga_nama').focus(function () {
            ctrlKey = 17;
            iKey = 73;

            // Document Ctrl + i
            $(document).keydown(function (e) {
                if (e.keyCode == ctrlKey) {
                    ctrlDown = true;
                }

                if (ctrlDown && (e.keyCode == iKey)) {
                    var nama_pasien = $('#frm-pasien-nama_pasien').val();
                    var namadepan = $('#frm-pasien-namadepan').val();
                    var alamat = $('#frm-pasien-alamat_pasien').val();
                    var no_telepon = $('#frm-pasien-no_telepon_pasien').val();
                    var rt = $('#frm-pasien-rt').val();
                    var rw = $('#frm-pasien-rw').val();
                    var pekerjaan = $('#frm-pasien-pekerjaan_id').val();
                    var propinsi = $('#frm-pasien-propinsi_id').val();
                    var kota = $('#kabupatenForm').val();
                    var kecamatan = $('#frm-pasien-kecamatan_id').val();
                    var keluarahan = $('#frm-pasien-kelurahan_id').val();
                    var alamatdepan = $('#frm-pasien-alamatdepan').val();

                    $('#keluargapasienform-keluarga_nama').val(nama_pasien);
                    $('#keluargapasienform-keluarga_namadepan').val(namadepan).trigger('change');
                    $('#keluargapasienform-keluarga_alamat').val(alamat);
                    $('#keluargapasienform-keluarga_no_telepon').val(no_telepon);
                    $('#form-keluarga-rt').val(rt);
                    $('#form-keluarga-rw').val(rw);
                    $('#keluargapasienform-keluarga_pekerjaan_id').val(pekerjaan).trigger('change');
                    $('#form-keluarga-propinsi_id').val(propinsi).trigger('change').trigger('depdrop:change');
                    // setTimeout(function() {
                    //     $('#form-keluarga-kabupaten_id').val(kota).trigger('change').trigger('depdrop:change');
                    // }, 500);
                   
                    // setTimeout(function() {
                    //     $('#form-keluarga-kecamatan_id').val(kecamatan).trigger('change').trigger('depdrop:change');
                    // }, 1000);
                    
                    // setTimeout(function() {
                    //     $('#form-keluarga-kelurahan_id').val(keluarahan).trigger('change').trigger('depdrop:change');
                    // }, 1500);
                    $('#keluargapasienform-alamatdepan').val(alamatdepan).trigger('change');
                    $('#form-keluarga-kabupaten_id').on('depdrop:afterChange', function (event, id, value) {
                        // console.log(event)
                        $(this).val(kota).trigger('change').trigger('depdrop:change');
                    });
                    $('#form-keluarga-kecamatan_id').on('depdrop:afterChange', function (event, id, value) {
                        $(this).val(kecamatan).trigger('change').trigger('depdrop:change');
                    });
                    setTimeout(() => {
                        $('#form-keluarga-kelurahan_id').on('depdrop:afterChange', function (event, id, value) {
                            $(this).val(keluarahan).trigger('change');
                        });
                    }, 500);

                    ctrlDown = false;
                }
            })
        })

        $('#keluargapasienform-keluarga_nama').blur(function () {
            ctrlDown = false;
            ctrlKey = 0;
            iKey = 0;
        });

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
            _kabupaten.data('code', '');
            _kecamatan.data('code', '');
            if ($('#frm-pasien-jenisidentitas').val() == '94') {
                // set propinsi
                _kabupaten.data('code', kodeKab);
                _kecamatan.data('code', kodeKec);
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

            }

        });
        
        $("#frm-pasien-namadepan").on('change', function () {
            var namadepan = $("#frm-pasien-namadepan").val();
            if (namadepan == 201 || namadepan == 993 || namadepan == 996 || namadepan == 999 || namadepan == 201 || namadepan == 995 || namadepan == 998 || namadepan == 1033) {
                //$('#frm-pasien-jeniskelamin').val(15).prop('checked', true).trigger('change');
                $("input[name='PasienForm[jeniskelamin]']").closest('span').removeClass('checked')
                $("input[name='PasienForm[jeniskelamin]']").closest('div').removeClass('checked')
                $("input[name='PasienForm[jeniskelamin]'][value=15]").prop('checked', true).trigger('change');
                $("input[name='PasienForm[jeniskelamin]'][value=15]").closest('span').addClass('checked')
                $("input[name='PasienForm[jeniskelamin]'][value=15]").closest('div').addClass('checked')
            } else if (namadepan == 202 || namadepan == 204 || namadepan == 1001 || namadepan == 994 || namadepan == 997 || namadepan == 1000  || namadepan == 1034) {
                //$('#frm-pasien-jeniskelamin').val(16).trigger('change');
                $("input[name='PasienForm[jeniskelamin]']").closest('span').removeClass('checked')
                $("input[name='PasienForm[jeniskelamin]']").closest('div').removeClass('checked')
                $("input[name='PasienForm[jeniskelamin]'][value=16]").prop('checked', true).trigger('change');
                $("input[name='PasienForm[jeniskelamin]'][value=16]").closest('span').addClass('checked')
                $("input[name='PasienForm[jeniskelamin]'][value=16]").closest('div').addClass('checked')
            } else {
                //$('#frm-pasien-jeniskelamin').val('').trigger('change');
                $("input[name='PasienForm[jeniskelamin]']").closest('span').removeClass('checked')
                $("input[name='PasienForm[jeniskelamin]']").closest('div').removeClass('checked')
                $("input[name='PasienForm[jeniskelamin]'][value='']").prop('checked', true).trigger('change');
                $("input[name='PasienForm[jeniskelamin]'][value='']").closest('span').addClass('checked')
                $("input[name='PasienForm[jeniskelamin]'][value='']").closest('div').addClass('checked')
            }
        });

        $("#keluargapasienform-keluarga_namadepan").on('change', function () {
            var namadepan = $("#keluargapasienform-keluarga_namadepan").val();
            if (namadepan != 202 && namadepan != 204 && namadepan != 1001 && namadepan != 994 && namadepan != 997 && namadepan != 1000 && namadepan != 1034) {
                //$('#frm-pasien-jeniskelamin').val(15).prop('checked', true).trigger('change');
                $("input[name='KeluargaPasienForm[keluarga_jk]']").closest('span').removeClass('checked')
                $("input[name='KeluargaPasienForm[keluarga_jk]']").closest('div').removeClass('checked')
                $("input[name='KeluargaPasienForm[keluarga_jk]'][value=15]").prop('checked', true).trigger('change');
                $("input[name='KeluargaPasienForm[keluarga_jk]'][value=15]").closest('span').addClass('checked')
                $("input[name='KeluargaPasienForm[keluarga_jk]'][value=15]").closest('div').addClass('checked')
            } else {
                //$('#frm-pasien-jeniskelamin').val(16).trigger('change');
                $("input[name='KeluargaPasienForm[keluarga_jk]']").closest('span').removeClass('checked')
                $("input[name='KeluargaPasienForm[keluarga_jk]']").closest('div').removeClass('checked')
                $("input[name='KeluargaPasienForm[keluarga_jk]'][value=16]").prop('checked', true).trigger('change');
                $("input[name='KeluargaPasienForm[keluarga_jk]'][value=16]").closest('span').addClass('checked')
                $("input[name='KeluargaPasienForm[keluarga_jk]'][value=16]").closest('div').addClass('checked')
            }
        });

        $("#frm-pasien-jenisidentitas").val(94).trigger('change');
        if (pasienBaruBpjs == true) {
            $('#form-parent-info').show();
            $('#form-parent').removeClass("col-md-12").addClass("col-md-9");
            $('#btn-edit-info-pasien').prop('disabled', true)
        } else {
            $('#form-parent-info').hide();
            $('#form-parent').removeClass("col-md-9").addClass("col-md-12");
        }

        /*if (_formPendaftaran.tmpPasien.nama_pasien != null) {
            $('#frm-pasien-namadepan').val(_formPendaftaran.tmpPasien.namadepan).trigger('change');                            
            $('#frm-pasien-nama_pasien').val(_formPendaftaran.tmpPasien.nama_pasien);
            $('#frm-pasien-propinsi_id').val(_formPendaftaran.tmpPasien.propinsi_id).trigger('change');
            $('#kabupatenForm').val(_formPendaftaran.tmpPasien.kabupaten_id).trigger('change');
            $('#frm-pasien-kecamatan_id').val(_formPendaftaran.tmpPasien.kecamatan_id).trigger('change');
            $('#frm-pasien-kelurahan_id').val(_formPendaftaran.tmpPasien.kelurahan_id).trigger('change');
            $('#frm-pasien-rt').val(_formPendaftaran.tmpPasien.rt);
            $('#frm-pasien-rw').val(_formPendaftaran.tmpPasien.rw);
            $('#frm-pasien-alamat_pasien').val(_formPendaftaran.tmpPasien.alamat_pasien);
            $('#frm-pasien-no_telepon_pasien').val(_formPendaftaran.tmpPasien.no_telepon_pasien);
            $('#frm-pasien-pekerjaan_id').val(_formPendaftaran.tmpPasien.pekerjaan_id);
        }*/
        
        
        $('#form-keluarga-kabupaten_id').depdrop({
            depends: ["form-keluarga-propinsi_id"],
            placeholder: "-- PILIH --",
            url: "/master/kabupaten/list-kabupaten"
        });
        
        $('#form-keluarga-kecamatan_id').depdrop({
            depends: ["form-keluarga-kabupaten_id"],
            placeholder: "-- PILIH --",
            url: "/master/kecamatan/list-kecamatan"
        });
        $('#form-keluarga-kelurahan_id').depdrop({
            depends: ["form-keluarga-kecamatan_id"],
            placeholder: "-- PILIH --",
            url: "/master/kelurahan/list-kelurahan"
        });

        $('#form-keluarga-kecamatan_id').trigger('change').trigger('depdrop:change');
        $('#form-keluarga-kelurahan_id').trigger('change').trigger('depdrop:change');
        // $('#form-keluarga-kelurahan_id').on('depdrop:afterChange', function (event, id, value) {
        //     $('#form-keluarga-kelurahan_id').trigger('change');
        //     $('#form-keluarga-kelurahan_id').trigger('depdrop:change');
        // });

        $('#btn-data-keluarga').click(function (e) {
            e.preventDefault();
            $('#modal_data_keluarga').modal('show');
            return false;
        });

        _formPendaftaran.wilayahIndex = setInterval(function() {
            $('.select2Wilayah').attr('tabindex', '1');
        },300);

        // Return input no rm to original size
        $( "span[aria-labelledby='select2-no_rekam_medik-container']" ).css("max-width","");
    },
    formRujukan: function () {
        var _clone = _formPendaftaran.formInputRujukan;
        _clone.find('input').attr("autocomplete", "off");
        $('.steps-basic').steps("add", {
            title: "Data Rujukan",
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

        $('#rujukanform-rujukandari_id').select2();
        refreshOptionSelect2($('#rujukanform-rujukandari_id'), dataRujukanDari)
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
            title: "Data Asuransi",
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
    },
    formPreviewMcu: function () {
        var tableMcuPreview;
        var _clone = _formPendaftaran.formPasienMcu;

        if (typeof dataPasienMcu !== 'undefined' && dataPasienMcu.length != 0) {
            $('.steps-basic').steps("add", {
                title: "Preview Pasien MCU",
                content: _clone.html()
            });

            $("#info-lengkap").empty().html(_formPendaftaran.tmpDataMcu.dataComplete + " Data Lengkap");
            $("#info-tidak-lengkap").empty().html(_formPendaftaran.tmpDataMcu.dataIncomplete + " Data Tidak Lengkap");
            $("#info-file").empty().html("Sumber : " + _formPendaftaran.tmpDataMcu.dataFile);
            $("#info-double-rm").empty().html(_formPendaftaran.tmpDataMcu.dataDoubleRm + " No Rekam Medik Salah / Duplikasi");

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
                    }
                    else {
                        $(nRow).css("background-color", "#99FFCC");
                    }
                }
            })

            tableMcuPreview.on('order.dt search.dt', function () {
                tableMcuPreview.column(1, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
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
    validateAsuransi: function (object) {
        var _data = object.serializeArray();
        var _result = false;
        $().docoForm('click', {
            url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-asuransi',
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
        
        // Skip confirmation tujuan kunjungan
        if (_formPendaftaran.params == 'igd') {
            $('#is_tujuan_kunj').val(tujuanKunjTrue);
        }

        var _data = object.serializeArray();
        if (typeof _formPendaftaran.dataReturnBpjs.rujukan !== 'undefined') {
            _data.push({
                name: 'BpjsNewForm[asal_rujukan]',
                value: _formPendaftaran.dataReturnBpjs.asalFaskes
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
        } else if (typeof _formPendaftaran.dataReturnBpjs.post_ranap !== 'undefined') {
            _data.push({
                name: 'asal_rujukan_hidden',
                value: _formPendaftaran.dataPostRanap.asal_rujukan
            });
            _data.push({
                name: 'ppk_rujukan_hidden',
                value: _formPendaftaran.dataPostRanap.ppkrujukan_kode
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
        _data.push({
            name: 'ext',
            value: extension
        });
        var _result = false;
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

                if (is_tujuan_kunj == tujuanKunjTrue || kunjungan.length === 0 ) {
                    _result = true;

                    // Set to 0
                    $('#is_tujuan_kunj').val(tujuanKunReset);
                } else {
                    lastPoli = data.response.data_rujukan.lastPoli;
                    poliAsalRujuk = data.response.data_rujukan.rujukan.poliRujukan ? data.response.data_rujukan.rujukan.poliRujukan.kode : null;

                    if (tanggal_sep == kunjungan[0].tglSep) {
                        if (poli_tujuan == poliAsalRujuk) {
                            $("#tujuan_kunjungan").val(tujuanKunProsedur);
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
        return _result;
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
        if (post_ranap) {
            if (dataPostRanap && Object.keys(dataPostRanap).length) {
                asal_rujukan = dataPostRanap.asal_rujukan
            } else {
                asal_rujukan = post_ranap.asal_rujukan
            }
        }
        var no_rujukan = (post_ranap) ? dataPostRanap.no_rujukan : null;
        var ppkrujukan_kode = (post_ranap) ? dataPostRanap.ppkrujukan_kode : null;
        var ppkrujukan_nama = (post_ranap) ? dataPostRanap.ppkrujukan_nama : null;
        var rujukan = {};

        if (jenisRujukan == 1) {
            rujukan = _formPendaftaran.dataReturnBpjs.rujukan;
            no_rujukan = _formPendaftaran.tmpBpjs.no_kartu;
        }

        if (Object.keys(_formPendaftaran.dataBpjs).length) {
            $('#ket-bpjs').show();
            $('.steps-basic').steps("add", {
                title: "Data BPJS",
                content: _clone.html()
            });
            var _content = $('#form-bpjs-content');

            _content.find('.select2Bpjs').select2();
            // poli tujuan otomatis terisi sesuai kondisi = IGD
            if (_inputBpjs.jenis_pencarian === '2' && getRoom === 'igd') {
                refreshOptionSelect2($('#poli_tujuan'), [{ "id": "IGD", "text": "INSTALASI GAWAT DARURAT" }]);
                $(".asal_rujukan").show();
                $(".ppk_rujukan").show();
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

            // FIX BUGS List nama-nama DPJP pemberi Surat SKDP/SPRI muncul berdasarkan Spesialis/SubSpesialis #3749
            $(".select2Dpjp").select2({
                placeholder: "PILIH DOKTER DPJP",
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
                templateSelection: function (res) {
                    _formPendaftaran.tmpBpjs.dpjpServeText = res.text
                    return res.text;
                }
            });

            $(".select2DpjpServe").select2({
                placeholder: "PILIH DOKTER DPJP MELAYANI",
                ajax: {
                    url: "/api/bpjs/referensi-dpjp",
                    dataType: "json",
                    quietMillis: 250,
                    data: function(params) {
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
                templateSelection: function (res) {
                    _formPendaftaran.tmpBpjs.dpjpServeText = res.text
                    return res.text;
                }
            });

            $(".select2JenisPeserta").select2({
                placeholder: "PILIH JENIS KEPESERTAAN",
                ajax: {
                    url: "/api/bpjs/referensi-kepesertaan",
                    dataType: "json",
                    quietMillis: 250,
                    data: function (params) {
                        var query = {
                            search: params.term,
                            type: 'public',
                        }

                        return query;
                    },
                },
            });

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
            });

            $("select").on('select2:close', ({ delegateTarget }) => {
                $(delegateTarget).focus()
            })

            if (jenisRujukan != 1) {
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
                $.ajax({
                    type: "GET",
                    url: url,
                    dataType: "JSON",
                    success: function (responsen) {
                        let responsebpjs = responsen.response;
                        $("#option_dpjp").remove();
                        $("#option_dpjp_pemberi").remove();

                        if (responsebpjs.kodeDokter != null) {
                            let option_dpjp = '<option id="option_dpjp" value="'+responsebpjs.kodeDokter+'" selected="">'+responsebpjs.namaDokter+'</option>'
                            $("#kode_dpjp_melayani").append(option_dpjp);
                            $('#kode_dpjp_melayani').val(responsebpjs.kodeDokter).trigger('change');
                        }
                        if (responsebpjs.kodeDokterPembuat) {
                            let option_dpjp_pemberi = '<option id="option_dpjp_pemberi" value="'+responsebpjs.kodeDokterPembuat+'" selected="">'+responsebpjs.namaDokterPembuat+'</option>'
                            $("#kode_dpjp").append(option_dpjp_pemberi);
                            $('#kode_dpjp').val(responsebpjs.kodeDokterPembuat).trigger('change');
                        }
                        if (responsebpjs.poliTujuan) {
                            let option_poli_tujuan = '<option id="option_poli_tujuan" value="'+responsebpjs.poliTujuan+'" selected="">'+responsebpjs.namaPoliTujuan+'</option>'
                            $("#poli_tujuan").append(option_poli_tujuan);
                            $('#poli_tujuan').val(responsebpjs.poliTujuan).trigger('change');
                        }
                    },
                    error: function(res) {
                        docoNotification("warning", "Peringatan!", res.responseJSON.response.text);
                    }
                });
            })
            // hide form rujukan when poli tujuan = IGD
            $('#poli_tujuan').change(function () {
                if ($(this).val() == 'IGD') {
                    $('.frm-rujukan').hide();
                    // meminimalisasi form jika poli IGD
                    // $(".asal_rujukan").hide();
                    // $(".ppk_rujukan").hide();
                    $(".tanggal_rujukan").hide();
                    $(".no_rujukan").hide();
                    $(".kelas_rawat").hide();
                    $(".katarak").hide();
                    // $(".dpjp_form").hide();
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

                if (jenisRujukan != 1) { // dpjp untuk rujukan, tidak boleh diubah. harus tetap ambil dari rujukan sebelumnya
                    _formPendaftaran.setdpjp($('#jenis_pelayanan').val(), $('#tanggal_sep').val(), $(this).val());
                }

                if (typeof _formPendaftaran.dataReturnBpjs.rujukan !== 'undefined') {
                    // ketika beda poli dengan poli rujukan awal
                    if ($(this).val() != _formPendaftaran.dataReturnBpjs.rujukan.poliRujukan.kode) {
                        $('.field-kode_dpjp').removeClass('required');
                        $('.field-kode_dpjp').addClass('hidden');
                        $('.field-no_surat_kontrol').removeClass('required');
                        $('.field-no_surat_kontrol').addClass('hidden');
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
                // $(".asal_rujukan").hide();
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
            $('#bpjsnew_detail_tgl_lahir').html('<strong>:</strong> ' + convertDateByFormat(peserta.tglLahir, 'd m Y'));
            $('#bpjsnew_detail_jenis_peserta').html('<strong>:</strong> ' + peserta.jenisPeserta.keterangan);
            $('#bpjsnew_detail_hak_kelas').html('<strong>:</strong> ' + peserta.hakKelas.keterangan);
            $('#bpjsnew_detail_nokartu').html('<strong>:</strong> ' + peserta.noKartu);
            $('.btn-detail-bpjs').attr('action', `/pendaftaran/daftar-igd/detail-history-bpjs?no_kartu=${peserta.noKartu}`)
            $('.btn-detail-bpjs').show();

            if ($('#asal_rujukan').val() == 2) {
                $('#no_surat_kontrol').val('');
                $('.dpjp_form').show();
            } else {
                $('#no_surat_kontrol').val('');
                $('.dpjp_form').hide();
            }
            
            if(getRoom == 'igd') {
                $('.field-no_surat_kontrol').removeClass('required')
            }

            if (jenisRujukan == 1) { // rujukan
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

                    $("#no_surat_kontrol").val('');
                    $(".dpjp_form").show();

                    _formPendaftaran.setdpjp($('#jenis_pelayanan').val(), $('#tanggal_sep').val(), _formPendaftaran.dataReturnBpjs.lastPoli);

                } else {
                    $("#no_surat_kontrol").val('');
                    $(".dpjp_form").hide();
                    // $("#is_skdp").val('0');
                }

                // diagnosaawal
                var diagnosa = rujukan.diagnosa;
                var option = new Option(diagnosa.nama, diagnosa.kode, true, true);
                $("#diagnosa_awal").append(option);
                $('#diagnosa_awal').val(diagnosa.kode).trigger('change');

                $('#no_kartu').val(peserta.noKartu);
                $('#jenis_pelayanan').val(rujukan.pelayanan.kode).trigger('change');
                $('#no_rujukan_1').val(rujukan.noKunjungan);
                $('#kelas_rawat').val(peserta.hakKelas.kode);
                $('#nomr').val(peserta.mr.noMR);
                $('#no_telp').val(peserta.mr.noTelepon);

                $('#hide-pelayanan').val(rujukan.pelayanan.kode);
            }

            var _disabled = (post_ranap || jenisRujukan == 1) ? true : false;
            var _disabledNoRujukan = (post_ranap || jenisRujukan == 1) ? true : false;

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
                $("#no_surat_kontrol").val('');
                $(".dpjp_form").show();
            }

            $("input[name='BpjsNewForm[no_rekam_medik]']").val(peserta.mr.noMR);
            $("#no_rujukan_1").prop("readonly", _disabledNoRujukan);
            $("#no_rujukan_1").val(no_rujukan);
            $("#no_asuransi").val(peserta.noKartu);
            $("#kelas_rawat").val(peserta.hakKelas.kode).trigger('change');
            var tmt = peserta.tglTMT;
            var tat = peserta.tglTAT;
            $('#bpjsnew_detail_tmt_tat').html('<strong>:</strong> ' + convertDateByFormat(tmt, 'd m Y') + ' - ' + convertDateByFormat(tat, 'd m Y'));
            var kdProv = peserta.provUmum.kdProvider;
            var nmProv = peserta.provUmum.nmProvider;
            $('#bpjsnew_detail_ppk_rujukan').html('<strong>:</strong> ' + kdProv + " - " + nmProv);
            var statusPeserta = peserta.statusPeserta.keterangan;
            $('#bpjsnew_detail_status_peserta').html('<strong>:</strong> ' + statusPeserta);

            var tglSep = $("input[name='BpjsNewForm[tanggal_sep]_submit']").val();
            $('#button-list-sep').attr('href', '/pendaftaran/daftar/list-sep?no_kartu=' + peserta.noKartu + '&tgl_sep=' + tglSep);
            $("#no_telp").val(peserta.mr.noTelepon);

            $('input[type=text]').keyup(function () {
                $(this).val($(this).val().toUpperCase());
            });

            $('textarea').keyup(function () {
                $(this).val($(this).val().toUpperCase());
            });

            $('#jenis_peserta').on('change', function() {
                var jenisPeserta = $(this).val();
                $('#penjamin_id').val(jenisPeserta);
                $('#penjamin_id').trigger('change');
            });

            if(peserta.informasi.prolanisPRB != null) {
                $('#group_prb').show();
                $('#content_prb').html(peserta.informasi.prolanisPRB);
            }
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
                _result = false;
                var no_identitas_pasien = $('#frm-pasien-no_identitas_pasien').val();
                if (no_identitas_pasien == null || no_identitas_pasien == '') {

                    $('.field-frm-pasien-no_identitas_pasien').parent().addClass('has-error');
                    $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('<i class="fa fa-exclamation-circle" aria-hidden=true></i> &nbsp;NIK Tidak Boleh Kosong.');
                }else if (!no_identitas_pasien.match(/^[0-9]+$/)) {
                    $('.field-frm-pasien-no_identitas_pasien').parent().addClass('has-error');
                    $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('<i class="fa fa-exclamation-circle" aria-hidden=true></i> &nbsp;NIK hanya boleh angka.');
                }else if (no_identitas_pasien.length > 16) {
                    $('.field-frm-pasien-no_identitas_pasien').parent().addClass('has-error');
                    $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('<i class="fa fa-exclamation-circle" aria-hidden=true></i> &nbsp;NIK tidak boleh lebih dari 16 char.');
                }else {
                    $('.field-frm-pasien-no_identitas_pasien').parent().removeClass('has-error');
                    $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('');
                }

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

                //Penanda Button Data Keluarga
                $('.div-data-keluarga').parent().find('.help-block').html('<span style="color: black;">Data Keluarga Harus Diisi</span> *');
            }
        });

        //_result = false;

        if (_result == true) {
            $().docoForm('click', {
                url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/validation-keluarga-pasien',
                data: _data,
                skipConfirm: true,
                skipSuccessNotif: true,
                async: false,
                success: function (data) {
                    _result = true;

                    //Penanda Button Data Keluarga
                    $('.div-data-keluarga').parent().find('.help-block').html('<span style="color: black;">Data Keluarga Harus Diisi</span> *');

                    var no_identitas_pasien = $('#frm-pasien-no_identitas_pasien').val();
                    if (no_identitas_pasien == null || no_identitas_pasien == '') {

                        $('.field-frm-pasien-no_identitas_pasien').parent().addClass('has-error');
                        $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('<i class="fa fa-exclamation-circle" aria-hidden=true></i> &nbsp;NIK Tidak Boleh Kosong.');
                        _result = false;
                    } else {
                        $('.field-frm-pasien-no_identitas_pasien').parent().removeClass('has-error');
                        $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('');
                        clearInterval(_formPendaftaran.wilayahIndex);
                    }
                },
                error: function (error) {
                    _result = false;

                    //Penanda Button Data Keluarga
                    $('.div-data-keluarga').parent().find('.help-block').html('<i class="fa fa-exclamation-circle" aria-hidden=true></i> &nbsp;Cek Inputan Data Keluarga.');

                    var no_identitas_pasien = $('#frm-pasien-no_identitas_pasien').val();
                    if (no_identitas_pasien == null || no_identitas_pasien == '') {
                        $('.field-frm-pasien-no_identitas_pasien').parent().addClass('has-error');
                        $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('<i class="fa fa-exclamation-circle" aria-hidden=true></i> &nbsp;NIK Tidak Boleh Kosong.');
                    } else {
                        $('.field-frm-pasien-no_identitas_pasien').parent().removeClass('has-error');
                        $('.field-frm-pasien-no_identitas_pasien').parent().find('.help-block').html('');
                    }
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
        var _clonePj = _formPendaftaran.formInputPj;
        $('.steps-basic').steps("add", {
            title: "Data Registrasi",
            content: _clone.html()
        });
        $('#kunjunganform-is_pj').on("change", function () {
            if (this.checked) {
                $('.steps-basic').steps("add", {
                    title: "Penanggung Jawab",
                    content: _clonePj.html()
                });
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
                    $('.umurtext').val(umur);
                })
                $('#form-pj-content').find('.styled, .multiselect-container input').uniform({
                    radioClass: 'choice'
                });
                $('select').removeAttr('tabindex');
                var _parent = $('fieldset[aria-hidden="false"]');
                // _parent.find('[data-urutan="1"]').focus();
            } else {
                var _totalStep = $('ul[role="tablist"] > li').length;
                $('.steps-basic').steps("remove", _totalStep - 1);
            }
        });

        /** Re init */
        var _content = $('#form-kunjungan-content');
        _content.find('span.select2').remove();
        _content.find('.select2').select2();

        let pasienId = $("#pasien_id_hidden").val()

        getListDokterAll();
        if ($('#kunjunganform-jeniskasuspenyakit_id').data('depdrop')) {
            $('#kunjunganform-jeniskasuspenyakit_id').depdrop('destroy');
        }

        $('#kunjunganform-dokter_pengganti_id').on('change', function() {
            let ddPenunjang = $('#instalasi_id').val()

            if(_formPendaftaran.params == PARAM_PENUNJANG) {
                var dd_dokter = $('#kunjunganform-pegawai_id')
            } else {
                var dd_dokter = $('#kunjunganform-dokter_id')
            }

            if($(this).val()) {
                dd_dokter.val('').trigger('change')
                dd_dokter.prop('disabled', true)
            } else {
                if(_formPendaftaran.params == PARAM_PENUNJANG) {
                    if(ddPenunjang) {
                        dd_dokter.prop('disabled', false)
                    }
                } else {
                    if($('#ruangan_id').val()) {
                        dd_dokter.prop('disabled', false)
                    }
                }
            }
        })

        if (_formPendaftaran.params == PARAM_PENUNJANG) {
            /** get kunjungan sebelumnya */
            $.ajax({
                url: '/pendaftaran/end-point/search-kunjungan-sebelumnya?pasienId=' + pasienId,
                method: 'GET',
                dataType: 'json',
                success: (data) => {
                    let result = data.results
                    if(result) {
                        let rujukDari = result.instalasi_id
                        let ddRujukDari = $('#styrujukandari_id')
                        if(rujukDari) {
                            if(I_RDRJ.includes(rujukDari)) {
                                ddRujukDari.val(1).trigger('change')
                            } else if (I_RI.includes(rujukDari)) {
                                ddRujukDari.val(3).trigger('change')
                            } else if (I_LUAR.includes(rujukDari)) {
                                ddRujukDari.val(85).trigger('change')
                            }
                            ddRujukDari.trigger("depdrop:change");
                            setTimeout(function() {
                                $('#ruangan_id').val(result.ruangan_id).trigger('change')
                                if(result.dokterpengganti_id) {
                                    $('#kunjunganform-dokterpengirim_id').val(result.dokterpengganti_id).trigger('change')
                                } else {
                                    $('#kunjunganform-dokterpengirim_id').val(result.dokterutama_id).trigger('change')
                                }
                            }, 2000);
                        }
                    }
                },
            })
            
            $(document).on('change', '#instalasi_id', function(e) {
                e.preventDefault()
                let carabayar = $('#selectCarabayar').val()
                if(R_EXCLUDE_FARMASI.includes(parseInt($(this).val()))) {
                    if(CB_EXCLUDE_FARMASI.includes(parseInt(carabayar))) {
                        return new PNotify({
                            title: "Perhatian",
                            text: "Pendaftaran Farmasi tidak bisa mendaftarkan pasien dengan cara bayar personil",
                            addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                            type: "warning"
                        });
                    }
                }

                if(R_INCLUDE_DIAGNOSA.includes(parseInt($(this).val()))) {
                    $('.diagnosa').removeClass('hidden')
                } else {
                    $('.diagnosa').addClass('hidden')
                }
            });

            if (_formPendaftaran.tipePasien.jenis_pendaftaran == 'pasien-rs') {
                $('#penanggung-jawab').attr("type", "hidden");
                $('#uniform-penanggung-jawab').attr("style", "display: none");
                $("label[for='penanggung-jawab']").attr("style", "display: none");

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
                    url: "/pendaftaran/daftar/get-dokter?param=penunjang&isSelected=0&asalrujukan_id=" + _formPendaftaran.tipePasien.asalrujukan_id
                });

                // if ($('#kunjunganform-dokterpengirim_id').data('depdrop')) { $('#kunjunganform-dokterpengirim_id').depdrop('destroy'); }
                // $('#kunjunganform-dokterpengirim_id').depdrop({
                //     allowClear: true,
                //     depends: ["ruangan_id"],
                //     placeholder: "-- Pilih --",
                //     url: "/pendaftaran/daftar/get-dokter?param=penunjang&asalrujukan_id=" + _formPendaftaran.tipePasien.asalrujukan_id
                // });
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
                    depends: ["instalasi_id"],
                    placeholder: "-- Pilih --",
                    url: "/pendaftaran/daftar/get-dokter?param=penunjang&isSelected=0&isInstalasi=true"
                });

                // if ($('#kunjunganform-dokterpengirim_id').data('depdrop')) { $('#kunjunganform-dokterpengirim_id').depdrop('destroy'); }
                // $('#kunjunganform-dokterpengirim_id').depdrop({
                //     allowClear: true,
                //     depends: ["ruangan_id"],
                //     placeholder: "-- Pilih --",
                //     url: "/pendaftaran/daftar/get-dokter?param=penunjang"
                // });

                // if ($('#kunjunganform-dokter_pengganti_id').data('depdrop')) { $('#kunjunganform-dokter_pengganti_id').depdrop('destroy'); }
                // $('#kunjunganform-dokter_pengganti_id').depdrop({
                //     allowClear: true,
                //     depends: ["ruangan_id"],
                //     placeholder: "-- Pilih --",
                //     url: "/pendaftaran/daftar/get-dokter?param=penunjang"
                // });


                $('#kunjunganform-keadaan_masuk').trigger('change');
                let _isNew = $('input[name="chk-statuspasien"]:checked').val();
                if (_isNew != 'undefined' && _isNew == 1) {
                    $('#status_kunjungan_181').prop('checked', true);
                } else {
                    $('#status_kunjungan_180').prop('checked', true);
                }
            }

            $('#kunjunganform-pegawai_id').on('depdrop:afterChange', function (event, id, value) {
                let ruangan_id = parseInt($('#ruangan_id').val());
                let dokterPengganti = $('#kunjunganform-dokter_pengganti_id').val()
                let dokter = $('#kunjunganform-pegawai_id')

                // Case ruangan lab luar
                if (DEFAULT_D_LAR.includes(ruangan_id)) {
                    $('#kunjunganform-pegawai_id').val(352).trigger("change")
                    $('#kunjunganform-pegawai_id').trigger("depdrop:change");
                }

                if(dokterPengganti) {
                    dokter.val('').trigger('change')
                    dokter.prop('disabled', true)
                } else {
                    dokter.prop('disabled', false)
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
                    $('#kunjunganform-dokter_id').depdrop({
                        allowClear: true,
                        depends: ["datetime", "ruangan_id"],
                        placeholder: "-- Pilih --",
                        url: "/pendaftaran/reservasi-poliklinik/get-dokter?is_from_reservasi=1"
                    });
                }
            }
            if (type_kunjungan_dokter == true) {
                $('#kunjunganform-dokter_id').depdrop({
                    allowClear: true,
                    depends: ["ruangan_id"],
                    placeholder: "-- Pilih --",
                    url: "/pendaftaran/daftar/get-dokter"
                });
            }


            $('#kunjunganform-keadaan_masuk').trigger('change');
        }

        $('#kunjunganform-kelaspelayanan_id').on('depdrop:afterChange', function (event, id, value) {
            //_formPendaftaran.listTarif = [];
            //_formPendaftaran.getKarcis();
            $('.btn-pemeriksaan-clear').trigger('click');
        });

        if ($('#ruangan_id').data('depdrop')) { $('#ruangan_id').depdrop('destroy'); }
        if (_formPendaftaran.params == 'penunjang') {
            $('#ruangan_id').depdrop({
                class: "select2",
                depends: ["styrujukandari_id"],
                placeholder: "",
                url: "/pendaftaran/daftar/get-ruangan"
            });
        } else {
            switch(parseInt(typeRegist)) {
                case WS_IGD:
                    refreshOptionSelect2($('#ruangan_id'), [{ "id": R_IGD, "text": "INSTALASI GAWAT DARURAT" }]);
                    $('#ruangan_id').trigger('depdrop:change');
                    break;
                default:
                    $('#ruangan_id').depdrop({
                        class: "select2",
                        depends: ["instalasi_id"],
                        placeholder: "",
                        url: "/pendaftaran/daftar/get-ruangan"
                    });
            }
        }

        if (_content.find('#instalasi_id').val()) {
            $('#instalasi_id').trigger('depdrop:change');
        }

        if (_formPendaftaran.dataAntrian) {
            _content.find('#instalasi_id').val(_formPendaftaran.dataAntrian.instalasi_id).trigger("change");
            _content.find('#instalasi_id').trigger("depdrop:change");
            _content.find('#instalasi_id').val(_formPendaftaran.dataAntrian.instalasi_id).trigger("change");
            _content.find('#ruangan_id').val(_formPendaftaran.dataAntrian.ruangan_id).trigger("change");
            _content.find('#ruangan_id').trigger("depdrop:change");
        }

        $('#kunjunganform-dokter_id').on('depdrop:afterChange', function (event, id, value) {
            let dokterPengganti = $('#kunjunganform-dokter_pengganti_id').val()
            let dokter = $('#kunjunganform-dokter_id')
            
            if (_formPendaftaran.dataAntrian) {
                setTimeout(function () {
                    $('#kunjunganform-dokter_id').val(_formPendaftaran.dataAntrian.pegawai_id).trigger("change");
                    $('#kunjunganform-dokter_id').trigger("depdrop:change");
                }, 2000);
            }

            if(dokterPengganti) {
                dokter.val('').trigger('change')
                dokter.prop('disabled', true)
            } else {
                dokter.prop('disabled', false)
            }
        });

        if (Object.keys(_formPendaftaran.pendaftaranOl).length) {
            _content.find('#ruangan_id').val(_formPendaftaran.pendaftaranOl.ruangan_id).trigger("change");
            _content.find('#ruangan_id').trigger("depdrop:change");
        }

        $("#datetime").AnyTime_noPicker();
        $("#datetime").AnyTime_picker({
            format: "%d-%m-%Y %H:%i",
        });
        $("#datetime").removeAttr('readonly')

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
            //_formPendaftaran.listTarif = [];
            //_formPendaftaran.getKarcis();
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
        $('.btn-pemeriksaan-clear').on('click', function () {
            _formPendaftaran.listPenunjang = {};
            loadpemeriksaan(_formPendaftaran.listPenunjang);
            loadpaketmcu(_formPendaftaran.listPenunjang);
        })

        if ($('#kunjunganform-nomor_urut').data('depdrop')) {
            $('#kunjunganform-nomor_urut').depdrop('destroy');
        }

        $('#kunjunganform-nomor_urut').depdrop({
            class: 'select2',
            depends: ['ruangan_id', 'kunjunganform-dokter_id'],
            url: '/pendaftaran/daftar/get-nomor-urut',
            placeholder: false
        });

        if (_formPendaftaran.params == 'ranap') {
            $('.kelas_rawat').show();
        } else {
            $('.kelas_rawat').hide();
        }

        var pendaftaran_id_hidden = $('#pendaftaran_id_hidden').val();
        if (pendaftaran_id_hidden != '') {
            //$('#kunjunganform-tgl_pendaftaran').val(_formPendaftaran.tmpKunjungan.tgl_pendaftaran).trigger('change');
            $('#ruangan_id').val(_formPendaftaran.tmpKunjungan.ruangan_id).trigger('change').trigger('depdrop:change');
            $('#kunjunganform-jeniskasuspenyakit_id').on('depdrop:afterChange', function (event, id, value) {
                $('#kunjunganform-jeniskasuspenyakit_id').val(_formPendaftaran.tmpKunjungan.jeniskasuspenyakit_id).trigger("change")
                $('#kunjunganform-jeniskasuspenyakit_id').trigger("depdrop:change");
            });
            $('#kunjunganform-kelaspelayanan_id').on('depdrop:afterChange', function (event, id, value) {
                $('#kunjunganform-kelaspelayanan_id').val(_formPendaftaran.tmpKunjungan.kelaspelayanan_id).trigger("change")
                $('#kunjunganform-kelaspelayanan_id').trigger("depdrop:change");
            });
            $('#kunjunganform-dokter_id').on('depdrop:afterChange', function (event, id, value) {
                $('#kunjunganform-dokter_id').val(_formPendaftaran.tmpKunjungan.pegawai_id).trigger("change")
                $('#kunjunganform-dokter_id').trigger("depdrop:change");
            });
            /*$('#kunjunganform-jeniskasuspenyakit_id').val(_formPendaftaran.tmpKunjungan.jeniskasuspenyakit_id).trigger('change').trigger('depdrop:change');
            $('#kunjunganform-kelaspelayanan_id').val(_formPendaftaran.tmpKunjungan.kelaspelayanan_id).trigger('change').trigger('depdrop:change');
            $('#kunjunganform-dokter_id').val(_formPendaftaran.tmpKunjungan.pegawai_id).trigger('change').trigger('depdrop:change');*/
            $('#kunjunganform-keadaan_masuk').val(_formPendaftaran.tmpKunjungan.keadaan_masuk).trigger('change').trigger('depdrop:change');
            $('#kunjunganform-keterangan').val(_formPendaftaran.tmpKunjungan.keterangan_pendaftaran);
        }

        $('input[type=text]').keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });

        $('textarea').keyup(function () {
            $(this).val($(this).val().toUpperCase());
        });
    },
    generateKunjungan: function (data) {
        var _penunjang = {
            paket: [],
            tindakan: []
        };
        $.each(data, function (x, y) {
            var _tmp = {
                is_cyto: y.is_cyto == 'false' ? 0 : 1,
                tariftindakan_id: y.tariftindakan_id
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
    getInfoPasien: function (id, noRm, getPenanggung = false) {
        $.ajax({
            type: 'GET',
            url: '/pendaftaran/daftar-' + _formPendaftaran.params + '/get-info-pasien?id=' + id + '&no_rm=' + noRm + '&param=sty',
            dataType: 'JSON',
            success: function (res) {
                var _caraBayar = $('.selectCarabayar').val();
                var _response = res.response;
                var _infoPasien = _response.info_pasien;
                var _infoKeluarga = _response.keluarga;
                if (!_infoPasien) {
                    _formPendaftaran.formPasien(2);
                    $('select').removeAttr('tabindex');
                    return true;
                }
                $("#pasien_id_hidden").val(_infoPasien.pasien_id);

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
                    $("#kunjunganform-catatanpenting_pasien").val(_infoPasien.catatanpenting_pasien);
                } else {
                    info_catatan.style.display = "none";
                    $("#kunjunganform-catatanpenting_pasien").val(null);
                }

                $('.nama-pasien').text(_infoPasien.nama_pasien);
                $('.rm-pasien').text(_infoPasien.no_rekam_medik);
                $('.nik-pasien').text(': ' + (_infoPasien.nik_pasien != null ? _infoPasien.nik_pasien : '-'));
                $('.kelamin-pasien').text(': ' + (_infoPasien.jenis_kelamin ? _infoPasien.jenis_kelamin : '-'));
                $('.tempat-lahir-pasien').text(': ' + (_infoPasien.tempat_lahir ? _infoPasien.tempat_lahir : '-'));
                $('.tanggal-lahir-pasien').text(': ' + (_infoPasien.tanggal_lahir ? convertDateByFormat(_infoPasien.tanggal_lahir, 'd m Y') : '-'));
                $('.darah-pasien').text(': ' + (_infoPasien.golongan_darah ? _infoPasien.golongan_darah : '-'));
                if (_infoKeluarga) {
                    $('.ibu-pasien').text(': ' + (_infoKeluarga.keluarga_nama ? _infoKeluarga.keluarga_nama + (_infoKeluarga.keluarga_hubungan ? ' - ' + _infoKeluarga.keluarga_hubungan : '') : '-'));
                } else {
                    $('.ibu-pasien').text(': -');
                }
                $('.tlp-pasien').text(': ' + (_infoPasien.no_telepon_pasien ? _infoPasien.no_telepon_pasien : '-'));
                $('.alamat-pasien').text(': ' + (_infoPasien.alamat_pasien ? _infoPasien.alamat_pasien : '-'));
                var _html = "";
                var countKunjungan = _response.kunjugan.length;
                if (countKunjungan === 0) {
                    $("#kunjungan").html('<p class="text-center">Pasien Belum Memiliki Riwayat</p>')
                } else {
                    $.each(_response.kunjugan, function (key, data) {
                        var _clone = _formPendaftaran.historyKunjugan;
                        _clone.find('.history-pendaftaran-id').text(data.no_pendaftaran);
                        _clone.find('.history-instalasi').text(': ' + (data.instalasi_nama ? data.instalasi_nama : '-'));
                        _clone.find('.history-ruangan').text(': ' + (data.ruangan_nama ? data.ruangan_nama : '-'));
                        _clone.find('.history-dokter').text(': ' + (data.nama_pegawai ? data.nama_pegawai : '-'));
                        _clone.find('.history-tgl-pendaftaran').text(': ' + (data.tgl_pendaftaran ? convertDateByFormat(data.tgl_pendaftaran, 'd m Y - h:i') : '-'));
                        _clone.find('.history-keluar').text(': ' + (data.tglpasienpulang ? convertDateByFormat(data.tglpasienpulang, 'd m Y - h:i') : '-'));
                        _clone.find('.history-cara-keluar').text(': ' + (data.carakeluar_nama ? data.carakeluar_nama : '-'));
                        _clone.find('.history-penanggung-biaya').text(': ' + (data.carabayar_nama ? data.carabayar_nama : '-'));
                        _html += _clone.html();
                    });
                    if (countKunjungan >= 3) {
                        var _action = $('.btn-detailss').attr('action');
                        $('.btn-detailss').show();
                        _html += '<hr><div align="center"><button type="button" class="btn btn-detailss btn-info btn-labeled btn-xs" action="/pendaftaran/daftar-igd/detail-kunjungan?no_rekam_medik=' + _infoPasien.no_rekam_medik + '" data-toggle="modal" data-target="#modal_backdrop" data-width="75%"><b><i class="fa fa-eye"></i></b>Detail Kunjungan</button></div>';
                    }
                    $('#kunjungan').html(_html);
                }

                $('#form-parent-info').show();
                $('#form-parent').removeClass("col-md-12").addClass("col-md-9");

                var url_edit_pasien = '/pendaftaran/informasi-pencarian-pasien/update?id=' + _infoPasien.encrypted_pasien_id + '&is_close=true';
                $('#btn-edit-info-pasien').attr('data-target', url_edit_pasien);

                // Change input no rm size
                $( "span[aria-labelledby='select2-no_rekam_medik-container']" ).css("max-width","215px");

                // Get all penanggung biaya from list kunjungan
                if (getPenanggung) {
                    var kunjungan = res.response.kunjugan[parseInt(res.response.kunjugan.length) - 1];
                    var asal_rujukan_id;

                    if (res.response.kunjugan !== undefined) {
                        var allKunjungan = res.response.kunjugan;
                        for (var x = 0; x < allKunjungan.length; x++) {
                            if (defaultPenBiaya[allKunjungan[x].carabayar_id] === undefined) {
                                defaultPenBiaya[allKunjungan[x].carabayar_id] = {
                                    penjamin_id: allKunjungan[x].penjamin_id,
                                    asal_rujukan_id: allKunjungan[x].asal_rujukan_id,
                                    namapemilik_asuransi: allKunjungan[x].namapemilik_asuransi,
                                    no_asuransi: allKunjungan[x].no_asuransi,
                                    nopokokperusahaan: allKunjungan[x].nopokokperusahaan,
                                    namaperusahaan: allKunjungan[x].namaperusahaan,
                                    penanggungbiaya_nama: allKunjungan[x].penanggungbiaya_nama,
                                    ruangcarabayar_id: allKunjungan[x].ruangcarabayar_id,
                                    noindukkaryawan: allKunjungan[x].noindukkaryawan,
                                    instansi: allKunjungan[x].instansi,
                                    namabagian: allKunjungan[x].namabagian,
                                };
                            }
                        }
                    }
                            
                    if (kunjungan.asalrujukan_id != null) {
                        asal_rujukan_id = kunjungan.asalrujukan_id;
                    }else {
                        asal_rujukan_id =1;
                    }
                            
                    if(kunjungan.carabayar_id != null) {
                        $('#selectCarabayar').val(kunjungan.carabayar_id).trigger('change').trigger('depdrop:change');
                    }
                            
                    $.ajax({
                        url: '/pendaftaran/daftar/get-penjamin',
                        data: {
                            depdrop_parents: [
                                $("#selectCarabayar").val()
                            ]
                        },
                        method: 'POST',
                        success: (res) => {
                            $('#penjamin_id').prop('disabled', false);
                            $("#penjamin_id").select2('destroy')
                            $("#penjamin_id").html('')
                            res.output.map(({ id, name }) => {
                                $("#penjamin_id").append(`<option value="${id}">${name}</option>`)
                            })
                            $("#penjamin_id").select2()
                            if (kunjungan.penjamin_id) {
                                $('#penjamin_id').val(kunjungan.penjamin_id).trigger('change').trigger('depdrop:change');
                                kunjungan.penjamin_id = false;
                            }
                        }
                    });
                    $('#asalrujukan_id').val(asal_rujukan_id).trigger('change').trigger('depdrop:change');
                    // $('#penjamin_id').val(kunjungan.penjamin_id).trigger('change').trigger('depdrop:change');
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
    },
    getKarcis: function () {
        var _params = {
            ruangan_id: $('.selectRuangan').val(),
            kp_id: $('.selectKp').val(),
            status: _formPendaftaran.tipePasien.tipe_pasien ? 0 : 1,
            penjamin_id: _formPendaftaran.tipePasien.penjamin_id
        };
        _params = $.param(_params);
        var tbl;
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
                                docoNotification("warning", "Peringatan!", "Tidak boleh memilih karcis konsultasi lebih dari satu.");
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
                    title: 'Karcis',
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
        var _currentLi = $('ul[role="tablist"] > li.current > a').attr("aria-controls");
        $('#' + _currentLi + ' select').val('').trigger('change');
        // tableDaftarTerakhir.draw();
        // if (_formPendaftaran.params === 'igd') {
        //     tableDaftarTerakhirIgd.draw();
        // } else {
        //     tableDaftarTerakhir.draw();
        // }
        _formPendaftaran.resetAsuransi();
        $('.field-tipepasienform-no_asuransi').hide();
        $('#selectCarabayar').focus();
        $('input[name="no_antrian"]').val("");
        $("#pendaftaran_id_hidden").val("");
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
    }
};

function validasiKunjungan(data) {
    let validate = true

    if (typeof _formPendaftaran.pendaftaranOl.buatjanjipoli_id !== 'undefined') {
        validate = false
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
        return 'Pendaftaran Konsul Poli'
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

const stateCaraBayar = function (carabayar_group) {
    var caraBayar = $('#selectCarabayar').val();
    if (carabayar_group == docoHelper.groupUmum) {
        $('.field-no_rekam_medik').show();
        $('.field-tipepasienform-no_asuransi').hide();
        $('#form-asuransi').hide();
        $('.field-asalrujukan_id').show();
        $('#form-bpjs').hide();
        $('#form-bpjs-1').hide();
        $('#form-penanggung').hide();
        $('.field-penjamin_id').show();

        if (caraBayar == 44) {
            $('#radio-penanggung_jawab').hide();
            $('#form-penanggung').show();
            $('#form-penanggung-instansi').show();
            $('#form-penanggung-noindukkaryawan').show();
            $('#form-penanggung-jpkm').hide();
            $('#div-namabagian').show();
            $('#div-ruangcarabayar_id').hide();
            $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Pegawai");
        } else if (caraBayar == 45) {
            $('#radio-penanggung_jawab').hide();
            $('#form-penanggung').show();
            $('#form-penanggung-instansi').hide();
            $('#form-penanggung-noindukkaryawan').hide();
            $('#form-penanggung-jpkm').show();
            $('#div-namabagian').hide();
            $('#div-ruangcarabayar_id').show();
            $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Nama");
        } else if (caraBayar == 41) {
            //$('.field-no_rekam_medik').show();
            $('.field-asalrujukan_id').show();
            $('#form-penanggung').show();
            $('#form-penanggung-instansi').hide();
            $('#form-penanggung-noindukkaryawan').show();
            $('#form-penanggung-jpkm').hide();
            $('#div-namabagian').hide();
            $('#div-ruangcarabayar_id').show();
            $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Nama");
        } else {
            $('#radio-penanggung_jawab').show();
            getLastPenanggung();
        }
    } else if (carabayar_group == docoHelper.groupJaminan) {
        $('.field-tipepasienform-no_asuransi').show();
        $('#form-asuransi').show();
        /*$("#form-asuransi :input").attr("disabled", true);*/
        $("#form-asuransi-namaperusahaan").show();
        $("#form-asuransi-namapemilikasuransi").show();
        $("#form-asuransi-personil").hide();
        $('.field-no_rekam_medik').show();
        $('.field-asalrujukan_id').show();
        $('#form-bpjs').hide();
        $('#form-bpjs-1').hide();
        $('#radio-penanggung_jawab').hide();
        $('#form-penanggung').hide();
        $('.field-penjamin_id').show();
    } else if (carabayar_group == docoHelper.groupBPJS) {
        switch(_formPendaftaran.params) {
            case PARAM_PENUNJANG:
                $('#jenis_rujukan').addClass('bpjs-penunjang')
                $('#form-bpjs').show();
                $('#form-bpjs-1').show();
                $('#jenis_pelayanan').val(2).trigger("change");
                $('.field-asalrujukan_id').hide();
                $('.field-tipepasienform-no_asuransi').hide();
                $('#form-asuransi').hide();
                $('#radio-penanggung_jawab').hide();
                $('#form-penanggung').hide();
                if(skipBpjs == true) {
                    $('.field-no_rekam_medik').show();
                    $("input[name='BpjsNewForm[jenis_rujukan]'][value=2]").prop("checked", true);
                    $("input[name='BpjsNewForm[jenis_rujukan]'][value=2]").closest("span").addClass("checked");
                    $("input[name='BpjsNewForm[jenis_rujukan]'][value=1]").closest("label").addClass("hidden");
                    $("input[name='BpjsNewForm[jenis_kartu]'][value=1]").closest("span").addClass("checked");
                    $("input[name='BpjsNewForm[jenis_kartu]'][value=2]").closest("label").addClass("hidden");
                    $("input[name='BpjsNewForm[jenis_rujukan]'][value=2]").trigger('change');
                    $("input[name='BpjsNewForm[jenis_kartu]'][value=2]").trigger('change');
                } else {
                    $('.field-no_rekam_medik').hide();
                }
                break;
            default:
                $('#jenis_rujukan').removeClass('bpjs-penunjang')
                $('#form-bpjs').show();
                $('#form-bpjs-1').show();
                $('#jenis_pelayanan').val(2).trigger("change");
                $('.field-asalrujukan_id').hide();
                $('.field-tipepasienform-no_asuransi').hide();
                $('#form-asuransi').hide();
                $('#radio-penanggung_jawab').hide();
                $('#form-penanggung').hide();
                $('.field-no_rekam_medik').hide();
        }
    } else {
        $('#form-bpjs').hide();
        $('#form-bpjs-1').hide();
        $('.field-tipepasienform-no_asuransi').hide();
        $('#form-asuransi').hide();
        $('#radio-penanggung_jawab').hide();
        $('#form-penanggung').hide();
        $('.field-no_rekam_medik').show();
        $('.field-asalrujukan_id').show();
        $('.field-penjamin_id').show();

        //RK-Karyawan
        if (caraBayar == 44) {
            $('#radio-penanggung_jawab').hide();
            $('#form-penanggung').show();
            $('#form-penanggung-instansi').show();
            $('#form-penanggung-noindukkaryawan').show();
            $('#form-penanggung-jpkm').hide();
            $('#div-namabagian').show();
            $('#div-ruangcarabayar_id').hide();
            $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Pegawai");
        } else if (caraBayar == 45) {
            $('#radio-penanggung_jawab').hide();
            $('#form-penanggung').show();
            $('#form-penanggung-instansi').hide();
            $('#form-penanggung-noindukkaryawan').hide();
            $('#form-penanggung-jpkm').show();
            $('#div-namabagian').hide();
            $('#div-ruangcarabayar_id').show();
            $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Nama");
        } else if (caraBayar == 41) {
            //$('.field-no_rekam_medik').show();
            $('.field-asalrujukan_id').show();
            $('#form-penanggung').show();
            $('#form-penanggung-instansi').hide();
            $('#form-penanggung-noindukkaryawan').show();
            $('#form-penanggung-jpkm').hide();
            $('#div-namabagian').hide();
            $('#div-ruangcarabayar_id').show();
            $("label[for='penanggungbiayaform-penanggungbiaya_nama']").text("Nama");
        }
    }
    //$('input[name="chk-statuspasien"]').prop("checked", true);
    //$(".field-no_rekam_medik").addClass('required');
    //$('#no_rekam_medik').val('').trigger('change');
    $("#note-asuransi").html("");
    $("#tipepasienform-no_asuransi").val("").trigger("change");
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

    formData.append("TipePasienForm[upload_file]", $("#file-upload")[0].files[0]);
    $.ajax({
        type: "post",
        dataType: false,
        cache: false,
        contentType: false,
        processData: false,
        url: "/pendaftaran/daftar/upload-template",
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
                dataPasienMcu = data
                _formPendaftaran.tmpDataMcu.dataComplete = complete
                _formPendaftaran.tmpDataMcu.dataIncomplete = incomplete
                _formPendaftaran.tmpDataMcu.dataFile = nama_file
                _formPendaftaran.tmpDataMcu.dataDoubleRm = dobleRm
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

            setTimeout(function () {
                $('#kunjunganform-dokter_id').val(_penOl.dokter_id).trigger('change');
                $('input[name="KunjunganForm[tgl_pendaftaran]"]').val(_penOl.all.tgl_pendaftaran).trigger('change');
            }, 1000);

            setTimeout(function () {
                $('#kunjunganform-dokter_id').val(_penOl.dokter_id).trigger('change');
                $('#kunjunganform-dokter_id').trigger('depdrop:change');
            }, 2000);
        }
    }
}

function getListDokterAll() {
    if ($('#kunjunganform-dokter_pengganti_id').hasClass("select2-hidden-accessible")) {
        $('#kunjunganform-dokter_pengganti_id').select2('destroy');
    }
    if ($('#kunjunganform-dokterpengirim_id').hasClass("select2-hidden-accessible")) {
        $('#kunjunganform-dokterpengirim_id').select2('destroy');
    }
    $.ajax({
        url: '/pendaftaran/end-point/search-dokter',
        method: 'GET',
        dataType: 'json',
        success: (data) => {
            const dataDropDownDokter = []
            var placeholder = { id: "", text: "-- Pilih --" }
            dataDropDownDokter.push(placeholder);

            for (var i = 0; i < data.results.length; i++) {
                dataDropDownDokter.push(data.results[i]);
            }
            refreshOptionSelect2($('#kunjunganform-dokter_pengganti_id'), dataDropDownDokter)
            refreshOptionSelect2($('#kunjunganform-dokterpengirim_id'), dataDropDownDokter)
        },
        complete: () => {
            $('#kunjunganform-dokter_pengganti_id').prop('disabled', false)
            $('#kunjunganform-dokterpengirim_id').prop('disabled', false)
        }
    })
}

function getLastPenanggung() {
    var pasien_id = $("#pasien_id_hidden").val();
    var no_rekam_medik = $('#no_rekam_medik').val();
    if (pasien_id != '' && pasien_id != 'undefined' && no_rekam_medik != '' && no_rekam_medik != 'undefined') {
        var pengantar_lainnya = '992';
        var pengantar_ortu = '991';
        $.ajax({
            url: '/pendaftaran/end-point/get-last-penanggung?pasien_id=' + pasien_id,
            method: 'GET',
            dataType: 'json',
            success: (data) => {
                var result = data.results;
                if(result){
                    if(result.pengantar == pengantar_ortu) {
                        $("input[name='PjpasienForm[pj_pengantar]']").closest('span').removeClass('checked')
                        $("input[name='PjpasienForm[pj_pengantar]']").closest('div').removeClass('checked')
                        $("input[name='PjpasienForm[pj_pengantar]'][value=991]").prop('checked', true).trigger('change');
                        $("input[name='PjpasienForm[pj_pengantar]'][value=991]").closest('span').addClass('checked')
                        $("input[name='PjpasienForm[pj_pengantar]'][value=991]").closest('div').addClass('checked')
                    } else if(result.pengantar == pengantar_lainnya) {
                        $("input[name='PjpasienForm[pj_pengantar]']").closest('span').removeClass('checked')
                        $("input[name='PjpasienForm[pj_pengantar]']").closest('div').removeClass('checked')
                        $("input[name='PjpasienForm[pj_pengantar]'][value=992]").prop('checked', true).trigger('change');
                        $("input[name='PjpasienForm[pj_pengantar]'][value=992]").closest('span').addClass('checked')
                        $("input[name='PjpasienForm[pj_pengantar]'][value=992]").closest('div').addClass('checked')

                        // Update form penanggung jawab
                        var _content = $('#form-pasien');
                        _content.find('#form-tipepasien-nama_pasien').val(result.penanggungjawab_nama);
                        _content.find('#form-tipepasien-namadepan').val(result.pj_namadepan).trigger('change');
                        _content.find('#form-tipepasien-propinsi_id').val(result.pj_propinsi_id).trigger('change');
                        _content.find('#form-tipepasien-kabupaten_id').val(result.pj_kabupaten_id).trigger('change');
                        _content.find('#form-tipepasien-kecamatan_id').val(result.pj_kecamatan_id).trigger('change').trigger('depdrop:change');
                        _content.find('#form-tipepasien-kelurahan_id').val(result.pj_kelurahan_id).trigger('change').trigger('depdrop:change');
                        _content.find('#form-tipepasien-rt').val(result.pj_rt);
                        _content.find('#form-tipepasien-rw').val(result.pj_rw);
                        _content.find('#form-tipepasien-alamat_pasien').val(result.penanggungjawab_alamat);
                        _content.find('#form-tipepasien-no_telepon_pasien').val(result.penanggungjawab_notelp);
                        _content.find('#form-tipepasien-pekerjaan_id').val(result.pj_pekerjaan_id).trigger('change');
                        $("input[name='PjpasienForm[pj_jk]']").closest('span').removeClass('checked')
                        $("input[name='PjpasienForm[pj_jk]']").closest('div').removeClass('checked')
                        $("input[name='PjpasienForm[pj_jk]'][value="+result.penanggungjawab_jeniskelamin+"]").prop('checked', true).trigger('change');
                        $("input[name='PjpasienForm[pj_jk]'][value="+result.penanggungjawab_jeniskelamin+"]").closest('span').addClass('checked')
                        $("input[name='PjpasienForm[pj_jk]'][value="+result.penanggungjawab_jeniskelamin+"]").closest('div').addClass('checked')
                    }
                }
            }
        })
    }
}

function modalTujuanKunjunganBpjs(url) {
    $('#modal_backdrop').modal('hide');

    setTimeout(function () {
        $('.btn-tujuan-kunjungan').attr('action', url)
        $('.btn-tujuan-kunjungan').trigger('click');
    }, 1000)
}

function changeFormatDate(date){
    var dates = new Date(date);
    var newDate = dates.getDate();
    var newMonth = dates.getMonth();
    var newYear = dates.getFullYear();
    
    var newDates = (newDate < 10 ? '0' : '')+newDate+'-'+ (newMonth < 10 ? '0' : '')+newMonth+'-'+newYear;
    return newDates;
}