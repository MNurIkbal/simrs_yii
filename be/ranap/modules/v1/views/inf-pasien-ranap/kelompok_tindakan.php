<?php 
use Doco\components\DocoHelpers;

?>

<style>
    .number {
        text-align: right
    }

    table thead tr th {
        font:arial !important;
        font-size:12px !important;
    }

    table tbody tr td {
        font:arial !important;
        font-size:12px !important;
    }

    table tfoot tr td {
        font:arial !important;
        font-size:12px !important;
    }

</style>

    <table border="0" style="width:100%; border-collapse: collapse;">
        <tbody>
            <?php 
            $no = 1;
            foreach ($detailTindakan as $key => $value){
            ?>
                <tr>
                    <td style="text-align: center;"><?= $no ?></td>
                    <td style="text-align: left;"><?= ucwords($value['kelompoktindakan_nama']); ?></td>
                    <td style="text-align: right;"><?= DocoHelpers::rupiahDisplay($value['total']); ?></td>
                </tr>
            <?php
            $no++;
            }
            ?>
            <?php if($no < 1): ?>
            <tr>
                <td colspan="3" class="text-center"><i>Data tidak ditemukan</i></td>
            </tr>
            <?php endIf;
             ?>
        </tbody>
    </table>