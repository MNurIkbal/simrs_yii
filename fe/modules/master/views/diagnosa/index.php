<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Diagnosa');
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
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body">
                <ul class="nav nav-tabs nav-tabs-solid nav-justified">
                    <li class="active"><a href="#view-diagnosa" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Diagnosa')?></a></li>
                    <li class=""><a href="#view-klasifikasidiagnosa" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Klasifikasi Diagnosa')?></a></li>
                    <li class=""><a href="#view-dtd" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'DTD')?></a></li>
                    <li class=""><a href="#view-kelompokdiagnosa" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Kelompok Diagnosa')?></a></li>
                    <li class=""><a href="#view-tabularchapter" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Tabular Chapter')?></a></li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="view-diagnosa">
                    <div id="kontenDiagnosa"></div>
                </div>
                <div class="tab-pane" id="view-klasifikasidiagnosa">
                    <div id="kontenKlasifikasiDiagnosa"></div>
                </div>
                <div class="tab-pane" id="view-dtd">
                    <div id="kontenDtd"></div>
                </div>
                <div class="tab-pane" id="view-kelompokdiagnosa">
                    <div id="kontenKelompokDiagnosa"></div>
                </div>
                <div class="tab-pane" id="view-tabularchapter">
                    <div id="kontentTabularChapter">  </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$this->registerJs('

    $.ajax({
        type: "GET",
        url: "/master/diagnosa/page-diagnosa",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res){
         $("#kontenDiagnosa").html(res)
        },
    });

    $.ajax({
        type: "GET",
        url: "/master/diagnosa/page-klasifikasi-diagnosa",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res2){
         $("#kontenKlasifikasiDiagnosa").html(res2)
        },
    });

    $.ajax({
        type: "GET",
        url: "/master/diagnosa/page-dtd",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res3){
         $("#kontenDtd").html(res3)
        },
    });

    $.ajax({
        type: "GET",
        url: "/master/diagnosa/page-kelompok-diagnosa",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res4){
         $("#kontenKelompokDiagnosa").html(res4)
        },
    });

    $.ajax({
        type: "GET",
        url: "/master/diagnosa/page-tabular-chapter",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res5){
         $("#kontentTabularChapter").html(res5)
        },
    });


', View::POS_END, 'b-index');
?>
