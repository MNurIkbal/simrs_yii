<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-11 14:35:17
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-12 14:03:36
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;
?>

<div class="panel panel-white">
	<div class="panel-heading">
		<h3 class="panel-title"><?=Yii::t('fe','10 Pasien terakhir yang mendaftar')?></h3>
		<div class="heading-elements">
            <ul class="icons-list">                        
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
	</div>
	<div class="panel-body">
		<table id="tbl-pasien-daftar" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
                <tr class="bg-inverse">                                    
                	<th><?=\Yii::t("fe", "No");?></th>
                    <th><?=\Yii::t("fe", "Tanggal pendaftaran");?></th>
                    <th><?=\Yii::t("fe", "No pendaftaran");?></th>
                    <th><?=\Yii::t("fe", "No rekam medik");?></th>
                    <th><?=\Yii::t("fe", "Nama pasien");?></th>
                    <th><?=\Yii::t("fe", "Umur");?></th>
                    <th><?=\Yii::t("fe", "Jenis kelamin");?></th>
                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                    <th><?=\Yii::t("fe", "Dokter");?></th>
                    <th><?=\Yii::t("fe", "Cara bayar");?></th>
                    <th><?=\Yii::t("fe", "Penjamin");?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                </tr>
            </tbody>
        </table>
	</div>
</div>		