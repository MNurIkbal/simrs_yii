<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-18 17:00:09
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-18 17:00:59
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$title = "List Obat";

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
                <th>NIP</th>
                <th>Nama Pegawai</th>
                <th>Jabatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        	<tr>
        		<td>1</td>
        		<td>19982270</td>
        		<td>Febri</td>
        		<td>Ketua</td>
        		<td><a href="#" class="btn btn-success"><i class="fa fa-lg fa-check-square-o"></i></a></td>
        	</tr>
            <!-- <tr>
                <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
            </tr> -->
        </tbody>
    </table>
</div>