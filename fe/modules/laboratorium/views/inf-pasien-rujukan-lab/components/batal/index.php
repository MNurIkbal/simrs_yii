<?php

/**
 * @author Randy Vianda Putra
 * @todo View Informasi Pasien Laboratorium
 * @copyright 09 Juli 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;
use yii\helpers\ArrayHelper;
?>

<div class="row">
   <div class="col-md-12">
      <div class="panel panel-white">
         <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
               'search' => [
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                     'data-table-id' => 'table-batal',
                     'data-options' => 'click',
                  ]
               ],
               'reset' => [
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset-batal',
                     'data-table-id' => 'table-batal',
                     'data-options' => 'click',
                  ]
               ],
            ],'#table-batal');
            ?>
         </div>
         <div class="panel-body">
            <div class="row">
               <div class="table-wrapper table-scroll-x">
                  <div class="col-md-6"></div>
                  <?= Yii::$app->controller->renderPartial('legend_cara_bayar', [ 'legendCaraBayar' => $legendCaraBayar ]) ?>
                  <table id="table-batal" class="table table-condensed table-hover" style="width:100%">
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
', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
