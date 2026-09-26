<?php

/**
 * @Author: Rizal
 * @Date:   2018-07-25 11:16:29
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
// use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;
?>

<?php 
// $form = ActiveForm::begin([
//     'id' => 'pasien-keluar-form', 
//     'type' => ActiveForm::TYPE_HORIZONTAL,
//     'action' => '/igd/pemeriksaan-igd/simpan-pasien-pulang',
//     'enableAjaxValidation' => false,
//     'enableClientValidation' => false,
//     'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
// ]);
?>

<div class="panel panel-default">
    <div class="panel-heading">

        <div id="head_panel_kesimpulan_pulang"  class="checkbox2 head_kesimpulan_pulang  form-group">
            <label class="form_kesimpulan_pulang col-md-1">
                <?=Html::checkbox('checked_kesimpulan_pulang',false,['class'=>'styled','id'=>'checked_kesimpulan_pulang'])?>
                <span class="cr"><i class="cr-icon fa fa-check"></i></span>
            </label>
            <div class="col-md-11">
                <h5 class="panel-title"><?=Yii::t('fe','Pasien Pulang')?></h5>
            </div>
        </div>
    </div>

    <div id="col_kesimpulan_pulang" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="row form_kesimpulan_pulang">
                <div class='col-md-6'>
                    <?= $form->field($modelKesimpulanPulang, 'instruksi_lanjutan', [
                            // 'labelOptions' => ['class' => 'text-right'],
                        ])->textArea([ 
                            'class' => 'form-control input-sm',
                            'id' => 'instruksi_lanjutan',
                        ]); 
                    ?>
                </div>

                <div class='col-md-6'>
                    <?=$form->field($modelKesimpulanPulang, 'tgl_lanjut_rawat')
                    ->widget(DateTimePicker::className(),[
                        'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                        'readonly' => true,
                        'convertFormat' => true,
                        'options'=>['id'=>'tgl_lanjut_rawat'],
                        'pluginOptions' => [
                            'format' => 'dd-MM-yyyy HH:mm:ss',
                            'autoclose' => true,
                            'todayBtn' => true,
                            'startDate' => date("Y-m-d")
                        ]
                        ])->label('Perawatan Lanjutan Tanggal'); 
                    ?>
                    <?= $form->field($modelKesimpulanPulang, 'poliklinik_id', [
                        // 'labelOptions' => ['class' => 'text-right']
                        ])->dropDownList([], [
                            'class' => 'select2 poliklinik_id',
                            'id' => 'poliklinik_id',
                            'prompt' => Yii::t('fe', '-- Pilih --')
                        ]);?>
                    <?=
                        $form->field($modelKesimpulanPulang, 'dokter_id', [
                            // 'labelOptions' => ['class' => 'text-right']
                        ])->widget(DepDrop::classname(), [
                            'options'=>['id'=>'dokter_id', 'class'=>'select2'],
                            // 'type'=>DepDrop::TYPE_SELECT2,
                            'pluginOptions'=>[
                                'depends'=>['poliklinik_id'],
                                'placeholder'=>Yii::t('fe', '--Pilih--'),
                                'url'=>Url::to(['/igd/end-point/get-list-dokter']),
                                'params'=>['poliklinik_id', 'tgl_lanjut_rawat']
                            ]
                        ]);
                    ?>

                    <?= 
                    $form->field($modelKesimpulanPulang, 'kesimpulanrd_id')->hiddenInput(['class'=>'kesimpulanrd_id'])->label(false);
                    ?>
                    <?= 
                    $form->field($modelKesimpulanPulang, 'pendaftaran_id')->hiddenInput(['class'=>'pendaftaran_id'])->label(false);
                    ?>
                </div>
            </div>

            <br>
        </div>
    </div>
</div>

<?php //ActiveForm::end() ?>

<?php 
$this->registerJs($this->render('_js/_kesimpulan_pulang.js'), View::POS_END);
?>

