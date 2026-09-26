<?php

/**
 * @Author: afil
 * @Date:   2018-01-16 17:31:57
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-01 09:14:35
 * @Description: 
 */

use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
?>


<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title"><?=$title?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    
    <div class="panel-body table-responsive">
        <?php
            $form = ActiveForm::begin([
                'id' => 'form-penunjang',
                'enableAjaxValidation'=>false,
                'enableClientValidation'=>false,
                // 'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]);
        ?>
        <table 
            class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" 
            id="tabel-r" style="width: 100% background-color:red">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th><?=Yii::t('fe', 'Instalasi - Ruangan')?></th>
                    <th><?=Yii::t('fe', 'Paket / Tindakan')?></th>
                    <th><?=Yii::t('fe', 'Status')?></th>
                </tr>
            </thead>
            <tbody> 
            </tbody>
        </table>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$this->registerJs('
var pend_id = ' . $pendaftaran_id . ';
', View::POS_END);
$this->registerJs($this->render('js/_penunjang.js'), View::POS_END);
?>