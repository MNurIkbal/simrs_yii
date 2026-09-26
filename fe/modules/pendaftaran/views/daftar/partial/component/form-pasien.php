<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use kartik\widgets\DepDrop;
    use kartik\widgets\ActiveForm;
    use yii\web\View;
    use app\components\DocoConstants;
?>
<?php
    $form = ActiveForm::begin([
        'id' => 'tipe-pasien',
        'type' => ActiveForm::TYPE_VERTICAL,
        'enableClientValidation'=>false,
        'enableAjaxValidation'=>false,
        // 'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]);
?>
<div id="form-input-pasien" style="display: none;">
    <div class="form-group" id="form-pasien-content">
        <div class="div-pasien">
            <div class="row">
                <div class="col-md-6">
                    <div class="row col-md-12 identitas">
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'jenisidentitas[]')->dropDownList(ArrayHelper::map($data_lookup['jenis_identitas'], 'lookup_id', 'lookup_value'), [
                                'class' => 'select2 select2pasien jenis_identitas',
                                'id'=>'frm-pasien-jenisidentitas',
                                'prompt' => '— PILIH —',
                            ])->label(Yii::t('fe', 'Jenis Identitas')); ?>
                        </div>
                        <div class="col-sm-5">
                            <?= $form->field($modelPasien, 'no_identitas_pasien[]', [
                                'inputOptions' => [
                                    'id' => 'frm-pasien-no_identitas_pasien',
                                    'class' => 'form-control input-sm'
                                ]
                            ])->textInput(['class' => 'no_identitas_pasien']) ?>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group highlight-addon has-size-sm">
                                <label class="control-label"><b style="color:white;">Aksi</b></label>
                                <?= Html::button('+', ['class' => 'btn btn-success tambah-jenis']); ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <?php if (isset($is_hide_alias) && $is_hide_alias == true): ?>
                            <div class="col-sm-12">
                                <?= $form->field($modelPasien, 'nama_pasien', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-nama_pasien',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]); ?>
                            </div>
                        <?php else: ?>
                            <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'namadepan')->dropDownList(ArrayHelper::map($data_lookup['nama_depan'], 'lookup_id', 'lookup_value'), [
                                    'class' => 'select2 select2pasien',
                                    'id'=>'frm-pasien-namadepan',
                                    'prompt' => '— PILIH —',
                                ])->label(Yii::t('fe', 'Nama Depan')); ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'nama_pasien', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-nama_pasien',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]); ?>
                            </div>
                        <?php endif ?>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'tempat_lahir', [
                                'inputOptions' => [
                                    'id' => 'frm-pasien-tempat_lahir',
                                    'class' => 'form-control input-sm'
                                ]
                            ]) ?>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group required">
                                <label class="control-label has-star">Tanggal Lahir</label>
                                <div class="input-group inline-datepicker">
                                        <input type="text" id="frm-pasien-tanggal_lahir" class="form-control" name="PasienForm[tanggal_lahir]" data-mask="99-99-9999">
                                        <span class="input-group-addon"><i id="btn_addon_tgllahir" class="fa fa-calendar "></i></span>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-12">
                            <?= $form->field($modelPasien, 'umur', [
                                'inputOptions' => [
                                    'id' => 'frm-pasien-umur',
                                    'readonly' => true
                                ]
                            ]); ?>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'nama_panggilan', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-nama_panggilan',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]); ?>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'jeniskelamin')
                                ->radioList(
                                    ArrayHelper::map($data_lookup['jenis_kelamin'], 'lookup_id', 'lookup_value'),
                                    ['inline'=>true, 'id'=>'frm-pasien-jeniskelamin']
                                )
                                ->label(Yii::t('fe', 'Jenis Kelamin'));
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <?=
                                $form->field($modelPasien, 'is_pj', [
                                    'options' => [
                                                'tag' => false,
                                            ],
                                ])->checkbox([
                                    'label' => 'Penanggung Jawab',
                                    'value' => 1,
                                    'class' => 'styled action-checked',
                                ])->label(false);
                            ?>
                            </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-12">
                            <?= $form->field($modelPasien, 'golongandarah')
                                ->dropDownList(
                                    ArrayHelper::map($data_lookup['golongan_darah'], 'lookup_id', 'lookup_value'),
                                    [
                                        'id'=>'frm-pasien-golongandarah',
                                        'class' => 'select2',
                                        'prompt' => '-- PILIH --'
                                    ]
                                )
                                ->label(Yii::t('fe', 'Golongan Darah'));
                            ?>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-12">
                            <?= $form->field($modelPasien, 'statusperkawinan')
                                ->dropDownList(
                                    ArrayHelper::map($data_lookup['status_perkawinan'], 'lookup_id', 'lookup_value'),
                                    ['id'=>'frm-pasien-statusperkawinan', 'class'=>'select2  select2pasien', 'prompt'=>'— PILIH —']
                                )
                                ->label(Yii::t('fe', 'Status Perkawinan'))
                            ?>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-12">
                            <?= $form->field($modelPasien, 'propinsi_id')
                                ->dropDownList(
                                    ArrayHelper::map($data_master['propinsi'], 'propinsi_id', 'propinsi_nama'),
                                    [
                                        'id'=>'frm-pasien-propinsi_id',
                                        'class'=>'select2 select2pasien',
                                        'options'=>$optionsProv,
                                        'prompt'=>'— PILIH —'
                                    ]
                                )
                                ->label(Yii::t('fe', 'Propinsi'));
                            ?>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-12">
                            <?= $form->field($modelPasien, 'kabupaten_id')
                                ->dropDownList(
                                    [],
                                    ['id'=>'kabupatenForm', 'class'=>'select2 select2pasien', 'prompt'=>'— PILIH —']
                                )->label(Yii::t('fe', 'Kabupaten'))
                            ?>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-12">
                            <?= $form->field($modelPasien, 'kecamatan_id')
                                ->dropDownList(
                                    [],
                                    ['id'=>'frm-pasien-kecamatan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                                )->label(Yii::t('fe', 'Kecamatan'))
                            ?>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-12">
                            <?= $form->field($modelPasien, 'kelurahan_id')
                                ->dropDownList(
                                    [],
                                    ['id'=>'frm-pasien-kelurahan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                                )->label(Yii::t('fe', 'Kelurahan'))
                            ?>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-12">
                            <?= $form->field($modelPasien, 'alamat_pasien')->textArea([
                                'id' => 'frm-pasien-alamat_pasien'
                            ]); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row col-md-12">
                        <?= $form->field($modelPasien, 'no_telepon_pasien', [
                            'inputOptions' => ['id' => 'frm-pasien-no_telepon_pasien'],
                        ])->textInput()->input('text', ['placeholder' => 'Contoh: 08XXXXXXXXXX']); ?>
                    </div>
                    <div class="row col-md-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'rt', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-rt',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]) ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'rw', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-rw',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <?= $form->field($modelPasien, 'nama_ibu', [
                            'inputOptions' => ['id' => 'frm-pasien-nama_ibu']
                        ]) ?>
                    </div>
                    <div class="row col-md-12">
                        <?= $form->field($modelPasien, 'nama_ayah', [
                            'inputOptions' => ['id' => 'frm-pasien-nama_ayah']
                        ]) ?>
                    </div>
                    <div class="row col-md-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'anakke', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-anakke',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]) ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'jumlah_bersaudara', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-jumlah_bersaudara',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <?= $form->field($modelPasien, 'alamatemail', [
                            'inputOptions' => ['id' => 'frm-pasien-alamatemail']
                        ])->label(Yii::t('fe', 'Alamat Email')); ?>
                    </div>
                    <div class="row col-md-12">
                        <?= $form->field($modelPasien, 'pendidikan_id')
                            ->dropDownList(
                                ArrayHelper::map($data_master['pendidikan'], 'pendidikan_id', 'pendidikan_nama'),
                                ['id'=>'frm-pasien-pendidikan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                            )->label(Yii::t('fe', 'Pendidikan'))
                        ?>
                    </div>
                    <div class="row col-md-12">
                        <?= $form->field($modelPasien, 'pekerjaan_id')
                            ->dropDownList(
                                ArrayHelper::map($data_master['pekerjaan'], 'pekerjaan_id', 'pekerjaan_nama'),
                                ['id'=>'frm-pasien-pekerjaan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                            )->label(Yii::t('fe', 'Pekerjaan'))
                        ?>
                    </div>
                    <div class="row col-md-12">
                        <?= $form->field($modelPasien, 'suku_id')
                            ->dropDownList(
                                ArrayHelper::map($data_master['suku'], 'suku_id', 'suku_nama'),
                                ['id'=>'frm-pasien-suku_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                            )
                            ->label(Yii::t('fe', 'Suku'));
                        ?>
                    </div>
                    <div class="row col-md-12">
                        <?php $modelPasien->warga_negara = '308'; ?>
                        <?= $form->field($modelPasien, 'warga_negara')
                            ->dropDownList(
                                ArrayHelper::map($data_lookup['warga_negara'], 'lookup_id', 'lookup_value'),
                                [
                                    'id'=>'frm-pasien-warga_negara',
                                    'class'=>'select2 select2pasien',
                                    'prompt'=>'— PILIH —'
                                ]
                            )
                        ?>
                    </div>
                    <div class="row col-md-12">
                        <?= $form->field($modelPasien, 'agama')
                            ->dropDownList(
                                ArrayHelper::map($data_lookup['agama'], 'lookup_id', 'lookup_value'),
                                ['id'=>'frm-pasien-agama', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                            )
                        ?>
                    </div>
                    <div class="row col-md-12">
                            <?= $form->field($modelPasien, 'catatanpenting_pasien')->textArea([
                                'id' => 'frm-pasien-catatanpenting_pasien'
                            ]); ?>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <?= $form->field($modelPasien, 'additional_data')->hiddenInput(['value'=> (isset($instalasi_id)) ? $instalasi_id : ""])->label(false);?>
        <?= $form->field($modelPasien, 'is_pasienbaru')->hiddenInput(['value'=> ''])->label(false);?>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php $this->registerJs("
    var propinsi_id = '".$modelPasien->propinsi_id."';
    var kabupaten_id = '".$modelPasien->kabupaten_id."';
    var instalasiIdFormPasien = '". (!empty($instalasi_id) ? $instalasi_id : 0) ."';
    var arrayJenisIdentitas = [];
    $(document).ready(function () {
        if (instalasiIdFormPasien != 2) { // instalasi igd tidak wajib
            $('.field-frm-pasien-no_identitas_pasien').addClass('required');
        }
    });
    $(document).on('click', '.tambah-jenis', function(event) {
        var next = true;
        var html = $('.identitas:last').clone();
        arrayJenisIdentitas = [];
        html.find('span').remove();
        html.find('select').select2();
        html.find('.no_identitas_pasien').val(null);
        html.find('.tambah-jenis').html('X');
        html.find('.tambah-jenis').removeClass('btn-success');
        html.find('.tambah-jenis').addClass('btn-danger');
        html.find('.tambah-jenis').addClass('hapus-jenis');
        html.find('.tambah-jenis').removeClass('tambah-jenis');
        html.find('.tambah-jenis').prop('id', null);

        $('.no_identitas_pasien').each(function(key, obj) {
            if (!$(this).val()) {
                next = false;

                $(this).parent().addClass('has-error');
                $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;No Identitas Pasien cannot be blank.');
                $(this).parent().find('.fa').addClass('fa-exclamation-circle');

                docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
            }
        });

        $('.jenis_identitas').each(function(key, obj) {
            if (!$(this).val()) {
                next = false;

                $(this).parent().addClass('has-error');
                $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Jenis Identitas cannot be blank.');
                $(this).parent().find('.fa').addClass('fa-exclamation-circle');

                docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
            }

            arrayJenisIdentitas.push($(this).val());
        });

        if (next) {
            // Append in the last
            $('.identitas:last').after(html);
        }
    });

    $(document).on('click', '.hapus-jenis', function(event) {
        $(this).parent().parent().parent().remove();
    });

    $(document).on('change', '.jenis_identitas', function(event) {
        if (jQuery.inArray($(this).val(), arrayJenisIdentitas) !== -1) {
            $(this).val(null).trigger('change.select2');

            $(this).parent().addClass('has-error');
            $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Jenis Identitas sudah dipilih.');
            $(this).parent().find('.fa').addClass('fa-exclamation-circle');

            docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
        }
    });

    $(document).on('blur', '.no_identitas_pasien', function(event) {
        if ($(this).val()) {
            $(this).parent().removeClass('has-error');
            $(this).parent().find('.help-block').html('');
        }
    });
", View::POS_END) ?>
