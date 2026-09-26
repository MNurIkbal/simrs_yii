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
            <h5 class="panel-title"><b>Formulir Nutrional Risk Score</b></h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-12">
                    <table class="table table-bordered">
                        <thead>
                          <tr class="bg-inverse">
                                <th class="text-left" style="width: 10%">No.</th>
                                <th class="text-left" style="width: 30%">Variabel</th>
                                <th class="text-left" style="width: 10%">Skor</th>
                                <th class="text-left" style="width: 50%">Pengertian</th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 0;
                                foreach ($skrining_nrs_anak as $key => $data) :
                                    $no++;
                            ?>

                                <?php foreach ($data['data']  as $key_skrining_anak => $skrining_nrs_skor): ?>
                                    <tr>

                                        <?php if(array_keys($data['data'])[0] == $key_skrining_anak) : ?>
                                            <td rowspan="<?= count($data['data']) ?>"> <?= ($no) ?> </td>
                                            <td rowspan="<?= count($data['data']) ?>"> <?= $data['nama'] ?> </td>
                                        <?php endif; ?>

                                        <td>
                                            <div>
                                                <?= Html::activeRadio($model, "skrining[anak][$data[id]]", ['label' => $skrining_nrs_skor['skor'], 'value' => $skrining_nrs_skor['skor'], 'id' => 'skor-anak-' . $key . '-'.$key_skrining_anak, 'class' => 'skor-skrining-anak']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <?= $skrining_nrs_skor['additional_data'] ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>

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

    <div class='header-form'>
        <h4></h4>
    </div>
    <div class="row">
        <div class="col-6 col-sm-3">
            <?= $form->field($model, 'skor[anak][total]')->textInput([
                'class' => 'form-control input-sm',
                'rows' => '2',
                'readonly' => true,
            ])->label('Total Skor' ) ?>
        </div>
        <div class="col-6 col-sm-9">
            <?= $form->field($model, 'skor[anak][keterangan]')->textInput([
                'class' => 'form-control input-sm',
                'rows' => '2',
                'readonly' => true,
            ])->label('Keterangan' ) ?>
        </div>
    </div>
</div>
