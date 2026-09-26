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
            <h5 class="panel-title">H. Penilaian Status Fungsional <span style="font-style:italic;">(Skala Barthel Indeks)</span> </h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <table style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="font-weight:bold;">Parameter/Fungsi</th>
                                        <th class="text-center" style="font-weight:bold;">3</th>
                                        <th class="text-center" style="font-weight:bold;">2</th>
                                        <th class="text-center" style="font-weight:bold;">1</th>
                                        <th class="text-center" style="font-weight:bold;">0</th>
                                        <th class="text-center" style="font-weight:bold;">Skor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Mengendalikan Rangsang Defekasi</td>
                                        <td>-</td>
                                        <td>Mandiri</td>
                                        <td>Kadang-Kadang</td>
                                        <td>Tak Terkendali/Perlu Bantuan</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_defekasi')
                                            ->label(false)
                                            ->radioList([
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                                0 => 0,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Mengendalikan Rangsang Berkemih</td>
                                        <td>-</td>
                                        <td>Mandiri</td>
                                        <td>Kadang-Kadang (1x24 Jam)</td>
                                        <td>Tak Terkendali/Pakai Kateter</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_berkemih')
                                            ->label(false)
                                            ->radioList([
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                                0 => 0,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Membersihkan Diri</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>Mandiri</td>
                                        <td>Butuh Pertolongan Orang Lain</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_bersih')
                                            ->label(false)
                                            ->radioList([
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                                0 => 0,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Penggunaan Jamban (Masuk/Keluar)</td>
                                        <td>-</td>
                                        <td>Mandiri</td>
                                        <td>Sebagian Perlu Pertolongan</td>
                                        <td>Tergantung Orang Lain</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_jamban')
                                            ->label(false)
                                            ->radioList([
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                                0 => 0,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Makan</td>
                                        <td>-</td>
                                        <td>Mandiri</td>
                                        <td>Perlu Pertolongan</td>
                                        <td>Tidak Mampu</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_makan')
                                            ->label(false)
                                            ->radioList([
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                                0 => 0,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Berubah Sikap (Berbaring ke Duduk)</td>
                                        <td>Mandiri</td>
                                        <td>Bantuan Minimal 2 Orang</td>
                                        <td>Perlu Bantuan Duduk (2 Orang)</td>
                                        <td>Tidak Mampu</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_duduk')
                                            ->label(false)
                                            ->radioList([
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                                0 => 0,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Berpindah/Berjalan</td>
                                        <td>Mandiri</td>
                                        <td>Berjalan Bantuan 1 Orang</td>
                                        <td>Bantuan Minimal 2 Orang</td>
                                        <td>Tidak Mampu</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_berjalan')
                                            ->label(false)
                                            ->radioList([
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                                0 => 0,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Memakai Baju</td>
                                        <td>-</td>
                                        <td>Mandiri</td>
                                        <td>Sebagian Dibantu</td>
                                        <td>Tergantung Orang Lain</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_baju')
                                            ->label(false)
                                            ->radioList([
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                                0 => 0,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Naik Turun Tangga</td>
                                        <td>-</td>
                                        <td>Mandiri</td>
                                        <td>Butuh Pertolongan</td>
                                        <td>Tidak Mampu</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_tangga')
                                            ->label(false)
                                            ->radioList([
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                                0 => 0,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Mandi</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>Mandiri</td>
                                        <td>Tergantung Orang Lain</td>
                                        <td style="text-align:center;width:25%;"><?= $form->field($model, 'skor_mandi')
                                            ->label(false)
                                            ->radioList([
                                                3 => 3,
                                                2 => 2,
                                                1 => 1,
                                                0 => 0,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-center" style="font-weight:bold;">Skor Barthel Indeks</td>
                                        <td style="text-align:center;font-weight:bold;"> Jumlah Skor </td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;">
                                            <span class="total_skor_fungsional"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" style="font-weight:bold;font-style:italic;">
                                            <p>&nbsp;</p>
                                            <p style="margin-left:10px;">Keterangan :</p>
                                            <?= $form->field($model, 'kategori_status_fungsional')
                                            ->label(false)
                                            ->radioList(
                                                [
                                                    '1' => '20 : Mandiri',
                                                    '2' => '12 - 19 : Ketergantungan Ringan',
                                                    '3' => '9 - 11 : Ketergantungan Sedang',
                                                    '4' => '5 - 8 : Ketergantungan Berat',
                                                    '5' => '0 - 4 : Ketergantungan Total',
                                                ],
                                                [
                                                    'itemOptions' => [
                                                        'class' => 'kategori_status_fungsional'
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
var skorDefekasi = "'.$model->skor_defekasi.'"
var skorBerkemih = "'.$model->skor_berkemih.'"
var skorBersih = "'.$model->skor_bersih.'"
var skorJamban = "'.$model->skor_jamban.'"
var skorMakan = "'.$model->skor_makan.'"
var skorDuduk = "'.$model->skor_duduk.'"
var skorBerjalan = "'.$model->skor_berjalan.'"
var skorBaju = "'.$model->skor_baju.'"
var skorTangga = "'.$model->skor_tangga.'"
var skorMandi = "'.$model->skor_mandi.'"

$(document).ready(function(){
    var defekasi = berkemih = bersih = jamban = makan = duduk = berjalan = baju = tangga = mandi = 0
    var totalSkorFungsional = 0
    $(".total_skor_fungsional").html(totalSkorFungsional)
    if(asesmenMedisId) {
        defekasi = skorDefekasi ? parseInt(skorDefekasi) : 0
        berkemih = skorBerkemih ? parseInt(skorBerkemih) : 0
        bersih = skorBersih ? parseInt(skorBersih) : 0
        jamban = skorJamban ? parseInt(skorJamban) : 0
        makan = skorMakan ? parseInt(skorMakan) : 0
        duduk = skorDuduk ? parseInt(skorDuduk) : 0
        berjalan = skorBerjalan ? parseInt(skorBerjalan) : 0
        baju = skorBaju ? parseInt(skorBaju) : 0
        tangga = skorTangga ? parseInt(skorTangga) : 0
        mandi = skorMandi ? parseInt(skorMandi) : 0
        updateSkorFungsional()
    }

    function updateSkorFungsional() {
        totalSkorFungsional = defekasi + berkemih + bersih +
                              jamban + makan + duduk +
                              berjalan + baju + tangga +
                              mandi

        $(".total_skor_fungsional").html(totalSkorFungsional)
    }

    $(document).on("change", "input[name=\'GinekologiForm[skor_defekasi]\']", function(){
        if($(this).is(":checked")) {
            defekasi = parseInt($(this).val())
            updateSkorFungsional()
        }
    })
    $(document).on("change", "input[name=\'GinekologiForm[skor_berkemih]\']", function(){
        if($(this).is(":checked")) {
            berkemih = parseInt($(this).val())
            updateSkorFungsional()
        }
    })
    $(document).on("change", "input[name=\'GinekologiForm[skor_bersih]\']", function(){
        if($(this).is(":checked")) {
            bersih = parseInt($(this).val())
            updateSkorFungsional()
        }
    })
    $(document).on("change", "input[name=\'GinekologiForm[skor_jamban]\']", function(){
        if($(this).is(":checked")) {
            jamban = parseInt($(this).val())
            updateSkorFungsional()
        }
    })
    $(document).on("change", "input[name=\'GinekologiForm[skor_makan]\']", function(){
        if($(this).is(":checked")) {
            makan = parseInt($(this).val())
            updateSkorFungsional()
        }
    })
    $(document).on("change", "input[name=\'GinekologiForm[skor_duduk]\']", function(){
        if($(this).is(":checked")) {
            duduk = parseInt($(this).val())
            updateSkorFungsional()
        }
    })
    $(document).on("change", "input[name=\'GinekologiForm[skor_berjalan]\']", function(){
        if($(this).is(":checked")) {
            berjalan = parseInt($(this).val())
            updateSkorFungsional()
        }
    })
    $(document).on("change", "input[name=\'GinekologiForm[skor_baju]\']", function(){
        if($(this).is(":checked")) {
            baju = parseInt($(this).val())
            updateSkorFungsional()
        }
    })
    $(document).on("change", "input[name=\'GinekologiForm[skor_tangga]\']", function(){
        if($(this).is(":checked")) {
            tangga = parseInt($(this).val())
            updateSkorFungsional()
        }
    })
    $(document).on("change", "input[name=\'GinekologiForm[skor_mandi]\']", function(){
        if($(this).is(":checked")) {
            mandi = parseInt($(this).val())
            updateSkorFungsional()
        }
    })
})

', View::POS_END);
?>
