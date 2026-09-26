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
    .monev {width: 57px}
</style>

<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title"><b>Monev (Monitoring & Evaluasi)</b></h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-12">
                    <div class="row form-row">
                        <div class="col-sm-12">
                            <table class="table table-bordered">
                                <thead>
                                  <tr class="bg-inverse">
                                    <th>Indikator Monev</th>
                                    <th colspan="4"><?= date('d F y H:i:s')?></th>
                                    <?php
                                        foreach ($historyData['history'] as $value) {
                                            echo "<th colspan='4'>".date('d F y H:i:s',strtotime($value['tgl_monev']))."</th>";
                                        }
                                    ?>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>Berat Badan</td>
                                    <td colspan="4"><?= $form->field($modelMonev, 'berat_badan', ['addon' => ['append' => ['content' => 'kg']]])->textInput(['class' => 'doco-decimal-wcomma'])->label(false); ?></td>
                                    <?php
                                        foreach ($historyData['history'] as $value) {
                                            echo "
                                            <td colspan='4'><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['berat_badan']."'' disabled>
                                            <span class='input-group-addon'>kg</span></div>
                                            </td>";
                                        }
                                    ?>
                                  </tr>
                                  <tr>
                                    <td>Tekanan Darah</td>
                                    <td colspan="4"><?= $form->field($modelMonev, 'tekanan_darah', ['addon' => ['append' => ['content' => 'mmHg']]])->textInput(['class' => 'text-bold tekanan-darah-format'])->label(false); ?></td>
                                    <?php
                                        foreach ($historyData['history'] as $value) {
                                            echo "
                                            <td colspan='4'><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['tekanan_darah']."'' disabled>
                                            <span class='input-group-addon'>mmHg</span></div>
                                            </td>";
                                        }
                                    ?>
                                  </tr>
                                  <tr>
                                    <td>Nilai Lab Abnormal</td>
                                    <td colspan="4"><?= $form->field($modelMonev, 'nilai_lab_abnormal')->textInput(['class' => 'doco-decimal-wcomma'])->label(false); ?></td>
                                    <?php
                                        foreach ($historyData['history'] as $value) {
                                            echo "
                                            <td colspan='4'><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['nilai_lab_abnormal']."'' disabled>
                                            </div>
                                            </td>";
                                        }
                                    ?>
                                  </tr>
                                  <tr class="bg-inverse">
                                    <td>Asupan Makanan</td>
                                    <td>Energi (kal)</td>
                                    <td>Protein (gr)</td>
                                    <td>Lemak (gr)</td>
                                    <td>KH (gr)</td>
                                    <?php
                                        foreach ($historyData['history'] as $value) {
                                            echo "
                                            <td>Energi (kal)</td>
                                            <td>Protein (gr)</td>
                                            <td>Lemak (gr)</td>
                                            <td>KH (gr)</td>
                                            ";
                                        }
                                    ?>
                                  </tr>
                                  <tr>
                                    <td>Oral</td>
                                    <td><?= $form->field($modelMonev, 'oral_energi')->textInput(['class' => 'doco-decimal-wcomma form-control monev'])->label(false); ?></td>
                                    <td><?= $form->field($modelMonev, 'oral_protein')->textInput(['class' => 'doco-decimal-wcomma form-control monev'])->label(false); ?></td>
                                    <td><?= $form->field($modelMonev, 'oral_lemak')->textInput(['class' => 'doco-decimal-wcomma form-control monev'])->label(false); ?></td>
                                    <td><?= $form->field($modelMonev, 'oral_kh')->textInput(['class' => 'doco-decimal-wcomma form-control monev'])->label(false); ?></td>
                                    <?php
                                        foreach ($historyData['history'] as $value) {
                                            echo "
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['oral_energi']."'' disabled></div></td>
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['oral_protein']."'' disabled></div></td>
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['oral_lemak']."'' disabled></div></td>
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['oral_kh']."'' disabled></div></td>
                                            ";
                                        }
                                    ?>
                                  </tr>
                                  <tr>
                                    <td>Enteral</td>
                                    <td><?= $form->field($modelMonev, 'enteral_energi')->textInput(['class' => 'doco-decimal-wcomma monev'])->label(false); ?></td>
                                    <td><?= $form->field($modelMonev, 'enteral_protein')->textInput(['class' => 'doco-decimal-wcomma form-control monev'])->label(false); ?></td>
                                    <td><?= $form->field($modelMonev, 'enteral_lemak')->textInput(['class' => 'doco-decimal-wcomma form-control monev'])->label(false); ?></td>
                                    <td><?= $form->field($modelMonev, 'enteral_kh')->textInput(['class' => 'doco-decimal-wcomma form-control monev'])->label(false); ?></td>
                                    <?php
                                        foreach ($historyData['history'] as $value) {
                                            echo "
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['enteral_energi']."'' disabled></div></td>
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['enteral_protein']."'' disabled></div></td>
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['enteral_lemak']."'' disabled></div></td>
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['enteral_kh']."'' disabled></div></td>
                                            ";
                                        }
                                    ?>
                                  </tr>
                                  <tr>
                                    <td>Parenteral</td>
                                    <td><?= $form->field($modelMonev, 'parenteral_energi')->textInput(['class' => 'doco-decimal-wcomma monev'])->label(false); ?></td>
                                    <td><?= $form->field($modelMonev, 'parenteral_protein')->textInput(['class' => 'doco-decimal-wcomma form-control monev'])->label(false); ?></td>
                                    <td><?= $form->field($modelMonev, 'parenteral_lemak')->textInput(['class' => 'doco-decimal-wcomma form-control monev'])->label(false); ?></td>
                                    <td><?= $form->field($modelMonev, 'parenteral_kh')->textInput(['class' => 'doco-decimal-wcomma form-control monev'])->label(false); ?></td>
                                    <?php
                                        foreach ($historyData['history'] as $value) {
                                            echo "
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['parenteral_energi']."'' disabled></div></td>
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['parenteral_protein']."'' disabled></div></td>
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['parenteral_lemak']."'' disabled></div></td>
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".$value['parenteral_kh']."'' disabled></div></td>
                                            ";
                                        }
                                    ?>
                                  </tr>
                                  <tr>
                                    <td>Total Asupan</td>
                                    <td><?= $form->field($modelMonev, 'total_asupan_energi')->textInput(['class' => 'monev doco-decimal-wcomma', 'readonly' => true])->label(false); ?></td>
                                    <td><?= $form->field($modelMonev, 'total_asupan_protein')->textInput(['class' => 'monev doco-decimal-wcomma', 'readonly' => true])->label(false); ?></td>
                                    <td><?= $form->field($modelMonev, 'total_asupan_lemak')->textInput(['class' => 'monev doco-decimal-wcomma', 'readonly' => true])->label(false); ?></td>
                                    <td><?= $form->field($modelMonev, 'total_asupan_kh')->textInput(['class' => 'monev doco-decimal-wcomma', 'readonly' => true])->label(false); ?></td>
                                    <?php
                                        foreach ($historyData['history'] as $key => $value) {
                                            echo "
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".((int) $value['oral_energi'] + (int) $value['enteral_energi'] + (int) $value['parenteral_energi'])."' disabled></div></td>
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".((int) $value['oral_protein'] + (int) $value['enteral_protein'] + (int) $value['parenteral_protein'])."' disabled></div></td>
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".((int) $value['oral_lemak'] + (int) $value['enteral_lemak'] + (int) $value['parenteral_lemak'])."' disabled></div></td>
                                            <td><div class='input-group'><input type='text' class='doco-decimal-wcomma form-control' value='".((int) $value['oral_kh'] + (int) $value['enteral_kh'] + (int) $value['parenteral_kh'])."' disabled></div></td>
                                            ";
                                        }
                                    ?>
                                  </tr>
                                  <tr>
                                    <td>Usulan Evaluasi</td>
                                    <td colspan="4" class="form-column"><?= Html::activeTextInput($modelMonev, 'evaluasi_usulan', ['class' => 'form-control']) ?></td>
                                    <?php
                                        foreach ($historyData['history'] as $value) {
                                            echo "
                                            <td colspan='4' class='form-column'><input type='text' class='form-control' value='".$value['evaluasi_usulan']."' disabled></td>
                                            ";
                                        }
                                    ?>
                                  </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
