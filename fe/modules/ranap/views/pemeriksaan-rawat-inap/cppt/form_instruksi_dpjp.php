<?php

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
?>

<style>
 .ruangan-kamar {
     width: 605px;
     margin-left: -9px;
 }
</style>

<div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title"><?= Yii::t('fe', 'Instruksi DPJP') ?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-toolbar clearfix">
        <?= DocoHelpers::generateToolbar([
            'custom-back' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Kembali'),
                'icon' => 'fa fa-arrow-left',
                'attributes' => [
                    'id' => 'btn-back-instruksi-dpjp',
                    'onclick' => 'kembaliInstruksiDpjp()',
                ],
            ],
            'custom-save' => [
                'type' => 'submit',
                'title' => Yii::t('fe', 'Simpan'),
                'icon' => 'fa fa-floppy-o',
                'attributes' => [
                    'id' => 'btn-save-instruksi-dpjp',
                    'onclick' => 'simpanInstruksiDpjp()',
                ],
            ],
            'custom-reset' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Muat Ulang'),
                'icon' => 'fa fa-refresh',
                'attributes' => [
                    'id' => 'btn-reset-instruksi-dpjp',
                ],
            ],
        ]) ?>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-6">
                <?php $form = ActiveForm::begin([
                    'id' => 'form-instruksi-dpjp',
                    // 'type' => ActiveForm::TYPE_HORIZONTAL,
                    'enableAjaxValidation'=>false, 
                    'enableClientValidation'=>false,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]) ?>
                <?= Html::activeHiddenInput($model, 'pendaftaran_id', ['id' => 'pendaftaran_id']) ?>
                <?= Html::activeHiddenInput($model, 'ruangan_id', ['id' => 'ruangan_id']) ?>
                <?= Html::activeHiddenInput($model, 'penjamin_id', ['id' => 'penjamin_id']) ?>
                <?= Html::activeHiddenInput($model, 'kelaspelayanan_id', ['id' => 'kelaspelayanan_id']) ?>

                <!-- Ruangan -->
                <?php
                    if(count($listRuangan) == 1) {
                        echo "<div class='form-group required'>";
                        echo Html::label(Yii::t('fe', 'Ruangan'), 'nama_ruangan', ['class' => 'control-label col-sm-2 required']);
                        echo "<br><div class='col-sm-12 ruangan-kamar'>";
                        echo Html::textInput('nama_ruangan', array_values($listRuangan)[0], ['class' => 'form-control input-sm', 'id' => 'nama_ruangan', 'disabled' => 'disabled']);
                        echo "</div>";
                        echo "<div class='help-block'></div>";
                        echo Html::hiddenInput('InstruksiDpjpForm[ruangan_id_kamar]', array_keys($listRuangan)[0]);
                        echo "</div>";
                    }else{
                        echo $form->field($model, 'ruangan_id')->widget(Select2::classname(), [
                            'data' => $listRuangan,
                            'options' => [
                                'class' => 'select2',
                                'placeholder' => '-- Pilih --'
                            ],
                        ]);
                    }
                ?>
                
                <?= $form->field($model, 'instruksi')->textArea(['rows' => 5]) ?>
                <?= $form->field($model, 'pemberi_instruksi_id',[]) 
                ->dropDownList([],
                    [
                        'id' => 'pemberi_instruksi_id',
                        'class' => 'form-control input-sm select2', 
                        'prompt' => Yii::t('fe', '-- Pilih Instruksi --'),
                    ]
                );
                ?>
                <?= $form->field($model, 'fee_konsul',[]) 
                ->dropDownList([],
                    [
                        'id' => 'fee_konsul',
                        'class' => 'form-control input-sm select2', 
                        'prompt' => Yii::t('fe', '-- Pilih Fee Konsul --'),
                    ]
                );
                ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs($this->render('js/form_instruksi_dpjp.js'), View::POS_END); ?>