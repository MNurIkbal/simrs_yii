<?php

use app\components\DHtml;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;

?>
<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-titel">Diagnosa Keperawatan</h5>
        </div>
        <div class="panel-body">
            <?php echo $form->field($model, 'diagnosa_keperawatan')->widget(Select2::classname(), [
                // 'initValueText' => isset($text_diagnosa) && $text_diagnosa != '' ? $text_diagnosa : null,
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
                                    all_text: 0,
                                    id_with_text: 1,
                                    is_perawat: 1
                                };
                            }
                        ')
                    ],
                    'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                    'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                    'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                ],
            ])->label(false) ?>
        </div>
    </div>
</div>