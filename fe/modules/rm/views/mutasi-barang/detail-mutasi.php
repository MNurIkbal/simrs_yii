<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-18 13:45:59
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-18 13:58:00
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

$title = "Detail Mutasi";

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body" style="margin-top: -20px">        
	<h5 class="text-center">Mutasi Barang</h5>
	<h5 class="text-center">Periode 12-10-2017</h5>
    <div class="row">    	
    	<div class="col-md-6">
			<table class="table"> 
    			<tr >
    				<td style="border: 0">Tanggal Mutasi</td>
    				<td style="border: 0"></td>
    			</tr>
    			<tr>
    				<td style="border: 0">Nomor Mutasi</td>
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
    	<table id="pemesanan-list" class="table table-bordered table-striped table-condensed table-hover" style="width:100%">
	        <thead>
	            <tr class="bg-inverse">
	                <th width="1">No</th>
	                <th>Nama Barang</th>
	                <th>Qty Mutasi</th>                
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
    <div class="row">    	
    	<br><br>
        <table width="100%">
        	<tr>
        		<td class="text-center">Pegawai Mutasi</td>
        		<td class="text-center">Pegawai Mengetahui</td>
        	</tr>
        	<tr>
        		<td class="text-center">
        			<br><br><br>
        			Febriyanto M Rais
        		</td>
        		<td class="text-center">
        			<br><br><br>
        			Febri
        		</td>
        	</tr>
        </table>
    </div>
</div>
