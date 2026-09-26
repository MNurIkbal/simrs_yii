<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-09 13:10:36
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-18 10:41:20
 */
?>
<!-- Header -->
<h4 align="center"><?= Yii::t('app', 'BUKTI PEMESANAN DOKUMEN REKAM MEDIK') ?></h4>
<h4 align="center"><?= Yii::t('app', 'RUANGAN ' . strtoupper($ruangan)) ?></h4>

<!-- Content -->
<table border="0" style="width:100%">
	<tr>
		<td><?= Yii::t('app', 'Tanggal Pemesanan') ?></td>
		<td>:</td>
		<td><?= $model->tgl_pesandokrm ?></td>
		<td></td>
		<td><?= Yii::t('app', 'Instalasi Tujuan Pemesanan') ?></td>
		<td>:</td>
		<td><?= !empty($instalasi) ? $instalasi : ''; ?></td>
	</tr>
	<tr>
		<td><?= Yii::t('app', 'Tanggal Minta Kirim') ?></td>
		<td>:</td>
		<td><?= $model->tgl_mintakirim ?></td>
		<td></td>
		<td><?= Yii::t('app', 'Ruangan Tujuan Pemesanan') ?></td>
		<td>:</td>
		<td><?= !empty($ruangan) ? $ruangan : ''; ?></td>
	</tr>
</table>
<br>
<!-- Table -->
<table border="1" style="width:100%; border-collapse: collapse;">
	<thead>
		<tr>
			<th><?= Yii::t('app', 'No') ?></th>
			<th><?= Yii::t('app', 'Tanggal Rekam Medik') ?></th>
			<th><?= Yii::t('app', 'No. Rekam Medik') ?></th>
			<th><?= Yii::t('app', 'Nama Pasien') ?></th>
			<th><?= Yii::t('app', 'Warna Dokumen') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($model->detail)): ?>
			<?= $no = 1; ?>
			<?php foreach ($model->detail as $index => $value): ?>
			<tr>
				<td><?= $no ?></td>
				<td><?= date('d-m-Y', strtotime($value->infoPosisiDokRekamMedik->tglrekammedis)); ?></td>
				<td><?= $value->infoPosisiDokRekamMedik->no_rekam_medik ?></td>
				<td><?= $value->infoPosisiDokRekamMedik->nama_pasien ?></td>
				<td><?= $value->infoPosisiDokRekamMedik->warnadokrm_namawarna ?></td>
			</tr>
			<?= $no++; ?>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>
<br>
<table align="right">
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td><?= Yii::t('app', 'Pemesanan dokumen rekam medik') ?></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td><br><br><br></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td><center><?= Yii::$app->user->identity->nama_pemakai; ?></center></td>
    </tr>
</table>