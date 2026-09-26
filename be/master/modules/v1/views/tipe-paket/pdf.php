<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-26 10:40:04
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-08-03 16:43:42
 */

?>

<!-- Header -->

<!-- Table -->
<table border="1" style="width:100%; border-collapse: collapse;">
	<thead>
		<tr>
			<th><?= Yii::t('app', 'No') ?></th>
			<th><?= Yii::t('app', 'Kode') ?></th>
			<th><?= Yii::t('app', 'Nama Paket') ?></th>
			<th><?= Yii::t('app', 'Nama Lainnya') ?></th>
			<th><?= Yii::t('app', 'Status') ?></th>
			<th><?= Yii::t('app', 'Catatan') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($model)): ?>
			<?= $no = 1; ?>
			<?php foreach ($model as $index => $value): ?>
			<tr>
				<td><?= $no ?></td>
				<td><?= $value->tipepaket_kode ?></td>
				<td><?= $value->tipepaket_nama ?></td>
				<td><?= $value->tipepaket_namalainnya ?></td>
				<td><?= ($value->is_active) ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak aktif') ?></td>
				<td><?= $value->keterangan_tipepaket ?></td>
			</tr>
			<?= $no++; ?>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>