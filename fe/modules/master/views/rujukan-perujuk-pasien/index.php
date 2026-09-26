<?php
// Author : Naufal Ziyad L

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = Yii::t('fe', 'Rujukan - Perujuk Pasien');
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
                <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                    <li class="active"><a href="#view-asal-rujukan" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Asal Rujukan')?></a></li>
                    <li class=""><a href="#view-perujuk" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Perujuk')?></a></li>
                    <li class=""><a href="#view-rujukan-keluar" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Rujukan Keluar')?></a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="view-asal-rujukan">
                    <div id="kontenAsalRujukan">  </div>
                </div>
                <div class="tab-pane" id="view-perujuk">
                    <div id="kontenPerujuk">  </div>
                </div>
                <div class="tab-pane" id="view-rujukan-keluar">
                    <div id="kontenRujukanKeluar">  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('

    $.ajax({
        type: "GET",
        url: "/master/rujukan-perujuk-pasien/page-asal-rujukan",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res){
         $("#kontenAsalRujukan").html(res)
        },
    });

    $.ajax({
        type: "GET",
        url: "/master/rujukan-perujuk-pasien/page-perujuk",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res2){
         $("#kontenPerujuk").html(res2)
        },
    });

    $.ajax({
        type: "GET",
        url: "/master/rujukan-perujuk-pasien/page-rujukan-keluar",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res3){
         $("#kontenRujukanKeluar").html(res3)
        },
    });

', View::POS_END, 'b-index');
?>
