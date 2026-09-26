<div class="row">
    <div class="col-md-6">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#keadaanumum" role="button" aria-expanded="false" aria-controls="keadaanumum">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Keadaan Umum'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse  multi-collapse" id="keadaanumum">
                        <?= $form->field($modelFisikNew, 'kesadaran_umum_batas_normal')->checkbox() ?>
                        <?= $form->field($modelFisikNew, 'kesadaran')->radioList([0 => 'Compos Mentis', 1 => 'Menurun'], ['inline' => true, 'class' => 'kesadaran'])->label($modelFisikNew->getAttributeLabel('kesadaran')); ?>
                        <?= $form->field($modelFisikNew, 'kontak')->radioList([0 => 'Adekuat', 1 => 'Inadekuat'], ['inline' => true, 'class' => 'kontak'])->label($modelFisikNew->getAttributeLabel('kontak')); ?>
                        <?= $form->field($modelFisikNew, 'gangguan_berjalan')->radioList($option_yesorno, ['inline' => true, 'class' => 'gangguan_berjalan'])->label($modelFisikNew->getAttributeLabel('gangguan_berjalan')); ?>
                        <?= $form->field($modelFisikNew, 'sakit_saat_berjalan')->radioList($option_yesorno, ['inline' => true, 'class' => 'sakit_saat_berjalan'])->label($modelFisikNew->getAttributeLabel('sakit_saat_berjalan')); ?>
                        <?= $form->field($modelFisikNew, 'postur')->radioList([0 => 'Normal', 1 => 'Lordosis', 2 => 'Kifosis', 3 => 'Skoliosis'], ['inline' => true, 'class' => 'postur'])->label($modelFisikNew->getAttributeLabel('postur')); ?>
                        <?= $form->field($modelFisikNew, 'keterangan_keadaan_umum', [])->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#kelenjar_getah_bening" role="button" aria-expanded="false" aria-controls="kelenjar_getah_bening" onKeyPress="">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Kelenjar Getah Bening'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse  multi-collapse" id="kelenjar_getah_bening">
                        <?= $form->field($modelFisikNew, 'kelenjar_getah_bening_umum_batas_normal')->checkbox() ?>
                        <?= $form->field($modelFisikNew, 'leher')->radioList($option_leher, ['inline' => true, 'class' => 'leher'])->label($modelFisikNew->getAttributeLabel('leher')); ?>
                        <?= $form->field($modelFisikNew, 'aksilla')->radioList($option_leher, ['inline' => true, 'class' => 'aksilla'])->label($modelFisikNew->getAttributeLabel('aksilla')); ?>
                        <?= $form->field($modelFisikNew, 'inguinal')->radioList($option_leher, ['inline' => true, 'class' => 'inguinal'])->label($modelFisikNew->getAttributeLabel('inguinal')); ?>
                        <div class="row detail">
                        <div class="col-md-7 detail">
                            <?= $form->field($modelFisikNew, 'note_lainnya_kelenjar_getah_bening', [])->textInput(['class' => $classForm]); ?>
                        </div>
                        <div class="col-md-5 detail">
                            <label for="lainnya_kelenjar_getah_bening"></label>
                            <?= $form->field($modelFisikNew, 'lainnya_kelenjar_getah_bening')->radioList($option_leher, ['inline' => true, 'class' => 'lainnya_kelenjar_getah_bening'])->label(false); ?>
                        </div>
                        </div>
                        <?= $form->field($modelFisikNew, 'keterangan_kelenjar_getah_bening', [])->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
