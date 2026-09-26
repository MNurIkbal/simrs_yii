<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\helpers\Url;

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
                     'data-table-id' => 'laporan-adjustment',
                     'data-options' => 'click',
                  ]
               ],
               'reset' => [
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                     'data-table-id' => 'laporan-adjustment',
                     'data-options' => 'click',
                  ]
               ],
               'export-excel-serconn' => [
                  'type' => 'button',
                  'title' => \Yii::t('fe', 'Export Excel'),
                  'icon' => 'fa fa-file-excel-o',
                  'attributes' => [
                     'id' => 'data-export-excel-serconn',
                     'data-options' => 'excel-serconn',
                     'data-target' => '#modal_backdrop',
                     'data-table-id' => 'laporan-adjustment',
                     'data-url' => Url::home() . 'gudang/laporan-adjustment/show-popup-excel?',
                     'data-width' => '75%'
                  ]
               ],
            ];
            ?>
            <?= DocoHelpers::generateToolbar($btn_toolbar, '#laporan-adjustment'); ?>
         </div>
         <div class="panel-body">
            <table id="laporan-adjustment" class="table table-striped table-condensed table-hover" style="width:100%">
               <thead>
                  <tr class="bg-inverse">
                     <?php foreach ($columns as $column) : ?>
                        <th><?= $column ?></th>
                     <?php endforeach; ?>
                  </tr>
               </thead>
               <tbody></tbody>
            </table>
         </div>
      </div>
   </div>
</div>

<?php
$this->registerJs('
const dropdownColumns = ' . json_encode($columns) . '
const filters = ' . json_encode($filters) . '
', View::POS_END, 'b-index');
$this->registerJs($this->render("js/obat-alkes.js"), View::POS_END); ?>
