<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-03-04 16:52:42
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-08 14:52:40
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat Inap'), 'url' => ['index']];
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
                      <h3 class="panel-title"><b><?=$this->title?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
            </div>
            <div class="panel-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group required">
                      <label class="control-label col-md-4" style="margin-top: 4px"><?=Yii::t('fe', 'Cari No. Rekam Medik Orang Tua')?></label>
                      <div class="col-md-6">
                        <?= Select2::widget([
                          'name' => 'no_rekam_medik',
                          'options' => ['id'=>'no-rekam-medik','placeholder' => Yii::t('fe', 'Ketik No. Rekam Medik')],
                          'pluginOptions' => [
                              'minimumInputLength' => 0,
                              'ajax' => [
                                  'url' => "/ranap/pendaftaran-bayi/cari-rm-ibu?trace=1",
                                  'dataType' => 'json',
                                  'delay' => 250,
                                  'data' => new JsExpression('function (params) {
                                      var query = {
                                          term: params.term,
                                          page: params.page || 1,
                                        }
                                        return query; 
                                      }'),
                                  'processResults' => new JsExpression('
                                      function (data, params) {
                                          params.page = params.page || 1;
                                      return {
                                      results: data.result,
                                          pagination: {
                                              more: data.pagination
                                                      }
                                              };
                                      }'),
                                      ],
                              ],
                          ]);?>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12" id="content-pasien">
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 

$this->registerJs($this->render('js/index.js'), View::POS_END, 'js');

?>