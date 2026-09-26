<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-17 17:49:19
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-17 18:01:10
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$title = "Detail Pemesanan Obat Alkes";

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body" style="margin-top: -20px">        
	<h5 class="text-center">Pemesanan Obat Alkes</h5>
	<h5 class="text-center">Ruangan Apotek Farmasi</h5>
    <div class="row">    	
    	<div class="col-md-6">
			<table class="table"> 
    			<tr >
    				<td style="border: 0">Tanggal Pemesanan</td>
    				<td style="border: 0"></td>
    			</tr>
    			<tr>
    				<td style="border: 0">Tanggal Minta Dikirim</td>
    				<td style="border: 0"></td>
    			</tr>
    		</table>		
		</div>
		<div class="col-md-6">
			<table class="table"> 
    			<tr>
    				<td style="border: 0">Nomor Pemesanan</td>
    				<td style="border: 0"></td>
    			</tr>
    			<tr>
    				<td style="border: 0">Ruangan Tujuan Pemesanan</td>
    				<td style="border: 0"></td>
    			</tr>
    		</table>		
		</div>
    </div>
    <div class="row">
    	<table id="pemesanan-list" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th>Nama Obat Alkes</th>
                <th>Qty Pemesanan</th>                
            </tr>
        </thead>
        <tbody>
        	<tr>
        		<td>1</td>
        		<td>Paracetamol</td>
        		<td>10</td>        		
        	</tr>
            <!-- <tr>
                <td class="text-center" colspan="3"></td>
            </tr> -->
        </tbody>
    </table>
    </div>
</div>
