<?php

use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\web\View;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
?>
<div class="panel panel-default long-form">
    <div class="panel-toolbar clearfix sticky">
        <?= DocoHelpers::generateToolbar([
            'custom-save' => [
                'title' => Yii::t('fe', 'Simpan'),
                'icon' => 'fa fa-floppy-o',
                'attributes' => [
                    'data-options' => 'click',
                    'id' => 'btn-save-pagt',
                ],
            ],
        ]) ?>
    </div>
    <div class="panel-body gizi-form">
        <?php
        $form = ActiveForm::begin([
            'id' => 'form-pagt',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 4,
                'deviceSize' => ActiveForm::SIZE_SMALL,
            ],
        ]);
        ?>
        <?= Yii::$app->controller->renderPartial('pagt/partials/__asesmen_gizi.php', compact('model', 'form', 'arrayConfig')); ?>
        <?= Yii::$app->controller->renderPartial('pagt/partials/__diagnosa.php', compact('model', 'form', 'arrayConfig')); ?>
        <?= Yii::$app->controller->renderPartial('pagt/partials/__monev.php', compact('model', 'modelMonev', 'form', 'arrayConfig', 'historyData')); ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>


<?php
$this->registerJs("
    var data = " . json_encode($data) . "
", View::POS_END, 'js2');
$this->registerJs($this->render('__form.js'), View::POS_END, 'js')

?>
