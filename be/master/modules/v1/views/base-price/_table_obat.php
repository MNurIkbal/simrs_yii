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
            <th>No</th>
            <th>Jenis Obat</th>
            <th>Nama Obat</th>
            <th align="right">Harga Dasar Yang Gunakan (Rp.)</th>
            <th align="right">Harga Jual</th>
       </tr>
    </thead>
    <tbody>
        <?php if(count($dataObat) > 0):
            $nomor = 0;
            foreach ($dataObat as $key => $value) : 
            $nomor ++;
        ?>
            <tr>
                <td width="5%" style="text-align: center;"><?= $nomor ?></td>
                <td><?= $value['jenisobatalkes_nama'] ?></td>
                <td><?= $value['obatalkes_nama'] ?></td>
                <td align="right" style="text-align: right;"><?= !empty($value['harganetto_ygdipakai']) ? DocoHelpers::formatNumber($value['harganetto_ygdipakai']) : 0 ?></td>
                <td style="text-align: right;"><?= !empty($value['hargaygdipakai']) ? DocoHelpers::formatNumber($value['hargaygdipakai']) : 0 ?></td>
            </tr>
        <?php endforeach ?>
        <?php else: ?>
            <tr>
                <td class="text-center" colspan="3"><?=\Yii::t("app", "Tidak ada data.");?></td>
            </tr>
        <?php endif ?>
    </tbody>
</table>
