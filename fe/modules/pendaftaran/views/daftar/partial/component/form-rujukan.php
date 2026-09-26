<div id="form-rujukan" style="display: none;">
    <div class="form-group" id="form-rujukan-content">
        <div class="col-sm-6">
            <?= $form->field($modelRujukan, 'no_rujukan', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput([
                    'data-urutan' => 1
                ]);
            ?>
            <?=
                $form->field($modelRujukan, 'rujukandari_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList([], [
                    'prompt' => '-- Pilih --'
                ])->label(Yii::t('fe', 'Rujukan Dari'));
            ?>
            <?=
                $form->field($modelRujukan, 'nama_perujuk', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput([
                    'onkeyup' => '$((elm) => {this.value = this.value.toLocaleUpperCase()})'
                ]);
            ?>
        </div>
        <div class="col-sm-6">
            <?=
                $form->field($modelRujukan, 'tanggal_rujukan', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ],
                    'addon' => [
                        'append' => [
                            [
                                'content' => '<i id="btn_addon_tglrujuk" class="fa fa-calendar btn_addon_tglrujuk"></i>'
                            ],
                        ]
                    ]
                ])->textInput([
                    'class' => '',
                    'id' => 'tanggal_rujukan_rs',
                    'data-mask' => '99-99-9999'
                ]);
            ?>
            <?=
                $form->field($modelRujukan, 'diagnosa_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList([], [
                    'prompt' => '-'
                ])->label(Yii::t('fe', 'Diagnosa'));
            ?>
        </div>
    </div>
</div>