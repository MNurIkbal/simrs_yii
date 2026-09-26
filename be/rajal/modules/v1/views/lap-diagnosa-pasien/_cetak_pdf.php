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
  /*tr:nth-child(even) {
    background-color: #eee;
  }
  tr:nth-child(odd) {
    background-color: #fff;
  } */ 
</style>

<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table style="font-size: 11px;" cellSpacing="2" width="100%" border="0">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th width="20%"><?=\Yii::t("app", "Tanggal Diagnosa");?></th>
                        <th width="18%"><?=\Yii::t("app", "No. Pendaftaran / No. Rekam Medik");?></th>
                        <th width="15%"><?=\Yii::t("app", "Nama Pasien");?></th>
                        <th ><?=\Yii::t("app", "Klasifikasi Diagnosa");?></th>
                        <th><?=\Yii::t("app", "Kode Diagnosa");?></th>
                        <th ><?=\Yii::t("app", "Nama Diagnosa");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr class="td-inverse">
                            <td align="left"><?= $no ?></td>
                            <td align="left"><?= $value['tgl_diagnosa'] ?></td>
                            <td align="left"><?= $value['no_pendaftaran_rm'] ?></td>
                            <td align="left"><?= $value['nama_pasien'] ?></td>
                            <td align="left"><?= $value['klasifikasidiagnosa_nama'] ?></td>
                            <td align="left"><?= $value['diagnosa_kode'] ?></td>
                            <td align="left"><?= $value['exp_nama_diagnosa'] ?></td>
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