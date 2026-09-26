<?php use Doco\components\DocoHelpers; ?>
<style>
    .tbl-header {
        border: 1px solid black;
    }

    .tbl-header td {
        padding: 3px;
    }
    .tbl-bordered {
        border-collapse: collapse;
        font-size: 12px;
    }

    .tbl-bordered thead th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered tbody td {
        border: 1px solid black;
        padding: 5px;
    }

    .tbl-bordered tfoot td {
        padding: 3px;
    }

    .number {
        text-align: right
    }
    .center {
        text-align: center
    }
    .box {
      width: 200px;
      border: 1px solid;
      padding: 10px;
      margin: 0;
    }
    /*.tbl-summary thead tr th:nth-child(2) {
        border-bottom: 1px solid !important;
    }*/
    /*.tbl-summary tbody tr:nth-child(4) th {
        border-bottom: 1px solid !important;
    }*/
    .tbl-summary tbody tr:nth-child(3) td {
        border-bottom: 1px solid !important;
    }
    /*.tbl-summary tbody tr:nth-child(6) td {
        border-bottom: 1px solid !important;
    }*/
    .header-box {
        background-color: #ffffff;
        filter: alpha(opacity=40);
        opacity: 0.95;
        border:1px solid;
    }
    .border-bottom {
        border-bottom: 1px solid;
    }
</style>
<table width="100%" class="tbl-bordered">
    <thead>
        <tr>
            <th style="width:5%">No.</th>
            <th style="width:50%">Item Name</th>
            <th style="width:5%">Qty</th>
            <th style="width:5%">UOM</th>
            <th style="width:25%">Total Amount</th>
        </tr>
    </thead>
    <?php if(!empty($detail)) : ?>
    <tbody>
        <?php
            $no = 1; 
            $total = 0;
            $endingBalance = $pembayaran['total_kembalian'];
            $biayaAdmin = $pembayaran['total_administrasi'];
            $roundedBillAmount = (int) ($pembayaran['bill_amount'] + $biayaAdmin - $total_diskon);
            foreach ($detail as $key => $value) :
                $total += ($value['tarif']);
        ?>
            <tr>
                <td> <?= $no++ ?></td>
                <td><?= $value['obatalkes_nama'] ?></td>
                <td class="number"><?= $value['qty'] ?></td>
                <td><?= $value['uom'] ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($value['tarif']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="number">Total Amount:</td>
            <td class="number"><?= DocoHelpers::formatNumber($total) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">Discount :</td>
            <td class="number"><?= DocoHelpers::formatNumber($total_diskon) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">Administration Fee :</td>
            <td class="number border-bottom"><?= DocoHelpers::formatNumber($biayaAdmin) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">Rounded Bill Amount :</td>
            <td class="number"><?= DocoHelpers::formatNumber($roundedBillAmount) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="border-bottom">Patient Amount in Words : <?= DocoHelpers::terbilangToEnglish($roundedBillAmount) ?> Rupiahs</td>
        </tr>
        <tr>
            <td colspan="4">Received payment from : <?= $nama_pasien ?> </td>
        </tr>
        <?php
            if (!empty($pembayaran['total_tunai'])) :
        ?>
                <tr>
                    <td colspan="4">Cash</td>
                    <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembayaran['total_tunai']) ?></td>
                </tr>
        <?php
            endif;
        ?>
        <?php
            if (!empty($listMetode)) :
                foreach ($listMetode as $value) :
                    $metode = preg_replace("/^\w+ - /", '', $value['metode_bayar']);
        ?>
                <tr>
                    <td colspan="4"><?= $value['no_kartu'] ?> - <?= $metode ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($value['total_dibayar']) ?></td>
                </tr>
        <?php
                endforeach;
            endif; 
        ?>
        <?php
            if (!empty($listPayer)) :
                foreach ($listPayer as $value) :
                    $nama = preg_replace("/^\w+ - /", '', $value['penjamin_nama']);
        ?>
                <tr>
                    <td colspan="4">by Payer: <?= $nama ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($value['total_dijamin']) ?></td>
                </tr>
        <?php
                endforeach;
            endif; 
        ?>
        <tr>
            <td colspan="4" class="number"><strong>Ending Balance :</strong></td>
            <td class="number"><?= DocoHelpers::formatNumber($endingBalance) ?></td>
        </tr>
    </tfoot>
    <?php endif; ?>
</table>

