<?php

use yii\helpers\Html;
?>
<style>
    .mt-10 {
        margin-top: 10px;
    }
</style>
<div class="row">
    <div class="col-md-12 ml-3 mt-3">
        <div class="panel panel-default">
            <a data-toggle="collapse" href="#riwayatkerja" role="button" aria-expanded="true" aria-controls="riwayatkerja">
                <div class="panel-heading flex-container">
                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Riwayat Kerja (Tenaga Kerja)'); ?></b></h6>
                    <div>
                        <ul class="icons-list">
                            <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                        </ul>
                    </div>
                </div>
            </a>
            <div class="panel-body collapse multi-collapse collapse in" id="riwayatkerja">
                <div class="row mt-3 ml-1">
                    <div class="row">
                        <div class="col-md-11">

                            <!-- Start Foreach -->
                            <?php if (!empty($riwayatRekap)) : ?>
                                <?php foreach ($riwayatRekap as $key => $value) : ?>
                                    <div class="row riwayat-kerjaan mt-3 index-card-<?= $key ?>" rowindex="<?= $key ?>">
                                        <div class="col-md-3 select-mulai">
                                            <span>Tahun Mulai</span>
                                            <?=
                                            Html::dropDownList('RiwayatPenyakitFormPrima[tahun_mulai][]', $value['tahun_mulai'], $currentSelectYear, [
                                                'class' => 'select2custom tanggal-mulai'
                                            ])
                                            ?>
                                        </div>
                                        <div class="col-md-3 select-selesai">
                                            <span>Tahun Selesai</span>
                                            <?= Html::dropDownList('RiwayatPenyakitFormPrima[tahun_selesai][]', $value['tahun_selesai'], $currentSelectEndYear, [
                                                'class' => 'select2custom tanggal-selesai'
                                            ]) ?>
                                        </div>
                                        <div class="col-md-3">
                                            <span>Perusahaan</span>
                                            <input type="text" class="form-control" value="<?= $value['perusahaan'] ?>" name="RiwayatPenyakitFormPrima[perusahaan][]">
                                        </div>
                                        <div class="col-md-3">
                                            <span>Jabatan</span>
                                            <input type="text" class="form-control" value="<?= $value['jabatan'] ?>" name="RiwayatPenyakitFormPrima[jabatan][]">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <!-- Kalo kondisi kosong / Pertama kal masuk kesini -->
                                <div class="row riwayat-kerjaan mt-3 index-card-0" rowindex="0">
                                    <div class="col-md-3 select-mulai">
                                        <span>Tahun Mulai</span>
                                        <?=
                                        Html::dropDownList('RiwayatPenyakitFormPrima[tahun_mulai][]', null, $currentSelectYear, [
                                            'class' => 'select2custom tanggal-mulai'
                                        ])
                                        ?>
                                    </div>
                                    <div class="col-md-3 select-selesai">
                                        <span>Tahun Selesai</span>
                                        <?= Html::dropDownList('RiwayatPenyakitFormPrima[tahun_selesai][]', null, $currentSelectEndYear, [
                                            'class' => 'select2custom tanggal-selesai'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <span>Perusahaan</span>
                                        <input type="text" class="form-control" name="RiwayatPenyakitFormPrima[perusahaan][]">
                                    </div>
                                    <div class="col-md-3">
                                        <span>Jabatan</span>
                                        <input type="text" class="form-control" name="RiwayatPenyakitFormPrima[jabatan][]">
                                    </div>
                                </div>
                            <?php endif; ?>
                            <!-- Endforeach -->

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <?= $form->field($model, 'uraian_singkat') ?>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Paparan di Tempat Kerja</label>
                                        <div style="display: flex;">
                                            <span style="width: 80px;">
                                                <?= $form->field($model, 'paparan_tidak_ada')->checkbox()->label("Tidak Ada") ?>
                                            </span>
                                            <span class="ml-3" style="width: 80px;">
                                                <?= $form->field($model, 'paparan_bising')->checkbox()->label("Bising") ?>
                                            </span>
                                            <span class="ml-3" style="width: 80px;">
                                                <?= $form->field($model, 'paparan_kimia')->checkbox()->label("Kimia") ?>
                                            </span>
                                            <span class="ml-3" style="width: 80px;">
                                                <?= $form->field($model, 'paparan_radiasi')->checkbox()->label("Radiasi ") ?>
                                            </span>
                                        </div>
                                        <div style="display: flex;">
                                            <span style="width: 80px;">
                                                <?= $form->field($model, 'paparan_stress')->checkbox()->label("Stress") ?>
                                            </span>
                                            <span class="ml-3" style="width: 80px;">
                                                <?= $form->field($model, 'paparan_ergonomis')->checkbox()->label("Ergonomis") ?>
                                            </span>
                                            <span class="ml-3" style="width: 80px;">
                                                <?= $form->field($model, 'paparan_lainnya')->checkbox()->label("Lainnya") ?>
                                            </span>
                                        </div>
                                        <div class="mt-2">
                                            <?= $form->field($model, 'paparan_secara_singkat') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Resiko Lingkungan Kerja</label>
                                        <div style="display: flex;">
                                            <span style="width: 120px;">
                                                <?= $form->field($model, 'resiko_confined_space')->checkbox()->label("Confined Space") ?>
                                            </span>
                                            <span class="ml-3" style="width: 150px;">
                                                <?= $form->field($model, 'resiko_operator_berat')->checkbox()->label("Operator Alat Berat") ?>
                                            </span>
                                            <span class="ml-3" style="width: 120px;">
                                                <?= $form->field($model, 'resiko_tangki_penyelam')->checkbox()->label("Tangki Penyelam") ?>
                                            </span>
                                        </div>
                                        <div style="display: flex;">
                                            <span style="width: 120px;">
                                                <?= $form->field($model, 'resiko_security')->checkbox()->label("Resiko Security") ?>
                                            </span>
                                            <span class="ml-3" style="width: 150px;">
                                                <?= $form->field($model, 'resiko_bekerja_ketinggian')->checkbox()->label("Bekerja Diketinggian") ?>
                                            </span>
                                            <span class="ml-3" style="width: 120px;">
                                                <?= $form->field($model, 'resiko_awak_mobil')->checkbox()->label("Awak Mobil") ?>
                                            </span>
                                        </div>
                                        <div style="display: flex;" class="mt-1">
                                            <span style="width: 120px;">
                                                <?= $form->field($model, 'resiko_pengemudi')->checkbox()->label("Pengemudi") ?>
                                            </span>
                                            <span class="ml-3" style="width: 150px;">
                                                <?= $form->field($model, 'resiko_fire_brigade')->checkbox()->label("Fire Brigade") ?>
                                            </span>
                                        </div>
                                        <div style="display: flex;" class="mt-1">
                                            <span style="width: 300px; display: inline-flex;">
                                                <span>
                                                    <?= $form->field($model, 'resiko_lain')->checkbox()->label("Lain - lain") ?>
                                                </span>
                                                <span class="ml-3">
                                                    <?= $form->field($model, 'resiko_lain_text')->label(false) ?>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div style="margin-top: 25px;">
                                <button class="btn" style="background-color: #35bea3;" id="add-row" type="button"><i class="fa fa-plus" style="color: white;"></i></button>
                            </div>
                            <div class="page-delete">
                                <!-- Start Foreach -->
                                <?php if (!empty($riwayatRekap)) : ?>
                                    <?php foreach ($riwayatRekap as $key => $value) : ?>
                                        <!-- Kondisi bukan parent div -->
                                        <?php if ($key != 0) : ?>
                                            <div style="margin-top: 25px;" class="button-card-delete">
                                                <button class="btn delete-row" style="background-color: #a01b1b;" data-remove="<?= $key ?>" type="button"><i class="fa fa-trash" style="color: white;"></i></button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <!-- Endforeach -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.select2custom').select2()
    });

    $('#add-row').on("click", function(e) {
        e.preventDefault()
        var lastRow = $(".riwayat-kerjaan").last();
        var rowIndex = parseInt(lastRow.attr("rowindex")) + 1;
        var textTanggalMulai = ""
        var textTanggalSelesai = `<option value="Sekarang">Sekarang</option>`

        var currentYear = new Date().getFullYear();
        var earliestYear = 1970;

        while (currentYear >= earliestYear) {
            textTanggalMulai += `<option value="${currentYear}">${currentYear}</option>`
            currentYear -= 1;
        }

        var currentYearLast = new Date().getFullYear();
        while (currentYearLast >= earliestYear) {
            textTanggalSelesai += `<option value="${currentYearLast}">${currentYearLast}</option>`
            currentYearLast -= 1;
        }

        var content = `
            <div class="row riwayat-kerjaan mt-10 index-card-${rowIndex}" rowindex="${rowIndex}">
                <div class="col-md-3">
                    <span>Tahun Mulai</span>
                    <select class="select2custom tanggal-mulai" name="RiwayatPenyakitFormPrima[tahun_mulai][]">
                        ${textTanggalMulai}
                    </select>
                </div>
                <div class="col-md-3">
                    <span>Tahun Selesai</span>
                    <select class="select2custom tanggal-selesai" name="RiwayatPenyakitFormPrima[tahun_selesai][]">
                        ${textTanggalSelesai}
                    </select>
                </div>
                <div class="col-md-3">
                    <span>Perusahaan</span>
                    <input type="text" class="form-control" name="RiwayatPenyakitFormPrima[perusahaan][]">
                </div>
                <div class="col-md-3">
                    <span>Jabatan</span>
                    <input type="text" class="form-control" name="RiwayatPenyakitFormPrima[jabatan][]">
                </div>
            </div>
        `
        lastRow.after(content);
        newButtonRow()

        $('.select2custom').select2()
    })

    $(document).on("click", ".delete-row", function(e) {
        e.preventDefault()
        var removeIndex = $(this).attr("data-remove")
        $(`.index-card-${removeIndex}`).remove();
        $(this).remove();
    });

    function newButtonRow() {
        var buttonlastRow = $(".button-card-delete");
        var buttonlastRowKey = $(".delete-row").last().attr("data-remove");
        if (buttonlastRowKey != undefined) {
            buttonlastRowKey = parseInt(buttonlastRowKey) + 1
        } else {
            buttonlastRowKey = 1;
        }

        // Kalo div nya kosong pake yang ini
        if (buttonlastRow.length == 0) {
            buttonlastRow = $(".page-delete")
        }
        var newContent = `
            <div style="margin-top: 25px;" class="button-card-delete">
                <button class="btn delete-row" style="background-color: #a01b1b;" data-remove="${buttonlastRowKey}" type="button">
                <i class="fa fa-trash" style="color: white;"></i>
                </button>
            </div>
        `

        buttonlastRow.last().after(newContent);
    }
</script>