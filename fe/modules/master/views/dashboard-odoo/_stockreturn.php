<?php

/**
 * @Author: Ardi Pratama [ardi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = Yii::t('fe', 'Stock Return');
?>
<style type="text/css">
    .square-batal {
        background-color: #ffcccc !important;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-body">
                <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                    <li class="active" id="tabs-stockreturn-picking"><a href="#view-stockreturn-picking" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Stock Return (stock.picking)')?></a></li>
                    <li class="" id="tabs-stockreturn-move"><a href="#view-stockreturn-move" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Stock Return Detail (stock.move)')?></a></li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="view-stockreturn-picking">
                    <div id="konten-stockreturn-picking"></div>
                </div>
                <div class="tab-pane" id="view-stockreturn-move">
                    <div id="konten-stockreturn-move"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    $(document).ready(function(){
        $("#konten-stockreturn-picking").docoLoad({
            url: "/master/dashboard-odoo/stock-return?type=picking",
            dataType: "html",
            success: function(data){
                
            },
        })
    });

    $("#tabs-stockreturn-move").on("click", function(e){
        $("#konten-stockreturn-move").docoLoad({
            url: "/master/dashboard-odoo/stock-return?type=move",
            dataType: "html",
            success: function(data){
            },
        })
    });
', View::POS_END, 'b-index');
?>