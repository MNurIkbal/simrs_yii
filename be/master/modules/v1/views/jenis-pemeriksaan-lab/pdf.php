<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-24 16:16:53
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-24 16:19:10
 */

?>

<!-- Header -->
<h3><center><?= Yii::t('app', 'Jenis Pemeriksaan Laboratorium') ?></center></h3>

<!-- Table -->
<table border="1" style="width:100%; border-collapse: collapse;">
	<thead>
		<tr>
			<th><?= Yii::t('app', 'No') ?></th>
			<th><?= Yii::t('app', 'Kode') ?></th>
			<th><?= Yii::t('app', 'Jenis Pemeriksaan') ?></th>
			<th><?= Yii::t('app', 'Nama Lainnya') ?></th>
			<th><?= Yii::t('app', 'Kelompok Pemeriksaan') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($model)): ?>
			<?= $no = 1; ?>
			<?php foreach ($model as $index => $value): ?>
			<tr>
				<td><?= $no ?></td>
				<td><?= $value->jenispemeriksaanlab_kode ?></td>
				<td><?= $value->jenispemeriksaanlab_nama ?></td>
				<td><?= $value->jenispemeriksaanlab_namalainnya ?></td>
				<td><?= $value->kelompokPemeriksaanLab->nama_kelompok ?></td>
			</tr>
			<?= $no++; ?>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>