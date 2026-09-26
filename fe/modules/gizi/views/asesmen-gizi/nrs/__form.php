<?php

use app\components\DHtml;
use app\components\DocoConstants;
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
                    'id' => 'btn-save-nrs',
                ],
            ],
        ]) ?>
    </div>
    <div class="panel-body nrs-form">
        <?php
        $form = ActiveForm::begin([
            'id' => 'form-nrs',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'type' => ActiveForm::TYPE_VERTICAL,
        ]);
        ?>

        <div class='header-form'>
            Kategori Pasien :
        </div>
        <div><?= Html::activeRadio($model, "kategori", ['label' => 'Dewasa', 'value' => 'dewasa', 'id' => 'kategori-dewasa']) ?></div>
        <div><?= Html::activeRadio($model, "kategori", ['label' => 'Anak', 'value' => 'anak', 'id' => 'kategori-anak']) ?></div>

        <div class="dewasa" id="nrs-dewasa">
            <?= Yii::$app->controller->renderPartial('nrs/partials/__skrining_awal.php', compact('model', 'form', 'skriningAwal')); ?>
            <?= Yii::$app->controller->renderPartial('nrs/partials/__skrining_lanjut.php', compact('model', 'form', 'skriningLanjut')); ?>
            <?= Yii::$app->controller->renderPartial('nrs/partials/__skrining_kesimpulan.php', compact('model', 'form', 'skriningLanjut')); ?>
        </div>

        <div class="anak" id="nrs-anak">
            <?= Yii::$app->controller->renderPartial('nrs/partials/__formulir_nrs.php', compact('model', 'form', 'skrining_nrs_anak')); ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>


<?php
$this->registerJs("
    var kesimpulan = " . json_encode($kesimpulan) . "
    var KATEGORI_NRS = ".json_encode(DocoConstants::KATEGORI_NRS)."
    var total_skrining_anak = ".count($skrining_nrs_anak)."
    var total_skrining_dewasa = ".count($skriningLanjut)."
    var umur = " . $umur . "    
", View::POS_END, 'js2');
$this->registerJs($this->render('__form.js'), View::POS_END, 'js')

?>
