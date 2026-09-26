<?php

    /**
     * @author Chacha Nurholis (chacha@sirs.co.id)
     * A Product of PT Citraraya Nusatama
     * Powered by Sirs
     */

    use yii\helpers\Html;
    use app\components\DocoHelpers;
?>
<style type="text/css">
    .modal-body {
        position: relative;
        padding-top: 5px !important;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Riwayat Radiologi</h5>
</div>
<div class="modal-body">
	<div class="row">
		<div class="form-horizontal">
		    <div class="col-md-6">
		    	<div class="form-group">
		    		<label class="control-label text-bold col-sm-3">
		    			Nama Pasien
		    		</label>
		    		<div class="col-sm-8">
		      			<p class="form-control-static">: <?= @$infoPasien['nama_pasien'] ?></p>
		    		</div>
		    	</div>
		    	<div class="form-group">
		    		<label class="control-label text-bold col-sm-3">
		    			Umur / Jenis Kelamin
		    		</label>
		    		<div class="col-sm-8">
		      			<p class="form-control-static">: <?= @$infoPasien['umur']?> / <?=@$infoPasien['jenis_kelamin'] ?></p>
		    		</div>
		    	</div>
		    	<div class="form-group">
		    		<label class="control-label text-bold col-sm-3">
		    			No. Bill
		    		</label>
		    		<div class="col-sm-8">
		      			<p class="form-control-static">: -</p>
		    		</div>
		    	</div>
		    	<div class="form-group">
		    		<label class="control-label text-bold col-sm-3">
		    			Nama Penjamin
		    		</label>
		    		<div class="col-sm-8">
		      			<p class="form-control-static">: <?= @$infoPasien['penjamin_nama'] ?></p>
		    		</div>
		    	</div>
		    </div>
		    <div class="col-md-6">
		    	<div class="form-group">
		    		<label class="control-label text-bold col-sm-3">
		    			Nomor Telepon Pasien
		    		</label>
		    		<div class="col-sm-8">
		      			<p class="form-control-static">: <?= @$infoPasien['no_telepon_pasien'] ?></p>
		    		</div>
		    	</div>
		    	<div class="form-group">
		    		<label class="control-label text-bold col-sm-3">
		    			Nomor Rekam Medik
		    		</label>
		    		<div class="col-sm-8">
		      			<p class="form-control-static">: <?= @$infoPasien['no_rekam_medik'] ?></p>
		    		</div>
		    	</div>
		    	<div class="form-group">
		    		<label class="control-label text-bold col-sm-3">
		    			Ruangan
		    		</label>
		    		<div class="col-sm-8">
		      			<p class="form-control-static">: <?= @$infoPasien['ruangan_nama'] ?></p>
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
                        <th><?= Yii::t('fe', 'No') ?></th>
                        <th><?= Yii::t('fe', 'No Rujukan') ?></th>
                        <th><?= Yii::t('fe', 'Tanggal Pemeriksaan') ?></th>
                        <th><?= Yii::t('fe', 'Nama Pemeriksaan') ?></th>
                        <th><?= Yii::t('fe', 'Status') ?></th>
                        <th><?= Yii::t('fe', 'Aksi') ?></th>
                    </tr>
                </thead>
                <tbody>
                	<?php
                		$rowNum = 1;
                		foreach ($listRad as $group_rujukan) :
                			$count_rujukan = count($group_rujukan);
                			$i_detail = 1;
                			foreach ($group_rujukan as $norujuk => $val_rad) :
                                $penunjang_id = DocoHelpers::encrypt($val_rad['pasienmasukpenunjang_id']);
                                $hasilpemeriksaanrad_id = DocoHelpers::encrypt($val_rad['hasilpemeriksaanrad_id']);
                                $folderNames = $hasilpemeriksaanrad_id.'-'.$penunjang_id;

                    ?>
                		<tr>
                			<?php 
                				$rowspan_rujukan = $count_rujukan;
                				if ($i_detail == 1) :
                			?>
                                <td rowspan="<?= $rowspan_rujukan ?>"><?= $rowNum ?></td>
                                <td rowspan="<?= $rowspan_rujukan ?>"><?= @$val_rad['no_rujukan'] ?></td>
                                <td rowspan="<?= $rowspan_rujukan ?>"><?= DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($val_rad['tglmasukpenunjang'])), false, true) ?></td>
                			<?php endif ?>
                			<td><?= @$val_rad['daftartindakan_nama'] ?></td>
                			<?php 
                                if ($i_detail == 1) :
                			        $status_dipakai = '-';
                                    if (!is_null($val_rad['stat_penunjang'])) {
                                        $status_dipakai = @$val_rad['stat_penunjang'];
                                    } else {
                                        $status_dipakai = @$val_rad['stat_periksa'];
                                    }
                			?>
                			<td rowspan="<?= $rowspan_rujukan ?>"><?= $status_dipakai ?></td>
							<?php endif ?>
                			<td>
                				<?php
                					if (isset($val_rad['status_periksa']) && $val_rad['status_periksa'] == '475') {
                						$btn_kritis = 'btn-info';
                						if (isset($val_rad['is_hasilkritis']) && $val_rad['is_hasilkritis'] == TRUE) {
                							$btn_kritis = 'btn-danger';
                						}
	                					echo Html::button('Lihat Expertise', [
											'class'          => 'btn '.$btn_kritis.' btn-sm btn-riwayat btn-cetak-penunjang',
											'data-norujukan' => @$val_rad['no_rujukan'],
											'data-url'       => '/radiologi/hasil-rad/cetak-hasil?id=' . DocoHelpers::encrypt($val_rad['daftartindakan_id']) .'&tindakan_id=' . DocoHelpers::encrypt($val_rad['tindakanpelayanan_id']) . '&penunjang_id=' . DocoHelpers::encrypt($val_rad['pasienmasukpenunjang_id'])
										]);
                                        $alias                  = Yii::getAlias("@media");
                                        $penunjang_id           = DocoHelpers::encrypt($val_rad['pasienmasukpenunjang_id']);
                                        $hasilpemeriksaanrad_id = DocoHelpers::encrypt($val_rad['hasilpemeriksaanrad_id']);
                                        $folderName             = $hasilpemeriksaanrad_id.'-'.$penunjang_id;
                                        $prefix                 = '/input-hasil-rad/';
                                        $expl_1                 = explode('_', $val_rad['penunjang_pemeriksaanrad']);
                                        $res_data               = [];
                                        $res_folder             = false;
                                        foreach ($expl_1 as $key => $value) {
                                            if ($value != '' ) {
                                                if (file_exists($alias.$prefix.$value.'/') ) {
                                                    $res_folder = true;
                                                }
                                                $res_data[] = $value;
                                            }
                                        }
                                        if ($res_folder) {
                                            echo Html::a('Unduh foto',
    							                [
    							                    '/radiologi/expertise/all-unduh-hasil?folder='.json_encode($res_data).'&target='.$val_rad['no_rujukan']
    							                ], [
    							                    'class'=>'btn '.$btn_kritis.' btn-sm btn-riwayat btn-unduh-foto',
                                                    'target' => '_blank',
    							                ]
    							            );
                                        }
	                				}
                				?>
							</td>
                		</tr>
                	<?php
                        $i_detail++;
                        $rowNum++;
                        endforeach;
                        endforeach;
                	?>
                </tbody>
            </table>
		</div>
	</div>
</div>