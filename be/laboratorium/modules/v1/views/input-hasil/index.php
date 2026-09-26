<?php
use Doco\components\DocoHelpers;
?>
<style type="text/css">
    body {
        font-family: Arial, sans-serif;
    }
    .table td {
        vertical-align:top;
    }
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
    .bold {
        font-weight: bold;
    }
    .header-right {
        padding-right: 15px;
    }
    .tbl-result td {
        line-height: 9px !important;
    }
    hr { margin: 3px; }
</style>

<table cellpadding="5" class="tbl-bordered tbl-result" cellspacing="0" width="100%" style="font-size: 12">
    <thead>
        <tr>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'PEMERIKSAAN') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'HASIL') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'NILAI RUJUKAN') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'SATUAN') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'METODE') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php $jenisPemeriksaan = ''; ?>
    <?php foreach ($detail as $pemeriksaan => $detail) : ?>
        <?php if (!$jenisPemeriksaan || $jenisPemeriksaan != $detail[0]['jenispemeriksaanlab_nama']) : ?>
            <tr style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px">
                <td colspan=4><strong><?= str_replace(' ', '&nbsp;', $detail[0]['jenispemeriksaanlab_nama']) ?></strong></td>
            </tr>
            <?php $jenisPemeriksaan = $detail[0]['jenispemeriksaanlab_nama']; ?>
        <?php endif; ?>
        <tr>
            <!-- <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px">&nbsp;&nbsp;<?= str_replace(' ', '&nbsp;', $pemeriksaan) ?></td> -->
            <?php
            $isHead = false;
            if (substr(trim($pemeriksaan), -1, 1) == ':') {
                $isHead = true;
            }
            ?>
            <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px;">
                <?= $isHead ? "<strong>" : ""; ?>
                &nbsp;&nbsp;<?= str_replace(' ', '&nbsp;', $pemeriksaan) ?>
                <?= $isHead ? "</strong>" : ""; ?>
            </td>
            <?php
                foreach ($detail as $value) :

                if (!$value['is_verifikasi']) {
                    $value['hasil'] = null;
                }
            ?>
                <?php
                    $suffix = DocoHelpers::getResultSuffix($value['nilai_min'],$value['nilai_max'],$value['nilai_rujukan'],$value['hasil']);
                ?>
                <td width="20%" style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px">
                    <?= $value['hasil'] . ' ' . $suffix ?>
                </td>
                <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $value['nilai_rujukan']; ?></td>
                <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $value['satuanlab_nama']; ?></td>
                <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= !empty($value['metode']) ? $value['metode'] : '-' ; ?></td>
            <?php
                endforeach;
            ?>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
    </tr>
    </tbody>
</table>

<b><?= Yii::t('app', 'Expertise') ?></b>
<p style="text-align: justify; margin-top: 0px"><?= nl2br($expertise) ?></p>

<div style="display:none;">
    <h6 align="right">Diotorisasi Oleh, </h6>
    <br>
    <h6 align="right">(<?= !empty($nama_pegawai) ? $nama_pegawai : '-' ?>)</h6>
</div>
