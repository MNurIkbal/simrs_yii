<?php 
use Doco\components\DocoHelpers;
$payerAmount = $pembayaran['total_dijamin'];
$is_pembulatankeatas = $konfigSystem['is_pembulatankeatas'];
$satuan_pembulatan = $konfigSystem['satuanpembulatan'];

?>
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

    .tbl-footer {
        border-collapse: collapse;
        font-size: 12px;
    }

    .tbl-bordered thead th {
        border: 1px solid black;
        padding: 3px;
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
            $no = 1; 
            $total = $tmp_diskon = 0; 
            $subTotalPayer = $subTotalPatient = 0;
            $additionalInvoice = isset($pembayaran['additional_data']) ? json_decode($pembayaran['additional_data'], true) : [];
            $adm_diskon_pasien = isset($pembayaran['total_discountadm']) ? $pembayaran['total_discountadm'] : 0;
            $admDetail = isset($additionalInvoice['adm_asuransi']) ? $additionalInvoice['adm_asuransi'] : [];
            $nominalAdm = !empty($pembayaran['total_administrasi']) ? $pembayaran['total_administrasi'] : 0;

            foreach ($additionalInvoice['pembayaran_penjamin'] as $key=>$value)
            {
                $tmp_diskon += isset($value['discount_adm_penjamin']) ? $value['discount_adm_penjamin'] : 0;
            }
            if($adm_diskon_pasien != 0){
                $tmp_diskon = 0;
            }

            if ($nominalAdm) :
                $admPatient = isset($admDetail['harusbayar']) ? $admDetail['harusbayar'] : 0;
                $admPatient = !empty($payerAmount) ? $admPatient : $nominalAdm + $adm_diskon_pasien;
                $admPayer = !empty($payerAmount) ? $nominalAdm - $admPatient : 0;
                $admDist = isset($admDetail['nominal_diskon']) ? $admDetail['nominal_diskon'] : 0;
                $subTotalPayer += $admPayer;
                $subTotalPatient += $admPatient;
        ?>
            <tr>
                <td style="width:2px"> <?= $no++ ?></td>
                <td>
                    Administration Fee
                </td>
                <td class="number"><?= DocoHelpers::formatNumber($admPayer) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($admPatient + $dataDiskon['diskon_adminPasien']) ?></td>
            </tr>
        <?php
            endif;
            foreach ($data as $key => $value) :
                $totalPayer = $totalPatient = 0;
                $totalDiskon = 0;
                foreach ($value as $k => $v) : 
                    $totalPatient += !empty($payerAmount) ? $v['tarif_dibayarkan'] : $v['sub_total'];
                    $totalPatient += ((int)$v['tarif_dijamin'] == 0 && !empty($payerAmount)) ? $v['tarif_diskon'] : 0;
                    $totalPayer += !empty($payerAmount) ? $v['sub_total'] - $v['tarif_dibayarkan'] : 0;
                    $totalPayer -= ((int)$v['tarif_dijamin'] == 0 && !empty($payerAmount)) ? $v['tarif_diskon'] : 0;
                    $totalDiskon += isset($v['tarif_diskon']) ? $v['tarif_diskon'] : 0;
                    $total += $v['sub_total'];
                endforeach; 
                $subTotalPayer += $totalPayer;
                $subTotalPatient += $totalPatient;
        ?>
            <tr>
                <td style="width:2px"> <?= $no++ ?></td>
                <td>
                    <?= $key ?>
                </td>
                <td class="number"><?= DocoHelpers::formatNumber($totalPayer) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber($totalPatient) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<table width="100%" class="tbl-bordered">
        <tr>
            <td colspan="4" class="number" style="width:60%">Total Amount:</td>
            <td class="number border-bottom" style="width:20%"><?= DocoHelpers::formatNumber($subTotalPayer) ?></td>
            <td class="number border-bottom" style="width:20%"><?= DocoHelpers::formatNumber($subTotalPatient + $dataDiskon['diskon_adminPasien']) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">Discount :</td>
            <td class="number"><?= DocoHelpers::formatNumber($dataDiskon['diskonPayer'] + $dataDiskon['diskon_adminPayer']) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($dataDiskon['diskonPasien']) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">DP :</td>
            <td class="number">0</td>
            <td class="number"><?= DocoHelpers::formatNumber($pembayaran['penggunaan_uangmuka']) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">DEBT :</td>
            <td class="number">0</td>
            <td class="number"><?= DocoHelpers::formatNumber($pembayaran['total_sisatagihan']) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">Rounded Bill Amount :</td>
            <td class="number"><?= DocoHelpers::formatNumber($pembayaran['total_pembulatan']) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($pembayaran['pembulatan']) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">Amount:</td>
            <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembayaran['total_dijamin'] ) ?></td>
            <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembayaran['total_ditagihkan'] - $pembayaran['pembulatan'] ) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="number">Total amount cover by payer:</td>
            <td class="number"><?= DocoHelpers::formatNumber($pembayaran['total_dijamin'] + $pembayaran['total_pembulatan']) ?></td>
            <td class="number"><?= DocoHelpers::formatNumber($pembayaran['total_ditagihkan']) ?></td>
        </tr>

        <?php
            if (!empty($pembayaran['total_dijamin'])):
        ?>
            <tr>
                <td colspan="6" class="border-bottom">
                    Payer Amount in Words : <?= DocoHelpers::terbilangToEnglish($pembayaran['total_dijamin']) ?> Rupiahs
                </td>
            </tr>
        <?php
            endif;
        ?>


        <?php
            if (!empty($pembayaran['total_ditagihkan'])):
        ?>
            <tr>
                <td colspan="6" class="border-bottom">
                    Patient Amount in Words : <?= DocoHelpers::terbilangToEnglish($pembayaran['total_ditagihkan']) ?> Rupiahs
                </td>
            </tr>
        <?php
            endif;
        ?>

        <tr>
            <td colspan="6">Received payment from : <?= $nama_pasien ?> </td>
        </tr>
        <?php
            if (!empty($pembayaran['total_tunai'])) :
        ?>
                <tr>
                    <td colspan="5">Cash</td>
                    <td class="number"><?= DocoHelpers::formatNumber($pembayaran['total_tunai']) ?></td>
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
                    $nama = preg_replace("/^\w+ - /", '', $value['penjamin_nama']);
        ?>
                <tr>
                    <td colspan="5">by Payer: <?= $nama ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber(ceil($value['total_dijamin']/$satuan_pembulatan)*$satuan_pembulatan) ?></td>
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
            <td class="number"><?= DocoHelpers::formatNumber($pembayaran['total_kembalian']) ?></td>
        </tr>
</table>
