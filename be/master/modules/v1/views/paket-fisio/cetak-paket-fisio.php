<style>
	th, td {
		padding: 8px;
		font-size: 11px;
	}
</style>
<table border="1" style="width:100%; border-collapse: collapse;">
	<thead>
		<tr>
			<th style="text-align: left;">No</th>
			<th style="text-align: left;">Kode Paket</th>
			<th style="text-align: left;">Nama Paket</th>
			<th style="text-align: left;">Nama Paket Lainnya</th>
			<th style="text-align: left;">Frekuensi</th>
			<th style="text-align: left;">Jumlah</th>
			<th style="text-align: left;">Status</th>
			<th style="text-align: left;">Catatan</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($model)): ?>
			<?= $no = 1; ?>
			<?php foreach ($model as $index => $value): ?>
			<tr>
				<td><?= $no ?></td>
				<td><?= $value->daftartindakan_kode ?></td>
				<td><?= $value->daftartindakan_nama ?></td>
				<td><?= $value->daftartindakan_namalainnya ?></td>
				<td style="text-align: center;"><?= $value->frekuensi ?></td>
				<td style="text-align: center;"><?= $value->jumlah ?></td>
				<td><?= $value->is_active ? 'Aktif' : 'Tidak aktif' ?></td>
				<td><?= $value->catatan ? $value->catatan : '-' ?></td>
			</tr>
			<?= $no++; ?>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
</table>