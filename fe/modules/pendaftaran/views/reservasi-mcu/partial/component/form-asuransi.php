<div id="form-input-asuransi" style="display: none;">
    <div class="form-group" id="form-asuransi-content">
        <div class="col-sm-6">
            <?=
                $form->field($modelAsuransi, 'namapemilikasuransi', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3',
                        'wrapper' => 'col-md-9',
                        'autofocus' => 'autofocus'
                    ]
                ])->textInput([
                    'data-urutan' => 1,
                ])->label(Yii::t('fe', 'Nama Pemilik'));
            ?>
            <?=
                $form->field($modelAsuransi, 'nomorpokokperusahaan', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3',
                        'wrapper' => 'col-md-9'
                    ]
                ])->textInput()->label(Yii::t('fe', 'Nomor Pokok Perusahaan'));
            ?>
            <?=
                $form->field($modelAsuransi, 'kelastanggungan_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3',
                        'wrapper' => 'col-md-9'
                    ]
                ])->dropDownList($kelaspelayanan, [
                    'prompt' => '-'
                ])->label(Yii::t('fe', 'Kelas Tanggungan'));
            ?>
        </div>
        <div class="col-sm-6">
        <?=
            $form->field($modelAsuransi, 'namaperusahaan', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->textInput()->label(Yii::t('fe', 'Nama Perusahaan'));
        ?>
        <?=
            $form->field($modelAsuransi, 'tgl_konfirmasi', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ],
                'addon' => [
                    'append' => [
                        ['content' => '<i id="btn_addon_tgllahir" class="fa fa-calendar "></i>'],
                    ],
                ]
            ])->textInput([
                'class'=>'pickadate-w-month',
                'data-mask' => '99-99-9999',
                'id' => 'tgl_konfirmasi'
            ])->label(Yii::t('fe', 'Tanggal Konfirmasi'));
        ?>
        <?=
            $form->field($modelAsuransi, 'status_konfirmasi', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->checkbox([
                'class' => 'styled action-checked',
            ]);
        ?>
        </div>
    </div>
</div>