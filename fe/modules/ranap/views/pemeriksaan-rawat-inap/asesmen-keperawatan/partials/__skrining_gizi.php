<?php

use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
?>
<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Skrining Gizi <span style="color:red; "><sup>*</sup></span></h5>
        </div>
        <div class="panel-body">
            <p class="header-form" style="font-weight: normal;">Pasien Dewasa</p>
            <p class="header-form">1. Apakah pasien mengalami penurunan berat badan yang tidak diinginkan dalam 6 bulan terakhir?</p>
            <div class="row form-row">
                <div class="col-sm-12">
                    <table class="table-nutrition">
                        <tr>
                            <td>a. Tidak penurunan berat badan</td>
                            <td><?= Html::activeRadio($model, 'nutrisi_1b', ['class' => 'nutrisi-check', 'label' => '0', 'data-score' => 0, 'value' => '0', 'id' => 'nutrisi_1a-0']) ?></td>
                        </tr>
                        <tr>
                            <td>b. Tidak yakin / tidak tahu /terasa baju lebih longgar</td>
                            <td><?= Html::activeRadio($model, 'nutrisi_1b', ['class' => 'nutrisi-check', 'label' => '2', 'data-score' => 2, 'value' => '2', 'id' => 'nutrisi_1a-2']) ?></td>
                        </tr>
                        <tr>
                            <td>c. Jika ya, berapa penurunan berat badan tersebut</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>1-5 Kg</td>
                            <td><?= Html::activeRadio($model, 'nutrisi_1b', ['class' => 'nutrisi-check', 'label' => '1', 'data-score' => 1, 'value' => '1', 'id' => 'nutrisi_1b-1']) ?></td>
                        </tr>
                        <tr>
                            <td>6-10 Kg</td>
                            <td><?= Html::activeRadio($model, 'nutrisi_1b', ['class' => 'nutrisi-check', 'label' => '2', 'data-score' => 2, 'value' => '2', 'id' => 'nutrisi_1b-2']) ?></td>
                        </tr>
                        <tr>
                            <td>10-15 Kg</td>
                            <td><?= Html::activeRadio($model, 'nutrisi_1b', ['class' => 'nutrisi-check', 'label' => '3', 'data-score' => 3, 'value' => '3', 'id' => 'nutrisi_1b-3']) ?></td>
                        </tr>
                        <tr>
                            <td>>15 Kg</td>
                            <td><?= Html::activeRadio($model, 'nutrisi_1b', ['class' => 'nutrisi-check', 'label' => '4', 'data-score' => 4, 'value' => '4', 'id' => 'nutrisi_1b-4']) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <p class="header-form">2. Apakah asupan makan berkurang karena berkurangnya nafsu makan?</p>
            <div class="row form-row">
                <div class="col-sm-12">
                    <table class="table-nutrition">
                        <tr>
                            <td>a. Ya</td>
                            <td><?= Html::activeRadio($model, 'nutrisi_2', ['class' => 'nutrisi-check', 'label' => '1', 'data-score' => 1, 'value' => '1', 'id' => 'nutrisi_2-1']) ?></td>
                        </tr>
                        <tr>
                            <td>b. Tidak</td>
                            <td><?= Html::activeRadio($model, 'nutrisi_2', ['class' => 'nutrisi-check', 'label' => '0', 'data-score' => 0, 'value' => '0', 'id' => 'nutrisi_2-0']) ?></td>
                        </tr>
                        <tfoot>
                            <tr>
                                <td>Total Skor</td>
                                <td id="score-section">0</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <p class="header-form">3. Pasien dengan diagnosa khusus</p>
            <div class="row form-row">
                <?= Dhtml::trueFalseRadio($model, 'diagnosa_khusus', [
                    'childDependent' => [
                        'class' => 'diagnosa_khusus--dependent'
                    ]
                ]) ?>
                <?= DHtml::multipleCheckbox([
                    'model' => $model,
                    'fieldName' => 'jenis_diagnosa_khusus',
                    'data' => $arrayConfig['jenis_diagnosa_khusus']['first_row'],
                    'class' => 'diagnosa_khusus--dependent default-disabled'
                    // 'colSize' => '2'
                ]);
                ?>
            </div>
            <div class="row form-row">
                <div class="col-sm-2">&nbsp;</div>
                <?= DHtml::multipleCheckbox([
                    'model' => $model,
                    'fieldName' => 'jenis_diagnosa_khusus',
                    'data' => $arrayConfig['jenis_diagnosa_khusus']['second_row'],
                    'class' => 'diagnosa_khusus--dependent default-disabled'
                    // 'colSize' => '2' 
                ]);
                ?>
            </div>
            <hr>
            <p class="header-form" style="font-weight: normal;">Pasien Anak (adaptasi <i>STRONG-kids</i>)</p>
            <p class="header-form">1. Apakah pasien tampak Kurus?</p>
            <div class="row form-row">
                <div class="col-sm-12">
                    <table class="table-nutrition">
                        <tr>
                            <td>a. Ya</td>
                            <td><?= Html::activeRadio($model, 'strongkids_kurus', ['class' => 'strongkids-check', 'label' => '1', 'data-score' => 1, 'value' => '1', 'id' => 'strongkids_kurus-1']) ?></td>
                        </tr>
                        <tr>
                            <td>b. Tidak</td>
                            <td><?= Html::activeRadio($model, 'strongkids_kurus', ['class' => 'strongkids-check', 'label' => '0', 'data-score' => 0, 'value' => '0', 'id' => 'strongkids_kurus-0']) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <p class="header-form">2. Apakah terdapat penurunan BB selama 1 bulan terakhir? <br>( berdasarkan data penurunan BB objektif bila ada/penilaian subjektif dari orang tua ATAU untuk bayi < 1 tahun: BB tidak naik dalam 3 bulan terakhir )</p>
                    <div class="row">
                        <div class="col-sm-12 text-left">
                            <table class="table-nutrition">
                                <tr>
                                    <td>a. Ya</td>
                                    <td><?= Html::activeRadio($model, 'strongkids_turunbb', ['class' => 'strongkids-check', 'label' => '1', 'data-score' => 1, 'value' => '1', 'id' => 'strongkids_turunbb-1']) ?></td>
                                </tr>
                                <tr>
                                    <td>b. Tidak</td>
                                    <td><?= Html::activeRadio($model, 'strongkids_turunbb', ['class' => 'strongkids-check', 'label' => '0', 'data-score' => 0, 'value' => '0', 'id' => 'strongkids_turunbb-0']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <p class="header-form">3. Apakah terdapat salah satu kondisi berikut? <br>( diare >= 5 kali sehari atau muntah >= 3 kali sehari dalam satu minggu terakhir atau asupan makan berkurang dalam satu minggu terakhir )</p>
                    <div class="row">
                        <table class="table-nutrition">
                            <tr>
                                <td>a. Ya</td>
                                <td><?= Html::activeRadio($model, 'strongkids_kondisikhusus', ['class' => 'strongkids-check', 'label' => '1', 'data-score' => 1, 'value' => '1', 'id' => 'strongkids_kondisikhusus-1']) ?></td>
                            </tr>
                            <tr>
                                <td>b. Tidak</td>
                                <td><?= Html::activeRadio($model, 'strongkids_kondisikhusus', ['class' => 'strongkids-check', 'label' => '0', 'data-score' => 0, 'value' => '0', 'id' => 'strongkids_kondisikhusus-0']) ?></td>
                            </tr>
                        </table>
                    </div>
                    <p class="header-form">4. Apakah terdapat penyakit/keadaan yang mengakibatkan berisiko mengalami malnutrisi?</p>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table-nutrition">
                                <tr>
                                    <td>a. Ya</td>
                                    <td><?= Html::activeRadio($model, 'strongkids_keadaan_beresiko', ['class' => 'strongkids-check', 'label' => '2', 'data-score' => 2, 'value' => '2', 'id' => 'strongkids_keadaan_beresiko-1']) ?></td>
                                </tr>
                                <tr>
                                    <td>b. Tidak</td>
                                    <td><?= Html::activeRadio($model, 'strongkids_keadaan_beresiko', ['class' => 'strongkids-check', 'label' => '0', 'data-score' => 0, 'value' => '0', 'id' => 'strongkids_keadaan_beresiko-0']) ?></td>
                                </tr>
                                <tfoot>
                                    <tr>
                                        <td>Total Skor</td>
                                        <td id="strongkids-score-section">0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <?php if (isset($model->is_verifikasigizi) && !$model->is_verifikasigizi) : ?>
                            <div class="col-sm-5">
                                <p class="header-form" style="font-weight: normal;">Sudah dibaca dan diketahui oleh .... pada tanggal ....</p>
                            </div>
                            <div class="col-sm-5">
                                <p class="header-form"><?= Html::button("<b><i class='fa fa-check'></i></b> Verifikasi", ['class' => 'btn btn-xs btn-labeled btn-info btn-verifikasi-gizi ' . ($disableGiziBtn ? 'disabled' : '')]) ?></p>
                            </div>
                        <?php else : ?>
                            <div class="col-sm-12">
                                <p class="header-form" style="font-weight: normal;">Sudah dibaca dan diketahui oleh <?= isset($model->pegawaiverifikasigizi_nama) && !empty($model->pegawaiverifikasigizi_nama) ? $model->pegawaiverifikasigizi_nama : '....' ?> pada tanggal <?= isset($model->tgl_verifikasigizi) && !empty($model->tgl_verifikasigizi) ? date('d/m/Y H:i:s', strtotime($model->tgl_verifikasigizi)) : '....' ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
        </div>
    </div>
</div>
