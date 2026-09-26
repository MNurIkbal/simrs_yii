<?php
// Author : Naufal Ziyad L
// Modified By: Ardi Pratama
// Modified By: Arief S.

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = Yii::t('fe', 'Identitas Sosial');
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
                    <li class="active"><a href="#view-pendidikan" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Pendidikan')?></a></li>
                    <li class=""><a href="#view-pendidikan-kualifikasi" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Pendidikankualifikasi')?></a></li>
                    <li class=""><a href="#view-pekerjaan" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Pekerjaan')?></a></li>
                    <li class=""><a href="#view-suku" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Suku')?></a></li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="view-pendidikan">
                    <div id="kontenPendidikan"></div>
                </div>
                <div class="tab-pane" id="view-pendidikan-kualifikasi">
                    <div id="kontenPendidikanKualifikasi"></div>
                </div>
                <div class="tab-pane" id="view-pekerjaan">
                    <div id="kontenPekerjaan"></div>
                </div>
                <div class="tab-pane" id="view-suku">
                    <div id="kontenSuku"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('

    $.ajax({
        type: "GET",
        url: "/master/identitas-sosial/pendidikan",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res){
         $("#kontenPendidikan").html(res)
        },
    });

    $.ajax({
        type: "GET",
        url: "/master/identitas-sosial/pendidikan-kualifikasi",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res2){
         $("#kontenPendidikanKualifikasi").html(res2)
        },
    });

    $.ajax({
        type: "GET",
        url: "/master/identitas-sosial/pekerjaan",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res3){
         $("#kontenPekerjaan").html(res3)
        },
    });

    $.ajax({
        type: "GET",
        url: "/master/identitas-sosial/suku",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res4){
         $("#kontenSuku").html(res4)
        },
    });


', View::POS_END, 'b-index');
?>
