<?php

    /**
     * @Author: rizqi_fitrianto
     * @Date:   2018-09-25 17:30:42
     * @Last Modified by:   rizqi_fitrianto
     * @Last Modified time: 2018-09-28 10:25:15
     */

    use yii\web\View;
    use yii\helpers\Url;
    use yii\helpers\Html;
    use app\components\DHtml;
    use yii\widgets\Breadcrumbs;
    use yii\helpers\ArrayHelper;
    use kartik\widgets\DepDrop;
    use kartik\widgets\ActiveForm;
    use kartik\datetime\DateTimePicker;
    use app\components\DocoHelpers;
    use app\components\DocoConstants;

    $this->title = DHtml::getTitleMenu();
    $this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace('modul_alias'), 'url' => ['index']];
    $this->params['breadcrumbs'][] = ['label' => 'Informasi', 'url' => ['index']];
    $this->params['breadcrumbs'][] = $this->title;
?>

<style>
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
                    'back',
                    'save' => [
                        'title' => 'Update',
                        'attributes' => [
                            'onClick'  => null,
                            'id'       => 'simpan-penerimaan',
                            'disabled' => $statusAlokasi ? true : false
                        ]
                    ],
                    'rincian-detail' => [
                        'type' => 'button',
                        'title' => 'Unduh Excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'cetak-detail',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '75%',
                            'data-url' => '/penjamin-asuransi/informasi-penerimaan-pembayaran/show-popup-detail?id=' . $id.'&',
                        ]
                    ],
                ], '#tbl-ajuan'); ?>
            </div>
            <div class="panel-body">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'form-penerimaan-pembayaran',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'action' => '/penjamin-asuransi/informasi-penerimaan-pembayaran/save?id=' . $id,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_MEDIUM]
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
                                            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                            'readonly' => true,
                                            'value' => !empty($model->tgl_terimabayarklaim)? date('d-M-Y H:i:s', strtotime($model->tgl_terimabayarklaim)): '',
                                            'name' => "PenerimaanPembayaranForm[tgl_terimabayarklaim]",
                                            'id' => 'penerimaanpembayaranform-tgl_terimabayarklaim',
                                            // 'convertFormat' => true,
                                            'pluginOptions' => [
                                                // 'disabled' => true,
                                                'format' => 'yyyy-mm-dd hh:i:ss',
                                                // 'startDate' => date('Y-m-d H:i:s'),
                                                // 'autoclose' => true,
                                                // 'minuteStep' => 1,
                                                // 'timePicker24Hour' => true,
                                                // 'endDate' => date('Y-m-d H:i:s',strtotime('+1 minute'))
                                            ],
                                        ]);
                                    ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group field-penerimaanpembayaranform-no_terimabayarklaim required">
                            <label class="control-label col-md-4" for="penerimaanpembayaranform-no_terimabayarklaim"><?= $model->attributeLabels()['no_terimabayarklaim'] ?></label>
                            <div class="col-md-6">
                                <?= Html::activeTextInput($model, 'no_terimabayarklaim', [
                                    'class' => 'form-control',
                                    'readonly' => true
                                ]) ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="form-group field-penerimaanpembayaranform-total_terimabayar required">
                            <label class="control-label col-md-4" for="penerimaanpembayaranform-total_terimabayar"><?= $model->attributeLabels()['total_terimabayar'] ?></label>
                            <div class="col-md-6">
                                <?= Html::activeTextInput($model, 'total_terimabayar', ['class' => 'form-control doco-number']) ?>
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
                                        'prompt' => Yii::t('fe', 'Pilih')
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
                                    'data' => $penjamin,
                                    'options' => [
                                        'disabled' => false,
                                        'class' => 'form-control select2 cara-pembayaran',
                                        'id' => 'penjamin_id'
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
                        <div class="form-group field-penerimaanpembayaranform-pegawaipenerima_id required">
                            <label class="control-label col-md-4" for="penerimaanpembayaranform-pegawaipenerima_id"><?= $model->attributeLabels()['pegawaipenerima_id'] ?></label>
                            <div class="col-md-6">
                                <?= Html::activeDropdownList($model, 'pegawaipenerima_id', $penerimaDana, ['class' => 'form-control select2']) ?>
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
                                <?= Html::activeTextArea($model, 'catatan', ['rows' => 4, 'class' => 'form-control']) ?>
                                <div class="help-block"></div>
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
                            <div class="col-md-12">
                                <div id="error_PenerimaanPembayaranFormdata_pengajuan"></div>
                                <table id="tbl-ajuan" class="table table-striped table-condensed table-hover" style="width:100%">
                                    <thead>
                                        <tr class=" bg-inverse">
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
</div>
<?php

$this->registerJs(
    '
    var _caraBayarBpjs = "'.DocoConstants::CARA_BAYAR_BPJS.'"
    var _table; 
    var ruangan = ' . Yii::$app->docoVars->workspace("ruangan_id") . ';
    var _statusAlokasi = ' . ($statusAlokasi ? 1 : 0) . ';' .
        $this->render('index.js'),
    View::POS_END,
    'js'
);

$this->registerJs('
    var _table; 
    var ruangan = ' . Yii::$app->docoVars->workspace("ruangan_id") . ';'
    . $this->render('/asset/global.js'), View::POS_END, 'jxxs');
?>