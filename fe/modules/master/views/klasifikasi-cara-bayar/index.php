<?php
// Author : Naufal Ziyad L

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\web\View;

$this->title = Yii::t('fe', 'Klasifikasi Cara Bayar');
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
            <div class="panel-body">
                <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                    <li class="active">
                        <a href="#view-cara-bayar" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Cara Bayar')?></a>
                    </li>
                    <li>
                        <a href="#view-penjamin-pasien" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Penjamin Pasien')?></a>
                    </li>
                    <li>
                        <a href="#view-otoritas-penjamin" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Otoritas Penjamin')?></a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="view-cara-bayar">
                    <div id="kontenCaraBayar">  </div>
                </div>
                <div class="tab-pane" id="view-penjamin-pasien">
                    <div id="kontenPenjaminPasien">  </div>
                </div>
                <div class="tab-pane" id="view-otoritas-penjamin">
                    <div id="kontenOtoritasPenjamin">  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php
$this->registerJs('
    $.ajax({
        type: "GET",
        url: "/master/klasifikasi-cara-bayar/page-cara-bayar",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res){
         $("#kontenCaraBayar").html(res)
        },
    });

    $.ajax({
        type: "GET",
        url: "/master/klasifikasi-cara-bayar/page-penjamin",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res2){
         $("#kontenPenjaminPasien").html(res2)
        },
    });
    
    $.ajax({
        type: "GET",
        url: "/master/klasifikasi-cara-bayar/page-penjamin-diskon",
        dataType: "html",
        contentType: "application/html; charset=utf-8",
        success: function(res2){
         $("#kontenOtoritasPenjamin").html(res2)
        },
    });

', View::POS_END, 'b-index');
?>
