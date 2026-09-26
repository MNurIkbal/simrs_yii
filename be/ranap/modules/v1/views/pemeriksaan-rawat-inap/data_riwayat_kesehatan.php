<h3 align="center"> Riwayat Kesehatan</h3>
<table>
	<tr>
		<td>Keluhan Utama</td>
		<td>: <?=@$data_riwayatkesehatan['keluhan_utama']?></td>
		<td>&nbsp;</td>
		<td>Riwayat Kesehatan Sekarang</td>
		<td>: <?=@$data_riwayatkesehatan['r_kes_sekarang']?></td>
	</tr>
	<tr>
		<td>Diagnosa Masuk</td>
		<td>: <?=@$data_riwayatkesehatan['diagnosa_nama']?></td>
		<td>&nbsp;</td>
		<td>Pernah Dirawat</td>
		<td>: <?=@$data_riwayatkesehatan['ket_pernahdirawat']?></td>
	</tr>
	<?php if($data_riwayatkesehatan['pernah_dirawat'] == TRUE){?>
		<tr>
			<td colspan="3">&nbsp;</td>
			<td>Tanggal Dirawat</td>
			<td>: <?=@$data_riwayatkesehatan['tgl_dirawat']?></td>
		</tr>
		<tr>
			<td colspan="3">&nbsp;</td>
			<td>Alasan Dirawat</td>
			<td>: <?=@$data_riwayatkesehatan['alasan_dirawat']?></td>
		</tr>
	<?php }?>
	<tr>
		<td rowspan="3">Riwayat Kehamilan</td>
		<td>: G = <?=@$data_riwayatkesehatan['r_kehamilan_g']?> ; P = <?=@$data_riwayatkesehatan['r_kehamilan_p']?> ; A = <?=@$data_riwayatkesehatan['r_kehamilan_a']?></td>
		<td>&nbsp;</td>
		<td>Pernah Operasi/Tindakan</td>
		<td>: <?=@$data_riwayatkesehatan['ket_pernahtindakan']?></td>
	</tr>
	<tr>
		<td>: HPHT = <?=@$data_riwayatkesehatan['hpht']?></td>
		<?php if($data_riwayatkesehatan['pernah_tindakan'] == TRUE){?>
		<td>&nbsp;</td>
		<td>Tanggal Operasi/Tindakan</td>
		<td>: <?=@$data_riwayatkesehatan['tgl_tindakan']?></td>
		<?php }else{?>
		<td colspan="3">&nbsp;</td>
		<?php }?>
	</tr>
	<tr>
		<td>: HAID = <?=@$data_riwayatkesehatan['ket_haid']?></td>
		<?php if($data_riwayatkesehatan['pernah_tindakan'] == TRUE){?>
		<td>&nbsp;</td>
		<td>Jenis Operasi/Tindakan</td>
		<td>: <?=@$data_riwayatkesehatan['golonganoperasi_nama']?></td>
		<?php }else{?>
		<td colspan="3">&nbsp;</td>
		<?php }?>
	</tr>
	<tr>
		<td>Ketergantungan</td>
		<td>: <?=@$list_asmenketergantungan?></td>
		<td>&nbsp;</td>
		<td>Pernah Alergi</td>
		<td>: <?=@$data_riwayatkesehatan['ket_pernahalergi']?></td>
	</tr>
	<tr>
		<td>Riwayat Penyakit Keluarga</td>
		<td>: <?=@$list_asmenpenyakitkel?></td>
		<?php if($data_riwayatkesehatan['r_alergi'] == TRUE){?>
		<td>&nbsp;</td>
		<td>Nama Alergi</td>
		<td>: <?=@$data_riwayatkesehatan['nama_alergi']?></td>
		<?php }else{?>
		<td colspan="3">&nbsp;</td>
		<?php }?>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
		<td>Transfusi Darah</td>
		<td>: <?=@$data_riwayatkesehatan['ket_transfusi']?></td>
	</tr>
	<?php if($data_riwayatkesehatan['transfusi'] == TRUE){?>
	<tr>
		<td colspan="3">&nbsp;</td>
		<td>Reaksi</td>
		<td>: <?=@$data_riwayatkesehatan['reaksi']?></td>
	</tr>
	<?php }?>
</table>