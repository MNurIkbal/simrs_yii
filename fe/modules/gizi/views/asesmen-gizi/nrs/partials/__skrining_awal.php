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

<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title"><b>Screening Gizi Awal</b></h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-12">
                    <table class="table table-borderless">
                        <thead>
                          <tr class="bg-inverse">
                                <th class="text-left" style="width: 10%">No.</th>
                                <th class="text-left" style="width: 70%">Variable</th>
                                <th class="text-left" style="width: 20%">Keterangan</th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 0;
                                foreach ($skriningAwal as $key => $keterangan) :
                                    $no++;
                            ?>
                                <tr>
                                    <td><?= ($no) ?></td>
                                    <td><?= $keterangan ?></td>
                                    <td>
                                        <?= Html::activeRadio($model, "keterangan[$key]", ['label' => 'Tidak', 'value' => '0', 'id' => 'keterangan-' . $key . '-0']) ?>
                                        <?= Html::activeRadio($model, "keterangan[$key]", ['label' => 'Ya', 'value' => '1', 'id' => 'keterangan-' . $key . '-1']) ?>
                                    </td>
                                </tr>
                            <?php
                                endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="skrining-warning" class="row hidden">
                <div class="alert alert-warning border-danger text-center text-danger" role="alert">
                    Mohon lakukan asesmen ulang 1 minggu kemudian
                </div>
            </div>
        </div>
    </div>
</div>