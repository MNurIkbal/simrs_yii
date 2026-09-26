<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;

$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 
'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

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
                      <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'back',
                    'custom-print-kwitansi' => [
                        'type'=>'button',
                        'title' => Yii::t('fe', 'Cetak PDF'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'id' => 'print-detail',
                            'data-target' => '/gudang/informasi-penerimaan-obat/detail-export-pdf?id='.DocoHelpers::encrypt($id),
                            'data-options' => 'link',
                            'target' => "_blank"
                        ],
                    ],
                    'ubah' => [
                        'title' => Yii::t('fe', "Edit Penerimaan"),
                        'icon' => "fa fa-pencil",
                        'attributes' => [
                            "data-target" => $module.'edit-penerimaan-form?id='.DocoHelpers::encrypt($id),
                            "data-options" => 'link',
                            "id" => "btn-edit-penerimaan"
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Info Penerimaan') ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Nomer Penerimaan</b></label>
                                    <div class="col-sm-7"><b>:</b>&nbsp;<?= isset($header["no_penerimaan"]) ? $header["no_penerimaan"] : "-" ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Nomor Faktur</b></label>
                                    <div class="col-sm-7"><b>:</b>&nbsp;<?= isset($header["no_faktur"]) ? $header["no_faktur"] : "-" ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Tanggal Penerimaan</b></label>
                                    <div class="col-sm-7"><b>:</b>&nbsp;<?= isset($header["tgl_penerimaan"]) ? date("d-M-Y", strtotime($header["tgl_penerimaan"])) : "-" ?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Nama Supplier</b></label>
                                    <div class="col-sm-7"><b>:</b>&nbsp;<?= isset($header["supplier_nama"]) ? $header["supplier_nama"] : "-" ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Pegawai Mengetahui</b></label>
                                    <div class="col-sm-7"><b>:</b>&nbsp;<?= isset($header["mengetahui"]) ? $header["mengetahui"] : "-" ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Pegawai Menerima</b></label>
                                    <div class="col-sm-7"><b>:</b>&nbsp;<?= isset($header["menerima"]) ? $header["menerima"] : "-" ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>No PO</b></label>
                                    <div class="col-sm-7"><b>:</b>&nbsp;<?= isset($header["no_poobat"]) ? $header["no_poobat"] : "-" ?></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Pegawai Menyetujui</b></label>
                                    <div class="col-sm-7"><b>:</b>&nbsp;<?= isset($header["menyetujui"]) ? $header["menyetujui"] : "-" ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Nomor Surat Jalan</b></label>
                                    <div class="col-sm-7"><b>:</b>&nbsp;<?= isset($header["no_suratjalan"]) ? $header["no_suratjalan"] : "-" ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Tanggal Surat Jalan</b></label>
                                    <div class="col-sm-7"><b>:</b>&nbsp;<?= isset($header["tgl_suratjalan"]) ? date("d-M-Y", strtotime($header["tgl_suratjalan"])) : "-" ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Penerimaan Detail') ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <table id="table-penerimaan-obat" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th>No</th>
                                        <th class="text-center"><?=\Yii::t("fe", "Nama Obat");?></th>
                                        <th class="text-center"><?=\Yii::t("fe", "Satuan");?></th>
                                        <th class="text-center"><?=\Yii::t("fe", "Qty PO");?></th>
                                        <th class="text-center"><?=\Yii::t("fe", "PO Ballance");?></th>
                                        <th class="text-center"><?=\Yii::t("fe", "Qty Diterima");?></th>
                                        <th class="text-center" width="20%"><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                                        <th class="text-center"><?=\Yii::t("fe", "No Batch");?></th>
                                        <th class="text-center"><?=\Yii::t("fe", "Keterangan");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($detail)) : ?>
                                         <?php 
                                             $no = 1;
                                             foreach ($detail as $row) :
                                             $rowspan = count($row);
                                         ?>
                                             <?php foreach ($row as $key => $row_detail) : ?>
                                                 <tr>
                                                     <?php if($key == 0) : ?>
                                                         <td rowspan="<?= $rowspan ?>" class="text-center"><?= $no ?></td>
                                                         <td rowspan="<?= $rowspan ?>"><?= isset($row_detail["obatalkes_nama"]) ? $row_detail["obatalkes_nama"] : "" ?></td>
                                                         <td rowspan="<?= $rowspan ?>" class="text-center"><?= isset($row_detail["satuan_besar"]) ? $row_detail["satuan_besar"] : "" ?></td>
                                                         <td rowspan="<?= $rowspan ?>" class="text-right">
                                                                <?= isset($row_detail["qty_po"]) ? 
                                                                    DocoHelpers::formatNumber($row_detail["qty_po"]) : 0 ?></td>
                                                         <td rowspan="<?= $rowspan ?>" class="text-right"><?= isset($row_detail["po_balance"]) ? 
                                                                    DocoHelpers::formatNumber($row_detail["po_balance"]) : 0 ?></td>
                                                     <?php endif ?>
                                                     <td class="text-right">
                                                        <?= isset($row_detail["qty_diterima"]) ? 
                                                            DocoHelpers::formatNumber($row_detail["qty_diterima"]) : 0 ?>
                                                    </td>
                                                     <td class="text-center">
                                                        <?= isset($row_detail["tgl_kadaluarsa"]) ? date("d-M-Y", strtotime($row_detail["tgl_kadaluarsa"])) : "" ?>
                                                    </td>
                                                     <td class="text-center">
                                                        <?= isset($row_detail["no_batch"]) ? $row_detail["no_batch"] : "" ?>
                                                    </td>
                                                     <td class="text-center">
                                                        <?= isset($row_detail["keterangan"]) ? $row_detail["keterangan"] : "" ?>
                                                    </td>
                                                 </tr>
                                             <?php endforeach ?>
                                        <?php $no++; endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td class="text-center" colspan="10"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Penerimaan Dokumen') ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-12">
                            <table id="table-penerimaan-obat" 
                                class="table table-striped table-condensed table-hover" 
                                style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th>No</th>
                                        <th>Nama Dokument</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(!empty($document)) : 
                                        $no = 1;
                                    ?>
                                       <?php foreach($document as $docs) : 
                                        $nameFile = preg_replace('/^(.+?)-/', '', $docs["upload_berkas"]);
                                        $linkFile = $path . '/' . $docs["upload_berkas"];
                                       ?>
                                        <tr>
                                            <td><?= $no ?></td>
                                            <td>
                                                <a href="<?= $linkFile ?>" target="_blank">
                                                    <?= $nameFile ?>
                                                </a>
                                            </td>
                                            <td><?= $docs['catatan_berkas'] ?></td>
                                        </tr>
                                        <?php
                                            $no++; 
                                            endforeach; 
                                        ?>
                                    <?php else : ?>
                                    <tr>
                                        <td colspan="2" class="text-center">Tidak ada Dokumen</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <br>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Catatan</b></label>
                                    <div class="col-sm-7"><b>:</b>&nbsp;
                                        <?= isset($header["catatan"]) ? $header["catatan"] : "" ?>
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
