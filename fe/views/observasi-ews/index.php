<?php

use app\components\DocoConstants;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\web\JsExpression;
use kartik\widgets\DateTimePicker;
use kartik\widgets\DatePicker;

?>

<style>
    .dataTables_scroll {
        height: 100% !important;
        max-height: 100%;
    }

    .select2-selection__clear:after {
        content: '' !important;
    }

    .range_custom {
        height: 20px;
        padding: 13px 12px;
    }

    .select2-selection__clear {
        position: absolute;
        top: 0;
        left: -10px;
        height: 100%;
        padding: 0 12px;
        display: inline-block;
    }

    .btnSearch,
    .btnClear {
        display: inline-block;
        vertical-align: top;
    }


    /* Lock kolom pertama (header + body) */
    #observasiEws th:first-child,
    #observasiEws td:first-child {
        position: sticky;
        left: 0;
        background: #fff;
        /* warna putih supaya tidak transparan */
        z-index: 2;
    }

    /* Lock header agar tetap di atas */
    #observasiEws thead th {
        position: sticky;
        top: 0;
        background: #f5f5f5;
        z-index: 3;
    }

    /* Extra: pastikan header kolom pertama berada di atas body */
    #observasiEws thead th:first-child {
        z-index: 4;
    }

    .resiko-rendah {
        background-color: #a8d08d;
        color: #535353;
    }

    .resiko-sedang {
        background-color: #ffd966;
        color: #535353;
    }

    .resiko-tinggi {
        background-color: #ffc400ff;
        color: #535353;
    }

    .resiko-sangat-tinggi {
        background-color: rgb(255, 192, 203);
        color: #535353;
    }
</style>

<div class="row">
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h6 class="panel-title text-bold"><?= Yii::t('fe', $title) ?></h6>
        </div>
        <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
                'search' => [
                    'attributes' => [
                        'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar',
                        'data-options' => 'click',
                        'id' => 'btn-search-ews'
                    ]
                ],
                'reset' => [
                    'attributes' => [
                        'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                        'data-options' => 'click',
                        'id' => 'btn-refresh-ews',
                    ],
                ],
                'add' => [
                    'title' => 'Input EWS',
                    'attributes' => [
                        'data-options' => 'click',
                        'id' => 'input-ews',
                        'data-width' => '75%',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop_ews',
                        'action' => '/' . $modul . '/' . $url . '/input-ews?pendaftaran_id=' . $pendaftaranId,
                    ],
                ],
                'excel' => [
                    'attributes' => [
                        'data-options' => 'click',
                        'id' => 'btn-cetak-excel',
                    ],
                ],
                'unduh' => [
                    'title' => 'Buku Panduan',
                    'icon' => 'fa fa-file-pdf-o',
                    'attributes' => [
                        'data-options' => 'click',
                        'id' => 'btn-panduan',
                        'data-target' => '#modal-preview',
                        'data-url' => '/rajal/pemeriksaan/buku-panduan-ews?'
                    ],
                ],
            ]);
            ?>
        </div>
        <div class="panel-body">
            <div class="col-12 mt-3">
                <button type="button" style="display: none;" id="load-more"></button>
                <button type="button" style="display: none;" id="scroll-load"></button>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label style="width: 100px;"><?= Yii::t('fe', 'Jenis EWS'); ?><span class="text-danger text-bold ml-1">*</span> :</label>
                    <?=
                    Html::dropDownList('jenis_ews', $latestJenisEws, [], [
                        'class' => 'form-control input-sm selectJenisEws',
                        'required' => true,
                        'prompt' => Yii::t('fe', '--Pilih--'),
                    ]);
                    ?>
                </div>
                <p class="help-block text-danger hidden" id="error-jenis-ews">
                    Jenis EWS Tidak Boleh Kosong !
                </p>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="tgl_pendaftaran">Tanggal EWS :</label>
                    <div class="input-group">
                        <input id="tgl_pendaftaran-startDate" type="text" class="form-control input-xs startDateEws" readonly="">
                        <span class="input-group-addon">-</span>
                        <input id="tgl_pendaftaran-endDate" type="text" class="form-control input-xs endDateEws" readonly="">
                        <input type="text" style="display:none" id="tgl_ews_target" name="tgl_pendaftaran" class="targetDateEws" readonly="true">
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-3" id="banner-ews"></div>
            <div class="col-md-12 mt-4 p-0">
                <div class="table-wrapper" id="content-table-observasi-ews">
                    <table id="observasiEwsEmpty" class="table table-striped table-hover table-bordered no-footer" style="width: 100%;">
                        <thead>
                            <tr class="bg-inverse header-observasi">
                                <th style="width: 250px;"><?= Yii::t('fe', 'Parameter') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-10 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-bold">Legend : </h6>
                    </div>
                    <div class="col-md-3">
                        <button style="width: 10px; height: 25px; border-radius: 50%;" type="button" class="btn resiko-rendah"></button>
                        <span style="font-size: 12px;">Resiko Rendah</span>
                    </div>
                    <div class="col-md-3">
                        <button style="width: 10px; height: 25px; border-radius: 50%;" type="button" class="btn resiko-sedang"></button>
                        <span style="font-size: 12px;">Resiko Sedang</span>
                    </div>
                    <div class="col-md-3">
                        <button style="width: 10px; height: 25px; border-radius: 50%;" type="button" class="btn resiko-tinggi"></button>
                        <span style="font-size: 12px;">Resiko Tinggi</span>
                    </div>
                    <div class="col-md-3">
                        <button style="width: 10px; height: 25px; border-radius: 50%;" type="button" class="btn resiko-sangat-tinggi"></button>
                        <span style="font-size: 12px;">Resiko Sangat Tinggi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_backdrop_ews" class="modal fade" style="z-index: 1041 !important; overflow-y: auto;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>

<div id="modal-preview" class="modal">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-header bg-inverse" style="z-index: 1050">
            <button type="button" id="dismiss-preview-btn" class="close" data-dismiss="modal">&times;</button>
            <h5 class="modal-title">Preview</h5>
        </div>
        <div class="modal-content">
            <div class="preview-wrapper" style="position: relative;" id="preview-wrapper">
                <div class="overlay-preview"></div>
                <iframe frameborder="0" id="preview-content" style="width: 100%; height: 85vh;"></iframe>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
        var pendaftaranId = '" . $pendaftaranId . "';
        var url = '" . $url . "';
        var modul = '" . $modul . "/';
        var latestJenisEws = '".$latestJenisEws."';
        var latestJenisEwsNama = '".$latestJenisEwsNama."';
    ", View::POS_END);
$this->registerJs($this->render('index.js'), View::POS_END);
?>