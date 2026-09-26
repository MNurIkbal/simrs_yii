<?php

/**
 * @Author: Sunarko
 * @Date:   2018-07-13 16:05:52
 * @Last Modified by:
 * @Last Modified time:
 */

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
            <th>No</th>
            <th>Tanggal Admisi</th>
            <th>Tanggal Visite</th>
            <th>No. Pendaftaran</th>
            <th>No. Rekam Medik</th>
            <th>Nama Pasien</th>
            <th>Jenis Kelamin</th>
            <th>Cara  Bayar / Penjamin</th>
            <th>Kasus Penyakit</th>
            <th>Ruangan Kamar</th>
            <th>Dokter Penanggung Jawab</th>
            <th>Jenis Visite</th>
            <th>Dokter Visite</th>
       </tr>
	</thead>
	<tbody>
    	<?php $counter=1; foreach($data as $row) : ?>
    	   <tr>
                <td><?= $counter ?></td>
                <td><?= $row['Tanggal Admisi'] ?></td>
                <td><?= $row['Tanggal Visite'] ?></td>
                <td><?= $row['No. Pendaftaran'] ?></td>
                <td><?= $row['No. Rekam Medik'] ?></td>
                <td><?= $row['Nama Pasien'] ?></td>
                <td><?= $row['Jenis Kelamin'] ?></td>
                <td><?= $row['Cara Bayar'].' / '.$row['Penjamin'] ?></td>
                <td><?= $row['Kasus Penyakit'] ?></td>
                <td><?= $row['Ruangan'].' - '.$row['Kamar'].' - '.$row['Bed'] ?></td>
                <td><?= $row['Dokter Penanggung Jawab'] ?></td>
                <td><?= $row['Jenis Visite'] ?></td>
                <td><?= $row['Dokter Visite'] ?></td>
           </tr>
        <?php $counter++; endforeach; ?>
    </tbody>
</table>