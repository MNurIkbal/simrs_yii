<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style>
    body{
        font-family: tahoma;
    }
    .fz-9 {
        font-size: 9px;
    }
    .fz-10 {
        font-size: 10px;
    }
    .fz-11 {
        font-size: 11px;
    }
    .fz-12 {
        font-size: 12px;
    }
    .fz-13 {
        font-size: 13px;
    }
    .fw-b {
        font-weight: bold;
    }

    .header-label1 {
        font-size:13px; font-weight: bold; border-right: 0
    }
    .header-label2 {
        font-size:10px; font-weight: bold; border-right: 0
    }
    .bg-inverse th {
        padding: 0px;
        text-align: center;
    }
    .td-inverse td {
        padding: 0px;
        text-align: left;
    }
</style>
<div>
  <?php foreach($datas as $key => $data){ ?>

    <table class="table table-striped table-condensed table-hover" style="width:100%;margin: 1px;" border="0">
        <thead>
            <tr class="bg-inverse">
                <th width="100%"  align="center" class="header-label2 fz-10">
                    <?php echo @$data['nama_ruangan'] ?>
                </th>
            </tr>
            <tr>
                <th width="100%"  align="center" class="header-label2 fz-10">
                    <?php echo 'Antrian No Ke : '.@$data['no_antrian'] ?>
                </th>
            </tr>
            <tr>
                <th width="100%"  align="center" class="header-label2 fz-13">
                    <?php echo @$data['no_rekam_medik'] ?>
                </th>
            </tr>
            <tr>
                <td width="100%"  align="center" class="fz-9">
                    <?php echo @$data['tgl_pendaftaran'].' '.@$data['jam_kunjungan']# ?>
                </td>
            </tr>
        </thead>
    </table>

    <br>

    <table class="table table-striped table-condensed table-hover" style="width:100%;" border="0">
        <tbody class="td-inverse">
            <tr>
                <td><span style="font-size:9px">NIK</span></td>
                <td>:</td>
                <td><span style="font-size:9px"><?php echo @$data['nik'] ?></span></td>
            </tr>
            <tr>
                <td><span style="font-size:9px">NAMA PASIEN</span></td>
                <td>:</td>
                <td><span style="font-size:9px"><?php echo @$data['nama_depan'].' '.@$data['nama_pasien']# ?></span></td>
            </tr>
            <tr>
                <td><span style="font-size:9px">JENIS KELAMIN</span></td>
                <td>:</td>
                <td><span style="font-size:9px"><?php echo @$data['jk'] ?></span></td>
            </tr>
            <tr>
                <td><span style="font-size:9px">TGL LAHIR</span></td>
                <td>:</td>
                <td><span style="font-size:9px"><?php echo @$data['tgl_lahir'] ?></span></td>
            </tr>
            <tr>
                <td><span style="font-size:9px">UMUR</span></td>
                <td>:</td>
                <td><span style="font-size:9px"><?php echo @$data['umur'] ?></span></td>
            </tr>
            <tr>
                <td><span style="font-size:9px">DOKTER</span></td>
                <td>:</td>
                <td><span style="font-size:9px"><?php echo @$data['nama_dokter'] ?></span></td>
            </tr>
            <tr>
                <td><span style="font-size:9px">JENIS PASIEN</span></td>
                <td>:</td>
                <td><span style="font-size:9px"><?php echo @$data['cara_bayar'] ?></span></td>
            </tr>
            <tr>
                <td><span style="font-size:9px">STATUS</span></td>
                <td>:</td>
                <td><span style="font-size:9px"><?php echo @$data['status_pasien'] ?></span></td>
            </tr>
        </tbody>
    </table>

    <br>

    <table class="table table-striped table-condensed table-hover" style="width:100%;" border="0">
        <tbody>
            <tr>
                <td width="40%"><span style="font-size:9px">Petugas</span></td>
                <td>&nbsp;</td>
                <td><span style="font-size:9px">&nbsp;</span></td>
            </tr>
            <tr>
                <td><span style="font-size:9px;font-weight: bold" ><?php echo @$data['loginpemanai_nama'] ?></span></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>


  <?php }?>
</div>
