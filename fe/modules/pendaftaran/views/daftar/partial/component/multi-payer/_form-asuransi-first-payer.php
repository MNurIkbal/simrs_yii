<?php
    use yii\helpers\Html;
?>

<div id="form-input-first-asuransi" style="display: none;">
    <div class="form-group" id="form-first-asuransi-content">
        <div class="col-sm-6">
            <?=
                $form->field($multiPayer, 'add_namapemilikasuransi_1', [
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
                $form->field($multiPayer, 'add_nomorpokokperusahaan_1', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3',
                        'wrapper' => 'col-md-9'
                    ]
                ])->textInput()->label(Yii::t('fe', 'Nomor Pokok Perusahaan'));
            ?>
            <?=
                $form->field($multiPayer, 'add_kelastanggungan_id_1', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3',
                        'wrapper' => 'col-md-9'
                    ]
                ])->dropDownList($kelaspelayanan, [
                    'prompt' => '-'
                ])->label(Yii::t('fe', 'Kelas Tanggungan'));
            ?>
            <?=
                $form->field($multiPayer, 'add_penjamingrade_id_1', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3',
                        'wrapper' => 'col-md-9'
                    ]
                ])->dropDownList([], [
                    'prompt' => '-'
                ])->label(Yii::t('fe', 'Grade Penjamin'));
            ?>
        </div>
        <div class="col-sm-6">
        <?=
            $form->field($multiPayer, 'add_namaperusahaan_1', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->textInput()->label(Yii::t('fe', 'Nama Perusahaan'));
        ?>
        <?=
            $form->field($multiPayer, 'add_tgl_konfirmasi_1', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ],
                'addon' => [
                    'append' => [
                        ['content' => '<i id="btn_addon_add_tgl_konfirmasi_1" class="fa fa-calendar "></i>'],
                    ],
                ]
            ])->textInput([
                'class'=>'pickadate-w-month',
                'data-mask' => '99-99-9999',
                'id' => 'add_tgl_konfirmasi_1'
            ])->label(Yii::t('fe', 'Tanggal Konfirmasi'));
        ?>
        <?=
            $form->field($multiPayer, 'add_status_konfirmasi_1', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->checkbox([
                'class' => 'styled action-checked',
            ]);
        ?>
        <?= 
            Html::hiddenInput('multicarabayarform-add_asuransipasien_id_1', '', ['id' => 'multicarabayarform-add_asuransipasien_id_1']);
        ?>
        </div>
    </div>
</div>