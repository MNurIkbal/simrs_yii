<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>
<style lang="">
    .datepicker>div{
        display:block;
    }
    .js .inputfile {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }
    #file-1 {
        display:none;
        margin: 10px;
    }
    .inputfile + label {
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
        padding: 7px 45px;
        max-width: 350px;
        /* 10px 20px */
    }

    .no-js .inputfile + label {
        display: none;
    }

    .inputfile:focus + label,
    .inputfile.has-focus + label {
        outline: 1px dotted #000;
        outline: -webkit-focus-ring-color auto 5px;
    }

    .inputfile + label * {
        /* pointer-events: none; */
        /* in case of FastClick lib use */
    }

    .inputfile + label svg {
        width: 1em;
        height: 1em;
        vertical-align: middle;
        fill: currentColor;
        margin-top: -0.25em;
        /* 4px */
        margin-right: 0.25em;
        /* 4px */
    }


    .inputfile-1 + label {
        color: #ffffff;
        background-color: #009ACD;

    }

    .inputfile-1:focus + label,
    .inputfile-1.has-focus + label,
    .inputfile-1 + label:hover {
        background-color: #00688B;
    }

    .lurus {
        float: left;
        margin-left: 5px;
    }

    .link-upload {
        font-size: 14px;
        color: black;
        margin: 5px;
        padding: 5px;
        font-weight: bold;
    }

    .content {
        min-height: 480px;
    }
    .table > tfoot > tr > th {
        border-top: none !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'back' => [
                            'attributes' => [
                                'href' => Yii::$app->request->referrer
                            ]
                        ],
                        'custom-save' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Terima'),
                            'icon' => 'fa fa-check',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'btn-simpan',
                                'disabled' => $isExpired || $isFullReceived
                            ],
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title">Info Data Penerimaan</h6>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Nomer Penerimaan") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= !empty($model->no_penerimaan) ? $model->no_penerimaan : '-' ?> </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Tanggal penerimaan") ?></b>
                                </label>
                                <div class="col-sm-5 no_po">
                                    <p>
                                        <b>:</b>&nbsp;
                                        <?= $isFullReceived ? date('d-M-Y',strtotime($model->tgl_perubahan)) : date('d-M-Y') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Nama supplier") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= !empty($model->supplier_nama) ? $model->supplier_nama : '-' ?> </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "No PO") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= !empty($model->no_transaksi) ? $model->no_transaksi : '-' ?> </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Diterima oleh") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <?php $id_penerima = !empty(Yii::$app->docoVars->user('id_pegawai')) ? Yii::$app->docoVars->user('id_pegawai') : 0 ?>
                                    <?php $nama_penerima = !empty(Yii::$app->docoVars->user('nama_pegawai')) ? Yii::$app->docoVars->user('nama_pegawai') : 0 ?>
                                    <?php $nama_peg_penerima = $isFullReceived && empty($model->peg_penerima_nama) ? $model->peg_mengubah : $model->peg_penerima_nama ?>
                                    <p><b>:</b>&nbsp;<?= !empty($nama_peg_penerima) ? $nama_peg_penerima : '-' ?> </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Status Penerimaan") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= !empty($model->stat_penerimaan) ? $model->stat_penerimaan : '-' ?> </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title">Data Penerimaan</h6>
                    </div>
                    <div class="panel-body">
                        <?php
                            $form = ActiveForm::begin([
                                'id' => 'po-form',
                                'enableAjaxValidation' => false,
                                'enableClientValidation' => false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'action' => "/gudang/informasi-po/save?id={$id}&type={$type}",
                                'formConfig' => [
                                    'labelSpan' => 3,
                                    'deviceSize' => ActiveForm::SIZE_SMALL
                                ],
                                'options' => [
                                    'skip-confirm' => "true"
                                ]
                        ]);
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                    $model->tgl_suratjalan = !empty($model->tgl_suratjalan)
                                        ? date('d-M-Y',strtotime($model->tgl_suratjalan)) : date('d-M-Y');
                                ?>
                                  <?= $form->field($model, 'tgl_suratjalan',[
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-6'
                                            ],
                                        ])->widget(DatePicker::classname(), [
                                            'name' => 'date_12',
                                            'value' => date('d-M-Y'),
                                            'language' => 'en',
                                            'readonly' => true,
                                            'pluginOptions' => [
                                                'autoclose' => true,
                                                'format' => 'dd-M-yyyy',
                                                'endDate' => "0d",
                                            ]
                                    ]); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'no_suratjalan',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->textInput([
                                    'class' => 'form-control',
                                ]); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'peg_mengetahui',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6 select2-md'
                                    ],
                                ])->dropDownList([],[
                                    'class' => 'form-control select2 search-pegawai',
                                ]); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'peg_menyetujui',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6 select2-md'
                                    ],
                                ])->dropDownList([],[
                                    'class' => 'form-control search-pegawai select2',
                                ]); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'no_faktur',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->textInput([
                                    'class' => 'form-control',
                                    'id' => 'no_faktur'
                                ]); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'no_faktur_sementara',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->textInput([
                                    'class' => 'form-control',
                                    'id' => 'no_faktur_sementara'
                                ]); ?>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                        <hr>
                        <div id="error_InfoPoFormdata_valid"></div>
                        <table id="table-po" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th>No</th>
                                    <th class="text-center"><?=\Yii::t("fe", "Nama Barang / Obat");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "Satuan");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "Qty PO");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "PO Balance");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "Harga (Rp.)");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "Discount (%)");?></th>
                                    <th class="text-center"><?=\Yii::t("fe", "Qty diterima");?> <sup style="color: red">*</sup></th>
                                    <th width="20%"><?=\Yii::t("fe", "Tanggal Kadaluarsa");?> <sup style="color: red">*</sup></th>
                                    <th><?=\Yii::t("fe", "No Batch");?> <sup style="color: red">*</sup></th>
                                    <th><?=\Yii::t("fe", "Keterangan");?></th>
                                    <th><?=\Yii::t("fe", "Aksi");?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $subTotalBeforeDisc = 0;
                                    $totalDiskon = !empty($model->total_discount) ? $model->total_discount : 0;
                                    $subTotal = !empty($model->sub_total) ? $model->sub_total : 0; 
                                    $totalPpn = !empty($model->ppn_nilai) ? $model->ppn_nilai : 0;
                                    $grandTotal = !empty($model->total) ? $model->total : 0;

                                    $listCache = [];
                                    $validPO = [];
                                    if (!empty($model->list_data)) :
                                        $no = 1;
                                        foreach ($model->list_data as $value) :
                                            $validPO[$value['podetail_id']] = [
                                                'max' => $value['po_balance']
                                            ];
                                            $disabled = $value['is_kadaluarsa'] ? false : true;
                                            $listCache[$value['podetail_id']]["row-0"] = [
                                                'podetail_id' => $value['podetail_id'],
                                                'obat_barang_id' => $value['obat_barang_id'],
                                                'qty_input' => $value['qty_input'],
                                                'po_balance' => $value['po_balance'],
                                                'qty_penerimaan' => $value['qty_penerimaan'],
                                                'satuan' => $value['satuan'],
                                                'qty_diterima' => 0,
                                                'harga' => $value['harga'],
                                                'discount' => $value['discount'],
                                                'discount_rp' => $value['discount_rp'],
                                                'jumlah' => $value['jumlah'],
                                                'transaksi_id' => $value['transaksi_id'],
                                                'is_kadaluarsa' => $value['is_kadaluarsa'],
                                                'id_detail' => $value['id_detail'],
                                                'konversi_id' => $value['s_konversiobt_id'],
                                                'no_batch' => null,
                                                'keterangan' => null,
                                                'ppn_nilai' => $value['ppn_nilai'],
                                                'ppn_persen' => $value['ppn_persen'],
                                                'total_harga' => ($value['harga'] * $value['qty_input']),
                                            ];
                                ?>
                                    <tr data-id="<?= $value['podetail_id'] ?>"
                                        data-key="0"
                                        data-last="0"
                                        >
                                        <td><?= $no ?></td>
                                        <td><?= $value['obat_barang_nama'] ?></td>
                                        <td><?= $value['satuan_besar'] ?></td>
                                        <td class="text-right"><?= DocoHelpers::formatNumber($value['qty_input']) ?></td>
                                        <td class="text-right"><?= DocoHelpers::formatNumber($value['po_balance']) ?></td><td class="text-right"><?= DocoHelpers::formatNumber($value['harga']) ?></td>
                                        <td class="text-right"><?= DocoHelpers::formatNumber($value['discount']) ?></td>
                                        <td class="text-right">
                                        <?=
                                            Html::textInput('qty_penerimaan',0,[
                                                'class' => 'form-control doco-number text-right qty_penerimaan',
                                                'maxlength' => 10
                                            ])
                                        ?>
                                        <div id="error_InfoPoForm<?= $value['podetail_id'] ?>qty-row-0"
                                            class="error-parent"></div>
                                        </td>
                                        <td>
                                            <?=
                                                DatePicker::widget([
                                                    'name' => 'dp_2',
                                                    'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                                                    'readonly' => true,
                                                    'language' => 'en',
                                                    'disabled' => $disabled,
                                                    'options' => [
                                                        'class' => 'form-control date-kartik',
                                                    ],
                                                    'pluginOptions' => [
                                                        'autoclose'=>true,
                                                        'format' => 'dd-M-yyyy',
                                                        'startDate' => "0d",
                                                    ]
                                                ]);
                                            ?>
                                            <div id="error_InfoPoForm<?= $value['podetail_id'] ?>date-row-0"
                                                class="error-parent"></div>
                                        </td>
                                        <td>
                                            <?=
                                                Html::textInput('no_batch',null,[
                                                    'class' => 'form-control no_batch'
                                                ])
                                            ?>
                                            <div id="error_InfoPoForm<?= $value['podetail_id'] ?>no_batch-row-0"
                                                class="error-parent"></div>
                                            </td>
                                        </td>
                                        <td>
                                          <?=
                                                Html::textInput('keterangan',null,[
                                                    'class' => 'form-control keterangan'
                                                ])
                                            ?>
                                        </td>
                                        <td>
                                            <button type="button" class="addrow btn btn-info btn-xs btn-custom"
                                            style="padding-left:8px !important;">
                                                <span class="fa fa-plus"></span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php
                                            $harga = !empty($value['harga']) ? $value['harga'] : 0;
                                            $qtyInput = !empty($value['qty_input']) ? $value['qty_input'] : 0;

                                            $subTotalBeforeDisc += ($harga * $qtyInput);

                                            $no++;
                                        endforeach;
                                    else :
                                ?>
                                    <tr>
                                        <td class="text-center" colspan="10"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                    </tr>
                                <?php
                                    endif;
                                ?>
                            </tbody>
                        </table>
                        <hr>
                        <div style="display: flex; gap: 20px; margin-left: 20px;">
                            <table style="width:17%; margin-left: 20px;">
                                <tr>
                                    <td><b><?= Yii::t("fe", "Sub Total Harga PO (Rp.)") ?></b></td>
                                    <td><b>:</b></td>
                                    <td class="text-right"><span class="sub_total_before_disc"><?= DocoHelpers::formatNumber($subTotalBeforeDisc, 0) ?></span></td>
                                </tr>
                                <tr>
                                    <td> <b><?= Yii::t("fe", "Total Diskon PO (Rp.)") ?></b></td>
                                    <td><b>:</b></td>
                                    <td class="text-right"><span class="total_diskon"><?= DocoHelpers::formatNumber($totalDiskon, 0) ?></td>
                                </tr>
                                <tr>
                                    <td> <b><?= Yii::t("fe", "Sub Total (Rp.)") ?></b></td>
                                    <td><b>:</b></td>
                                    <td class="text-right"><span class="sub_total"><?= DocoHelpers::formatNumber($subTotal, 0) ?></td>
                                </tr>
                                <tr>
                                    <td> <b><?= Yii::t("fe", "PPN (Rp.)") ?></b></td>
                                    <td><b>:</b></td>
                                    <td class="text-right"><span class="ppn"><?= DocoHelpers::formatNumber($totalPpn, 0) ?></td>
                                </tr>
                                <tr>
                                    <td> <b><?= Yii::t("fe", "Total (Rp.)") ?></b></td>
                                    <td><b>:</b></td>
                                    <td class="text-right"><span class="grand_total"><?= DocoHelpers::formatNumber($grandTotal, 0) ?></td>
                                </tr>
                            </table>
                            <table style="width:17%; margin-left: auto; margin-right: 20px;">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right"><b>Harga Penerimaan</b></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><span class="sub_total_before_disc_partial">0</span></td>
                                    <td style="padding-left:10px;">:</td>
                                    <td class="text-right"><?= Yii::t("fe", "Sub Total Harga (Rp.)") ?></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><span class="total_diskon_partial">0</td>
                                    <td style="padding-left:10px;">:</td>
                                    <td class="text-right"><?= Yii::t("fe", "Total Diskon (Rp.)") ?></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><span class="sub_total_partial">0</td>
                                    <td style="padding-left:10px;">:</td>
                                    <td class="text-right"><?= Yii::t("fe", "Sub Total (Rp.)") ?></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><span class="ppn_partial">0</td>
                                    <td style="padding-left:10px;">:</td>
                                    <td class="text-right"><?= Yii::t("fe", "PPN (Rp.)") ?></td>
                                </tr>
                                <tr>
                                    <td class="text-right"><span class="grand_total_partial">0</td>
                                    <td style="padding-left:10px;">:</td>
                                    <td class="text-right"><?= Yii::t("fe", "Total (Rp.)") ?></td>
                                </tr>
                            </table>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-5">
                                    <div class="form-group field-uploadhasilform-upload">
                                        <label class="control-label text-left control-label col-sm-4" for="uploadhasilform-upload"><b><?= Yii::t('fe', 'Upload berkas') ?></b></label>
                                        <div class="col-md-8">
                                            <?=
                                                Html::button('<b><i class="fa fa-plus"></i></b>' . \Yii::t('fe', 'Tambah berkas'), [
                                                    'class' => 'btn btn-info btn-labeled btn-xs',
                                                    'id' => 'add-upload',
                                                    'disabled' => false
                                                ]);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-12" >
                                <i class="text-danger">*</i> <b><?= Yii::t('fe', 'maksimal 100 mb'); ?></b>
                            </div>
                            <div class="col-md-12 upload-section" id="upload-section">
                                <div class="lurus">
                                    <button type="button" class="btn btn-sm btn-block btn-danger delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                                <div class="lurus ">
                                    <input type="file" name="UploadHasilForm[upload_file][]" id="file-1" class="form-control inputfile inputfile-1" multiple=true>
                                    <label for="file-1">
                                        <i class="fa fa-upload"></i>
                                        <span id="label-file">Pilih Berkas</span>
                                    </label>
                                </div>
                                <div class="lurus">
                                    <div class="col-md-12">
                                        <input class="form-control" id="catatan-1" name="UploadHasilForm[catatan][]" type="text"
                                        style="width:400px;" placeholder="Masukan catatan">
                                    </div>
                                </div>
                            </div>
                            <div id="list-upload"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label class="control-label text-left control-label col-sm-4">
                                            <b><?= Yii::t('fe', 'Catatan') ?></b>
                                        </label>
                                        <div class="col-md-8">
                                            <?=
                                                Html::textarea('catatan',null, [
                                                    'class' => 'form-control',
                                                    'cols' => 15
                                                ]);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$listCache = json_encode($listCache);
$validPO = json_encode($validPO);
$suppId = !empty($model->supplier_id) ? $model->supplier_id : 0;
$this->registerJs('
    var _cache = '. $listCache .';
    var _validPo = '. $validPO .';
    var _idPenerima = '. $id_penerima .';
    var _supplierId = '. $suppId .';
    var list_upload = $(\'.upload-section\').get();
    var increment = list_upload.length;
    var list = [1];

    if (parseInt(increment) == 0) {
        $(\'.info-upload\').hide();
    }
    $(document).on("click",".addrow", function(e) {
        e.preventDefault();
        var _parent = $(this).closest("tr");
        var _id = parseInt(_parent.attr("data-id"));
        var _key = parseInt(_parent.attr("data-key"));
        var _last = parseInt(_parent.attr("data-last"));
        var _clone = _parent.clone();
        var _tr = $("<tr></tr>");
        _length = 0;

        if (typeof  _cache[_id]["row-"+_key] != "undefined") {
            _last++;
            var _dP = _cache[_id]["row-"+_key];
            var _data = {
                podetail_id : _dP.podetail_id,
                obat_barang_id : _dP.obat_barang_id,
                qty_input : _dP.qty_input,
                id_detail : _dP.id_detail,
                konversi_id : _dP.konversi_id,
                po_balance : _dP.po_balance,
                qty_penerimaan : _dP.qty_penerimaan,
                satuan : _dP.satuan,
                qty_diterima : 0,
                harga : _dP.harga,
                discount : _dP.discount,
                discount_rp : _dP.discount_rp,
                jumlah : _dP.jumlah,
                transaksi_id : _dP.transaksi_id,
                is_kadaluarsa : _dP.is_kadaluarsa,
            };

            _cache[_id]["row-"+_last] = _data;
            var _length = Object.keys(_cache[_id]).length;

        }

        _clone.filter(function () {
            var _td = $(this).find("td");
            $.each(_td, function (key, val) {
                var _div = $("<div></div>");
                if (key < 5) {
                    _td.eq(key).remove();
                }

                if (key == 5) {
                    _td.eq(key).find("div.error-parent").remove();
                    _td.eq(key).append(_div.attr("id","error_InfoPoForm" + _id + "qty-row-" + _last));
                }

                if (key == 6) {
                    _td.eq(key).find("div.error-parent").remove();
                    _td.eq(key).append(_div.attr("id","error_InfoPoForm" + _id + "date-row-" + _last));
                }
            })
            return _td;
        });

        _clone.attr("data-key",_last);
        var _button = "<button type=\"button\" class=\"del-row btn btn-danger btn-xs btn-custom\" style=\"padding-left:8px !important;\"><span class=\"fa fa-trash\"></span></button>";

        _clone.find("td:last-child").html(_button);
        _clone.find("input").val(null)
        _parent.attr("data-last",_last);
        _parent.after(_clone);
        _parent.filter(function () {
            var _td = $(this).find("td");
            $.each(_td, function (key, val) {
                if (key < 5) {
                    _td.eq(key).attr("rowspan",_length);
                }
            })
            return _td;
        });
        $(".doco-number").trigger("change");
        $(".date-kartik").kvDatepicker({
            autoclose : true,
            format : "dd-M-yyyy",
            lang : "en",
            startDate : "0d"
        });
    });

    $(document).on("click",".del-row", function (e) {
        e.preventDefault();
        var _parent = $(this).closest("tr");
        var _id = parseInt(_parent.attr("data-id"));
        var _key = parseInt(_parent.attr("data-key"));
        var _last = parseInt(_parent.attr("data-last"));
        var _grandPa = $("tr[data-id="+_id+"][data-key=0]");
        _length = 0;
        if (typeof  _cache[_id]["row-"+_key] != "undefined") {
            delete _cache[_id]["row-"+_key];
            var _length = Object.keys(_cache[_id]).length;

        }

        _grandPa.filter(function () {
            var _td = $(this).find("td");
            $.each(_td, function (key, val) {
                if (key < 5) {
                    _td.eq(key).attr("rowspan",_length);
                }
            })
            return _td;
        });
        _parent.remove();
    });

    $(document).on("keyup change", ".qty_penerimaan", function (e) {
        e.preventDefault();
        var _parent = $(this).closest("tr");
        var _id = parseInt(_parent.attr("data-id"));
        var _key = parseInt(_parent.attr("data-key"));
        var _last = parseInt(_parent.attr("data-last"));
        var _default = 0;
        if (typeof  _cache[_id]["row-"+_key] != "undefined") {
             _cache[_id]["row-"+_key].qty_diterima = 0;
            if (typeof _validPo[_id] != "undefined") {
                var _max = _validPo[_id].max;
                var _value = parseInt(docoHelper.convertToAngka($(this).val()));
                var _usage = 0;
                $.each(_cache[_id], function (key, val) {
                    if (typeof val.qty_diterima != "undefined") {
                        _usage += parseInt(val.qty_diterima);
                    }
                });
                _usage += _value;
                if (_max >= _usage) {
                    _cache[_id]["row-"+_key].qty_diterima = parseInt(docoHelper.convertToAngka($(this).val()));
                } else {
                    _usage -= _value;
                    _cache[_id]["row-"+_key].qty_diterima = _max - parseInt(_usage);
                    $(this).val(docoHelper.convertToRupiah(_max - _usage));
                    return false;
                }
            }

            if (_cache[_id]["row-"+_key].discount > 0) {
                var _disc_unit = _cache[_id]["row-"+_key].discount
                var _price_unit = _cache[_id]["row-"+_key].harga
                var _qty_unit = _cache[_id]["row-"+_key].qty_diterima
                var _disc_value = (_qty_unit * _price_unit) * (_disc_unit/100)

                _cache[_id]["row-"+_key].discount_rp = _disc_value
                // console.log(_qty_unit, _price_unit, _disc_unit, _disc_value)
            }
            $("#error_InfoPoForm" + _id + "qty-row-" + _key).html("");

        }
    });

    $(document).on("change",".date-kartik", function (e) {
        e.preventDefault();
        var _parent = $(this).closest("tr");
        var _id = parseInt(_parent.attr("data-id"));
        var _key = parseInt(_parent.attr("data-key"));
        var _last = parseInt(_parent.attr("data-last"));
        if (typeof  _cache[_id]["row-"+_key] != "undefined") {
            var _data = _cache[_id]["row-"+_key];
            _data.tgl_kadaluarsa = $(this).val();
            var _dataEx = $.extend({},_data, _cache[_id]["row-"+_key]);
            _cache[_id]["row-"+_key] = _dataEx;
            $("#error_InfoPoForm" + _id + "date-row-" + _key).html("");            
        }
    });

    $(document).on("keyup change", ".no_batch", function (e) {
        e.preventDefault();
        var _parent = $(this).closest("tr");
        var _id = parseInt(_parent.attr("data-id"));
        var _key = parseInt(_parent.attr("data-key"));
        var _last = parseInt(_parent.attr("data-last"));
        if (typeof  _cache[_id]["row-"+_key] != "undefined") {
            var _data = _cache[_id]["row-"+_key];
            _data.no_batch = $(this).val();
            var _dataEx = $.extend({},_data, _cache[_id]["row-"+_key]);
            _cache[_id]["row-"+_key] = _dataEx;
            $("#error_InfoPoForm" + _id + "no_batch-row-" + _key).html("");
        }
        
    });

    $(document).on("keyup change", ".keterangan", function (e) {
        e.preventDefault();
        var _parent = $(this).closest("tr");
        var _id = parseInt(_parent.attr("data-id"));
        var _key = parseInt(_parent.attr("data-key"));
        var _last = parseInt(_parent.attr("data-last"));
        if (typeof  _cache[_id]["row-"+_key] != "undefined") {
            var _data = _cache[_id]["row-"+_key];
            _data.keterangan = $(this).val();
            var _dataEx = $.extend({},_data, _cache[_id]["row-"+_key]);
            _cache[_id]["row-"+_key] = _dataEx;
        }
    });

    $(document).on("click", "#btn-simpan", function (e) {
        e.preventDefault();
        var dataPost = $("#po-form").serializeArray();
        var no_faktur = $("#no_faktur").val();
        var no_faktur_sementara = $("#no_faktur_sementara").val();
        
        if(no_faktur_sementara == "" && no_faktur == "") {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("No Faktur / No Faktur Sementara harus di isi"));
            return false;
        }

        dataPost.push({
            name : "data_detail",
            value : JSON.stringify(_cache)
        });

        dataPost.push({
            name : "valid_data",
            value : JSON.stringify(_validPo)
        });

        dataPost.push({
            name : "diterima_oleh",
            value : _idPenerima
        });

        dataPost.push({
            name : "supplier_id",
            value : _supplierId
        });
        var _data = new FormData();
        for (let i = 1; i < list.length+1; i++) {
            let getFile = $("#file-" + i)[0];
            if (typeof getFile !== \'undefined\') {
                _data.append("UploadHasilForm[upload_file][]", $("#file-" + i)[0].files[0]);
                _data.append("UploadHasilForm[catatan][]", $("#catatan-" + i).val());
            }
        }

        $.each(dataPost, function(key, value) {
            _data.append(value.name, value.value);
        });
        _data.append("catatan",$("textarea[name=catatan]").val());
        $().docoForm("click",{
            url : $("#po-form").attr("action"),
            data : _data,
            dataType: false,
            cache: false,
            contentType: false,
            processData: false,
            isUpload: true,
            success : function (data) {
                var no_penerimaan = data.response.no_penerimaan;
                var id_parent = data.response.id_parent;
                var _type = data.response.type;
                (new PNotify({
                    title: "Proses Berhasil !",
                    text: "Data Penerimaan dengan Nomor <strong>" + no_penerimaan + "</strong> berhasil disimpan, apakah Anda ingin melakukan verifikasi penerimaan?",
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: "success",
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    hide: false,
                    confirm: {
                        confirm: true,
                        buttons: [
                            {
                                text: \'Ya\',
                                addClass: \'btn btn-xs btn-success\',
                            },
                            {
                                text: \'Tidak\',
                                addClass: \'btn btn-xs btn-danger\',
                            }
                        ]
                    },
                    history: {
                        history: false
                    }
                })).get().on(\'pnotify.confirm\', function() {
                    if (_type == "obat") {
                        window.location = "/gudang/informasi-penerimaan-obat";
                    } else {
                        window.location = "/gudang/informasi-penerimaan-po-barang";
                    }
                }).on(\'pnotify.cancel\', function() {
                    if (_type == "obat") {
                        window.location = "/gudang/informasi-po";
                    } else {
                        window.location = "/gudang/informasi-po/barang";
                    }
                });
            },
            error : function (data) {

            }
        });
    });

    $(document).ready(function () {
        $(".date-kartik").kvDatepicker({
            autoclose : true,
            format : "dd-M-yyyy",
            language : "id"
        });

        $(".search-pegawai").select2({
            placeholder: "Pilih Pegawai",
            minimumInputLength: 3,
            ajax : {
                url: baseUrl+"gudang/informasi-po/search-pegawai",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params;
                },
                processResults: function (data) {
                    $.each(data.result, function (key,val) {
                    });
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; },
            },
        });
    });


    $(document).ready(function () {
        _checkButton();
        _hitung_partial();
        // _hitung();
    });

    var _checkButton = function() {
        var _length = $(".inputfile").length;
        var _no = 0;
        $.each($(".inputfile"), function(){
            var _value = $(this).val();
            if (_value) {
                _no++;
            }
        });

        if (_length == _no) {
            $("#add-upload").prop("disabled",false);
        } else {
            $("#add-upload").prop("disabled",true);
        }
    }

    $(document).on("click", ".delete", function() {
        let button = this;
        let pkId = $(this).attr("data-id");
        let parent = $(this).attr("data-parent");
        if (pkId) {
            $(this).docoForm("delete", {
                url: "/radiologi/input-hasil/delete-upload?id="+pkId + "&parent="+ parent,
                success: function (params) {
                    $(button).parent().parent().remove();
                }
            });
        } else {
            $(button).parent().parent().remove();
            _checkButton();
        }
    })

    function uploadForm(increment) {
        let uploadDiv = "";
        uploadDiv += "<div class=\"col-md-12 upload-section\" id=\"upload-section\">";
            uploadDiv += "<div class=\"lurus\">";
                uploadDiv += "<button type=\"button\" class=\"btn btn-sm btn-block btn-danger delete\"><i class=\"fa fa-trash\"></i></button>";
            uploadDiv += "</div>";
            uploadDiv += "<div class=\"lurus\">";
                uploadDiv += "<input type=\"file\" name=\"UploadHasilForm[upload_file][]\" id=\"file-" + increment +"\" class=\"form-control inputfile inputfile-1\" multiple=\"true\">";
                uploadDiv += "<label for=\"file-"+increment+"\" class=\"label-upload\">";
                    uploadDiv += "<i class=\"fa fa-upload\"></i>";
                    uploadDiv += "<span id=\"label-file\"> Pilih Berkas</span>";
                uploadDiv += "</label>";
            uploadDiv += "</div>";
            uploadDiv += "<div class=\"lurus\">";
                uploadDiv += "<div class=\"col-md-12\">";
                    uploadDiv += "<input class=\"form-control\" name=\"UploadHasilForm[catatan][]\" type=\"text\" style=\"width:400px;\" placeholder=\"Masukan catatan\" id=\"catatan-" + increment +"\">";
                uploadDiv += "</div>";
            uploadDiv += "</div>";
        uploadDiv += "</div>";

        $("#list-upload").append(uploadDiv);
    }

    $(\'#add-upload\').on(\'click\', function() {
        increment++;
        uploadForm(increment)
        list.push(increment)
        $(\'.info-upload\').show();
        let list_upload = $(\'.upload-section\').get();
        for (var i = 1; i < list.length+1; i++) {
            $("#file-"+i).hide();
            var inputs = document.querySelectorAll(\'.inputfile\');
            Array.prototype.forEach.call(inputs, function (input) {
                var label = input.nextElementSibling,
                    labelVal = label.innerHTML;

                input.addEventListener(\'change\', function (e) {
                    var fileName = \'\';
                    if (this.files && this.files.length > 1) {
                        fileName = (this.getAttribute(\'data-multiple-caption\') || \'\').replace("{count}", this.files.length);
                    } else {
                        fileName = e.target.value.split("\\\").pop();
                    }

                    if (fileName) {
                        $(\'#add-upload\').prop("disabled",false);
                        label.querySelector(\'span\').innerHTML = fileName;
                    }
                });

                input.addEventListener(\'focus\', function () {
                    input.classList.add(\'has-focus\');
                });
                input.addEventListener(\'blur\', function () {
                    input.classList.remove(\'has-focus\');
                });
            });
        }
        $(this).prop("disabled",true);
    });

    (function (document, window, index) {
        var inputs = document.querySelectorAll(\'.inputfile\');
        Array.prototype.forEach.call(inputs, function (input) {
            var label = input.nextElementSibling,
                labelVal = label.innerHTML;

                input.addEventListener(\'change\', function (e) {
                var fileName = \'\';
                if (this.files && this.files.length > 1) {
                    fileName = (this.getAttribute(\'data-multiple-caption\') || \'\').replace(\'{count}\', this.files.length);
                } else {
                    fileName = e.target.value.split("\\\").pop();
                }

                if (fileName)
                    $(\'#add-upload\').prop("disabled",false);
                    label.querySelector(\'span\').innerHTML = fileName;
            });

            input.addEventListener(\'focus\', function () {
                input.classList.add(\'has-focus\');
            });
            input.addEventListener(\'blur\', function () {
                input.classList.remove(\'has-focus\');
            });
        });
    }(document, window, 0));

    var _hitung = function () {
        var subTotal = 0;
        var subTotalBeforeDisc = 0;
        var totalDiskon = 0;
        var totalPpn = 0;
        var grandTotal = 0;
        $.each(_cache, function(key,values){
            $.each(values, function(k,val) {
                totalPpn  = isNaN(val.ppn_nilai) ? 0 : val.ppn_nilai;
                var totHar = isNaN(val.total_harga) ? 0 : val.total_harga;
                var totDis = isNaN(val.discount_rp) ? 0 : val.discount_rp;
                subTotalBeforeDisc += totHar;
                totalDiskon += totDis;
            })
        });
        
        subTotal = subTotalBeforeDisc - totalDiskon

        // ambil dari data
        // totalPpn = totalPpn;
    
        grandTotal = subTotal + totalPpn;
    
        $(".sub_total_before_disc").html(docoHelper.convertToRupiah(subTotalBeforeDisc));
        $(".sub_total").html(docoHelper.convertToRupiah(subTotal));
        $(".total_diskon").html(docoHelper.convertToRupiah(totalDiskon));
        $(".ppn").html(docoHelper.convertToRupiah(totalPpn));
        $(".grand_total").html(docoHelper.convertToRupiah(grandTotal));
    }
    
    $(document).on("keyup change",".qty_penerimaan", function (e) {
        _hitung_partial();
    });

    var _hitung_partial = function () {
        var finalTotalDiskon = [];

        var subTotal            = 0;
        var totalDisc           = 0;
        var subWithDisc    = 0;
        var ppnAmount      = 0;
        var grandTotal        = 0;

        $.each(_cache, function (key, values) {
            $.each(values, function (k, val) {
                // declare base
                var price = isNaN(val.harga) ? 0 : val.harga;
                var qty_val = isNaN(val.qty_diterima) ? 0 : val.qty_diterima;
                var disc_percentage = val.discount !== undefined ? val.discount : (values["row-0"] ? values["row-0"].discount : 0);
                var ppn_percentage = val.ppn_persen !== undefined ? val.ppn_persen : (values["row-0"] ? values["row-0"].ppn_persen : 0);

                // Calculation
                var _subtotal               = price * qty_val;
                var _discount_amount        = (disc_percentage / 100) * _subtotal;
                var _subtotalWithDiscount   = _subtotal - _discount_amount;
                var _tax_amount             = (ppn_percentage / 100) * _subtotalWithDiscount;
                var _total                  = _subtotalWithDiscount + _tax_amount;

                // Sum process
                subTotal        += _subtotal;
                totalDisc       += _discount_amount;
                subWithDisc     += _subtotalWithDiscount;
                ppnAmount       += _tax_amount;
                grandTotal      += _total;
            })
        });

        $(".sub_total_before_disc_partial").html(docoHelper.convertToRupiah(subTotal));
        $(".total_diskon_partial").html(docoHelper.convertToRupiah(totalDisc));
        $(".sub_total_partial").html(docoHelper.convertToRupiah(subWithDisc));
        $(".ppn_partial").html(docoHelper.convertToRupiah(ppnAmount));
        $(".grand_total_partial").html(docoHelper.convertToRupiah(grandTotal));
    }
', View::POS_END);
?>
