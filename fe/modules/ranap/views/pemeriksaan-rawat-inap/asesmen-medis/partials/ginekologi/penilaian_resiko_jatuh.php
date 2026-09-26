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
            <h5 class="panel-title">F. Penilaian Resiko Jatuh <span style="font-style:italic;">(Skala Morse)</span> </h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <table style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="font-weight:bold;">Faktor Risiko</th>
                                        <th class="text-center" style="font-weight:bold;">Skala</th>
                                        <th class="text-center" style="font-weight:bold;">Nilai Skor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Riwayat Jatuh Dalam 3 Bulan Terakhir</td>
                                        <td style="text-align:center;width:10%">
                                            <?= $form->field($model, 'riwayat_jatuh')
                                            ->label(false)
                                            ->radioList(
                                                [
                                                    '0' => 'Tidak',
                                                    '25' => 'Ya',
                                                ],
                                                [
                                                    'itemOptions' => [
                                                        'class' => 'riwayat_jatuh'
                                                    ]
                                                ]
                                            ); ?>
                                        </td>
                                        <td style="text-align:center;width:10%;">
                                            <span class="skor_riwayat_jatuh" style="font-size:20px;font-weight:bold;"></span>
                                            <input type="hidden" name="GinekologiForm[skor_riwayat_jatuh]">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Diagnosis Sekunder</td>
                                        <td style="text-align:center;width:10%">
                                            <?= $form->field($model, 'diagnosis_sekunder')
                                            ->label(false)
                                            ->radioList(
                                                [
                                                    '0' => 'Tidak',
                                                    '15' => 'Ya',
                                                ],
                                                [
                                                    'itemOptions' => [
                                                        'class' => 'diagnosis_sekunder'
                                                    ]
                                                ]
                                            ); ?>
                                        </td>
                                        <td style="text-align:center;width:10%;">
                                            <span class="skor_diagnosis_sekunder" style="font-size:20px;font-weight:bold;"></span>
                                            <input type="hidden" name="GinekologiForm[skor_diagnosis_sekunder]">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Ambulasi/Alat Bantu Jalan :</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Berest,/Kursi Roda/Bantuan Perawat</td>
                                        <td rowspan="3" style="text-align:center;width:10%">
                                            <?= $form->field($model, 'ambulasi')
                                            ->label(false)
                                            ->radioList(
                                                [
                                                    '0' => '0',
                                                    '15' => '15',
                                                    '30' => '30',
                                                ],
                                                [
                                                    'itemOptions' => [
                                                        'class' => 'ambulasi'
                                                    ]
                                                ]
                                            ); ?>
                                        </td>
                                        <td rowspan="3" style="text-align:center;width:10%;">
                                            <span class="skor_ambulasi" style="font-size:20px;font-weight:bold;"></span>
                                            <input type="hidden" name="GinekologiForm[skor_ambulasi]">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Kruk/Tongkat/Walker</td>
                                    </tr>
                                    <tr>
                                        <td>Furniture/Berpegangan Pada Benda-Benda Sekitar</td>
                                    </tr>
                                    <tr>
                                        <td>Pasien Terpasang Infus/I.V Line/Heparin Lock</td>
                                        <td style="text-align:center;width:10%">
                                            <?= $form->field($model, 'terpasang_infus')
                                            ->label(false)
                                            ->radioList(
                                                [
                                                    '0' => 'Tidak',
                                                    '20' => 'Ya',
                                                ],
                                                [
                                                    'itemOptions' => [
                                                        'class' => 'terpasang_infus'
                                                    ]
                                                ]
                                            ); ?>
                                        </td>
                                        <td style="text-align:center;width:10%;">
                                            <span class="skor_terpasang_infus" style="font-size:20px;font-weight:bold;"></span>
                                            <input type="hidden" name="GinekologiForm[skor_terpasang_infus]">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Gaya Berjalan/Cara Berpindah :</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Normal/Bedrest/Imobilisasi</td>
                                        <td rowspan="3" style="text-align:center;width:10%">
                                            <?= $form->field($model, 'gaya_berjalan')
                                            ->label(false)
                                            ->radioList(
                                                [
                                                    '0' => '0',
                                                    '10' => '10',
                                                    '20' => '20',
                                                ],
                                                [
                                                    'itemOptions' => [
                                                        'class' => 'gaya_berjalan'
                                                    ]
                                                ]
                                            ); ?>
                                        </td>
                                        <td rowspan="3" style="text-align:center;width:10%;">
                                            <span class="skor_gaya_berjalan" style="font-size:20px;font-weight:bold;"></span>
                                            <input type="hidden" name="GinekologiForm[skor_gaya_berjalan]">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Lemah/Tidak Bertenaga</td>
                                    </tr>
                                    <tr>
                                        <td>Terganggu/Tidak Normal (Pincang/Diseret)</td>
                                    </tr>
                                    <tr>
                                        <td>Status Mental :</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Orientasi Baik (Pasien Menyadari Kondisi Dirinya)</td>
                                        <td rowspan="2" style="text-align:center;width:10%">
                                            <?= $form->field($model, 'status_mental_rj')
                                            ->label(false)
                                            ->radioList(
                                                [
                                                    '0' => '0',
                                                    '15' => '15',
                                                ],
                                                [
                                                    'itemOptions' => [
                                                        'class' => 'status_mental_rj'
                                                    ]
                                                ]
                                            ); ?>
                                        </td>
                                        <td rowspan="2" style="text-align:center;width:10%;">
                                            <span class="skor_status_mental_rj" style="font-size:20px;font-weight:bold;"></span>
                                            <input type="hidden" name="GinekologiForm[skor_status_mental_rj]">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Keterbatasan Daya Ingat</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="text-align:center;font-weight:bold;">Total Skor</td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;">
                                            <span class="total_skor_risiko_jatuh"></span>
                                            <input type="hidden" name="GinekologiForm[total_skor_risiko_jatuh]">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="font-weight:bold;font-style:italic;">
                                            <p>&nbsp;</p>
                                            <p style="margin-left:10px;">Kriteria Penilaian Hasil :</p>
                                            <?= $form->field($model, 'kriteria_penilaian_hasil')
                                            ->label(false)
                                            ->radioList(
                                                [
                                                    '1' => 'Skor 0 - 24 : Risiko Rendah',
                                                    '2' => 'Skor 25 - 44 : Risiko Sedang',
                                                    '3' => 'Skor > 45 : Risiko Berat',
                                                ],
                                                [
                                                    'itemOptions' => [
                                                        'class' => 'kriteria_penilaian_hasil'
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
var riwayat_jatuh = ' . json_encode($model->riwayat_jatuh) . ';
var diagnosis_sekunder = ' . json_encode($model->diagnosis_sekunder) . ';
var ambulasi = ' . json_encode($model->ambulasi) . ';
var terpasang_infus = ' . json_encode($model->terpasang_infus) . ';
var gaya_berjalan = ' . json_encode($model->gaya_berjalan) . ';
var status_mental_rj = ' . json_encode($model->status_mental_rj) . ';
var total_skor_risiko_jatuh = ' . json_encode($model->total_skor_risiko_jatuh) . ';

$(document).ready(function(){
    if(!total_skor_risiko_jatuh) {
        total_skor_risiko_jatuh = 0
    }
    
    var skorRiwayat = $(".skor_riwayat_jatuh")
    var skorDiagnosis = $(".skor_diagnosis_sekunder")
    var skorAmbulasi = $(".skor_ambulasi")
    var skorInfus = $(".skor_terpasang_infus")
    var skorBerjalan = $(".skor_gaya_berjalan")
    var skorMental = $(".skor_status_mental_rj")
    var totalSkor = $(".total_skor_risiko_jatuh")
    var totalSkorValue = $("input[name=\'GinekologiForm[total_skor_risiko_jatuh]\']")

    totalSkor.html(total_skor_risiko_jatuh)
    totalSkorValue.val(total_skor_risiko_jatuh);
    
    if(asesmenMedisId) {
        skorRiwayat.html(parseInt(riwayat_jatuh))
        skorDiagnosis.html(parseInt(diagnosis_sekunder))
        skorAmbulasi.html(parseInt(ambulasi))
        skorInfus.html(parseInt(terpasang_infus))
        skorBerjalan.html(parseInt(gaya_berjalan))
        skorMental.html(parseInt(status_mental_rj))
        
        setSkor()
    }
    
    function setSkor() {
        riwayat_jatuh = (typeof riwayat_jatuh != "undefined") ? parseInt(riwayat_jatuh) : 0
        diagnosis_sekunder = (typeof diagnosis_sekunder != "undefined") ? parseInt(diagnosis_sekunder) : 0
        terpasang_infus = (typeof terpasang_infus != "undefined") ? parseInt(terpasang_infus) : 0
        ambulasi = (typeof ambulasi != "undefined") ? parseInt(ambulasi) : 0
        gaya_berjalan = (typeof gaya_berjalan != "undefined") ? parseInt(gaya_berjalan) : 0
        status_mental_rj = (typeof status_mental_rj != "undefined") ? parseInt(status_mental_rj) : 0
        
        total_skor_risiko_jatuh =
            riwayat_jatuh + diagnosis_sekunder +
            ambulasi + terpasang_infus +
            gaya_berjalan + status_mental_rj

        totalSkor.html(total_skor_risiko_jatuh)
        totalSkorValue.val(total_skor_risiko_jatuh);
    }

    $(document).on("change", ".riwayat_jatuh", function(){
        if($(this).is(":checked")) {
            riwayat_jatuh = parseInt($(this).val())
            skorRiwayat.html(riwayat_jatuh)
            $("input[name=\'GinekologiForm[skor_riwayat_jatuh]\']").val(riwayat_jatuh);
            setSkor()
        }
    })
    $(document).on("change", ".diagnosis_sekunder", function(){
        if($(this).is(":checked")) {
            diagnosis_sekunder = parseInt($(this).val())
            skorDiagnosis.html(diagnosis_sekunder)
            $("input[name=\'GinekologiForm[skor_diagnosis_sekunder]\']").val(diagnosis_sekunder);
            setSkor()
        }
    })
    $(document).on("change", ".ambulasi", function(){
        if($(this).is(":checked")) {
            ambulasi = parseInt($(this).val())
            skorAmbulasi.html(ambulasi)
            $("input[name=\'GinekologiForm[skor_ambulasi]\']").val(ambulasi);
            setSkor()
        }
    })
    $(document).on("change", ".terpasang_infus", function(){
        if($(this).is(":checked")) {
            terpasang_infus = parseInt($(this).val())
            skorInfus.html(terpasang_infus)
            $("input[name=\'GinekologiForm[skor_terpasang_infus]\']").val(terpasang_infus);
            setSkor()
        }
    })
    $(document).on("change", ".gaya_berjalan", function(){
        if($(this).is(":checked")) {
            gaya_berjalan = parseInt($(this).val())
            skorBerjalan.html(gaya_berjalan)
            $("input[name=\'GinekologiForm[skor_gaya_berjalan]\']").val(gaya_berjalan);
            setSkor()
        }
    })
    $(document).on("change", ".status_mental_rj", function(){
        status_mental_rj = parseInt($(this).val())
        skorMental.html(status_mental_rj)
        setSkor()
    })
})

', View::POS_END);
?>
