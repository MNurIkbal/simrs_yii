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
  tr:nth-child(even) {
    background-color: #eee;
  }
  tr:nth-child(odd) {
    background-color: #fff;
  }  
</style>
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="2" width="100%" border="0">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("app", "Tanggal Pendaftaran");?></th>
                        <th><?=\Yii::t("app", "Nomor Pendaftaran");?></th>
                        <th><?=\Yii::t("app", "Nomor Rekam Medik");?></th>
                        <th><?=\Yii::t("app", "Nama Pasien");?></th>
                        <th><?=\Yii::t("app", "Jenis Kelamin");?></th>
                        <th><?=\Yii::t("app", "Penjamin");?></th>
                        <th><?=\Yii::t("app", "Dokter Jaga");?></th>
                        <th><?=\Yii::t("app", "Dokter Penanggung Jawab");?></th>
                        <th><?=\Yii::t("app", "Status Pasien");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($detail as $value) :
                    ?>
                        <tr class="td-inverse">
                            <td align="center"><?= $no ?></td>
                            <td align="center"><?= date('d M Y H:i:s', strtotime($value['tgl_pendaftaran']))  ?></td>
                            <td align="center"><?= $value['no_pendaftaran'] ?></td>
                            <td align="center"><?= $value['no_rekam_medik'] ?></td>
                            <td align="center"><?= $value['nama_pasien'] ?></td>
                            <td align="center"><?= $value['jenis_kelamin'] ?></td>
                            <td align="center"><?= $value['penjamin_nama'] ?></td>
                            <td align="center"><?= $value['dokter_jaga'] ?></td>
                            <td align="center"><?= $value['dokter'] ?></td>
                            <td align="center"><?= $value['status_periksa'] ?></td>
                        </tr>
                    <?php
                        $no++;
                        endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>