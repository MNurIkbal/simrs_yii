<?php

/**
 * 
 * @author : Erlangga (erlangga@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DateTimePicker;
use kartik\widgets\DepDrop;
use app\components\DocoConstants;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Asuransi Penjamin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$cetak = 'hidden';
$offset = 'col-md-offset-4';
$final = '';
if ($state) {
    $cetak = '';
    $final = 'hidden';
}
$sep_valid = '';
$sep_valid_btn = '';
if (!$info['nosep']) {
    $sep_valid = 'hidden';
} else {
    $sep_valid_btn = '';
}
?>
<script src="https://rawgit.com/enyo/dropzone/master/dist/dropzone.js"></script>
<link rel="stylesheet" href="https://rawgit.com/enyo/dropzone/master/dist/dropzone.css">
<style type="text/css">
    td.add-diagnosa-wrapper{
        border:0;
        padding:15px !important;
    }
    td.left-side{
        border-right: 0 !important;
    }
    td.right-side{
        width: 100px;
        text-align: center;
        border-left: 0px !important;
    }
    .font-14 {
        font-size: 14px !important;
    }
    .font-13 {
        font-size: 13px !important;
        color: #000;
    }

    .button-select {
        cursor: pointer;
    }

    .dropzone .dz-default.dz-message::before  {
        padding: 30px !important;
    }

    .dz-button {
        margin-top: 30px;
    }

    .margin-10 {
        margin-top: 10px !important;
    }

    .custom-image {
        height: 120px !important;
        width: 120px !important;
        padding: 10px;
    }

    .dz-image {
        margin-top: 75px !important;
    }
</style>
<br>
<input type="hidden" value="<?= $model->naik_kelas ?>" id="set-kelas">
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'backBtn' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'id'    => 'btn-back',
                            'data-options' => 'click',
                        ]
                    ],
                ]); ?>
            </div>
            <div class="panel-body">
                <center>
                    <h3><?= Yii::t('fe', 'E-Klaim INACBGS') ?></h3>
                </center>
                <hr>
                <?php
                $form = ActiveForm::begin([
                    'id' => 'form-proses-klaim',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_MEDIUM]
                ]);
                ?>
                <?= Html::activeHiddenInput($model, 'kunjungan_id', ['class' => 'pendaftaran-id-txt']) ?>
                <!-- <?= Html::activeHiddenInput($model, 'nama_pasien') ?> -->
                <?= Html::activeHiddenInput($model, 'pasienadmisi_id') ?>
                <?= Html::activeHiddenInput($model, 'no_rekam_medik') ?>
                <?= Html::activeHiddenInput($model, 'jeniskelamin') ?>
                <?= Html::activeHiddenInput($model, 'instalasi_id') ?>
                <?= Html::activeHiddenInput($model, 'pasien_id') ?>
                <?= Html::activeHiddenInput($model, 'dokterdpjp_id') ?>
                <!-- <?= Html::activeHiddenInput($model, 'tgl_masuk') ?> -->
                <!-- <?= Html::activeHiddenInput($model, 'tgl_keluar') ?> -->
                <!-- <?= Html::activeHiddenInput($model, 'total_tarifrs') ?> -->
                <?= Html::activeHiddenInput($model, 'no_sep', ['class' => 'no-sep']) ?>
                <?= Html::activeHiddenInput($model, 'umur') ?>
                <!-- <?= Html::activeHiddenInput($model, 'berat_lahir') ?> -->
                <?= Html::activeHiddenInput($model, 'los') ?>
                <?= Html::activeHiddenInput($model, 'adl_subacute') ?>
                <?= Html::activeHiddenInput($model, 'adl_cronic') ?>
                <?= Html::activeHiddenInput($model, 'carapulang_id') ?>
                <?= Html::activeHiddenInput($model, 'diagnosa_primer', ['id' => 'klaiminacbgranapform-diagnosa_primer']) ?>
                <?= Html::activeHiddenInput($model, 'diagnosa_sekunder', ['id' => 'klaiminacbgranapform-diagnosa_sekunder']) ?>
                <?= Html::activeHiddenInput($model, 'no_kartu') ?>
                <?= Html::activeHiddenInput($model, 'tgl_lahir') ?>
                <?= Html::activeHiddenInput($model, 'nama_dokter') ?>

                <div class="row user-info">
                    <div class="col-md-12" style="margin-bottom: 20px;">
                        <?= $this->render('@app/modules/penjaminasuransi/views/informasi-pasien-ranap-bpjs/_form_input_header', [
                            'info' => $info,
                            'noKartu' => $noKartu,
                            'opsi' => $opsi,
                            'model' => $model
                        ]) ?>
                    </div>
                    <div class="col-md-12">
                        <?= $this->render('@app/modules/penjaminasuransi/views/informasi-pasien-ranap-bpjs/_form_detail_pasien', [
                            'info' => $info,
                            'jenistarif' => $jenistarif,
                            'opsi' => $opsi,
                            'expUmur' => $expUmur,
                            'sep_valid_btn' => $sep_valid_btn,
                            'dokterDpjp' => $dokterDpjp,
                            'model' => $model
                        ]) ?>
                    </div>
                </div>

                <div>
                    <hr>
                    <?= $this->render('@app/modules/penjaminasuransi/views/informasi-pasien-ranap-bpjs/_form_tarif', [
                        'model' => $model
                    ]) ?>
                    <!-- end tarif -->
                    <br>
                    <div id="modal-preview" class="modal">
                        <div class="modal-dialog modal-lg" style="width: 90%;">
                            <div class="modal-header bg-inverse" style="z-index: 1050">
                                <button type="button" id="dismiss-preview-btn" class="close" data-dismiss="modal">&times;</button>
                                <h5 class="modal-title">Preview</h5>
                            </div>
                            <div class="modal-content">
                                <div class="preview-wrapper" style="margin-top: -50pekpx; position: relative;" id="preview-wrapper">
                                    <div class="overlay-preview"></div>
                                    <iframe frameborder="0" id="preview-content" style="width:100%;height:90vh"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <p><i><?= Html::checkbox('agree', true, ['disabled' => true]); ?> Menyatakan benar bahwa data tarif yang tersebut di atas adalah benar sesuai dengan kondisi yang sesungguhnya.</i></p>
                        <h3 class="covid-select hidden"><b>Unggah Berkas Pendukung Klaim</b>    
                        </div></h3>
                    </div>
                    <hr>
                    <div class="row covid-berkas covid-select hidden">
                    <div class="col-md-12">
                        <table class="table">
                            <tr>
                                <th class="label-grouper" width="170">
                                    <?= Yii::t('fe', 'Resume Medis') ?>
                                </th>
                                <td>
                                    <div id="upload1" class="fallback dropzone" enctype="multipart/form-data">
                                    <input type="file" class="input-file" id="files1" class="display" multiple />
                                    </div>
                                </td>
                                <td class="text-right" width="170">
                                    <span class="button-select">
                                        [ <span class="fileinput-1 fileselect"><?= Yii::t('fe', '  pilih berkas  ') ?></span> ]
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="label-grouper">
                                    <?= Yii::t('fe', 'Kartu Identitas') ?>
                                </th>
                                <td>
                                    <div id="upload8" class="fallback dropzone" enctype="multipart/form-data">
                                        <input type="file" class="input-file" id="files8" class="display" multiple />
                                    </div> 
                                </td>
                                <td class="text-right">
                                    <span class="button-select">
                                        [ <span class="fileinput-8 fileselect"><?= Yii::t('fe', '  pilih berkas  ') ?></span> ]
                                    </span>
                                </td>
                            </tr>
                            <tr class="kipi-section hidden">
                                <th class="label-grouper">
                                    <?= Yii::t('fe', 'Dokumen KIPI') ?>
                                </th>
                                <td>
                                    <div id="upload-dokumen-kipi" class="fallback dropzone" enctype="multipart/form-data">
                                        <input type="file" class="input-file" id="files-dokumen-kipi" class="display" multiple />
                                    </div> 
                                </td>
                                <td class="text-right">
                                    <span class="button-select">
                                        [ <span class="fileinput-kipi fileselect"><?= Yii::t('fe', '  pilih berkas  ') ?></span> ]
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="label-grouper">
                                    <?= Yii::t('fe', 'Surat Bebas Biaya') ?>
                                </th>
                                <td>
                                    <div id="upload-bebas-biaya" class="fallback dropzone" enctype="multipart/form-data">
                                        <input type="file" class="input-file" id="files-upload-biaya" class="display" multiple />
                                    </div>
                                    <div>
                                        <span id="source"></span>
                                    </div>
                                   <div>
                                <td class="text-right">
                                    <span class="button-select">
                                        [ <span class="bebas-biaya-input fileselect"><?= Yii::t('fe', '  pilih berkas  ') ?></span> ]
                                    </span>
                                </td>
                            </tr>
                        </table>
                        <hr>
                    </div>
                </div>
                    <div class="row">
                        <div class="col-md-2"><br><label><b>Diagnosa (ICD 10)</b></label></div>
                        <div class="col-md-10">
                            <table class="table table-bordered tbl-icd-10" style="border-collapse: collapse;">
                                <tbody>
                                    <?php
                                    $dupliDiagnosa = [];
                                    $icdPrimer = '';
                                    $i = 0;
                                    foreach ($diagnosa as $key => $value) {
                                        if ($value['is_icdprimer'] == 'true') {
                                            $icdPrimer = $value['diagnosa_kode'];
                                        }
                                        if (!in_array($value['diagnosa_kode'], $dupliDiagnosa)) {
                                            $dupliDiagnosa[] = $value['diagnosa_kode'];
                                            if ($value['kelompokdiagnosa_id'] == DocoConstants::MAP_DIAGNOSA_TAMBAHAN) {
                                                $primarybadge = '';
                                                if ($value['is_icdprimer'] == 'true' || $icdPrimer == $value['diagnosa_kode']) {
                                                    $primarybadge = '<span id="label-primary" class="badge badge-warning font-14" type="10" dig-id="' . $value['diagnosa_id'] . '" dig-kode="' . $value['diagnosa_kode'] . '" style="float:right">ICD Primer</span>';
                                                }
                                    ?>
                                                <tr>
                                                    <th class="list-diagnosa" style="width: 60%; border-right: 0px;"><?= $value['diagnosa_nama'] ?>
                                                    <th class="dig-aksi" style="border-right: 0px;border-left: 0px;">
                                                        <?php
                                                        if ($value['is_icdprimer'] != 'true' || $icdPrimer != $value['diagnosa_kode']) {
                                                        ?>
                                                            <button id="set-<?= $value['diagnosa_id'] ?>" style="float:right; margin-top: -3px;" data-target="dig-icd-10" data-key="<?= 'a' . $i ?>" class="btn btn-warning btn-md set-primer hide-me font-13" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>>Set Primer</button>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <button id="set-<?= $value['diagnosa_id'] ?>" style="float:right; margin-top: -3px;" data-target="dig-icd-10" data-key="<?= 'a' . $i ?>" class="btn btn-warning btn-md set-primer hide-me hidden font-13" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>>Set Primer</button>
                                                        <?php
                                                        }
                                                        ?>
                                                    </th>
                                                    </th>
                                                    <th class="dig-info" style="width: 190px; border-left: 0px; border-right: 0px;">
                                                        <?= $primarybadge ?> <span class="badge badge-primary font-14" style="float:left;margin-right: 3px"><?= $value['diagnosa_kode'] ?> </span>
                                                        <?php
                                                        if ($value['is_icdprimer'] != 'true' || $icdPrimer != $value['diagnosa_kode']) {
                                                        ?>
                                                            <button id="del-<?= $value['diagnosa_id'] ?>" style="float:right; margin-top: -3px;" data-target="tbl-icd-10" data-key="<?= 'p' . $i ?>" class="btn btn-danger btn-lg btn-remove-diagnosa hide-me" style="float:left" type="10" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>><i class="fa fa-trash"></i></button>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <button id="del-<?= $value['diagnosa_id'] ?>" style="float:right; margin-top: -3px;" data-target="tbl-icd-10" data-key="<?= 'p' . $i ?>" class="btn btn-danger btn-lg btn-remove-diagnosa hide-me hidden" style="float:left" type="10" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>><i class="fa fa-trash"></i></button>
                                                        <?php
                                                        }
                                                        ?>
                                                    </th>
                                                </tr>
                                                <?php $i++ ?>
                                    <?php
                                            }
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <br>
                            <table class="table table-bordered hide-me" style="border-collapse: collapse;table-layout: fixed;">
                                <tr>
                                    <td class="add-diagnosa-wrapper left-side">
                                        <label>Tambah diagnosa</label><br>
                                        <?= Html::dropDownList('diagnosa', '', [], 
                                            ['class' => 'form-control select2 select-diagnosa-10', 
                                            'data-url' => Url::to(['get-diagnosa', 'type' => 10]),
                                            'data-target' => 'tbl-icd-10',
                                            'data-type' => '10'
                                        ]) ?></td>
                                    <!-- <td class="add-diagnosa-wrapper right-side">
                                        <br>
                                        <button type="button" class="btn btn-success btn-sm btn-add-diagnosa" data-type="10" data-target="tbl-icd-10"><i class="fa fa-plus"></i></button>
                                    </td> -->
                                </tr>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-2"><br><label><b>Diagnosa (ICD 9)</b></label></div>
                        <div class="col-md-10">
                            <table class="table table-bordered tbl-icd-9" style="border-collapse: collapse;">
                                <?php
                                $dupliProc = [];
                                $x = 0;
                                foreach ($diagnosa as $key => $value) {
                                    if (!in_array($value['diagnosa_kode'], $dupliProc)) {
                                        $dupliProc[] = $value['diagnosa_kode'];
                                        if ($value['kelompokdiagnosa_id'] == DocoConstants::DIAGNOSA_TERAPI) {
                                            $primarybadge = '';
                                ?>
                                            <tr>
                                                <th style="width: 60%; border-right: 0px;"><?= $value['diagnosa_nama'] ?> </th>
                                                <th class="dig-aksi" style="border-right: 0px;border-left: 0px;">
                                                </th>
                                                <th class="dig-info text-right" style="border-left: 0px; width: 190px;">
                                                    <span class="badge badge-primary font-14" style="margin-right: 30px"><?= $value['diagnosa_kode'] ?></span>
                                                    <button style="float:right; margin-top: -3px" data-target="tbl-icd-9" data-key="<?= 's' . $x ?>" class="btn btn-danger btn-lg btn-remove-diagnosa hide-me" style="float:left" type="9" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>><i class="fa fa-trash"></i></button>
                                                </th>
                                            </tr>
                                    <?php
                                            $x++;
                                        }
                                    }
                                }
                                if ($x == 0) {
                                    ?>
                                    <tr class="row-null empty-icd9">
                                        <th colspan="2" style="color:red">Tidak Ada diagnosa dengan ICD 9 yang dipilih</th>
                                    </tr>
                                <?php
                                }
                                ?>
                            </table>
                            <br>
                            <table class="table table-bordered hide-me" style="border-collapse: collapse;table-layout: fixed;">
                                <tr>
                                    <td class="add-diagnosa-wrapper left-side"><label>Tambah diagnosa</label><br>
                                        <?= Html::dropDownList('diagnosa', '', [], 
                                            [
                                                'class' => 'form-control select2 select-diagnosa-9', 
                                                'data-url' => Url::to(['get-diagnosa', 'type' => 9]),
                                                'data-target' => 'tbl-icd-9',
                                                'data-type' => '9'
                                        ]) 
                                            ?>
                                    </td>
                                    <!-- <td class="add-diagnosa-wrapper right-side">
                                        <br>
                                        <button type="button" class="btn btn-success btn-sm btn-add-diagnosa" data-type="9" data-target="tbl-icd-9"><i class="fa fa-plus"></i></button>
                                    </td> -->
                                </tr>
                            </table>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4 text-center">
                            <button type="button" class="btn btn-success btn-xs btn-labeled" id="btn-proses" <?= $disabledProses ?>><b><i class="fa fa-save"></i></b> <?= Yii::t('fe', 'Proses') ?></button>
                            <button disabled="disabled" type="button" class="btn btn-danger btn-xs btn-labeled" id="btn-hapus-klaim"><b><i class="fa fa-trash"></i></b><?= Yii::t('fe', 'Hapus Klaim') ?></button>
                        </div>
                    </div>
                    <?php
                    /* "A Product of PT Docotel Teknologi Powered by Sirs" */
                    ActiveForm::end();
                    ?>
                    <br>
                    <div id="proses-final-klaim" class="hidden">
                        <hr>
                        <?= $this->render('@app/modules/penjaminasuransi/views/informasi-pasien-ranap-bpjs/_grouper_result', [
                            'model' => $model
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
/* "A Product of PT Docotel Teknologi Powered by Sirs" */

$this->registerCss($this->render('css/eklaim.css'));
$this->registerJs("
    var _detailDiagnosa = " . $detailDiagnosa . ";
    var _final = '" . $state . "';
    var _updated = '" . $update_dec . "'
    var _diajukan = '" . $isAjukan . "'
    var jenisRawat = '" . $jenisRawat . "';
    var infoTxt = '" . $infoTxt . "';
    var is_rawatintensif = '" . false . "';
    var jenisKelasRawatAwal = '" . $jenisKelasRawatAwal . "';
    var tambahanBiaya = '" . $tambahanBiaya . "';
    var instalasi_nama = '" . $instalasiNama . "';
    var infoNoSep = '" . $info['nosep'] . "';
    var namaPasien = '" . addslashes(strtolower($info['nama_pasien'])) . "';
    var noRm = '" . strtolower($info['no_rekammedik']) . "';
    var kunjunganId = '" . $id . "';
    var isCheck = '" . $isChek . "';
    var stateEnc = '".$stateEnc."';
    var idEnc = '".$idEnc."';
    var COVID = '".$payorCovid."';
    var KIPI = '".$payorKipi."';
    var JAMPERSAL = '".$payorJampersal."';
    var COINSIDENSE = '".$payorCoinsidense."';
    var BAYIBARULAHIR = '".$payorBayi."';
    var PERPANJANGANMASARAWAT = '".$payorPerpanjanganRawat."';
    var JKN = '".$payorJkn."';
    var dateNow = '" . date('d-M-Y H:i', strtotime('now')) . "';
    var hakKelasBpjs = '".$hakKelas."';
    
    " . $this->render('eklaim.js'), View::POS_END, 'js');

?>