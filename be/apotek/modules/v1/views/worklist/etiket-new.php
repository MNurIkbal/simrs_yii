<?php
/**
 * @author : Iqbal Ramadhani (iqbal.ramdhani@sirs.co.id)
 * A product of PT. Citra Raya Nusatama
 * Powered by Sirs
 */
?>

<style>
table {
    font-size: 11px;
    border-collapse: collapse;
    border-spacing: 0;
    width: 100%;
    table-layout: fixed;
    font-family: Arial, Helvetica, sans-serif;
    line-height: 100%;
}

.judul {
    margin-top: 11px;
    margin-bottom: -10px;
    font-size: 10px;
    font-family: Arial, Helvetica, sans-serif;
    text-align: center;
}

hr.new-hr{
    border-top: 10px dashed red;
}

.hr{
    text-align: center;
    margin-left: -20x;
    margin-right: -20px;
    margin-top: -8px;
    margin-bottom: -20px;
}

.pasien {
    margin-bottom: 10px;
}

</style>

<?php
    if(!empty($detail)):
        $pageCount = count($detail);
            foreach ($detail as $key => $value) :
            $current = $key + 1;
            $type = $value['is_oral'] == 1 ? 'OBAT ORAL' : 'OBAT NON ORAL' ;
?>

<div class="judul"><strong>Instalasi Farmasi @nama_rs@</strong></div>
<p class="hr">========================</p>
<table border="0">
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: right;">
            <strong><?= date('d M Y', strtotime( date('Y-m-d') )) ?></strong>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <strong><?= $pasien['nama_pasien'];?> (<?= $pasien['no_rm']; ?>)</strong>
        </td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">
            <strong><?= $value['nama_obat'] ?></strong>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <strong><?= $value['qty_obat'] ?>&nbsp;<?= $value['satuan_input'] ?></strong>
        </td>
    </tr>

    <tr>
        <td colspan="2">
            <strong><?= $value['signa'] ?></strong>
        </td>
    </tr>
    <tr>
        <td colspan="2"><strong><?= $value['catatan'] ?></strong></td>
    </tr>
    <tr>
        <td colspan="2"><strong><?= $value['expired_date'] ?></strong></td>
    </tr>
</table>

<?php
    if ($current != $pageCount) {
?>
<pagebreak />
<?php } ?>

<?php endforeach ?>
<?php else: ?>
       <?= Yii::t("app", "Tidak ada data.");?></td>
<?php endif ?>

