<?php 

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use app\components\DocoHelpers;
use yii\helpers\Url;
?>
<br>
<div class="row">
	<div class="col-md-12 flex-container">
		<div class="flex-info">
			<?= Yii::$app->controller->renderPartial('//layouts/pasien_identitas', [
				'data_pasien' => $data_pasien,
				'title' => Yii::t('fe', 'Informasi Kunjungan Terakhir Pasien')
			]) ?>
		</div>
		<div class="flex-detail">
            <div class="panel panel-default side-margin">
                <a data-toggle="collapse" href="#detailinfo-ranap" role="button" aria-expanded="false" aria-controls="detailinfo-ranap">
                    <div class="panel-heading flex-container">
                        <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Informasi'); ?></b></h6>
                        <ul class="icons-list">
                            <li><i class="fa fa-chevron-down" id="chevron"></i></li>
                        </ul>
                    </div>
                </a>

                <div class="panel-body column-detail collapse multi-collapse" id="detailinfo-ranap">
                    <div class="row">
                        <div class="col-md-12 flex-container">
                            <div class="flex-60">
	                            <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Instalasi - Ruangan") ?></b></label>
	                            <p class="col-sm-12"><?= (isset($data_pasien['instalasi_ruangan']) ? $data_pasien['instalasi_ruangan'] : '-' ); ?></p>
	                            <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Cara Bayar - Penjamin") ?></b></label>
	                            <p class="col-sm-12"><?= isset($data_pasien['carabayar_penjamin']) ? $data_pasien['carabayar_penjamin'] : '-' ?></p>
	                        </div>
	                        <div class="flex-40">
	                            <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Kasus Penyakit") ?></b></label>
	                            <p class="col-sm-12"><?= isset($data_pasien['jeniskasuspenyakit_nama']) ? $data_pasien['jeniskasuspenyakit_nama'] : '-' ?></p>
	                            <label class="text-left control-label col-sm-12 font-design"><b><?= Yii::t("fe", "Status") ?></b></label>
	                            <p class="col-sm-12"><?= isset($data_pasien['status_periksa']) ? $data_pasien['status_periksa'] : '-' ?></p>
	                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
            <a data-toggle="collapse" href="#detail-list-kunjungan" role="button" aria-expanded="false" aria-controls="detail-list-kunjungan">
                <div class="panel-heading flex-container">
                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Data Kunjungan Pasien'); ?></b></h6>

                    <ul class="icons-list">
                        <li><i class="fa fa-chevron-down" id="chevron"></i></li>
                    </ul>
                </div>
            </a>

            <div class="panel-body" id="detail-list-kunjungan">
                <div class="row">
                    <div class="col-md-12">
						<br>
						<table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" id="tbl-list-kunjungan">
			            	<thead>
			            		<tr class="bg-inverse">
			            			<th>No.</th>
			            			<th><?=Yii::t('fe','Tanggal Pendaftaran')?></th>
			            			<th><?=Yii::t('fe','Nomor Pendaftaran')?></th>
			            			<th><?=Yii::t('fe','Instalasi - Ruangan Akhir')?></th>
			            			<th><?=Yii::t('fe','Kelas Pelayanan')?></th>
			            			<th><?=Yii::t('fe','Cara Bayar - Penjamin')?></th>
			            			<th><?=Yii::t('fe','Status Periksa')?></th>
			            			<th><?=Yii::t('fe','Aksi')?></th>
			            		</tr>
			            	</thead>
			            	<tbody>
			            	<?php
			            	if(count($list_kunjungan)){
			            		$no = 0;
				            	foreach($list_kunjungan as $key => $value):
				            		$no++;?>
				            		<tr>
				            			<td><?=$no?></td>
				            			<td><?=DocoHelpers::convDateTime($value['tgl_pendaftaran'])?></td>
				            			<td><?=$value['no_pendaftaran']?></td>
				            			<td><?=$value['instalasi_ruangan']?></td>
				            			<td><?= ($value['is_pasientitipan'])?$value['kelas_ditagihkan_nama']:$value['kelaspelayanan_nama']  ?></td>
				            			<td><?=$value['carabayar_penjamin']?></td>
				            			<td><?=$value['status_periksa']?></td>
				            			<td>
				            				<a class="btn btn-info btn-labeled btn-xs" href="<?=Url::to(['inf-tagihan-pasien/detail', 'no_pendaftaran' => $value['no_pendaftaran'], 'pendaftaran_id' => DocoHelpers::encrypt($value['pendaftaran_id'])])?>"> <b><i class="fa fa-eye fa-xs"></i></b> <?=Yii::t('fe','Lihat')?></a>
				            			</td>
				            		</tr>
				            		<?php
				            	endforeach;
			            	} 
			            	?>
			            	</tbody>
			            </table>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
<?php 
$this->registerJs("
	$('#tbl-list-kunjungan').DataTable({
		filter: false,
		paging: false,
		scrollY: '45vh',
		scrollCollapse: true,
		language: {
			emptyTable: 'Belum Ada Data Kunjungan Pasien.'
		}
	});
")
?>