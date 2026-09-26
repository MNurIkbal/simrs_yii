<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-22 10:33:04
 */
?>

<table id="tabel-riwayat-pindah-kamar" border=1 style="width:100%; border-collapse: collapse;">
    <thead>
        <tr class="bg-inverse">
            <th>No</th>
            <th><?= Yii::t('app', 'Tanggal Admisi') ?></th>
            <th><?= Yii::t('app', 'Tanggal Pindah') ?></th>
            <th><?= Yii::t('app', 'No Rekam Medik') ?></th>
            <th><?= Yii::t('app', 'No Pendaftaran') ?></th>
            <th><?= Yii::t('app', 'Nama Pasien') ?></th>
            <th><?= Yii::t('app', 'Jenis Kelamin') ?></th>
            <th><?= Yii::t('app', 'Dokter') ?></th>
            <th><?= Yii::t('app', 'Cara Bayar / Penjamin') ?></th>
            <th><?= Yii::t('app', 'Kelas Pelayanan') ?></th>
            <th><?= Yii::t('app', 'Jenis Kasus Penyakit') ?></th>
            <th><?= Yii::t('app', 'Ruangan Asal') ?></th>
            <th><?= Yii::t('app', 'Ruangan Tujuan') ?></th>
        </tr>
    </thead>
    <tbody>
    	<?php 
    		$row=1;
    		foreach ($data_pindah_kamar as $value) {
    	?>
	        <tr>
	            <td><?= $row ?></td>
	            <td><?= $value['tgl_admisi'] ?></td>
	            <td><?= $value['tgl_pindahkamar'] ?></td>
	            <td><?= $value['no_rekam_medik'] ?></td>
	            <td><?= $value['no_pendaftaran'] ?></td>
	            <td><?= $value['nama_pasien'] ?></td>
	            <td><?= $value['jenis_kelamin'] ?></td>
	            <td><?= $value['dokter_admisi'] ?></td>
	            <td><?= $value['carabayar_nama'].' / '.$value['penjamin_nama'] ?></td>
	            <td><?= $value['kelaspelayanan_nama'] ?></td>
	            <td><?= $value['jeniskasuspenyakit_nama'] ?></td>
	            <td><?= $value['ruangan_sekarang'] ?></td>
	            <td><?= $value['ruangan_pindah'] ?></td>
	        </tr>
        <?php 
        		$row++;
    		}
        ?>
    </tbody>
</table>