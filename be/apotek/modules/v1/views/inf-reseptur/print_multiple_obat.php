
<style type="text/css">
    .tbl-bordered {
        font-size: 10px;
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
        table-layout: fixed;
        font-family: Arial, Helvetica, sans-serif;
        /* margin-top: 10px; */
        /* padding: 5px; */
    }
</style>
<?php
    $pageData = count($data['obat']);
    foreach ($data['obat'] as $key => $value) :
    $current = $key + 1;
?>
        <table class="tbl-bordered" style="width:100%;">
            <tr>
                <td style="font-size: 9px;text-align:left;" ><?= $data['header']['no_rm']?></td>
                <td style="font-size: 9px; text-align:right;" ><?= $data['header']['nama_ruangan']?></td>
            </tr>
            <tr>
                <td style="font-size: 9px;text-align:left;" ><?= $data['header']['nama_pasien']?></td>
                <td style="font-size: 9px; text-align:right;" ><?= $data['header']['tgl_lahir_pasien']?></td>
            </tr>
            <tr>
                <td style="font-size: 9px;text-align:left;" ><?= $data['header']['ket_waktu_obat']?></td>
            </tr>
            <tr>
                <td colspan="2">
                    <hr>
                </td>
            </tr>
            <?php
                foreach ($value as $nama_obat) :
            ?>
                <tr>
                    <td style="font-size: 10px;" colspan="2"><?= $nama_obat  ?></td><br>
                </tr>
            <?php endforeach ?>
        </table>
        <?php
            if($key < $pageData){
        ?>
        <pagebreak />
        <?php } ?>
<?php endforeach ?>
