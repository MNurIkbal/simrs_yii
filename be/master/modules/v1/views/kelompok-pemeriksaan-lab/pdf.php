<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-23 15:58:50
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-23 16:01:21
 */
?>

<!-- Header -->
<h3><center><?= Yii::t('app', 'Kelompok Pemeriksaan Laboratorium') ?></center></h3>

<!-- Table -->
<table border="1" style="width:100%; border-collapse: collapse;">
	<thead>
		<tr>
			<th><?= Yii::t('app', 'No') ?></th>
			<th><?= Yii::t('app', 'Kode') ?></th>
			<th><?= Yii::t('app', 'Kelompok Pemeriksaan') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($model)): ?>
			<?= $no = 1; ?>
			<?php foreach ($model as $index => $value): ?>
			<tr>
				<td><?= $no ?></td>
				<td><?= $value->kode_kelompok ?></td>
				<td><?= $value->nama_kelompok ?></td>
			</tr>
			<?= $no++; ?>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>