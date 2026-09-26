<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-23 10:37:56
 */

use yii\web\View;
use app\components\DocoHelpers;
?>

<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Formulir RL 5.2') ?></h3>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Kunjungan Rawat Jalan') ?></h3>

<div class="form-group">
    <div class="col-lg-12">
        <h5><?= Yii::t('fe', 'Kode RS') ?> : <?= @$profil['nokode_rumahsakit'] ?></h5>
        <h5><?= Yii::t('fe', 'Nama RS') ?> : <?= @$profil['nama_rumahsakit'] ?></h5>
        <h5><?= Yii::t('fe', 'Bulan') ?>   : <?= @$textBulan ?></h5>
        <h5><?= Yii::t('fe', 'Tahun') ?>   : <?= @$tahun ?></h5>
        <div class="table-responsive">
            <table id="tb-rl-5-2" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th><?= \Yii::t("fe", "No.") ?></th>
                        <th><?= \Yii::t("fe", "Jenis Kegiatan") ?></th>
                        <th><?= \Yii::t("fe", "Jumlah") ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach($data as $value){ 
                    ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= @$value['jeniskasuspenyakit_nama'] ?></td>
                        <td><?= @$value['jumlah'] ?></td>
                    </tr>
                    <?php 
                            $no++;
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