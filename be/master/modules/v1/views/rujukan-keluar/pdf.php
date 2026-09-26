<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-18 16:57:03
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-05-04 16:02:06
 */
	
?>

<!-- Header -->
<h3><center><?= Yii::t('app', 'RUJUKAN KELUAR') ?></center></h3>

<!-- Table -->
<table border="1" style="width:100%; border-collapse: collapse;">
	<thead>
		<tr>
			<th><?= Yii::t('app', 'No') ?></th>
			<th><?= Yii::t('app', 'Asal Rujukan') ?></th>
			<th><?= Yii::t('app', 'Rumah Sakit Rujukan') ?></th>
			<th><?= Yii::t('app', 'Alamat Lengkap') ?></th>
			<th><?= Yii::t('app', 'Nomor Telpon') ?></th>
			<th><?= Yii::t('app', 'Status') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($model)): ?>
			<?= $no = 1; ?>
			<?php foreach ($model as $index => $value): ?>
			<tr>
				<td><?= $no ?></td>
				<td><?= !empty($value->asalRujukan) ? $value->asalRujukan->asalrujukan_nama : '-' ?></td>
				<td><?= $value->rumahsakit_rujukan ?></td>
				<td><?= $value->alamat_rsrujukan ?></td>
				<td><?= $value->telp_fax ?></td>
				<td><?= ($value->is_active) ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif') ?></td>
			</tr>
			<?= $no++; ?>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>