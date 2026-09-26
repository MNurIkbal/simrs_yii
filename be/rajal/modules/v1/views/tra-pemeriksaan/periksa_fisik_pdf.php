<?php
/**
 * @Author: Sigit
 * @Date:   2018-04-09 13:10:36
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-09 17:17:28
 */
?>

<!-- HEADER -->
<center><?= Yii::t('app', 'PEMERIKSAAN FISIK') ?></center>

<!-- CONTENT -->
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
	<tr>
		<th><?= Yii::t('app', 'Poliklinik') ?></th>
		<td><?= $model->pendaftaran->ruangan->ruangan_nama ?></td>
		<th><?= Yii::t('app', 'Jenis Kelamin') ?></th>
		<td><?= $model->pendaftaran->pasien->jenisKelamin->lookup_name ?></td>
	</tr>
	<tr>
		<th><?= Yii::t('app', 'Nomor Pendaftaran') ?></th>
		<td><?= $model->pendaftaran->no_pendaftaran ?></td>
		<th><?= Yii::t('app', 'Tanggal Lahir') ?></th>
		<td><?= $model->pendaftaran->pasien->tanggal_lahir ?></td>
	</tr>
	<tr>
		<th><?= Yii::t('app', 'Nomor Rekam Medik') ?></th>
		<td><?= $model->pendaftaran->pasien->no_rekam_medik ?></td>
		<th><?= Yii::t('app', 'Cara Bayar') ?></th>
		<td><?= $model->pendaftaran->caraBayar ?></td>
	</tr>
	<tr>
		<th><?= Yii::t('app', 'Nama Pasien') ?></th>
		<td><?= $model->pendaftaran->pasien->nama_pasien ?></td>
		<th><?= Yii::t('app', 'Penjamin') ?></th>
		<td><?= $model->pendaftaran->penjamin->penjamin_nama ?></td>
	</tr>
</table>
<br>
<br>
<table border="0" cellpadding="1" cellspacing="1" style="width:100%">
	<tr>
		<th colspan="2"><?= Yii::t('app', 'DATA PEMERIKSAAN') ?></th>
		<th colspan="2"><?= Yii::t('app', 'DATA TANDA VITAL') ?></th>
	</tr>
	<tr>
		<th><?= Yii::t('app', 'Dokter') ?></th>
		<td><?= $model->pemeriksaanfisik_id ?></td>
		<th><?= Yii::t('app', 'Tekanan Darah') ?></th>
		<td><?= $model->pemeriksaanfisik_id ?></td>
	</tr>
	<tr>
		<th><?= Yii::t('app', 'Perawat') ?></th>
		<td><?= $model->pemeriksaanfisik_id ?></td>
		<th><?= Yii::t('app', 'Mean Arteri Pressure') ?></th>
		<td><?= $model->pemeriksaanfisik_id ?></td>
	</tr>
	<tr>
		<th><?= Yii::t('app', 'Tanggal Periksa') ?></th>
		<td><?= $model->pemeriksaanfisik_id ?></td>
		<th><?= Yii::t('app', 'Detak Nadi') ?></th>
		<td><?= $model->pemeriksaanfisik_id ?></td>
	</tr>
	<tr>
		<th><?= Yii::t('app', 'Keadaan Umum') ?></th>
		<td><?= $model->pemeriksaanfisik_id ?></td>
		<th><?= Yii::t('app', 'Pernafasan') ?></th>
		<td><?= $model->pemeriksaanfisik_id ?></td>
	</tr>
	<tr>
		<th colspan="2"><?= Yii::t('app', 'PEMERIKSAAN THORAX') ?></th>
		<th><?= Yii::t('app', 'Suhu Tubuh') ?></th>
		<td><?= $model->suhutubuh ?></td>
	</tr>
	<tr>
		<th><?= Yii::t('app', 'Inspeksi') ?></th>
		<td><?= $model->inspeksi ?></td>
		<th><?= Yii::t('app', 'Tinggi Badan') ?></th>
		<td><?= $model->tinggibadan_cm ?></td>
		<th><?= Yii::t('app', 'Berat Badan') ?></th>
		<td><?= $model->beratbadan_kg ?></td>
	</tr>
	<tr>
		<th><?= Yii::t('app', 'Palpasi') ?></th>
		<td><?= $model->palpasi ?></td>
		<th><?= Yii::t('app', 'Kelainan pada Bagian Tubuh') ?></th>
		<td><?= $model->kelainanpadabagtubuh ?></td>
	</tr>
	<tr>
		<th><?= Yii::t('app', 'Perkusi') ?></th>
		<td><?= $model->perkusi ?></td>
		<th colspan="2"><?= Yii::t('app', 'GLASCOW COMA SCALE') ?></th>
	</tr>
	<tr>
		<th><?= Yii::t('app', 'Auskultasi') ?></th>
		<td><?= $model->auskultasi ?></td>
		<th><?= Yii::t('app', 'GCS Eye') ?></th>
		<td><?= $model->auskultasi ?></td>
	</tr>
</table>