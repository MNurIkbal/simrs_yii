<?php
use yii\helpers\Html;
use yii\web\View;

$classForm = 'form-control';
$classFormNumber = 'form-control doco-number';
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
            <h5 class="panel-title">M. Penilaian Resiko Dekubitus <span style="font-style:italic;">(Skala Norton)</span> </h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <table style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="font-weight:bold;">Parameter/Yang Dinilai</th>
                                        <th class="text-center" style="font-weight:bold;">4</th>
                                        <th class="text-center" style="font-weight:bold;">3</th>
                                        <th class="text-center" style="font-weight:bold;">2</th>
                                        <th class="text-center" style="font-weight:bold;">1</th>
                                        <th class="text-center" style="font-weight:bold;">Skor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Kondisi Fisik Umum</td>
                                        <td>Baik</td>
                                        <td>Sedang/Cukup Baik</td>
                                        <td>Buruk</td>
                                        <td>Sangat Buruk</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_kondisi_fisik')
                                            ->label(false)
                                            ->radioList([
                                                4 => 4,
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Status Mental/Kesadaran</td>
                                        <td>Sadar/Komposmentis</td>
                                        <td>Apatis</td>
                                        <td>Bingung/Disorientasi</td>
                                        <td>Stupor/Koma</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_status_mental')
                                            ->label(false)
                                            ->radioList([
                                                4 => 4,
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Aktivitas</td>
                                        <td>Jalan Sendiri</td>
                                        <td>Jalan Dengan Bantuan</td>
                                        <td>Hanya Duduk/Kursi Roda</td>
                                        <td>Bedrest/Di Tempat Tidur</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_aktifitas')
                                            ->label(false)
                                            ->radioList([
                                                4 => 4,
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Mobilitas</td>
                                        <td>Bebas Bergerak</td>
                                        <td>Gerak Terbatas</td>
                                        <td>Sangat Terbatas</td>
                                        <td>Tidak Bergerak</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_mobilitas')
                                            ->label(false)
                                            ->radioList([
                                                4 => 4,
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Inkontinensia</td>
                                        <td>Tidak/Kontinen</td>
                                        <td>Kadang Inkontinen</td>
                                        <td>Selalu Inkontinen</td>
                                        <td>Inkontinen Urin & Alvi</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_inkontinensia')
                                            ->label(false)
                                            ->radioList([
                                                4 => 4,
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-center" style="font-weight:bold;">Kategori Risiko Dekubitus</td>
                                        <td style="text-align:center;font-weight:bold;"> Jumlah Skor </td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;"> <span class="total_skor_dekubitus"></span> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" style="text-align:center;font-weight:bold;font-style:italic;">
                                            <?= $form->field($model, 'kategori_dekubitus')
                                            ->label(false)
                                            ->radioList(
                                                [
                                                    '1' => '16 - 20 : Tidak Ada Risiko/Risiko Rendah',
                                                    '2' => '12 - 15 : Rentan Risiko/Risiko Sedang',
                                                    '3' => '< 12 : Risiko Tinggi',
                                                ],
                                                [
                                                    'inline' => true,
                                                    'itemOptions' => [
                                                        'class' => 'kategori_dekubitus'
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
var skorKondisiFisik = "'.$model->skor_kondisi_fisik.'"
var skorStatusMental = "'.$model->skor_status_mental.'"
var skorAktifitas = "'.$model->skor_aktifitas.'"
var skorMobilitas = "'.$model->skor_mobilitas.'"
var skorInkontinensia = "'.$model->skor_inkontinensia.'"

$(document).ready(function(){
    var kondisiFisik = statusMental = aktifitas = mobilitas = inkontinensia = 0
    var totalSkorDekubitus = 0
    $(".total_skor_dekubitus").html(totalSkorDekubitus)

    if(asesmenMedisId) {
        kondisiFisik = skorKondisiFisik ? skorKondisiFisik : 0
        statusMental = skorStatusMental ? skorStatusMental : 0
        aktifitas = skorAktifitas ? skorAktifitas : 0
        mobilitas = skorMobilitas ? skorMobilitas : 0
        inkontinensia = skorInkontinensia ? skorInkontinensia : 0
        updateSkorDekubitus()
    }
    
    function updateSkorDekubitus() {
        totalSkorDekubitus = parseInt(kondisiFisik) + parseInt(statusMental) + parseInt(aktifitas) + parseInt(mobilitas) + parseInt(inkontinensia)
        $(".total_skor_dekubitus").html(totalSkorDekubitus)
    }
    
    $(document).on("change", "input[name=\'AnakForm[skor_kondisi_fisik]\']", function(){
        if($(this).is(":checked")) {
            kondisiFisik = parseInt($(this).val())
            updateSkorDekubitus()
        }
    })
    $(document).on("change", "input[name=\'AnakForm[skor_status_mental]\']", function(){
        if($(this).is(":checked")) {
            statusMental = parseInt($(this).val())
            updateSkorDekubitus()
        }
    })
    $(document).on("change", "input[name=\'AnakForm[skor_aktifitas]\']", function(){
        if($(this).is(":checked")) {
            aktifitas = parseInt($(this).val())
            updateSkorDekubitus()
        }
    })
    $(document).on("change", "input[name=\'AnakForm[skor_mobilitas]\']", function(){
        if($(this).is(":checked")) {
            mobilitas = parseInt($(this).val())
            updateSkorDekubitus()
        }
    })
    $(document).on("change", "input[name=\'AnakForm[skor_inkontinensia]\']", function(){
        if($(this).is(":checked")) {
            inkontinensia = parseInt($(this).val())
            updateSkorDekubitus()
        }
    })
})

', View::POS_END);
?>
