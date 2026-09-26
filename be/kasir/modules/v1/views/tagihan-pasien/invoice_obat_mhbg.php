<?php use Doco\components\DocoHelpers; ?>
<style>
    .tbl-bordered {
        border-collapse: collapse;
        /*border: 1px solid black;*/
        font-size: 12px;
        font-family: Courier New,Courier,monospace;
    }
    .tbl-bordered thead th {
        /*border: 1px solid black;*/
        font-family: Courier New,Courier,monospace;
    }
    .tbl-bordered tbody td {
        /*border: 1px solid black;*/
        font-family: Courier New,Courier,monospace;
    }

    .tbl-bordered tfoot td {
        padding: 3px;
        font-family: Courier New,Courier,monospace;
    }
    .tbl-alamat {
        border-collapse: collapse;
        /*border: 1px solid black;*/
        font-size: 12px;
        font-family: Courier New,Courier,monospace;
    }
    .tbl-alamat tr td {
        /*border: 1px solid black;*/
        font-family: Courier New,Courier,monospace;
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
<table width="100%" class="tbl-bordered" style="margin-top: 30px;">
    <thead>
        <tr>
            <th colspan="5">&nbsp;&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1; 
            $total = $totalDisc = 0; 
            $biayaAdm = isset($pembayaran['total_administrasi']) ? $pembayaran['total_administrasi'] : 0;
            $additonalPembayaran = !empty($pembayaran['additional_data']) ? json_decode($pembayaran['additional_data'], true) : [];
            $additionalAdm = isset($additonalPembayaran['adm_asuransi']) ? $additonalPembayaran['adm_asuransi'] : [];
            $nominalDisc = isset($additionalAdm['nominal_diskon']) ? $additionalAdm['nominal_diskon'] : 0;
            $total += $biayaAdm;
            $totalDisc += $nominalDisc;
            if (!empty($biayaAdm)) :
        ?>
            <tr>
                <td></td>
                <td><strong>BIAYA ADMINISTRASI</strong> </td>
                <td></td>
                <td></td>
                <td class="number"><?= DocoHelpers::formatNumber($biayaAdm) ?></td>
            </tr>
        <?php
            endif;

            if (!empty($nominalDisc)):
        ?>

        <tr>
            <td style="width:15%;"></td>
            <td style="width:50%;"><i>disc(<?= DocoHelpers::formatNumber($nominalDisc) ?>)</i></td>
            <td style="width:5%;" class="number"></td>
            <td class="number"></td>
            <td class="number">- <?= DocoHelpers::formatNumber($nominalDisc) ?></td>
        </tr>


        <?php
            endif;
            foreach ($dataTindakan as $key => $value) :
                $tarifDiskon = $value['tarif_diskon'];
                $total += (int) $value['tarif'];
                $totalDisc += $tarifDiskon;
        ?>
            <tr>
                <td style="width:15%;"><?= isset($value['tgl_resep']) ? date('d/m/Y', strtotime($value['tgl_resep'])) : '-' ?></td>
                <td style="width:50%;"><?= $value['obatalkes_nama'] ?></td>
                <td style="width:5%;" class="number"><?= $value['qty'] ?></td>
                <td class="number"><?= DocoHelpers::formatNumber(isset($value['harga_satuan']) ?  $value['harga_satuan'] : ($value['tarif']/$value['qty'])) ?></td>
                <td class="number"><?= DocoHelpers::formatNumber((int) $value['tarif']) ?></td>
            </tr>
            <?php
                if (!empty($tarifDiskon)) :
            ?>
                <tr>
                    <td style="width:15%;"></td>
                    <td style="width:50%;"><i>disc(<?= DocoHelpers::formatNumber($tarifDiskon) ?>)</i></td>
                    <td style="width:5%;" class="number"></td>
                    <td class="number"></td>
                    <td class="number">- <?= DocoHelpers::formatNumber($tarifDiskon) ?></td>
                </tr>
            <?php
                endif;
            endforeach; 
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5">&nbsp;&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;</td>
            <td><strong>SUB TOTAL</strong></td>
            <td>&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;</td>
            <td class="number"><strong><?= DocoHelpers::formatNumber($total); ?></strong></td>
        </tr>
        <?php
            if (!empty($pembayaran['discount'])) :
        ?>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td><strong>DISKON TOTAL</strong></td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"><strong>- <?= DocoHelpers::formatNumber($pembayaran['discount']); ?></strong></td>
            </tr>
        <?php
            endif;

            if (!empty($pembayaran['penggunaan_uangmuka']) || !empty($pembayaran['total_tunai']) || !empty($pembayaran['total_nontunai'])):
        ?>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td><strong>PEMBAYARAN :</strong></td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"></td>
            </tr>
        <?php
                if (!empty($pembayaran['total_tunai'])) :
        ?>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;&nbsp;** Uang Cash</td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"><strong><?= DocoHelpers::formatNumber($pembayaran['total_tunai']); ?></strong></td>
            </tr>
        <?php
                endif;

                if ($pembayaran['total_nontunai']):
        ?>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;&nbsp;** Uang Non Tunai</td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"><strong><?= DocoHelpers::formatNumber($pembayaran['total_nontunai']); ?></strong></td>
            </tr>
        <?php
                endif;

                if (!empty($pembayaran['penggunaan_uangmuka'])) :
        ?>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;&nbsp;** Uang Muka</td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"><strong><?= DocoHelpers::formatNumber($pembayaran['penggunaan_uangmuka']); ?></strong></td>
            </tr>
        <?php
                endif;
            endif;
        ?>
        <?php
            if (!empty($pembayaran['total_pembulatan'])):
        ?>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td><strong>PEMBULATAN</strong></td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"><strong><?= DocoHelpers::formatNumber($pembayaran['total_pembulatan']); ?></strong></td>
            </tr>
        <?php
            endif;

            if (!empty($pembayaran['total_kembalian'])) :
        ?>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td><strong>KEMBALIAN</strong></td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"><strong><?= DocoHelpers::formatNumber($pembayaran['total_kembalian']); ?></strong></td>
            </tr>
        <?php
            endif;
        ?>
        <tr>
            <td>&nbsp;&nbsp;</td>
            <td><strong>TOTAL AKHIR</strong></td>
            <td>&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;</td>
            <td class="number">
                <strong>
                <?= DocoHelpers::formatNumber(($total - $pembayaran['discount']) - $pembayaran['penggunaan_uangmuka'] - $pembayaran['total_nontunai'] - $pembayaran['total_tunai'] + $pembayaran['total_pembulatan'] - $pembayaran['total_kembalian']); ?>
                </strong>
            </td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;</td>
            <td><strong>SISA TAGIHAN</strong></td>
            <td>&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;</td>
            <td class="number"><strong><?= DocoHelpers::formatNumber($pembayaran['total_dijamin']); ?></strong></td>
        </tr>
    </tfoot>
</table>
