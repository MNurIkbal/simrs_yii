<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-02-11 16:25:11
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-02-13 11:24:01
 */

use yii\web\View;
use app\components\DocoHelpers;
?>

<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Formulir RL 5.3') ?></h3>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Daftar 10 Besar Penyakit Rawat Inap') ?></h3>

<div class="form-group">
    <div class="col-lg-12">
        <h5><?= Yii::t('fe', 'Kode RS') ?> : <?= @$profil['nokode_rumahsakit'] ?></h5>
        <h5><?= Yii::t('fe', 'Nama RS') ?> : <?= @$profil['nama_rumahsakit'] ?></h5>
        <h5><?= Yii::t('fe', 'Tahun') ?>   : <?= @$tahun ?></h5>
        <div class="table-responsive">
            <table id="rl-5_4" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th rowspan="2" class="text-center"><?=\Yii::t("fe", "No. Urut");?></th>
                            <th rowspan="2" class="text-center"><?=\Yii::t("fe", "KODE ICD 10");?></th>
                            <th rowspan="2" class="text-center"><?=\Yii::t("fe", "DESKRIPSI");?></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "Pasien Keluar Hidup Menurut Jenis Kelamin");?></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "Pasien Keluar Mati Menurut Jenis Kelamin");?></th>
                            <th rowspan="2" class="text-center"><?=\Yii::t("fe", "Total <br>(Hidup Dan Mati)");?></th>
                        </tr>
                        <tr class="bg-inverse">
                            <th class="text-center"><?=\Yii::t("fe", "Laki-Laki");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Perempuan");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Laki-Laki");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Perempuan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no_urut = 1;
                            foreach($data as $content){ 
                        ?>
                        <tr>
                            <td><?=$no_urut?></td>
                            <td><?=@$content['kode_diagnosa']?></td>
                            <td><?=@$content['nama_diagnosa']?></td>
                            <td><?=@$content['jumlah_lakihidup']?></td>
                            <td><?=@$content['jumlah_perempuanhidup']?></td>
                            <td><?=@$content['jumlah_lakimati']?></td>
                            <td><?=@$content['jumlah_perempuanmati']?></td>
                            <td><?=@$content['jumlah_hidup_mati']?></td>
                        </tr>
                        <?php 
                                $no_urut++;
                            }
                        ?>
                    </tbody>
                </table>
        </div>
    </div>
</div>

<script type="text/javascript">
var table;
$(document).ready(function() {
    table = $("#tb-rl-5-2").DataTable({
        "language": {
            "search": "Pencarian&nbsp;:&nbsp;"
        },
        ordering : false,
        searching : false,
        paging: false,
    });
});
</script>