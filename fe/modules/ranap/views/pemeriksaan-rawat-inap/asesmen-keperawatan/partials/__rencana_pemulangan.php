<?php

use app\components\DHtml;
use yii\helpers\Html;
?>
<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Rencana Pemulangan</h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-2">Tujuan Pulang</label>
                        <div class="col-sm-10" style="margin-bottom: 8px;">
                            <?=
                                DHtml::multipleRadio([
                                    'model' => $model,
                                    'fieldName' => 'tujuan_pulang',
                                    'otherFieldName' => 'tujuan_pulang_lainnya',
                                    'data' => ['rumah' => 'Rumah', '00' => 'Lainnya'],
                                    'colSize' => 2
                                ])
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-2">Transportasi yang digunakan</label>
                        <div class="col-sm-10">
                            <?=
                                DHtml::multipleRadio([
                                    'model' => $model,
                                    'fieldName' => 'transportasi',
                                    'data' => $arrayConfig['pemulangan']['transportasi'],
                                    'colSize' => 2
                                ])
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-2">Orang yang merawat</label>
                        <div class="col-sm-10">
                            <?=
                                DHtml::multipleRadio([
                                    'model' => $model,
                                    'fieldName' => 'orang_merawat',
                                    'data' => $arrayConfig['pemulangan']['orang_merawat'],
                                    'colSize' => 2
                                ])
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-2">Sarana Kesehatan/Rujukan terdekat</label>
                        <div class="col-sm-10">
                            <?=
                                DHtml::multipleRadio([
                                    'model' => $model,
                                    'fieldName' => 'sarana_kesehatan',
                                    'data' => $arrayConfig['pemulangan']['sarana_kesehatan'],
                                    'colSize' => 2
                                ])
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>