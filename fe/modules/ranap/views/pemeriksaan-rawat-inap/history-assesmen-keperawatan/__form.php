<?php

use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\web\View;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Detail History Assesmen Keperawatan</h5>
</div>
<div class="modal-body history_askep">
    <div class="panel panel-default long-form">
        <div class="panel-heading">
            <h5 class="panel-title">Pengkajian Keperawatan Rawat Inap</h5>
        </div>
        <div class="panel-body rajal-form">
            <?php
            $form = ActiveForm::begin([
                'id' => 'form-asesmen-keperawatan-history',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => [
                    'labelSpan' => 4,
                    'deviceSize' => ActiveForm::SIZE_SMALL,
                ],
            ]);
            ?>
            <div class="row form-row">
                <div class="col-sm-6">
                    <?=
                        $form->field($model, 'tgl_datang')->widget(DateTimePicker::className(), [
                            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                            'disabled' => true,
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'format' => 'dd/MM/yyyy HH:mm:ss',
                                'autoclose' => true,
                                'todayBtn' => true
                            ]
                        ]);
                    ?>
                </div>
                <div class="col-sm-6">
                    <?=
                        $form->field($model, 'tgl_keluar')->widget(DateTimePicker::className(), [
                            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                            'disabled' => true,
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'format' => 'dd/MM/yyyy HH:mm:ss',
                                'autoclose' => true,
                                'todayBtn' => true
                            ]
                        ]);
                    ?>
                </div>
                <div class="col-sm-12">
                    <hr>
                </div>
            </div>
            <?= Yii::$app->controller->renderPartial('history-assesmen-keperawatan/partials/__identitas_pasien.php', compact('model', 'form', 'data', 'arrayConfig', 'data_bmi')); ?>
            <?= Yii::$app->controller->renderPartial('history-assesmen-keperawatan/partials/__alasan.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('history-assesmen-keperawatan/partials/__airway_breathing.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('history-assesmen-keperawatan/partials/__kategori_triase.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('history-assesmen-keperawatan/partials/__ekg.php', compact('model', 'form', 'data', 'arrayConfig', 'modelResiko', 'pendaftaran_id')); ?>
            <?= Yii::$app->controller->renderPartial('history-assesmen-keperawatan/partials/__skrining_fungsional.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('history-assesmen-keperawatan/partials/__skrining_gizi.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('history-assesmen-keperawatan/partials/__psikososial.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('history-assesmen-keperawatan/partials/__kebutuhan_edukasi.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('history-assesmen-keperawatan/partials/__diagnosa_keperawatan.php', compact('model', 'form')); ?>
            <?= Yii::$app->controller->renderPartial('history-assesmen-keperawatan/partials/__masuk_ke.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('history-assesmen-keperawatan/partials/__rencana_pemulangan.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> " . Yii::t('fe', 'Kembali'), [
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
<?php
$this->registerJs("
    var data_history = " . json_encode($data) . ";
    var data_bmi = ". $data_bmi .";
    var jeniskelamin = ". $jeniskelamin .";
" . $this->render('js/__form.js'), View::POS_END);
?>
