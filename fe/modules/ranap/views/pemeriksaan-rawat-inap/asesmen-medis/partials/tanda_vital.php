<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title"><?=Yii::t('fe', 'Tanda tanda vital')?></h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <br>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-sm-4">Tekanan Darah Systolic</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <?=Html::activeTextInput($model, 'td_systolic', ['class'=>'form-control systolic doco-decimal-wcomma'])?>
                                <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'mmHg')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">Tekanan Darah Diastolic</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <?=Html::activeTextInput($model, 'td_diastolic', ['class'=>'form-control diastolic sysdia doco-decimal-wcomma'])?>
                                <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'mmHg')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">Tekanan Darah Kategori</label>
                        <div class="col-sm-6">
                            <?=Html::activeHiddenInput($model, 'tekanan_darah', ['class'=>'form-control tekanan-darah', 'readonly'=>'true'])?>
                            <?=Html::activeTextInput($model, 'hasil_td', ['class'=>'form-control hasil-td','readonly'=>'true'])?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4"><?=$model->attributeLabels()['tinggi_badan']?></label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <?=Html::activeTextInput($model, 'tinggi_badan', ['class'=>'form-control tinggi-badan doco-decimal-wcomma imt_field'])?>
                                <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Cm')?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['berat_badan']?></label>
                        <div class="col-sm-7">
                            <div class="input-group">
                                <?=Html::activeTextInput($model, 'berat_badan', ['class'=>'form-control berat-badan doco-decimal-wcomma imt_field'])?>
                                <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Kg')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['bb_ideal']?></label>
                        <div class="col-sm-7">
                            <div class="input-group">
                                <?=Html::activeTextInput($model, 'bb_ideal', ['class'=>'form-control bb-ideal','readonly'=>'true'])?>
                                <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Kg')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['imt']?></label>
                        <div class="col-sm-7">
                            <?=Html::activeTextInput($model, 'imt', ['class'=>'form-control imt','readonly'=>'true'])?>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['ket_imt']?></label>
                        <div class="col-sm-7">
                            <?=Html::activeTextInput($model, 'ket_imt', ['class'=>'form-control ket-imt','readonly'=>'true'])?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['detak_nadi']?></label>
                        <div class="col-sm-7">
                            <div class="input-group">
                                <?=Html::activeTextInput($model, 'detak_nadi', ['class'=>'form-control doco-number'])?>
                                <span class="input-group-addon" id="basic-addon2">/<?=Yii::t('fe', 'Menit')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['kategori_denyut_nadi']?></label>
                        <div class="col-sm-7">
                                <?=Html::activeTextInput($model, 'kategori_denyut_nadi', ['class'=>'form-control'])?>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['pernapasan']?></label>
                        <div class="col-sm-7">
                            <div class="input-group">
                                <?=Html::activeTextInput($model, 'pernapasan', ['class'=>'form-control doco-number'])?>
                                <span class="input-group-addon" id="basic-addon2">/<?=Yii::t('fe', 'Menit')?></span>
                            </div>
                        </div>
                    </div> -->
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['denyut_jantung']?></label>
                        <div class="col-sm-7">
                            <?=Html::activeDropDownList($model, 'denyut_jantung',ArrayHelper::map($data_denyutJantung, 'lookup_value', 'lookup_name'), ['class'=>'form-control select2', 'prompt'=>''])?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['suhu_tubuh']?></label>
                        <div class="col-sm-7">
                            <div class="input-group">
                                <?=Html::activeTextInput($model, 'suhu_tubuh', ['class'=>'form-control doco-decimal-wcomma'])?>
                                <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Celcius')?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>