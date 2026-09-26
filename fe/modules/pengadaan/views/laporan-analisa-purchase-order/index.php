<?php

/**
 * ? @author : Budi (budi@sirs.co.id)
 * ? Powered by Sirs
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
<style>
   #is_prcyto, #is_admin, #is_consignment {
      width: 20%;
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
                     'data-table-id' => 'example',
                     'data-options' => 'click',
                  ]
               ],
               'reset' => [
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset data-reset',
                     'data-table-id' => 'example',
                     'data-options' => 'click',
                  ]
               ],
               'excel-post' => [
                  'title' => Yii::t('fe', 'Export Excel'),
                  'icon' => 'fa fa-file-excel-o',
                  'attributes' => [
                     'data-options' => 'excel-serconn',
                     'data-target' => '#modal_backdrop',
                     'data-width' => '75%',
                     'data-url' => Url::home() . 'pengadaan/laporan-analisa-purchase-order/show-popup-excel?',
                     'id' => 'excel-bg-process',
                     'data-table-param-exclude' => ['columns'],
                     'class' => 'data-excel',
                  ]
               ],
            ];
            ?>
            <?= DocoHelpers::generateToolbar($btn_toolbar, '#example'); ?>
         </div>
         <div class="panel-body">
            <div class="row">
               <div class="legend-index">
                     <?php echo $this->render('../assets/js/filter/filter.php', [
                        'tableName' => 'example',
                        'url' => '/pengadaan/laporan-analisa-purchase-order',
                        'get' => '/get-data?',
                        'excel' => '/show-popup-excel?',
                        'type' => 'obat',
                     ]); ?>
               </div>
            </div>
            <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
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
const dropdownStatus = ' . json_encode($dropdownStatus) . ';
const dropdownColumns = ' . json_encode($columns) . '
', View::POS_END, 'b-index');
$this->registerJs($this->render("index.js"), View::POS_END); ?>