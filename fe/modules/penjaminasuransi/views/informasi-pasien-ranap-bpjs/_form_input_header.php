<?php 

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\DateTimePicker;
use yii\helpers\Url;

?>

<div class="col-md-12" style="margin-bottom: 20px;">
    <div style="display: flex; justify-content: center;">
        <div class="mr-3">
            <i for="">Jaminan / Cara Bayar : </i><br>
            <!-- <span style="font-weight: bold;"><?= ArrayHelper::getValue($info, 'penjamin_nama', '-')?></span> -->
            <div class="dep-jaminan" style="min-width: 250px;">
                <?= Html::activeDropDownList($model, 'klaim_penjamin', ArrayHelper::map($opsi['inacbg_penjamin'], 'lookup_value', 'lookup_name'), ['class' => 'form-control select2 delete-on-edit']) ?>
                </div>
        </div>
        <div class="jkn-select mr-3">
            <i for="">No. Peserta </i><br>
            <input type="text" class="form-control jkn-select" id="nomer_peserta" readonly  value="<?= $noKartu ?>">
        </div>
        <div class="mr-4 jkn-select">
            <i for="">No. SEP </i><br>
            <input type="text" id="no_sep" class="form-control jkn-select" readonly value="<?= ArrayHelper::getValue($info, 'nosep') ?>">
        </div>
        <div class="mr-3">
            <label class="text-left control-label covid-select hidden" style="padding: 0 10px"><b><?= Yii::t("fe", "No Identitas Pasien") ?></b></label>
            <div class="covid-select hidden" style="display: flex;">
                <div style="width:250px;">
                    <?= Html::activeDropDownList($model, 'identitas_id', ArrayHelper::map($opsi['jenis_identitas'], 'lookup_value', 'lookup_name'), ['class' => 'form-control select2 mr-3 delete-on-edit']) ?>
                </div>
                <div class="mr-3">
                    <?= Html::activeTextInput(
                        $model,
                        'identitas_value',
                    [
                        'id' => 'identitas_value',
                        'class' => 'form-control input-sm free-txt ml-4 delete-on-edit',
                        ]) 
                    ?>
                </div>
            </div>
        </div>
        <div>
            <label class="text-left control-label covid-select hidden" style="padding: 0 10px"><b><?= Yii::t("fe", "No. Pengajuan Klaim") ?></b></label>
            <div class="covid-select hidden">
                <?= Html::activeTextInput(
                    $model,
                    'no_klaimcovid',
                    [
                        'id' => 'no_klaimcovid',
                        'class' => 'form-control input-sm free-txt',
                        'disabled' => true
                    ]
                ) ?>
            </div>
        </div>
    </div>  
</div>
