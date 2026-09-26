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
                            'title' => Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-floppy-o',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'btn-edit',
                            ],
                        ],
                        'custom-penerimaan' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', "Batal Penerimaan"),
                            'icon' => "fa fa-ban",
                            'attributes' => [
                                'data-target' => $module.'batal-penerimaan?id='.DocoHelpers::encrypt($id),
                                'data-options' => 'click',
                                'id' => "btn-batal-penerimaan",
                            ]
                        ],
                        'custom-print-kwitansi' => [
                            'type'=>'button',
                            'title' => Yii::t('fe', 'Print'),
                            'icon' => 'fa fa-print',
                            'attributes' => [
                                'id' => 'print-detail',
                                'data-target' => '/gudang/informasi-penerimaan-obat/detail-export-pdf?id='.DocoHelpers::encrypt($id),
                                'data-options' => 'link',
                                'target' => "_blank"
                            ],
                        ],
                        'print-resep'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Cetak GRN'),
                            'icon' => 'fa fa-print',
                            'attributes' => [
                                'id'=>'btn-print-grn',
                                'class'=>'print-grn',
                                'target'=>'_blank',
                                'data-options'=>'link',
                                'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-grn?id='.DocoHelpers::encrypt($id).'&type=obat'
                            ]
                        ],
                        'verifikasi' => [
                            'title' => Yii::t('fe', "Verifikasi Transaksi"),
                            'icon' => "fa fa-check",
                            'attributes' => [
                                'data-target' => $module.'verifikasi-penerimaan?id='.DocoHelpers::encrypt($id),
                                'data-options' => "click",
                                'id' => "btn-verifikasi",
                            ]
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
                                <label class="control-label col-sm-5"><b><?= Yii::t("fe", "Status Penerimaan") ?></b></label>
                                <div class="col-sm-5">
                                    <p><b>: </b> <b><?= isset($header_static["verifikasi_nama"]) ? ucwords($header_static["verifikasi_nama"]) : "" ?></b></p>
                                </div>
                            </div>
                        </div>
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
                                        <?= !empty($model->tgl_penerimaan)
                                                ? date('d-M-Y',strtotime($model->tgl_penerimaan)) : date('d-M-Y') ?>
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
                                    <p><b>:</b>&nbsp;<?= !empty($header_static["no_poobat"]) ? $header_static["no_poobat"] : '-' ?> </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Diterima oleh") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <?php $diorder_id = !is_null($header_static["diterima_oleh"]) ? $header_static["diterima_oleh"] : Yii::$app->docoVars->user('id_pegawai') ?>

                                    <p><b>:</b>&nbsp;<?= !empty($header_static["menerima"]) ? $header_static["menerima"] : Yii::$app->docoVars->user('nama_pegawai') ?> </p>
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
                                'action' => "/gudang/informasi-penerimaan-obat/edit-penerimaan?id={$id}",
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
                                    'disabled' => $verified
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
                                ])->dropDownList($list_pegawai["mengetahui"] ,[
                                    'class' => 'form-control select2 search-pegawai',
                                    'disabled' => $verified
                                ]); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'peg_menyetujui',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6 select2-md'
                                    ],
                                ])->dropDownList($list_pegawai["menyetujui"],[
                                    'class' => 'form-control select2 search-pegawai',
                                    'disabled' => $verified
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
                                    'id' => 'no_faktur_sementara',
                                    'disabled' => $verified
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
                                    <th class="text-center"><?=\Yii::t("fe", "Qty diterima");?><sup style="color: red"> *</sup></th>
                                    <th width="20%"><?=\Yii::t("fe", "Tanggal Kadaluarsa");?><sup style="color: red"> *</sup></th>
                                    <th><?=\Yii::t("fe", "No Batch");?></th>
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
                                    $tmp__ = [];
                                    if (!empty($model->list_data)) :
                                        $no = 1;
                                        foreach ($detail_static as $obatSatuan => $list_data) :
                                            $qty[$obatSatuan] = $poBalance[$obatSatuan] = 0;
                                            foreach ($list_data as $key => $value) :
                                                $qty[$obatSatuan] += $value['qty_po'];
                                                $poBalance[$obatSatuan] += $value['po_balance'];
                                            endforeach;
                                        endforeach;

                                        foreach ($detail_static as $obatSatuan => $list_data) :
                                            foreach ($list_data as $key => $value) :
                                            $validPO[$value['obatalkes_id']] = [
                                                'max' => $value['po_balance']
                                            ];
                                            $disabled = false;
                                            $count = count($list_data);

                                            $listCache[$value['obatalkes_id']]["row-0"] = [
                                                "obatalkes_id" => $value["obatalkes_id"],
                                                "obatalkes_nama" => $value["obatalkes_nama"],
                                                "qty_po" => $qty[$obatSatuan],
                                                "po_balance" => $poBalance[$obatSatuan],
                                                "qty_input" => $value["qty_input"],
                                                "qty_diterima" => $value["qty_diterima"],
                                                "satuan_besar" => $value["satuan_besar"],
                                                "tgl_kadaluarsa" => $value["tgl_kadaluarsa"],
                                                "no_batch" => $value["no_batch"],
                                                "keterangan" => $value["keterangan"],
                                                'ppn_nilai' => $value['ppn_nilai'],
                                                'ppn_persen' =>  $value['ppn_persen'],
                                            ];

                                            $tmp__[$value["obatalkes_id"]]["row-".$key] = [
                                                "obatalkes_id" => $value["obatalkes_id"],
                                                "qty_po" => $qty[$obatSatuan],
                                                "po_balance" => $poBalance[$obatSatuan],
                                                "qty_diterima" => $value["qty_diterima"],
                                                "no_batch" => $value["no_batch"],
                                                "keterangan" => $value["keterangan"],
                                                "tgl_kadaluarsa" => $value["tgl_kadaluarsa"],
                                                "s_konversiobt_id" => $value["s_konversiobt_id"],
                                                "harga" => $value["harga"],
                                                "discount" => $value["discount"],
                                                "discount_rp" => $value["discount_rp"],
                                                "jumlah" => $value["jumlah"],
                                                "validasipoobatdetail_id" => $value["validasipoobatdetail_id"],
                                                "is_kadaluarsa" => true,
                                                'ppn_nilai' => $value['ppn_nilai'],
                                                'ppn_persen' =>  $value['ppn_persen'],
                                            ];
                                ?>

                                    <tr data-id="<?= $value['obatalkes_id'] ?>"
                                        data-key="<?= $key ?>"
                                        data-last="<?= $count ?>"
                                        >
                                        <?php if ($key == 0): ?>
                                            <td rowspan="<?= $count ?>">
                                                <?= $no ?>
                                            </td>
                                            <td rowspan="<?= $count ?>">
                                                <?= $value['obatalkes_nama'] ?>
                                            </td>
                                            <td rowspan="<?= $count ?>"><?= $value["satuan_besar"] ?></td>
                                            <td class="text-right" rowspan="<?= $count ?>"><?= $qty[$obatSatuan] ?></td>
                                            <td class="text-right" rowspan="<?= $count ?>"><?= $poBalance[$obatSatuan] ?></td>
                                            <td class="text-right" rowspan="<?= $count ?>"><?= DocoHelpers::formatNumber($value['harga']) ?></td>
                                        <?php endif ?>
                                        <td class="text-right">
                                        <?=
                                            Html::textInput('qty_penerimaan',$value["qty_diterima"],[
                                                'class' => 'form-control doco-number text-right qty_penerimaan',
                                                'maxlength' => 10,
                                                'disabled' => $verified
                                            ])
                                        ?>
                                        <div id="error_InfoPoForm<?= $value['obatalkes_id'] ?>qty-row-<?= $key ?>"
                                            class="error-parent"></div>
                                        </td>
                                        <td>
                                            <?=
                                                DatePicker::widget([
                                                    'name' => 'dp_2',
                                                    'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                                                    'readonly' => true,
                                                    "value" => date("d-M-Y", strtotime($value["tgl_kadaluarsa"])),
                                                    'language' => 'en',
                                                    // 'disabled' => $disabled,
                                                    'disabled' => $verified,
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
                                            <div id="error_InfoPoForm<?= $value['obatalkes_id'] ?>date-row-<?= $key ?>"
                                                class="error-parent"></div>
                                        </td>
                                        <td>
                                            <?=
                                                Html::textInput('no_batch',$value["no_batch"],[
                                                    'class' => 'form-control no_batch',
                                                    "readonly" => "true"
                                                ])
                                            ?>
                                        </td>
                                        <td>
                                          <?=
                                                Html::textInput('keterangan',$value["keterangan"],[
                                                    'class' => 'form-control keterangan',
                                                    'disabled' => $verified
                                                ])
                                            ?>
                                        </td>
                                        <?php if ($key == 0): ?>
                                            <td>
                                                <button type="button" class="addrow adddet btn btn-info btn-xs btn-custom"
                                                style="padding-left:8px !important;">
                                                    <span class="fa fa-plus"></span>
                                                </button>
                                            </td>
                                        <?php else: ?>
                                            <td>
                                                <button type="button" class="del-row btn btn-danger btn-xs btn-custom" style="padding-left:8px !important;">
                                                    <span class="fa fa-trash"></span>
                                                </button>
                                            </td>
                                        <?php endif ?>
                                    </tr>
                                <?php
                                            $harga = !empty($value['harga']) ? $value['harga'] : 0;
                                            $qtyInput = !empty($value['qty_diterima']) ? $value['qty_diterima'] : 0;
        
                                            $subTotalBeforeDisc += ($harga * $qtyInput);
                                            endforeach;
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
                        <table style="width:17%; margin-left: 20px;">
                            <tr>
                                <td><b><?= Yii::t("fe", "Sub Total Harga PO (Rp.)") ?></b></td>
                                <td><b>:</b></td>
                                <td class="text-right"><span class="sub_total_before_disc"></span></td>
                            </tr>
                            <tr>
                                <td> <b><?= Yii::t("fe", "Total Diskon PO (Rp.)") ?></b></td>
                                <td><b>:</b></td>
                                <td class="text-right"><span class="total_diskon"></td>
                            </tr>
                            <tr>
                                <td> <b><?= Yii::t("fe", "Sub Total (Rp.)") ?></b></td>
                                <td><b>:</b></td>
                                <td class="text-right"><span class="sub_total"></td>
                            </tr>
                            <tr>
                                <td> <b><?= Yii::t("fe", "PPN (Rp.)") ?></b></td>
                                <td><b>:</b></td>
                                <td class="text-right"><span class="ppn"></td>
                            </tr>
                            <tr>
                                <td> <b><?= Yii::t("fe", "Total (Rp.)") ?></b></td>
                                <td><b>:</b></td>
                                <td class="text-right"><span class="grand_total"></td>
                            </tr>
                        </table>
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
                            <div class="col-md-12">
                                <?php foreach ($list_document as $index_doc => $doc):
                                    $idDoc = DocoHelpers::encrypt($doc['penerimaanobatdoc_id']);
                                    $doc_no_penerimaan = !empty($model->no_penerimaan) ? $model->no_penerimaan : "undefined";
                                    $full_path = $path_doc."/".$doc_no_penerimaan."/".$doc['upload_berkas'];
                                    ?>
                                    <div class="col-md-12 upload-section" style="margin-bottom:5px;">
                                        <div class="lurus">
                                            <button
                                                type="button"
                                                data-id="<?= $idDoc ?>"
                                                class="btn btn-sm btn-block btn-danger delete-doc"
                                                data-action="/gudang/informasi-penerimaan-obat/delete-doc"
                                            ><i class="fa fa-trash"></i></button>
                                        </div>
                                        <div class="lurus">
                                            <input type="hidden" class="form-control">
                                            <label class="" style="margin-top:5px;">
                                                <a href="<?= $full_path ?>" target="blank" class="link-upload">
                                                    <?= preg_replace('/^(.+?)-/', '', $doc["upload_berkas"]); ?>
                                                </a>
                                            </label>
                                        </div>
                                        <div class="lurus">
                                            <div class="col-md-12">
                                                <label class="" style="margin-top:5px;">
                                                    <a href="<?= $full_path ?>" target="blank" class="link-upload">
                                                        <?= $doc['catatan_berkas'] ?>
                                                    </a>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-12" >
                                <i class="text-danger">*</i> <b><?= Yii::t('fe', 'maksimal 100 mb'); ?></b>
                            </div>
                            <div class="col-md-12 upload-section" id="upload-section">
                                <div class="lurus">
                                    <button type="button" class="btn btn-sm btn-block btn-danger delete-doc" disabled="<?= $verified ?>">
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
                                                    'cols' => 15,
                                                    'disabled' => $verified
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

<?= $this->render("/alert-perubahan-harga/_modal.php") ?>

<?php
$listCache = json_encode($listCache);
$validPO = json_encode($validPO);
$suppId = !empty($model->supplier_id) ? $model->supplier_id : 0;

$this->registerJs('
    var _cache = '. $listCache .';
    var _validPo = '. $validPO .';
    var _idPenerima = '. $diorder_id .';
    var _supplierId = '. $suppId .';
    var _verif = '. $header_static["is_verifikasi"] .';
    var list_upload = $(\'.upload-section\').get();
    var increment = list_upload.length;
    var list = [1];
    var totalPpn = '.$totalPpn.';

    var _tmp = '.json_encode($tmp__).';
    var _urlToIndex = "' . Url::to(['index']) . '";
'
. $this->render('/assets/js/informasi-penerimaan-obat/edit-penerimaan.js')
.$this->render('/alert-perubahan-harga/alert-harga.js', ["url"=>"informasi-penerimaan-obat"]), View::POS_END);
?>
