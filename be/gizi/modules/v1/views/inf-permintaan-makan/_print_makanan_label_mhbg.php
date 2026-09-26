<?php
    use Doco\components\DocoConstants;
    use Doco\components\DocoHelpers;

?>

<style>
    .fz-9 {
        font-size: <?=$body_font?>px;
    }

    .fw-b {
        font-weight: bold;
        font-family: Arial, Helvetica, sans-serif;
    }
    .center {
        text-align : center;
    }

    /* .label-size-half {
        width: 60%;
        height: 16.5%;
    } */
</style>
<?php if (!empty($detail)){?>
<div style="background-color: white; margin-bottom: 10px;">
<?php for ($i = 0; $i < $jumlah; $i++) { ?>
<?php $mod = $i % 3; ?>
    <div style="float:left; width:33%;" >
        <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
            <tbody>
                <tr>
                     <td colspan="3" class="center"><span class="fz-9 fw-b"><?= $kamar?> / <?= DocoHelpers::cutSentence($nama_pasien, 16)?>(<?= $norm?>) </span></td>
                </tr>
                <tr>
                     <td colspan="3" class="center"><span class="fz-9 fw-b"><?= $jk?> / <?= $umur?></span></td>
                </tr>
                <tr>
                     <td colspan="3" class="center"><span class="fz-9 fw-b"><?= $catatan?></span></td>
                </tr>
            </tbody>
        </table>
    </div>
<?php if ($mod == 2) { ?>
</div>
<div style="background-color: white; margin-bottom: 10px;">
<?php } ?>

<?php } ?>
</div>
<?php } else {  ?>

    <div style="background-color: white;" class="div-border-red">
        <div style="background-color: white; margin-left: 5%;  " class="div-border-green label-size-half">
            <table border="0" cellpadding="1" cellspacing="1" style="width:100%;">
                <tbody>
                    <tr>
                            <td colspan="3" class="center"><span class="fz-11 fw-b">Tidak Ada Data</span></td>
                    </tr>
                </tbody>

            </table>
        </div>
    </div>
<?php } ?>