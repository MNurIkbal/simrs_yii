<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-08 17:09:50
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-05 10:42:09
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Bedah sentral', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Informasi pasien operasi', 'url' => ['informasi-pasien-operasi']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .datepicker>div{
        display:none;
    }
</style>
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
                                <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                                <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                        </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <div <?php echo $display ?> >
                    <?= DocoHelpers::generateToolbar([
                        // 'search',
                        'back' 
                    ]) ?>
                </div>
            </div>
            <div class="panel-body">
               <!-- pannel detail pasien -->
               <div class="col-md-12">
                    <?=$this->render('detail-partial/_infopasien', ['data'=>$data])?>
               </div>
               <!-- pannel detail pasien -->

               <div class="col-md-12">
                    <?=$this->render('detail-partial/_tab', ['data'=>$data, 'posisi'=>$posisi, 'status'=>$status])?>
               </div>
            </div>
        </div>
    </div>
</div>