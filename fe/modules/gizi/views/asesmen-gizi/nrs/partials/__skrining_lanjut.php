<?php
use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\web\View;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
?>
<style>
    .gizi {width: 57px}
</style>
<div class="skrining-lanjut hidden">
    <?php foreach ($skriningLanjut as $keterangan) : ?>
    <div class="row form-row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><b><?= $keterangan['nama'] ?></b></h5>
            </div>
            <div class="panel-body">
                <div class="row form-row">
                    <div class="col-sm-12">
                        <table class="table table-borderless">
                            <thead>
                              <tr class="bg-inverse">
                                <th class="text-left" style="width: 10%">No.</th>
                                <th class="text-left" style="width: 70%">Variable</th>
                                <th class="text-left" style="width: 20%">Skor</th>
                              </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $no = 0;
                                    $ketId = $keterangan['id'];
                                    foreach ($keterangan['data'] as $key => $detail) :
                                        $no++;
                                        $skor = $detail['skor'];
                                ?>
                                    <tr>
                                        <td><?= ($no) ?></td>
                                        <td><?= $detail['skriningnrs_nama'] ?></td>
                                        <td>
                                            <?= Html::activeRadio($model, "skrining[dewasa][$ketId]", ['label' => $skor, 'value' => $skor, 'id' => "skrining-$ketId-$no", "data" => ["skor" => $skor, "ket-id" => $ketId] ]) ?>
                                        </td>
                                    </tr>
                                <?php
                                    endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
