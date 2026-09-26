


<?php

/**
 * @Author: ilhamsyah
 * @Date:   2021-08-30 10:41:48
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
        <button type="button" class="close close-modal-jadwal" data-dismiss-confirmation="modal">&times;</button>
        </div>
    </div>
</div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-7">
                <?php $form = ActiveForm::begin([
                    'id' => 'form-verbal-order',
                    // 'type' => ActiveForm::TYPE_HORIZONTAL,
                    'enableAjaxValidation'=>false,
                    'enableClientValidation'=>false,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]) ?>
                <?= Html::activeHiddenInput($model, 'pendaftaran_id', ['id' => 'pendaftaran_id']) ?>
                <?= Html::activeHiddenInput($model, 'ruangan_id', ['id' => 'ruangan_id']) ?>
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
    <div class="modal-footer text-right">
    <button type='button' style='margin-right: 5px' id="btn-save" class='btn btn-labeled btn-info btn-xs'><b><i class='fa fa-save'></i></b> Simpan</button>
</div>
</div>

<?php
$this->registerJs('
var pendaftaran_id = "' . $pendaftaran_id . '"
var ruangan_id = "' . $ruangan_id . '"
var kelaspelayanan_id = "' . $kelaspelayanan_id . '"
var penjamin_id = "' . $penjamin_id . '"
', View::POS_END);

$this->registerJs($this->render('form_verbal_order.js'), View::POS_END); ?>
