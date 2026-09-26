<?php

use yii\web\View;
// use yii\widgets\Pjax;
// use app\components\DocoController;
// use yii\helpers\Url;
// use yii\helpers\Html;
use app\components\DocoHelpers;
use yii\bootstrap\Tabs;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'Kelas');
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
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class='tabbable'>
                <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                    <li class="active">
                        <a href="#view-kelaspelayanan" data-toggle="tab" aria-expanded="true">
                            <?=Yii::t('fe', 'Kelas pelayanan')?>
                        </a>
                    </li>
                    <li class="">
                        <a href="#view-kelasruangan" data-toggle="tab" aria-expanded="true">
                            <?=Yii::t('fe', 'Kelas ruangan')?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="view-kelaspelayanan">
                    <div id="content-kelaspelayanan">  </div>
                </div>
                <div class="tab-pane" id="view-kelasruangan">
                    <div id="content-kelasruangan">  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
$.ajax({
    type: 'GET',
    url: '/master/kelas/page-kelas-pelayanan',
    dataType: 'html',
    contentType: 'application/html; charset=utf-8',
    success: function(res){
        $('#content-kelaspelayanan').html(res);
        $('.select2').select2();
    },
});
$.ajax({
    type: 'GET',
    url: '/master/kelas/page-kelas-ruangan',
    dataType: 'html',
    contentType: 'application/html; charset=utf-8',
    success: function(res){
        $('#content-kelasruangan').html(res);
        $('.select2').select2();
    },
});

",View::POS_END,'kelas');
?>
