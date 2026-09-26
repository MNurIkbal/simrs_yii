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

$this->title = Yii::t('fe', 'Stock Out');
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
                    <li class="active" id="tabs-stockout-picking"><a href="#view-stockout-picking" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Stock Out (stock.picking)')?></a></li>
                    <li class="" id="tabs-stockout-move"><a href="#view-stockout-move" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Stock Out Detail (stock.move)')?></a></li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="view-stockout-picking">
                    <div id="konten-stockout-picking"></div>
                </div>
                <div class="tab-pane" id="view-stockout-move">
                    <div id="konten-stockout-move"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    $(document).ready(function(){
        $("#konten-stockout-picking").docoLoad({
            url: "/master/dashboard-odoo/stockout?type=picking",
            dataType: "html",
            success: function(data){
                
            },
        })
    });

    $("#tabs-stockout-move").on("click", function(e){
        $("#konten-stockout-move").docoLoad({
            url: "/master/dashboard-odoo/stockout?type=move",
            dataType: "html",
            success: function(data){
            },
        })
    });
', View::POS_END, 'b-index');
?>