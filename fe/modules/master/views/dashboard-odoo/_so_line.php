<?php

/**
 * @Author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = Yii::t('fe', 'Sale Order Line');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                    <li class="active" id="tabs-tindakan"><a href="#view-tindakan" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Tindakan')?></a></li>
                    <li class="" id="tabs-obat"><a href="#view-obat" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Obat')?></a></li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="view-tindakan">
                    <div id="konten-tindakan"></div>
                </div>
                <div class="tab-pane" id="view-obat">
                    <div id="konten-obat"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    $(document).ready(function(){
        $("#konten-tindakan").docoLoad({
            url: "/master/dashboard-odoo/sale-order-line?type=saleorderline",
            dataType: "html",
            success: function(data){
                
            },
        })
    });

    $("#tabs-obat").on("click", function(e){
        $("#konten-obat").docoLoad({
            url: "/master/dashboard-odoo/sale-order-line?type=obatalkespasien",
            dataType: "html",
            success: function(data){
                
            },
        })
    });
', View::POS_END, 'b-index');
?>