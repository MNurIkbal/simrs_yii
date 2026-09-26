<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use kartik\widgets\DepDrop;
    use app\components\DocoConstants;
    use yii\helpers\ArrayHelper;
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="form-group">
        <div class="panel-button">
            <div class="form-group titipan-aps" style="display: none;">
                <input type="checkbox" name="kamarTitipan" id="kamarTitipanCheck" value="0">
                <label for="kamar_titipan" style="font-weight: bold;font-size: 15px;">Kamar Titipan</label>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="isAps" id="isApsCheck" value="0">
                <label for="is_aps" style="font-weight: bold;font-size: 15px;">APS</label>
            </div><br>
            <div class="row" id="filterHeader">
            </div>
        </div>
        <hr>
        <div class="row table-responsive">
            <div id="tableKamarWrapper" class="table-scroll">
                <table class="table table-striped table-condensed table-hover table-pilih-kamar" style="width:100%" id="tableKamar">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="80">No</th>
                            <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Kamar");?></th>
                            <th><?=\Yii::t("fe", "Kelas");?></th>
                            <th><?=\Yii::t("fe", "Harga");?></th>
                            <th><?=\Yii::t("fe", "No Tempat Tidur");?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-ban"></i> Batal Rawat Inap'), ['class' => 'btn btn bg-danger btn-sm btn-batal btn-batal-ranap']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<script type="text/javascript">
    var typeName;
    var key;

    var dataPenyakit = JSON.parse('<?php echo json_encode($jeniskasus); ?>');
    var dataKasus = JSON.parse('<?php echo json_encode($kelaspelayanan); ?>');

    var tableCekKamar;
    var isStillExistDataKamar = {
        semua: true,
        aps: true,
        titipan: true
    }
    var pageDataKamar = {
        semua: 1,
        aps: 1,
        titipan: 1
    }
    var columnGenerated = [];

    var columns = [
        {title: 'No', data: 'rowNum', searchable: false, orderable: false },
        {title: 'Jenis Kasus Penyakit', data: 'jeniskasuspenyakit_nama', searchable: false, orderable: false },
        {title: 'Ruangan', data: 'ruangan_nama', searchable: false, orderable: false},
        {title: 'Kamar', data: 'kamarruangan_nokamar', searchable: false, orderable: false },
        {title: 'Kelas', data: 'kelaspelayanan_nama', searchable: false, orderable: false },
        {title: 'Harga', data: 'harga_tariftindakan', searchable: false, orderable: false },
        {title: 'No Tempat Tidur', data: 'datakamar', searchable: false, orderable: false },
    ]

    $(document).ready(function() {
        var jenisKamar = 'semua';
        initDatatable(jenisKamar, true);
    });

    function initDatatable(jenisKamar, reinit = false) {
        typeName = jenisKamar;
        if(jenisKamar == 'titipan') {
            key = 'Titipan';
        }
        else if(jenisKamar == 'aps') {
            key = 'Aps';
        }
        else {
            key = 'Semua';
        }

        $(`#searchBtn${key}`).prop('disabled', true);
        if (reinit) {
            pageDataKamar[typeName] = 1
            paramDatatable[typeName] = {}
        }
        createHeaderDatatableKamar(typeName, key);
        setParamDatatable(typeName, key);
        var { kamar_id, status_kamar, ruangan_id, jenis_id, penjamin_id, kelas_id } = paramDatatable[typeName]
        
        if (jenisKamar == 'titipan') {
            columnGenerated = columnKamarTitipan
            tableCekKamar = $('#tableKamarTitipan')
            $('#tableKamarWrapper').hide();
            $('#tableKamarApsWrapper').hide();
            $('#filterHeaderKamarAps').hide();
            $('#filterHeaderKamarSemua').hide();
        } else if(jenisKamar == 'aps') {
            columnGenerated = columnKamarAps
            tableCekKamar = $('#tableKamarAps');
            $('#tableKamarWrapper').hide();
            $('#tableKamarTitipanWrapper').hide();
            $('#filterHeaderKamarTitipan').hide();
            $('#filterHeaderKamarSemua').hide();
        } else {
            columnGenerated = columns
            tableCekKamar = $('#tableKamar')
            $('#tableKamarTitipanWrapper').hide();
            $('#tableKamarApsWrapper').hide();
            $('#filterHeaderKamarTitipan').hide();
            $('#filterHeaderKamarAps').hide();
            $('#filterHeaderKamarSemua').show();
        }
        tableCekKamar.parent().show()
        if (pageDataKamar[typeName] === 1 || reinit) {
            tableCekKamar.find('tbody').html('')
        }
        tableCekKamar.block({
            message: null
        })
        // Append table
        var _default = {
            page: pageDataKamar[typeName], 
            gender: '' 
        }
        var content = paramDatatable[typeName];
        var _mergeObject = $.extend({}, _default, content);
        $.ajax({
            url: `${baseUrl}ranap/end-point/get-data-kamar-default`,
            data: _mergeObject,
            method: 'GET',
            beforeSend: () => {
            hideLoader()
            },
            success: (res) => {
                // Append if
                if(typeof res.data.length != 'undefined') {
                    if (res.data.length === 0 && pageDataKamar[typeName] == 1) {
                        tableCekKamar.find('tbody').html(`
                            <tr>
                            <td class="text-center" colspan="${columnGenerated.length}">Data tidak tersedia</td>
                            </tr>
                        `)
                    } else {
                        var records = res.data
                        var tbodySection = tableCekKamar.find('tbody')
                        var length = records.length
                        for (var indexRecord = 0; indexRecord < length; indexRecord++) {
                            var trHtml = '<tr>'
                            var valueOfColumn = ''
                            for (var indexColumn = 0; indexColumn < columnGenerated.length; indexColumn++) {
                                if (columnGenerated[indexColumn].data === 'rowNum') {
                                valueOfColumn = (pageDataKamar[typeName] - 1) * 10 + (indexRecord + 1)
                                } else {
                                valueOfColumn = records[indexRecord][columnGenerated[indexColumn].data]
                                }
                                trHtml += `<td ${typeof columnGenerated[indexColumn].className !== 'undefined' ? `class="${columnGenerated[indexColumn].className}"` : ''}>${valueOfColumn}</td>`
                            }
                            tbodySection.append(`${trHtml}</tr>`)
                            tbodySection.find('td').last().parent().attr('data-akomodasi', records[indexRecord]['is_akomodasi'] ? '1' : '0')
                        }
                        isStillExistDataKamar[typeName] = records.length > 10
                        pageDataKamar[typeName] += 1
                    }
                }
            },
            error: () => {
                tableCekKamar.find('tbody').html(`
                    <tr>
                    <td colspan="${columnGenerated.length}">Terjadi kesalahan</td>
                    </tr>
                `)
            },
            complete: () => {
                tableCekKamar.unblock()
                $(`#searchBtn${key}`).prop('disabled', false)
            }
        })
    }

    function createHeaderDatatableKamar(typeName, key) {
        var jenisKamar = typeName; 
        if ($(`#filterHeaderKamar${key}`).length === 0) {
            $("#filterHeader").append(`
                <div id="filterHeaderKamar${key}">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="jenis_penyakit">Jenis Penyakit</label>
                            <select name="jenisPenyakit" id="jenisPenyakitFilter${key}" class="form-control" placeholder="Semuaxxcc"></select>
                            <option></option>
                        </div>
                    </div>
                    <div class="col-md-3 filterKamarSection">
                        <div class="form-group">
                            <label for="jenis_penyakit">Kelas</label>
                            <select name="jenisPenyakit" id="kelasFilter${key}" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="jenis_penyakit">Ruangan</label>
                            <select name="jenisPenyakit" id="ruanganFilter${key}" class="form-control"></select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="jenis_penyakit">Kamar</label>
                            <select name="jenisPenyakit" id="kamarFilter${key}" class="form-control"></select>
                            
                        </div>  
                    </div>
                    <div class="col-sm-12 button-search-section">
                        <button type="button" id="searchBtn${key}" class="btn btn-info btn-sm btn-labeled pull-right"><b class="fa fa-lg fa-search"></b> Cari</button>
                    </div>
                </div>`);
            
            initFilter(key);
            
            $(`#kelasFilter${key}`).val(table.row(".selected").data().kelaspelayanan_id).trigger('change');
            $(`#jenisPenyakitFilter${key}`).val(table.row(".selected").data().jeniskasuspenyakit_id).trigger('change');
            $(`#kamarFilter${key}`).prop('disabled', true)
            $(`#ruanganFilter${key}`).prop('disabled', true)
            $(`#ruanganFilter${key}`).bind('change', ({ delegateTarget }) => {
                if ($(delegateTarget).val() === '' || $(delegateTarget).val() === '-' || $(delegateTarget).val() === 'Semua' || $(delegateTarget).val() == null) {
                    $(`#kamarFilter${key}`).prop('disabled', true)
                    $(`#kamarFilter${key}`).val('Semua').trigger('change')
                    return false
                }
                if ($(`#kamarFilter${key}`).hasClass("select2-hidden-accessible")) {
                    $(`#kamarFilter${key}`).select2('destroy')
                    $(`#kamarFilter${key}`).html('')
                }
                $.ajax({
                    url: `daftar/kamar-ruangan`,
                    data: {
                        ruanganId: $(delegateTarget).val(),
                        kelasPelayananId: $(`#kelasFilter${key}`).val(),
                    },
                    success: (res) => {
                        var dataKamar = []
                        res.data.map((item) => {
                            dataKamar.push({
                                id: item.kamarruangan_id,
                                text: item.kamarruangan_nokamar,
                            })
                        })
                        $(`#kamarFilter${key}`).prop('disabled', false)
                        $(`#kamarFilter${key}`).select2({
                            data: dataKamar
                        });
                        var optionKamar = new Option('Semua', '', true, true);
                        $(`#kamarFilter${key}`).prepend(optionKamar).trigger('change');
                    }
                })
            })
            $(`#jenisPenyakitFilter${key}`).bind('change', ({ delegateTarget }) => {
                initRuanganSource(key)
            })
            $(`#kelasFilter${key}`).bind('change', ({ delegateTarget }) => {
                initRuanganSource(key)
            })
            if (key == 'Semua') {
                $(`#jenisPenyakitFilter${key}`).val($("#jeniskasuspenyakit_id").val()).trigger('change')
            }
            $(`#searchBtn${key}`).bind('click', ({ delegateTarget }) => {
                initDatatable(jenisKamar, true)
            });
        }
    }

    function initRuanganSource(key) {
        if ($(`#jenisPenyakitFilter${key}`).val() === '' || $(`#jenisPenyakitFilter${key}`).val().toLowerCase() === 'semua') {
            $(`#ruanganFilter${key}`).prop('disabled', true)
            $(`#ruanganFilter${key}`).val('Semua').trigger('change')
            return false
        }

        if ($(`#ruanganFilter${key}`).hasClass("select2-hidden-accessible")) {
            $(`#ruanganFilter${key}`).select2('destroy')
            $(`#ruanganFilter${key}`).html('')
        }
        $.ajax({
            url: `daftar/get-list-ruangan-kelas?instalasi_id=3`,
            method: 'POST',
            data: {
                jeniskasuspenyakit_id: $(`#jenisPenyakitFilter${key}`).val(),
                kelaspelayanan_id: $(`#kelasFilter${key}`).val(),
            },
            success: (res) => {
                $(`#ruanganFilter${key}`).prop('disabled', false)
                var dataOutput = JSON.parse(res).output
                var dataRuangan = []
                dataOutput.map(({ id, name }) => {
                    dataRuangan.push({
                        id,
                        text: name
                    })
                })
                $(`#ruanganFilter${key}`).select2({
                    data: dataRuangan,
                });
                var optionRuangan = new Option('Semua', '', true, true);
                $(`#ruanganFilter${key}`).prepend(optionRuangan).trigger('change');
            },
            error: () => {
                $(`#ruanganFilter${key}`).select2({
                    data: []
                })
            },
            complete: () => {
                $("#kamarFilter").prop('disabled', true)
                hideLoader()
            }
        })
    }

    function initFilter(key) {
        var dataKasusArray = []
        var dataJenisPenyakit = []

        Object.keys(dataKasus).map((item) => {
            dataKasusArray.push({
                id: item,
                text: dataKasus[item]
            })
        })

        Object.keys(dataPenyakit).map((item) => {
            dataJenisPenyakit.push({
                id: item,
                text: dataPenyakit[item]
            });
        });

        $(`#jenisPenyakitFilter${key}`).select2({
            data: dataJenisPenyakit,
        })
        
        $(`#kelasFilter${key}`).select2({
            data: dataKasusArray,
        });

        var optionJenisPenyakit = new Option('Semua', '', true, true);
        $(`#jenisPenyakitFilter${key}`).prepend(optionJenisPenyakit).trigger('change');

        var optionKelas = new Option('Semua', '', true, true);
        $(`#kelasFilter${key}`).prepend(optionKelas).trigger('change');
    }
    var paramDatatable = {
        'titipan': {},
        'aps': {},
        'general': {},
    }
    function setParamDatatable(typeName, key) {
        if (Object.keys(paramDatatable[typeName]).length == 0) {
            paramDatatable[typeName] = {
                penjamin_id: $("#penjamin_id").val(),
                kamar_id: typeof $(`#kamarFilter${key}`) !== 'undefined' && $(`#kamarFilter${key}`).val() !== null ? $(`#kamarFilter${key}`).val() : '',
                status_kamar: typeof $(`#statusKamarFilter${key}`) !== 'undefined' && $(`#statusKamarFilter${key}`).val() !== null ? $(`#statusKamarFilter${key}`).val() : '',
                ruangan_id: typeof $(`#ruanganFilter${key}`) !== 'undefined' && $(`#ruanganFilter${key}`).val() !== null ? $(`#ruanganFilter${key}`).val() : '',
                jenis_id: typeof $(`#jenisPenyakitFilter${key}`) !== 'undefined' && $(`#jenisPenyakitFilter${key}`).val() !== null ? $(`#jenisPenyakitFilter${key}`).val() : '',
                kelas_id: typeof $(`#kelasFilter${key}`) !== 'undefined' && $(`#kelasFilter${key}`).val() !== null ? $(`#kelasFilter${key}`).val() : ''
            }
        }
    }

    var scrollWrapper = document.querySelector('#tableKamarWrapper');

    scrollWrapper.addEventListener('scroll', function () {
        if (scrollWrapper.scrollTop + scrollWrapper.clientHeight >= scrollWrapper.scrollHeight && isStillExistDataKamar.general) {
            var jenisKamar = 'semua';
            initDatatable(jenisKamar);
        }
    });

    var activeTable = 'kamar';

    function pilihKamar(identifier) {
        let link = document.URL;
        let patternAction = link.match(/pemesanan-kamar/g);
        const jenisTempatTidur = $(identifier).data('kettempattidur_id');
        const kamarruangan_jenis = $(identifier).data('kamarruangan_jenis');
        const kamarruangan_id = $(identifier).data('kamarruangan_id');

        var jk_kamar;
        var allow_jk;
        var jk = table.row(".selected").data().jeniskelamin_id;
        var attr = $(identifier).data('allow_jk');
        
        if (typeof attr !== typeof undefined && attr !== false) {
            allow_jk = $(identifier).data('allow_jk');
        }

        if (parseInt(jenisTempatTidur) == 1) {
            jk_kamar = 16;
        } else if (parseInt(jenisTempatTidur) == 2) {
            jk_kamar = 15;
        } else {
            jk_kamar = jk;
        }

        if (allow_jk && allow_jk != parseInt(jk)) {
            docoNotification('error', i18next.t('Perhatian'), i18next.t('Kamar fleksibel tidak sesuai dengan jenis kelamin'));
            return false;
        }

        if (jk != jk_kamar) {
            docoNotification('error', i18next.t('Perhatian'), i18next.t('Kamar tidak sesuai dengan jenis kelamin'));
            return false;
        }
        $.ajax({
            url: '/ranap/end-point/cek-ruangan-default',
            data: {
                ruangan_id: $(identifier).data('ruangan_id'),
                kelaspelayanan_id: $(identifier).data('kelaspelayanan_id'),
                penjamin_id: table.row(".selected").data().penjamin_id,
                kamarruanganId: kamarruangan_id
            },
            method: 'POST',
            success: () => {
                var _data = [];
                _data.push({
                    name: 'KetersediaanKamarForm[pendaftaran_id]',
                    value: table.row(".selected").data().pendaftaran_id
                });
                _data.push({
                    name: 'KetersediaanKamarForm[pasien_id]',
                    value: table.row(".selected").data().pasien_id
                });
                _data.push({
                    name: 'KetersediaanKamarForm[ruangan_id]',
                    value: $(identifier).data('ruangan_id')
                });
                _data.push({
                    name: 'KetersediaanKamarForm[kelaspelayanan_id]',
                    value: $(identifier).data('kelaspelayanan_id')
                });
                _data.push({
                    name: 'KetersediaanKamarForm[kamarruangan_id]',
                    value: kamarruangan_id
                });
                _data.push({
                    name: 'KetersediaanKamarForm[kamartempattidur_id]',
                    value: $(identifier).data('kamartempattidur_id')
                });

                saveKetersediaanKamar(_data);
            },
            error: ({ responseJSON }) => {
                docoNotification('error', responseJSON.meta.message, '')
            },
            complete: () => {
                hideLoader()
                $.unblockUI()
            }
        })
    }

    function saveKetersediaanKamar(dataPost) {
        $.ajax({
            url: '/pendaftaran/informasi-pasien-rujuk-ranap/ketersediaan-kamar',
            data: dataPost,
            method: 'POST',
            success: () => {
                $("#modal_backdrop").modal("toggle");
                var interval = setInterval(function() {
                    table.draw();
                    clearInterval(interval);
                },2000);
            },
            error: ({ responseJSON }) => {
                docoNotification('error', responseJSON.meta.message, '')
            },
        })
    }

    $(".btn-batal-ranap").on("click", function(e) {
        var _data = [];
        _data.push({
            name: 'pendaftaran_id',
            value: table.row(".selected").data().pendaftaran_id
        });

        $.ajax({
            url: '/pendaftaran/informasi-pasien-rujuk-ranap/batal-ranap',
            data: _data,
            method: 'POST',
            success: () => {
                $("#modal_backdrop").modal("toggle");
                var interval = setInterval(function() {
                    table.draw();
                    clearInterval(interval);
                },2000);
            },
            error: ({ responseJSON }) => {
                docoNotification('error', responseJSON.meta.message, '')
            },
        })
    });
</script>
