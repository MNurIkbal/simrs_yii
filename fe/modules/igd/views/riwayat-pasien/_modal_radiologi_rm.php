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

<style type="text/css">
    .modal-body {
    position: relative;
    padding-top: 5px !important;
}
	.not-read {
		background-color : #FF5722;
	}

	.btn-hasil{
		margin-bottom: 3px;
	}

	.btn-hasil{
		margin-bottom: 3px;
		width: 100px;
	}
</style>


<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Riwayat Radiologi - <?= ArrayHelper::getValue($infoPasien, 'nama_pasien', '-') . ' / ' . ArrayHelper::getValue($infoPasien, 'penjamin_nama', '-') . ' / ' . ArrayHelper::getValue($infoPasien, 'kelaspelayanan_nama', '-')?></h5>
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
		      			<p class="form-control-static">: <?=@$infoPasien['nama_pasien']?></p>
		    		</div>
		    	</div>
		    	<div class="form-group">
		    		<label class="control-label text-bold col-sm-3">
		    			Umur / Jenis Kelamin
		    		</label>
		    		<div class="col-sm-8">
		      			<p class="form-control-static">: <?=@$infoPasien['umur']?> / <?=@$infoPasien['jenis_kelamin']?></p>
		    		</div>
		    	</div>
		    </div>
		    <div class="col-md-6">
		    	<div class="form-group">
		    		<label class="control-label text-bold col-sm-3">
		    			Nomor Telepon Pasien
		    		</label>
		    		<div class="col-sm-8">
		      			<p class="form-control-static">: <?=@$infoPasien['no_telepon_pasien']?></p>
		    		</div>
		    	</div>
		    	<div class="form-group">
		    		<label class="control-label text-bold col-sm-3">
		    			Nomor Rekam Medik
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
			<table id="tabel-radiologi" class="table table-striped table-hover datatable-basic dataTable" style="width:100%;">
        <thead>
					<tr class="bg-inverse">
						<th>No</th>
						<th><?=Yii::t('fe', 'No Pendaftaran')?></th>
						<th><?=Yii::t('fe', 'Ruangan / Kamar')?></th>
						<th><?=Yii::t('fe', 'No Rujukan')?></th>
						<th><?=Yii::t('fe', 'Dokter Penunjang')?></th>
						<th><?=Yii::t('fe', 'Tanggal Pemeriksaan')?></th>
						<th><?=Yii::t('fe', 'Nama Pemeriksaan')?></th>
						<th><?=Yii::t('fe', 'Status Order')?></th>
						<th><?=Yii::t('fe', 'Status Periksa')?></th>
						<th>Aksi</th>
					</tr>
					</thead>
					<tbody>
						<?php
							if(empty($listRad))
							{
									echo "<td colspan='9' style='text-align:center'>No data available in table</td>";
							}
						?>
						<?php
							$rowNum = 1;
							foreach ($listRad as $group_rujukan) {
								$count_rujukan = count($group_rujukan);
								$i_detail = 1;
								foreach ($group_rujukan as $norujuk => $val_rad) {
											$penunjang_id = DocoHelpers::encrypt($val_rad['pasienmasukpenunjang_id']);
											$hasilpemeriksaanrad_id = DocoHelpers::encrypt($val_rad['hasilpemeriksaanrad_id']);
											$folderNames = $hasilpemeriksaanrad_id.'-'.$penunjang_id;
						?>
						<tr>
							<?php 
								$rowspan_rujukan = $count_rujukan;
								if($i_detail == 1){
							?>
							<td rowspan="<?=$rowspan_rujukan?>"><?=$rowNum?></td>
							<td rowspan="<?=$rowspan_rujukan?>"><?=@$val_rad['no_pendaftaran']?></td>
							<td rowspan="<?=$rowspan_rujukan?>"><?=@$val_rad['ruangan_nama']?></td>
							<td rowspan="<?=$rowspan_rujukan?>"><?=@$val_rad['no_rujukan']?></td>
							<td rowspan="<?=$rowspan_rujukan?>"><?=@$val_rad['dokter_penunjang']?></td>
							<td rowspan="<?=$rowspan_rujukan?>"><?=DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($val_rad['tglmasukpenunjang'])), false, true)?></td>
							<?php }?>
							<td><?=@$val_rad['daftartindakan_nama']?></td>
							<?php 
								$status_dipakai = '-';
								if(!is_null($val_rad['stat_penunjang'])){
									$status_dipakai = @$val_rad['stat_penunjang'];
								}else{
									$status_dipakai = @$val_rad['stat_periksa'];
								}
								?>
							<?php ?>
							<td><?=$status_dipakai?></td>
							<td><?=@$val_rad['stat_periksa']?></td>
							<td>
								<?php
									// if(isset($val_rad['status_periksa']) && $val_rad['status_periksa'] == '475'){
									$btn_kritis = 'btn-info';
									if(isset($val_rad['is_hasilkritis']) && $val_rad['is_hasilkritis'] == TRUE){
										$btn_kritis = 'btn-danger';
									}
									$css = '';
									if(isset($val_rad['is_hasil']) && isset($val_rad['is_read'])){
										if($val_rad['is_hasil'] == true && $val_rad['is_read'] == false && $val_rad['tgl_verifikasi'] != null){
											$css = '<i class="fa fa-info-circle" data-placement="top" data-toggle="tooltip" data-html="true" title="Belum Dibaca" aria-hidden="true"></i>';
											$btn_kritis = 'btn-warning';
										}
										
									}
						
									if(isset($val_rad['hide'])){
										$btnHasil = [
											'class'=>'btn '.$btn_kritis.' btn-sm btn-riwayat btn-cetak-penunjang not-read btn-hasil',
											'id' => 'btn-cetak-penunjang-rad',
											'data-norujukan' => @$val_rad['no_rujukan'],
											'data-daftartindakan_id' => @$val_rad['daftartindakan_id'],
											'data-tindakanpelayanan_id' => @$val_rad['tindakanpelayanan_id'],
											'data-pasienmasukpenunjang_id' => @$val_rad['pasienmasukpenunjang_id'],
											'data-isread' => $val_rad['is_read'] === false ? 'not-read' : 'is-read',
											'data-url' => '/radiologi/hasil-rad/cetak-hasil?id=' . DocoHelpers::encrypt($val_rad['daftartindakan_id']) .'&tindakan_id=' . DocoHelpers::encrypt($val_rad['tindakanpelayanan_id']) . '&penunjang_id=' . DocoHelpers::encrypt($val_rad['pasienmasukpenunjang_id']),
										];
										if (!isset($val_rad['disabled'])) {
											$btnHasil = array_merge([
												'disabled' => true,
												'data-original-title' => "Pemeriksaan Radiologi<br>Belum Selesai / Dibatalkan.",
												'data-toggle' => 'tooltip',
												'data-placement' => 'top'
											], $btnHasil);
										}
										echo Html::button($css.' Lihat Hasil', $btnHasil);
									}
									$alias = Yii::getAlias("@media");
									$penunjang_id = DocoHelpers::encrypt($val_rad['pasienmasukpenunjang_id']);
									$hasilpemeriksaanrad_id = DocoHelpers::encrypt($val_rad['hasilpemeriksaanrad_id']);
									$folderName = $hasilpemeriksaanrad_id.'-'.$penunjang_id;
									$prefix = '/input-hasil-rad/';
									$expl_1 = explode('_', $val_rad['penunjang_pemeriksaanrad']);
									$res_data = [];
									$res_folder = false;
									foreach ($expl_1 as $key => $value) {
											if ($value != '' ) {
													if (file_exists($alias.$prefix.$value.'/') ) {
															$res_folder = true;
													}
													$res_data[] = $value;
											}
									}
									echo '&nbsp;';
									if($res_folder){
										$btnFoto = [
											'class'=>'btn '.$btn_kritis.' btn-sm btn-riwayat btn-unduh-foto btn-hasil',
											'target' => '_blank',
											'onclick' => "window.location.href='/radiologi/expertise/all-unduh-hasil?folder=" . json_encode($res_data) . "&target=" . $val_rad['no_rujukan'] . "'"
										];
										if ($konfig_disable_button && !isset($val_rad['disabled'])) {
											$btnFoto = array_merge([
												'disabled' => true,
												'data-original-title' => "Pemeriksaan Radiologi<br>Belum Selesai / Dibatalkan.",
												'data-toggle' => 'tooltip',
												'data-placement' => 'top'
											], $btnFoto);
										}
										echo Html::button('Unduh foto', $btnFoto);
									}
									echo '&nbsp;';
									if(!empty($val_rad['image_link'])) {
										$btnGambar = [
											'class' => 'btn btn-warning btn-view-gambar btn-riwayat btn-hasil',
											'data-id' => $hasilpemeriksaanrad_id,
										];
										if ($konfig_disable_button && !isset($val_rad['disabled'])) {
											$btnGambar = array_merge([
												'disabled' => true,
												'data-original-title' => "Pemeriksaan Radiologi<br>Belum Selesai / Dibatalkan.",
												'data-toggle' => 'tooltip',
												'data-placement' => 'top'
											], $btnGambar);
										}
										echo Html::button('View Gambar', $btnGambar);
									}
									// }
								?>
							</td>
						</tr>
						<?php
								$i_detail++;
								$rowNum++;
								}
							}
						?>
					</tbody>
        </table>
		</div>
	</div>
</div>

<?php
$this->registerJs('
var dokter_dpjp = "'.$infoPasien['dokter_id'].'";
var user = "'.$infoPasien['id_pegawai'].'";
$(\'[data-toggle="tooltip"]\').tooltip({
	html: true
});
', View::POS_END);
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>