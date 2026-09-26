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
<table id="lap-cara-pulang-pasien" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">NO</th>
                <th align="center" >NO REGISTRASI</th>
                <th align="center" >TANGGAL PENDAFTARAN</th>
                <th align="center" >TANGGAL PULANG</th>
                <th align="center" >NO REKAM MEDIK</th>
                <th align="center" >NAMA PASIEN</th>
                <th align="center" >INSTALASI</th>
                <th align="center" >RUANGAN</th>
                <th align="center" >CARA PULANG</th>
                <th align="center" >RUMAH SAKIT DIRUJUK</th>
                <th align="center" >KONDISI PULANG</th>
                <th align="center" >CARA BAYAR</th>
            </tr>
        </thead>
            <tbody>
            <?php
                $no = 1;
                foreach ($data as $value) :
            ?>
                <tr class="td-inverse">
                    <td align="center"><?= $no++ ?></td>
                    <td align="center"><?= $value['no_registrasi'] ?></td>
                    <td align="center"><?= date('d F Y H:i:s', strtotime($value['tgl_pendaftaran'])) ?></td>
                    <td align="center"><?= date('d F Y H:i:s', strtotime($value['tgl_pulang'])) ?></td>
                    <td align="center"><?= $value['no_rekam_medik'] ?></td>
                    <td align="center"><?= $value['nama_pasien'] ?></td>
                    <td align="center"><?= $value['instalasi_nama'] ?></td>
                    <td align="center"><?= $value['ruangan_nama'] ?></td>
                    <td align="center"><?= $value['cara_pulang'] ?></td>
                    <td align="center"><?= $value['rumahsakit_rujukan'] ? $value['rumahsakit_rujukan'] : '-' ?></td>
                    <td align="center"><?= $value['kondisi_pulang'] ? $value['kondisi_pulang'] : '-' ?></td>
                    <td align="center"><?= $value['cara_bayar'] ?></td>
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
</table>