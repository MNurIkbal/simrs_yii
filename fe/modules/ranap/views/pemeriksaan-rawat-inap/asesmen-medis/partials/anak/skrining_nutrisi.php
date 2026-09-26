<?php
use yii\helpers\Html;
use yii\web\View;

$classForm = 'form-control';
$classFormNumber = 'form-control doco-number';
$classNameSkriningPenyakit = 'skrining_penyakit';
$skriningPenyakit = 'AnakForm[skrining_penyakit][]';
$classNameSkriningKurus = 'skrining_kurus';
$skriningKurus = 'AnakForm[skrining_kurus][]';
$classNameSkriningDiare = 'skrining_diare';
$skriningDiare = 'AnakForm[skrining_diare][]';
$classNameSkriningBb = 'skrining_bb';
$skriningBb = 'AnakForm[skrining_bb][]';
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
            <h5 class="panel-title">O. Skrining Nutrisi (Gizi Awal) </h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <table style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="font-weight:bold;">Parameter (Modifikasi Strong Kids, Untuk Pasien Anak)</th>
                                        <th class="text-center" style="font-weight:bold;">Skor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1. Apakah ada penyakit yang berisiko malnutrisi atau apakah ada tindakan pembedahan besar ?</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;a. Ya</td>
                                        <td style="text-align:center;width:15%;">
                                            2 &nbsp; <input type="radio" class="<?=$classNameSkriningPenyakit?>" name="<?=$skriningPenyakit?>" value="2">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;b. Tidak</td>
                                        <td style="text-align:center;width:15%;">
                                            0 &nbsp; <input type="radio" class="<?=$classNameSkriningPenyakit?>" name="<?=$skriningPenyakit?>" value="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2. Apakah pasien tampak kurus ?</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;a. Ya</td>
                                        <td style="text-align:center;width:15%;">
                                            1 &nbsp; <input type="radio" class="<?=$classNameSkriningKurus?>" name="<?=$skriningKurus?>" value="1">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;b. Tidak</td>
                                        <td style="text-align:center;width:15%;">
                                            0 &nbsp; <input type="radio" class="<?=$classNameSkriningKurus?>" name="<?=$skriningKurus?>" value="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p>3. Apakah terdapat salah satu dari kondisi berikut (dalam 1 minggu terakhir) ?</p>
                                            <p>&nbsp;&nbsp;&nbsp;&nbsp;Diare > 5x/hari dan/atau muntah > 3x/hari, asupan makan berkurang</p>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;a. Ya</td>
                                        <td style="text-align:center;width:15%;">
                                            1 &nbsp; <input type="radio" class="<?=$classNameSkriningDiare?>" name="<?=$skriningDiare?>" value="1">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;b. Tidak</td>
                                        <td style="text-align:center;width:15%;">
                                            0 &nbsp; <input type="radio" class="<?=$classNameSkriningDiare?>" name="<?=$skriningDiare?>" value="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p>4. Apakah terjadi penurunan berat badan atau tidak adanya peningkatan berat badan dalam 1 bulan terakhir </p>
                                            <p>&nbsp;&nbsp;&nbsp;&nbsp;(berdasarkan penilaian objektif dari berat badan bila ada atau penilaian subjektif dari orang tua)</p>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;a. Ya</td>
                                        <td style="text-align:center;width:15%;">
                                            1 &nbsp; <input type="radio" class="<?=$classNameSkriningBb?>" name="<?=$skriningBb?>" value="1">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;b. Tidak</td>
                                        <td style="text-align:center;width:15%;">
                                            0 &nbsp; <input type="radio" class="<?=$classNameSkriningBb?>" name="<?=$skriningBb?>" value="0">
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td style="text-align:center;font-weight:bold;">Jumlah Skor</td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;"> <span class="total_skor_nutrisi"></span> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="font-weight:bold;font-style:italic;">
                                            <?= $form->field($model, 'kategori_skor_modifikasi')
                                            ->label(Yii::t('fe', 'Kriteria Penilaian Skor Modifikasi Strong Kids :'))
                                            ->radioList(
                                                [
                                                    '1' => 'Skor 0 : Tidak Risiko Malnutrisi',
                                                    '2' => 'Skor 1 -3 : Risiko Malnutrisi',
                                                    '3' => 'Skor 4 -5 : Malnutrisi',
                                                ],
                                                [
                                                    'itemOptions' => [
                                                        'class' => 'kategori_skor_modifikasi'
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
var _skriningPenyakit = ' . json_encode($model->skrining_penyakit) . ';
var _skriningKurus = ' . json_encode($model->skrining_kurus) . ';
var _skriningDiare = ' . json_encode($model->skrining_diare) . ';
var _skriningBb = ' . json_encode($model->skrining_bb) . ';

$(document).ready(function(){
    var skriningPenyakit = skriningKurus = skriningDiare = skriningBb = 0
    var totalSkorNutrisi = 0
    $(".total_skor_nutrisi").html(totalSkorNutrisi)

    if(asesmenMedisId) {
        skriningPenyakit = _skriningPenyakit
        skriningKurus = _skriningKurus
        skriningDiare = _skriningDiare
        skriningBb = _skriningBb
        updateSkorNutrisi()
    }

    function updateSkorNutrisi() {
        totalSkorNutrisi = parseInt(skriningPenyakit) + parseInt(skriningKurus) + parseInt(skriningDiare) + parseInt(skriningBb)
        $(".total_skor_nutrisi").html(totalSkorNutrisi)
    }
    $(document).on("change", ".skrining_penyakit", function(){
        if($(this).is(":checked")) {
            skriningPenyakit = parseInt($(this).val())
            updateSkorNutrisi()
        }
    })
    $(document).on("change", ".skrining_kurus", function(){
        if($(this).is(":checked")) {
            skriningKurus = parseInt($(this).val())
            updateSkorNutrisi()
        }
    })
    $(document).on("change", ".skrining_diare", function(){
        if($(this).is(":checked")) {
            skriningDiare = parseInt($(this).val())
            updateSkorNutrisi()
        }
    })
    $(document).on("change", ".skrining_bb", function(){
        if($(this).is(":checked")) {
            skriningBb = parseInt($(this).val())
            updateSkorNutrisi()
        }
    })

    if(_skriningPenyakit) {
        _skriningPenyakit = (typeof _skriningPenyakit[0] != "undefined") ? _skriningPenyakit[0] : 0
    }
    if(_skriningKurus) {
        _skriningKurus = (typeof _skriningKurus[0] != "undefined") ? _skriningKurus[0] : 0
    }
    if(_skriningDiare) {
        _skriningDiare = (typeof _skriningDiare[0] != "undefined") ? _skriningDiare[0] : 0
    }
    if(_skriningBb) {
        _skriningBb = (typeof _skriningBb[0] != "undefined") ? _skriningBb[0] : 0
    }

    $(".skrining_penyakit").each(function(el){
        if(_skriningPenyakit) {
            if($(this).val() == _skriningPenyakit) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $(".skrining_kurus").each(function(el){
        if(_skriningKurus) {
            if($(this).val() == _skriningKurus) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $(".skrining_diare").each(function(el){
        if(_skriningDiare) {
            if($(this).val() == _skriningDiare) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $(".skrining_bb").each(function(el){
        if(_skriningBb) {
            if($(this).val() == _skriningBb) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
})

', View::POS_END);
?>
