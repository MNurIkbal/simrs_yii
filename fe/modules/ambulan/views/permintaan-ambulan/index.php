<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-08-10 11:15:12
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-01-11 10:36:27
 */
use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"), 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'custom-save' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-floppy-o',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'simpan'
                            ],
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body" style="min-height: 400px;">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title"><b><?= $title ?></b></h6>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-12" style="overflow-x:auto;">
		                    <?=Yii::$app->controller->renderPartial('ambulan_tab', [
		                    ]);?>
		                </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs($this->render('/assets/js/_script.js'), View::POS_END);
?>