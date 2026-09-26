<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-18 16:44:18
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-18 16:56:05
 */

?>

<!-- Header -->
<h3><center><?= Yii::t('app', 'PERUJUK') ?></center></h3>

<!-- Table -->
<table border="1" style="width:100%; border-collapse: collapse;">
	<thead>
		<tr>
			<th><?= Yii::t('app', 'No') ?></th>
			<th><?= Yii::t('app', 'Asal Rujukan') ?></th>
			<th><?= Yii::t('app', 'Nama Perujuk') ?></th>
			<th><?= Yii::t('app', 'Kode Perujuk') ?></th>
			<th><?= Yii::t('app', 'Spesialis') ?></th>
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
				<td><?= $value->asalrujukan_nama ?></td>
				<td><?= $value->namaperujuk ?></td>
				<td><?= $value->perujuk_kode ?></td>
				<td><?= $value->spesialis ?></td>
				<td><?= $value->alamatlengkap ?></td>
				<td><?= $value->notelp ?></td>
				<td><?= ($value->is_active) ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif') ?></td>
			</tr>
			<?= $no++; ?>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>
