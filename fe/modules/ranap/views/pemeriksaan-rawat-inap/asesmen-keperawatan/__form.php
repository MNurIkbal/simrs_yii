<?php

use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\web\View;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
?>
<div class="panel panel-default long-form">
    <div class="panel-heading">
        <h5 class="panel-title">Pengkajian Keperawatan Rawat Inap</h5>
    </div>
    <div class="panel-toolbar clearfix sticky">
        <div class="col-sm-4">
            <?= DocoHelpers::generateToolbar([
                'custom-save' => [
                    'title' => Yii::t('fe', 'Simpan'),
                    'icon' => 'fa fa-floppy-o',
                    'attributes' => [
                        'data-options' => 'click',
                        'id' => 'btn-save-asesmen-perawat',
                    ],
                ],
                'custom-print' => [
                    'title' => Yii::t('fe', 'Cetak'),
                    'icon' => 'fa fa-print',
                    'attributes' => [
                        'data-options' => 'click',
                        'id' => 'btn-print-asesmen-perawat',
                        'disabled' => !empty($model->asesmenawal_id) ? false : true,
                    ],
                ],
                'history-keperawatan' => [
                    'type'  => 'button',
                    'title' => Yii::t('fe', 'History Assesmen Keperawatan'),
                    'icon'  => 'fa fa-history',
                    'attributes' => [
                        'id'          => 'btn-history-keperawatan',
                        'data-width'  => '90%',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'action'      => 'history-assesmen-keperawatan?pendaftaran_id=' . $pendaftaran_id, true,
                    ]
                ]
            ]) ?>
        </div>
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
        <?php if ($isStopAkomodasi) : ?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="alert alert-danger" role="alert"><strong>Pasien Sudah Melakukan Stop Akomodasi</strong></div>
                </div>
            </div>
        <?php endif; ?>
        <div class="row form-row">
            <div class="col-sm-6">
                <?=
                $form->field($model, 'tgl_datang')->widget(DateTimePicker::className(), [
                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                    'readonly' => true,
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
                    'readonly' => true,
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
        <?= Yii::$app->controller->renderPartial('asesmen-keperawatan/partials/__identitas_pasien.php', compact('model', 'form', 'data', 'arrayConfig', 'data_bmi')); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-keperawatan/partials/__alasan.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-keperawatan/partials/__airway_breathing.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-keperawatan/partials/__kategori_triase.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-keperawatan/partials/__ekg.php', compact('model', 'form', 'data', 'arrayConfig', 'modelResiko', 'pendaftaran_id')); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-keperawatan/partials/__skrining_fungsional.php', compact('model', 'form', 'data', 'arrayConfig', 'disableGiziBtn')); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-keperawatan/partials/__skrining_gizi.php', compact('model', 'form', 'data', 'arrayConfig', 'disableGiziBtn')); ?>
        <!-- <?php //Yii::$app->controller->renderPartial('asesmen-keperawatan/partials/__secondary_survey.php', compact('model', 'form','arrayConfig')); 
                ?> -->
        <?= Yii::$app->controller->renderPartial('asesmen-keperawatan/partials/__psikososial.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-keperawatan/partials/__kebutuhan_edukasi.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-keperawatan/partials/__diagnosa_keperawatan.php', compact('model', 'form')); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-keperawatan/partials/__masuk_ke.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
        <?= Yii::$app->controller->renderPartial('asesmen-keperawatan/partials/__rencana_pemulangan.php', compact('model', 'form', 'data', 'arrayConfig')); ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>


<?php

$this->registerJs("
    var data = " . json_encode($data) . ";
    var data_bmi = " . $data_bmi . ";
    var jeniskelamin = " . $jeniskelamin . ";
    var is_disabled = '" . $disabled . "';
    var status = '" . $status_disabled . "';
    var is_perawatasesmenawal = " . $is_perawat . ";
    $(document).ready(function(){
        if(!is_perawatasesmenawal || is_disabled || status){
            $('#form-asesmen-keperawatan :input').not('.btn-verifikasi-gizi').prop('disabled', true);
            $('#btn-save-asesmen-perawat, .data-reset').prop('disabled', true);
        }
    });
" . $this->render('__form.js'), View::POS_END);
?>
