<?php

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = [
   'label' => Yii::$app->docoVars->workspace("modul_alias"),
   'url' => ['index']
];
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
                  <h3 class="panel-title"><b><?= $title; ?></b></h3>
                  <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
            <?php
            $btn_toolbar = [
               'search' => [
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                     'data-table-id' => 'laporan-adjustment-barang',
                     'data-options' => 'click',
                  ]
               ],
               'reset' => [
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                     'data-table-id' => 'laporan-adjustment-barang',
                     'data-options' => 'click',
                  ]
               ],
               'excel-post' => [
                  'title' => Yii::t('fe', 'Export Excel'),
                  'icon' => 'fa fa-file-excel-o',
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs export-excel-laporan',
                     'data-options' => 'click',
                  ]
               ],
            ];
            ?>
            <?= DocoHelpers::generateToolbar($btn_toolbar, '#laporan-adjustment-barang'); ?>
         </div>
         <div class="panel-body">
            <table id="laporan-adjustment-barang" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th style="width: 1">No</th>
                        <th><?=\Yii::t("fe", "Ruangan");?></th>
                        <th><?=\Yii::t("fe", "No Transaksi");?></th>
                        <th><?=\Yii::t("fe", "Tanggal Adjustment");?></th>
                        <th><?=\Yii::t("fe", "Jenis Adjusment");?></th>
                        <th><?=\Yii::t("fe", "Kode Barang");?></th>
                        <th><?=\Yii::t("fe", "Nama Barang");?></th>
                        <th><?=\Yii::t("fe", "Qty");?></th>
                        <th><?=\Yii::t("fe", "Satuan");?></th>
                        <th><?=\Yii::t("fe", "Qty Konversi");?></th>
                        <th><?=\Yii::t("fe", "Satuan Terkecil");?></th>
                        <th><?=\Yii::t("fe", "Nama Pegawai");?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan="10"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                    </tr>
                </tbody>
            </table>
         </div>
      </div>
   </div>
</div>

<?php
$this->registerJs('
const filters = ' . json_encode($filters) . '
', View::POS_END, 'b-index');
$this->registerJs($this->render("js/index.js"), View::POS_END); ?>
