<?php
    use Doco\components\DocoConstants;
    use Doco\components\DocoHelpers;
?>

<style>
    .fz-9 {
        font-size: 9px;
    }

    .fz-10 {
        font-size: 10px;
    }

    .fz-12 {
        font-size: 11.2px;
    }

    .fz-11 {
        font-size: 12px;
    }

    .fw-b {
        font-weight: bold;
        font-family: Arial, Helvetica, sans-serif;
    }
    .right {
        text-align : right;
    }

    /* .label-size-half {
        width: 60%;
        height: 16.5%;
    } */
</style>
<?php for ($i = 0; $i < $jumlah; $i++) { ?>
<?php
    $displayNama = DocoHelpers::cutSentence($header['nama_pasien'], 20);
    if($i == 0) {
        $pos = $margin_top_1;
    } else {
        $pos = $margin_top_2;
    }
    $dob = !empty($header['tanggal_lahir']) ? $header['tanggal_lahir'] : '-';
    $no_tempattidur = !empty($header['no_tempattidur']) ? $header['no_tempattidur'] : '-';
?>
<div style="background-color: white;" class="div-border-red">
    <div style="background-color: white; margin-left: 5%;  margin-top: <?= $pos ?>" class="div-border-green label-size-half">
        <table border="0" cellpadding="1" cellspacing="1" style="width:100%;">
            <thead>  
                <tr>
                        <th width="33%" style="visibility:hidden;"></th>
                        <th width="33%" style="text-align:center;"><span class="fz-11 fw-b">Pagi/Siang/Malam</span></th>
                        <th width="33%" class="right"><span class="fz-11  fw-b"><?= $header['ruangan_nama']?></span></th>
                </tr>
            </thead>
            <tbody>
                <!-- <tr>
                        <td colspan="3" class="center"><span class="fz-11 fw-b"><?= $tgl_cetak ?></span></td>
                </tr> -->
                <tr>
                        <td></td>
                </tr>
                <tr>
                        <td></td>
                </tr>
                <tr>
                        <td></td>
                </tr>
                <tr>
                        <td></td>
                </tr>
                <tr>
                        <td colspan="1"><span class="fz-12 fw-b">Nama</span></td>
                        <td colspan="2" class="fz-12 fw-b">: <?= $displayNama?> </td>
                </tr>
                <tr>
                        <td colspan="1"><span class="fz-10 fw-b">Kelas/Kamar/Bed</span></td>
                        <td colspan="2" class="fz-12 fw-b">: <?= $header['kelaspelayanan_nama']?>/<?= $header['kamarruangan_nokamar']?>/<?= $no_tempattidur?> </td>
                </tr>
                <tr>
                        <td colspan="1"><span class="fz-10 fw-b">Tanggal Lahir</span></td>
                        <td colspan="2" class="fz-12 fw-b">: <?= $dob?> </td>
                </tr>
                <tr>
                        <td colspan="1"><span class="fz-12 fw-b">No RM</span></td>
                        <td colspan="2" class="fz-12 fw-b">: <?= $header['no_rekam_medik']?> </td>
                </tr>
                <tr>
                        <td colspan="1"><span class="fz-12 fw-b">Diet</span></td>
                        <td colspan="2" class="fz-12 fw-b">: <?= isset($detail['jenisdiet_nama']) ? $detail['jenisdiet_nama'] : ' - '?> </td>

                </tr>
            </tbody>

        </table>
        <?php if($jumlah - $i !== 1){?>
        <pagebreak>
        <?php }else{?>
        <?php }?>
    </div>
</div>

<?php } ?>
