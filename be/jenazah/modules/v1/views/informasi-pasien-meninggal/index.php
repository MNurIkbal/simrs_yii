<?php
use Doco\components\DocoHelpers;
?>
<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
</style>

<table width="100%" class="tbl-bordered">
	<thead>
	   <tr>
            <th>No.</th>
            <th>Tanggal Meninggal</th>
            <th>Nama Jenazah</th>
            <th>Nomor Rekam Medik</th>
            <th>Jenis Kelamin</th>
            <th>Ruangan Asal</th>
            <th>Penyebab Meninggal Dunia</th>
            <th>Nama Penanggung Jawab</th>
            <th>Status</th>
       </tr>
	</thead>
	<tbody>
    	<?php $no = 1; foreach ($data as $key => $value) : ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date(' d M Y', strtotime($value['tgl_meninggal'])) ?></td>
                <td><?= $value['nama_pasien'] ?></td>
                <td><?= $value['no_rekam_medik'] ?></td>
                <td><?= $value['jenis_kelamin'] ?></td>
                <td><?= $value['ruangan_nama'] ?></td>
                <td><?= $value['penanggungjawab_nama'] ?></td>
                <td><?= $value['penanggungjawab_nama'] ?></td>
                <td><?= $value['status_periksa_nama'] ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>