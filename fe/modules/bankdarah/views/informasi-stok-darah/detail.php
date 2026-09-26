<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"),
'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
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
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'excel' => [
                        'attributes' => [
                            "data-target" => $module."export-excel?id=".DocoHelpers::encrypt($id)."&"
                        ]
                    ],
                    'back'
                ]);?>
            </div>
            <div class="panel-body">
                <table id="example" class="table table-striped table-condensed table-hover" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?= \Yii::t('fe', "No. Kantong") ?></th>
                            <th><?= \Yii::t('fe', "Jenis Darah") ?></th>
                            <th><?= \Yii::t('fe', "Golongan Darah") ?></th>
                            <th><?= \Yii::t('fe', "Rhesus") ?></th>
                            <th><?= \Yii::t('fe', "Tanggal Kadaluarsa") ?></th>
                            <th><?= \Yii::t('fe', "Suhu Penyimpanan (C)") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8" class="text-center">Data tidak ditemukan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
 </div>

<?php
$this->registerJs("
    var _id = ".$id.";
    "
    .$this->render("js/infostokdarahdetail.js"), View::POS_END, 'js');
?>