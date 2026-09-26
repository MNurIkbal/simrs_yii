<?php

/**
 * @Author: Ardi
 * @Date:   2018-10-16 15:50
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\ArrayHelper;
?>

<style type="text/css">
    .modal-body {
    position: relative;
    padding-top: 5px !important;
}
</style>


<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Riwayat Operasi</h5>
</div>
<div class="modal-body">
	<div class="row">
		<div class="form-horizontal">
		    <div class="col-md-6">
		    	<div class="form-group">
		    		<label class="control-label col-sm-2">
		    			Nama Pasien
		    		</label>
		    		<div class="col-sm-8">
		      			<p class="form-control-static">: <?=@$infoPasien['nama_pasien']?></p>
		    		</div>
		    	</div>
		    </div>
		    <div class="col-md-6">
		    	<div class="form-group">
		    		<label class="control-label col-sm-2">
		    			Alamat Pasien
		    		</label>
		    		<div class="col-sm-8">
		      			<p class="form-control-static">: <?=@$infoPasien['alamat_pasien']?></p>
		    		</div>
		    	</div>
		    </div>
		</div>
	</div>
	<div class="row">
		<div class="form-horizontal">
		    <div class="col-md-6">
		    	<div class="form-group">
		    		<label class="control-label col-sm-2">
		    			Umur
		    		</label>
		    		<div class="col-sm-8">
		      			<p class="form-control-static">: <?=@$infoPasien['umur']?></p>
		    		</div>
		    	</div>
		    </div>
		    <div class="col-md-6">
		    	<div class="form-group">
		    		<label class="control-label col-sm-2">
		    			No RM
		    		</label>
		    		<div class="col-sm-8">
		      			<p class="form-control-static">: <?=@$infoPasien['no_rekam_medik']?></p>
		    		</div>
		    	</div>
		    </div>
		</div>
	</div>
	<div class="row">
		<div class="table-responsive">
			<table id="tabel-reseptur" class="table table-striped table-hover datatable-basic dataTable" style="width:100%;">
                <thead>
                    <tr class="bg-inverse">
                        <th>No</th>
                        <th><?=Yii::t('fe', 'No Rujukan')?></th>
                        <th><?=Yii::t('fe', 'No Operasi')?></th>
                        <th><?=Yii::t('fe', 'Tanggal Operasi')?></th>
                        <th><?=Yii::t('fe', 'Nama Prosedur')?></th>
                        <th><?=Yii::t('fe', 'Dokter Bedah')?></th>
                        <!-- <th><?php //Yii::t('fe', 'Status')?></th> -->
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                	<?php
                		$rowNum = 1;
                		foreach ($listBedah as $group_rujukan) {
                			$count_rujukan = count($group_rujukan);
                			$i_detail = 1;
                			foreach ($group_rujukan as $norujuk => $val_bedah) {
                	?>
                		<tr>
                			<?php 
                				$rowspan_rujukan = $count_rujukan;
                				if($i_detail == 1){
                			?>
                			<td rowspan="<?=$rowspan_rujukan?>"><?=$rowNum?></td>
                			<td rowspan="<?=$rowspan_rujukan?>"><?=@$val_bedah['no_rujukan']?></td>
                            <td rowspan="<?=$rowspan_rujukan?>" ><?=@$val_bedah['no_operasi']?></td>
                            <td rowspan="<?=$rowspan_rujukan?>"><?= date('d M Y H:i', strtotime($val_bedah['tgl_operasi']))?></td>
                            <?php }?>
                            <td><?=@$val_bedah['nama_prosedur']?></td>
                            <td><?=@$val_bedah['dok_operator']?></td>
                			<td>
                				<?php
                					if(isset($val_bedah['status_periksa'])){
										if($val_bedah['status_periksa'] == '483' || $val_bedah['status_periksa'] == '482'){
											$btn_kritis = 'btn-info';
											if(isset($val_bedah['is_hasilkritis']) && $val_bedah['is_hasilkritis'] == TRUE){
												$btn_kritis = 'btn-danger';
											}
	
											echo Html::button('Laporan Operasi',[
												'class'=>'btn '.$btn_kritis.' btn-sm btn-riwayat btn-cetak-penunjang',
												'data-url' => '/bedah/informasi-pasien-operasi/cetak?id='.@$val_bedah['pasienmasukpenunjang_id'].'&laporan_id='.@$val_bedah['laporanoperasi_id'],
												'disabled' => $val_bedah['status_periksa'] == '483' ? false : true ,
											]);
										}

	                				}
                				?>
                			</td>
                		</tr>
                	<?php
                			$i_detail++;
                			}
                		$rowNum++;
                		}
                	?>
                </tbody>
            </table>
		</div>
	</div>
</div>