<?php
?>

<table border="1">
	<thead>
		<tr>
			<th>No</th>
			<th>Kode Footer</th>
			<th>Nama Footer</th>
			<th>Gambar Kiri</th>
			<th>Gambar Kanan</th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$count = 1;
		foreach ($data as $key => $value) {
	?>
	<tr>
		<td><?=$count?></td>
		<td><?=$value['kode_footer']?></td>
		<td><?=$value['nama_footer']?></td>
		<td><?=$value['gambar_kiri']?></td>
		<td><?=$value['gambar_kanan']?></td>
	</tr>
	<?php 
		$count++;
		}
	?>
	</tbody>
</table>