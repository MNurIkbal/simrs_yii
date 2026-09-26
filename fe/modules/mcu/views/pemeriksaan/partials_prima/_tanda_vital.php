<?php

use yii\helpers\Html;
?>
<div class="row">
    <div class="col-md-12 ml-3 mt-3">
        <div class="panel panel-default">
            <a data-toggle="collapse" href="#tandavital" role="button" aria-expanded="true" aria-controls="tandavital">
                <div class="panel-heading flex-container">
                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Tanda Vital'); ?></b></h6>
                    <div>
                        <ul class="icons-list">
                            <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                        </ul>
                    </div>
                </div>
            </a>
            <div class="panel-body collapse multi-collapse collapse in" id="tandavital">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for=""><?= Yii::t('fe', 'Tekanan Darah'); ?></label>
                            <div class="input-group">
                                <?= Html::activeTextInput($model, 'vital_tekanan_darah', ['class' => 'form-control']) ?>
                                <span class="input-group-addon" id="basic-addon2">
                                    <label style="width: 50px !important;"><?= Yii::t('fe', 'mmHg') ?></label>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for=""><?= Yii::t('fe', 'Nadi'); ?></label>
                            <div class="input-group">
                                <?= Html::activeTextInput($model, 'vital_nadi', ['class' => 'form-control']) ?>
                                <span class="input-group-addon" id="basic-addon2">
                                    <label style="width: 50px !important;"><?= Yii::t('fe', 'x/menit') ?></label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for=""><?= Yii::t('fe', 'Suhu'); ?></label>
                            <div class="input-group">
                                <?= Html::activeTextInput($model, 'vital_suhu', ['class' => 'form-control']) ?>
                                <span class="input-group-addon" id="basic-addon2">
                                    <label style="width: 50px !important;"> <?= Yii::t('fe', 'C') ?></label>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for=""><?= Yii::t('fe', 'Respirasi'); ?></label>
                            <div class="input-group">
                                <?= Html::activeTextInput($model, 'vital_respirasi', ['class' => 'form-control']) ?>
                                <span class="input-group-addon" id="basic-addon2">
                                    <label style="width: 50px !important;"><?= Yii::t('fe', 'x/menit') ?></label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>