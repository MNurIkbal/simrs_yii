<?php 
use Doco\components\DocoHelpers; 
use yii\helpers\ArrayHelper;
?>

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
            $biayaAdmin = ArrayHelper::getValue($biaya_admin, 'biaya_admin', 0);
            $penjamin_id = ArrayHelper::getValue($biaya_admin, 'penjamin_id', 0);
            $nominPayer = ArrayHelper::getValue($biaya_admin, 'nominPayer', 0);
            $nominPatient = ArrayHelper::getValue($biaya_admin, 'nominPatient', 0);
            $admAsuransi = ArrayHelper::getValue($additionalPembayaran, 'adm_asuransi', []);
            $discAdm = ArrayHelper::getValue($admAsuransi, 'nominal_diskon', 0);
            if ($biayaAdmin > 0) :
        ?>
            <tr>
                <td></td>
                <td><strong>BIAYA ADMINISTRASI</strong> </td>
                <td></td>
                <td></td>
                <td class="number"><?= DocoHelpers::formatNumber($biayaAdmin) ?></td>
            </tr>
        <?php endif; ?>

        <?php if (!empty($discAdm)): ?>
        <tr>
            <td style="width:15%;"></td>
            <td style="width:50%;"><i>disc(<?= DocoHelpers::formatNumber($discAdm) ?>)</i></td>
            <td style="width:5%;" class="number"></td>
            <td class="number"></td>
            <td class="number">- <?= DocoHelpers::formatNumber($discAdm) ?></td>
        </tr>
        <?php endif; ?>
        
        <?php if (!empty($dataRoomRent)) : ?>
            <?php foreach ($dataRoomRent as $key => $ruangan) : ?>
                <?php foreach ($ruangan as $keyKelompok => $kelompok) : ?>
                    <?php foreach ($kelompok as $value) : 
                        $qty = ArrayHelper::getValue($value, 'qty', 1);
                        $min = ArrayHelper::getValue($value, 'min', 0);
                        $max = ArrayHelper::getValue($value, 'max', 0);
                        $noBed = ArrayHelper::getValue($value, 'no_bed');
                        $kelas = ArrayHelper::getValue($value, 'kelas');
                        $kamar = ArrayHelper::getValue($value, 'kamar');
      
                        $hargaSatuan = ArrayHelper::getValue($value, 'harga_satuan', 0);
                        $tarifDijamin = ArrayHelper::getValue($value, 'tarif_dijamin', 0);
                        $tarifDibayarkan = ArrayHelper::getValue($value, 'tarif_dibayarkan', 0);
                        
                        $nominPayer = ($jenis_invoice != $invoice_pasien) ? $tarifDijamin : 0;
                        $nominPatient = ($jenis_invoice == $invoice_penjamin) ?  0 : $tarifDibayarkan;
                        $totalPayer += $nominPayer;
                        $totalPatient += $nominPatient;    
                    ?>
                    <tr>
                        <td style="width:15%;"><?= date('d/m/Y', strtotime($min)) ?></td>
                        <td style="width:50%;"><?= $kelas.'/'.$kamar ?></td>
                        <td style="width:5%;" class="number"><?= $qty ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($hargaSatuan) ?></td>
                        <td class="number"><?= DocoHelpers::formatNumber($qty*$hargaSatuan) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php foreach ($dataTindakan as $ruangan => $kelompok) : ?>
            <?php foreach ($kelompok as $key => $value) : ?>
            <tr>
                <td></td>
                <td><strong><?= strtoupper($key) ?></strong> </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php 
                foreach ($value as $k => $v) : 
                    $qty = ArrayHelper::getValue($v, 'qty');
                    $isAkomodasi = ArrayHelper::getValue($v, 'is_akomodasi');
                    $isObat = ArrayHelper::getValue($v, 'is_obat');
                    $tglPelayanan = ArrayHelper::getValue($v, 'tgl_pelayanan');
                    $tindakanId = ArrayHelper::getValue($v, 'tindakan_obat_id');
                    $tindakan = ArrayHelper::getValue($v, 'tindakan_obat');
                    $dokter = ArrayHelper::getValue($v, 'dokter_tindakan');
                    $kelas = ArrayHelper::getValue($v, 'kelaspelayanan_nama');
                    $ruangan = ArrayHelper::getValue($v, 'ruangan');
                    $tarifSatuan = ArrayHelper::getValue($v, 'harga_satuan', 0);
                    $tarifDiskon = ArrayHelper::getValue($v, 'tarif_diskon', 0);
                    $subTotal = ArrayHelper::getValue($v, 'tarif', 0);
                    $total += $subTotal;
                    $totalDisc += $tarifDiskon;
                    $keterangan = ($isAkomodasi) ? " / ". $kelas .' / '. $ruangan : '';
            ?>
                <tr>
                    <td style="width:15%;"><?= date('d/m/Y', strtotime($tglPelayanan)) ?></td>
                    <td style="width:50%;"><?= $tindakan.$keterangan ?>
                    <?php if (empty($keterangan)) {
                            echo (in_array($tindakanId, $tindakan_keperawatan ) && !$isObat) 
                            ? "/ ".$kelas." - ".$dokter : ''; 
                        }
                    ?>
                    
                    </td>
                    <td style="width:5%;" class="number"><?= $qty ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($tarifSatuan) ?></td>
                    <td class="number"><?= DocoHelpers::formatNumber($subTotal) ?></td>
                </tr>
                <?php if ($tarifDiskon > 0) : ?>
                <tr>
                    <td style="width:15%;"></td>
                    <td style="width:50%;"><i>disc(<?= DocoHelpers::formatNumber($tarifDiskon) ?>)</i></td>
                    <td style="width:5%;" class="number"></td>
                    <td class="number"></td>
                    <td class="number">- <?= DocoHelpers::formatNumber($tarifDiskon) ?></td>
                </tr>
            <?php endif; ?>
            <?php endforeach; ?>
            <?php endforeach; ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        <?php endforeach; ?>
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
            $totalTunai = $totalTunai - $totalKembalian;
            $totalAkhir = $total - $discount - $uangMuka - $totalNonTunai - $totalTunai - $dijamin;
        ?>

        <?php if ($discount > 0) : ?>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td><strong>DISKON TOTAL</strong></td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"><strong>- <?= DocoHelpers::formatNumber($discount); ?></strong></td>
            </tr>
        <?php endif ?>
            
        <?php if ($uangMuka > 0 || $totalTunai > 0 || $totalNonTunai > 0) : ?>    
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td><strong>PEMBAYARAN :</strong></td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"></td>
            </tr>
        
        <?php if ($totalTunai > 0) : ?>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;&nbsp;** Uang Cash</td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"><strong><?= DocoHelpers::formatNumber($totalTunai); ?></strong></td>
            </tr>
        <?php endif; ?>

        <?php if ($totalNonTunai > 0) : ?>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;&nbsp;** Uang Non Tunai</td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"><strong><?= DocoHelpers::formatNumber($totalNonTunai); ?></strong></td>
            </tr>
        <?php endif ?>

        <?php if ($uangMuka > 0) : ?>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;&nbsp;** Uang Muka</td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"><strong><?= DocoHelpers::formatNumber($uangMuka); ?></strong></td>
            </tr>
        <?php endif; endif; ?>

        <?php if ($totalPembulatan > 0) : ?>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td><strong>PEMBULATAN</strong></td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"><strong><?= DocoHelpers::formatNumber($totalPembulatan); ?></strong></td>
            </tr>
        
        <?php endif ?>

        <?php if ($totalKembalian > 0) : ?>
            <tr>
                <td>&nbsp;&nbsp;</td>
                <td><strong>KEMBALIAN</strong></td>
                <td>&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;</td>
                <td class="number"><strong><?= DocoHelpers::formatNumber($totalKembalian); ?></strong></td>
            </tr>
        <?php endif ?>

        <tr>
            <td>&nbsp;&nbsp;</td>
            <td><strong>TOTAL AKHIR</strong></td>
            <td>&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;</td>
            <td class="number">
                <strong>
                <?= DocoHelpers::formatNumber($totalAkhir); ?>
                </strong>
            </td>
        </tr>
        <tr>
            <td>&nbsp;&nbsp;</td>
            <td><strong>SISA TAGIHAN</strong></td>
            <td>&nbsp;&nbsp;</td>
            <td>&nbsp;&nbsp;</td>
            <td class="number"><strong><?= ($jenis_invoice == 2) ? 0 : DocoHelpers::formatNumber($dijamin); ?></strong></td>
        </tr>
    </tfoot>
</table>
