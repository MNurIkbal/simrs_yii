<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style>
    table {
        border-collapse: collapse;
    }
    .bg-inverse th, .td-inverse td {
        border: 1px solid #000000;
        padding: 10px;
        text-align: left;
    }
  /* tr:nth-child(even) {
    background-color: #eee;
  }
  tr:nth-child(odd) {
    background-color: #fff;
  }   */
</style>
<table id="lap-rekap-pasien-batal-registrasi" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">NO</th>
                <th align="center" >NO REGISTRASI</th>
                <th align="center" >NO REKAM MEDIK</th>
                <th align="center" >NAMA PASIEN</th>
                <th align="center" >INSTALASI</th>
                <th align="center" >RUANGAN</th>
                <th align="center" >ALASAN BATAL</th>
                <th align="center" >PETUGAS</th>
            </tr>
        </thead>
            <tbody>
            <?php
                $no = 1;
                foreach ($data as $value) :
                    $tgl_pulang = explode(" ", date('d-m-Y H:i:s', strtotime($value['tgl_batal'])));
            ?>
                <tr class="td-inverse">
                    <td align="center"><?= $no++ ?></td>
                    <td align="center"><?= $value['no_registrasi'] ?></td>
                    <td align="center"><?= $value['no_rekam_medik'] ?></td>
                    <td align="center"><?= $value['nama_pasien'] ?></td>
                    <td align="center"><?= $value['instalasi_nama'] ?></td>
                    <td align="center"><?= $value['ruangan_nama'] ?></td>
                    <td align="center"><?= $value['alasan_batal'] ?></td>
                    <td align="center">
                        <?= $value['nama_petugas'].'<br/>'.$tgl_pulang[0].'<br/>'.$tgl_pulang[1] ?>
                    </td>
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
</table>