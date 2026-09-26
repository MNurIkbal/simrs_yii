<div class="row">
    <div class="col-md-12">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#mata" role="button" aria-expanded="false" aria-controls="mata">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Mata'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse  multi-collapse" id="mata">
                        <?= $form->field($modelFisikNew, 'mata_umum_batas_normal')->checkbox() ?>
                        <div class="row detail">
                        <div class="col-md-4 detail pemeriksaan-fisik">
                            <?= $form->field($modelFisikNew, 'persepsi_mata')->radioList([0 => 'Normal', 1 => 'Buta Warna Total', 2 => 'Buta Warna Parsial'], [
                            'item' => function($index, $label, $name, $checked, $value) {
                                $return = '<label class="modal-radio">';
                                if($checked) {$return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_persepsi_mata' . $value . '">';}
                                else {$return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_persepsi_mata' . $value . '">';}
                                $return .= '<i></i>';
                                $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                                $return .= '</label>';
                                return $return;
                            },'inline' => true,
                            ])->label(Yii::t('fe', 'Persepsi Warna')); ?>
                        </div>
                        <div class="col-md-8 detail detail-ui">
                            <label for=""></label>
                            <?= $form->field($modelFisikNew, 'persepsi_mata_note', [])->dropDownList($buta_parsial,[
                                'class' => 'select2 '.$classForm,
                                'prompt' => '— Pilih —',
                                'disabled' => 'disabled'
                            ])->label(false); ?>
                        </div>
                        </div>
                        <?= $form->field($modelFisikNew, 'stabisnus')->radioList($option_adadantiada, ['inline' => true, 'class' => 'stabisnus'])->label(Yii::t('fe', 'Strabismus')); ?>
                        <table class="table table-bordered table-striped table-hover dataTable no-footer table-framed" id="tabel-r">
                        <thead>
                            <tr class="bg-inverse">
                            <th>Bagian Mata</th>
                            <th>Mata Kanan</th>
                            <th>Mata Kiri</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td>Kelopak Mata</td>
                            <td><?= $form->field($modelFisikNew, 'kelopak_mata_kanan')->radioList($option_leher, ['inline' => true, 'class' => 'kelopak_mata_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'kelopak_mata_kiri')->radioList($option_leher, ['inline' => true, 'class' => 'kelopak_mata_kiri'])->label(false); ?></td>
                            </tr>
                            <tr>
                            <td>Bulu Mata</td>
                            <td><?= $form->field($modelFisikNew, 'bulu_mata_kanan')->radioList($option_leher, ['inline' => true, 'class' => 'bulu_mata_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'bulu_mata_kiri')->radioList($option_leher, ['inline' => true, 'class' => 'bulu_mata_kiri'])->label(false); ?></td>
                            </tr>
                            <tr>
                            <td>Konjungtiva</td>
                            <td>
                                <div class="row detail">
                                <div class="col-md-9 detail pemeriksaan-fisik">
                                    <?= $form->field($modelFisikNew, 'konjungtiva_kanan')->radioList([0 => 'Normal', 1 => 'Anemis', 2 => 'Hiperemis', 3 => 'Lainnya'], [
                                    'item' => function($index, $label, $name, $checked, $value) {
                                        $return = '<label class="modal-radio">';
                                        if($checked) {$return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_konjungtiva_kanan' . $value . '">';}
                                        else {$return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_konjungtiva_kanan' . $value . '">';}
                                        $return .= '<i></i>';
                                        $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                                        $return .= '</label>';
                                        return $return;
                                    },'inline' => true,
                                    ])->label(false); ?>
                                </div>
                                <div class="col-md-3 detail">
                                    <?= $form->field($modelFisikNew, 'note_konjungtiva_kanan', [])->textInput(['class' => $classForm,'disabled' => 'disabled'])->label(false); ?>
                                </div>
                                </div>
                            </td>
                            <td>
                                <div class="row detail">
                                <div class="col-md-9 detail pemeriksaan-fisik">
                                    <?= $form->field($modelFisikNew, 'konjungtiva_kiri')->radioList([0 => 'Normal', 1 => 'Anemis', 2 => 'Hiperemis', 3 => 'Lainnya'], [
                                    'item' => function($index, $label, $name, $checked, $value) {
                                        $return = '<label class="modal-radio">';
                                        if($checked) {$return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_konjungtiva_kiri' . $value . '">';}
                                        else {$return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_konjungtiva_kiri' . $value . '">';}
                                        $return .= '<i></i>';
                                        $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                                        $return .= '</label>';
                                        return $return;
                                    },'inline' => true,
                                    ])->label(false); ?>
                                </div>
                                <div class="col-md-3 detail">
                                    <?= $form->field($modelFisikNew, 'note_konjungtiva_kiri', [])->textInput(['class' => $classForm, 'disabled' => 'disabled'])->label(false); ?>
                                </div>
                                </div>
                            </td>
                            </tr>
                            <tr>
                            <td>Sklera</td>
                            <td><?= $form->field($modelFisikNew, 'sklera_kanan')->radioList([0 => 'Normal', 1 => 'Ikterik'], ['inline' => true, 'class' => 'sklera_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'sklera_kiri')->radioList([0 => 'Normal', 1 => 'Ikterik'], ['inline' => true, 'class' => 'sklera_kiri'])->label(false); ?></td>
                            </tr>
                            <tr>
                            <td>Kornea</td>
                            <td><?= $form->field($modelFisikNew, 'kornea_kanan')->radioList($option_leher, ['inline' => true, 'class' => 'kornea_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'kornea_kiri')->radioList($option_leher, ['inline' => true, 'class' => 'kornea_kiri'])->label(false); ?></td>
                            </tr>
                            <tr>
                            <td>Lensa</td>
                            <td><?= $form->field($modelFisikNew, 'lensa_kanan')->radioList([0 => 'Jernih', 1 => 'Keruh'], ['inline' => true, 'class' => 'lensa_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'lensa_kiri')->radioList([0 => 'Jernih', 1 => 'Keruh'], ['inline' => true, 'class' => 'lensa_kiri'])->label(false); ?></td>
                            </tr>
                            <tr>
                            <td>Pupil</td>
                            <td>
                                <?= $form->field($modelFisikNew, 'pupil_kanan')->radioList([0 => 'Bulat', 1 => 'Irregular', 2 => 'Oval'], ['inline' => true, 'class' => 'pupil_kanan'])->label(false); ?>
                                <hr>
                                <?= $form->field($modelFisikNew, 'pupil_isokor')->radioList([1 => 'Isokor', 2 => 'Anisokor'], ['inline' => true, 'class' => 'pupil_isokor'])->label(false); ?>
                            </td>
                            <td>
                                <?= $form->field($modelFisikNew, 'pupil_kiri')->radioList([0 => 'Bulat', 1 => 'Irregular', 2 => 'Oval'], ['inline' => true, 'class' => 'pupil_kiri'])->label(false); ?>
                                <hr>
                                <div class="row detail">
                                <div class="col-md-2 detail"><label for="">Diameter</label></div>
                                <div class="col-md-10 detail"><?= $form->field($modelFisikNew, 'pupil_diameter', [])->textInput(['class' => $classForm])->label(false); ?></div>
                            </td>
                            
                            </tr>
                            <tr style="background-color: #37474f;border-color:#37474f;color:#ffffff;">
                                <th>Refleks Cahaya</th>
                                <th>Mata Kanan</th>
                                <th>Mata Kiri</th>
                            </tr>
                            <tr>
                            <td>Direk</td>
                            <td><?= $form->field($modelFisikNew, 'direk_kanan')->radioList($option_adadantiada, ['inline' => true, 'class' => 'direk_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'direk_kiri')->radioList($option_adadantiada, ['inline' => true, 'class' => 'direk_kiri'])->label(false); ?></td>
                            </tr>
                            <tr>
                            <td>Indirek</td>
                            <td><?= $form->field($modelFisikNew, 'indirek_kanan')->radioList($option_adadantiada, ['inline' => true, 'class' => 'indirek_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'indirek_kiri')->radioList($option_adadantiada, ['inline' => true, 'class' => 'indirek_kiri'])->label(false); ?></td>
                            </tr>
                            <tr>
                            <td>Lapang Pandang</td>
                            <td colspan="2">
                                <?= $form->field($modelFisikNew, 'lapang_pandang', [])->textInput(['class' => $classForm])->label(false); ?>
                            </td>
                            </tr>
                        </tbody>
                        </table>
                        <div class="row detail"><br>
                        <div class="col-md-6">
                            <label for=""><b>VISUS Jauh</b></label>
                            <div class="row detail">
                            <div class="col-md-6">
                                <label for=""><?= Yii::t('fe', 'Tanpa Koreksi'); ?></label>
                                <div class="row">
                                <div class="col-md-2 detail"><label for="">OD</label></div>
                                <div class="col-md-10 detail"><?= $form->field($modelFisikNew, 'tanpa_koreksi_OD', [])->textInput(['class' => $classForm])->label(false); ?></div>
                                </div>
                                <div class="row">
                                <div class="col-md-2 detail"><label for="">OS</label></div>
                                <div class="col-md-10 detail"><?= $form->field($modelFisikNew, 'tanpa_koreksi_OS', [])->textInput(['class' => $classForm])->label(false); ?></div>
                                </div>
                                <div class="row">
                                <div class="col-md-2 detail"><label for="">ODS</label></div>
                                <div class="col-md-10 detail"><?= $form->field($modelFisikNew, 'tanpa_koreksi_ODS_jauh', [])->textInput(['class' => $classForm])->label(false); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for=""><?= Yii::t('fe', 'Dengan Koreksi'); ?></label>
                                <div class="row">
                                <div class="col-md-2 detail"><label for="">OD</label></div>
                                <div class="col-md-10 detail"><?= $form->field($modelFisikNew, 'dengan_koreksi_OD', [])->textInput(['class' => $classForm])->label(false); ?></div>
                                </div>
                                <div class="row">
                                <div class="col-md-2 detail"><label for="">OS</label></div>
                                <div class="col-md-10 detail"><?= $form->field($modelFisikNew, 'dengan_koreksi_OS', [])->textInput(['class' => $classForm])->label(false); ?></div>
                                </div>
                                <div class="row">
                                <div class="col-md-2 detail"><label for="">ODS</label></div>
                                <div class="col-md-10 detail"><?= $form->field($modelFisikNew, 'dengan_koreksi_ODS_jauh', [])->textInput(['class' => $classForm])->label(false); ?></div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for=""><b>VISUS Dekat</b></label>
                            <div class="row detail">
                            <div class="col-md-6">
                                <label for=""><?= Yii::t('fe', 'Tanpa Koreksi'); ?></label>
                                <div class="row">
                                <div class="col-md-2 detail"><label for="">ODS</label></div>
                                <div class="col-md-10 detail"><?= $form->field($modelFisikNew, 'tanpa_koreksi_ODS_dekat', [])->textInput(['class' => $classForm])->label(false); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for=""><?= Yii::t('fe', 'Dengan Koreksi'); ?></label>
                                <div class="row">
                                <div class="col-md-2 detail"><label for="">ODS</label></div>
                                <div class="col-md-10 detail"><?= $form->field($modelFisikNew, 'dengan_koreksi_ODS_dekat', [])->textInput(['class' => $classForm])->label(false); ?></div>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>
                        <?= $form->field($modelFisikNew, 'keterangan_mata')->textArea([
                        'class' => $classForm,
                        'rows' => 3
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
