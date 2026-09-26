<?php
/**
 * @author : iqbal (iqbal@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */
?>
<style media="print" type="text/css" rel="stylesheet">
@media print {
    table {
        font-size: 10px;
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
        table-layout: fixed;
        font-family: Arial, Helvetica, sans-serif;
    }

    .nama_obat {
        font-size: 7pt;
    }

    .komponen_racikan {
        font-size: 4.5pt;
    }

    .aturan_signa {
        font-size: 6.5pt;
    }

    .sm_text {
        font-size: 5.2pt;
    }
}
</style>

<div>
<?php
    if(!empty($detail)):
        $pageCount = count($detail);
        foreach ($detail as $key => $resep) :
            $current = $key + 1;
            $type = $resep['is_oral'] == 1 ? 'OBAT ORAL' : 'OBAT NON ORAL' ;
?>
        <table border="0" style="line-height: 0.9;">
            <tr>
                <td>
                    <strong>&nbsp; <?= $renderQrCode ?></strong>
                </td>
                <td style="text-align: right;vertical-align: top;">
                    <strong><?= date('d M Y', strtotime( date('Y-m-d') )) ?></strong>
                    <br>
                    <br>
                    <strong class="sm_text">
                        <?= Yii::t("app", "NO : ");?>&nbsp;<?= $pasien['no_transaksi'] ?>
                    </strong>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="nama_obat">
                    <strong><?= $resep['nama_obat'] ?></strong>
                </td>
            </tr>

            <?php 
                if(isset($resep['komponen_racikan']) && count($resep['komponen_racikan']) > 0) :
                    $komponen_racikan = implode(', ', $resep['komponen_racikan']);
                    $satuan_unit_nama = "";
                    if(isset($resep['satuan_unit_nama'])) $satuan_unit_nama = $resep['satuan_unit_nama'];
            ?>
                    <tr>
                        <td colspan="2" class="komponen_racikan" class="nama_obat">
                            <div class=""><strong><?= $komponen_racikan ?></strong></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="komponen_racikan">
                            <strong> <?= $satuan_unit_nama ?> </strong>
                        </td>
                    </tr>
            <?php endif; ?>
            
            <tr>
                <td colspan="2">
                    <strong><?= $resep['qty_obat'] ?>&nbsp;<?= $resep['satuan_input'] ?></strong>
                </td>
            </tr>

            <?php if(isset($resep['komponen_racikan']) && count($resep['komponen_racikan']) > 0) : ?>
                <tr>
                    <td colspan="2" class="aturan_signa">
                        <strong><?= $resep['signa'] ?></strong>
                    </td>
                </tr>
            <?php else: ?>
                
            <tr>
                <td colspan="2" class="aturan_signa" style="padding-top: 4px;">
                    <strong><?= $resep['signa'] ?></strong>
                </td>
            </tr>
            <?php endif; ?>
            
            <tr>
                <td colspan="2" class="aturan_signa">
                    <div><strong><?= $resep['catatan'] ?></strong></div>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="sm_text"><div><strong><?= $pasien['dokter'] ?></strong></div></td>
            </tr>
            <tr>
                <td colspan="2" class="sm_text">
                    <?php 
                        $lengthNoRM = strlen($pasien['no_rm']);
                        $lengthInfoPasien = 48 - $lengthNoRM;
                        $nama_pasien = substr($pasien['nama_pasien'], 0, $lengthInfoPasien);
                    ?>
                    <strong><?= $nama_pasien." / ".$pasien['no_rm'] ?></strong>
                </td>
            </tr>
            <tr>
                <td class="sm_text" style="width: 55%;">
                    <strong><?= Yii::t("app", "TL : ");?> &nbsp; <?= $pasien['tanggal_lahir'] ?></strong>
                </td>
                <td class="sm_text" style="vertical-align: top;padding-left:55px">
                    <strong>
                        <?= Yii::t("app", "TGL EXP : ");?>
                    </strong>
                </td>
            </tr>
        </table>

        <?php if ($current != $pageCount): ?>
            <pagebreak />
        <?php endif; ?>
    <?php endforeach ?>

    <?php else: ?>
        <?= Yii::t("app", "Tidak ada data.");?></td>
    <?php endif ?>
</div>
