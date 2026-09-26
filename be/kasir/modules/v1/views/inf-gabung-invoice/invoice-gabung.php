<?php 
    use Doco\components\DocoHelpers;
    $satuan_pembulatan = $konfigSystem['satuanpembulatan'];
?>
<style>
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

    .footer  {
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
    .tbl-summary thead tr th:nth-child(2) {
        border-bottom: 1px solid !important;
    }
    .tbl-summary tbody tr:nth-child(4) th {
        border-bottom: 1px solid !important;
    }
    .tbl-summary tbody tr:nth-child(4) td {
        border-bottom: 1px solid !important;
    }
    .tbl-summary tbody tr:nth-child(6) td {
        border-bottom: 1px solid !important;
    }
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
            <th style="width:55%">Service(s)</th>
            <th style="width:20%">Payer Amount</th>
            <th style="width:20%">Patient Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $adm_diskon_pasien = 0;
            $no = 1; 
            $total = 0; 
            $subTotalPayer = $subTotalPatient = $data_dijamin = $round_penjamin = $grand_total_diskon_payer = $grand_total_diskon_pasien = 0;
            if ($nominalAdm > 0) :
        ?>
            <tr>
                <td style="width:2px"> <?= $no++ ?></td>
                <td>
                    Administration Fee
                </td>
                <td class="number"><?= DocoHelpers::formatNumber($admPayer) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($admPatient) ?></td>
            </tr>
        <?php
            endif;
            $subtotal_pembulatan = $diskon_payer = 0 ;
            foreach ($data as $key => $value) :
                $totalPayer = $totalPatient =  0;
                $totalDiskon = 0;
                $total_dibayar = isset($value['total_dibayar']) ? $value['total_dibayar'] : 0;
                $payerAmount = isset($value['total_dijamin']) ? $value['total_dijamin'] : 0;
                $sub_total = isset($value['sub_total']) ? $value['sub_total'] : 0;
                $total_diskon = isset($value['total_diskon']) ? $value['total_diskon'] : 0;
                $total_diskon_pasien = isset($value['total_diskon_pasien']) ? $value['total_diskon_pasien'] : 0;
                $total_diskon_payer = isset($value['total_diskon_payer']) ? $value['total_diskon_payer'] : 0;
                $totalPatient += $total_dibayar + $total_diskon_pasien;
                $totalPayer += $payerAmount + $total_diskon_payer;
                $subTotalPayer += $totalPayer;
                $subTotalPatient += $totalPatient;
                $grand_total_diskon_pasien += $total_diskon_pasien;
                $grand_total_diskon_payer += $total_diskon_payer;
        ?>
            <tr>
                <td style="width:2px"> <?= $no++ ?></td>
                <td>
                    <?= isset($value['kelompoktindakan_nama']) ? $value['kelompoktindakan_nama'] : '' ?>
                </td>
                <td class="number"><?= DocoHelpers::formatNumber($totalPayer) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($totalPatient) ?></td>
            </tr>
        <?php endforeach; 
            $subTotalPayer += $admPayer;
            $subTotalPatient += $admPatient;
        ?>
    </tbody>
</table>

<table width="100%" class="tbl-bordered">
        <tr>
            <td colspan="4" class="number" style="width:60%">Total Amount:</td>
            <td class="number border-bottom" style="width:20%"><?= DocoHelpers::formatNumber($subTotalPayer) ?></td>
            <td class="number border-bottom" style="width:20%"><?= DocoHelpers::formatNumber($subTotalPatient) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">Discount :</td>
            <td class="number" style="width:20%"><?= DocoHelpers::formatNumber($grand_total_diskon_payer) ?></td>
            <td class="number" style="width:20%"><?= DocoHelpers::formatNumber($grand_total_diskon_pasien) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">DP :</td>
            <td class="number">0</td>
            <td class="number"><?= DocoHelpers::formatNumber($penggunaan_uangmuka) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">DEBT :</td>
            <td class="number">0</td>
            <td class="number"><?= DocoHelpers::formatNumber($total_sisatagihan) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">Rounded Bill Amount :</td>
            <td class="number"><?= DocoHelpers::formatNumber($roundeBillAmountPayer) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($roundeBillAmountPatient) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">Amount:</td>
            <td class="number border-bottom"><?= DocoHelpers::formatNumber($subTotalPayer - $grand_total_diskon_payer ) ?></td>
            <td class="number border-bottom"><?= DocoHelpers::formatNumber((($subTotalPatient - $grand_total_diskon_pasien - $penggunaan_uangmuka )>= 0)  ? $subTotalPatient + $roundeBillAmountPatient : 0) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">Total amount cover by payer:</td>
            <td class="number"><?= DocoHelpers::formatNumber($subTotalPayer - $grand_total_diskon_payer + $roundeBillAmountPayer) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber((($subTotalPatient - $grand_total_diskon_pasien - $penggunaan_uangmuka) >= 0)  ? $subTotalPatient + $roundeBillAmountPatient : 0) ?></td>
        </tr>

        <?php
            if ($total_dijamin > 0):
        ?>
            <tr>
                <td colspan="6" class="border-bottom">
                    Payer Amount in Words : <?= DocoHelpers::terbilangToEnglish($total_dijamin + $total_pembulatan) ?> Rupiahs
                </td>
            </tr>
        <?php
            endif;
        ?>

        <?php
            if ($total_ditagihkan > 0):
        ?>
            <tr>
                <td colspan="6" class="border-bottom">
                    Patient Amount in Words : <?= DocoHelpers::terbilangToEnglish($total_ditagihkan) ?> Rupiahs
                </td>
            </tr>
        <?php
            endif;
        ?>

        <tr>
            <td colspan="6">Received payment from : <?= $nama_pasien ?> </td>
        </tr>
        <?php
            if ($total_tunai > 0) :
        ?>
                <tr>
                    <td colspan="5">Cash</td>
                    <td class="number"><?= DocoHelpers::formatNumber($total_tunai) ?></td>
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
                    <td colspan="5"><?= $value['no_kartu'] ?> - <?= $metode ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($value['total_dibayar']) ?></td>
                </tr>
        <?php
                endforeach;
            endif; 
        ?>
        <?php
            if (!empty($listPayer)) :
                foreach ($listPayer as $value) :
                    $nama = preg_replace("/^\w+ - /", '', isset($value['penjamin_nama']) ? $value['penjamin_nama'] : '');
                    $total_dijamin_payer = isset($value['total_dijamin']) ? $value['total_dijamin'] : 0;
        ?>
                <tr>
                    <td colspan="5">by Payer: <?= $nama ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber(ceil($total_dijamin_payer/$satuan_pembulatan)*$satuan_pembulatan) ?></td>
                </tr>
        <?php
                endforeach;
            endif; 
        ?>
        <tr>
            <td colspan="5"></td>
            <td class="border-bottom"></td>
        </tr>
        <tr>
            <td colspan="5" class="number"><strong>Ending Balance :</strong></td>
            <td class="number"><?= DocoHelpers::formatNumber($total_kembalian) ?></td>
        </tr>
</table>
