<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-18 15:07:46
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-18 15:37:57
 */

?>

<!-- Header -->
<h3><center><?= Yii::t('app', 'ASAL RUJUKAN') ?></center></h3>

<!-- Table -->
<table border="1" style="width:100%; border-collapse: collapse;">
	<thead>
		<tr>
			<th><?= Yii::t('app', 'No') ?></th>
			<th><?= Yii::t('app', 'Asal Rujukan') ?></th>
			<th><?= Yii::t('app', 'Institusi Asal Rujukan') ?></th>
			<th><?= Yii::t('app', 'Nama Lainnya') ?></th>
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
				<td><?= $value->asalrujukan_institusi ?></td>
				<td><?= $value->asalrujukan_namalainnya ?></td>
				<td><?= ($value->is_active) ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif') ?></td>
			</tr>
			<?= $no++; ?>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>