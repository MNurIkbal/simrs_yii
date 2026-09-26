<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-white">
      <div class="panel-heading">
        <!-- breadcrumbs replace with this -->
        <div class="row">
          <div class="column-1">
            <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>" alt="logo">
          </div>
          <div class="column-2">
            <h3 class="panel-title"><b><?= $this->title ?></b></h3>
            <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
          </div>
        </div>
        <!-- end -->
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
          'add',
          'edit',
          'delete' => [
            'attributes' => [
              'id' => 'data-delete',
              'data-target' => '/master/konfig-asuransi/delete?id=',
            ]
          ],
        ]); ?>
      </div>

      <div class="panel-body">
        <div class="row">
          <div class="table-wrapper table-scroll-x">
            <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
              <thead>
                <tr class="bg-inverse">
                  <th style="width: 1;"></th>
                  <th style="width: 70;">No</th>
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

  <?php
  $this->registerJs($this->render('index.js'), View::POS_END);
  ?>