<?php
    use yii\helpers\Html;
    use kartik\widgets\ActiveForm;
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
                <h6><b><?= Yii::t('fe', 'Create Rencana Kontrol/Inap') ?></b></h6>
                <div class="row">
                    <div  class="col-md-5" style="">
                        <div class="row" id="form-tgl_rencanakontrol" class="datepicker">
                            <?= $form->field($data, 'tgl_rencanakontrol', [
                                'addon' => [
                                    'append' => [
                                        ['content' => '<i id="btn_addon_tgl_rencanakontrol" class="fa fa-calendar"></i>'],
                                    ],
                                ]
                            ])->textInput([
                                'class' => 'pickadate-w-month',
                                'id' => 'tgl_rencanakontrol',
                                'data-mask' => '99-99-9999',
                                'placeholder' => $data->getAttributeLabel('tgl_rencanakontrol'),
                                'value' => date('d-m-Y'),
                            ])->label($data->getAttributeLabel('tgl_rencanakontrol'), ['class' => 'mt-5']) ?>
                        </div>
                        
                        <div class="row" id="form-jenis_pelayanan">
                        <?= $form->field($data, 'jenis_pelayanan')->dropDownList([
                                Yii::t('fe', 'Rawat Jalan') => Yii::t('fe', 'Rawat Jalan'),
                                Yii::t('fe', 'Rawat Inap') => Yii::t('fe', 'Rawat Inap'),
                            ], [
                                'id' => 'jenis_pelayanan',
                                'class' => 'select2',
                            ])->label($data->getAttributeLabel('jenis_pelayanan'), ['class' => 'mt-5']) ?>
                        </div>

                    </div>
                    <div class="col-md-1"></div>
                    <div  class="col-md-5" style="">
                        <div class="row">
                        <?= $form->field($data, 'nama_spesialis', [
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
                            'placeholder' => $data->getAttributeLabel('nama_spesialis'),
                            'readonly' => true
                        ])->label($data->getAttributeLabel('nama_spesialis')) ?>
                        </div>
                        <div class="row">
                        <?= $form->field($data, 'dokterdpjp_nama')->textInput([
                            'id' => 'dokterdpjp_nama',
                            'class' => 'form-control',
                            'placeholder' => $data->getAttributeLabel('dokterdpjp_nama'),
                            'readonly' => true
                        ])->label($data->getAttributeLabel('dokterdpjp_nama')) ?>
                        </div>

                        <?= Html::activeHiddenInput($data, 'kode_poli', ['id' => 'kode_poli']); ?>
                        <?= Html::activeHiddenInput($data, 'dokterdpjp_kode', ['id' => 'dokterdpjp_kode']); ?>
                        <?= Html::activeHiddenInput($data, 'bpjs_id', ['id' => 'bpjs_id']); ?>
                        <?= Html::activeHiddenInput($data, 'pendaftaran_id', ['id' => 'pendaftaran_id']); ?>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="row mt-10 mb-10">
            <div class="col-md-6"></div>
            <div class="col-md-6">
            <button type="button" class="btn btn-secondary btn-sm btn-hapus mr-10" style="width: 20%;"><b><?=Yii::t('fe','Batal')?></b></button>
            <button type="submit" class="btn btn-info btn-sm btn-simpan mr-10" style="width: 20%;"><b><?=Yii::t('fe','Simpan')?></b></button>
            </div>
            
        </div>
</div>
<?php ActiveForm::end(); ?>

<div id="modal_pencarian_spesialis" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>