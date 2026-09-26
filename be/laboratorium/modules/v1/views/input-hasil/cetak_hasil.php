<?php
use Doco\components\DocoHelpers;
?>
<style type="text/css">
    body {
        font-family: Arial, sans-serif;
    }
    .tbl-bordered {
    border-collapse: collapse;
    }
    .table td {
        vertical-align:top;
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
    .no_border{
        border-bottom: 0px !important;
        border-top: 0px !important; 
        border-left: 0px !important; 
        border-right: 0px !important;
    }
    .v_bottom{
        vertical-align: bottom; 
    }
    .v_middle{
        vertical-align: middle; 
    }
    .is_title{
        padding-top:50px
    }
    .tbl-result td {
        line-height: 9px !important;
    }
    hr { margin: 2px; }
</style>
<?php
function checkVar($data){
  return !empty($data) ? $data : '--';
}
?>

<table cellpadding="5" class="tbl-bordered tbl-result" cellspacing="0" width="100%" style="font-size: 12">
    <thead>
        <tr>
            <!-- <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'JENIS PEMERIKSAAN') ?></th> -->
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'PEMERIKSAAN') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'HASIL') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'NILAI RUJUKAN') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'SATUAN') ?></th>
            <th align="left" style="text-align: left" style="border-left: 0px;border-right: 0px"><?= Yii::t('app', 'METODE') ?></th>
        </tr>
    </thead>
    <tbody>

    <?php $jenisPemeriksaan = ''; ?>
    <?php foreach ($details as $detail) : ?>
        <?php foreach ($detail as $pemeriksaan => $detail) : ?>
            <?php if (!$jenisPemeriksaan || $jenisPemeriksaan != $detail[0]['jenispemeriksaanlab_nama']) : ?>
                <tr style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px">
                    <td colspan=5><strong><?= str_replace(' ', '&nbsp;', $detail[0]['jenispemeriksaanlab_nama']) ?></strong></td>
                </tr>
                <?php $jenisPemeriksaan = $detail[0]['jenispemeriksaanlab_nama']; ?>
            <?php endif; ?>
            <tr>
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
                <?php foreach ($detail as $value) : ?>
                    <?php
                    if (!$value['is_verifikasi']) {
                        $value['hasil'] = null;
                    }
                    $suffix = DocoHelpers::getResultSuffix($value['nilai_min'],$value['nilai_max'],$value['nilai_rujukan'],$value['hasil']);
                    ?>
                    <td width="20%" style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px">
                        <?= $value['hasil'] . ' ' . $suffix ?>
                    </td>
                    <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $value['nilai_rujukan']; ?></td>
                    <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= $value['satuanlab_nama']; ?></td>
                    <td style="border-bottom: 0px;border-top: 0px; border-left: 0px ; border-right: 0px"><?= !empty($value['metode']) ? $value['metode'] : '-' ; ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    <tr>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td>
        <!-- <td style="border-bottom: 0px; border-left: 0px ; border-right: 0px"></td> -->
    </tr>
    </tbody>
</table>

<b><?= Yii::t('app', 'Expertise') ?></b>
<?php foreach ($queries as $query) : ?>
    <?php if (isset($query['data_hasil']) && isset($query['data_hasil']['expertise']) && $query['data_hasil']['expertise']) : ?>
    <p style="text-align: justify; margin-top: 0px"><?= nl2br($query['data_hasil']['expertise']) ?></p>
    <?php endif; ?>
<?php endforeach; ?>

<div style="display:none;">
    <h6 align="right">Diotorisasi Oleh, </h6>
    <br>
    <h6 align="right">(<?= !empty($nama_pegawai) ? $nama_pegawai : '-' ?>)</h6>
</div>
