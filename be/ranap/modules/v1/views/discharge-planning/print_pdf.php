<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-06-12 14:53:20
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-06-12 15:48:55
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
            <th>6</th>
            <th>Edukasi Kesehatan untuk di rumah</th>
            <th>Pemberi Edukasi</th>   
            <th>Tanggal/Pukul</th>
            <th>Nama Jelas & Tanda Tangan</th>
       </tr>
	</thead>
	<tbody>
    	<?php foreach($data as $row) : ?>
    	   <tr>
                <td></td>
                <td><?= $row['edukasi_kesehatan_nama'] ?></td>
                <td><?= $row['pemberi_edukasi'] ?></td>
                <td><?= $row['tgl_edukasi'] ?></td>
                <td><?= $row['nama_pegawai'] ?></td>
           </tr>
        <?php endforeach; ?>
    </tbody>
</table>