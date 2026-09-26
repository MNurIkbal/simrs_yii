<?php

/**
 * @Author: Ardi
 * @Date:   2018-10-11 13:10
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
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Riwayat Reseptur</h5>
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
                        <th><?=Yii::t('fe', 'No Resep')?></th>
                        <th><?=Yii::t('fe', 'Racikan / non racikan')?></th>
                        <th><?=Yii::t('fe', 'R ke-')?></th>
                        <th><?=Yii::t('fe', 'Nama obat')?></th>
                        <th><?=Yii::t('fe', 'Satuan kecil')?></th>
                        <th><?=Yii::t('fe', 'Signa')?></th>
                        <th><?=Yii::t('fe', 'Qty')?></th>
                        <th><?=Yii::t('fe', 'Status')?></th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                	<?php
                		$rowNum = 1;
                		foreach ($listReseptur as $group_reseptur) {
                			$count_resep = count($group_reseptur);
                			$i_detail = 1;
                			foreach ($group_reseptur as $idresep => $val_reseptur) {
                	?>
                		<tr>
                			<?php 
                				$rowspan_resep = $count_resep;
                				if($i_detail == 1){
                			?>
                			<td rowspan="<?=$rowspan_resep?>"><?=$rowNum?></td>
                			<td rowspan="<?=$rowspan_resep?>"><?=@$val_reseptur['noresep']?></td>
                			<?php }?>
                			<td><?=@$val_reseptur['racikan_nama']?></td>
                			<td><?=@$val_reseptur['rke']?></td>
                			<td><?=@$val_reseptur['obatalkes_nama']?></td>
                			<td><?=@$val_reseptur['satuan_kecil']?></td>
                			<td><?=isset($val_reseptur['signa']) && !empty($val_reseptur['signa']) ? @$val_reseptur['signa']['text'] : @$val_reseptur['signa_nama']?></td>
                			<td><?=@$val_reseptur['qty_reseptur']?></td>
                			<?php if($i_detail == 1){?>
                			<td rowspan="<?=$rowspan_resep?>"><?=@$val_reseptur['status_reseptur']?></td>
                			<td rowspan="<?=$rowspan_resep?>">
                				<?php
                					if ($ins == 3) {
                                        echo Html::button('Cetak',[
                                                'class'=>'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan btn-cetak-resep',
                                                'data-url' => '/apotek/informasi-reseptur/print-resep-detail?id='.DocoHelpers::encrypt(@$val_reseptur['reseptur_id']).'&nomor='.DocoHelpers::encrypt(@$val_reseptur['noresep']),
                                            ]
                                        );
                					} else {
							            echo Html::button('Cetak',[
                                                'class'=>'btn btn-info btn-sm btn-riwayat btn-cetak-pelayanan btn-cetak-resep',
                                                'data-url' => '/apotek/informasi-reseptur/print-resep-detail?id='.DocoHelpers::encrypt(@$val_reseptur['reseptur_id']).'&nomor='.DocoHelpers::encrypt(@$val_reseptur['noresep']),
                                            ]
                                        );
                					}
                				?>
                			</td>
                			<?php }?>
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