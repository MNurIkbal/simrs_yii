<div class="row">
    <div class="col-md-12" >
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#telinga" role="button" aria-expanded="false" aria-controls="telinga">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Telinga'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse  multi-collapse" id="telinga">
                        <?= $form->field($modelFisikNew, 'telinga_umum_batas_normal')->checkbox() ?>
                        <table class="table table-bordered table-striped table-hover dataTable no-footer table-framed" id="tabel-r">
                        <thead>
                            <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'Bagian telinga'); ?></th>
                            <th><?= Yii::t('fe', 'Telinga Kanan'); ?></th>
                            <th><?= Yii::t('fe', 'Telinga Kiri'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td><?= Yii::t('fe', 'Liang Telinga'); ?></td>
                            <td><?= $form->field($modelFisikNew, 'liang_telinga_kanan')->radioList($option_leher, ['inline' => true, 'class' => 'liang_telinga_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'liang_telinga_kiri')->radioList($option_leher, ['inline' => true, 'class' => 'liang_telinga_kiri'])->label(false); ?></td>
                            </tr>
                            <tr>
                            <td><?= Yii::t('fe', 'Serumen'); ?></td>
                            <td><?= $form->field($modelFisikNew, 'serumen_kanan')->radioList($option_adadantiada, ['inline' => true, 'class' => 'serumen_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'serumen_kiri')->radioList($option_adadantiada, ['inline' => true, 'class' => 'serumen_kiri'])->label(false); ?></td>
                            </tr>
                            <tr>
                            <td><?= Yii::t('fe', 'Membran Timpani'); ?></td>
                            <td><?= $form->field($modelFisikNew, 'membran_timpani_telinga_kanan')->radioList([0 => 'Intak', 1 => 'Tidak Intak'], ['inline' => true, 'class' => 'membran_timpani_telinga_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'membran_timpani_telinga_kiri')->radioList([0 => 'Intak', 1 => 'Tidak Intak'], ['inline' => true, 'class' => 'membran_timpani_telinga_kiri'])->label(false); ?></td>
                            </tr>
                            <tr>
                            <td><?= Yii::t('fe', 'Pendengaran'); ?></td>
                            <td><?= $form->field($modelFisikNew, 'pendengaran_telinga_kanan')->radioList($option_leher, ['inline' => true, 'class' => 'pendengaran_telinga_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'pendengaran_telinga_kiri')->radioList($option_leher, ['inline' => true, 'class' => 'pendengaran_telinga_kiri'])->label(false); ?></td>
                            </tr>
                        </tbody>
                        </table>
                        <?= $form->field($modelFisikNew, 'keterangan_telinga')->textArea([
                        'class' => 'form-control input-sm',
                        'rows' => 3
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
