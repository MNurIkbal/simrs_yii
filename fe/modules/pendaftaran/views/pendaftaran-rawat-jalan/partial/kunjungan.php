<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\View;
    use yii\web\JsExpression;
    use kartik\widgets\ActiveForm;
    use kartik\widgets\DepDrop;
    use kartik\select2\Select2;
?>
<?php $form = ActiveForm::begin([
    'id' => 'form-pendaftaran-rajal',
    'enableClientValidation' => false,
    'enableAjaxValidation' => false
]) ?>
<div class="panel panel-white">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <h6><b><?= Yii::t('fe', 'Tipe Pasien') ?></b></h6>
                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'carabayar_id')->dropDownList(
                        $optionsKunjungan['carabayarOptions'], [
                            'id' => 'carabayar_id',
                            'class' => 'select2'
                        ]
                    )->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'penjamin_id')->widget(DepDrop::classname(), [
                        'name' => 'penjamin_id',
                        'options' => [
                            'id' => 'penjamin_id',
                            'class' => 'form-control select2',
                            'disabled' => false,
                        ],
                        'pluginOptions' => [
                            'depends' => ['carabayar_id'],
                            'placeholder' => Yii::t('fe', 'Penjamin'),
                            'url' => Url::to(['daftar/get-penjamin'])
                        ],
                        'pluginEvents' => [
                            "depdrop:afterChange" => "function (event, id, value) {
                                if ($('#carabayar_id').is(':focus')) {
                                    $('#penjamin_id').focus();
                                }

                                if (value == 2) {
                                    $('#form-asalrujukan_id').show();
                                    $('#form-noasuransi').show();
                                    $('#form-asuransi').show();
                                    $('#form-rujukan').show();

                                    $('#form-jenisrujukan').hide();
                                    $('#form-tglsep').hide();
                                    $('#form-norujukan').hide();
                                    $('#form-jenislayanan').hide();
                                    $('#form-jeniskartu').hide();
                                    $('#form-nokartu').hide();
                                } else if (value == 6) {
                                    $('#form-jenisrujukan').show();
                                    $('#form-tglsep').show();

                                    $('#form-asal_rujukan').hide();
                                    $('#form-asalrujukan_id').hide();
                                    $('#form-jenislayanan').hide();
                                    $('#form-jeniskartu').hide();
                                    $('#form-norujukan').hide();
                                    $('#form-nokartu').hide();
                                    $('#form-noasuransi').hide();
                                    $('#form-asuransi').hide();
                                    $('#form-rujukan').hide();
                                } else {
                                    $('#form-asal_rujukan').show();

                                    $('#form-jenisrujukan').hide();
                                    $('#form-tglsep').hide();
                                    $('#form-jenislayanan').hide();
                                    $('#form-jeniskartu').hide();
                                    $('#form-norujukan').hide();
                                    $('#form-nokartu').hide();
                                    $('#form-noasuransi').hide();
                                    $('#form-asuransi').hide();
                                    $('#form-rujukan').hide();
                                }

                                getKarcis();
                            }",
                        ]
                    ])->label(false) ?>
                </div>

                <div id="form-jenisrujukan" class="col-md-3" style="display:none;">
                    <?= $form->field($modelBpjs, 'jenis_rujukan')->dropDownList([
                            '1' => Yii::t('fe', 'Rujukan'),
                            '2' => Yii::t('fe', 'Rujukan Manual / IGD'),
                        ], [
                            'id' => 'jenis_rujukan',
                            'class' => 'select2',
                        ]
                    )->label(false) ?>
                </div>

                <div id="form-tglsep" class="col-md-3" style="display:none;">
                    <?= $form->field($modelBpjs, 'tanggal_sep', [
                        'addon' => [
                            'append' => [
                                'content' => '<i class="fa fa-calendar"></i>'
                            ]
                        ]
                    ])->textInput([
                        'id' => 'tanggal_sep',
                        'class' => 'form-control input-sm',
                        'placeholder' => $modelBpjs->getAttributeLabel('tanggal_sep'),
                        'autocomplete' => 'off',
                        'readonly' => true
                    ])->label(false) ?>
                </div>

                <div id="form-asalrujukan_id" class="col-md-3" style="display:none;">
                    <?= $form->field($modelKunjungan, 'asalrujukan_id')->dropDownList(
                        $optionsKunjungan['asalRujukan'], [
                            'id'=>'asalrujukan_id',
                            'class' => 'select2',
                    ])->label(false) ?>
                </div>

                <div id="form-norujukan" class="col-md-3" style="display:none;">
                    <?= $form->field($modelBpjs, 'no_rujukan_f')->textInput([
                        'id' => 'no_rujukan_f',
                        'class' => 'form-control',
                        'placeholder' => $modelBpjs->getAttributeLabel('no_rujukan_f')
                    ])->label(false) ?>
                </div>

                <div id="form-jenislayanan" class="col-md-3" style="display:none;">
                    <?= $form->field($modelBpjs, 'jenis_pelayanan')->dropDownList(
                        [1 => 'Rawat Inap', 2 => 'Rawat Jalan'], [
                        'id' => 'jenis_pelayanan',
                        'class' => 'select2'
                    ])->label(false) ?>
                </div>

                <div id="form-jeniskartu" class="col-md-3" style="display:none;">
                    <?= $form->field($modelBpjs, 'jenis_kartu')->dropDownList(
                        [1 => 'No BPJS', 2 => 'NIK'], [
                        'id' => 'jenis_kartu',
                        'class' => 'select2'
                    ])->label(false) ?>
                </div>

                <div id="form-noasuransi" class="col-md-3" style="display:none;">
                    <?= $form->field($modelBpjs, 'no_asuransi')->textInput([
                        'id' => 'no_asuransi',
                        'class' => 'form-control',
                        'placeholder' => $modelBpjs->getAttributeLabel('no_asuransi')
                    ])->label(false) ?>
                </div>

                <div id="form-nokartu" class="col-md-3" style="display:none;">
                    <?= $form->field($modelBpjs, 'no_kartu')->textInput([
                        'id' => 'no_kartu',
                        'class' => 'form-control',
                        'placeholder' => $modelBpjs->getAttributeLabel('no_kartu')
                    ])->label(false) ?>
                </div>

            </div>
        </div>
        <div class="row" id="form-asuransi" style="display:none;">
            <div class="col-md-12">
                <h6><b><?= Yii::t('fe', 'Asuransi') ?></b></h6>
                <div class="col-md-3">
                    <?= $form->field($modelAsuransi, 'namapemilikasuransi')->textInput([
                        'id' => 'namapemilikasuransi',
                        'class' => 'form-control',
                        'placeholder' => $modelAsuransi->getAttributeLabel('namapemilikasuransi')
                    ])->label(false) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelAsuransi, 'nomorpokokperusahaan')->textInput([
                        'id' => 'nomorpokokperusahaan',
                        'class' => 'form-control',
                        'placeholder' => $modelAsuransi->getAttributeLabel('nomorpokokperusahaan')
                    ])->label(false) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelAsuransi, 'namaperusahaan')->textInput([
                        'id' => 'namaperusahaan',
                        'class' => 'form-control',
                        'placeholder' => $modelAsuransi->getAttributeLabel('namaperusahaan')
                    ])->label(false) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelAsuransi, 'kelastanggungan_id')->dropDownList(
                        ArrayHelper::map($optionsKunjungan['kelaspelayanan'], 'kelaspelayanan_id', 'kelaspelayanan_nama'), [
                            'id' => 'kelastanggungan_id',
                            'class' => 'select2',
                            'prompt' => '-'
                        ]
                    )->label(false) ?>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <?= $form->field($modelAsuransi, 'tgl_konfirmasi', [
                        'addon' => [
                            'append' => [
                                ['content' => '<i id="btn_addon_tglkonfirmasi" class="fa fa-calendar "></i>'],
                            ],
                        ]
                    ])->textInput([
                        'class' => '',
                        'id' => 'tgl_konfirmasi',
                        'data-mask' => '99-99-9999',
                        'placeholder' => $modelAsuransi->getAttributeLabel('tgl_konfirmasi')
                    ])->label(false) ?>
                    
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelAsuransi, 'status_konfirmasi')->checkbox([
                            'id' => 'status_konfirmasi'
                        ]
                    ) ?>
                </div>
            </div>
        </div>
        <div class="row" id="form-pasien" style="display:none;">
            <div class="col-md-12">
                <h6><b><?= Yii::t('fe', 'Data Pasien') ?></b></h6>
                <div class="col-md-6">
                    <div class="identitas">
                        <div class="col-md-6" style="padding-left: 0 !important; margin-left: 0 !important;">
                            <?= $form->field($modelPasien, 'jenisidentitas[]')->dropDownList(
                                ArrayHelper::map($optionsKunjungan['data_lookup']['jenis_identitas'], 'lookup_id', 'lookup_name'), [
                                'class' => 'select2 select2pasien jenis_identitas',
                                'id'=>'jenisidentitas',
                            ])->label(false) ?>
                        </div>
                        <div class="col-md-5">
                            <?= $form->field($modelPasien, 'no_identitas_pasien[]')->textInput([
                                'id' => 'no_identitas_pasien',
                                'class' => 'form-control no_identitas_pasien',
                                'placeholder' => $modelPasien->getAttributeLabel('no_identitas_pasien')
                            ])->label(false) ?>
                        </div>
                        <div class="col-md-1 form-group highlight-addon">
                            <?= Html::button('<i class="fa fa-plus"></i>', ['class' => 'btn btn-info btn-sm tambah-jenis']); ?>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'nama_pasien')->textInput([
                        'id' => 'nama_pasien',
                        'class' => 'form-control',
                        'placeholder' => $modelPasien->getAttributeLabel('nama_pasien')
                    ])->label(false) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'jeniskelamin')->dropDownList(
                        ArrayHelper::map($optionsKunjungan['data_lookup']['jenis_kelamin'], 'lookup_id', 'lookup_name'), [
                        'id' => 'jeniskelamin',
                        'class' => 'select2'
                    ])->label(false) ?>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'tempat_lahir')->textInput([
                        'id' => 'tempat_lahir',
                        'class' => 'form-control',
                        'placeholder' => $modelPasien->getAttributeLabel('tempat_lahir')
                    ])->label(false) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'tanggal_lahir', [
                        'addon' => [
                            'append' => [
                                ['content' => '<i id="btn_addon_tgllahir" class="fa fa-calendar "></i>'],
                            ],
                        ]
                    ])->textInput([
                        'class' => '',
                        'id' => 'tanggal_lahir',
                        'data-mask' => '99-99-9999',
                        'placeholder' => $modelPasien->getAttributeLabel('tanggal_lahir')
                    ])->label(false) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'no_telepon_pasien')->textInput([
                        'id' => 'no_telepon_pasien',
                        'class' => 'form-control',
                        'placeholder' => $modelPasien->getAttributeLabel('no_telepon_pasien')
                    ])->label(false) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'alamat_pasien')->textInput([
                        'id' => 'alamat_pasien',
                        'class' => 'form-control',
                        'placeholder' => $modelPasien->getAttributeLabel('alamat_pasien')
                    ])->label(false) ?>
                </div>
                <div class="col-md-12">
                    <b><i class="fa fa-chevron-down" id="advanced-option-icon"></i></b>
                    <?= Yii::t('fe', '<a href="javascript:void(0);" 
                        class="btn btn-link btn-sm"
                        id="btn-advanced-option"> 
                        Advanced Option</a>'
                    ) ?>
                </div>
            </div>
        </div>
        <div class="row" id="form-advanced-option" style="display:none;">
            <div class="col-md-12">
                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'rt')->textInput()->input('text', [
                        'placeholder' => $modelPasien->getAttributeLabel('rt')
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'rw')->textInput()->input('text', [
                        'placeholder' => $modelPasien->getAttributeLabel('rw')
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'nama_ayah')->textInput()->input('text', [
                        'placeholder' => $modelPasien->getAttributeLabel('nama_ayah')
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'nama_ibu')->textInput()->input('text', [
                        'placeholder' => $modelPasien->getAttributeLabel('nama_ibu')
                    ])->label(false) ?>
                </div>
                
            </div>
            <div class="col-md-12">

                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'anakke')->textInput()->input('text', [
                        'placeholder' => $modelPasien->getAttributeLabel('anakke')
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'jumlah_bersaudara')->textInput()->input('text', [
                        'placeholder' => $modelPasien->getAttributeLabel('jumlah_bersaudara')
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'agama')->dropDownList(
                        ArrayHelper::map(
                            $optionsKunjungan['data_lookup']['agama'],
                            'lookup_id',
                            'lookup_value'
                        ), [
                            'id' => 'agama',
                            'class' => 'select2'
                        ]
                    )->label(false) ?>
                </div>
                
                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'warga_negara')->dropDownList(
                        ArrayHelper::map(
                            $optionsKunjungan['data_lookup']['warga_negara'],
                            'lookup_id',
                            'lookup_value'
                        ), [
                            'id' => 'warga_negara',
                            'class' => 'select2'
                        ]
                    )->label(false) ?>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'pekerjaan_id')->dropDownList(
                        ArrayHelper::map(
                            $optionsKunjungan['data_master']['pekerjaan'],
                            'pekerjaan_id',
                            'pekerjaan_nama'
                        ), [
                            'id' => 'pekerjaan_id',
                            'class' => 'select2'
                        ]
                    )->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'pendidikan_id')->dropDownList(
                        ArrayHelper::map(
                            $optionsKunjungan['data_master']['pendidikan'],
                            'pendidikan_id',
                            'pendidikan_nama'
                        ), [
                            'id' => 'pendidikan_id',
                            'class' => 'select2'
                        ]
                    )->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'suku_id')->dropDownList(
                        ArrayHelper::map(
                            $optionsKunjungan['data_master']['suku'],
                            'suku_id',
                            'suku_nama'
                        ), [
                            'id' => 'suku_id',
                            'class' => 'select2'
                        ]
                    )->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelPasien, 'alamatemail')->textInput()->input('text', [
                        'placeholder' => $modelPasien->getAttributeLabel('alamatemail')
                    ])->label(false) ?>
                </div>
            </div>
        </div>
        <div class="row" id="form-rujukan" style="display:none;">
            <div class="col-md-12">
                <h6><b><?= Yii::t('fe', 'Rujukan') ?></b></h6>
                <div class="col-md-3">
                    <?= $form->field($modelRujukan, 'no_rujukan')->textInput([
                        'id' => 'no_rujukan',
                        'class' => 'form-control',
                        'placeholder' => $modelRujukan->getAttributeLabel('no_rujukan')
                    ])->label(false) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelRujukan, 'rujukandari_id')->widget(DepDrop::classname(), [
                        'name' => 'rujukandari_id',
                        'options' => [
                            'id' => 'rujukandari_id',
                            'class' => 'select2',
                            'disabled' => false
                        ],
                        'pluginOptions' => [
                            'depends' => ['asalrujukan_id'],
                            'placeholder' => $modelRujukan->getAttributeLabel('rujukandari_id'),
                            'url' => Url::to(['pendaftaran-rawat-jalan/get-rujukan-dari'])
                        ]
                    ])->label(false) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelRujukan, 'nama_perujuk')->textInput([
                        'id' => 'nama_perujuk',
                        'class' => 'form-control',
                        'placeholder' => $modelRujukan->getAttributeLabel('nama_perujuk')
                    ])->label(false) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($modelRujukan, 'tanggal_rujukan', [
                        'addon' => [
                            'append' => [
                                'id' => 'addon_tanggal_rujukan',
                                'content' => '<i class="fa fa-calendar"></i>'
                            ]
                        ]
                    ])->textInput([
                        'id' => 'tanggal_rujukan',
                        'class' => 'form-control input-sm pickadate',
                        'placeholder' => $modelRujukan->getAttributeLabel('tanggal_rujukan'),
                        'autocomplete' => 'off'
                    ])->label(false) ?>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <?= $form->field($modelRujukan, 'diagnosa_id')->widget(Select2::classname(), [
                        'initValueText' => '', // set the initial display text
                        'options' => [
                            'placeholder' => 'Diagnosa',
                            'id' => 'diagnosa_id',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Loading'; }"),
                            ],
                            'ajax' => [
                                'url' => Url::to(['get-diagnosa', 'type'=>'10']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(params) { return {search:params.term}; }')
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        ],
                    ])->label(false); ?>
                </div>
            </div>
        </div>
        <div class="row">
            
        </div>
        <div class="row">
            <div class="col-md-12">
                <h6><b><?= Yii::t('fe', 'Kunjungan') ?></b></h6>
                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'tgl_pendaftaran', [
                        'addon' => [
                            'append' => [
                                'content' => '<i class="fa fa-calendar"></i>'
                            ]
                        ]
                    ])->textInput([
                        'id' => 'tgl_pendaftaran',
                        'class' => 'form-control input-sm',
                        'placeholder' => $modelKunjungan->getAttributeLabel('tgl_pendaftaran'),
                        'autocomplete' => 'off',
                        'readonly' => true
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'ruangan_id')->dropDownList($optionsKunjungan['ruangan'], [
                        'id' => 'ruangan_id',
                        'class' => 'select2'
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'jeniskasuspenyakit_id')->widget(DepDrop::classname(), [
                        'name' => 'jeniskasuspenyakit_id',
                        'options' => [
                            'id' => 'jeniskasuspenyakit_id',
                            'class' => 'form-control select2',
                            'disabled' => false
                        ],
                        'pluginOptions' => [
                            'depends' => ['ruangan_id'],
                            'placeholder' => $modelKunjungan->getAttributeLabel('jeniskasuspenyakit_id'),
                            'url' => Url::to(['daftar/get-jenis-kasus-penyakit'])
                        ],
                        'pluginEvents'=>[
                            "depdrop:afterChange"=>"function(event, id, value) {
                                if ($('#selectCarabayar').is(':focus')) {
                                    $('#kunjunganform-jeniskasuspenyakit_id').focus();
                                }
                            }",
                        ]
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'kelaspelayanan_id')->widget(DepDrop::classname(), [
                        'name' => 'kelaspelayanan_id',
                        'options' => [
                            'id' => 'kelaspelayanan_id',
                            'class' => 'select2',
                            'disabled' => false
                        ],
                        'pluginOptions' => [
                            'depends' => ['ruangan_id'],
                            'placeholder' => $modelKunjungan->getAttributeLabel('kelaspelayanan_id'),
                            'url' => Url::to(['daftar/get-kelas-pelayanan'])
                        ],
                        'pluginEvents' => [
                            "depdrop:afterChange" => "function (event, id, value) {
                                getKarcis();
                            }",
                        ]
                    ])->label(false) ?>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'dokter_id')->widget(DepDrop::classname(), [
                        'name' => 'dokter_id',
                        'options' => [
                            'id' => 'dokter_id',
                            'class' => 'select2',
                            'disabled' => false
                        ],
                        'pluginOptions' => [
                            'depends' => ['ruangan_id'],
                            'placeholder' => $modelKunjungan->getAttributeLabel('dokter_id'),
                            'url' => Url::to(['daftar/get-dokter', 'param' => 'rajal'])
                        ]
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'keadaan_masuk')->dropDownList(ArrayHelper::map($optionsKunjungan['data_lookup']['keadaan_masuk'], 'lookup_id', 'lookup_value'), [
                        'id' => 'keadaan_masuk',
                        'class' => 'select2'
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'transportasi')->dropDownList(ArrayHelper::map($optionsKunjungan['data_lookup']['transportasi'], 'lookup_id', 'lookup_value'), [
                        'id' => 'transportasi',
                        'class' => 'select2'
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'keterangan')->textInput()->input('text', [
                        'placeholder' => $modelKunjungan->getAttributeLabel('keterangan')
                    ])->label(false) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'is_pj')->checkbox() ?>
                </div>
            </div>
        </div>
        <div id="form-pj" class="row" style="display:none;">
            <div class="col-md-12">
                <h6><b><?= Yii::t('fe', 'Penanggung Jawab') ?></b></h6>
                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'pj_pengantar')->dropDownList(ArrayHelper::map($optionsKunjungan['data_lookup']['pengantar'], 'lookup_id', 'lookup_value'), [
                        'id' => 'pj_pengantar',
                        'class' => 'select2'
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'pj_nama')->textInput()->input('text', [
                        'placeholder' => $modelKunjungan->getAttributeLabel('pj_nama')
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'pj_jk')->dropDownList(
                        ArrayHelper::map(
                            $optionsKunjungan['data_lookup']['jenis_kelamin'],
                            'lookup_id',
                            'lookup_value'
                        ), [
                            'id' => 'pj_jk',
                            'class' => 'select2'
                        ]
                    )->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'pj_hubungan')->dropDownList(
                        ArrayHelper::map(
                            $optionsKunjungan['data_lookup']['hubungan_keluarga'],
                            'lookup_id',
                            'lookup_value'
                        ), [
                            'id' => 'pj_hubungan',
                            'class' => 'select2'
                        ]
                    )->label(false) ?>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'pj_jenis_identitas')->dropDownList(ArrayHelper::map($optionsKunjungan['data_lookup']['jenis_identitas'], 'lookup_id', 'lookup_value'), [
                        'id' => 'pj_jenis_identitas',
                        'class' => 'select2'
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'pj_no_identitas')->textInput()->input('text', ['placeholder' => $modelKunjungan->getAttributeLabel('pj_no_identitas')])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'pj_tempat_lahir')->textInput()->input('text', ['placeholder' => $modelKunjungan->getAttributeLabel('pj_tempat_lahir')])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'pj_tanggal_lahir', [
                        'addon' => [
                            'append' => [
                                'id' => 'addon_pj_tanggal_lahir',
                                'content' => '<i class="fa fa-calendar"></i>'
                            ]
                        ]
                    ])->textInput([
                        'id' => 'pj_tanggal_lahir',
                        'class' => 'form-control input-sm pickadate',
                        'placeholder' => $modelKunjungan->getAttributeLabel('pj_tanggal_lahir'),
                        'autocomplete' => 'off'
                    ])->label(false) ?>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'pj_no_telepon')->textInput()->input('text', [
                        'id' => 'pj_no_telepon',
                        'placeholder' => $modelKunjungan->getAttributeLabel('pj_no_telepon')
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'pj_umur')->textInput()->input('text', [
                        'id' => 'pj_umur',
                        'placeholder' => $modelKunjungan->getAttributeLabel('pj_umur')
                    ])->label(false) ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($modelKunjungan, 'pj_alamat')->textInput()->input('text', [
                        'id' => 'pj_alamat',
                        'placeholder' => $modelKunjungan->getAttributeLabel('pj_alamat')
                    ])->label(false) ?>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top:1%;">
            <div class="col-md-12">
                <h6><b><?= Yii::t('fe', 'Karcis') ?></b></h6>
                <?= Yii::$app->controller->renderPartial('partial/karcis', []) ?>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class='text-right'>
            <?= Html::button('<b><i class="fa fa-close"></i></b>'.Yii::t('fe', 'Batal'), [
                'class' => 'btn btn-danger btn-labeled btn-xs',
                'id' => 'btn-batal'
            ]) ?>
        
            &nbsp;&nbsp;&nbsp;&nbsp;

            <?= Html::button('<b><i class="fa fa-save"></i></b>'.Yii::t('fe', 'Simpan'), [
                'class' => 'btn btn-info btn-labeled btn-xs',
                'id' => 'btn-simpan'
            ]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
    $this->registerJs(''.$this->render('../../daftar/partial/component/js/shortcut-tab.js'));
    $this->registerJs(''.$this->render('js/kunjungan.js'));
?>