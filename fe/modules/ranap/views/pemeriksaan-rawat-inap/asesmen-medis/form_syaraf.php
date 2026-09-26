<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$classForm = 'form-control';
?>

<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-asmed-ranap',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);
?>

<br>
<div class="row">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">Anamnese</h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'keluhan_utama')->textArea(['class' => $classForm]); ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'anamnese_terpimpin')->textArea(['class' => $classForm]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'pemeriksaan_fisis')
                            ->label(Yii::t('fe', 'Pemeriksaan Fisik dan Status Neurologis'))
                            ->textArea(['class' => $classForm]); ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo $form->field($model, 'diagnosis')->widget(Select2::classname(), [
                                'showToggleAll' => false,
                                'options' => [
                                    'placeholder' => '-- Pilih --',
                                    'class' => 'form-control input-sm select2',
                                    'multiple' => true
                                ],
                                'pluginOptions' => [
                                    'tags' => true,
                                    'tokenSeparators' => [',', '_'],
                                    'minimumInputLength' => 3,
                                    'language' => [
                                        'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                    ],
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['/ranap/end-point/get-new-diagnosa']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('
                                            function(params) {
                                                return {
                                                    q: params.term,
                                                    type: "diagnosa_utama",
                                                    all_text: 1,
                                                    id_with_text: 1,
                                                };
                                            }
                                        ')
                                    ],
                                    'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                                    'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                                    'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                                ],
                            ])->label() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>
