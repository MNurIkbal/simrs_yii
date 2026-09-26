<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Radiologi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
   <div class="col-md-12">
      <div class="panel panel-white">
         <div class="panel-heading">
            <div class="row">
               <div class="column-1">
                  <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
               </div>
               <div class="column-2">
                  <h3 class="panel-title"><b><?= Yii::t('fe', $this->title); ?></b></h3>
                  <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
               </div>
            </div>
         </div>

         <div class="panel-toolbar clearfix">
            <div class="btn-group pull-left">
               <?=DocoHelpers::generateToolbar([
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
                     'title' => 'Unduh excel',
                     'icon' => 'fa fa-file-excel-o',
                     'method' => 'not-exist',
                     'attributes' => [
                         'id'=>'excel-bgprocess',
                         'data-options' => 'excel-serconn',
                         'data-target' => '#modal_backdrop',
                         'data-width' => '50%',
                         'data-url' => $module . 'show-popup?tipe=1&',
                     ]
                  ],
                  'pdf-bgprocess' => [
                     'type' => 'button',
                     'title' => 'Cetak PDF',
                     'icon' => 'fa fa-file-pdf-o',
                     'method' => 'not-exist',
                     'attributes' => [
                         'id'=>'pdf-bgprocess',
                         'data-options' => 'excel-serconn',
                         'data-target' => '#modal_backdrop',
                         'data-width' => '50%',
                         'data-url' => $module . 'show-popup?tipe=2&',
                     ]
                  ],
               ]);?>
            </div>
         </div>

         <div class="panel-body">
            <div class="table-wrapper table-scroll-x">
               <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                  <thead>
                     <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?= Yii::t("fe", "Tanggal Persetujuan") ?></th>
                        <th><?= Yii::t("fe", "Kelas Pelayanan") ?></th>
                        <th><?= Yii::t("fe", "Pemeriksaan") ?></th>
                        <th><?= Yii::t("fe", "Tipe Prosedur") ?></th>
                        <th><?= Yii::t("fe", "Jumlah Pemeriksaan") ?></th>
                        <th><?= Yii::t("fe", "Harga Total") ?></th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td class="text-center" colspan="12"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

<?php 
$this->registerJs('
var _module = "'.$module.'"
', View::POS_END, 'b-index');
$this->registerJs($this->render('index.js'), View::POS_END);
?>
