<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-19 10:48:15
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-19 10:50:26
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$title = "Obat Alkes";

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">        
    <table id="obat-list" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th><?=\Yii::t("fe", "Nama obat alkes");?></th>
                <th><?=\Yii::t("fe", "kode obat alkes");?></th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        	<tr>
        		<td>1</td>
        		<td>Paracetamol</td>
        		<td>XTCBGAZ11</td>
        		<td><a href="#" class="btn btn-success"><i class="fa fa-lg fa-check-square-o"></i></a></td>
        	</tr>
            <!-- <tr>
                <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
            </tr> -->
        </tbody>
    </table>
</div>