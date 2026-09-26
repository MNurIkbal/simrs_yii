<?php
use app\components\DocoHelpers;

use kartik\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Respon Terapi</h6>
    </div>
    <div class="panel-body">
      <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%">No</th>
                            <th width="25%">Tanggal Pengecekan</th>
                            <th width="70%">Respon Terapi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listRespon as $idx => $respon) : ?>
                        <tr>
                            <td><?= $idx + 1?></td>
                            <td><?= isset($respon['tgl_respon']) ? DocoHelpers::convDateTime($respon['tgl_respon'], false, true) : '-' ?>
                                <hr style="margin-top: 3px; margin-bottom: 3px;">
                                <?= isset($respon['kelompokpegawai_nama']) ? $respon['kelompokpegawai_nama'] : '-' ?>
                                <br>
                                <?= isset($respon['nama_pegawai']) ? $respon['nama_pegawai'] : '-' ?>
                            </td>
                            <td><?= chunk_split($respon['respon'], 100, "<br>\r");?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>