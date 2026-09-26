<?php
use yii\helpers\Html;
use yii\web\View;
use kartik\date\DatePicker;

$classForm = 'form-control input-sm';
$classFormNumber = 'form-control doco-number';
$skriningPenyakit = 'skrining_penyakit[]';
$styleTable = 'text-align:center;font-weight:bold;';
$classCenter = 'text-center';
$styleCells = 'width:250px;margin-top:10px;margin-bottom:10px;';
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
            <h5 class="panel-title">K. Skrining Faktor Risiko Pasien Pulang </h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <table style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="<?=$styleTable?>" id="header1">Rencana tanggal pasien pulang : Apakah pasien/keluarga tahu rencana pulangnya ?</th>
                                        <th colspan="3" style="<?=$styleTable?>" id="header2">
                                            <?= $form->field($model, 'checked_risiko')
                                            ->label(false)
                                            ->radioList(
                                                [
                                                    '1' => 'Ya',
                                                    '0' => 'Tidak',
                                                ],
                                                [
                                                    'inline' => true,
                                                    'itemOptions' => [
                                                        'class' => 'checked_risiko'
                                                    ]
                                                ]
                                            ); ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th style="<?=$styleTable?>" id="header3">Faktor Risiko Pasien Pulang</th>
                                        <th style="<?=$styleTable?>" id="header4">Ya</th>
                                        <th style="<?=$styleTable?>" id="header5">Tidak</th>
                                        <th style="<?=$styleTable?>" id="header6">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1. Usia > 65 tahun</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko1_1" name="GinekologiForm[risiko_1][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko1_0" name="GinekologiForm[risiko_1][]" value="0"> </td>
                                        <td><input type="text" name="GinekologiForm[keterangan_1]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>2. Keterbatasan Mobilitas</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko2_1" name="GinekologiForm[risiko_2][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko2_0" name="GinekologiForm[risiko_2][]" value="0"> </td>
                                        <td><input type="text" name="GinekologiForm[keterangan_2]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>3. Bantuan untuk melakukan aktifitas sehari-hari</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko3_1" name="GinekologiForm[risiko_3][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko3_0" name="GinekologiForm[risiko_3][]" value="0"> </td>
                                        <td><input type="text" name="GinekologiForm[keterangan_3]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>4. Apakah pasien tinggal sendiri</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko4_1" name="GinekologiForm[risiko_4][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko4_0" name="GinekologiForm[risiko_4][]" value="0"> </td>
                                        <td><input type="text" name="GinekologiForm[keterangan_4]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>5. Apakah pasien dirumah ada yang merawat</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko5_1" name="GinekologiForm[risiko_5][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko5_0" name="GinekologiForm[risiko_5][]" value="0"> </td>
                                        <td><input type="text" name="GinekologiForm[keterangan_5]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>6. Bagaimana jenis tempat tinggal pasien</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko6_1" name="GinekologiForm[risiko_6][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko6_0" name="GinekologiForm[risiko_6][]" value="0"> </td>
                                        <td><input type="text" name="GinekologiForm[keterangan_6]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>7. Apakah tempat tinggal pasien ada tetangga</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko7_1" name="GinekologiForm[risiko_7][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko7_0" name="GinekologiForm[risiko_7][]" value="0"> </td>
                                        <td><input type="text" name="GinekologiForm[keterangan_7]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>8. Apakah pasien memiliki tanggung jawab memelihara anak/keluarga atau peliharaan dimiliki</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko8_1" name="GinekologiForm[risiko_8][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko8_0" name="GinekologiForm[risiko_8][]" value="0"> </td>
                                        <td><input type="text" name="GinekologiForm[keterangan_8]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>9. Apakah ketika pulang masih ada perawatan lanjutan yang harus dilakukan di rumah (rawat luka dll)</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko9_1" name="GinekologiForm[risiko_9][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko9_0" name="GinekologiForm[risiko_9][]" value="0"> </td>
                                        <td><input type="text" name="GinekologiForm[keterangan_9]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>10. Bagaimana transportasi pasien untuk pulang</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko10_1" name="GinekologiForm[risiko_10][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko10_0" name="GinekologiForm[risiko_10][]" value="0"> </td>
                                        <td><input type="text" name="GinekologiForm[keterangan_10]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="row">
                            <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">Perencanaan Pemulangan Pasien (Discharge Planning Awal) </p>
                        </div>
                        <div class="col-sm-12">
                        <table style="width:100%" class="table-pemulangan">
                                <thead>
                                    <tr>
                                        <th colspan="2" style="<?= $styleTable?>" id="header_lamarawat">
                                            <?= $form->field($model, 'perkiraan_lama_rawat')->textInput([
                                                'class' => 'form-control input-sm perkiraan_lama_rawat',
                                                'placeholder' => 'Perkiraan Lama Rawat'
                                            ]);
                                            ?></th>
                                        <th colspan="2" style="<?= $styleTable?>" id="header_tglpulang">
                                            <?= $form->field($model, 'perkiraan_tanggal_pulang')->widget(DatePicker::classname(), [
                                                'options' => ['placeholder' => 'Perkiraan Tanggal Pulang'],
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'yyyy-mm-dd',
                                                    'todayHighlight' => true,
                                                    'orientation' => 'bottom'
                                                ]
                                            ]);
                                            ?>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="<?=$classCenter?>" id="jp">Jenis Pemeriksaan</th>
                                        <th class="<?=$classCenter?>" id="ap">Asal Pemeriksaan</th>
                                        <th class="<?=$classCenter?>" id="jml">Jumlah</th>
                                        <th class="<?=$classCenter?>" id="aksi">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="margin-bottom:5px;margin-top:30px;text-align: center;">
                                        <td>
                                            <input type="text" name="GinekologiForm[jenis_pemeriksaan][]" id="jenis_pemeriksaan_1" class="jenis_pemeriksaan form-control input-sm" style="<?=$styleCells?>">
                                        </td>
                                        <td>
                                            <input type="text" name="GinekologiForm[asal_pemeriksaan][]" id="asal_pemeriksaan_1" class="asal_pemeriksaan form-control input-sm" style="<?=$styleCells?>">
                                        </td>
                                        <td>
                                            <input type="text" name="GinekologiForm[jumlah_pemeriksaan][]" id="jumlah_pemeriksaan_1" class="jumlah_pemeriksaan form-control input-sm" style="<?=$styleCells?>">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-success addRowPemeriksaan" name="addRowPemeriksaan"
                                                id="addRowPemeriksaan">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </td>
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

<?php
$this->registerJs('
var _classForm = "'.$classForm.'";
var _modelForm = "GinekologiForm"
var _styles = "width:250px;margin-top:10px;margin-bottom:10px;margin-left:10px;";
var _jenis_pemeriksaan = ' . json_encode($model->jenis_pemeriksaan) . ';
var _asal_pemeriksaan = ' . json_encode($model->asal_pemeriksaan) . ';
var _jumlah_pemeriksaan = ' . json_encode($model->jumlah_pemeriksaan) . ';

var _risiko1 = ' . json_encode($model->risiko_1) . ';
var _risiko2 = ' . json_encode($model->risiko_2) . ';
var _risiko3 = ' . json_encode($model->risiko_3) . ';
var _risiko4 = ' . json_encode($model->risiko_4) . ';
var _risiko5 = ' . json_encode($model->risiko_5) . ';
var _risiko6 = ' . json_encode($model->risiko_6) . ';
var _risiko7 = ' . json_encode($model->risiko_7) . ';
var _risiko8 = ' . json_encode($model->risiko_8) . ';
var _risiko9 = ' . json_encode($model->risiko_9) . ';
var _risiko10 = ' . json_encode($model->risiko_10) . ';

var _keterangan1 = "' . $model->keterangan_1 . '";
var _keterangan2 = "' . $model->keterangan_2 . '";
var _keterangan3 = "' . $model->keterangan_3 . '";
var _keterangan4 = "' . $model->keterangan_4 . '";
var _keterangan5 = "' . $model->keterangan_5 . '";
var _keterangan6 = "' . $model->keterangan_6 . '";
var _keterangan7 = "' . $model->keterangan_7 . '";
var _keterangan8 = "' . $model->keterangan_8 . '";
var _keterangan9 = "' . $model->keterangan_9 . '";
var _keterangan10 = "' . $model->keterangan_10 . '";

$(document).ready(function(){
    $(document).on("change", ".checked_risiko", function(){
        if($(this).val() == "1") {
            $(".risiko1_1").prop("checked", true);
            $(".risiko2_1").prop("checked", true);
            $(".risiko3_1").prop("checked", true);
            $(".risiko4_1").prop("checked", true);
            $(".risiko5_1").prop("checked", true);
            $(".risiko6_1").prop("checked", true);
            $(".risiko7_1").prop("checked", true);
            $(".risiko8_1").prop("checked", true);
            $(".risiko9_1").prop("checked", true);
            $(".risiko10_1").prop("checked", true);
        }
        else {
            $(".risiko1_0").prop("checked", true);
            $(".risiko2_0").prop("checked", true);
            $(".risiko3_0").prop("checked", true);
            $(".risiko4_0").prop("checked", true);
            $(".risiko5_0").prop("checked", true);
            $(".risiko6_0").prop("checked", true);
            $(".risiko7_0").prop("checked", true);
            $(".risiko8_0").prop("checked", true);
            $(".risiko9_0").prop("checked", true);
            $(".risiko10_0").prop("checked", true);
        }
    })
    
    if (_jenis_pemeriksaan) {
        $(".table-pemulangan tbody tr:first").remove();
        let rowDataJenisPemeriksaan;
        let rowCount = 0;
        $.each(_jenis_pemeriksaan, function(index, value) {
            rowCount++;
            let jenis_pemeriksaan = value;
            let asal_pemeriksaan = _asal_pemeriksaan[index] || "";
            let jumlah_pemeriksaan = _jumlah_pemeriksaan[index] || "";
            let _btnContent = rowCount === 1
                ? `<button type="button" class="btn btn-success addRowPemeriksaan" name="addRowPemeriksaan">
                        <i class="fa fa-plus"></i>
                    </button>`
                :
                `<button type="button" class="btn btn-danger deleteRowPemeriksaan ms-1">
                    <i class="fa fa-trash"></i>
                </button>`;
            
            rowDataJenisPemeriksaan = `
                <tr>
                    <td>
                        <input type="text" name="${_modelForm}[jenis_pemeriksaan][]" id="jenis_pemeriksaan_${rowCount}" class="jenis_pemeriksaan ${_classForm}" style="${_styles}" value="${jenis_pemeriksaan}">
                    </td>
                    <td>
                        <input type="text" name="${_modelForm}[asal_pemeriksaan][]" id="asal_pemeriksaan_${rowCount}" class="asal_pemeriksaan ${_classForm}" style="${_styles}" value="${asal_pemeriksaan}">
                    </td>
                    <td>
                        <input type="text" name="${_modelForm}[jumlah_pemeriksaan][]" id="jumlah_pemeriksaan_${rowCount}" class="jumlah_pemeriksaan ${_classForm}" style="${_styles}" value="${jumlah_pemeriksaan}">
                    </td>
                    <td style="text-align: center;">
                        ${_btnContent}
                    </td>
                </tr>
            `;

            $(".table-pemulangan tbody").append(rowDataJenisPemeriksaan);
        })
    }

    $("table tbody").on("click", ".addRowPemeriksaan", function () {
        const $table = $(this).closest("table");
        const _jenisPemeriksaan = $(".jenis_pemeriksaan").val()
        const _asalPemeriksaan = $(".asal_pemeriksaan").val()
        const _jumlahPemeriksaan = $(".jumlah_pemeriksaan").val()

        const newRowPemeriksaan = `
            <tr style="margin-bottom:5px;margin-top:30px;">
                <td>
                    <input type="text" name="${_modelForm}[jenis_pemeriksaan][]" class="${_classForm}" style="${_styles}" value="${_jenisPemeriksaan}">
                </td>
                <td>
                    <input type="text" name="${_modelForm}[asal_pemeriksaan][]" class="${_classForm}" style="${_styles}" value="${_asalPemeriksaan}">
                </td>
                <td>
                    <input type="text" name="${_modelForm}[jumlah_pemeriksaan][]" class="${_classForm}" style="${_styles}" value="${_jumlahPemeriksaan}">
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn btn-danger deleteRowPemeriksaan ms-1">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $table.find("tbody").append(newRowPemeriksaan);
        resetForm()
    });

    $("table tbody").on("click", ".deleteRowPemeriksaan", function () {
        const $table = $(this).closest("table");
        $(this).closest("tr").remove();
    });
    
    if(_risiko1) {
        _risiko1 = (typeof _risiko1[0] != "undefined") ? _risiko1[0] : 0
    }
    if(_risiko2) {
        _risiko2 = (typeof _risiko2[0] != "undefined") ? _risiko2[0] : 0
    }
    if(_risiko3) {
        _risiko3 = (typeof _risiko3[0] != "undefined") ? _risiko3[0] : 0
    }
    if(_risiko4) {
        _risiko4 = (typeof _risiko4[0] != "undefined") ? _risiko4[0] : 0
    }
    if(_risiko5) {
        _risiko5 = (typeof _risiko5[0] != "undefined") ? _risiko5[0] : 0
    }
    if(_risiko6) {
        _risiko6 = (typeof _risiko6[0] != "undefined") ? _risiko6[0] : 0
    }
    if(_risiko7) {
        _risiko7 = (typeof _risiko7[0] != "undefined") ? _risiko7[0] : 0
    }
    if(_risiko8) {
        _risiko8 = (typeof _risiko8[0] != "undefined") ? _risiko8[0] : 0
    }
    if(_risiko9) {
        _risiko9 = (typeof _risiko9[0] != "undefined") ? _risiko9[0] : 0
    }
    if(_risiko10) {
        _risiko10 = (typeof _risiko10[0] != "undefined") ? _risiko10[0] : 0
    }

    $("input[name=\'GinekologiForm[risiko_1][]\']").each(function(el){
        if(_risiko1) {
            if($(this).val() == _risiko1) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'GinekologiForm[risiko_2][]\']").each(function(el){
        if(_risiko2) {
            if($(this).val() == _risiko2) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'GinekologiForm[risiko_3][]\']").each(function(el){
        if(_risiko3) {
            if($(this).val() == _risiko3) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'GinekologiForm[risiko_4][]\']").each(function(el){
        if(_risiko4) {
            if($(this).val() == _risiko4) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'GinekologiForm[risiko_5][]\']").each(function(el){
        if(_risiko5) {
            if($(this).val() == _risiko5) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'GinekologiForm[risiko_6][]\']").each(function(el){
        if(_risiko6) {
            if($(this).val() == _risiko6) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'GinekologiForm[risiko_7][]\']").each(function(el){
        if(_risiko7) {
            if($(this).val() == _risiko7) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'GinekologiForm[risiko_8][]\']").each(function(el){
        if(_risiko8) {
            if($(this).val() == _risiko8) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'GinekologiForm[risiko_9][]\']").each(function(el){
        if(_risiko9) {
            if($(this).val() == _risiko9) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'GinekologiForm[risiko_10][]\']").each(function(el){
        if(_risiko10) {
            if($(this).val() == _risiko10) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })

    if(asesmenMedisId) {
        $("input[name=\'GinekologiForm[keterangan_1]\']").val(_keterangan1)
        $("input[name=\'GinekologiForm[keterangan_2]\']").val(_keterangan2)
        $("input[name=\'GinekologiForm[keterangan_3]\']").val(_keterangan3)
        $("input[name=\'GinekologiForm[keterangan_4]\']").val(_keterangan4)
        $("input[name=\'GinekologiForm[keterangan_5]\']").val(_keterangan5)
        $("input[name=\'GinekologiForm[keterangan_6]\']").val(_keterangan6)
        $("input[name=\'GinekologiForm[keterangan_7]\']").val(_keterangan7)
        $("input[name=\'GinekologiForm[keterangan_8]\']").val(_keterangan8)
        $("input[name=\'GinekologiForm[keterangan_9]\']").val(_keterangan9)
        $("input[name=\'GinekologiForm[keterangan_10]\']").val(_keterangan10)
    }
    
    function resetForm() {
        $("#jenis_pemeriksaan_1").val(null)
        $("#asal_pemeriksaan_1").val(null)
        $("#jumlah_pemeriksaan_1").val(null)
    }
})

', View::POS_END);
?>
