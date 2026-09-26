<?php
use yii\helpers\Html;
use yii\web\View;
use kartik\date\DatePicker;

$classForm = 'form-control input-sm';
$classFormNumber = 'form-control doco-number';
$skriningPenyakit = 'skrining_penyakit[]';
$styleTable = 'text-align:center;font-weight:bold;';
$classCenter = 'text-center';
$styleCells = 'width:250px;margin-top:10px';
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
            <h5 class="panel-title">Q. Skrining Faktor Risiko Pasien Pulang </h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <table style="width:100%">
                                <thead>
                                    <tr>
                                        <th style="<?=$styleTable?>" id="header1">Rencana tanggal pasien pulang : Apakah orang tua/keluarga tahu rencana pulangnya ?</th>
                                        <th colspan="3" style="<?=$styleTable?>" id="header2">
                                            <input type="radio" class="checked_risiko" name="checked_risiko" value="1"> Ya
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <input type="radio" class="checked_risiko" name="checked_risiko" value="0"> Tidak
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
                                        <td>1. Apakah pasien perlu pelayanan home care ?</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_hc1" name="AnakForm[risiko_hc][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_hc0" name="AnakForm[risiko_hc][]" value="0"> </td>
                                        <td><input type="text" name="AnakForm[keterangan_hc]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>2. Apakah pasien perlu pemasangan implan ?</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_implan1" name="AnakForm[risiko_implan][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_implan0" name="AnakForm[risiko_implan][]" value="0"> </td>
                                        <td><input type="text" name="AnakForm[keterangan_implan]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>3. Apakah pasien telah dilakukan pemasangan alat ?</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_alat1" name="AnakForm[risiko_alat][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_alat0" name="AnakForm[risiko_alat][]" value="0"> </td>
                                        <td><input type="text" name="AnakForm[keterangan_alat]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>4. Apakah pasien perlu dirujuk ke tim terapis ?</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_terapis1" name="AnakForm[risiko_terapis][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_terapis0" name="AnakForm[risiko_terapis][]" value="0"> </td>
                                        <td><input type="text" name="AnakForm[keterangan_terapis]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>5. Apakah pasien perlu dirujuk ke tim ahli gizi ?</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_gizi1" name="AnakForm[risiko_gizi][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_gizi0" name="AnakForm[risiko_gizi][]" value="0"> </td>
                                        <td><input type="text" name="AnakForm[keterangan_gizi]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>6. Apakah ketika pulang masih ada perawatan lanjutan yang harus dilakukan dirumah ?</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_perawatan1" name="AnakForm[risiko_perawatan][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_perawatan0" name="AnakForm[risiko_perawatan][]" value="0"> </td>
                                        <td><input type="text" name="AnakForm[keterangan_perawatan]" class ="<?= $classForm ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>7. Lain-Lain ?</td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_lain1" name="AnakForm[risiko_lain][]" value="1"> </td>
                                        <td style="text-align:center;"><input type="radio" class="risiko_lain0" name="AnakForm[risiko_lain][]" value="0"> </td>
                                        <td><input type="text" name="AnakForm[keterangan_lain]" class ="<?= $classForm ?>"></td>
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
                                                    'format' => 'yyyy-mm-dd'
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
                                    <tr style="margin-bottom:5px;margin-top:30px;">
                                        <td class="<?=$classCenter?>">
                                            <?= $form->field($model, 'jenis_pemeriksaan[]')->label(false)->textInput([
                                                'class' => 'form-control jenis_pemeriksaan',
                                                'style' => $styleCells
                                            ]);
                                            ?>
                                        </td>
                                        <td class="<?=$classCenter?>">
                                            <?= $form->field($model, 'asal_pemeriksaan[]')->label(false)->textInput([
                                                'class' => 'form-control asal_pemeriksaan',
                                                'style' => $styleCells
                                            ]);
                                            ?>
                                        </td>
                                        <td class="<?=$classCenter?>">
                                            <?= $form->field($model, 'jumlah_pemeriksaan[]')->label(false)->textInput([
                                                'class' => 'form-control jumlah_pemeriksaan',
                                                'style' => $styleCells
                                            ]);
                                            ?>
                                        </td>
                                        <td style="text-align: center;">
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
var _jenis_pemeriksaan = ' . json_encode($model->jenis_pemeriksaan) . ';
var _asal_pemeriksaan = ' . json_encode($model->asal_pemeriksaan) . ';
var _jumlah_pemeriksaan = ' . json_encode($model->jumlah_pemeriksaan) . ';
var _risikoHc = ' . json_encode($model->risiko_hc) . ';
var _risikoImplan = ' . json_encode($model->risiko_implan) . ';
var _risikoAlat = ' . json_encode($model->risiko_alat) . ';
var _risikoTerapis = ' . json_encode($model->risiko_terapis) . ';
var _risikoGizi = ' . json_encode($model->risiko_gizi) . ';
var _risikoPerawatan = ' . json_encode($model->risiko_perawatan) . ';
var _risikoLain = ' . json_encode($model->risiko_lain) . ';

var _keteranganHc = "' . $model->keterangan_hc . '";
var _keteranganImplan = "' . $model->keterangan_implan . '";
var _keteranganAlat = "' . $model->keterangan_alat . '";
var _keteranganTerapi = "' . $model->keterangan_terapis . '";
var _keteranganGizi = "' . $model->keterangan_gizi . '";
var _keteranganPerawatan = "' . $model->keterangan_perawatan . '";
var _keteranganLain = "' . $model->keterangan_lain . '";

$(document).ready(function(){
    $(document).on("change", ".checked_risiko", function(){
        if($(this).val() == "1") {
            $(".risiko_hc1").prop("checked", true);
            $(".risiko_implan1").prop("checked", true);
            $(".risiko_alat1").prop("checked", true);
            $(".risiko_terapis1").prop("checked", true);
            $(".risiko_gizi1").prop("checked", true);
            $(".risiko_perawatan1").prop("checked", true);
            $(".risiko_lain1").prop("checked", true);
        }
        else {
            $(".risiko_hc0").prop("checked", true);
            $(".risiko_implan0").prop("checked", true);
            $(".risiko_alat0").prop("checked", true);
            $(".risiko_terapis0").prop("checked", true);
            $(".risiko_gizi0").prop("checked", true);
            $(".risiko_perawatan0").prop("checked", true);
            $(".risiko_lain0").prop("checked", true);
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
                        <input type="text" name="AnakForm[jenis_pemeriksaan][]" class="form-control" style="width:250px;margin-top:10px;margin-bottom:10px;margin-left:10px;" value="${jenis_pemeriksaan}">
                    </td>
                    <td>
                        <input type="text" name="AnakForm[asal_pemeriksaan][]" class="form-control" style="width:250px;margin-top:10px;margin-bottom:10px;margin-left:10px;" value="${asal_pemeriksaan}">
                    </td>
                    <td>
                        <input type="text" name="AnakForm[jumlah_pemeriksaan][]" class="form-control" style="width:250px;margin-top:10px;margin-bottom:10px;margin-left:10px;" value="${jumlah_pemeriksaan}">
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
        const newRowPemeriksaan = `
            <tr style="margin-bottom:5px;margin-top:30px;">
                <td>
                    <input type="text" name="AnakForm[jenis_pemeriksaan][]" class="form-control" style="width:250px;margin-top:10px;margin-bottom:10px;margin-left:10px;">
                </td>
                <td>
                    <input type="text" name="AnakForm[asal_pemeriksaan][]" class="form-control" style="width:250px;margin-top:10px;margin-bottom:10px;margin-left:10px;">
                </td>
                <td>
                    <input type="text" name="AnakForm[jumlah_pemeriksaan][]" class="form-control" style="width:250px;margin-top:10px;margin-bottom:10px;margin-left:10px;">
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn btn-danger deleteRowPemeriksaan ms-1">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $table.find("tbody").append(newRowPemeriksaan);
    });

    $("table tbody").on("click", ".deleteRowPemeriksaan", function () {
        const $table = $(this).closest("table");
        $(this).closest("tr").remove();
    });
    
    if(_risikoHc) {
        _risikoHc = (typeof _risikoHc[0] != "undefined") ? _risikoHc[0] : 0
    }
    if(_risikoImplan) {
        _risikoImplan = (typeof _risikoImplan[0] != "undefined") ? _risikoImplan[0] : 0
    }
    if(_risikoAlat) {
        _risikoAlat = (typeof _risikoAlat[0] != "undefined") ? _risikoAlat[0] : 0
    }
    if(_risikoTerapis) {
        _risikoTerapis = (typeof _risikoTerapis[0] != "undefined") ? _risikoTerapis[0] : 0
    }
    if(_risikoGizi) {
        _risikoGizi = (typeof _risikoGizi[0] != "undefined") ? _risikoGizi[0] : 0
    }
    if(_risikoPerawatan) {
        _risikoPerawatan = (typeof _risikoPerawatan[0] != "undefined") ? _risikoPerawatan[0] : 0
    }
    if(_risikoLain) {
        _risikoLain = (typeof _risikoLain[0] != "undefined") ? _risikoLain[0] : 0
    }

    $("input[name=\'AnakForm[risiko_hc][]\']").each(function(el){
        if(_risikoHc) {
            if($(this).val() == _risikoHc) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'AnakForm[risiko_implan][]\']").each(function(el){
        if(_risikoImplan) {
            if($(this).val() == _risikoImplan) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'AnakForm[risiko_alat][]\']").each(function(el){
        if(_risikoAlat) {
            if($(this).val() == _risikoAlat) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'AnakForm[risiko_terapis][]\']").each(function(el){
        if(_risikoTerapis) {
            if($(this).val() == _risikoTerapis) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'AnakForm[risiko_gizi][]\']").each(function(el){
        if(_risikoGizi) {
            if($(this).val() == _risikoGizi) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'AnakForm[risiko_perawatan][]\']").each(function(el){
        if(_risikoPerawatan) {
            if($(this).val() == _risikoPerawatan) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    $("input[name=\'AnakForm[risiko_lain][]\']").each(function(el){
        if(_risikoLain) {
            if($(this).val() == _risikoLain) {
                $(this).prop("checked", true)
            }
            else {
                $(this).prop("checked", false)
            }
        }
    })
    if(asesmenMedisId) {
        $("input[name=\'AnakForm[keterangan_hc]\']").val(_keteranganHc)
        $("input[name=\'AnakForm[keterangan_implan]\']").val(_keteranganImplan)
        $("input[name=\'AnakForm[keterangan_alat]\']").val(_keteranganAlat)
        $("input[name=\'AnakForm[keterangan_terapis]\']").val(_keteranganTerapi)
        $("input[name=\'AnakForm[keterangan_gizi]\']").val(_keteranganGizi)
        $("input[name=\'AnakForm[keterangan_perawatan]\']").val(_keteranganPerawatan)
        $("input[name=\'AnakForm[keterangan_lain]\']").val(_keteranganLain)
    }
})

', View::POS_END);
?>
