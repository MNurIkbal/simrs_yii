<?php
?>

<table border="1">
	<thead>
		<tr>
			<th>No</th>
			<th>Kode Header</th>
			<th>Nama Header</th>
			<th>Jenis Kertas</th>
			<th>Logo Kiri</th>
			<th>Logo Kanan</th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$count = 1;
		foreach ($data as $key => $value) {
	?>
	<tr>
		<td><?=$count?></td>
		<td><?=$value['kode_header']?></td>
		<td><?=$value['nama_header']?></td>
		<td><?=$value['kertas_nama']?></td>
		<td><?=$value['logo_kiri']?></td>
		<td><?=$value['logo_kanan']?></td>
	</tr>
	<?php 
		$count++;
		}
	?>
	</tbody>
</table>