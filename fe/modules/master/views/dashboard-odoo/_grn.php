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

$this->title = Yii::t('fe', 'GRN');
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
                    <li class="active" id="tabs-grn-purchase"><a href="#view-grn-purchase" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'GRN Receipt (purchase.order)')?></a></li>
                    <li class="" id="tabs-grn-purchasedetail"><a href="#view-grn-purchasedetail" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'GRN Receipt Detail (purchase.order.line)')?></a></li>
                    <li class="" id="tabs-grn-purchaseretur"><a href="#view-grn-purchaseretur" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'GRN Issue (purchase.order)')?></a></li>
                    <li class="" id="tabs-grn-purchasereturdetail"><a href="#view-grn-purchasereturdetail" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'GRN Issue Detail (purchase.order.line)')?></a></li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="view-grn-purchase">
                    <div id="konten-grn-purchase"></div>
                </div>
                <div class="tab-pane" id="view-grn-purchasedetail">
                    <div id="konten-grn-purchasedetail"></div>
                </div>
                <div class="tab-pane active" id="view-grn-purchaseretur">
                    <div id="konten-grn-purchaseretur"></div>
                </div>
                <div class="tab-pane" id="view-grn-purchasereturdetail">
                    <div id="konten-grn-purchasereturdetail"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    $(document).ready(function(){
        $("#konten-grn-purchase").docoLoad({
            url: "/master/dashboard-odoo/grn?type=purchase",
            dataType: "html",
            success: function(data){
                
            },
        })
    });

    $("#tabs-grn-purchasedetail").on("click", function(e){
        $("#konten-grn-purchasedetail").docoLoad({
            url: "/master/dashboard-odoo/grn?type=purchasedetail",
            dataType: "html",
            success: function(data){
            },
        })
    });

    $("#tabs-grn-purchaseretur").on("click", function(e){
        $("#konten-grn-purchaseretur").docoLoad({
            url: "/master/dashboard-odoo/grn?type=purchaseretur",
            dataType: "html",
            success: function(data){
            },
        })
    });

    $("#tabs-grn-purchasereturdetail").on("click", function(e){
        $("#konten-grn-purchasereturdetail").docoLoad({
            url: "/master/dashboard-odoo/grn?type=purchasereturdetail",
            dataType: "html",
            success: function(data){
            },
        })
    });
', View::POS_END, 'b-index');
?>