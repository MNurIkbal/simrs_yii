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
                        <th><?=\Yii::t("app", "Tanggal Pulang");?></th>
                        <th><?=\Yii::t("app", "Nomor Pendaftaran");?></th>
                        <th><?=\Yii::t("app", "Nomor Rekam Medik");?></th>
                        <th><?=\Yii::t("app", "Nomor Telepon");?></th>
                        <th><?=\Yii::t("app", "Nama Pasien");?></th>
                        <th><?=\Yii::t("app", "Ruangan");?></th>
                        <th><?=\Yii::t("app", "Jenis Kelamin");?></th>
                        <th><?=\Yii::t("app", "Penjamin");?></th>
                        <th><?=\Yii::t("app", "Dokter");?></th>
                        <th><?=\Yii::t("app", "Cara Pulang");?></th>
                        <th><?=\Yii::t("app", "Dipulangkan Oleh");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr class="td-inverse">
                            <td align="center"><?= $no ?></td>
                            <td align="center"><?= isset($value['tgl_pendaftaran']) ? date('d M Y H:i:s', strtotime($value['tgl_pendaftaran'])) : ' - ' ?></td>
                            <td align="center"><?= isset($value['tglpasienpulang']) ? date('d M Y H:i:s', strtotime($value['tglpasienpulang'])) : ' - ' ?></td>
                            <td align="center"><?= isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['no_telepon_pasien']) ? $value['no_telepon_pasien'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['nama_pasien']) ? $value['nama_pasien'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['ruangan_nama']) ? $value['ruangan_nama'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['jenis_kelamin']) ? $value['jenis_kelamin'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['penjamin_nama']) ? $value['penjamin_nama'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['dokter']) ? $value['dokter'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['carakeluar_nama']) ? $value['carakeluar_nama'] : ' - ' ?></td>
                            <td align="center"><?= isset($value['petugas_pemulang_nama']) ? $value['petugas_pemulang_nama'] : ' - ' ?></td>
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
