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
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Asesmen Pasien Risiko Jatuh Geriatric (sydney)</h5>
</div>
<div class="modal-body">
    <div class="col-lg-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h5><b>Asesmen Pasien Risiko Jatuh Geriatric (sydney)</b></h5>
            </div>
            <div class="panel-body">
                <?php 
                    $form = ActiveForm::begin([
                        'id' => 'form-sydney',
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
                                <th width="5%">No</th>
                                <th width="45%">Asesmen Awal</th>
                                <th width="20%">Skala Skor</th>
                                <th width="20%">Keterangan</th>
                                <th width="10%">Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td bgcolor="#e5fff8" class="text-center"><b>1</b></td>
                                <td bgcolor="#e5fff8" colspan="4"><b>Riwayat Jatuh</b></td>
                            </tr>
                            <tr class="scoreone" data-scoreinput='skor_karena_jatuh'>
                                <td class="text-center"></td>
                                <td>Apakah pasien datang ke RS karena jatuh?</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_karena_jatuh',
                                                'data'      => $configVal['is_karena_jatuh'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column" rowspan="2">
                                    Salah Satu jawaban ya = 6
                                    <?php //Html::activeTextInput($modelSydney, 'karena_jatuh', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column" rowspan="2">
                                    <?= Html::activeTextInput($modelSydney, 'skor_karena_jatuh', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr class="scoreone" data-scoreinput='skor_karena_jatuh'>
                                <td class="text-center"></td>
                                <td>Jika tidak, apakah pasien mengalami jatuh dalam 2 bulan terakhir?</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_dua_bulan_terakhir',
                                                'data'      => $configVal['is_dua_bulan_terakhir'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'dua_bulan_terakhir', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'skor_dua_bulan_terakhir', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#e5fff8" class="text-center"><b>2</b></td>
                                <td bgcolor="#e5fff8" colspan="4"><b>Status Mental</b></td>
                            </tr>
                            <tr class="scoreone" data-scoreinput='skor_delirium'>
                                <td class="text-center"></td>
                                <td>Apakah pasien delirium? (tidak dapat membuat keputusan, pola pikir tidak terorganisir, gangguan daya ingat?)</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_delirium',
                                                'data'      => $configVal['is_delirium'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column" rowspan="3">
                                    Salah Satu jawaban ya = 14
                                    <?php //Html::activeTextInput($modelSydney, 'delirium', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column" rowspan="3">
                                    <?= Html::activeTextInput($modelSydney, 'skor_delirium', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr class="scoreone" data-scoreinput='skor_delirium'>
                                <td class="text-center"></td>
                                <td>Apakah pasien disorientasi? (salah menyebut waktu, tempat atau orang)</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_disorientasi',
                                                'data'      => $configVal['is_disorientasi'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'disorientasi', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'skor_disorientasi', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr class="scoreone" data-scoreinput='skor_delirium'>
                                <td class="text-center"></td>
                                <td>Apakah pasien mengalami agitasi? (ketakutan, gelisah dan cemas)</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_agitasi',
                                                'data'      => $configVal['is_agitasi'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'agitasi', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'skor_agitasi', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#e5fff8" class="text-center"><b>3</b></td>
                                <td bgcolor="#e5fff8" colspan="4"><b>Penglihatan</b></td>
                            </tr>
                            <tr class="scoreone" data-scoreinput='skor_kacamata'>
                                <td class="text-center"></td>
                                <td>Apakah pasien memakai kacamata?</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_kacamata',
                                                'data'      => $configVal['is_kacamata'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column" rowspan="3">
                                    Salah Satu jawaban ya = 1
                                    <?php //Html::activeTextInput($modelSydney, 'kacamata', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column" rowspan="3">
                                    <?= Html::activeTextInput($modelSydney, 'skor_kacamata', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr class="scoreone" data-scoreinput='skor_kacamata'>
                                <td class="text-center"></td>
                                <td>Apakah pasien mengeluh adanya penglihatan buram?</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_buram',
                                                'data'      => $configVal['is_buram'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'buram', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'skor_buram', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr class="scoreone" data-scoreinput='skor_kacamata'>
                                <td class="text-center"></td>
                                <td>Apakah pasien mengalami glaucoma,katarak, atau degenerasi makula?</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_glaucoma',
                                                'data'      => $configVal['is_glaucoma'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'glaucoma', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'skor_glaucoma', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#e5fff8" class="text-center"><b>4</b></td>
                                <td bgcolor="#e5fff8" colspan="4"><b>Kebiasaan Berkemih</b></td>
                            </tr>
                            <tr>
                                <td class="text-center"></td>
                                <td>Apakah terdapat perubahan perilaku berkemih? (frekuensi, urgency,
inkontinensia, nokturia) </td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_berkemih',
                                                'data'      => $configVal['is_berkemih'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column">
                                    Ya = 2
                                    <?php //Html::activeTextInput($modelSydney, 'berkemih', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column">
                                    <?= Html::activeTextInput($modelSydney, 'skor_berkemih', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#e5fff8" class="text-center"><b>5</b></td>
                                <td bgcolor="#e5fff8" colspan="4"><b>Transfer (dari tempat tidur ke kursi dan kembali ke tempat tidur)</b></td>
                            </tr>
                            <tr class="_transfer">
                                <td class="text-center"></td>
                                <td>Mandiri (boleh menggunakan alat bantu jalan)</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_mandiri',
                                                'data'      => $configVal['is_mandiri'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column" rowspan="9">
                                    Jumlahkan nilai transfer dan mobilitas. Jika nilai total 0 - 3, maka skor = 0. Jika nilai total 4 - 6, maka skor = 7
                                    <?php //Html::activeTextInput($modelSydney, 'mandiri', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column">
                                    <?= Html::activeTextInput($modelSydney, 'skor_mandiri', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr class="_transfer">
                                <td class="text-center"></td>
                                <td>Memerlukan sedikit bantuan (1 orang) / dalam pengawasan </td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_bantuan_sedikit',
                                                'data'      => $configVal['is_bantuan_sedikit'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'bantuan_sedikit', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column">
                                    <?= Html::activeTextInput($modelSydney, 'skor_bantuan_sedikit', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr class="_transfer">
                                <td class="text-center"></td>
                                <td>Memerlukan bantuan yang nyata (2 orang)</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_bantuan_nyata',
                                                'data'      => $configVal['is_bantuan_nyata'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'bantuan_nyata', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column">
                                    <?= Html::activeTextInput($modelSydney, 'skor_bantuan_nyata', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr class="_transfer">
                                <td class="text-center"></td>
                                <td>Tidak dapat duduk dengan seimbang, perlu bantuan total mbilitas</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_bantuan_total',
                                                'data'      => $configVal['is_bantuan_total'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'bantuan_total', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column">
                                    <?= Html::activeTextInput($modelSydney, 'skor_bantuan_total', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td bgcolor="#e5fff8" class="text-center"><b>6</b></td>
                                <td bgcolor="#e5fff8" colspan="2"><b>Mobilitas</b></td>
                                <td bgcolor="#e5fff8"></td>
                            </tr>
                            <tr class="_mobilitas">
                                <td class="text-center"></td>
                                <td>Mandiri (boleh menggunakan alat bantu jalan)</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_mobilitas_mandiri',
                                                'data'      => $configVal['is_mobilitas_mandiri'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'mobilitas_mandiri', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column">
                                    <?= Html::activeTextInput($modelSydney, 'skor_mobilitas_mandiri', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr class="_mobilitas">
                                <td class="text-center"></td>
                                <td>Berjalan dengan bantuan 1 orang (verbal/fisik)</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_mobilitas_bantuan',
                                                'data'      => $configVal['is_mobilitas_bantuan'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'mobilitas_bantuan', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column">
                                    <?= Html::activeTextInput($modelSydney, 'skor_mobilitas_bantuan', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr class="_mobilitas">
                                <td class="text-center"></td>
                                <td>Menggunakan kursi roda</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_kursi_roda',
                                                'data'      => $configVal['is_kursi_roda'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'kursi_roda', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column">
                                    <?= Html::activeTextInput($modelSydney, 'skor_kursi_roda', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                            <tr class="_mobilitas">
                                <td class="text-center"></td>
                                <td>Imobilisasi</td>
                                <td class="form-column">
                                    <div class="row">
                                        <?=
                                            DHtml::multipleRadio([
                                                'model'     => $modelSydney,
                                                'fieldName' => 'is_imobilisasi',
                                                'data'      => $configVal['is_imobilisasi'],
                                                'colSize'   => 6
                                            ]); 
                                        ?>
                                    </div>
                                </td>
                                <td class="form-column hidden">
                                    <?= Html::activeTextInput($modelSydney, 'imobilisasi', ['class' => 'form-control']) ?>
                                </td>
                                <td class="form-column">
                                    <?= Html::activeTextInput($modelSydney, 'skor_imobilisasi', ['class' => 'form-control', 'readonly' => true]) ?>
                                </td>
                            </tr>
                             <tr>
                                <td bgcolor="#e5fff8"></td>
                                <td bgcolor="#e5fff8" colspan="3"><b>TOTAL SKOR</b></td>
                                <td bgcolor="#e5fff8" class="form-column">
                                    <b><?= Html::activeTextInput($modelSydney, 'total_skor', ['class' => 'form-control', 'readonly' => true]) ?></b>
                                </td>
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
    var sydneyData    = ' . json_encode($sydneyData) . '
');
$this->registerJs($this->render('__sydney.js'), View::POS_END);
?>