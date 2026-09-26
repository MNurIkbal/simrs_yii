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
            <h5 class="panel-title">K. Penilaian Tingkat Nyeri</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">Skrining Nyeri Pada Pasien Bayi atau Anak Usia 2 Bulan Sampai 7 Tahun</p>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kategori_umur')
                            ->label(Yii::t('fe', 'Skrining Nyeri'))
                            ->radioList(
                                [
                                    '1' => '2 bulan > 7 tahun',
                                    '2' => '> 7 tahun',
                                ],
                                [
                                    'inline' => true,
                                    'itemOptions' => [
                                        'class' => 'kategori_umur'
                                    ]
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="usia1">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">FLACC SCALE / (Face, Legs, Activity, Cry, Consolability) / (Wajah, Kaki, Aktifitas, Menangis, Konsolability)</p>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table style="width:100%">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center" style="font-weight:bold;" id="header_ptn1">Kategori</th>
                                        <th colspan="3" class="text-center" style="font-weight:bold;" id="header_ptn2">Skor</th>
                                        <th rowspan="2" class="text-center" style="font-weight:bold;" id="header_ptn3">Skor</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="font-weight:bold;" id="header_ptn4">0</th>
                                        <th class="text-center" style="font-weight:bold;" id="header_ptn5">1</th>
                                        <th class="text-center" style="font-weight:bold;" id="header_ptn6">2</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Wajah (Face)</td>
                                        <td>Senyum tidak ada ekspresi tertentu</td>
                                        <td>Sesekali menangis cuek</td>
                                        <td>Sering cemberut/rahang terkatup</td>
                                        <td style="width:18%;text-align:center;">
                                            <?= $form->field($model, 'skor_wajah')
                                            ->label(false)
                                            ->radioList(
                                            [
                                                0 => 0,
                                                1 => 1,
                                                2 => 2,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Kaki (Legs)</td>
                                        <td>Posisi normal/rileks</td>
                                        <td>Tidak tenang, gelisah, tegang</td>
                                        <td>Menendang, mengangkat kaki</td>
                                        <td style="width:18%;text-align:center;">
                                            <?= $form->field($model, 'skor_kaki')
                                            ->label(false)
                                            ->radioList(
                                            [
                                                0 => 0,
                                                1 => 1,
                                                2 => 2,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Kegiatan (Activity)</td>
                                        <td>Berbaring dengan tenang, posisi normal, mulai bergerak</td>
                                        <td>Menggeliat, tegang</td>
                                        <td>Melengkung, kaku atau menghentak</td>
                                        <td style="width:18%;text-align:center;">
                                            <?= $form->field($model, 'skor_kegiatan')
                                            ->label(false)
                                            ->radioList(
                                            [
                                                0 => 0,
                                                1 => 1,
                                                2 => 2,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Menangis (Cry)</td>
                                        <td>Tidak menangis (saat bangun atau tidur)</td>
                                        <td>Mengerang atau merengek, sesekali mengeluh</td>
                                        <td>Menangis teriak/terisak-isak, sering mengeluh</td>
                                        <td style="width:18%;text-align:center;">
                                            <?= $form->field($model, 'skor_menangis')
                                            ->label(false)
                                            ->radioList(
                                            [
                                                0 => 0,
                                                1 => 1,
                                                2 => 2,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Kemampuan untuk dihibur (Consolability)</td>
                                        <td>Tenang, rileks</td>
                                        <td>Dapat dihibur dengan sentuhan/pelukan/diajak bicara</td>
                                        <td>Sering mengeluh, sulit untuk dihibur</td>
                                        <td style="width:18%;text-align:center;">
                                            <?= $form->field($model, 'skor_konsol')
                                            ->label(false)
                                            ->radioList(
                                            [
                                                0 => 0,
                                                1 => 1,
                                                2 => 2,
                                            ], ['inline' => true]); ?>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-center" style="font-weight:bold;">Total Skor</td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;"> <span class="total_skor"></span> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="font-weight:bold;">
                                            <p style="text-align:center;">Masing-masing dari lima kategori (FLACC) diberi nilai 0-2 ditambahkan untuk mendapatkan total dari 0-10 (F+L+A+C+C) = </p>
                                        </td>
                                        <td style="width:10%;text-align:center;font-size:20px;font-weight:bold;"> <span class="total_skor2"></span> </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-center" style="font-weight:bold;">
                                            <?= $form->field($model, 'kategori_tingkat_nyeri')
                                            ->label(false)
                                            ->radioList(
                                                [
                                                    '1' => 'Skor 0 : Tidak Nyeri',
                                                    '2' => 'Skor 1-3 : Nyeri Ringan',
                                                    '3' => 'Skor 4-6 : Nyeri Sedang',
                                                    '4' => 'Skor 8-10 : Nyeri Berat',
                                                ],
                                                [
                                                    'inline' => true,
                                                    'itemOptions' => [
                                                        'class' => 'kategori_tingkat_nyeri'
                                                    ]
                                                ]
                                            ); ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <br>
                    </div>
                    <div class="usia2">
                    <div class="row">
                        <p style="margin-left:20px;margin-bottom:20px;"><span style="font-weight:bold;"> Skala Nyeri</span> <span style="font-style:italic;">(Wong Baker Faces Pain Scale)</span> </p>
                        <p style="margin-left:20px;margin-bottom:20px;">(Skrining nyeri untuk anak usia > 7 tahun)</p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'rasa_nyeri')
                            ->label(Yii::t('fe', '1. Adakah Rasa Nyeri'))
                            ->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lokasi_nyeri')
                            ->label(Yii::t('fe', 'Lokasi'))
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'frekuensi_nyeri')
                            ->label(Yii::t('fe', 'Frekuensi'))
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'durasi_nyeri')
                            ->label(Yii::t('fe', 'Durasi'))
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row" style="text-align:center;">
                        <div class="image-frame">
                            <?php
                                echo Html::img( '@web/media/img/img-pemeriksaan/bagian_tubuh_medis.jpg', [
                                    'width'=> 500,
                                    'height'=> 520,
                                ]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tipe_nyeri')
                            ->label(Yii::t('fe', '2. Tipe Nyeri'))
                            ->radioList(
                            [
                                '1' => 'Terus Menerus',
                                '2' => 'Hilang Timbul',
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'karakteristik_nyeri')
                            ->label(Yii::t('fe', '3. Karakteristik Nyeri'))
                            ->checkboxList(
                            [
                                '1' => 'Terbakar',
                                '2' => 'Tertekan',
                                '3' => 'Kram',
                                '4' => 'Tertusuk',
                                '5' => 'Berat',
                                '6' => 'Tumpul',
                                '7' => 'Tajam',
                                '8' => 'Lain-Lain',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'karakteristik_nyeri_lainnya')
                            ->label(false)
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pengaruh_nyeri')
                            ->label(Yii::t('fe', '4. Nyeri Mempengaruhi'))
                            ->checkboxList(
                            [
                                '1' => 'Beraktifitas',
                                '2' => 'Olahraga Fisik',
                                '3' => 'Membungkukkan Badan',
                                '4' => 'Duduk',
                                '5' => 'Jalan',
                                '6' => 'Berdiri',
                                '7' => 'Stress',
                                '8' => 'Batuk',
                                '9' => 'Diet',
                                '10' => 'Membusungkan Dada',
                                '11' => 'Lain-Lain',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pengaruh_nyeri_lainnya')
                            ->label(false)
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pereda_nyeri')
                            ->label(Yii::t('fe', 'Yang Dapat Meredakan Nyeri'))
                            ->checkboxList(
                            [
                                '1' => 'Tirah Baring',
                                '2' => 'Miring Kanan/Kiri',
                                '3' => 'Istirahat',
                                '4' => 'Obat-Obatan',
                                '5' => 'Relaksasi',
                                '6' => 'Lain-Lain',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pereda_nyeri_lainnya')
                            ->label(false)
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'nyeri_dirasakan')
                            ->label(Yii::t('fe', 'Nyeri Yang Dirasakan Seperti'))
                            ->checkboxList(
                            [
                                '1' => 'Tertusuk',
                                '2' => 'Terbakar',
                                '3' => 'Kesemutan',
                                '4' => 'Tajam',
                                '5' => 'Tertekan/Tertimpa Beban Berat',
                                '6' => 'Mati Rasa/Kebas/Kaku',
                                '7' => 'Diris-Iris',
                                '8' => 'Lain-Lain',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'nyeri_dirasakan_lainnya')
                            ->label(false)
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row usia2">
                        <div class="col-md-12 text-center">
                            <p style="font-weight:bold;">Skala Nyeri</p>
                            <p style="font-weight:bold;font-style:italic;">(Wong Baker Faces Pain Scale/Numeric Rating Pain)</p>
                            <p style="font-weight:bold;font-size:16px;">PAIN MEASUREMENT SCALE</p>
                            <div class="box-scale">
                                <div class="box-scale-header">
                                    <img src="/media/img/all-emote.svg" alt="">
                                </div>
                                <div class="box-scale-line box-scale-line__separator">
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__hidePercentage"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kategori_nyeri')
                            ->radioList(
                            [
                                '1' => 'Nyeri Ringan',
                                '2' => 'Nyeri Sedang',
                                '3' => 'Nyeri Berat',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'skor_nyeri')
                            ->label(false)
                            ->radioList(
                            [
                                0 => 0,
                                1 => 1,
                                2 => 2,
                                3 => 3,
                                4 => 4,
                                5 => 5,
                                6 => 6,
                                7 => 7,
                                8 => 8,
                                9 => 9,
                                10 => 10,
                            ], ['inline' => true, 'itemOptions' => ['class' => 'skor_nyeri']]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'total_skor_nyeri')
                            ->label(Yii::t('fe', 'Skor/Skala Nyeri'))
                            ->textInput(['class' => $classFormNumber, 'readonly' => true]); ?>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var skorWajah = "'.$model->skor_wajah.'"
var skorKaki = "'.$model->skor_kaki.'"
var skorKegiatan = "'.$model->skor_kegiatan.'"
var skorMenangis = "'.$model->skor_menangis.'"
var skorKonsol = "'.$model->skor_konsol.'"
var skorNyeriRingan = "'.$model->nyeri_ringan.'"
var skorNyeriSedang = "'.$model->nyeri_sedang.'"
var skorNyeriBerat = "'.$model->nyeri_berat.'"
var karakteristik_lainnya = "'.$model->karakteristik_nyeri_lainnya.'"
var pengaruh_nyeri_lainnya = "'.$model->pengaruh_nyeri_lainnya.'"
var pereda_nyeri_lainnya = "'.$model->pereda_nyeri_lainnya.'"
var nyeri_dirasakan_lainnya = "'.$model->nyeri_dirasakan_lainnya.'"
var kategoriUmur = "'.$model->kategori_umur.'"
var $kategoriNyeri = "'.$model->kategori_nyeri.'"
var total_skor_nyeri = "'.$model->total_skor_nyeri.'"

if(!asesmenMedisId) {
    $(".total_skor").html(0)
    $(".total_skor2").html(0)
    $("input[name=\'AnakForm[total_skor_nyeri]\']").val(0)
}

$(document).ready(function(){
    $(".skor_nyeri").prop("disabled", true);
    var labelWajahFlacc = labelKakiFlacc = labelKegiatanFlacc = labelMenangisFlacc = labelKonsolFlacc = 0
    
    $(".usia1").css("display", "none")
    $(".usia2").css("display", "none")
    
    const kategoriNyeri = $("input[name=\'AnakForm[kategori_nyeri]\']");
    const checkboxKarakteristik = $("input[name=\'AnakForm[karakteristik_nyeri][]\'][value=\'8\']");
    const karakteristikLainnya = $("#anakform-karakteristik_nyeri_lainnya");
    const checkboxPengaruhNyeri = $("input[name=\'AnakForm[pengaruh_nyeri][]\'][value=\'11\']");
    const pengaruhNyeriLainnya = $("#anakform-pengaruh_nyeri_lainnya");
    const checkboxPeredaNyeri = $("input[name=\'AnakForm[pereda_nyeri][]\'][value=\'6\']");
    const peredaNyeriLainnya = $("#anakform-pereda_nyeri_lainnya");
    const checkboxNyeriDirasakan = $("input[name=\'AnakForm[nyeri_dirasakan][]\'][value=\'8\']");
    const nyeriDirasakaniLainnya = $("#anakform-nyeri_dirasakan_lainnya");
    
    karakteristikLainnya.prop("readonly", true);
    pengaruhNyeriLainnya.prop("readonly", true);
    peredaNyeriLainnya.prop("readonly", true);
    nyeriDirasakaniLainnya.prop("readonly", true);

    if(asesmenMedisId) {
        if(karakteristik_lainnya) {
            karakteristikLainnya.prop("readonly", false);
        }
        if(pengaruh_nyeri_lainnya) {
            pengaruhNyeriLainnya.prop("readonly", false);
        }
        if(pereda_nyeri_lainnya) {
            peredaNyeriLainnya.prop("readonly", false);
        }
        if(nyeri_dirasakan_lainnya) {
            nyeriDirasakaniLainnya.prop("readonly", false);
        }
        if(kategoriUmur) {
            if(kategoriUmur == "1") {
                $(".usia1").css("display", "block")
                $(".usia2").css("display", "none")
            }
            else {
                $(".usia1").css("display", "none")
                $(".usia2").css("display", "block")
            }
        }
        
        if($kategoriNyeri) {
            if($kategoriNyeri == "1") {
                updateNyeriRingan()
            }
            else if($kategoriNyeri == "2") {
                updateNyeriSedang()
            }
            else {
                updateNyeriBerat()
            }
        }
        
        labelWajahFlacc = skorWajah ? parseInt(skorWajah) : 0
        labelKakiFlacc = skorKaki ? parseInt(skorKaki) : 0
        labelKegiatanFlacc = skorKegiatan ? parseInt(skorKegiatan) : 0
        labelMenangisFlacc = skorMenangis ? parseInt(skorMenangis) : 0
        labelKonsolFlacc = skorKonsol ? parseInt(skorKonsol) : 0

        updateSkorFlacc()
        $("input[name=\'AnakForm[total_skor_nyeri]\']").val(total_skor_nyeri)
    }
    
    checkboxKarakteristik.change(function () {
        if(checkboxKarakteristik.is(":checked")) {
            karakteristikLainnya.prop("readonly", false);
        }
        else {
            karakteristikLainnya.val("").prop("readonly", true);
        }
    });
    checkboxPengaruhNyeri.change(function () {
        if(checkboxPengaruhNyeri.is(":checked")) {
            pengaruhNyeriLainnya.prop("readonly", false);
        }
        else {
            pengaruhNyeriLainnya.val("").prop("readonly", true);
        }
    });
    checkboxPeredaNyeri.change(function () {
        if(checkboxPeredaNyeri.is(":checked")) {
            peredaNyeriLainnya.prop("readonly", false);
        }
        else {
            peredaNyeriLainnya.val("").prop("readonly", true);
        }
    });
    checkboxNyeriDirasakan.change(function () {
        if(checkboxNyeriDirasakan.is(":checked")) {
            nyeriDirasakaniLainnya.prop("readonly", false);
        }
        else {
            nyeriDirasakaniLainnya.val("").prop("readonly", true);
        }
    });

    function updateSkorFlacc() {
        var totalSkorFlacc = labelWajahFlacc + labelKakiFlacc + labelKegiatanFlacc + labelMenangisFlacc + labelKonsolFlacc
        $(".total_skor").html(totalSkorFlacc)
        $(".total_skor2").html(totalSkorFlacc)
    }

    $(document).on("change", "input[name=\'AnakForm[skor_wajah]\']", function(){
        if($(this).is(":checked")) {
            labelWajahFlacc = parseInt($(this).val())
            updateSkorFlacc()
        }
    })
    $(document).on("change", "input[name=\'AnakForm[skor_kaki]\']", function(){
        if($(this).is(":checked")) {
            labelKakiFlacc = parseInt($(this).val())
            updateSkorFlacc()
        }
    })
    $(document).on("change", "input[name=\'AnakForm[skor_kegiatan]\']", function(){
        if($(this).is(":checked")) {
            labelKegiatanFlacc = parseInt($(this).val())
            updateSkorFlacc()
        }
    })
    $(document).on("change", "input[name=\'AnakForm[skor_menangis]\']", function(){
        if($(this).is(":checked")) {
            labelMenangisFlacc = parseInt($(this).val())
            updateSkorFlacc()
        }
    })
    $(document).on("change", "input[name=\'AnakForm[skor_konsol]\']", function(){
        if($(this).is(":checked")) {
            labelKonsolFlacc = parseInt($(this).val())
            updateSkorFlacc()
        }
    })
    $(document).on("change", "input[name=\'AnakForm[nyeri_ringan]\']", function(){
        if($(this).is(":checked")) {
            nyeriRingan = parseInt($(this).val())
            updateSkorNyeri()
        }
    })
    $(document).on("change", "input[name=\'AnakForm[nyeri_sedang]\']", function(){
        if($(this).is(":checked")) {
            nyeriSedang = parseInt($(this).val())
            updateSkorNyeri()
        }
    })
    $(document).on("change", "input[name=\'AnakForm[nyeri_berat]\']", function(){
        if($(this).is(":checked")) {
            nyeriBerat = parseInt($(this).val())
            updateSkorNyeri()
        }
    })
    $(document).on("change", ".kategori_umur", function(){
        if($(this).is(":checked")) {
            if($(this).val() == "1") {
                $(".usia1").css("display", "block")
                $(".usia2").css("display", "none")
            }
            else {
                $(".usia1").css("display", "none")
                $(".usia2").css("display", "block")
            }
        }
    })

    kategoriNyeri.on("change", function(){
        var _val = $(this).val()
        $(".skor_nyeri").prop("checked", false)
        if(_val == "1") {
            updateNyeriRingan()
        }
        else if(_val == "2") {
            updateNyeriSedang()
        }
        else {
            updateNyeriBerat()
        }
    })
    $(".skor_nyeri").on("change", function(){
        $("input[name=\'AnakForm[total_skor_nyeri]\']").val($(this).val())
    })

    function updateNyeriRingan() {
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'0\']").prop("disabled", false);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'1\']").prop("disabled", false);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'2\']").prop("disabled", false);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'3\']").prop("disabled", false);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'4\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'5\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'6\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'7\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'8\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'9\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'10\']").prop("disabled", true);
    }
    function updateNyeriSedang() {
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'0\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'1\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'2\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'3\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'4\']").prop("disabled", false);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'5\']").prop("disabled", false);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'6\']").prop("disabled", false);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'7\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'8\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'9\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'10\']").prop("disabled", true);
    }
    function updateNyeriBerat() {
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'0\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'1\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'2\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'3\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'4\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'5\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'6\']").prop("disabled", true);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'7\']").prop("disabled", false);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'8\']").prop("disabled", false);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'9\']").prop("disabled", false);
        $("input[name=\'AnakForm[skor_nyeri]\'][value=\'10\']").prop("disabled", false);
    }
})

', View::POS_END);
?>
