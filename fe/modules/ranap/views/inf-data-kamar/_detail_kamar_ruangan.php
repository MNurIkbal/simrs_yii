
<h3>Detail Ruangan <?=@$info_kamar['ruangan_nama']?> - <?=@$info_kamar['kamarruangan_nokamar']?> - <?=@$info_kamar['kelaspelayanan_nama']?></h3>
<div class="table-responsive">
	<table class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				<th>No</th>
				<th>No. Rekam Medis</th>
				<th>Nama</th>
				<th>Alamat</th>
				<th>Jenis Kelamin</th>
				<th>Cara Bayar</th>
				<th>Tanggal Masuk</th>
				<th>Tanggal Pindah</th>
				<th>Dokter DPJP</th>
				<th>Kamar/Kelas/No. Bed</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$rowNum = 1;
				foreach ($dataTable as $rowData) {
					
			?>
			<tr>
				<td><?=$rowNum?></td>
				<td><?=$rowData['no_rekam_medik']?></td>
				<td><?=$rowData['nama_pasien']?></td>
				<td><?=$rowData['alamat_pasien']?></td>
				<td><?=$rowData['jenis_kelamin']?></td>
				<td><?=$rowData['carabayar_nama']?></td>
				<td><?=date('d-m-Y H:i:s',strtotime($rowData['tgl_admisi']))?></td>
				<td><?=$rowData['tgl_pindahkamar']?></td>
				<td><?=$rowData['dr_dpjp']?></td>
				<td><?=$rowData['kamarruangan_nokamar'].'/'.$rowData['kelaspelayanan_nama'].'/'.$rowData['no_tempattidur']?></td>
				<td><?=$rowData['stat_ranap']?></td>
			</tr>
			<?php 
				$rowNum++;
			}
			if(count($dataTable)<1){
				?>
			<tr>
				<td align="center" colspan="11">Tidak ada data pasien di kamar ini.</td>
			</tr>
				<?php
			}

			?>
		</tbody>
	</table>
</div>