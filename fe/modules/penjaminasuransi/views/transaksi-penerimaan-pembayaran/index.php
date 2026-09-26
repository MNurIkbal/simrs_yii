<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-25 17:30:42
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-28 10:25:15
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\datetime\DateTimePicker;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use app\components\DHtml;
use app\components\DocoConstants;

$this->title = DHtml::getTitleMenu();
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace('modul_alias'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Transaksi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    #file-upload {
      /* display: none; */
      margin: 10px;
    }

    .inputfile+label {
        max-width: 100%;
        font-size: 1.25rem;
        /* 20px */
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
        display: inline-block;
        overflow: hidden;
        /* padding: 0.625rem 1.25rem; */
        padding: 7px 25px;
        width: auto;
        /* 10px 20px */
    }

    .no-js .inputfile+label {
        display: none;
    }

    .inputfile:focus+label,
    .inputfile.has-focus+label {
        outline: 1px dotted #000;
        outline: -webkit-focus-ring-color auto 5px;
    }
    .inputfile+label svg {
        width: 1em;
        height: 1em;
        vertical-align: middle;
        fill: currentColor;
        margin-top: -0.25em;
        margin-right: 0.25em;
    }

    .inputfile-1+label {
        color: #f1e5e6;
        background-color: #54be8b;

    }

    .inputfile-1:focus+label,
    .inputfile-1.has-focus+label,
    .inputfile-1+label:hover {
        background-color: #078448;
    }
    .component_form--left {
        margin-top: 10px;
        margin-left: -5px;
    }

    .component_form--notes {
        margin-left: -5px;
    }

    .component_form--bank {
        margin-top: 10px;
        margin-left: -5px;
    }

    .content__penjajuan {
        width: 99%;
        margin-left: 0px;
    }

    .table-content {
        width: 99%;
        margin-left: 0px;
        margin-bottom: 30px;
    }
    .kv-datetime-picker{
        display : none;
    }
    .kv-datetime-remove{
        border-right: 1px solid #ddd !important;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::t('fe', $title); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'save' => [
                        'attributes' => [
                            'onClick' => null,
                            'id' => 'simpan-penerimaan'
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                ]); ?>
            </div>
            <div class="panel-body">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'form-penerimaan-pembayaran',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'labelSpan' => 4,
                        'deviceSize' => ActiveForm::SIZE_MEDIUM
                    ],
                    'options' => [
                        'enctype' => 'multipart/form-data',
                        'skip-confirm' => "true"
                    ]
                ]);
                ?>
                <div class="row component_form--left">
                    <div class="col-md-6">
                        <div class="form-group field-penerimaanpembayaranform-tgl_terimabayarklaim required">
                            <label class="control-label col-md-4" for="penerimaanpembayaranform-tgl_terimabayarklaim"><?= $model->attributeLabels()['tgl_terimabayarklaim'] ?></label>
                            <div class="col-md-6">
                                <?=DateTimePicker::widget([
                                            'model' => $model,
                                            'attribute' => 'tgl_terimabayarklaim',
                                            'type' => DateTimePicker::TYPE_COMPONENT_APPEND ,
                                            'readonly' => true,
                                            'name' => "PenerimaanPembayaranForm[tgl_terimabayarklaim]",
                                            'id' => 'penerimaanpembayaranform-tgl_terimabayarklaim',
                                            'convertFormat' => true,
                                            'pluginOptions' => [
                                                'disabled' => true,
                                                'format' => 'yyyy-MM-dd hh:mm:ss',
                                                'timePicker24Hour'=>true,
                                                'startDate' => date('Y-m-d H:i:s',strtotime('-1 minute')),
                                                'autoclose' => true,
                                                'minuteStep' => 1,
                                                'endDate' => date('Y-m-d H:i:s')
                                            ]
                                        ]);
                                    ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group field-penerimaanpembayaranform-carabayar_id required">
                            <label class="control-label col-md-4" for="penerimaanpembayaranform-carabayar_id"><?= $model->attributeLabels()['carabayar_id'] ?></label>
                            <div class="col-md-6">
                                <?php
                                $listCarabayra = ArrayHelper::map($carabayar, 'carabayar_id', 'carabayar_nama');
                                if (isset($listCarabayra[5])) {
                                    unset($listCarabayra[5]);
                                }
                                ?>
                                <?= Html::activeDropdownList(
                                    $model,
                                    'carabayar_id',
                                    $listCarabayra,
                                    [
                                        'class' => 'form-control select2 cara-pembayaran',
                                        'prompt' => Yii::t('fe', '-- Pilih Cara Bayar --')
                                    ]
                                ) ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group field-penerimaanpembayaranform-penjamin_id required">
                            <label class="control-label col-md-4" for="penerimaanpembayaranform-penjamin_id"><?= $model->attributeLabels()['penjamin_id'] ?></label>
                            <div class="col-md-6">
                                <?= DepDrop::widget([
                                    'name' => 'PenerimaanPembayaranForm[penjamin_id]',
                                    'options' => [
                                        'disabled' => false,
                                        'class' => 'form-control select2 cara-pembayaran',
                                        'id' => 'penjamin_id',
                                    ],
                                    'pluginOptions' => [
                                        'depends'  => ['penerimaanpembayaranform-carabayar_id'],
                                        'placeholder' => '',
                                        'url' => Url::to(['get-penjamin'])
                                    ]
                                ]) ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="before-upload-wrapper">
                           <div class="form-group">
                                <label class="control-label col-md-4"> Upload File </label>
                                <div class="col-md-6">
                                <!-- <input type="file" name="TransaksiAlokasiImportForm[upload_file]" id="file-upload" class="form-control inputfile inputfile-1" data-multiple-caption="{count} files selected"> -->
                                    <button 
                                        type="button" 
                                        class="btn btn-info btn-xs btn-modal-upload" 
                                        data-toggle="modal" 
                                        data-target="#modal_backdrop" 
                                        action="/penjamin-asuransi/transaksi-penerimaan-pembayaran/upload">Upload File</button>
                                </div>
                           </div>
                           <div class="progress-wrapper form-group">
                              <div class="col-md-12">
                                 <div class="progress hidden">
                                    <div class="progress-bar progress-bar-success myprogress" role="progressbar" style="width:0%">0%</div>
                                 </div>
                                 <div class="msg"></div>
                              </div>
                           </div>
                           <br>
                        </div>
                        <div class="form-group field-penerimaanpembayaranform-no_terimabayarklaim required">
                            <label class="control-label col-md-4" for="penerimaanpembayaranform-no_terimabayarklaim"><?= $model->attributeLabels()['no_terimabayarklaim'] ?></label>
                            <div class="col-md-6">
                                <?= Html::activeTextInput($model, 'no_terimabayarklaim', ['class' => 'form-control']) ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group field-penerimaanpembayaranform-total_terimabayar required">
                            <label class="control-label col-md-4" for="penerimaanpembayaranform-total_terimabayar"><?= $model->attributeLabels()['total_terimabayar'] ?></label>
                            <div class="col-md-6">
                                <?= Html::activeTextInput($model, 'total_terimabayar', ['class' => 'form-control doco-number', 'id' => 'total_terimabayar']) ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group field-penerimaanpembayaranform-pegawaipenerima_id required">
                            <label class="control-label col-md-4" for="penerimaanpembayaranform-pegawaipenerima_id"><?= $model->attributeLabels()['pegawaipenerima_id'] ?></label>
                            <div class="col-md-6">
                                <?= Html::activeDropdownList($model, 'pegawaipenerima_id', [], ['class' => 'form-control select2']) ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <?= Html::activeCheckbox($model, 'is_nontunai', [
                            'class' => 'styled'
                        ]) ?>
                        <br>
                        <div class="row component_form--bank">
                            <div class="col-md-8">
                                <fieldset>
                                    <legend><?= Yii::t('fe', 'Bank Detail') ?></legend>
                                    <div class="form-group field-penerimaanpembayaranform-pemilik_rekening required">
                                        <label class="control-label col-md-4" for="penerimaanpembayaranform-pemilik_rekening"><?= $model->attributeLabels()['pemilik_rekening'] ?></label>
                                        <div class="col-md-8">
                                            <?= Html::activeTextInput($model, 'pemilik_rekening', ['class' => 'form-control', 'readonly' => true]) ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                    <div class="form-group field-penerimaanpembayaranform-bank required">
                                        <label class="control-label col-md-4" for="penerimaanpembayaranform-bank"><?= $model->attributeLabels()['bank'] ?></label>
                                        <div class="col-md-8">
                                            <?= Html::activeTextInput($model, 'bank', ['class' => 'form-control', 'readonly' => true]) ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                    <div class="form-group field-penerimaanpembayaranform-no_rekening required">
                                        <label class="control-label col-md-4" for="penerimaanpembayaranform-no_rekening"><?= $model->attributeLabels()['no_rekening'] ?></label>
                                        <div class="col-md-8">
                                            <?= Html::activeTextInput($model, 'no_rekening', ['class' => 'form-control', 'readonly' => true]) ?>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row component_form--notes">
                    <div class="col-md-12">
                        <div class="form-group field-penerimaanpembayaranform-catatan">
                            <label class="control-label col-md-2" for="penerimaanpembayaranform-catatan"><?= $model->attributeLabels()['catatan'] ?></label>
                            <div class="col-md-8">
                                <?= Html::activeTextArea($model, 'catatan', ['rows' => 4, 'class' => 'form-control',]) ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>
                    <?php ActiveForm::end() ?>
                    <div class="row form-ajuan-detail" style="display: none">
                        <div class="col-md-12">
                            <?php
                            $form2 = ActiveForm::begin([
                                'id' => 'form-ajuan',
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_MEDIUM]
                            ]);
                            ?>
                            <div class="row content__penjajuan">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend><?= Yii::t('fe', 'Alokasi Pengajuan') ?></legend>
                                        <div class="col-md-2">
                                            <label class="required" for="penerimaanpembayaranform-pengajuanklaim_id">
                                                <?= Yii::t('fe', 'No Ajuan') ?>
                                            </label>
                                            <?= Html::activeDropdownList($modelAjuan, 'pengajuanklaim_id', [], [
                                                'class' => 'form-control input-sm select2',
                                                'id' => 'no-pengajuanklaim'
                                            ]) ?>
                                            <div class="help-block"></div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="required" for="penerimaanpembayaranform-total_pengajuan"><?= Yii::t('fe', 'Total Pengajuan') ?></label>
                                            <?= Html::activeTextInput($modelAjuan, 'total_pengajuan', [
                                                'class' => 'text-right form-control doco-number input-sm',
                                                'readonly' => true,
                                                'id' => 'total-pengajuan'
                                            ]) ?>
                                            <div class="help-block"></div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="required" for="penerimaanpembayaranform-total_terbayar"><?= Yii::t('fe', 'Telah Bayar') ?></label>
                                            <?= Html::activeTextInput($modelAjuan, 'total_terbayar', [
                                                'class' => 'text-right form-control doco-number input-sm',
                                                'readonly' => true,
                                                'id' => 'total-terbayar'
                                            ])
                                            ?>
                                            <div class="help-block"></div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="required" for="penerimaanpembayaranform-pembayaran"><?= Yii::t('fe', 'Pembayaran') ?></label>
                                            <?= Html::activeTextInput($modelAjuan, 'pembayaran', ['class' => 'text-right form-control doco-number input-sm', 'id' => 'pembayaran']) ?>
                                            <div class="help-block"></div>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="required" for="penerimaanpembayaranform-total_sisapiutang"><?= Yii::t('fe', 'Total Sisa Piutang') ?></label>
                                            <?= Html::activeTextInput($modelAjuan, 'total_sisapiutang', ['class' => 'text-right form-control doco-number input-sm', 'readonly' => true, 'id' => 'total-sisapiutang']) ?>
                                            <div class="help-block"></div>
                                        </div>
                                        <?= Html::activeHiddenInput($modelAjuan, 'no_pengajuanklaim', ['id' => 'pengajuanklaim-id']) ?>
                                        <?= Html::activeHiddenInput($modelAjuan, 'status_pengajuan', [
                                            'id' => 'status_pengajuan'
                                        ]) ?>
                                        <?= Html::activeHiddenInput($modelAjuan, 'tgl_pengajuanklaim', [
                                            'id' => 'tgl_pengajuanklaim'
                                        ]) ?>
                                        <div class="col-md-1">
                                            <br>
                                            <button type="button" class="btn btn-md btn-success btn-add-ajuan"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                        <?php ActiveForm::end() ?>
                    </div>
                    <br>
                    <div class="row table-content">
                        <div class="col-md-12" style="margin-top: 20px;">
                            <div id="error_PenerimaanPembayaranFormdata_pengajuan"></div>
                            <table id="tbl-ajuan" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th>No</th>
                                        <th><?= Yii::t('fe', 'No Pengajuan') ?></th>
                                        <th><?= Yii::t('fe', 'Total Pengajuan') ?></th>
                                        <th><?= Yii::t('fe', 'Telah Bayar') ?></th>
                                        <th><?= Yii::t('fe', 'Pembayaran') ?></th>
                                        <th><?= Yii::t('fe', 'Sisa Piutang') ?></th>
                                        <th><?= Yii::t('fe', 'Aksi') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Untuk Kebutuhan Modal import klaim -->
<div id="modal_backdrop" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Import Data Klaim</h5>
            </div>
            <hr>
            <center><span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
            <div class="modal-body">
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                    <span class="label-persentase"></span>%</div>
                </div>
                <span class="help-block label-progress"></span>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJs('
    var _table; 
    var ruangan = ' . Yii::$app->docoVars->workspace("ruangan_id") . ';'
    . $this->render('/asset/global.js'), View::POS_END, 'jxxs');

$this->registerJs('
    var _table; 
    var _tableBpjs;
    var _caraBayarBpjs = "'.DocoConstants::CARA_BAYAR_BPJS.'"
    ' . $this->render('index.js'), View::POS_END, 'js');
?>