<div class="row">
    <div class="col-md-12 ml-3 mt-3">
        <div class="panel panel-default">
            <a data-toggle="collapse" href="#riwayatpenyakit" role="button" aria-expanded="true" aria-controls="riwayatpenyakit">
                <div class="panel-heading flex-container">
                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Pasien Khusus (Tenaga Kerja)'); ?></b></h6>
                    <div>
                        <ul class="icons-list">
                            <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                        </ul>
                    </div>
                </div>
            </a>
            <div class="panel-body collapse multi-collapse collapse in" id="riwayatpenyakit">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $form->field($model, 'pekerjaan')->textInput(); ?>
                        </div>
                        <div class="form-group">
                            <?= $form->field($model, 'lokasi_kerja'); ?>
                        </div>
                        <div class="form-group">
                            <?= $form->field($model, 'matriks_pemeriksaan')->radioList([0 => "Matriks Lama", 1 => "Matriks Baru "], [
                                'inline' => true
                            ]); ?>
                        </div>
                        <div class="form-group">
                            <?= $form->field($model, 'nama_perusaahan')->textInput()->label("Nama Perusahaan"); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $form->field($model, 'tipe_pekerja')->radioList([0 => "Field Worker", 1 => "Office Worker "], [
                                'inline' => true
                            ])->label("Tipe Pekerjaan"); ?>
                        </div>
                        <div class="form-group">
                            <?= $form->field($model, 'prosedur_pemeriksaan')->radioList(
                                [
                                    0 => "Sebelum Bekerja (Pre Employment)",
                                    1 => "Pemeriksaan Berkala (Periodic)",
                                    2 => "Pemeriksaan Spesific: Surveillance",
                                    3 => "Pemeriksaan Khusus: Job Transfer, For Cause Return to Work",
                                    4 => "Lainnya",
                                ],
                                [
                                    'inline' => false,
                                    'item' => function ($index, $label, $name, $checked, $value) use ($model) {
                                        $lainnyaValue = '';
                                        $check = null;
                                        if($model->prosedur_pemeriksaan != null) {
                                            if ($model->prosedur_pemeriksaan == $value) {
                                                $check = 'checked="checked"';
                                            }
                                        }
                                        if ($index == 4) {
                                            $lainnyaValue .= '
                                                <span class="ml-3">
                                                    <input type="text" name="RiwayatPenyakitFormPrima[prosedur_pemeriksaan_text]" class="form-control" value="'.$model->prosedur_pemeriksaan_text.'" style="width:100px; display:inline-flex !important">
                                                </span>
                                            ';
                                        }
                                        $return = '<div class="radio-' . $value . '">';
                                        $return .= '<input name="RiwayatPenyakitFormPrima[prosedur_pemeriksaan]"'.$check.'class="klaiminacbgranapform-prosedur_pemeriksaan" type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . ' id="pemeriksaan-' . $value . '">';
                                        $return .= ' <i></i>';
                                        $return .= '<span>' . ucwords($label) . $lainnyaValue . '</span>';
                                        $return .= '</div>';

                                        return $return;
                                    }
                                ]
                            ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>