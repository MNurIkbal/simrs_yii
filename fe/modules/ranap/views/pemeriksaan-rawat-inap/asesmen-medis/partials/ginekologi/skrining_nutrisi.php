<?php
use yii\helpers\Html;
use yii\web\View;

$classForm = 'form-control';
$classFormNumber = 'form-control doco-number';
$separator = '[]';
$modelForm = 'GinekologiForm';
$asupanMakan = 'skrining_asupan_makan';
$metabolisme = 'skrining_metabolisme';
$bb = 'skrining_bb';
$hb = 'skrining_hb';

$skriningAsupanMakan = $modelForm.'['.$asupanMakan.']'.$separator;
$skriningMetabolisme = $modelForm.'['.$metabolisme.']'.$separator;
$skriningBb = $modelForm.'['.$bb.']'.$separator;
$skriningHb = $modelForm.'['.$hb.']'.$separator;
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
            <h5 class="panel-title">I. Skrining Nutrisi (Gizi Awal) </h5>
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
                                        <th colspan="2" class="text-center" style="font-weight:bold;">Penilaian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1. Apakah asupan makan berkurang karena tidak ada nafsu makan ?</td>
                                        <td style="text-align:center;width:15%;">
                                            Ya &nbsp; <input type="radio" class="<?=$asupanMakan?>" name="<?=$skriningAsupanMakan?>" value="1">
                                        </td>
                                        <td style="text-align:center;width:15%;">
                                            Tidak &nbsp; <input type="radio" class="<?=$asupanMakan?>" name="<?=$skriningAsupanMakan?>" value="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2. Ada gangguan metabolisme ? (DM, gangguan fungsi tyroid, infeksi kronis seperti TB, HIV/AIDS, lupus, lain-lain, sebutkan ...)</td>
                                        <td style="text-align:center;width:15%;">
                                            Ya &nbsp; <input type="radio" class="<?=$metabolisme?>" name="<?=$skriningMetabolisme?>" value="1">
                                        </td>
                                        <td style="text-align:center;width:15%;">
                                            Tidak &nbsp; <input type="radio" class="<?=$metabolisme?>" name="<?=$skriningMetabolisme?>" value="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p>3. Apakah ada pertambahan berat badan yang tidak sesuai selama trimester kehamilan ?</p>
                                        </td>
                                        <td style="text-align:center;width:15%;">
                                            Ya &nbsp; <input type="radio" class="<?=$bb?>" name="<?=$skriningBb?>" value="1">
                                        </td>
                                        <td style="text-align:center;width:15%;">
                                            Tidak &nbsp; <input type="radio" class="<?=$bb?>" name="<?=$skriningBb?>" value="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p>4. Nilai Hb < 10 g/dl atau Hct < 30%</p>
                                        </td>
                                        <td style="text-align:center;width:15%;">
                                            Ya &nbsp; <input type="radio" class="<?=$hb?>" name="<?=$skriningHb?>" value="1">
                                        </td>
                                        <td style="text-align:center;width:15%;">
                                            Tidak &nbsp; <input type="radio" class="<?=$hb?>" name="<?=$skriningHb?>" value="0">
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td style="text-align:center;font-weight:bold;">Jumlah Skor</td>
                                        <td colspan="2" style="width:10%;text-align:center;font-size:20px;font-weight:bold;"> <span class="total_skor_nutrisi"></span> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="font-weight:bold;font-style:italic;">
                                            <p>&nbsp;</p>
                                            <p style="margin-left:10px;">Kriteria Penilaian :</p>
                                            <?= $form->field($model, 'kategori_skor_nutrisi')
                                            ->label(false)
                                            ->radioList(
                                                [
                                                    '1' => 'Bila Skor >= 0-3 wajib lapor DPJP, Dietisien melakukan asuhan gizi',
                                                    '2' => 'Bila Skor >= 4-5 wajib lapor DPJP dan disarankan untuk dirujuk ke dokter Sp.GK, Dietisien melakukan asuhan gizi',
                                                ],
                                                [
                                                    'itemOptions' => [
                                                        'class' => 'kategori_skor_nutrisi'
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
var skrining_asupan_makan = ' . json_encode($model->skrining_asupan_makan) . ';
var skrining_metabolisme = ' . json_encode($model->skrining_metabolisme) . ';
var skrining_bb = ' . json_encode($model->skrining_bb) . ';
var skrining_hb = ' . json_encode($model->skrining_hb) . ';

$(document).ready(function(){
    var skAsupanMakan = skMetabolisme = skBb = skHb = 0
    var totalSkorNutrisi = 0
    $(".total_skor_nutrisi").html(totalSkorNutrisi)

    if(asesmenMedisId) {
        skAsupanMakan = skrining_asupan_makan ? parseInt(skrining_asupan_makan) : 0
        skMetabolisme = skrining_metabolisme ? parseInt(skrining_metabolisme) : 0
        skBb = skrining_bb ? parseInt(skrining_bb) : 0
        skHb = skrining_hb ? parseInt(skrining_hb) : 0
        updateSkorNutrisi()
    }

    function updateSkorNutrisi() {
        totalSkorNutrisi = skAsupanMakan + skMetabolisme + skBb + skHb
        $(".total_skor_nutrisi").html(totalSkorNutrisi)
    }
    $(document).on("change", ".skrining_asupan_makan", function(){
        if($(this).is(":checked")) {
            skAsupanMakan = parseInt($(this).val())
            updateSkorNutrisi()
        }
    })
    $(document).on("change", ".skrining_metabolisme", function(){
        if($(this).is(":checked")) {
            skMetabolisme = parseInt($(this).val())
            updateSkorNutrisi()
        }
    })
    $(document).on("change", ".skrining_bb", function(){
        if($(this).is(":checked")) {
            skBb = parseInt($(this).val())
            updateSkorNutrisi()
        }
    })
    $(document).on("change", ".skrining_hb", function(){
        if($(this).is(":checked")) {
            skHb = parseInt($(this).val())
            updateSkorNutrisi()
        }
    })

    if(skrining_asupan_makan) {
        skrining_asupan_makan = (typeof skrining_asupan_makan[0] != "undefined") ? skrining_asupan_makan[0] : 0
    }
    if(skrining_metabolisme) {
        skrining_metabolisme = (typeof skrining_metabolisme[0] != "undefined") ? skrining_metabolisme[0] : 0
    }
    if(skrining_bb) {
        skrining_bb = (typeof skrining_bb[0] != "undefined") ? skrining_bb[0] : 0
    }
    if(skrining_hb) {
        skrining_hb = (typeof skrining_hb[0] != "undefined") ? skrining_hb[0] : 0
    }

    $(".skrining_asupan_makan").each(function(el){
        if(skrining_asupan_makan) {
            if($(this).val() == skrining_asupan_makan) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $(".skrining_metabolisme").each(function(el){
        if(skrining_metabolisme) {
            if($(this).val() == skrining_metabolisme) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $(".skrining_bb").each(function(el){
        if(skrining_bb) {
            if($(this).val() == skrining_bb) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $(".skrining_hb").each(function(el){
        if(skrining_hb) {
            if($(this).val() == skrining_hb) {
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
