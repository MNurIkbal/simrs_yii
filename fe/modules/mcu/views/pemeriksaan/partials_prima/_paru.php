<div class="row">
    <div class="col-md-12">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#paru" role="button" aria-expanded="false" aria-controls="paru">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Paru - paru'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse  multi-collapse" id="paru">
                        <?= $form->field($modelFisikNew, 'paru_umum_batas_normal')->checkbox() ?>
                        <?= $form->field($modelFisikNew, 'pergerakan')->radioList([0 => 'Simetris', 1 => 'Asimetris'], ['inline' => true, 'class' => 'pergerakan']); ?>
                        <table class="table table-bordered table-striped table-hover dataTable no-footer table-framed" id="tabel-r">
                        <thead>
                            <tr class="bg-inverse">
                            <th>Bagian Paru</th>
                            <th>Paru Kanan</th>
                            <th>Paru Kiri</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td>Perkusi</td>
                            <td><?= $form->field($modelFisikNew, 'perkusi_kanan')->radioList([0 => 'Sonor', 1 => 'Dull', 2 => 'Timpani', 3 => 'Hipersonor'], ['inline' => true, 'class' => 'perkusi_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'perkusi_kiri')->radioList([0 => 'Sonor', 1 => 'Dull', 2 => 'Timpani', 3 => 'Hipersonor'], ['inline' => true, 'class' => 'perkusi_kiri'])->label(false); ?></td>
                            </tr>
                            <tr>
                            <td>Bunyi Napas</td>
                            <td><?= $form->field($modelFisikNew, 'bunyi_napas_kanan')->radioList([0 => 'Vesikuler', 1 => 'Bronc.Vesikuler', 2 => 'Tidak Ada'], ['inline' => true, 'class' => 'bunyi_napas_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'bunyi_napas_kiri')->radioList([0 => 'Vesikuler', 1 => 'Bronc.Vesikuler', 2 => 'Tidak Ada'], ['inline' => true, 'class' => 'bunyi_napas_kiri'])->label(false); ?></td>
                            </tr>
                            <tr>
                            <td>Rhonchi</td>
                            <td><?= $form->field($modelFisikNew, 'membran_timpani_paru_kanan')->radioList($option_adadantiada, ['inline' => true, 'class' => 'membran_timpani_paru_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'membran_timpani_paru_kiri')->radioList($option_adadantiada, ['inline' => true, 'class' => 'membran_timpani_paru_kiri'])->label(false); ?></td>
                            </tr>
                            <tr>
                            <td>Wheezing</td>
                            <td><?= $form->field($modelFisikNew, 'pendengaran_paru_kanan')->radioList($option_adadantiada, ['inline' => true, 'class' => 'pendengaran_paru_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'pendengaran_paru_kiri')->radioList($option_adadantiada, ['inline' => true, 'class' => 'pendengaran_paru_kiri'])->label(false); ?></td>
                            </tr>
                        </tbody>
                        </table>
                        <?= $form->field($modelFisikNew, 'keterangan_paru')->textArea([
                        'class' => 'form-control input-sm',
                        'rows' => 3
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
