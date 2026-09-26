<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>
<style type="text/css">
    .no-border {
    border-bottom: 0px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title"><?=Yii::t('fe', 'Pemeriksaan Fisik')?></h6>
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
                        <legend class="no-border"><?=Yii::t('fe', 'Kepala')?></legend>

                        <div class="form-group">
                            <div class="col-sm-5">
                            <?=$form->field($model, 'kepala')->radioList($data_kepala,['class' => 'fisik'])->label(false)?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'kepala_lainnya', ['class' => 'form-control kepala-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>

                        <legend class="no-border"><?=Yii::t('fe', 'Mata')?></legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                            <?=$form->field($model, 'mata')->radioList($data_mata,['class' => 'fisik'])->label(false)?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'mata_lainnya', ['class' => 'form-control mata-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>


                      
                        <legend class="no-border"><?=Yii::t('fe', 'Telinga, Hidung dan Tenggorokan')?></legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                            <?=$form->field($model, 'tht')->radioList($data_telinga,['class' => 'fisik'])->label(false)?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'tht_lainnya', ['class' => 'form-control tht-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>


                        <legend class="no-border"><?=Yii::t('fe', 'Leher')?></legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                            <?=$form->field($model, 'leher')->radioList($data_leher,['class' => 'fisik'])->label(false)?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'leher_lainnya', ['class' => 'form-control leher-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>

                        <legend class="no-border"><?=Yii::t('fe', 'Mulut')?></legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                            <?=$form->field($model, 'mulut')->radioList($data_mulut,['class' => 'fisik'])->label(false)?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'mulut_lainnya', ['class' => 'form-control mulut-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>
                        

                        <legend class="no-border"><?=Yii::t('fe', 'Thoraks')?></legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                            <?=$form->field($model, 'toraks')->radioList($data_thoraks,['class' => 'fisik'])->label(false)?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'thoraks_lainnya', ['class' => 'form-control toraks-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>

                        <legend class="no-border"><?=Yii::t('fe', 'Paru-paru')?></legend> 

                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'pergerakan',[
                                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-md-7'
                                    ]
                                ])->radioList($data_pergerakan)->label('Pergerakan',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'perkusi',[
                                    'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-7'
                                ]
                                ])->radioList($data_perkusi,['class' => 'fisik'])->label('Perkusi',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'perkusi_lainnya', ['class' => 'form-control perkusi-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'nafas',[
                                    'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-7'
                                ]
                                ])->radioList($data_pernapasan,['class' => 'fisik'])->label('Pernapasan',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'pernapasan_lainnya', ['class' => 'form-control nafas-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>


                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'rochi',[
                                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-md-7'
                                    ]
                                ])->radioList($data_rochi)->label('Rochi',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'wheezing',[
                                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-md-7'
                                    ]
                                ])->radioList($data_wheezing)->label('Wheezing',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                        </div>

                    </fieldset>
                </div>
                <div class="col-md-6">
                    <fieldset>
                        <legend class="no-border"><?=Yii::t('fe', 'Jantung')?></legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'irama',[
                                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-md-7'
                                    ]
                                ])->radioList($data_irama)->label('Irama',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'bunyi_jantung',[
                                    'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-7'
                                ]
                                ])->radioList($data_bunyi_jantung,['class' => 'fisik'])->label('Bunyi jantung',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'bunyi_jantung_lainnya', ['class' => 'form-control bunyi_jantung-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>

                        <legend class="no-border"><?=Yii::t('fe', 'Abdomen')?></legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'kelainan',[
                                    'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-7'
                                ]
                                ])->radioList($data_kelainan,['class' => 'fisik'])->label('Kelainan',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'kelaianan_lainnya', ['class' => 'form-control kelainan-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'benjolan',[
                                    'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-7'
                                ]
                                ])->radioList($data_benjolan,['class' => 'fisik'])->label('Benjolan',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'benjolan_lainnya', ['class' => 'form-control benjolan-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'nyeri_tekan',[
                                    'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-7'
                                ]
                                ])->radioList($data_nyeri_tekan,['class' => 'fisik'])->label('Nyeri Tekan',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'nyeri_tekan_lainnya', ['class' => 'form-control nyeri_tekan-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'hernia',[
                                    'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-7'
                                ]
                                ])->radioList($data_hernia,['class' => 'fisik'])->label('Hernia',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'hernia_lainnya', ['class' => 'form-control hernia-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'bising_usus',[
                                    'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-7'
                                ]
                                ])->radioList($data_bising_usus,['class' => 'fisik'])->label('Bising Usus',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'bising_usus_lainnya', ['class' => 'form-control bising_usus-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'distensi',[
                                    'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-7'
                                ]
                                ])->radioList($data_distensi,['class' => 'fisik'])->label('Distensi',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'distensi_lainnya', ['class' => 'form-control distensi-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>

                        <legend class="no-border"><?=Yii::t('fe', 'Tulang Belakang dan Anggota Tubuh')?></legend>
                        
                        <div class="form-group">
                            <div class="col-sm-5">
                            <?=$form->field($model, 'tulang_belakang')->radioList($data_tulang_belakang,['class' => 'fisik'])->label(false)?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'tulang_belakang_lainnya', ['class' => 'form-control tulang_belakang-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>

                        <legend class="no-border"><?=Yii::t('fe', 'Sistem Saraf')?></legend>
                        
                        <div class="form-group">
                            <div class="col-sm-5">
                            <?=$form->field($model, 'sistem_saraf')->radioList($data_sistem_saraf,['class' => 'fisik'])->label(false)?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'sistem_saraf_lainnya', ['class' => 'form-control sistem_saraf-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>

                        <legend class="no-border"><?=Yii::t('fe', 'Genetalia')?></legend>
                        
                        <div class="form-group">
                            <div class="col-sm-5">
                            <?=$form->field($model, 'genetalia')->radioList($data_genetalia,['class' => 'fisik'])->label(false)?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'genetalia_lainnya', ['class' => 'form-control genetalia-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>

                        <legend class="no-border"><?=Yii::t('fe', 'Ekstremitas')?></legend>
                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'edema',[
                                    'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-7'
                                ]
                                ])->radioList($data_edema,['class' => 'fisik'])->label('Edema',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'edema_lainnya', ['class' => 'form-control edema-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-5">
                                <?=$form->field($model, 'crt',[
                                    'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-7'
                                ]
                                ])->radioList($data_edema,['class' => 'fisik'])->label('CRT',['class' => 'control-label has-star col-sm-5']);?>
                            </div>
                            <br>
                            <label class="control-label col-sm-4">
                            <?= Html::activeTextInput($model, 'crt_lainnya', ['class' => 'form-control crt-lainnya', 'readonly' => 'true']) ?>
                            </label>
                        </div>

                        <div class="form-group">
                            <?= $form->field($model, 'pemeriksaan_fisik_lainnya', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-2',
                                        'wrapper' => 'col-md-7'
                                    ]
                                ])->textArea([
                                    'class' => 'form-control contol-sm-7',
                            ]); ?>
                        </div>

                    </fieldset>
                </div>
            </div>
            
        </div>
    </div>
</div>