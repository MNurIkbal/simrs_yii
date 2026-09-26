<?php
// Author : Ardi Pratama

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\web\View;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Tarif Tindakan');
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
                        <!-- <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li> -->
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="tabbable">
                    <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                        <li class="active" id="tab-komponen"><a href="#view-komponen" data-toggle="tab" aria-expanded="true"><strong><?=Yii::t('fe', 'Komponen')?></strong></a></li>
                        <li class="" id="tab-perda"><a href="#view-perda" data-toggle="tab" aria-expanded="true"><strong><?=Yii::t('fe', 'Perda')?></strong></a></li>
                        <li class="" id="tab-tarif"><a href="#view-tarif" data-toggle="tab" aria-expanded="true"><strong><?=Yii::t('fe', 'Tarif')?></strong></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="view-komponen">
                            <div id="content-komponen" >  </div>
                        </div>
                        <div class="tab-pane" id="view-perda">
                            <div id="content-perda" >  </div>
                        </div>
                        <div class="tab-pane" id="view-tarif">
                            <div id="content-tarif" >  </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
 
<?php 
    $this->registerJs('

        // $.ajax({
        //     type: "GET",
        //     url: "/master/identitas/cara-masuk",
        //     dataType: "html",
        //     contentType: "application/html; charset=utf-8",
        //     success: function(res){
        //      $("#kontenCaraMasuk").html(res)     
        //     },
        // });

        // $.ajax({
        //     type: "GET",
        //     url: "/master/identitas/gol-umur",
        //     dataType: "html",
        //     contentType: "application/html; charset=utf-8",
        //     success: function(res){
        //      $("#kontenGolonganUmur").html(res)     
        //     },
        // });
       
    ', View::POS_END, 'b-index');
    $this->registerJs($this->render('js/index.js'), View::POS_END);
?>
