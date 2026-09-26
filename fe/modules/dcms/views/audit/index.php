<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = Yii::t('fe', 'Log Audit');
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
                    <li class="active" id="tabs-log"><a href="#view-log" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'Audit Trail')?></a></li>
                    <li class="" id="tabs-useract"><a href="#view-useract" data-toggle="tab" aria-expanded="true"><?=Yii::t('fe', 'User Action')?></a></li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="view-log">
                    <div id="konten-log" class="konten-tab"></div>
                </div>
                <div class="tab-pane" id="view-useract">
                    <div id="konten-useract" class="konten-tab"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    $(document).ready(function(){
        $(".konten-tab").html("");
        $("#konten-log").docoLoad({
            url: "/dcms/audit/load?view=log",
            dataType: "html",
            success: function(data){
                
            },
        })
    });

    $("#tabs-log").on("click", function(e){
        $(".konten-tab").html("");
        $("#konten-log").docoLoad({
            url: "/dcms/audit/load?view=log",
            dataType: "html",
            success: function(data){
                
            },
        })
    });

    $("#tabs-useract").on("click", function(e){
        $(".konten-tab").html("");
        $("#konten-useract").docoLoad({
            url: "/dcms/audit/load?view=useract",
            dataType: "html",
            success: function(data){
                
            },
        })
    });
', View::POS_END, 'b-index');
?>