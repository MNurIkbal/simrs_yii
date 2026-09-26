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
            <h5 class="panel-title">Pengkajian Keperawatan Gawat Darurat</h5>
        </div>
        <div class="panel-body rajal-form">
            <?php
            $form = ActiveForm::begin([
                'id' => 'form-asesmen-keperawatan',
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
            <?= Yii::$app->controller->renderPartial('partials/__identitas_pasien.php', compact('model', 'form', 'data', 'arrayConfig', 'data_bmi')); ?>
            <?= Yii::$app->controller->renderPartial('partials/__alasan_igd.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('partials/__airway_breathing.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('partials/__kategori_triase.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('partials/__ekg.php', compact('model', 'form', 'data', 'arrayConfig', 'modelResiko', 'pendaftaran_id')); ?>
            <?= Yii::$app->controller->renderPartial('partials/__skrining_gizi.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('partials/__psikososial.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('partials/__kebutuhan_edukasi.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('partials/__diagnosa_keperawatan.php', compact('model', 'form')); ?>
            <?= Yii::$app->controller->renderPartial('partials/__masuk_ke.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
            <?= Yii::$app->controller->renderPartial('partials/__rencana_pemulangan.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
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
    var data = " . json_encode($data) . ";
    var data_bmi = ". $data_bmi .";
    var jeniskelamin = ". $jeniskelamin .";
" . $this->render('js/__form.js'), View::POS_END);
?>
