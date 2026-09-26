<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-08 09:33:20
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-02-08 10:10:40
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use Doco\components\DocoHelpers;

?>
<table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
	<thead>
	    <tr class="bg-inverse">
	        <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
	        <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
	        <th><?=\Yii::t("fe", "No batch")?></th>
	        <th><?=\Yii::t("fe", "Stok Sistem");?></th>
	        <th><?=\Yii::t("fe", "Stok Fisik");?></th>
	        <th><?=\Yii::t("fe", "Kondisi");?></th>
	        <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
	    </tr>
	</thead>
	<tbody>
	    <tr>
	        <td colspan="7" class="text-center">Data tidak tersedia</td>
	    </tr>
	</tbody>
</table>
