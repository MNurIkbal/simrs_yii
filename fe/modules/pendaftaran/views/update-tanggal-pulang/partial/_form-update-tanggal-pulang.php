<?php
    use yii\helpers\Html;
    use kartik\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin([
    'id' => 'form',
    'enableClientValidation' => false,
    'enableAjaxValidation' => false
]) ?>
<div class="panel panel-white">
    <div class="panel-body">
        <div class="row mb-10 p-10">
            <div class="col-md-12 mb-10">
                <h6><b><?= Yii::t('fe', 'Create Rencana Kontrol/Inap') ?></b></h6>
                <div class="row">
                    <div  class="col-md-5">
                        <div class="row">
                        <?= $form->field($model, 'nosep')->textInput([
                                'id' => 'nosep',
                                'class' => 'form-control',
                                'placeholder' => $model->getAttributeLabel('nosep'),
                                'readonly' => true
                            ])->label($model->getAttributeLabel('nosep')) ?>
                        </div>
                    </div>
                </div>
                            
                <div class="row">
                    <div  class="col-md-5">
                        <div class="row" id="form-tglpasienpulang" class="datepicker">
                            <?= $form->field($model, 'tglpasienpulang', [
                                'addon' => [
                                    'append' => [
                                        ['content' => '<i id="btn_addon_tglpasienpulang" class="fa fa-calendar"></i>'],
                                    ],
                                ]
                            ])->textInput([
                                'class' => 'pickadate-w-month',
                                'id' => 'tglpasienpulang',
                                'data-mask' => '99-99-9999',
                                'placeholder' => $model->getAttributeLabel('tglpasienpulang'),
                            ])->label($model->getAttributeLabel('tglpasienpulang'), ['class' => 'mt-5']) ?>
                        </div>
                        
                        <div class="row" id="form-status_pulang">
                        <?php
                            echo $form->field($model, 'status_pulang_id', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-7'
                                ]
                            ])->dropDownList($status_pulang,[
                                'class' => 'select2 selectStatusPulang',
                                'id'=>'selectStatusPulang',
                                'prompt' => '-',
                            ])->label($model->getAttributeLabel('status_pulang'));
                        ?>
                        </div>

                        <div class="row">
                            <?= $form->field($model, 'no_surat_kematian')->textInput([
                                'id' => 'no_surat_kematian',
                                'class' => 'form-control',
                                'placeholder' => $model->getAttributeLabel('no_surat_kematian'),
                                'readonly' => true
                            ])->label($model->getAttributeLabel('no_surat_kematian')) ?>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div  class="col-md-5">
                        <div class="row">
                        <?= $form->field($model, 'tgl_meninggal', [
                                'addon' => [
                                    'append' => [
                                        ['content' => '<i id="btn_addon_tgl_meninggal" class="fa fa-calendar"></i>'],
                                    ],
                                ]
                            ])->textInput([
                                'class' => 'pickadate-w-month',
                                'readonly' => true,
                                'id' => 'tgl_meninggal',
                                'data-mask' => '99-99-9999',
                                'placeholder' => $model->getAttributeLabel('tgl_meninggal'),
                            ])->label($model->getAttributeLabel('tgl_meninggal'), ['class' => 'mt-5']) ?>
                        </div>

                        <div class="row">
                        <?= $form->field($model, 'no_up_manual')->textInput([
                                'id' => 'no_up_manual',
                                'class' => 'form-control',
                                'placeholder' => $model->getAttributeLabel('no_up_manual')
                            ])->label($model->getAttributeLabel('no_up_manual')) ?>
                        </div>
                        <div class="row">
                        <?= Html::activeHiddenInput($model, 'pasienadmisi_id', ['id' => 'pasienadmisi_id']); ?>
                        <?= Html::activeHiddenInput($model, 'pasienpulang_id', ['id' => 'pasienpulang_id']); ?>
                        <?= Html::activeHiddenInput($model, 'bpjs_id', ['id' => 'bpjs_id']); ?>
                        <?= Html::activeHiddenInput($model, 'pendaftaran_id', ['id' => 'pendaftaran_id']); ?>
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