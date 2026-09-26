<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Jenis Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DateTimePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Radiologi', 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => 'Informasi Pasien Rujukan '.$instalasi, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .datepicker>div{
        display:block;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'back' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-kembali',
                            'id' => 'btn-kembali',
                            'onClick' => null,
                        ]
                    ],
                    'save' => [
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-check-square-o',
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-simpan',
                            'id' => 'btn-simpan',
                            'onClick' => null,
                        ]
                    ],
                    'cetak-rujukan' => [
                        'title' => \Yii::t('fe', 'Cetak Rujukan'),
                        'icon' => 'fa fa-print',
                        'method' => '#',
                        'attributes' => [
                        'id'=>'cetak-rujukan',
                        'disabled' => true,
                        'data-options' => 'link',
                            'target'=>'_blank',
                        ]
                    ],
                ], '#example') ?>
            </div>
            <div class="panel-body">
                <?= Yii::$app->controller->renderPartial('_info_pasien', ['detail' => $detail]) ?>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', $this->title); ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12 filter-form"></div>
                                <div class="col-md-12">
                                    <?php $form = ActiveForm::begin(
                                        [
                                            'action' => 'rujuk',
                                            'method' => 'post',
                                            'id' => 'form-rujuk',
                                            'type' => ActiveForm::TYPE_VERTICAL,
                                            'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
                                            'enableAjaxValidation' => false,
                                            'enableClientValidation' => false,
                                        ]
                                    ) ?>
                                    <?= Html::hiddenInput('pasienkirimkeunitlain_id', DocoHelpers::encrypt($id), ['id' => 'pasien-kirim-unit-lain-id', 'readonly' => 'readonly']) ?>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <?= $form->field($model, 'tanggal_rujukan')->widget(DateTimePicker::classname(), [
                                                'options' => ['placeholder' => 'Tanggal Rujukan', 'readonly' => true],
                                                'language' => 'en',
                                                'pluginOptions' => [
                                                   'autoclose' => true,
                                                   'format' => 'dd-mm-yyyy hh:ii',
                                                   'endDate' => date('Y-m-d H:i'),
                                                   'todayHighlight' => true,
                                                ]
                                            ]); ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= $form->field($model, 'rs_tujuan')->dropDownList([], [
                                                'class' => 'form-control select2 rs_tujuan',
                                                'prompt' => '— Pilih Rumah Sakit Tujuan —',
                                            ]); ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= $form->field($model, 'pegawai_menyetujui')->dropDownList([], [
                                                'class' => 'form-control select2 pegawai_menyetujui',
                                                'prompt' => '— PILIH —',
                                            ]); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <?= $form->field($model, 'alasan_rujukan')->textArea([
                                                'class' => 'form-control',
                                                'placeholder' => 'Alasan Rujukan',
                                            ]); ?>
                                        </div>
                                    </div>
                                    <?php ActiveForm::end(); ?>
                                </div>
                            </div><br>
                            <table class="table table-striped table-hover no-footer screening-table" id="example">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th><?= Yii::t('fe', 'No') ?></th>
                                        <th><?= Yii::t("fe", "Jenis Pemeriksaan") ?></th>
                                        <th><?= Yii::t("fe", "Nama Pemeriksaan") ?></th>
                                        <th style="width:300px"><?= Yii::t("fe", "Diagnosa") ?></th>
                                        <th style="width:20px"><?= Yii::t("fe", "Qty") ?></th>
                                        <th style="width:50px"><?= Yii::t("fe", "Cyto") ?></th>
                                        <th style="width:300px"><?= Yii::t("fe", "Dokter Perujuk") ?></th>
                                        <th style="width:20px"><?= Yii::t("fe", "Dirujuk") ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="8"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var table;
var pasienKirimKeUnitLainId = ' . $id . ';
var _instalasi = "'.DocoConstants::INSTALASI_ID_RAD.'";
var _dokterRujukId = "'.$dokterRujukId.'";
var _dokterRujukNama = "'.$dokterPerujuk.'";
var _diagnosaUtamaId = "'.$diagnosaUtamaId.'";
var _diagnosaUtamaNama = "'.$diagnosaUtamaNama.'";
const cetakRujukUrl = "/radiologi/inf-pasien-rujukan-rad/cetak-rujukan?instalasi_id='.$instalasiId.'&id='.DocoHelpers::encrypt($id).'&rujukankeluar_id=";

', View::POS_END);
$this->registerJs($this->render('js/form_rujuk.js'), View::POS_END);
?>