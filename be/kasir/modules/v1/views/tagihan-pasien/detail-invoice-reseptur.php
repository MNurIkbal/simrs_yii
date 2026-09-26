<?php 
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
?>
<style>
    body { 
        font-size: 12px;
        letter-spacing: 2px;
        font-family: "Arial, Helvetica, sans-serif";
    }
    .tbl-bordered {
        border-collapse: collapse;
        /*border: 1px solid black;*/
        font-size: 12px;
        letter-spacing: 2px;
        font-family: "Arial, Helvetica, sans-serif";
    }
    .tbl-bordered thead td {
        border-top: 1px solid black;
        border-bottom: 1px solid black;
        font-family: "Arial, Helvetica, sans-serif";
        text-align: center;
        letter-spacing: 2px;
    }
    .tbl-bordered tbody td {
        /*border: 1px solid black;*/
        font-family: "Arial, Helvetica, sans-serif";
        letter-spacing: 2px;
    }

    .tbl-bordered tfoot td {
        padding: 3px;
        letter-spacing: 2px;
    }
    .tbl-alamat {
        border-collapse: collapse;
        /*border: 1px solid black;*/
        font-size: 12px;
        font-family: "Arial, Helvetica, sans-serif";
        letter-spacing: 2px;
    }
    .tabel-header {
        font-family: "Arial, Helvetica, sans-serif";
        letter-spacing: 2px;
    }
    .tbl-alamat tr td {
        /*border: 1px solid black;*/
        font-family: "Arial, Helvetica, sans-serif";
        letter-spacing: 2px;
    }
    .footer  {
        padding: 3px;
    }

    .number {
        text-align: right
    }
    .center {
        text-align: center
    }
</style>
<table width="100%" class="tbl-bordered">
    <thead>
        <tr>
            <td style="width: 50%;">TRANSAKSI</td>
            <td style="width: 15%;">QTY</td>
            <td style="width: 35%;">JUMLAH</td>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($dataTindakan as $ruangan => $kelompok) : ?>
            <?php foreach ($kelompok as $key => $value) : ?>
               <?php foreach ($value as $val) : ?>
               <?php 
                  $totalTarif = ArrayHelper::getValue($val, 'tarif', 0);
                  $tindakan = ArrayHelper::getValue($val, 'tindakan_obat');
                  $qty = ArrayHelper::getValue($val, 'qty', 1);
               ?>
               <tr>
                  <td style="width: 50%;"><?= $tindakan ?></td>
                  <td  style="text-align: right; width: 15%;"><?= $qty ?></td>
                  <td style="text-align: right; width: 35%;"><?= DocoHelpers::formatNumber($totalTarif) ?></td>
               </tr>
            <?php endforeach; ?>
        <?php
        endforeach;
            endforeach;
        ?>
    </tbody>
</table>
