<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>
<style type="text/css">
    .no-border {
    border-bottom: 0px;
    padding-bottom: 5px;
    }
    /* .no-padding-left {
    padding-left: 0px;
    }
    .no-padding-left {
    padding-left: 0px;
    } */
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title"><?=Yii::t('fe', 'Pemeriksaan Umum')?></h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <br>
                <div class="col-md-6">
                    <fieldset>                       
                        <legend class="no-border"><?=Yii::t('fe', 'Glasgow coma scale')?></legend>
                            <?=$form->field($model, 'gcs_eye')->dropDownList(ArrayHelper::map($data_gcsEye, 'metodegcs_id', 'nama_and_nilai'), ['class'=>'select2 gcs_eye','prompt'=>'','options'=>$gcsEyeOptions])?>
                            <?=$form->field($model, 'gcs_verbal')->dropDownList(ArrayHelper::map($data_gcsVerbal, 'metodegcs_id', 'nama_and_nilai'), ['class'=>'select2 gcs_verbal','prompt'=>'','options'=>$gcsVerbalOptions])?>
                            <?=$form->field($model, 'gcs_motorik')->dropDownList(ArrayHelper::map($data_gcsMotorik, 'metodegcs_id', 'nama_and_nilai'), ['class'=>'select2 gcs_motorik','prompt'=>'','options'=>$gcsMotorikOptions])?>
                            <?=$form->field($model, 'jumlah_gcs')->textInput(['readonly'=>true]);?>
                            <div style="display:none">
                                <?=$form->field($model, 'is_kapitis')->checkbox()?>
                                <?=$form->field($model, 'hasil_gcs')->textInput(['class'=>'hasil_gcs','readonly'=>true])?>
                            </div>
                            <?=$form->field($model, 'kontak')->radioList($data_kontak)?>
                        <legend class="no-border"><?=Yii::t('fe', 'Kesadaran')?></legend>
                        <?=$form->field($model, 'kesadaran')->radioList($data_kesadaran)?>
                    </fieldset>
                </div>
                <div class="col-md-6">
                    <fieldset>
                        <legend><?=Yii::t('fe', 'Keadaan Umum')?></legend>
                        <?=$form->field($model, 'keadaan_umum')->radioList($data_kesadaran_umum)?>
                        <legend><?=Yii::t('fe', 'Metode asesmen nyeri')?></legend>
                        <?=$form->field($model, 'metod_asmennyeri')->radioList(ArrayHelper::map($data_metodAsesmen, 'lookup_value', 'lookup_name'), [])?>
                        <?=$form->field($model, 'skala')?>
                        <?=$form->field($model, 'lokasi_nyeri')?>


                        <div class="col-sm-6">
                            <?=$form->field($model, 'is_terintubasi',[
                                'horizontalCssClasses' => [
                                'wrapper' => 'col-md-6'
                            ]
                            ])->radioList($data_isTerintubasi,['class' => 'fisik'])->label('Terintubasi',['class' => 'control-label has-star col-sm-6']);?>
                        </div>
                        <label class="control-label col-sm-6">
                        <?= Html::activeTextInput($model, 'terintubasi_lainnya', ['class' => 'form-control is_terintubasi-lainnya', 'readonly' => 'true']) ?>
                        </label>
                    </fieldset>
                </div>
            </div>
            
        </div>
    </div>
</div>