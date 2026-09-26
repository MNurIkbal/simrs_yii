<?php

use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use kartik\widgets\ActiveForm;
use yii\web\JsExpression;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;

?>
<style>
    .datepicker>div {
        display: block;
    }
</style>

<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title"><?= Yii::t('fe', $title) ?></h6>
    </div>
    <br>
    <button type="button" id="btn-generate"
        class="btn btn-info btn-labeled btn-xs data-save"
        style="margin-left:10px;margin-bottom:10px;">
        <b><i class="fa fa-floppy-o"></i></b>
        Generate Lembar Program Terapi
    </button>
    <button type="button" id="btn-cetak-rehabilitasi"
        class="btn btn-info btn-labeled btn-xs data-pdf <?php if ($isHide) { ?> hidden <?php } ?>"
        style="margin-left:10px;margin-bottom:10px;">
        <b><i class="fa fa-file-pdf-o"></i></b>
        Cetak PDF
    </button>
    <br>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6" style="display: none;" id="loading-generate">
                <div class="alert alert-danger">
                    <span class="text-danger">
                        <i class="fa fa-info-circle"></i>
                        Proses generate lembar program terapi sedang berlangsung, mohon tunggu sebentar.
                    </span>
                </div>
            </div>
            <div class="col-md-12">
                <table id="tableDataIntegrasi" class="table table-striped table-condensed table-hover" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%">
                                <div class="text-center">
                                    <input type="checkbox" class="select-checkbox" id="check-all">
                                </div>
                            </th>
                            <th>No</th>
                            <th>Tanggal Rujukan</th>
                            <th>Dokter Perujuk</th>
                            <th>Jumlah Terapi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="12"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$phpVars = [
    'pasienId' => $pasienId,
    'pendaftaranId' => $pendaftaranId,
    'randString' => $randString,
    'pendaftaranIdEnc' => $pendaftaranIdEnc
];
$this->registerJsVar('rehabVars', $phpVars);
$this->registerJs($this->render('rehabilitasi.js'), View::POS_END, 'js');
?>