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
    'id' => 'form',
    'enableClientValidation' => false,
    'enableAjaxValidation' => false
]) ?>
<div class="panel panel-white">
    <div class="panel-heading">
        <div class="row">
                <h5 class="panel-title">
                    <b>Formulir Rencana Kontrol/Inap</b>
                </h5>
        </div>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'tgl_rencanakontrol', [
                            'addon' => [
                                'append' => [
                                    'content' => '<i class="fa fa-calendar"></i>'
                                ]
                            ]
                        ])->textInput([
                            'id' => 'tgl_rencanakontrol',
                            'class' => 'form-control input-sm pickadate-w-month',
                            'placeholder' => $model->getAttributeLabel('tgl_rencanakontrol'),
                            'autocomplete' => 'off'
                        ])->label($model->getAttributeLabel('tgl_rencanakontrol')) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'nama_spesialis', [
                            'horizontalCssClasses' => [
                                'label' => 'col-md-4',
                                'wrapper' => 'col-md-8'
                            ],
                            'addon' => [
                                'append' => [
                                    'content' => Html::a('<i class="fa fa-hospital-o "></i>',null, [
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal_pencarian_spesialis',
                                        'data-width' => '1000px',
                                        'data-popup' => "tooltip",
                                        'id' => 'btn-pencarian-spesialis',
                                        'action' =>'/pendaftaran/rencana-kontrol-inap/modal-pencarian-spesialis',
                                        'title' => Yii::t("fe","Pencarian Spesialis")
                                    ])
                                ],
                                'class' => 'asdasd'
                            ]
                        ])->textInput([
                            'id' => 'nama_spesialis',
                            'class' => 'form-control',
                            'placeholder' => $model->getAttributeLabel('nama_spesialis'),
                            'readonly' => true
                        ])->label($model->getAttributeLabel('nama_spesialis')) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'jenis_pelayanan_nama')->dropDownList([
                                Yii::t('fe', 'Rawat Jalan') => Yii::t('fe', 'Rawat Jalan'),
                                Yii::t('fe', 'Rawat Inap') => Yii::t('fe', 'Rawat Inap'),
                            ], [
                                'id' => 'jenis_pelayanan_nama',
                                'class' => 'select2',
                                'disabled' => true
                            ]
                        )->label($model->getAttributeLabel('jenis_pelayanan_nama')) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'dokterdpjp_nama')->textInput([
                            'id' => 'dokterdpjp_nama',
                            'class' => 'form-control',
                            'placeholder' => $model->getAttributeLabel('dokterdpjp_nama'),
                            'readonly' => true
                        ])->label($model->getAttributeLabel('dokterdpjp_nama')) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 no-surat-kontrol">
                        <?= $form->field($model, 'nosuratkontrol')->textInput([
                            'id' => 'nosuratkontrol',
                            'class' => 'form-control',
                            'placeholder' => $model->getAttributeLabel('nosuratkontrol'),
                            'readonly' => true
                        ])->label($model->getAttributeLabel('nosuratkontrol')) ?>
                    </div>
                    <div class="col-md-6 no-spri" style="display: none;">
                        <?= $form->field($model, 'no_spri')->textInput([
                            'id' => 'no_spri',
                            'class' => 'form-control',
                            'placeholder' => $model->getAttributeLabel('no_spri'),
                            'readonly' => true
                        ])->label($model->getAttributeLabel('no_spri')) ?>
                    </div>
                </div>
                <?= Html::activeHiddenInput($model, 'pendaftaran_id', ['id' => 'pendaftaran_id']); ?>
                <?= Html::activeHiddenInput($model, 'bpjs_id', ['id' => 'bpjs_id']); ?>
                <?= Html::activeHiddenInput($model, 'jenis_rencana', ['id' => 'jenis_rencana']); ?>
                <?= Html::activeHiddenInput($model, 'jenis_pelayanan', ['id' => 'jenis_pelayanan']); ?>
                <?= Html::activeHiddenInput($model, 'no_sep', ['id' => 'no_sep']); ?>
                <?= Html::activeHiddenInput($model, 'no_kartu', ['id' => 'no_kartu']); ?>
                <?= Html::activeHiddenInput($model, 'kode_poli', ['id' => 'kode_poli']); ?>
                <?= Html::activeHiddenInput($model, 'nama', ['id' => 'nama']); ?>
                <?= Html::activeHiddenInput($model, 'dokterdpjp_kode', ['id' => 'dokterdpjp_kode']); ?>
            </div>
        </div>
        <div class="row mt-10 mb-10">
            <div class="col-md-6"></div>
            <div class="col-md-6">
                <button type="button" class="btn btn-secondary btn-sm btn-batal mr-10" style="width: 20%;"><b><?=Yii::t('fe','Batal')?></b></button>
                <button type="submit" class="btn btn-info btn-sm btn-simpan mr-10" style="width: 20%;"><b><?=Yii::t('fe','Simpan')?></b></button>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<div id="modal_pencarian_spesialis" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>