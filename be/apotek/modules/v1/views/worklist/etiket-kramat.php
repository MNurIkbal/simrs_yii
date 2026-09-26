<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.co.id)
 * A product of PT. Citraraya Nusatama
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
            font-size: 10px;
        }

        .satuan_input {
            font-size: 10px;
        }

        .aturan_signa {
            font-size: 10px;
        }

        .sm_text {
            font-size: 5pt;
        }

        .md_text {
            font-size: 7pt;
        }
    }
</style>

<div>
    <?php
    if (!empty($detail)) :
        $pageCount = count($detail);
        foreach ($detail as $key => $resep) :
            $current = $key + 1;
            $type = $resep['is_oral'] == 1 ? 'OBAT ORAL' : 'OBAT NON ORAL';
    ?>
            <table border="0">
                <tr>
                    <td colspan="2" style="text-align: right;">
                        <strong><?= date('d M Y', strtotime(date('Y-m-d'))) ?></strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 5px;" class="nama_obat">
                        <strong><?= $resep['nama_obat'] ?></strong>
                    </td>
                </tr>

                <?php
                if (isset($resep['komponen_racikan']) && count($resep['komponen_racikan']) > 0) :
                    $komponen_racikan = implode(', ', $resep['komponen_racikan']);
                    $satuan_unit_nama = "";
                    if (isset($resep['satuan_unit_nama'])) $satuan_unit_nama = $resep['satuan_unit_nama'];
                ?>
                
                    <tr>
                        <td colspan="2" class="nama_obat">
                            <div class=""><strong><?= $komponen_racikan ?></strong></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="komponen_racikan">
                            <strong> <?= $satuan_unit_nama ?> </strong>
                        </td>
                    </tr>
                
                <?php else: ?>

                    <tr>
                        <td colspan="2">
                            <strong><?= $resep['qty_obat'] ?>&nbsp;<?= $resep['satuan_input'] ?></strong>
                        </td>
                    </tr>

                <?php endif; ?>

                <tr>
                    <td colspan="2" class="aturan_signa" style="padding-top: 5px;">
                        <strong><?= $resep['signa'] ?></strong>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class="aturan_signa">
                        <div><strong><?= $resep['catatan'] ?></strong></div>
                    </td>
                </tr>

                <?php
                $lengthNoRM = strlen($pasien['no_rm']);
                $lengthInfoPasien = 48 - $lengthNoRM;
                $nama_pasien = substr($pasien['nama_pasien'], 0, $lengthInfoPasien);
                ?>

                <tr>
                    <td colspan="2" class="md_text" style="padding-top: 7px;">
                        <!-- <div><strong><?= $pasien['dokter'] ?></strong></div> -->
                        <strong><?= $nama_pasien ?></strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="sm_text">
                        <strong><?= $pasien['no_rm'] ?></strong>
                    </td>
                </tr>
                <tr>
                    <td class="sm_text" style="width: 55%;">
                        <strong><?= Yii::t("app", "TL : "); ?> &nbsp; <?= $pasien['tanggal_lahir'] ?></strong>
                    </td>
                    <td style="width: 45%;" class="sm_text">
                        <strong>
                            <?= Yii::t("app", "NO : "); ?>&nbsp;<?= $pasien['no_transaksi'] ?>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td class="sm_text">
                        <strong>
                            <?= Yii::t("app", "TGL EXP : "); ?>
                        </strong>
                    </td>
                </tr>
            </table>

            <?php if ($current != $pageCount) : ?>
                <pagebreak />
            <?php endif; ?>
        <?php endforeach ?>

    <?php else : ?>
        <?= Yii::t("app", "Tidak ada data."); ?></td>
    <?php endif ?>
</div>
