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
<style>
    .datepicker>div{
        display:block;
    }

    .select2-selection--single .select2-selection__rendered {
        padding-right: 31px!important;
    }

    .table-condensed > tbody > tr > td,
    .table-condensed > tbody > tr > th {
        padding: 8px 10px;
    }

    .action-button {
        width: 30px;
        height: 30px;
        font-size: 13px;
        padding: 5px;
    }

    .required-asterisk {
        color: red;
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
                                'href' => '/pengadaan/info-purchase-order'
                            ]
                        ],
                        'custom-validasi' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Validasi'),
                            'icon' => 'fa fa-floppy-o',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'btn-validasi',
                                'style' => $btnValidasiAccess ? null : 'display: none;'
                            ],
                        ],
                        'custom-print' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Cetak'),
                            'icon' => 'fa fa-print',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'btn-print',
                                'action' => "/pengadaan/info-purchase-order/cetak-rincian?no_transaksi={$model->no_transaksi}&type_po={$type_po}"
                            ],
                        ],
                        'print-rincian-kop' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Cetak (Kop)'),
                            'icon' => 'fa fa-print',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'btn-print-kop',
                                'action' => "/pengadaan/info-purchase-order/cetak-rincian-kop?no_transaksi={$model->no_transaksi}&type_po={$type_po}"
                            ],
                        ],
                        'custom-save' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-floppy-o',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'btn-simpan',
                                'style' => !empty($model->is_validasi) ? 'display: none;' : null
                            ],
                        ],
                        'custom-edit' => [
                            'type' => 'button',
                            'icon' => 'fa fa-pencil',
                            'title' => \Yii::t('fe', 'Edit'),
                            'attributes' => [
                                'id' => 'btn-edit-po',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '50%',
                                'action' => "/pengadaan/info-purchase-order/edit",
                                'style' => empty($model->is_validasi) ? 'display: none;' : null
                            ]
                        ],
                        'custom-log' => [
                            'type' => 'button',
                            'icon' => 'fa fa-list',
                            'title' => \Yii::t('fe', 'Log Perubahan'),
                            'attributes' => [
                                'id' => 'btn-log',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '60%',
                                'action' => "/pengadaan/info-purchase-order/log-edit?id={$id}&type_po={$type_po}"
                            ]
                        ]
                    ]);
                ?>
            </div>

            <?php
                if ($status_batal) :
            ?>
                <div class="row">
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-7 text-danger">
                            <b>
                                *) Transaksi Telah Dibatalkan/Ditolak
                            </b>
                        </label>
                    </div>
                </div>
            <?php
                endif;
            ?>
            <?php
                $form = ActiveForm::begin([
                    'id' => 'po-form',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'action' => "/pengadaan/info-purchase-order/save?id={$id}&type_po={$type_po}",
                    'formConfig' => [
                        'labelSpan' => 3,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'skip-confirm' => "true"
                    ]
                ]);
            ?>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title">Info Data Pesanan</h6>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Nomor PO") ?></b>
                                </label>
                                <div class="col-sm-5 no_po">
                                    <p>
                                        <b>:</b>&nbsp;<?= !empty($model->no_transaksi)
                                        ? $model->no_transaksi : '-' ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Nomor Transaksi (RO/PR)") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= $nomor_pr ?> </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Tanggal PO") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= !empty($model->tanggal_po) ? date('d M Y', strtotime($model->tanggal_po)) : '-' ?> </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Tanggal (RO/PR)") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= !empty($model->tgl_rekomendasi) ? date('d M Y', strtotime($model->tgl_rekomendasi)) : '-' ?> </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Instalasi") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= !empty($model->instalasi_nama) ? $model->instalasi_nama : '-' ?> </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Ruangan") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= !empty($model->ruangan_nama) ? $model->ruangan_nama : '-' ?> </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Supplier") ?> <span class="required-asterisk">*</span></b>
                                </label>
                                <?= $form->field($model, 'supplier_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-sm-5'
                                    ],
                                ])->dropDownList(ArrayHelper::map($supplier,'supplier_id','supplier_nama'),[
                                    'class' => 'form-control select2',
                                    'prompt' => Yii::t('fe', 'Pilih Supplier'),
                                    'id' => 'supplier_id',
                                ])->label(false); ?>
                            </div>
                           <!--  <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Supplier") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= !empty($model->supplier_nama) ? $model->supplier_nama : '-' ?> </p>
                                </div>
                            </div> -->
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5">
                                    <b><?= Yii::t("fe", "Diorder Oleh") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <?php $diorder_id = empty($model->diorder_oleh ? $model->diorder_oleh : 0) ?>
                                    <p><b>:</b>&nbsp;
                                        <?= !empty($order_name['diorder_nama']) ? $order_name['diorder_nama'] : '-' ?> </p>
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
                        <div class="row">
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5" style="padding-top: 0px !important;">
                                    <b><?= Yii::t("fe", "Cito ") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($model, 'po_cito'); ?> </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5" style="padding-top: 0px !important;">
                                    <b><?= Yii::t("fe", "Admin") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($model, 'po_admin'); ?> </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-left control-label col-sm-5" style="padding-top: 0px !important;">
                                    <b><?= Yii::t("fe", "Consigment") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($model, 'po_consigment'); ?> </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title">Data Pesanan</h6>
                    </div>
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-md-6">
                            <?php
                                $model->tgl_rencanaterima = !empty($model->tgl_rencanaterima)
                                    ? date('d-M-Y',strtotime($model->tgl_rencanaterima)) : date('d-M-Y')
                            ?>
                            <?= $form->field($model, 'tgl_rencanaterima',[
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
                                    'startDate' => "0d",
                                ]
                            ]); ?>
                            </div>
                            <div class="col-md-6">
                                <?php
                                    $peg_menyetujui_id = !empty($model->peg_menyetujui_id)
                                        ? $model->peg_menyetujui_id : null;
                                    $peg_menyetujui = !empty($model->peg_menyetujui) ? $model->peg_menyetujui : null;
                                ?>
                                <?= $form->field($model, 'peg_menyetujui_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->dropDownList([$peg_menyetujui_id => $peg_menyetujui],[
                                    'class' => 'form-control selectPegawai select2',
                                    'prompt' => Yii::t('fe', 'Pilih Pegawai Menyetujui'),
                                    'id' => 'peg_menyetujui_id',
                                ])->label(Yii::t('fe', 'Menyetujui')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'payterm_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->dropDownList(ArrayHelper::map($payterm,'payterm_id','payterm_nama'),[
                                    'class' => 'form-control select2',
                                    'prompt' => Yii::t('fe', 'Pilih Payment Term'),
                                    'id' => 'payterm_id',
                                ])->label(Yii::t('fe', 'Payment Term')); ?>
                            </div>
                            <div class="col-md-6">
                                <?php
                                    $megetahui_id = !empty($model->peg_mengetahui_id)
                                        ? $model->peg_mengetahui_id : null;
                                    $peg_mengetahui = !empty($model->peg_mengetahui) ? $model->peg_mengetahui : null;
                                ?>
                                <?= $form->field($model, 'peg_mengetahui_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->dropDownList([$megetahui_id => $peg_mengetahui],[
                                    'class' => 'form-control selectPegawai select2',
                                    'prompt' => Yii::t('fe', 'Pilih Pegawai Mengetahui'),
                                    'id' => 'peg_mengetahui_id',
                                ])->label(Yii::t('fe', 'Mengetahui')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'pajak_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])->dropDownList(ArrayHelper::map($pajak,'pajak_id','pajak_name'),[
                                    'class' => 'form-control select2',
                                    'prompt' => Yii::t('fe', 'Pilih Tarif Pajak'),
                                    'id' => 'pajak_id',
                                ])->label(Yii::t('fe', 'Tarif Pajak')); ?>
                            </div>
                        </div>
                        <hr>
                        <h3 class="form-section">Data Barang/Obat</h3>
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="po" class="table table-condensed">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th class="text-center" width="1%">
                                                <?=\Yii::t("fe", "No");?>
                                            </th>
                                            <th class="text-center">
                                                <?=\Yii::t("fe", "Nomor PR");?>
                                            </th>
                                            <th class="text-center" width="5%">
                                                <?=\Yii::t("fe", "Nama");?>
                                            </th>
                                            <th class="text-center" width="3%">
                                                <?=\Yii::t("fe", "Qty Permintaan");?>
                                            </th>
                                            <th class="text-center" width="10%">
                                                <?=\Yii::t("fe", "Qty");?>
                                            </th>
                                            <th class="text-center" width="10%">
                                                <?=\Yii::t("fe", "Satuan");?>
                                            </th>
                                            <th class="text-center" width="10%">
                                                <?=\Yii::t("fe", "Konversi");?>
                                            </th>
                                            <th class="text-center" width="10%">
                                                <?=\Yii::t("fe", "Harga Dasar Sekarang (Rp.)");?>
                                            </th>
                                            <th class="text-center" width="10%">
                                                <?=\Yii::t("fe", "Harga (Rp.)");?>
                                            </th>
                                            <th class="text-center" width="">
                                                <?=\Yii::t("fe", "Subtotal (Rp.)");?>
                                            </th>
                                            <th class="text-center" width="5%">
                                                <?=\Yii::t("fe", "Discount (%)");?>
                                            </th>
                                            <th class="text-center" width="20%">
                                                <?=\Yii::t("fe", "Discount (Rp.)");?>
                                            </th>
                                            <th class="text-center">
                                                <?=\Yii::t("fe", "Jumlah (Rp.)");?>
                                            </th>
                                            <th class="text-center" width="6%">
                                                <?=\Yii::t("fe", "Aksi");?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $no = 1;
                                            $_listIncremnt = [];
                                            $_listQty = [];
                                            foreach ($detail as $value) :
                                                if($value["is_active"] === false) {
                                                    continue;
                                                }
                                                $is_belumterima = false;
                                                $list = isset($konversi[$value['obat_barang_id']])
                                                        ? $konversi[$value['obat_barang_id']] : [];
                                                $default = isset($nilaiDefault[$value['obat_barang_id']])
                                                        ? $nilaiDefault[$value['obat_barang_id']] : null;
                                                $idPrimary = $value['nomor'] . "-" .$value['obat_barang_id'];
                                                $arrDetailKonversi = isset($detailKonversi[$value['obat_barang_id']])
                                                        ? $detailKonversi[$value['obat_barang_id']] : [];
                                                $optionAttributes = [];
                                                foreach ($arrDetailKonversi as $key => $v) {
                                                    $optionAttributes[$key] = [
                                                        'data-detailKonversi' => $v,
                                                        'data-idPrimary' => $idPrimary
                                                    ];
                                                }
            
                                                if($instalasi_id == 15) {
                                                    $code = "-B";
                                                } else {
                                                    $code = "-O";
                                                }

                                                $obatalkes_id = $value['obat_barang_id'];
                                                
                                                $arrLabel = [];
                                                if(isset($hasilKonversi[$obatalkes_id][$value['s_konversiobt_id']])) {
                                                    $nilai_konversi = $hasilKonversi[$obatalkes_id][$value['s_konversiobt_id']];
                                                } else {
                                                    $nilai_konversi = 1;
                                                }

                                                $satuan_konversi = "-";
                                                if(isset($labelKonversi[$obatalkes_id][$value['s_konversiobt_id']])) {
                                                    $satuan_besar = 1 . " " . $labelKonversi[$obatalkes_id][$value['s_konversiobt_id']]["besar"];
                                                    $satuan_kecil = (1 * $nilai_konversi) . " " . $labelKonversi[$obatalkes_id][$value['s_konversiobt_id']]["kecil"];
                                                    $satuan_konversi = $satuan_besar . " = " . $satuan_kecil;
//                                                    if($nilai_konversi == 1) {
//                                                        $satuan_konversi = $satuan_besar;
//                                                    }
                                                }

                                                $harga_sekarang = (isset($master_harga[$obatalkes_id]) ? $master_harga[$obatalkes_id] : 0) * $nilai_konversi;
                                                $harga = $value['harga'];

                                                $_listIncremnt[$idPrimary] = [
                                                    'id' => $no,
                                                    'item_id' => $value['obat_barang_id'].$code,
                                                    'qty' => $value['qty_input'],
                                                    'qty_awal' => $value['qty_input'],
                                                    'satuan_id' => $value['s_konversiobt_id'],
                                                    'satuan_asal' => $value['s_konversiobt_id'],
                                                    'satuan_konversi' => $satuan_konversi,
                                                    'harga_sekarang' => $harga_sekarang,
                                                    'harga' => $harga,
                                                    'harga_awal' => $harga,
                                                    'discount' => $value['discount'],
                                                    'discount_rp' => $value['discount_rp'],
                                                    'total_harga' => $value['jumlah'],
                                                    'nilai_konversi' => $nilai_konversi,
                                                    'is_obat' => $value['is_obat'],
                                                    'id_detail' => $value['id_detail'],
                                                    'is_disc_nominal' => $value['is_disc_nominal'],
                                                ];

                                                $_listQty[$idPrimary] = [
                                                    'qty' => $value['qty_input'],
                                                    'nilai_konversi' => $nilai_konversi
                                                ];
                                                // $obatalkes_id = $value['obat_barang_id'];
                                                // $nilai_konversi = $hasilKonversi[$obatalkes_id][$value['s_konversiobt_id']];
                                                // $harga_sekarang = $master_harga[$obatalkes_id] * $nilai_konversi;
                                                $harga_after_diskon = ($value['harga'] * $value['qty_input']) - $value['discount_rp'];

                                                if($value['is_terima'] == false) {
                                                    $is_belumterima = true;
                                                }
                                        ?>
                                            <tr id="<?= $idPrimary ?>">
                                                <td class="text-center"><?= $no ?></td>
                                                <td><?= $value['nomor'] ?></td>
                                                <td width="20%"><?= $value['kode_item']." - ".$value['obat_barang_nama'] ?></td>
                                                <td class="text-center">
                                                    <?= DocoHelpers::formatNumber($value['qty_rekomendasi'])." ".$value['satuan_pr'] ?>
                                                </td>
                                                <td width="4%">
                                                    <div class="input-group">
                                                            <input type='text'
                                                                name='InfoPoForm[qty][<?= $idPrimary ?>]'
                                                                class='form-control input-sm text-right qty'
                                                                value="<?= $value['qty_input'] ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <?= Html::dropDownList('InfoPoForm[konversi]['.$idPrimary.']', !empty($value['s_konversiobt_id'])
                                                                ? $value['s_konversiobt_id'] : $default,$list,[
                                                                        'class' => 'select2 konversi',
                                                                        'prompt' => '-- Pilih --',
                                                                        'data-index' => $idPrimary,
                                                                        'options' => $optionAttributes
                                                                    ]) ?>
                                                    </div>
                                                </td>
                                                <td class="text-left satuan_konversi detail-konversi-<?= $idPrimary ?>">
                                                    <?php 
                                                    //$satuan_konversi
                                                    ?>
                                                </td>
                                                <td class="text-right harga_sekarang">
                                                    <?= DocoHelpers::formatNumber($harga_sekarang) ?>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type='text'
                                                            name='InfoPoForm[harga][<?= $idPrimary ?>]'
                                                            class='form-control harga input-sm text-right'
                                                            value="<?= number_format($value['harga'],2,",",".") ?>"
                                                        >
                                                    </div>
                                                </td>
                                                <td class="text-right jumlah">
                                                    <?= DocoHelpers::formatNumber($value['jumlah']) ?>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type='text'
                                                            name='disc[]'
                                                            class='form-control disc input-sm text-right'
                                                            value="<?= DocoHelpers::formatNumber($value['discount'],2) ?>">
                                                    </div>
                                                </td>
                                                <td class="text-right disc-rp">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">
                                                            <label><input type="checkbox" class="discount_rp_check" value="<?= $value['is_disc_nominal'] ?>"></label>
                                                        </span>
                                                        <input type="text" 
                                                            name='InfoPoForm[discount_rp][<?= $idPrimary ?>]'
                                                            class="form-control diskon_rp input-sm doco-number-decimal text-right"
                                                            readonly="true"
                                                            value="<?= DocoHelpers::formatNumber($value['discount_rp']) ?>">
                                                    </div>
                                                </td>
                                                <td class="text-right subtotal-after-disc">
                                                    <?= DocoHelpers::formatNumber($harga_after_diskon) ?>
                                                </td>
                                                <td>
                                                    <!-- button delete here -->
                                                    <button type="button" class="deleteRow btn btn-danger btn-custom action-button"><span class="fa fa-trash"></span></button>
                                                    
                                                    <?php if($is_belumterima) {
                                                        echo '<button 
                                                                type="button"
                                                                class="splitPO btn btn-primary btn-custom action-button"
                                                                data-toggle="modal"
                                                                data-target="#modal_backdrop"
                                                                data-width="80%",
                                                                action="/pengadaan/info-purchase-order/split-po?id='.$id.'&item_key='.$idPrimary.'&type_po='.$type_po.'"
                                                            >
                                                                <span class="fa fa-exchange"></span>
                                                            </button>';
                                                    }
                                                    ?>
                                                    
                                                </td>
                                            </tr>
                                            <?php
                                                $no++;
                                                endforeach;
                                            ?>
                                            <!-- <tr id="tr-default" data-row="" data-last="">
                                                <td colspan="9" class="text-center">
                                                </td>
                                                <td class="" style="height: 50px!important;">
                                                    <button type="button" class="addrow btn btn-info btn-custom">
                                                        <span class="fa fa-plus"></span>
                                                    </button>
                                                </td>
                                            </tr> -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="11" class="text-right"><h5>Sub Total Harga (Rp.)</h5></th>
                                            <th colspan="2" class="text-right"><h5><span class="sub_total_before_disc"></span></h5></th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th colspan="11" class="text-right"><h5> Total Diskon (Rp.)</h5></th>
                                            <th colspan="2" class="text-right"><h5><span class="total_diskon"></span></h5></th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th colspan="11" class="text-right"><h5>Sub Total (Rp.)</h5></th>
                                            <th colspan="2" class="text-right"><h5><span class="sub_total"></span></h5></th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th colspan="11" class="text-right"><h5> PPN (Rp.) </h5></th>
                                            <th colspan="2" class="text-right"><h5><span class="ppn"></span></h5></th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th colspan="11" class="text-right"><h5> Total (Rp.)</h5></th>
                                            <th colspan="2" class="text-right"><h5><span class="grand_total"></span></h5></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <br><br>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'catatan1',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-7'
                                    ],
                                ])->textArea([
                                    'rows' => 5,
                                ])->label(Yii::t('fe', 'Catatan Internal')); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'catatan2',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-7'
                                    ],
                                ])->textArea([
                                    'rows' => 5,
                                ])->label(Yii::t('fe', 'Catatan Eksternal')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
            <?php
                if (isset($model->peg_mengubah)) :
            ?>
                <div class="row">
                    <div class="col-md-6">
                        <label class="text-left control-label col-sm-7">
                            <b>
                                *) Terakhir diubah oleh : <?= $model->peg_mengubah; ?>
                            </b><br>
                            <b>
                                **) Waktu perubahan : <?= $model->tgl_perubahan; ?>
                            </b>
                        </label>
                    </div>
                </div>
            <?php
                endif;
            ?>
        </div>
    </div>
</div>
<?php
$mapPajak = ArrayHelper::map($pajak,'pajak_id','pajak_persen');
$statusPo = $model->status_penerimaan == DocoConstants::STATUS_BELUM_TERIMA_PO ? 0 : 1;
$this->registerJs('
    var _hasilKonversi = '. json_encode($hasilKonversi) .';
    var _listIncremnt = '. json_encode($_listIncremnt) .';
    var _listQty = '. json_encode($_listQty) .';
    var _mapPajak = '. json_encode($mapPajak) .';
    var _status = '. $statusPo .';
    var _isValidasi = "'. $isValidasi .'";
    var instalasi_id = "'. $instalasi_id .'";
    var ruangan_id = "'. $ruangan_id .'";
    var is_valid = false;
    var alasan_edit;
    var status_batal = "'. $status_batal .'";
    var diorder_oleh = "'. $diorder_id .'";
', View::POS_END);

$this->registerJs($this->render("assets/js/detail.js"));
$this->registerJs($this->render("assets/js/edit-po.js"));
?>
