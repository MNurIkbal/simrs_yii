<?php

use app\components\DHtml;
?>
<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Data Psikososial</h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-3">Perasaan klien terhadap penyakit saat ini</label>
                        <?= 
                            DHtml::multipleCheckbox([
                                'model' => $model,
                                'fieldName' => 'perasaan_klien',
                                'data' => $arrayConfig['perasaan_klien'],
                                'colSize' => 2
                            ])
                        ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-3">Dukungan sosial</label>
                        <?= 
                            DHtml::multipleCheckbox([
                                'model' => $model,
                                'fieldName' => 'sosial_support',
                                'data' => $arrayConfig['sosial_support'],
                                'otherColSize' => 4,
                                'colSize' => 2
                            ])
                        ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-3">Hubungan pasien dengan keluarga</label>
                        <?= 
                            DHtml::multipleCheckbox([
                                'model' => $model,
                                'fieldName' => 'hubungan_pasien',
                                'data' => $arrayConfig['hubungan_pasien'],
                                'colSize' => 2
                            ])
                        ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-3">Keluarga lain yang tinggal di rumah</label>
                        <?= 
                            DHtml::multipleCheckbox([
                                'model' => $model,
                                'fieldName' => 'keluarga_lain',
                                'data' => $arrayConfig['keluarga_lain'],
                                'otherColSize' => 4,
                                'colSize' => 2
                            ])
                        ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-3">Keadaan emosi saat interaksi</label>
                        <?= 
                            DHtml::multipleCheckbox([
                                'model' => $model,
                                'fieldName' => 'keadaan_emosi',
                                'data' => $arrayConfig['keadaan_emosi'],
                                'colSize' => 2
                            ])
                        ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <?= $form->field($model, 'suku_id', ['horizontalCssClasses' => ['label' => 'col-sm-3', 'wrapper' => 'col-sm-4']])->dropdownList([], ['id' => 'sukuForm']) ?>
                </div>
            </div>
        </div>
    </div>
</div>