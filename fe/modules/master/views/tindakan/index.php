<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Tindakan Tabular
 * @copyright 26 April 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Master'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?= 
                    Breadcrumbs::widget([
                        'homeLink' => [ 
                            'label' => Yii::t('yii', 'Home'),
                            'url' => Yii::$app->homeUrl,
                        ],
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]); 
                ?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body">

                <div class="form-group">
                    <div class="col-md-12" style="overflow-x:auto;">
                        <!-- tab start -->
                        <?=Yii::$app->controller->renderPartial('tindakan_tab', [
                            // params variable
                        ]);?>
                        <!-- tab end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
    $this->registerJs('
    ', View::POS_END);
    $this->registerJs($this->render('/assets/js/tindakan.js'), View::POS_END);
?>