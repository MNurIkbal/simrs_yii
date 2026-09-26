<?php
// Author : Dede Herdiana

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\web\View;
use app\components\DocoHelpers;
use app\components\DHtml;

$this->title = DHtml::getTitleMenu('Tarif Akomodasi Bedah');
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
                    <div class="tab-content">
                        <div class="tab-pane active" id="view-tarif">
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
    ', View::POS_END, 'b-index');
    $this->registerJs($this->render('js/index.js'), View::POS_END);
?>
