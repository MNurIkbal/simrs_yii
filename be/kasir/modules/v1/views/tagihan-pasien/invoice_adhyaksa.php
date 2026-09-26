<?php use Doco\components\DocoHelpers;
$isNaik = $konfigSystem['is_pembulatankeatas'];
$satuan = $konfigSystem['satuanpembulatan']; ?>
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
            <th style="width:50%">Keterangan</th>
            <th style="width:5%">Qty</th>
            <th style="width:5%">Satuan</th>
            <th style="width:20%">Tarif</th>
            <th style="width:25%">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1; 
            $total = 0; 
            $totalItem = 0;
            foreach ($data as $key => $value) :
                $totalItem += count($value);
        ?>
            <tr>
                <td rowspan="<?= count($value) + 1 ?>" style="width:2px"> <?= $no++ ?></td>
                <td colspan="5"><strong><?= $key ?></strong> </td>
            </tr>
            <?php 
                foreach ($value as $k => $v) : 
                    $total += $v['sub_total'];
                    $tindakan = ($v['is_konsultasi']) ? $v['dokter_tindakan'] : $v['tindakan_obat_nama'];
            ?>
                <tr>
                    <td><?= $tindakan ?></td>
                    <td class="number"><?= $v['qty'] ?></td>
                    <td></td>
                    <td class="number"><?= DocoHelpers::formatNumber($v['tarif_satuan']) ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($v['sub_total']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
</table>
<?php if ($totalItem >= 10) : 
?>
<pagebreak />
<?php
    endif;
?>
<?php 
$rounded = ($total + $pembayaran['total_administrasi'] - $pembayaran['discount'] - $pembayaran['penggunaan_uangmuka'] - $pembayaran['total_sisatagihan']);

$pembulatan = DocoHelpers::pembulatan($rounded, $isNaik, $satuan); ?>

<table width="100%" class="tbl-bordered">
        <tr>
            <td colspan="5" class="number">Sub Total :</td>
            <td class="number border-bottom"><?= DocoHelpers::formatNumber($total) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Biaya Administrasi :</td>
            <td class="number"><?= DocoHelpers::formatNumber($pembayaran['total_administrasi']) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Diskon :</td>
            <td class="number"><?= DocoHelpers::formatNumber($pembayaran['discount']) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Uang Muka :</td>
            <td class="number"><?= DocoHelpers::formatNumber($pembayaran['penggunaan_uangmuka']) ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Piutang :</td>
            <td class="number"><?= DocoHelpers::formatNumber($pembayaran['total_sisatagihan']) ?></td>
        </tr>
        <tr>
            <th colspan="5" class="number"><strong>Pembulatan :</strong></th>
            <td class="number"><?= $pembulatan['pembulatan'] ?></td>
        </tr>
        <tr>
            <td colspan="5" class="number">Grand Total :</td>
            <td class="number border-bottom"><?= DocoHelpers::formatNumber($pembulatan['total']) ?></td>
        </tr>
        <tr>
            <td colspan="6" class="border-bottom">Terbilang : <?= DocoHelpers::Terbilang($pembulatan['total']) ?> Rupiah</td>
        </tr>
        <tr>
            <td colspan="6">Sudah diterima dari : <?= $nama_pasien ?> </td>
        </tr>
        <?php
            if (!empty($pembayaran['total_tunai'])) :
        ?>
                <tr>
                    <td colspan="5">Tunai</td>
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
                    <td colspan="5">oleh Penjamin: <?= $nama ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($value['total_dijamin']) ?></td>
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
            <td colspan="5" class="number"><strong>Sisa :</strong></td>
            <td class="number"><?= DocoHelpers::formatNumber($pembayaran['total_kembalian']) ?></td>
        </tr>
</table>
