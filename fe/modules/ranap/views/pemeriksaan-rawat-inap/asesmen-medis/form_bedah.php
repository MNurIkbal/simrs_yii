<?php

use app\components\DocoHelpers;
use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\widgets\Select2;
use yii\web\JsExpression;

if(!$asesmenMedisId) {
    $model->tanggal_asesment = date('Y-m-d H:i:s');
}
?>

<div class="panel-toolbar clearfix">
    <?= DocoHelpers::generateToolbar([
        'save' => [
            'attributes' => [
                'form_id' => 'form-asmed-ranap',
                'id' => 'submit-asmed-ranap',
            ]
        ],
    ]); ?>
</div>

<?php
$form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-asmed-ranap',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);
?>

<h1 style="text-align:center;"><?= $title ?></h1>
<hr style="margin-top:15px;">
<h3 style="margin-left:10px;">Asesmen Pra Bedah</h3>
<hr style="margin-top:15px;">
<div class="row" style="margin-right:5px;">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-body">
                <br>
                <div class="row" style="margin-bottom:10px;">
                    <div class="row" style="margin-bottom:10px;margin-left:15px;">
                        <div class="col-sm-6">
                            <?=$form->field($model, 'tanggal_asesment')
                                ->label(Yii::t('fe', 'Tanggal dan Jam Assesmen'))
                                ->widget(DateTimePicker::className(),[
                                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                    'readonly' => true,
                                    'convertFormat' => true,
                                    'pluginOptions' => [
                                        'format' => 'dd/MM/yyyy HH:mm:ss',
                                        'autoclose' => true,
                                        'todayBtn' => true,
                                        'startDate' => date('Y-m-d')
                                    ]
                                ]);
                            ?>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:10px;margin-left:15px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'data_subjektif')->label(Yii::t('fe', 'Data Subjektif (Anamnesis)'))
                            ->textArea(['class' => 'form-control']); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'data_objektif')->label(Yii::t('fe', 'Data Objektif (Pemeriksaan Fisik)'))
                            ->textArea(['class' => 'form-control']); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:10px;margin-left:15px;">
                        <div class="col-sm-6">
                            <?php echo $form->field($model, 'diagnosa_pra_bedah')->widget(Select2::classname(), [
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
                        <div class="col-sm-6">
                            <?= $form->field($model, 'rencana_tindakan')->label(Yii::t('fe', 'Rencana Tindakan Operasi'))
                            ->textArea(['class' => 'form-control']); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row" style="text-align:center;">
                        <div class="image-frame">
                            <?php
                                echo Html::img( '@web/media/img/img-pemeriksaan/tubuh-pra-bedah.jpg', [
                                    'width'=> 700,
                                    'height'=> 520,
                                ]);
                            ?>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:10px;margin-left:15px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'deskripsi_lokasi')->label(Yii::t('fe', 'Deskripsi Lokasi Operasi Pasien'))
                            ->textArea(['class' => 'form-control']); ?>
                        </div>
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
