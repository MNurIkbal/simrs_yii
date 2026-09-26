<?php

/**
 * @Author: Budi
 * @Date:   2022-12-19
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
   <div class="col-md-12">
      <div class="panel panel-default">
         <div class="panel-heading">
            <div class="row">
               <div class="column-1">
                  <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
               </div>
               <div class="column-2">
                  <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                  <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
               </div>
            </div>
            <div class="heading-elements">
               <ul class="icons-list">
                  <li><a data-action="collapse"></a></li>
                  <li><a data-action="reload"></a></li>
               </ul>
            </div>
         </div>

         <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
               'search' => [
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                     'data-table-id' => 'example',
                     'data-options' => 'click',
                  ]
               ],
               'reset' => [
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                     'data-table-id' => 'example',
                     'data-options' => 'click',
                  ]
               ],
               'excel-bgprocess' => [
                  'type' => 'button',
                  'title' => 'Lihat',
                  'icon' => 'fa fa-file-excel-o',
                  'method' => 'not-exist',
                  'attributes' => [
                     'id' => 'excel-bgprocess',
                     'data-options' => 'click',
                     'data-options' => 'excel-serconn',
                     'data-target' => '#modal_backdrop',
                     'data-width' => '75%',
                     'data-url' => '/remunerasi/integrasi/show-popup?id=',
                  ]
               ],
               'delete' => [
                  'attributes' => [
                     'id' => 'btn-delete',
                  ]
               ],
               'hitung-remun' => [
                  'type' => 'button',
                  'title' => Yii::t('fe', 'Hitung Remun'),
                  'icon' => 'fa fa-money',
                  'method' => 'not-exist',
                  'attributes' => [
                     'id' => 'calc-bgprocess',
                     'data-options' => 'click',
                     'data-options' => 'excel-serconn',
                     'data-target' => '#modal_backdrop',
                     'data-width' => '75%',
                     'data-url' => '/remunerasi/integrasi/show-popup-calc?id=',
                     'style' => 'float: right'
                  ]
               ],
               'sync' => [
                  'title' => \Yii::t('fe', 'Sinkron Data'),
                  'icon' => 'fa fa-refresh',
                  'attributes' => [
                     'id' => 'btn-sync',
                     'data-options' => 'click',
                     'style' => 'float: right'
                  ]
               ],
            ]); ?>
         </div>

         <div class="panel-body">
            <div class="table-wrapper table-scroll-x">
               <table width="100%" id="example" class="table datatable-basic table-striped table-hover dataTable no-footer">
                  <thead>
                     <tr class="bg-inverse">
                        <th class="select-checkbox" style="text-align: center;"><input id="checkBox" type="checkbox" style="width: 18px; height: 18px;"></th>
                        <th width="80"><?= Yii::t('fe', 'No') ?></th>
                        <th><?= Yii::t('fe', 'Nama Pegawai') ?></th>
                        <th><?= Yii::t('fe', 'NIK') ?></th>
                        <th><?= Yii::t('fe', 'Jabatan') ?></th>
                        <th><?= Yii::t('fe', 'Periode Remunerasi') ?></th>
                        <th><?= Yii::t('fe', 'Estimasi Penerimaan Remunerasi') ?></th>
                     </tr>
                  </thead>
                  <tbody></tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-12 col-md-12">
      <div id="modal_sinkron" class="modal fade" style="z-index:1065;" data-backdrop="static">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
         </div>
      </div>
   </div>
</div>

<?php
$this->registerJs($this->render('index.js'), View::POS_END);
?>
