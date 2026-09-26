<?php

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', 'Laporan Rekap Jasa Dokter');
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
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
                     <h3 class="panel-title"><b><?= Yii::t('fe', 'Laporan Rekap Jasa Dokter'); ?></b></h3>
                     <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
               </div>
            </div>
               <div class="heading-elements">
                  <ul class="icons-list">
                     <li><a data-action="collapse"></a></li>
                  </ul>
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
                     'detail' => [
                        'title' => Yii::t('fe', 'Detail'),
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'id' => 'btn-detail',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '75%',
                            'data-url' => '/kasir/lap-rekap-jasa-dokter/detail?id=',
                            'data-conditions' => 'dokterpenanggungjawab_id',
                            'disabled' => true,
                        ]
                    ],
                  ]);?>
                  <button id="btnGroupRajal" type="button" class="btn btn-info btn-labeled btn-toolbar btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <b><i class="fa fa-search"> </i> </b> Filter Pasien Per Instalasi / Cara Bayar  <i class="caret"> </i> 
                  </button>
                  <div class="dropdown-menu">
                     <li><a href="#" class="btn-rajal-umum">Rajal Pribadi</a></li>
                     <li><a href="#" class="btn-rajal-penjamin">Rajal Penjamin</a></li>
                     <li class="divider"></li>
                     <li><a href="#" class="btn-igd-umum">IGD Pribadi</a></li>
                     <li><a href="#" class="btn-igd-penjamin">IGD Penjamin</a></li>
                     <li class="divider"></li>
                     <li><a href="#" class="btn-ranap-umum">Ranap Pribadi</a></li>
                     <li><a href="#" class="btn-ranap-penjamin">Ranap Penjamin</a></li>
                     <li class="divider"></li>
                     <li><a href="#" class="btn-all-umum">Semua Pribadi</a></li>
                     <li><a href="#" class="btn-all-penjamin">Semua Penjamin</a></li>
                  </div>
               </div>
         </div>
         <div class="panel-body">
            <div class="table-wrapper table-scroll-x">
               <hr>
               <div class="row" style="margin-left: 20px;">
                  <p style="text-align: center;" class="keterangan_title"><span id="info_title"></span></br><span id="info_periode"></span></p>
                  <p class="keterangan_dokter"><span id="info_label_dokter"></span></p>
                  <p class="keterangan_payer"><span id="info_label_payer"></span></p>
               </div>
               <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                  <thead>
                     <tr class="bg-inverse">
                        <th width="1"></th>
                        <th width="1">No</th>
                        <th><?=\Yii::t("fe", "");?></th>
                        <th><?=\Yii::t("fe", "");?></th>
                        <th><?=\Yii::t("fe", "");?></th>
                        <th><?=\Yii::t("fe", "");?></th>
                        <th><?=\Yii::t("fe", "");?></th>
                        <th><?=\Yii::t("fe", "");?></th>
                        <th><?=\Yii::t("fe", "");?></th>
                     </tr>
                  </thead>
                  <tbody>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

<?php
$this->registerJs('
', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END); ?>
