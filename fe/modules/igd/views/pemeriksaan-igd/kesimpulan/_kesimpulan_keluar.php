<?php

/**
 * @Author: Rizal
 * @Date:   2018-07-25 11:16:29
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\ArrayHelper;
?>


<div class="row">
    <hr>
</div>
<?php 
// $form = ActiveForm::begin([
//     'id' => 'pasien-keluar-form', 
//     'type' => ActiveForm::TYPE_HORIZONTAL,
//     'action' => '/igd/pemeriksaan-igd/simpan-pasien-keluar',
//     'enableAjaxValidation' => false,
//     'enableClientValidation' => false,
//     'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
// ]) 
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div id="head_panel_kesimpulan_keluar" class="checkbox2 head_kesimpulan_keluar form-group">
            <label class="control-label col-md-1">
                <?=Html::checkbox('checked_kesimpulan_keluar',false,['class'=>'styled','id'=>'checked_kesimpulan_keluar'])?>
                <span class="cr"><i class="cr-icon fa fa-check"></i></span>
            </label>
            <div class="col-md-11">
                <h5 class="panel-title"><?=Yii::t('fe','Pasien Pindah Rawat Inap')?></h5>
            </div>
        </div>
    </div>

    <div id="col_kesimpulan_keluar" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="row form_kesimpulan_keluar">
                <div class='col-md-6'>
                    <?= $form->field($modelKesimpulanKeluar, 'kondisi', [
                            // 'labelOptions' => ['class' => 'text-right'],
                        ])->textArea([ 
                            'class' => 'form-control input-sm',
                            'id' => 'kondisi',
                        ])
                        ->label(Yii::t('fe','Kondisi / Masalah'));
                    ?>
                    <?= $form->field($modelKesimpulanKeluar, 'hr',  [
                            'addon' => ['append' => ['content'=>'Menit']]
                        ])->textInput([ 
                            'class' => 'form-control input-sm docoNumberOnly',
                            'id' => 'hr'
                        ])
                        ->label(Yii::t('fe','Detak Jantung (HR)'));
                    ?>
                    <?= $form->field($modelKesimpulanKeluar, 'rr', [
                            'addon' => ['append' => ['content'=>'Menit']]
                        ])->textInput([ 
                            'class' => 'form-control input-sm docoNumberOnly',
                            'id' => 'rr',
                        ])
                        ->label(Yii::t('fe','Pernapasan (RR)'));
                    ?>
                    <?= $form->field($modelKesimpulanKeluar, 'spo2', [
                            'addon' => ['append' => ['content'=>'%']]
                        ])->textInput([ 
                            'class' => 'form-control input-sm docoNumberOnly',
                            'id' => 'spo2',
                        ])
                        ->label(Yii::t('fe','Oksigen (SpO2)')); 
                    ?>
                    <?= $form->field($modelKesimpulanKeluar, 't', [
                            'addon' => ['append' => ['content'=>'C']]
                        ])->textInput([ 
                            'class' => 'form-control input-sm doco-decimal-wcomma',
                            'id' => 't',
                        ])
                        ->label(Yii::t('fe','Temperatur (T)')); 
                    ?>

                    <?= 
                    // Html::hiddenInput('kesimpulanrd_id', $modelKesimpulanKeluar['kesimpulanrd_id'], ['class' => 'kesimpulanrd_id']) 
                    $form->field($modelKesimpulanKeluar, 'kesimpulanrd_id')->hiddenInput(['class'=>'kesimpulanrd_id'])->label(false);
                    ?>
                    <?= 
                    // Html::hiddenInput('pendaftaran_id', $modelKesimpulanKeluar['pendaftaran_id'], ['class' => 'pendaftaran_id']) 
                    $form->field($modelKesimpulanKeluar, 'pendaftaran_id')->hiddenInput(['class'=>'pendaftaran_id'])->label(false);
                    ?>
                </div>

                <div class='col-md-6'>
                    <fieldset>
                        <legend><?=Yii::t('fe', 'Glasgow coma scale')?></legend>
                        <?=
                            $form->field($modelKesimpulanKeluar, 'gcs_eye_id')
                                ->dropDownList(
                                    ArrayHelper::map($data_gcsEye, 'metodegcs_id', 'nama_and_nilai'), 
                                    ['class'=>'select2 gcs_eye','prompt' => '--Pilih--','options'=>$gcsEyeOptions]
                                )
                                ->label(Yii::t('fe','GCS Eye'));
                        ?>
                        <?=
                            $form->field($modelKesimpulanKeluar, 'gcs_verbal_id')
                                ->dropDownList(
                                    ArrayHelper::map($data_gcsVerbal, 'metodegcs_id', 'nama_and_nilai'), 
                                    ['class'=>'select2 gcs_verbal','prompt' => '--Pilih--','options'=>$gcsVerbalOptions]
                                )
                                ->label(Yii::t('fe','GCS Verbal'));
                        ?>
                        <?=
                            $form->field($modelKesimpulanKeluar, 'gcs_motorik_id')
                                ->dropDownList(
                                    ArrayHelper::map($data_gcsMotorik, 'metodegcs_id', 'nama_and_nilai'), 
                                    ['class'=>'select2 gcs_motorik','prompt' => '--Pilih--','options'=>$gcsMotorikOptions]
                                )
                                ->label(Yii::t('fe','GCS Motorik'));
                        ?>
                        <?=$form->field($modelKesimpulanKeluar, 'hasil_gcs')->textInput(['class'=>'nilai_gcs', 'readonly'=>true,'placeholder'=>'Kategori GCS', 'tabindex' => -1])->label('Hasil Metode GCS') ?>
                        <?=$form->field($modelKesimpulanKeluar, 'is_kapitis')->checkbox(['class'=>'is_kapitis', 'type'=>'hidden'])->label(false) ?>
                        <?=$form->field($modelKesimpulanKeluar, 'gcs_kategori')->textInput(['class'=>'hasil_gcs', 'readonly'=>true,'placeholder'=>'Keterangan GCS', 'tabindex' => -1, 'type'=>'hidden'])
                                ->label(false);?>
                    </fieldset>
                </div>
            </div>

            <br>
        </div>
    </div>
</div>

<?php //ActiveForm::end() ?>

<?php 
$this->registerJs("
    var metodeGcs = ".json_encode($data_metodegcs)."
    var dataGcs = ".json_encode($data_gcs)."
    var listGcs = ".json_encode($data_listgcs)."
", View::POS_END, 'js2');

$this->registerJs($this->render('_js/_kesimpulan_keluar.js'), View::POS_END);
?>

