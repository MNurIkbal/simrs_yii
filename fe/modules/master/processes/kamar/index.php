<?php

use yii\web\View;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', $title);
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
                        <a href="#view_kamar" data-toggle="tab" aria-expanded="true">
                            <?=Yii::t('fe', $_subMenuTitleKamar)?>
                        </a>
                    </li>
                    <li class="">
                        <a href="#view_kamar_klasifikasi" data-toggle="tab" aria-expanded="true">
                            <?=Yii::t('fe', $_subMenuTitleKlasifikasiKamar)?>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="view_kamar">
                    <div id="content-kamar">  </div>
                </div>
                <div class="tab-pane" id="view_kamar_klasifikasi">
                    <div id="content-kamar_klasifikasi">  </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
$.ajax({
    type: 'GET',
    url: '/master/kamar/render-page-kamar',
    dataType: 'html',
    contentType: 'application/html; charset=utf-8',
    success: function(res){
        $('#content-kamar').html(res);
        $('.select2').select2();
    },
});

$.ajax({
    type: 'GET',
    url: '/master/kamar/render-page-klasifikasi-kamar',
    dataType: 'html',
    contentType: 'application/html; charset=utf-8',
    success: function(res){
        $('#content-kamar_klasifikasi').html(res);
        $('.select2').select2();
    },
});

",View::POS_END,'b-index');
?>
