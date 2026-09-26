<?php
// Author : Naufal Ziyad L

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\web\View;

$this->title = Yii::t('fe', 'Identitas Pasien');
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
                    <li class="active"><a href="#view-cara-masuk" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Cara Masuk')?></a></li>
                    <li class=""><a href="#view-golongan-umur" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Golongan Umur')?></a></li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="view-cara-masuk">
                    <div id="kontenCaraMasuk">  </div>
                </div>
                <div class="tab-pane" id="view-golongan-umur">
                    <div id="kontenGolonganUmur">  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('

    $.ajax({
        type: "GET",
        url: "/master/identitas/cara-masuk",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res){
         $("#kontenCaraMasuk").html(res)
        },
    });

    $.ajax({
        type: "GET",
        url: "/master/identitas/gol-umur",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res){
         $("#kontenGolonganUmur").html(res)
        },
    });

', View::POS_END, 'b-index');
?>
