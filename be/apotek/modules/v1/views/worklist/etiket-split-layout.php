<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.com)
 * A product of PT. Citra Raya Nusatama
 * Powered by Sirs
 */
?>

<style>
    table {
        font-size: 8px;
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
        table-layout: fixed;
        font-family: Arial, Helvetica, sans-serif;
    }

    .komponen_obat {
        font-size: 5pt;
    }
</style>

<?php
if (!empty($detail)) :
    $pageCount = count($detail);
    foreach ($detail as $key => $value) :
        $current = $key + 1;
        $type = $value['is_oral'] == 1 ? 'OBAT ORAL' : 'OBAT NON ORAL';
?>
        <table border="0">
            <tr>
                <td style="width: 50%;">
                    <strong><?= $pasien['gender']; ?>/ <?= substr(strtoupper($pasien['nama_pasien']), 0, 20); ?></strong>
                </td>

                <td style="width: 50%; text-align: justify;">
                    <span style="font-size: 7;">
                        <?= !empty($pasien['no_transaksi']) ? $pasien['no_transaksi'] : $pasien['no_transaksi'] ?>
                    </span>
                    &nbsp;|&nbsp;
                    <span style="font-size: 7;">
                        <strong><?= $pasien['tgl_cetak'] ?></strong>
                    </span>
                </td>
            </tr>

            <tr>
                <td style="width: 50%;">
                    <span style="font-size: 7;">
                        <strong>DOB: <?= $pasien['tanggal_lahir'] ?></strong>
                    </span>
                    <span style="font-size: 7;">
                        <strong><?= isset($pasien['no_rm']) ? ' [' . $pasien['no_rm'] . ']' : ''; ?></strong>
                    </span>
                </td>

                <td style="width: 50%;">
                    <span>
                        <?= strtoupper($pasien['dokter']) ?>
                    </span>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="text-align: center; font-size: 7px">
                    <?php if (isset($value['type']) && $value['type'] == 'content') : ?>
                        <strong>KOMPOSISI RACIKAN (<?= $value['segment'] ?>/<?= $value['length'] ?>)</strong>
                    <?php else : ?>
                        &nbsp;&nbsp;
                    <?php endif ?>
                </td>
            </tr>

            <?php if (isset($value['type']) && $value['type'] == 'content') : ?>
                <?php for ($i = 0; $i < count($value['komponen_obat']); $i++) : ?>
                    <tr>
                        <td style="width: 50%; font-size: 7px">
                            <strong>
                                <?php $nama_obat_racikan = isset($value['komponen_obat'][$i]) ? substr($value['komponen_obat'][$i]['nama_obat'], 0, 20) : '&nbsp;&nbsp;'; ?>
                                <?= $nama_obat_racikan ?>
                            </strong>
                        </td>

                        <td style="width: 50%;">
                            <table style="width: 100%; font-size: 7px">
                                <tr>
                                    <td style="min-width: 55%; text-align: right;">
                                        <strong>
                                            <?= isset($value['komponen_obat'][$i]) ? $value['komponen_obat'][$i]['qty_obat'] : '&nbsp;&nbsp;'; ?>
                                        </strong>
                                    </td>

                                    <td style="width: 45%; text-align: right;">
                                        <strong>
                                            &nbsp;
                                            <?= isset($value['komponen_obat'][$i]) ? $value['komponen_obat'][$i]['satuan_input'] : '&nbsp;&nbsp;'; ?>
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                <?php endfor; ?>

                <tr>
                    <td colspan="2" style="text-align: right;">
                        &nbsp;
                    </td>
                </tr>
            <?php else : ?>
                <tr>
                    <td style="width: 55%;padding-top: -7px">
                        <?php if (!empty($value['komponen_obat'])) : ?>
                            <strong><?= $value['nama_racikan'] ?></strong>
                        <?php else : ?>
                            <strong><?= substr($value['nama_obat'], 0, 50); ?></strong>
                        <?php endif ?>
                    </td>

                    <td style="width: 45%; text-align: right;">
                        <?php if (!empty($value['komponen_obat'])) : ?>
                            <table style="width: 100%; font-size: 7px">
                                <tr>
                                    <td style="min-width: 65%; text-align: right;">
                                        <strong>
                                            <?= $value['qty_racikan'] ?>
                                        </strong>
                                    </td>

                                    <td style="width: 35%; text-align: right;">
                                        <strong>
                                            &nbsp;
                                            <?= $value['satuan_racikan'] ?>
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        <?php else : ?>
                            <table style="width: 100%; font-size: 7px">
                                <tr>
                                    <td style="min-width: 65%; text-align: right;">
                                        <strong>
                                            <?= $value['qty_obat'] ?>
                                        </strong>
                                    </td>

                                    <td style="width: 35%; text-align: right;">
                                        <strong>
                                            &nbsp;
                                            <?= $value['satuan_input'] ?>
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        <?php endif ?>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <strong><?= $value['signa'] ?></strong>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <strong><?= $value['catatan'] ?></strong>
                    </td>
                </tr>

                <tr>
                    <td>
                        <?php if ($value['is_oral'] == 1) : ?>
                            <span>
                                <strong>PER ORAL</strong>
                            </span>
                        <?php endif ?>
                    </td>
                    <td style="text-align: right;vertical-align: top;">
                        <strong>EXPIRED:</strong>
                    </td>
                </tr>
            <?php endif ?>
            <tr>
                <td><?= $renderQrCode ?></td>
            </tr>
        </table>

        <?php
        if ($current != $pageCount) {
        ?>
            <pagebreak />
        <?php } ?>

    <?php endforeach ?>
<?php else : ?>
    <?= Yii::t("app", "Tidak ada data."); ?></td>
<?php endif ?>
