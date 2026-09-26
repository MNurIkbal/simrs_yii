<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\typeahead\Typeahead;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 
'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$parentId = DocoHelpers::encrypt($model->pemakaianobat_id);
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
              <!-- breadcrumbs replace with this -->
              <div class="row">
                  <div class="column-1">
                      <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                      <h3 class="panel-title"><b><?= $title; ?></b></h3>
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
                <?= 
                    Html::button('<b><i class="fa fa-file-pdf-o"></i></b>'.Yii::t('fe', ' Cetak PDF'),  [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'action' => '/apotek/informasi-pemakaian-obatalkes/before-print?id='.$parentId ,
                        'id' => 'show-cetak',
                        'data-width' => '800px',
                        'data-target' => '#modal_backdrop',
                        'data-toggle' => 'modal',
                    ]);
                ?>
                <?= DocoHelpers::generateToolbar(['back']); ?>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="panel-heading text-center">
                        <h5 class="panel-title"><b>Data Pemakaian Obat Alkes</b></h5>
                    </div>
                    <br>
                    <label class="control-label text-left control-label col-sm-3" for="tanggal-pemakaian">
                        <strong><?= Yii::t('fe','Tanggal Pemakaian') ?></strong>
                    </label>
                    <label class="control-label text-left control-label col-sm-3" for="tanggal-pemakaian">
                        <?= date('d M Y H:i:s', strtotime($model->tglpemakaianobat)) ?>
                    </label>
                    <label class="control-label text-left control-label col-sm-3" for="tanggal-pemakaian">
                        <strong><?= Yii::t('fe','Nomor Pemakaian') ?></strong>
                    </label>
                    <label class="control-label text-left control-label col-sm-3" for="tanggal-pemakaian">
                        <?= $model->nopemakaian_obat ?>
                    </label>
                    <br><br>
                    <label class="control-label text-left control-label col-sm-3">
                        <strong><?= Yii::t('fe','Nama Penginput') ?></strong>
                    </label>
                    <label class="control-label text-left control-label col-sm-3">
                        <?= $model->nama_pegawai ?>
                    </label>
                    <br><br>
                    <div class="panel-body">
                        <table id="pemakaian" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?=\Yii::t("fe", "Kode obat alkes");?></th>
                                    <th><?=\Yii::t("fe", "Nama obat alkes");?></th>
                                    <th><?=\Yii::t("fe", "Qty");?></th>
                                    <th><?=\Yii::t("fe", "Satuan Besar");?></th>
                                    <th><?=\Yii::t("fe", "Qty");?></th>
                                    <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
                                    <th><?=\Yii::t("fe", "Keterangan");?></th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" colspan="9">
                                        <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var pemakaian;
    $(document).ready(function() {
        $(".pickadate").pickadate({
            format: "dd-mm-yyyy",
            formatSubmit: "yyyy-mm-dd",
            onStart: function() {
                var date = new Date();
                this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
            }
        });

        pemakaian = $("#pemakaian").docoTabel({
            filter: false,
            displayLength: 10,
            processing: true,
            sorting: [[2, "asc"]], 
            serverSide: true,
            ajax: baseUrl+"apotek/informasi-pemakaian-obatalkes/get-list-item-before?id='.$parentId.'",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Kode obat alkes")).'", 
                    data: "obatalkes_kode",
                    orderable: false
                },                
                {
                    title: "'.(\Yii::t("fe", "Nama obat alkes")).'", 
                    data: "obatalkes_nama",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Qty")).'", 
                    data: "jumlah_input",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Satuan Besar")).'",
                    data: "satuanbesar_nama",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                },
               {
                    title: "'.(\Yii::t("fe", "Qty")).'", 
                    data: "qty_satuanpakai",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Satuan Kecil")).'",
                    data: "satuankecil_nama",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                },
                {
                    title: "'.(\Yii::t("fe", "Keterangan")).'",
                    data: "ket_obatpakai",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
        });
    });

',View::POS_END,'pemakaian-obat-alkes');