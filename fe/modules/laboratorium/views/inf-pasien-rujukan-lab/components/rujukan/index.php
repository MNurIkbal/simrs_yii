<?php 
use yii\web\View;
use app\components\DocoHelpers;
use app\components\DocoConstants;
?>

<div class="row">
   <div class="col-md-12">
      <div class="panel panel-white">
         <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
               'search' => [
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                     'data-table-id' => 'tbl-rujukan',
                     'data-options' => 'click',
                  ]
               ],
               'reset' => [
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                     'data-table-id' => 'tbl-rujukan',
                     'data-options' => 'click',
                  ]
               ],
               'approve' => [
                  'title' => \Yii::t('fe', 'Approve'),
                  'icon' => 'fa fa-check-square-o',
                  'attributes' => [
                     'class' => 'spa',
                     'data-options' => 'click',
                     'data-render' => 'form-approval?id=',
                     'data-tab' => 'tab-rujukan',
                     'data-target' => '#view-rujukan',
                     'data-type' => 'wp'
                  ]
               ],
               'rujuk' => [
                  'title' => \Yii::t('fe', 'Dirujuk'),
                  'icon' => 'fa fa-arrow-right',
                  'attributes' => [
                     'class' => 'spa',
                     'data-options' => 'click',
                     'data-render' => 'rujuk?id=',
                     'data-tab' => 'tab-rujukan',
                     'data-target' => '#view-rujukan',
                     'data-type' => 'wp'
                  ]
               ],
               'batal' => [
                  'title' => \Yii::t('fe', 'Batal'),
                  'icon' => 'fa fa-times-circle-o',
                  'attributes' => [
                      'class' => 'spa',
                      'data-options' => 'click',
                      'data-render' => 'form-batal?id=',
                      'data-tab' => 'tab-rujukan',
                      'data-target' => '#view-rujukan',
                      'data-type' => 'wp',
                  ]
               ],
               'edit-tanggal' => [
                  'title' => \Yii::t('fe', 'Edit Tanggal Rujukan'),
                  'icon' => 'fa fa-pencil',
                  'attributes' => [
                     'data-options' => 'modal',
                     'data-target' => '#modal_backdrop',
                     'data-width' => '75%',
                     'id' => 'btn-edit-tanggal',
                     'data-url' => '/laboratorium/inf-pasien-rujukan-lab/form-update?id=',
                  ]
               ],
               'cetak-rujukan' => [
                  'title' => \Yii::t('fe', 'Cetak Rujukan'),
                  'icon' => 'fa fa-print',
                  'method' => '#',
                  'attributes' => [
                  'id'=>'cetak-rujukan',
                  'disabled' => true,
                  'data-options' => 'link',
                     'target'=>'_blank',
                  ]
               ],
            ], '#tbl-rujukan') ?>
         </div>
         <div class="panel-body">
            <div class="row">
               <div class="table-wrapper table-scroll-x">
                  <?= Yii::$app->controller->renderPartial('components/rujukan/_legend') ?>
                  <?= Yii::$app->controller->renderPartial('legend_cara_bayar', [ 'legendCaraBayar' => $legendCaraBayar ]) ?>
                  <table id="tbl-rujukan" class="table table-condensed table-hover" style="width:100%">
                     <thead>
                        <tr class="bg-inverse">
                           <th width="1">&nbsp;</th>
                           <th></th>
                           <th></th>
                           <th></th>
                           <th></th>
                           <th></th>
                           <th></th>
                           <th></th>
                           <th></th>
                           <th></th>
                        </tr>
                     </thead>
                     <tbody></tbody>
                  </table>
               </div>
            </div>   
         </div>
      </div>
   </div>
</div>

<?php
$this->registerJs('
var constInstalasiRJ = '.DocoConstants::INSTALASI_ID_RJ.'
var constPenjaminPerseorangan = '.DocoConstants::VAR_P_Perseorangan.'
var constDisetujui = '.DocoConstants::VAR_S_PEN_D.'
var constBatal = '.DocoConstants::VAR_S_PEN_B.'
var constAmbilSample = '.DocoConstants::VAR_S_PEN_AS.'
var constBelumPeriksa = '.DocoConstants::VAR_S_PEN_BP.'
var updateUrl = "/laboratorium/inf-pasien-rujukan-lab/update?id=";
var approveUrl = "/laboratorium/inf-pasien-rujukan-lab/form-aproval?id=";
var batalUrl = "/laboratorium/inf-pasien-rujukan-lab/form-batal?id=";
var cetakRujukUrlLab = "/laboratorium/inf-pasien-rujukan-lab/cetak-rujukan?instalasi_id='.DocoConstants::INSTALASI_ID_LAB.'&id=";
', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index-rujukan.js'), View::POS_END); ?>
