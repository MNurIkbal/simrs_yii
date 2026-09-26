<?php 
use yii\helpers\Html;
use yii\web\View;
?>

<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 5px;
}
</style>
<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">G. Penilaian Nyeri</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'nyeri')->radioList(
                            [
                                'Ya' => 'Ya',
                                'Tidak' => 'Tidak'
                            ],
                            [
                                'inline' => true, 
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'onset')->checkboxList(
                                [
                                    'Akut' => 'Akut',
                                    'Kronis' => 'Kronis'
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pencetus')->textInput(
                            ['class' => 'form-control input-sm']); ?>
                        </div>
                        <div class="col-sm-6">
                        <?= $form->field($model, 'lokasi_nyeri')->textInput(
                            ['class' => 'form-control input-sm']); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gambaran_nyeri')->textInput(
                            ['class' => 'form-control input-sm']); ?>
                        </div>
                        <div class="col-sm-6">
                        <?= $form->field($model, 'durasi_nyeri')->textInput(
                            ['class' => 'form-control input-sm']); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">Skala Nyeri : Neonatal Infant Pain Scale (NIPS) </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table style="width:100%">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="text-center" style="font-weight:bold;">Pengkajian Nyeri</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="font-weight:bold;">Skoring</th>
                                        <th class="text-center" style="font-weight:bold;">Ekspresi Wajah</th>
                                        <th class="text-center" style="font-weight:bold;">Skor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="width:30%;">
                                            <?= $form->field($model, 'skoring_otot')->radioList(
                                            [
                                                '0' => '0 : Otot-Otot Rileks',
                                                '1' => '1 : Meringis'
                                            ],
                                            ['itemOptions' => ['class' => 'skoring_otot']]); ?>
                                        </td>
                                        <td>
                                            <p>Wajah tenang ekspresi netral</p>
                                            <p>Otot wajah tegang, alis berkerut, dahu dan rahang tegang (ekspresi wajah negatif, hidung mulut dan alis)</p>
                                        </td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;"> <span class="label_otot"></span> </td>
                                    </tr>
                                    <tr>
                                        <td style="width:30%;"><?= $form->field($model, 'skoring_menangis')->radioList(
                                            [
                                                '0' => '0 : Tidak Menangis',
                                                '1' => '1 : Mengerang',
                                                '2' => '2 : Menangis Keras'
                                            ],
                                            ['itemOptions' => ['class' => 'skoring_menangis']]); ?>
                                        </td>
                                        <td>
                                            <p>Tenang, tidak menangis</p>
                                            <p>Merengek ringan, kadang-kadang</p>
                                            <p>Berteriak kencang, menaik, melengking, terus menerus (Catatan menangis lirih mungkin dinilai jika bayi diintubasi yang dibutuhkan melalui gerakan mulut dan wajah yang jelas) </p>
                                        </td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;"> <span class="label_menangis"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="width:30%;"><?= $form->field($model, 'skoring_pernafasan')->radioList(
                                            [
                                                '0' => '0 : Bernafas Rileks',
                                                '1' => '1 : Perubahan Pola Pernafasan',
                                            ],
                                            ['itemOptions' => ['class' => 'skoring_pernafasan']]); ?>
                                        </td>
                                        <td>
                                            <p>Pola bernafas bayi yang normal</p>
                                            <p>Tidak teratur, lebih cepat dari biasanya, tersedak, nafas tertahan</p>
                                        </td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;"> <span class="label_pernafasan"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="width:30%;"><?= $form->field($model, 'skoring_lengan')->radioList(
                                            [
                                                '0' => '0 : Relaks/Terikat',
                                                '1' => '1 : Fleksi/Eksistensi',
                                            ],
                                            ['itemOptions' => ['class' => 'skoring_lengan']]); ?>
                                        </td>
                                        <td>
                                            <p>Tidak ada kekuatan otot, gerakan tangan acak sekali-kali</p>
                                            <p>Tegang, tangan lurus, kaku dan/atau ekstensi cepat, ekstensi, fleksi</p>
                                        </td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;"> <span class="label_lengan"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="width:30%;"><?= $form->field($model, 'skoring_kaki')->radioList(
                                            [
                                                '0' => '0 : Relaks/Terikat',
                                                '1' => '1 : Fleksi/Eksistensi',
                                            ],
                                            ['itemOptions' => ['class' => 'skoring_kaki']]); ?>
                                        </td>
                                        <td>
                                            <p>Tidak ada kekuatan otot, gerakan tangan acak sekali-kali</p>
                                            <p>Tegang, tangan lurus, kaku dan/atau ekstensi cepat, ekstensi, fleksi</p>
                                        </td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;"> <span class="label_kaki"></span></td>
                                    </tr>
                                    <tr>
                                        <td style="width:30%;"><?= $form->field($model, 'skoring_kesadaran')->radioList(
                                            [
                                                '0' => '0 : Tidur/Terjaga',
                                                '1' => '1 : Rewel',
                                            ],
                                            ['itemOptions' => ['class' => 'skoring_kesadaran']]); ?>
                                        </td>
                                        <td>
                                            <p>Tenang, tidur damai atau gerakan kaki acak yang terjaga</p>
                                            <p>Terjaga, gelisah dan meronta-ronta</p>
                                        </td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;"> <span class="label_kesadaran"></span></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" class="text-center" style="font-weight:bold;">Total Skor</td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;"> <span class="total_skor"></span> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="font-weight:bold;">
                                            <p style="text-align:center;">Interpretasi : Parameter ada enam dengan skor 0 (terendah) sampai dengan skor 7 (tertinggi)</p>
                                            <p style="text-align:center;">Nilai 0 - 2 : Tidak nyeri/nyeri ringan</p>
                                            <p style="text-align:center;">Nilai 3 - 4 : Nyeri ringan sampai moderat</p>
                                            <p style="text-align:center;">Nilai > 4 : Nyeri berat</p>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <br><br>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'skoring_skala_nyeri')->textInput(
                            ['class' => 'form-control input-sm']); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'frekuensi')->textInput(
                            ['class' => 'form-control input-sm']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var skoring_otot = "'.$model->skoring_otot.'"
var skoring_menangis = "'.$model->skoring_menangis.'"
var skoring_pernafasan = "'.$model->skoring_pernafasan.'"
var skoring_lengan = "'.$model->skoring_lengan.'"
var skoring_kaki = "'.$model->skoring_kaki.'"
var skoring_kesadaran = "'.$model->skoring_kesadaran.'"

var asesmenMedisId = "'.$asesmenMedisId.'"

$(document).ready(function(){
    var labelOtot = labelMenangis = labelPernafasan = labelLengan = labelKaki = labelKesadaran = 0
    $(document).on("change", ".skoring_otot", function(){
        if($(this).is(":checked")) {
            labelOtot = parseInt($(this).val())
            $(".label_otot").html(labelOtot)
            updateSkor()
        }
    })
    $(document).on("change", ".skoring_menangis", function(){
        if($(this).is(":checked")) {
            labelMenangis = parseInt($(this).val())
            $(".label_menangis").html(labelMenangis)
            updateSkor()
        }
    })
    $(document).on("change", ".skoring_pernafasan", function(){
        if($(this).is(":checked")) {
            labelPernafasan = parseInt($(this).val())
            $(".label_pernafasan").html(labelPernafasan)
            updateSkor()
        }
    })
    $(document).on("change", ".skoring_lengan", function(){
        if($(this).is(":checked")) {
            labelLengan = parseInt($(this).val())
            $(".label_lengan").html(labelLengan)
            updateSkor()
        }
    })
    $(document).on("change", ".skoring_kaki", function(){
        if($(this).is(":checked")) {
            labelKaki = parseInt($(this).val())
            $(".label_kaki").html(labelKaki)
            updateSkor()
        }
    })
    $(document).on("change", ".skoring_kesadaran", function(){
        if($(this).is(":checked")) {
            labelKesadaran = parseInt($(this).val())
            $(".label_kesadaran").html(labelKesadaran)
            updateSkor()
        }
    })
    
    function updateSkor() {
        var totalSkor = labelOtot + labelMenangis + labelPernafasan + labelLengan + labelKaki + labelKesadaran
        $(".total_skor").html(totalSkor)
    }

    if(asesmenMedisId) {
        if(skoring_otot) {
            labelOtot = parseInt(skoring_otot)
            $(".label_otot").html(labelOtot)
            updateSkor()
        }
        if(skoring_menangis) {
            labelMenangis = parseInt(skoring_menangis)
            $(".label_menangis").html(labelMenangis)
            updateSkor()
        }
        if(skoring_pernafasan) {
            labelPernafasan = parseInt(skoring_pernafasan)
            $(".label_pernafasan").html(labelPernafasan)
            updateSkor()
        }
        if(skoring_lengan) {
            labelLengan = parseInt(skoring_lengan)
            $(".label_lengan").html(labelLengan)
            updateSkor()
        }
        if(skoring_kaki) {
            labelKaki = parseInt(skoring_kaki)
            $(".label_kaki").html(labelKaki)
            updateSkor()
        }
        if(skoring_kesadaran) {
            labelKesadaran = parseInt(skoring_kesadaran)
            $(".label_kesadaran").html(labelKesadaran)
            updateSkor()
        }
    }
})

', View::POS_END);
?>