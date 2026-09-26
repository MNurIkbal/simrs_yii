<?php 

use yii\helpers\Html;

?>

<div class="row p-5">
    <div class="col-md-4">
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Prosedur Bedah") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'prosedur_bedah', ['class' => 'form-control doco-number group-tarif ', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Tenaga Ahli") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'tenaga_ahli', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Radiologi") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'radiologi', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Rehabilitasi") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'rehabilitasi', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Obat") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'obat', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Sewa Alat") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'sewa_alat', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>

    </div>
    <div class="col-md-4">
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Prosedur Non Bedah") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'prosedur_nonbedah', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Keperawatan") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'keperawatan', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Laboratorium") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'laboratorium', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Kamar/Akomodasi") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'kamar_akomodasi', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Alkes") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'alkes', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Obat Kemoterapi") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'obat_kemoterapi', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Konsultasi") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'konsultasi', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Penunjang") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'penunjang', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Pelayanan Darah") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'pelayanan_darah', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Rawat Intensif") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'rawat_intensif', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "BMHP") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'bmhp', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-4" style="padding: 5px 8px"><b><?= Yii::t("fe", "Obat Kronis") ?></b></label>
            <div class="col-sm-6 marginbottom">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1">Rp.</span>
                    <?= Html::activeTextInput($model, 'obat_kronis', ['class' => 'form-control doco-number group-tarif', 'style' => 'text-align: right']) ?>
                </div>
            </div>
        </div>
    </div>
</div>