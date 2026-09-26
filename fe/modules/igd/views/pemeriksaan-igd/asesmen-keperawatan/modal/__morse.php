<?php
use app\components\DHtml;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
?>

<style type="text/css">
.modal-dialog {
    width: 85% !important;
    padding-top: 5px;
}
label.control-label{
    font-weight: bold;
}
table tr td.top
{
    vertical-align: top;
}

table tr th.center
{
    text-align: center;
}

table tr td.center
{
    text-align: center;
}
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Pengkajian Skala Morse Risiko Jatuh</h5>
</div>
<div class="modal-body">
    <div class="col-lg-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h5><b>Pengkajian Skala Morse Risiko Jatuh</b></h5>
            </div>
            <div class="panel-body">
                <?php 
                    $form = ActiveForm::begin([
                        'id' => 'form-morse',
                        'type' => ActiveForm::TYPE_VERTICAL,
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                    ]); 
                ?>
                <div class="col-sm-12">
                    <div class="row form-row">
                        <div class="col-sm-6">
                            <?= $form->field($modelMorse, 'resikojatuh_id')->dropdownList([], ['id' => 'resikojatuh_id-form']); ?>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                          <tr class="bg-inverse">
                            <th rowspan="3" width="5%">No</th>
                            <th rowspan="3" colspan="2">Variabel</th>
                            <th colspan="<?=$totalTanggal?>" class="center">Asesmen Risiko Jatuh</th>
                          </tr>
                          <tr class="bg-inverse">
                            <td width="7%" class="center"><?= $tanggal?></td>
                            <?php 
                                foreach ($historyData['group'] as $value) {
                                    echo "<td colspan=".$value['count']." width='5%' class='center'>".$value['tanggal']."</td>";
                                }
                            ?>
                          </tr>
                          <tr class="bg-inverse">
                            <td class="center"><?= $jam?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    echo "<td class='center'>".$value['jam']."</td>";
                                }
                            ?>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td rowspan="2" class="top"><b>1</b></td>
                            <td rowspan="2" class="top"><b>Riwayat Pernah Jatuh (Dalam 3 Bulan Terakhir)</b></td>
                            <td>Tidak</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'riwayat_jatuh')->label(false)->radioList(array('0'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['riwayat_jatuh'] == 0){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Ya</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'riwayat_jatuh')->label(false)->radioList(array('25'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['riwayat_jatuh'] == 25){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td rowspan="2" class="top"><b>2</b></td>
                            <td rowspan="2" class="top"><b>Diagnosa Sekunder (Lebih Dari 1 Diagnosa Medis)</b></td>
                            <td>Tidak</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'diagnosis_sekunder')->label(false)->radioList(array('0'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['diagnosis_sekunder'] == 0){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Ya</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'diagnosis_sekunder')->label(false)->radioList(array('15'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['diagnosis_sekunder'] == 15){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td rowspan="3" class="top"><b>3</b></td>
                            <td rowspan="3" class="top"><b>Alat Bantu Pergerakan</b></td>
                            <td>Bedrest/selalu dibantu perawat</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'alat_bantu')->label(false)->radioList(array('0'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['alat_bantu'] == 0){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Menggunakan kruk/walker, tongkat</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'alat_bantu')->label(false)->radioList(array('15'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['alat_bantu'] == 15){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Furniture (tempat tidur)</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'alat_bantu')->label(false)->radioList(array('30'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['alat_bantu'] == 30){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td rowspan="2" class="top"><b>4</b></td>
                            <td rowspan="2" class="top"><b>Menggunakan IV Catheter</b></td>
                            <td>Tidak</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'catheter')->label(false)->radioList(array('0'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['catheter'] == 0){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Ya</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'catheter')->label(false)->radioList(array('20'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['catheter'] == 20){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td rowspan="3" class="top"><b>5</b></td>
                            <td rowspan="3" class="top"><b>Kemampuan Berjalan</b></td>
                            <td>Normal/bedrest/kursi roda</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'kemampuan_berjalan')->label(false)->radioList(array('0'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['kemampuan_berjalan'] == 0){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Lemah (menggunakan pegangan untuk keseimbangan)</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'kemampuan_berjalan')->label(false)->radioList(array('10'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['kemampuan_berjalan'] == 10){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Terganggu (sulit berdiri)</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'kemampuan_berjalan')->label(false)->radioList(array('20'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['kemampuan_berjalan'] == 20){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td rowspan="2" class="top"><b>6</b></td>
                            <td rowspan="2" class="top"><b>Status Mental</b></td>
                            <td>Sadar akan kemampuannya</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'status_mental')->label(false)->radioList(array('0'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['status_mental'] == 0){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Tidak sadar akan kemampuannya</td>
                            <td class="form-column"><?= $form->field($modelMorse, 'status_mental')->label(false)->radioList(array('15'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['status_mental'] == 15){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td bgcolor="#e5fff8" colspan="3"><b>Total Skor</b></td>
                            <td bgcolor="#e5fff8" class="form-column">
                                <b><?= Html::activeTextInput($modelMorse, 'total_skor', ['class' => 'form-control', 'readonly' => true]) ?></b>
                            </td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                       echo "<td bgcolor='#e5fff8'><b>".$value['total_skor']."</b></td>"; 
                                }
                            ?>
                          </tr>
                        </tbody>
                    </table>
                    <br>
                    <div class="row form-row">
                        <div class="col-sm-3">
                            <label for="control-label text-label"><b>Kesimpulan Hasil Pengkajian</b></label>
                        </div>
                        <div class="col-sm-9 form-group">
                            <?=
                                Html::activeTextarea($modelMorse, 'kesimpulan', ['class' => 'form-control']);
                            ?>
                            <div class="help-block">
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">

<?= Html::button("<i class='fa fa-arrow-left'> " . Yii::t('fe', 'Batal') . "</i>", [
    'class' => 'btn bg-slate',
    'data-dismiss' => 'modal'
]); ?>
<?= Html::button("<i class='fa fa-floppy-o'> " . Yii::t('fe', 'Submit') . "</i>", ['id' => 'btn-simpan', 'class' => 'btn bg-teal', 'data-dismiss' => 'modal']) ?>

<?php
$this->registerJs('
    var pendaftaranId = "' . $pendaftaran_id . '"
    var morseData    = ' . json_encode($morseData) . '
');
$this->registerJs($this->render('__morse.js'), View::POS_END);
?>