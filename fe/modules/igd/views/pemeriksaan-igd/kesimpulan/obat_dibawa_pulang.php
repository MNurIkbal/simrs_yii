<?php

/**
 * @Author: ilhamsyah
 * @Date:   2021-11-28 11:16:29
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\ArrayHelper;
?>

<?php 
?>

<div class="panel panel-default">
    <div class="panel-heading">

        <div id="head_panel_obat_dibawa_pulang"  class="head_obat_dibawa_pulang  form-group">
            <label class="obat_dibawa_pulang col-md-1">

            </label>
            <div class="col-md-11">
                <h5 class="panel-title"><?=Yii::t('fe','Obat Saat Pulang')?></h5>
            </div>
        </div>
    </div>

    <div id="col_obat_dibawa_pulang" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="table-responsive">
                <table id="tabel-reseptur" class="table table-striped table-hover datatable-basic dataTable" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?=Yii::t('fe', 'Nama obat')?></th>
                            <th><?=Yii::t('fe', 'Jumlah')?></th>
                            <th><?=Yii::t('fe', 'Dosis')?></th>
                            <th><?=Yii::t('fe', 'Cara Pemberian')?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no = 0;
                    if (!empty($data_obat)) : ?>
                        <?php foreach ($data_obat as $key => $value) : $no++;
                        ?>
                            <tr>
                                <td><?= $no; ?></td>
                                <td><?= !empty($value['obatalkes_nama']) ? $value['obatalkes_nama'] : ''; ?></td>
                                <td><?= $qty_reseptur = !empty($value['qty_reseptur']) ? $value['qty_reseptur'] : ''; ?> <?= $satuan_kecil = !empty($value['satuan_kecil']) ? $value['satuan_kecil'] : ''; ?> </td>
                                <td><?= $signa_nama = !empty($value['signa_nama']) ? $value['signa_nama'] : ''; ?></td>
                                <td><?= $qty_reseptur = !empty($value['nama_rute']) ? $value['nama_rute'] : ''; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php else :?>
                            <tr>
                                <td colspan="9" class="text-center">Data Obat Tidak Tersedia</td>
                            </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>
            <br>
        </div>
    </div>
</div>



