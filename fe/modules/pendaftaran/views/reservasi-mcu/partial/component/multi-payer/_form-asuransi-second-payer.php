<?php
    use yii\helpers\Html;
?>

<div id="form-input-second-asuransi" style="display: none;">
    <div class="form-group" id="form-second-asuransi-content">
        <div class="col-sm-6">
            <?=
                $form->field($multiPayer, 'add_namapemilikasuransi_2', [
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
                $form->field($multiPayer, 'add_nomorpokokperusahaan_2', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-3',
                        'wrapper' => 'col-md-9'
                    ]
                ])->textInput()->label(Yii::t('fe', 'Nomor Pokok Perusahaan'));
            ?>
            <?=
                $form->field($multiPayer, 'add_kelastanggungan_id_2', [
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
            $form->field($multiPayer, 'add_namaperusahaan_2', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->textInput()->label(Yii::t('fe', 'Nama Perusahaan'));
        ?>
        <?=
            $form->field($multiPayer, 'add_tgl_konfirmasi_2', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ],
                'addon' => [
                    'append' => [
                        ['content' => '<i id="btn_addon_add_tgl_konfirmasi_2" class="fa fa-calendar "></i>'],
                    ],
                ]
            ])->textInput([
                'class'=>'pickadate-w-month',
                'data-mask' => '99-99-9999',
                'id' => 'add_tgl_konfirmasi_2'
            ])->label(Yii::t('fe', 'Tanggal Konfirmasi'));
        ?>
        <?=
            $form->field($multiPayer, 'add_status_konfirmasi_2', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-3',
                    'wrapper' => 'col-md-9'
                ]
            ])->checkbox([
                'class' => 'styled action-checked',
            ]);
        ?>
        <?= 
            Html::hiddenInput('multicarabayarform-add_asuransipasien_id_2', '', ['id' => 'multicarabayarform-add_asuransipasien_id_2']);
        ?>
        </div>
    </div>
</div>