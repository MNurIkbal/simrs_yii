<?php
use Doco\components\DocoHelpers;

?>

<style type="text/css">
    .text-center{
        text-align: center;
    }
    .head-title{
        margin-bottom: -5px;
    }
    .tbl{
        border-collapse: collapse;
    }
    .tbl-no-border th{
        border: 0px;
        padding: 5px;
    }
    .tbl-no-border td{
        border: 0px;
        padding: 5px;
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
<table id="tabel-reseptur" class="tbl tbl-bordered" border="1" align="center">
    <thead>
        <tr class="bg-inverse">
            <th>No</th>
            <th><?=Yii::t('app', 'Nama obat')?></th>
            <th><?=Yii::t('app', 'Jumlah')?></th>
            <th><?=Yii::t('app', 'Dosis')?></th>
            <th><?=Yii::t('app', 'Cara Pemberian')?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if(count($data_obat) > 0){
            $rowNum=1;
            foreach ($data_obat as $key => $value) {
        ?>
        <tr>
            <td><?=$rowNum?></td>
            <td><?= !empty($value['obatalkes_nama']) ? $value['obatalkes_nama'] : ''; ?></td>
            <td><?= $qty_reseptur = !empty($value['qty_reseptur']) ? $value['qty_reseptur'] : ''; ?> <?= $satuan_kecil = !empty($value['satuan_kecil']) ? $value['satuan_kecil'] : ''; ?> </td>
            <td><?= $signa_nama = !empty($value['signa_nama']) ? $value['signa_nama'] : ''; ?></td>
            <td align="center"><?= $qty_reseptur = !empty($value['nama_rute']) ? $value['nama_rute'] : ''; ?></td>
        </tr>

        <?php 
            $rowNum++;
            }
        }else{
            ?>
            <tr>
                <td colspan="9" class="text-center">Data Obat Tidak Tersedia</td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>