<?php

/**
 * @Author: Sigit
 * @Date:   2018-06-06 09:24:42
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2020-01-31 16:20:53
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\web\View;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
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
                          <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                          <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                      </div>
                  </div>
                  <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <!-- <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li> -->
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="tabbable">
                    <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                        <li class="active" id="tab-group-margin"><a href="#view-group-margin" data-toggle="tab" aria-expanded="true"><strong><?=Yii::t('fe', 'Group Margin')?></strong></a></li>
                        <li class="" id="tab-margin-harga-obat"><a href="#view-margin-harga-obat" data-toggle="tab" aria-expanded="true"><strong><?=Yii::t('fe', 'Margin Harga Obat')?></strong></a></li>
                        <li class="" id="tab-margin-khusus"><a href="#view-margin-khusus" data-toggle="tab" aria-expanded="true"><strong><?=Yii::t('fe', 'Margin Khusus Rumah Sakit')?></strong></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="view-group-margin">
                            <div id="content-group-margin" >  </div>
                        </div>
                        <div class="tab-pane" id="view-margin-harga-obat">
                            <div id="content-margin-harga-obat" >  </div>
                        </div>
                        <div class="tab-pane" id="view-margin-khusus">
                            <div id="content-margin-khusus">  </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
  var jenisobatalkes;
  ', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
