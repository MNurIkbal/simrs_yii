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
    <h5 class="modal-title">Pengkajian Risiko Jatuh Humpty Dumpty</h5>
</div>
<div class="modal-body">
    <div class="col-lg-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h5><b>Pengkajian Risiko Jatuh Humpty Dumpty</b></h5>
            </div>
            <div class="panel-body">
                <?php 
                    $form = ActiveForm::begin([
                        'id' => 'form-dumpty',
                        'type' => ActiveForm::TYPE_VERTICAL,
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                    ]); 
                ?>
                <div class="col-sm-12">
                    <table class="table table-bordered">
                        <thead>
                          <tr class="bg-inverse">
                            <th rowspan="3" width="5%">No</th>
                            <th rowspan="3" width="5%">Parameter</th>
                            <th rowspan="3">Kriteria</th>
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
                            <td rowspan="4" class="top"><b>1</b></td>
                            <td rowspan="4" class="top"><b>Usia</b></td>
                            <td>&#60; 3 Tahun</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'usia')->label(false)->radioList(array('4'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['usia'] == 4){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>3 - 7 Tahun</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'usia')->label(false)->radioList(array('3'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['usia'] == 3){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>7 - 13 Tahun</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'usia')->label(false)->radioList(array('2'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['usia'] == 2){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>&#62;&#61; 13 Tahun</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'usia')->label(false)->radioList(array('1'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['usia'] == 1){
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
                            <td rowspan="2" class="top"><b>Jenis Kelamin</b></td>
                            <td>Laki - Laki</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'jenis_kelamin')->label(false)->radioList(array('2'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['jenis_kelamin'] == 2){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Perempuan</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'jenis_kelamin')->label(false)->radioList(array('1'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['jenis_kelamin'] == 1){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td rowspan="4" class="top"><b>3</b></td>
                            <td rowspan="4" class="top"><b>Diagnosis</b></td>
                            <td>Diagnosis neurologi</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'diagnosis')->label(false)->radioList(array('4'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['diagnosis'] == 4){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Perubahan oksigenasi (diagnosis respiratorik, dehidrasi, anemia anoreksia, sinkop, pusing, dsb)</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'diagnosis')->label(false)->radioList(array('3'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['diagnosis'] == 3){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Gangguan perilaku/ psikiatri</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'diagnosis')->label(false)->radioList(array('2'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['diagnosis'] == 2){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Diagnosis lainnya</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'diagnosis')->label(false)->radioList(array('1'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['diagnosis'] == 1){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td rowspan="3" class="top"><b>4</b></td>
                            <td rowspan="3" class="top"><b>Gangguan Kognitif</b></td>
                            <td>Tidak menyadari keterbatasan dirinya</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'gangguan_kognitif')->label(false)->radioList(array('3'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['gangguan_kognitif'] == 3){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Lupa akan adanya keterbatasan</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'gangguan_kognitif')->label(false)->radioList(array('2'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['gangguan_kognitif'] == 2){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Orientasi baik terhadap diri sendiri</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'gangguan_kognitif')->label(false)->radioList(array('1'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['gangguan_kognitif'] == 1){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td rowspan="4" class="top"><b>5</b></td>
                            <td rowspan="4" class="top"><b>Faktor Lingkungan</b></td>
                            <td>Riwayat jatuh/bayi diletakkan di tempat tidur dewasa</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'faktor_lingkungan')->label(false)->radioList(array('4'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['faktor_lingkungan'] == 4){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Pasien menggunakan alat bantu/ bayi diletakan di dalam tempat tidur bayi/ perabot rumah</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'faktor_lingkungan')->label(false)->radioList(array('3'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['faktor_lingkungan'] == 3){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Pasien diletakkan ditempat tidur</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'faktor_lingkungan')->label(false)->radioList(array('2'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['faktor_lingkungan'] == 2){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Area di luar rumah sakit</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'faktor_lingkungan')->label(false)->radioList(array('1'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['faktor_lingkungan'] == 1){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td rowspan="3" class="top"><b>6</b></td>
                            <td rowspan="3" class="top"><b>Pembedahan/Sedasi/Anastesi</b></td>
                            <td>Dalam 24 jam</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'anastesi')->label(false)->radioList(array('3'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['anastesi'] == 3){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Dalam 48 jam</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'anastesi')->label(false)->radioList(array('2'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['anastesi'] == 2){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>&#62; 48 jam atau tidak mengalami pembedahan/ sedasi/anestesi</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'anastesi')->label(false)->radioList(array('1'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['anastesi'] == 1){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td rowspan="3" class="top"><b>7</b></td>
                            <td rowspan="3" class="top"><b>Penggunaan Medika Mentosa</b></td>
                            <td>Pengguna Multiple; sedatif, obat hipnosis, barbiturat, fenotiazin, antidepresan, pencahar, diuretik, narkose</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'medika_mentosa')->label(false)->radioList(array('3'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['medika_mentosa'] == 3){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Pengguna salah satu obat diatas</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'medika_mentosa')->label(false)->radioList(array('2'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['medika_mentosa'] == 2){
                                       echo "<td><input type='radio' disabled checked></td>"; 
                                    }
                                    else{
                                        echo "<td><input type='radio' disabled></td>";
                                    }
                                }
                            ?>
                          </tr>
                          <tr>
                            <td>Pengguna medikasi lainnya/tidak ada medikasi</td>
                            <td class="form-column"><?= $form->field($modelDumpty, 'medika_mentosa')->label(false)->radioList(array('1'=>'')); ?></td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                    if($value['medika_mentosa'] == 1){
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
                                <b><?= Html::activeTextInput($modelDumpty, 'total_skor', ['class' => 'form-control', 'readonly' => true]) ?></b>
                            </td>
                            <?php 
                                foreach ($historyData['history'] as $value) {
                                       echo "<td bgcolor='#e5fff8'><b>".$value['total_skor']."</b></td>"; 
                                }
                            ?>
                          </tr>
                        </tbody>
                    </table>
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
    var dumptyData    = ' . json_encode($dumptyData) . '
');
$this->registerJs($this->render('__dumpty.js'), View::POS_END);
?>