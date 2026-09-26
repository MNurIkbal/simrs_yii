<div class="row">
    <div class="col-md-12 ml-3 mt-3">
        <div class="panel panel-default">
            <a data-toggle="collapse" href="#penyakitkeluarga" role="button" aria-expanded="true" aria-controls="penyakitkeluarga">
                <div class="panel-heading flex-container">
                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Penyakit Dalam Keluarga'); ?></b></h6>
                    <div>
                        <ul class="icons-list">
                            <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                        </ul>
                    </div>
                </div>
            </a>
            <div class="panel-body collapse multi-collapse collapse in" id="penyakitkeluarga">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <span>Apakah dalam keluarga sedarah (Ayah/Ibu/Saudara Kandung/dll) Pernah mengalami serangan jantung atau stroke saat usia kurang dari 55 tahun pada laki laki dan 65 tahun pada wanita ?</span>
                            <?= $form->field($model, 'penyakit_jantung_stroke')->radioList(["Tidak", "Ya"], ["inline" => true])->label(false); ?>
                        </div>
                        <div class="form-group">
                            <span>Apakah dalam keluarga sedarah (Ayah/Ibu/Saudara Kandung/dll) diketahui mengalami kanker atau tumor ganas seperti kanker payudara, kanker usus besar, dll ?</span>
                            <?= $form->field($model, 'penyakit_kanker_tumor')->radioList(["Tidak", "Ya"], ["inline" => true])->label(false); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $form->field($model, 'penyakit_riwayat_saudara')->label("Riwayat Penyakit Keluarga Lainnya (Saudara Kandung)"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>