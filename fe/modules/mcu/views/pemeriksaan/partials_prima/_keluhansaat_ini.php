<?php

use yii\helpers\Html;
?>
<style>
    .bg-dark-custom {
        background-color: #37444a;
        color: white;
    }
</style>
<div class="row">
    <div class="col-md-12 ml-3 mt-3">
        <div class="panel panel-default">
            <a data-toggle="collapse" href="#keluhansaatini" role="button" aria-expanded="true" aria-controls="keluhansaatini">
                <div class="panel-heading flex-container">
                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Keluhan Saat Ini'); ?></b></h6>
                    <div>
                        <ul class="icons-list">
                            <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                        </ul>
                    </div>
                </div>
            </a>
            <div class="panel-body collapse multi-collapse collapse in" id="keluhansaatini">
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div>
                            <?= $form->field($model, "keluhan_saat_ini")->textInput()->label("Keluhan saat ini") ?>
                        </div>
                        <table class="table table-bordered table-hover dataTable no-footer">
                            <thead>
                                <tr>
                                    <th class="bg-dark-custom">Riwayat</th>
                                    <th class="bg-dark-custom">Status</th>
                                    <th class="bg-dark-custom">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Migrain/Vertigo</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_migrain")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_migrain_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Pingsan/Epilepsi (Kejang)</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_epilepsi")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_epilepsi_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gangguan Penglihatan</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_gangguan_pengelihatan")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_gangguan_pengelihatan_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gangguan Pendengaran</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_gangguan_pendengaran")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_gangguan_pendengaran_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Masalah Hidung, Sinus, Tenggorokan</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_masalah_hidung")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_masalah_hidung_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tuberculosis (TBC)</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_tbc")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_tbc_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Pneumonia</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_pneumonia")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_pneumonia_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Asma</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_asma")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_asma_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gangguan Saluran Cerna (maag, diare menahun, sembelit)</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_gangguang_saluran")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_gangguang_saluran_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hernia/Wasir</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_hernia")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_hernia_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Nyeri/Dada</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_nyeri_dada")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_nyeri_dada_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Penyakit Ginjal</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_penyakit_ginjal")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_penyakit_ginjal_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Batu Ginjal/Saluran Kemih</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_batu_ginjal")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_batu_ginjal_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Penyakit Kulit</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_penyakit_kulit")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_penyakit_kulit_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Riwayat Kecelakaan</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_riwayat_kecelakaan")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_riwayat_kecelakaan_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Riwayat Inap di RS</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_riwayat_inap_rs")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_riwayat_inap_rs_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Riwayat Operasi/Pembedahan</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_riwayat_operasi")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_riwayat_operasi_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alergi</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_alergi")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_alergi_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Demam Reumatik</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_demam_reumatik")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_demam_reumatik_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Demam Typhoid</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_demam_typhoid")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_demam_typhoid_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Demam Berdarah</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_demam_berdarah")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_demam_berdarah_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Malaria</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_malaria")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_malaria_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hepatitis (Penyakit Kuning)</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_hepatitis")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_hepatitis_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Diabetes Melitus (Penyakit Gula)</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_diabetes")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_diabetes_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Nyeri Persendian</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_nyeri_sendi")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_nyeri_sendi_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Nyeri Punggung/Pinggang</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_nyeri_punggung")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_nyeri_punggung_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Varises</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_varises")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_varises_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Kanker/Tumor</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_kanker")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_kanker_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Gangguan Psikiatrik (Depresi, Halusinasi, Dll)</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_psikiatrik")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_psikiatrik_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Penyakit Kelamin</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_penyakit_kelamin")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_penyakit_kelamin_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Lain - lain</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_lainnya")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_lainnya_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Masalah kebidanan dan kandungan (khusus wanita*)</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_masalah_kebidanan")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_hpht")->textInput(["disabled" => false])->label("HPHT") ?>
                                        <?= $form->field($model, "keluhan_menarche")->textInput(["disabled" => false])->label("Menarche") ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Keterangan Obat yang diminum saat ini/Obat Rutin/Obat Jangka Panjang</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_keteranganobat")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_keteranganobat_text")->textInput(["disabled" => false])->label(false) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Perubahan berat badan akhir-akhir ini ? </td>
                                    <td>
                                        <?= $form->field($model, "keluhan_perubahanbb")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <div>
                                            <?= $form->field($model, "keluhan_perubahanbb_type")->dropDownList([
                                                0 => "Stabil",
                                                1 => "Naik",
                                                2 => "Turun",
                                            ])->label(false) ?>
                                            <div class="input-group">
                                                <?= Html::activeTextInput($model, 'keluhan_perubahanbb_kg', ['class' => 'form-control']) ?>
                                                <span class="input-group-addon" id="basic-addon2">
                                                    <label style="width: 50px !important;"><?= Yii::t('fe', 'Kg') ?></label>
                                                </span>
                                            </div>
                                        </div>
                                        <?= $form->field($model, "keluhan_perubahanbb_napsumakan")->textInput(["disabled" => false])->label("Napsu Makan") ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="background-color: #e3fff9; ">
                                        <b>RIWAYAT KEBIASAAN</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Merokok</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_merokok")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items: center;">
                                            <label for="" class="mt-2" style="width: 55px !important;">Jenis</label>
                                            <?= Html::activeTextInput($model, 'keluhan_merokok_jenis', ['class' => 'form-control ml-3']) ?>
                                        </div>
                                        <div style="display: flex;" class="mt-3">
                                            <div class="form-group mt-2" style="display: inline-flex; align-items: center;">
                                                <label for="" class="mt-2" style="width: 50px !important;">Jumlah</label>
                                                <div class="input-group">
                                                    <?= Html::activeTextInput($model, 'keluhan_merokok_jumlah',  ['class' => 'form-control ml-3', 'style' => 'width: 100% !important']) ?>
                                                    <span class="input-group-addon" id="basic-addon2">
                                                        <label style="width: 70px !important;"><?= Yii::t('fe', 'Batang/hari') ?></label>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group ml-3" style="display: inline-flex; align-items: center;">
                                                <label for="" class="mt-2" style="width: 50px !important;">Sejak</label>
                                                <?= Html::activeTextInput($model, 'keluhan_merokok_sejak', ['class' => 'form-control ml-3', 'style' => 'width: 100% !important']) ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alkohol</td>
                                    <td>
                                        <?= $form->field($model, "keluhan_alkohol")->radioList([0 => "Ya", 1 => "Tidak"], ['inline' => true])->label(false) ?>
                                    </td>
                                    <td>
                                        <div style="display:flex; align-items: center;">
                                            <label for="" class="mt-2" style="width: 55px !important;">Jenis</label>
                                            <?= Html::activeTextInput($model, 'keluhan_alkohol_jenis', ['class' => 'form-control ml-3']) ?>

                                        </div>
                                        <div style="display: flex;" class="mt-3">
                                            <div class="form-group mt-2" style="display: inline-flex; align-items: center;">
                                                <label for="" class="mt-2" style="width: 50px !important;">Jumlah</label>
                                                <div class="input-group">
                                                    <?= Html::activeTextInput($model, 'keluhan_alkohol_jumlah',  ['class' => 'form-control ml-3', 'style' => 'width: 100% !important']) ?>
                                                    <span class="input-group-addon" id="basic-addon2">
                                                        <label style="width: 70px !important;"><?= Yii::t('fe', 'Gelas/hari') ?></label>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group ml-3" style="display: inline-flex; align-items: center;">
                                                <label for="" class="mt-2" style="width: 50px !important;">Sejak</label>
                                                <?= Html::activeTextInput($model, 'keluhan_alkohol_sejak', ['class' => 'form-control ml-3']) ?>
                                            </div>
                                        </div>
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
<script>
    $(document).ready(function() {
        $("#riwayatpenyakitformprima-keluhan_perubahanbb_type").select2()
    });
</script>