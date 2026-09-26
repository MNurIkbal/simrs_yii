<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-26 11:06:22
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-26 11:11:53
 */

?>

<!-- Header -->
<h2><center><?= Yii::t('app', 'PAKET PELAYANAN') ?></center></h2>
<h4><center><?= date('j F Y', strtotime("NOW")) ?></center></h4>

<!-- Table -->
<table border="1" style="width:100%; border-collapse: collapse;">
	<thead>
		<tr>
			<th><?= Yii::t('app', 'No') ?></th>
			<th><?= Yii::t('app', 'Kode Paket') ?></th>
			<th><?= Yii::t('app', 'Nama Paket') ?></th>
			<th><?= Yii::t('app', 'Nama Lainnya') ?></th>
			<th><?= Yii::t('app', 'Nama Tindakan') ?></th>
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
				<td><?= $value->tipePaket->tipepaket_kode ?></td>
				<td><?= $value->tipePaket->tipepaket_nama ?></td>
				<td><?= $value->tipePaket->tipepaket_namalainnya ?></td>
				<td><?= $value->daftarTindakan->daftartindakan_nama ?></td>
				<td><?= $value->is_active == true ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif') ?></td>
				<td></td>
			</tr>
			<?= $no++; ?>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>