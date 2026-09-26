<?php
use yii\helpers\Html;
use yii\web\View;

$classForm = 'form-control';
$classFormNumber = 'form-control doco-number';
$parameterUmur = 'parameter_umur[]';
$parameterGender = 'parameter_gender[]';
$parameterDiagnosa = 'parameter_diagnosa[]';
$parameterKognitif = 'parameter_kognitif[]';
$parameterLingkungan = 'parameter_lingkungan[]';
$parameterRespon = 'parameter_respon[]';
$parameterObat = 'parameter_obat[]';
?>

<style>
.box-scale {
    margin-top: 10px;
    padding-top: 10px;
    padding-bottom: 10px;
}

.box-scale-header {
    margin-bottom: 3px !important;
}

table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 5px;
}
</style>
<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">L. Penilaian Resiko Jatuh <span style="font-style:italic;">(Skala Humpty Dumpty)</span> </h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <table style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="font-weight:bold;">Parameter</th>
                                        <th class="text-center" style="font-weight:bold;">Kriteria</th>
                                        <th class="text-center" style="font-weight:bold;">Skor</th>
                                        <th class="text-center" style="font-weight:bold;">Nilai Skor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td rowspan="4">Umur</td>
                                        <td>Dibawah 3 Tahun</td>
                                        <td class="text-center">4</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterUmur)
                                            ->radio(['label' => false, 'value' => 4, 'class' => 'parameter_umur', 'id' => 'parameterumur4']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3-7 Tahun</td>
                                        <td class="text-center">3</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterUmur)
                                            ->radio(['label' => false, 'value' => 3, 'class' => 'parameter_umur', 'id' => 'parameterumur3']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>7-13 Tahun</td>
                                        <td class="text-center">2</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterUmur)
                                            ->radio(['label' => false, 'value' => 2, 'class' => 'parameter_umur', 'id' => 'parameterumur2']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>> 13 Tahun</td>
                                        <td class="text-center">1</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterUmur)
                                            ->radio(['label' => false, 'value' => 1, 'class' => 'parameter_umur', 'id' => 'parameterumur1']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td rowspan="2">Jenis Kelamin</td>
                                        <td>Laki-Laki</td>
                                        <td class="text-center">2</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterGender)
                                            ->radio(['label' => false, 'value' => 2, 'class' => 'parameter_gender', 'id' => 'parametergender2']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Perempuan</td>
                                        <td class="text-center">1</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterGender)
                                            ->radio(['label' => false, 'value' => 1, 'class' => 'parameter_gender', 'id' => 'parametergender1']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td rowspan="4">Diagnosa</td>
                                        <td>Diagnosis Neurologi</td>
                                        <td class="text-center">4</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterDiagnosa)
                                            ->radio(['label' => false, 'value' => 4, 'class' => 'parameter_diagnosa', 'id' => 'parameterdiagnosa4']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Perubahan Oksigenasi (Diagnosis respiratorik, dehidrasi, anemia anoreksia, sinkop, pusing, dll)</td>
                                        <td class="text-center">3</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterDiagnosa)
                                            ->radio(['label' => false, 'value' => 3, 'class' => 'parameter_diagnosa', 'id' => 'parameterdiagnosa3']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Kelainan Psikis/Perilaku</td>
                                        <td class="text-center">2</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterDiagnosa)
                                            ->radio(['label' => false, 'value' => 2, 'class' => 'parameter_diagnosa', 'id' => 'parameterdiagnosa2']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Diagnosis Lain</td>
                                        <td class="text-center">1</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterDiagnosa)
                                            ->radio(['label' => false, 'value' => 1, 'class' => 'parameter_diagnosa', 'id' => 'parameterdiagnosa1']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td rowspan="3">Gangguan Kognitif</td>
                                        <td>Tidak Menyadari Keterbatasan Dirinya</td>
                                        <td class="text-center">3</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterKognitif)
                                            ->radio(['label' => false, 'value' => 3, 'class' => 'parameter_kognitif', 'id' => 'parameterkognitif3']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Lupa Akan Adanya Keterbatasan</td>
                                        <td class="text-center">2</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterKognitif)
                                            ->radio(['label' => false, 'value' => 2, 'class' => 'parameter_kognitif', 'id' => 'parameterkognitif2']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Orientasi Baik Terhadap Diri Sendiri/Mengetahui Kemampuan Dirinya</td>
                                        <td class="text-center">1</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterKognitif)
                                            ->radio(['label' => false, 'value' => 1, 'class' => 'parameter_kognitif', 'id' => 'parameterkognitif1']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td rowspan="4">Faktor Lingkungan</td>
                                        <td>Riwayat Jatuh Dari Tempat Tidur Saat Bayi/Anak</td>
                                        <td class="text-center">4</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterLingkungan)
                                            ->radio(['label' => false, 'value' => 4, 'class' => 'parameter_lingkungan', 'id' => 'parameterlingkungan4']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pasien Menggunakan Alat Bantu Atau Box Atau Mebel</td>
                                        <td class="text-center">3</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterLingkungan)
                                            ->radio(['label' => false, 'value' => 3, 'class' => 'parameter_lingkungan', 'id' => 'parameterlingkungan3']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pasien Berada di Tempat Tidur</td>
                                        <td class="text-center">2</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterLingkungan)
                                            ->radio(['label' => false, 'value' => 2, 'class' => 'parameter_lingkungan', 'id' => 'parameterlingkungan2']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Diluar Ruang Rawat</td>
                                        <td class="text-center">1</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterLingkungan)
                                            ->radio(['label' => false, 'value' => 1, 'class' => 'parameter_lingkungan', 'id' => 'parameterlingkungan1']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td rowspan="3">Respon Terhadap Pembedahan/Sedasi/Anastesi</td>
                                        <td>Dalam 24 Jam</td>
                                        <td class="text-center">3</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterRespon)
                                            ->radio(['label' => false, 'value' => 3, 'class' => 'parameter_respon', 'id' => 'parameterrespon3']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dalam 48 Jam Riwayat Jatuh</td>
                                        <td class="text-center">2</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterRespon)
                                            ->radio(['label' => false, 'value' => 2, 'class' => 'parameter_respon', 'id' => 'parameterrespon2']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>> 48 Jam/Tidak Menjalani Pembedahan/Sedasi/Anastesi</td>
                                        <td class="text-center">1</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterRespon)
                                            ->radio(['label' => false, 'value' => 1, 'class' => 'parameter_respon', 'id' => 'parameterrespon1']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td rowspan="3">Penggunaan Obat</td>
                                        <td>Bermacam-macam obat yang digunakan : Obat Sedatif (kecuali pasien ICU yang menggunakan sedasi dan paralisis), hipnotik, barbiturat, fenotiazin, anti depresan, laksatif/diuretik, narkotik</td>
                                        <td class="text-center">3</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterObat)
                                            ->radio(['label' => false, 'value' => 3, 'class' => 'parameter_obat', 'id' => 'parameterobat3']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Salah Satu Dari Pengobatan di Atas</td>
                                        <td class="text-center">2</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterObat)
                                            ->radio(['label' => false, 'value' => 2, 'class' => 'parameter_obat', 'id' => 'parameterobat2']); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Pengobatan Lain/Tidak Ada Medikasi</td>
                                        <td class="text-center">1</td>
                                        <td style="text-align:center;width:15%;"><?= $form->field($model, $parameterObat)
                                            ->radio(['label' => false, 'value' => 1, 'class' => 'parameter_obat', 'id' => 'parameterobat1']); ?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-center" style="font-weight:bold;">Total Skor</td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;"> <span class="total_skor_parameter"></span> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="font-weight:bold;">
                                            <p style="text-align:center;">Tingkat Risiko : (Skor Minimal : 7, Skor Maksimal : 23) </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-center" style="font-weight:bold;">
                                            <?= $form->field($model, 'kategori_risiko_jatuh')
                                            ->label(false)
                                            ->radioList(
                                                [
                                                    '1' => 'Skor 7 - 11 : Risiko Rendah',
                                                    '2' => 'Skor > 12 : Risiko Tinggi',
                                                ],
                                                [
                                                    'inline' => true,
                                                    'itemOptions' => [
                                                        'class' => 'kategori_risiko_jatuh'
                                                    ]
                                                ]
                                            ); ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var _parameterUmur = ' . json_encode($model->parameter_umur) . ';
var _parameterGender = ' . json_encode($model->parameter_gender) . ';
var _parameterDiagnosa = ' . json_encode($model->parameter_diagnosa) . ';
var _parameterKognitif = ' . json_encode($model->parameter_kognitif) . ';
var _parameterLingkungan = ' . json_encode($model->parameter_lingkungan) . ';
var _parameterRespon = ' . json_encode($model->parameter_respon) . ';
var _parameterObat = ' . json_encode($model->parameter_obat) . ';

$(document).ready(function(){
    if(!asesmenMedisId) {
        $(".total_skor_parameter").html(0)
    }
    
    var paramUmur = paramGender = paramDiagnosa = paramKognitif = paramLingkungan = paramRespon = paramObat = 0
    function updateSkorParam() {
        var totalSkorParam = paramUmur + paramGender + paramDiagnosa + paramKognitif + paramLingkungan + paramRespon + paramObat
        $(".total_skor_parameter").html(totalSkorParam)
    }

    $(document).on("change", ".parameter_umur", function(){
        if($(this).is(":checked")) {
            paramUmur = parseInt($(this).val())
            updateSkorParam()
        }
    })
    $(document).on("change", ".parameter_gender", function(){
        if($(this).is(":checked")) {
            paramGender = parseInt($(this).val())
            updateSkorParam()
        }
    })
    $(document).on("change", ".parameter_diagnosa", function(){
        if($(this).is(":checked")) {
            paramDiagnosa = parseInt($(this).val())
            updateSkorParam()
        }
    })
    $(document).on("change", ".parameter_kognitif", function(){
        if($(this).is(":checked")) {
            paramKognitif = parseInt($(this).val())
            updateSkorParam()
        }
    })
    $(document).on("change", ".parameter_lingkungan", function(){
        if($(this).is(":checked")) {
            paramLingkungan = parseInt($(this).val())
            updateSkorParam()
        }
    })
    $(document).on("change", ".parameter_respon", function(){
        if($(this).is(":checked")) {
            paramRespon = parseInt($(this).val())
            updateSkorParam()
        }
    })
    $(document).on("change", ".parameter_obat", function(){
        if($(this).is(":checked")) {
            paramObat = parseInt($(this).val())
            updateSkorParam()
        }
    })
    
    function checkSkor(skor) {
        return skor > 0;
    }
    
    var skorParamUmur = skorParamGender = skorParamDiagnosa = skorParamKognitif = skorParamLingkungan = skorParamRespon = skorParamObat = 0

    if(_parameterUmur) {
        skorParamUmur = _parameterUmur.filter(checkSkor)
        skorParamUmur = (typeof skorParamUmur[0] != "undefined") ? skorParamUmur[0] : 0
    }
    if(_parameterGender) {
        skorParamGender = _parameterGender.filter(checkSkor)
        skorParamGender = (typeof skorParamGender[0] != "undefined") ? skorParamGender[0] : 0
    }
    if(_parameterDiagnosa) {
        skorParamDiagnosa = _parameterDiagnosa.filter(checkSkor)
        skorParamDiagnosa = (typeof skorParamDiagnosa[0] != "undefined") ? skorParamDiagnosa[0] : 0
    }
    if(_parameterKognitif) {
        skorParamKognitif = _parameterKognitif.filter(checkSkor)
        skorParamKognitif = (typeof skorParamKognitif[0] != "undefined") ? skorParamKognitif[0] : 0
    }
    if(_parameterLingkungan) {
        skorParamLingkungan = _parameterLingkungan.filter(checkSkor)
        skorParamLingkungan = (typeof skorParamLingkungan[0] != "undefined") ? skorParamLingkungan[0] : 0
    }
    if(_parameterRespon) {
        skorParamRespon = _parameterRespon.filter(checkSkor)
        skorParamRespon = (typeof skorParamRespon[0] != "undefined") ? skorParamRespon[0] : 0
    }
    if(_parameterObat) {
        skorParamObat = _parameterObat.filter(checkSkor)
        skorParamObat = (typeof skorParamObat[0] != "undefined") ? skorParamObat[0] : 0
    }

    const totalSkorParam = parseInt(skorParamUmur) + parseInt(skorParamGender) + parseInt(skorParamDiagnosa) + parseInt(skorParamKognitif) +
                            parseInt(skorParamLingkungan) + parseInt(skorParamRespon) + parseInt(skorParamObat)
    

    $(".parameter_umur").each(function(el){
        if($(this).val() == skorParamUmur) {
            $(this).prop("checked", true)
        }
        else {
            $(this).prop("checked", false)
        }
    })
    $(".parameter_gender").each(function(el){
        if($(this).val() == skorParamGender) {
            $(this).prop("checked", true)
        }
        else {
            $(this).prop("checked", false)
        }
    })
    $(".parameter_diagnosa").each(function(el){
        if($(this).val() == skorParamDiagnosa) {
            $(this).prop("checked", true)
        }
        else {
            $(this).prop("checked", false)
        }
    })
    $(".parameter_kognitif").each(function(el){
        if($(this).val() == skorParamKognitif) {
            $(this).prop("checked", true)
        }
        else {
            $(this).prop("checked", false)
        }
    })
    $(".parameter_lingkungan").each(function(el){
        if($(this).val() == skorParamLingkungan) {
            $(this).prop("checked", true)
        }
        else {
            $(this).prop("checked", false)
        }
    })
    $(".parameter_respon").each(function(el){
        if($(this).val() == skorParamRespon) {
            $(this).prop("checked", true)
        }
        else {
            $(this).prop("checked", false)
        }
    })
    $(".parameter_obat").each(function(el){
        if($(this).val() == skorParamObat) {
            $(this).prop("checked", true)
        }
        else {
            $(this).prop("checked", false)
        }
    })

    if(asesmenMedisId) {
        $(".total_skor_parameter").html(totalSkorParam)
    }
})

', View::POS_END);
?>
