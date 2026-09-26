<?php

use yii\widgets\Breadcrumbs;
use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;

$this->title = $title;

$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'DCMS'), 'url' => ['/dcms/dashboard']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading" >
                 <!-- breadcrumbs replace with this -->
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
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
            <br>
                <?php 
                    foreach( $listInstalasi as $key => $item) {
                        ?>
                            <div class="col-md-3">
                                <div class="panel panel-default">
                                    <div class="panel-heading text-center">
                                        <h5><?=$item['instalasi_nama']?></h5>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h3 id="<?= $slug[$item['instalasi_id']] ?>">0</h3>
                                    </div>
                                </div>
                            </div>
                        <?php
                    }
                ?>
            </div>
        </div>
    </div>
</div>

<?php 
    $this->registerJs($this->render('index.js'), View::POS_END);
?>