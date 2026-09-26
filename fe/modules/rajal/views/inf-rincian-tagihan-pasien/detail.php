<?php

/**
 * @Author: Ayip
 * @Date:   2018-03-03 11:40:04
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-02-23 16:11:12
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Info Konsul Poli'), 'url' => ['/rajal/inf-konsul-poli']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>

                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
            	<?php echo DocoHelpers::generateToolbar([
	                    'kembali' => [
				            'type' => 'link',
				            'title' => \Yii::t('fe', 'Kembali'),
				            'icon' => 'fa fa-arrow-left',
				            'method' => 'not exist',
				            'attributes' => [
				                'class' => 'bg-slate data-kembali',
				                'href' => Url::home().('rajal/inf-konsul-poli'),
				            ]
				        ],
	                    'print',
                ]); ?>
            </div>

            <div id="printable" class="panel-body">
            <?php 
    		$form = ActiveForm::begin([
    			'id'=>'infRincianTagihanPasien-form',                    			
    			'options'=>[
    				'class'=>'form-horizontal',                    				
    			],
    			'enableClientValidation'=>false
    		]); ?>
            	<div class="row">
            		<div class="col-md-4">
            			<div class="form-group" style="margin-bottom: 0 !important;">
                    		<label class="control-label col-sm-4 text-black"><?=Yii::t('fe','Tanggal Pendaftaran')?></label>
                    		<div class="col-sm-8">
                    			<div class="form-control-static"><?php echo date('d F Y', strtotime($data[0]['tgl_pendaftaran'])); ?></div>
                    		</div>
                    	</div>
                    	<div class="form-group" style="margin-bottom: 0 !important;">
                    		<label class="control-label col-sm-4 text-black"><?=Yii::t('fe','No. Rekam Medik')?></label>
                    		<div class="col-sm-8">
                    			<div class="form-control-static"><?php echo $data[0]['no_rekam_medik']; ?></div>
                    		</div>
                    	</div>
                    	<div class="form-group" style="margin-bottom: 0 !important;">
                    		<label class="control-label col-sm-4 text-black"><?=Yii::t('fe','No. Pendaftaran')?></label>
                    		<div class="col-sm-8">
                    			<div class="form-control-static"><?php echo $data[0]['no_pendaftaran']; ?></div>
                    		</div>
                    	</div>
                    	<div class="form-group" style="margin-bottom: 0 !important;">
                    		<label class="control-label col-sm-4 text-black"><?=Yii::t('fe','Nama Pasien')?></label>
                    		<div class="col-sm-8">
                    			<div class="form-control-static"><?php echo $data[0]['nama_pasien']; ?></div>
                    		</div>
                    	</div>
            		</div> 
            		<div class="col-md-4">
	            		<div class="form-group" style="margin-bottom: 0 !important;">
                    		<label class="control-label col-sm-4 text-black"><?=Yii::t('fe','Cara Bayar')?></label>
                    		<div class="col-sm-8">
                    			<div class="form-control-static"><?php echo $data[0]['carabayar_nama']; ?></div>
                    		</div>
                    	</div>
                    	<div class="form-group" style="margin-bottom: 0 !important;">
                    		<label class="control-label col-sm-4 text-black"><?=Yii::t('fe','Jenis Kasus Penyakit')?></label>
                    		<div class="col-sm-8">
                    			<div class="form-control-static"><?php echo $data[0]['jeniskasuspenyakit_nama']; ?></div>
                    		</div>
                    	</div>
                    	<div class="form-group" style="margin-bottom: 0 !important;">
                    		<label class="control-label col-sm-4 text-black"><?=Yii::t('fe','Dokter')?></label>
                    		<div class="col-sm-8">
                    			<div class="form-control-static"><?php echo $data[0]['nama_pegawai']; ?></div>
                    		</div>
                    	</div>
                    	<div class="form-group" style="margin-bottom: 0 !important;">
                    		<label class="control-label col-sm-4 text-black"><?=Yii::t('fe','Ruangan')?></label>
                    		<div class="col-sm-8">
                    			<div class="form-control-static"><?php echo $data[0]['ruangan_nama']; ?></div>
                    		</div>
                    	</div>
	            	</div>
	            	<div class="col-md-4">
	            		<div class="form-group" style="margin-bottom: 0 !important;">
                    		<label class="control-label col-sm-4 text-black"><?=Yii::t('fe','Penjamin')?></label>
                    		<div class="col-sm-8">
                    			<div class="form-control-static"><?php echo $data[0]['penjamin_nama']; ?></div>
                    		</div>
                    	</div>
                    	<div class="form-group" style="margin-bottom: 0 !important;">
                    		<label class="control-label col-sm-4 text-black"><?=Yii::t('fe','Kelas Pelayanan')?></label>
                    		<div class="col-sm-8">
                    			<div class="form-control-static"><?php echo $data[0]['kelaspelayanan_nama']; ?></div>
                    		</div>
                    	</div>
                    	<div class="form-group" style="margin-bottom: 0 !important;">
                    		<label class="control-label col-sm-4 text-black"><?=Yii::t('fe','Status Bayar')?></label>
                    		<div class="col-sm-8">
                    			<div class="form-control-static"><?php echo $data[0]['statusbayar_nama']; ?></div>
                    		</div>
                    	</div>
	            	</div>
            	</div>

            	<hr>

            	<div class="panel">
            		<div class="panel-heading bg-inverse">
						<h5 class="panel-title">Riwayat Pembayaran</h5>
					</div>
            		<table id="tbl-riwayat-pembayaran
            		" class="table table-striped table-condensed table-hover" style="width:100%">
	                    <tbody>
	                    	<tr>
	                    		<td><?=\Yii::t("fe", "Total Tagihan");?></td>
	                    	</tr>
	                    	<tr>
	                    		<td><?=\Yii::t("fe", "Total Uang Muka");?></td>
	                    	</tr>
	                    	<tr>
	                    		<td><?=\Yii::t("fe", "Total Sudah Dibayarkan");?></td>
	                    	</tr>
	                    	<tr>
	                    		<td><?=\Yii::t("fe", "Total Sisa Tagihan");?></td>
	                    	</tr>
	                    </tbody>
	                </table>
            	</div>

            	<?php foreach($dataPerRuangan as $key => $value): ?>
        		<div class="panel">
            		<div class="panel-heading bg-inverse">
						<h5 class="panel-title"><?php echo $value; ?></h5>
					</div>

					<?php 
						$filterBy = $value; // or Finance etc.

						$filteredData = array_filter($data, function ($var) use ($filterBy) {
						    return ($var['ruangan_nama'] == $filterBy);
						});
					?>

					<?php if($key == '1'): ?>
						<table id="tbl-<?php echo $key; ?>" class="table table-striped table-condensed table-hover" style="width:100%;">
			                <thead>
		                        <tr>
		                            <th width="20">No</th>
		                            <th><?=\Yii::t("fe", "Tanggal Tindakan");?></th>
		                            <th><?=\Yii::t("fe", "Nama Tindakan");?></th>
		                            <th><?=\Yii::t("fe", "Qty");?></th>
		                            <th><?=\Yii::t("fe", "Tarif Satuan");?></th>
		                            <th><?=\Yii::t("fe", "Tarif Cyto");?></th>
		                            <th><?=\Yii::t("fe", "Jumlah Tarif");?></th>
		                        </tr>
		                    </thead>
		                    <tbody>
		                    <?php 
		                    $i = 1;
		                    foreach($filteredData as $row): ?>
		                    	<tr>
		                    		<td><?=$i;?></td>
		                    		<td><?=date('d F Y H:i:s', strtotime($row['tgl_tindakan'])); ?></td>
		                    		<td><?=$row['daftartindakan_nama']; ?></td>
		                    		<td><?=$row['qty_tindakan']; ?></td>
		                    		<td><?=$row['tarif_satuan']; ?></td>
		                    		<td><?=$row['tarifcyto_tindakan']; ?></td>
		                    		<td><?=$row['tarif_tindakan']; ?></td>
		                    	</tr>
	                    	<?php $i++; endforeach; ?>
		                    </tbody>
		                </table>
		            <?php endif; ?>

					<?php if($key == '6'): ?>
						<table id="tbl-<?php echo $key; ?>" class="table table-striped table-condensed table-hover" style="width:100%">
			                <thead>
		                        <tr class="bg-inverse">
		                            <th width="20">No</th>
		                            <th><?=\Yii::t("fe", "Tanggal Order Obat");?></th>
		                            <th><?=\Yii::t("fe", "Nama Obat");?></th>
		                            <th><?=\Yii::t("fe", "Qty");?></th>
		                            <th><?=\Yii::t("fe", "Tarif Satuan");?></th>
		                            <th><?=\Yii::t("fe", "Jumlah Tarif");?></th>
		                        </tr>
		                    </thead>
		                    <tbody>
		                    <?php 
		                    $i = 1;
		                    foreach($filteredData as $row): ?>
		                    	<tr>
		                    		<td><?=$i;?></td>
		                    		<td><?=date('d F Y H:i:s', $row['tgl_tindakan']); ?></td>
		                    		<td><?=$row['daftartindakan_nama']; ?></td>
		                    		<td><?=$row['qty_tindakan']; ?></td>
		                    		<td><?=$row['tarif_satuan']; ?></td>
		                    		<td><?=$row['tarif_tindakan']; ?></td>
		                    	</tr>
	                    	<?php $i++; endforeach; ?>
		                    </tbody>
		                </table>
		            <?php endif; ?>

		            <?php if($key == '5' || $key == '4'): ?>
		            	<table id="tbl-<?php echo $key; ?>" class="table table-striped table-condensed table-hover" style="width:100%">
			                <thead>
		                        <tr class="bg-inverse">
		                            <th width="20">No</th>
		                            <th><?=\Yii::t("fe", "Tanggal Pemeriksaan");?></th>
		                            <th><?=\Yii::t("fe", "Jenis Pemeriksaan");?></th>
		                            <th><?=\Yii::t("fe", "Nama Pemeriksaan");?></th>
		                            <th><?=\Yii::t("fe", "Tarif Satuan");?></th>
		                            <th><?=\Yii::t("fe", "Cyto");?>?</th>
		                            <th><?=\Yii::t("fe", "Tarif Satuan Cyto");?></th>
		                            <th><?=\Yii::t("fe", "Qty");?></th>
		                            <th><?=\Yii::t("fe", "Jumlah");?></th>
		                        </tr>
		                    </thead>
		                    <tbody>
		                    <?php 
		                    $i = 1;
		                    foreach($filteredData as $row): ?>
		                    	<tr>
		                    		<td><?=$i;?></td>
		                    		<td><?=date('d F Y H:i:s', $row['tgl_tindakan']); ?></td>
		                    		<td>(Nunggu di join)</td>
		                    		<td><?=$row['daftartindakan_nama']; ?></td>
		                    		<td><?=$row['tarif_satuan']; ?></td>
		                    		<td><?=$row['cyto_tindakan']; ?></td>
		                    		<td><?=$row['tarifcyto_tindakan']; ?></td>
		                    		<td><?=$row['qty_tindakan']; ?></td>
		                    		<td><?=$row['tarif_tindakan']; ?></td>
		                    	</tr>
	                    	<?php $i++; endforeach; ?>
		                    </tbody>
		                </table>
					<?php endif; ?>

            		
            	</div>
            	<?php endforeach; ?>

            <?php ActiveForm::end() ?>
            </div>

        </div>
    </div>
</div>