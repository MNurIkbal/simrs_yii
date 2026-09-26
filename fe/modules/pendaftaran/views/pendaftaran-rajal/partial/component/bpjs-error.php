<?php
    use yii\helpers\Html;
?>
<div id="form-bpjs-error" style="display: none;">
    <div id="form-bpjs-error-content">
        <div class="row">
            <div class="col-sm-12">
            <div class="alert alert-warning alert-styled-left alert-arrow-left text-center">
                <span class="text-semibold" style="font-size:27px;">Perhatian !</span><br>
                <strong style="font-size: 14px;">Server BPJS Sedang ada ganguan mohon bersabar</strong><br>
                <strong style="font-size: 14px;">Pesan Error : <u><i id="error-bpjs-msg"></i></u></strong>
            </div>
            </div>
            <div class="col-md-6">
                <?php
                    echo $form->field($modelPasien, 'no_rekam_medik', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-4',
                            'wrapper' => 'col-md-7'
                        ],
                        'addon' => [
                            'prepend' => [
                                'content'=> Html::checkbox('chk-statuspasien-bpjs', true) . ' ' . Yii::t('fe', 'Pasien lama'),
                                'options'=>[]
                            ],
                            'append' => [
                                'content' => Html::a('<i class="fa fa-search"></i>',null, [
                                                            'data-toggle' => 'modal',
                                                            'data-target' => '#modal_pencarian_lanjutan',
                                                            'data-width' => '1000px',
                                                            'data-popup' => "tooltip",
                                                            'id' => 'btn-pencarian-lanjutan',
                                                            'action' =>'/pendaftaran/daftar/pencarian-lanjutan',
                                                            'title' => Yii::t("fe","Pencarian Lanjutan")
                                                    ])
                            ]
                        ]
                    ])->dropDownList([], [
                        'class' => 'select2',
                        'prompt' => '-',
                        'id' => 'no_rekam_medik_bpjs',
                        'placeholder' => Yii::t('fe','No rm').' / '.Yii::t('fe', 'Nama pasien'). ' / ' .Yii::t('fe', 'Tanggal lahir'),
                        'data-urutan' => 1
                    ]);
                ?>
            </div>
        </div>
    </div>
</div>