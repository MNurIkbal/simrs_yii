<?php

/**
 * @author Budi
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Laboratorium'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body">
                <div class="form-group">
                    <div class="col-md-12" style="overflow-x:auto;">
                        <?=Yii::$app->controller->renderPartial('tab_informasi', []);?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
    $this->registerJs('
    var baseController = "/laboratorium/inf-pasien-rujukan-lab/";
    ', View::POS_END);
    $this->registerJs($this->render('tab_informasi.js'), View::POS_END);
?>