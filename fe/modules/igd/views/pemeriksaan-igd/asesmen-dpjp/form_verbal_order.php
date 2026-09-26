<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-13 10:41:48
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h5 class="panel-title"><?= Yii::t('fe', 'Verbal Order') ?></h5>
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
                    'id' => 'btn-back-verbal-order-igd',
                    'onclick' => 'kembaliVerbalOrder()',
                ],
            ],
            'custom-save' => [
                'type' => 'submit',
                'title' => Yii::t('fe', 'Simpan'),
                'icon' => 'fa fa-floppy-o',
                'attributes' => [
                    'id' => 'btn-save-verbal-order-igd',
                    'onclick' => 'simpanVerbalOrder(this)',
                ],
            ],
            'custom-reset' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Muat Ulang'),
                'icon' => 'fa fa-refresh',
                'attributes' => [
                    'id' => 'btn-reset-verbal-order-igd',
                    'onclick' => 'resetVerbalOrder()',
                ],
            ],
        ]) ?>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-6">
                <?php $form = ActiveForm::begin([
                    'id' => 'form-verbal-order',
                    // 'type' => ActiveForm::TYPE_HORIZONTAL,
                    'enableAjaxValidation'=>false, 
                    'enableClientValidation'=>false,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]) ?>
                <?= Html::activeHiddenInput($model, 'pendaftaran_id', ['id' => 'pendaftaran_id']) ?>
                <?= Html::activeHiddenInput($model, 'ruangan_id', ['id' => 'ruangan_id']) ?>
                <?= Html::activeHiddenInput($model, 'penjamin_id', ['id' => 'penjamin_id']) ?>
                <?= Html::activeHiddenInput($model, 'kelaspelayanan_id', ['id' => 'kelaspelayanan_id']) ?>
                <?= $form->field($model, 'ruangan_id',[]) 
                ->dropDownList([$ruangan_id => $ruangan_nama],
                    [
                        'id' => 'ruangan_id_dropdown',
                        'class' => 'form-control input-sm select2', 
                        'prompt' => Yii::t('fe', '-- Pilih Ruangan --'),
                    ]
                );
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

<?php $this->registerJs($this->render('js/form_verbal_order.js'), View::POS_END); ?>