<?php
    use yii\helpers\ArrayHelper;
?>
<div id="form-input-pj" style="display: none;">
    <div class="form-group" id="form-pj-content">
        <div class="col-sm-6">
            <?php
                echo $form->field($modelPj, 'pj_pengantar', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($data_lookup['pengantar'], 'lookup_id', 'lookup_value'), [
                    'prompt' => '-',
                    'data-urutan' => 1
                ]);

                echo $form->field($modelPj, 'pj_nama', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput();

                echo $form->field($modelPj, 'pj_jk', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->radioList(
                    ArrayHelper::map($data_lookup['jenis_kelamin'], 'lookup_id', 'lookup_value'),
                    [
                        'inline' => true,
                        'item' => function($index, $label, $name, $checked, $value) {
                            $return = '<label class="radio-inlineo">';
                                $return .= '<input id="pj_jk_'. $value.'" type="radio" name="' . $name . '" value="' . $value . '" class="styled">';
                                $return .= '<i></i>';
                                $return .= '<span style="margin-left:5px;">' . $label . '</span>';
                            $return .= '</label>';

                            return $return;
                        }
                    ]
                );
                ?>
                <div id="error_PjpasienFormpj_jk" style="margin-left:178px;"></div>
                <?php
                echo $form->field($modelPj, 'pj_jenis_identitas', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(
                    ArrayHelper::map($data_lookup['jenis_identitas'], 'lookup_id', 'lookup_value'), [
                    'prompt' => '-'
                ]);

                echo $form->field($modelPj, 'pj_no_identitas', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput();

                echo $form->field($modelPj, 'pj_hubungan', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($data_lookup['hubungan_keluarga'], 'lookup_id', 'lookup_value'), [
                    'prompt' => '-'
                ]);
            ?>
        </div>
        <div class="col-sm-6">
            <?php
                echo $form->field($modelPj, 'pj_tempat_lahir', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput();

                echo $form->field($modelPj, 'pj_tanggal_lahir', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ],
                    'addon' => [
                        'append' => [
                            ['content' => '<i id="pj-date" class="fa fa-calendar "></i>'],
                        ],
                    ]
                ])->textInput([
                    'class'=>'pickadate-w-month dateusia',
                    'data-mask'=>'99-99-9999'
                ]);

                echo $form->field($modelPj, 'pj_umur', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput(['class'=>'usiapjtext', 'readonly'=>true]);

                echo $form->field($modelPj, 'pj_no_telepon', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ],
                ])->textInput();

                echo $form->field($modelPj, 'pj_alamat', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textArea();
            ?>
        </div>
    </div>
</div>