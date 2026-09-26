<?php

/**
 * @Author: Muhamad Lukman Hakim (hakim.muhamad@docotel.com)
 * @Date:   2021-01-14 08:47:00
*/

use app\modules\v1\models\SatuanKonversi;

?>

<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
    }

    .tbl-bordered th {
        border: none;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }

    .text-left {
        text-align: left;
    }
</style>

<table width="100%" border="none">
	<thead>
	   <tr>
            <th class="text-left">No.</th>
            <th class="text-left">Nama Barang</th>
            <th class="text-left">Qty Mutasi</th>   
            <th class="text-left">Qty Konversi</th>
       </tr>
	</thead>
	<tbody>
    	<?php $no = 1; foreach($detail as $row) : ?>
    	<?php $konversi = SatuanKonversi::find()->where([
    		'satuanbesar_id' => $row['satuanbesar_id'], 'satuankecil_id' => $row['satuankecil_id']
    	])->one(); 
    	?>
    	   <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['barang_nama'] ?></td>
                <td><?= $row['qty_input']. ' '. $row['satuanbesar_nama'] ?></td>
                <td><?= $row['qty_dipesan'].' '.$row['satuankecil_nama'] ?></td>
           </tr>
        <?php endforeach; ?>
    </tbody>
</table>
