<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;
use app\components\DHtml;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Sysadmin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
   .dont-break-out {
   overflow-wrap: break-word;
   word-wrap: break-word;
   -ms-word-break: break-all;
   word-break: break-all;
   word-break: break-word;
   -ms-hyphens: auto;
   -moz-hyphens: auto;
   -webkit-hyphens: auto;
   hyphens: auto;
}
</style>
<div class="row">
   <div class="col-md-12">
      <div class="panel panel-white">
         <div class="panel-heading">
            <div class="row">
               <div class="column-1">
                  <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
               </div>
               <div class="column-2">
                  <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                  <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
               </div>
            </div>
         </div>

         <div class="panel-toolbar clearfix">
            <div class="btn-group pull-left">
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
               ]); ?>
            </div>
         </div>
         <div class="panel-body">
            <div class="table-wrapper table-scroll-x">
               <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                  <thead>
                        <tr class="bg-inverse">
                           <th width="1">No</th>
                           <th>Detail</th>
                           <th>Log BPJS ID</th>
                           <th>Created Date</th>
                           <th>Url</th>
                           <!-- <th>Request</th>
                           <th>Response</th> -->
                        </tr>
                  </thead>
                  <tbody></tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

<?php
$this->registerJs($this->render('index.js'), View::POS_END);
?>