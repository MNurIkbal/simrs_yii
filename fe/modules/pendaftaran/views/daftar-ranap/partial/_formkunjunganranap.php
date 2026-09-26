<?php

/**
 * @Author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

// $param = isset($_GET['param']) ? $_GET['param'] : '';
?>

<?php
$form = ActiveForm::begin([
    'id' => 'ranap-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => ActiveForm::TYPE_VERTICAL,
    // 'formConfig' => [
    //     'labelSpan' => 3,
    //     'deviceSize' => ActiveForm::SIZE_SMALL
    // ],
]);
?>

<div id="form-kunjungan-content">
    <div class='col-md-6'>
        <?= $form->field($modelAdmisi, 'bookingkamar_no', [
            'inputOptions'=>[
                'placeholder'=>'--No pemesanan--',
                'readonly'=>true,
                'id'=>'bookingkamar_no'
            ],
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-7'
            ],
            'addon' => [
                'prepend' => [
                    'content'=>'<input type="checkbox" class="notUniform" id="is_booked" data-urutan=1> pesan kamar'
                ],
                'append' => [
                    'content'=>'
                        <button
                            type="button"
                            action="/pendaftaran/daftar/modal-bookingkamar"
                            data-width="80%"
                            data-toggle="modal" 
                            data-target="#modal_backdrop"
                            class="btn btn-info btn-caribooking"
                            disabled="disabled">
                        Cari</button>',
                    'asButton'=>true
                ]
            ]
        ]); ?>

        <?= 
        $form->field($modelAdmisi, 'tgl_admisi', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-7'
            ],
            'addon' => ['append' => [
                'content' => '<i class="fa fa-calendar"></i>'
            ]
        ]
        ])->textInput([
            'placeholder' => $modelAdmisi->getAttributeLabel('tgl_pendaftaran'),
            'class' => 'form-control input-sm pickadate',
            'id' => 'datetime',
            'autocomplete' => "off",
            'readonly' => true
        ]);
        ?>

        <?=
            $form->field($modelKunjungan, 'jeniskasuspenyakit_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->dropDownList($jeniskasus, [
                'class' => 'selectJeniskasus',
                'id'=>'jeniskasuspenyakit_id',
                'prompt' => Yii::t('fe', '--Pilih--')
            ]);
        ?>

        <?=
            $form->field($modelKunjungan, 'kelaspelayanan_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ],
                'addon' => [
                    'append' => [
                        'content'=>Html::button(Yii::t('fe','Kamar'), [
                            'id'=>'btnCariKamar',
                            'class' => 'btn btn-default',
                        ]),
                        'asButton'=>true
                    ]
                ]
            ])->dropDownList($kelaspelayanan, [
                'class' => 'selectKp',
                'id'=>'kelaspelayanan_id',
                'prompt' => Yii::t('fe', '--Pilih--')
            ]);
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group highlight-addon field-ruangan_id required">
                    <div class="col-md-8">
                        <label class="control-label has-star">Ruangan</label>
                        <span id="ruanganLabelValue" style="color: green;font-weight: bold;"></span>
                        <?=Html::activeHiddenInput($modelKunjungan, 'ruangan_id', ['id'=> 'ruanganIdHidden'])?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 is_pasientitipan hidden">
            <?= $form->field($modelAdmisi, 'is_pasientitipan', [
                    'options' => [
                        'tag' => false,
                    ],
                ])->checkbox([
                    'label' => 'Kamar Tagihan',
                    'value' => 1,
                    'class' => 'styled action-checked',
                ])->label(false);
            ?>
        </div>
        <div class="kelas_ditagihkan hidden">
            <?= $form->field($modelAdmisi, 'kelas_ditagihkan_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ],
                'addon' => [
                    'append' => [
                        'content' => Html::button(Yii::t('fe','Kamar'), [
                            'id' => 'btnCariKamarTitipan',
                            'class' => 'btn btn-default',
                        ]),
                        'asButton'=>true
                    ]
                ]
            ])->dropDownList($kelaspelayanan, [
                'class' => 'selectKt',
                'id' => 'kelas_ditagihkan_id',
                // 'onChange' => 'autoPilihKamarTitipan(this)', //comment sementara RPP 310
                'prompt' => Yii::t('fe', '--Pilih--')
            ]) ?>
            <div class="form-group highlight-addon field-ruangan_titipan_id required">
                <div class="col-md-8">
                    <label class="control-label has-star">Ruangan Tagihan</label>
                    <span id="ruanganTitipanLabelValue" style="color: green;font-weight: bold;"></span>
                    <?=Html::activeHiddenInput($modelAdmisi, 'ruangan_titipan_id', ['id'=> 'ruanganTitipanIdHidden'])?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <?= $form->field($modelAdmisi, 'kamarruangan_nokamar', [
            'inputOptions'=>['id'=>'nokamar', 'readonly'=>true],
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-7'
            ],
        ]); ?>

        <?php
        echo $form->field($modelAdmisi, 'pegawai_id', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-7'
            ]
            ])->dropDownList([], [
                'id' => 'pegawai_id',
                'class' => 'form-control',
                'disabled' => true,
                'prompt' => Yii::t('fe', '--Pilih--')
            ]);
        ?>
        <?= $form->field($modelAdmisi, 'keterangan', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->textArea(); 
        ?>
        <?php if (isset($is_limit_tagihan) && $is_limit_tagihan) { ?>
        <?= $form->field($modelKunjungan, 'limit_tagihan', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-7'
            ],
            'addon' => [
                'prepend' => [
                    'asButton' => false,
                    'content' => 'Rp.'
                ]
            ]
        ])->textInput([
                'placeholder' => $modelKunjungan->getAttributeLabel('limit_tagihan'),
                'class' => 'form-control input-sm doco-number',
                'id' => 'limit_tagihan'
        ]) ?>
        <?php } ?>

        <?php 
        if($show_referal) :
            $data_referral = $modelKunjungan->konfig_referral_required
                ? ArrayHelper::map(ArrayHelper::getValue($data_lookup, 'referral_marketing', []), 'lookup_value', 'lookup_name')
                : ArrayHelper::map(ArrayHelper::getValue($data_master, 'pegawai', []), 'pegawai_id', 'nama_pegawai');

            echo $form->field($modelKunjungan, 'referal', [
                'options' => [
                    'class' => 'form-group '.($modelKunjungan->konfig_referral_required ? 'required' : '')
                ],
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4 '.($modelKunjungan->konfig_referral_required ? 'has-star' : ''),
                    'wrapper' => 'col-md-7'
                ]
            ])->dropDownList($data_referral, [
                'class' => 'selectReferal',
                'id'=>'referal',
                'prompt' => '-',
                'data-urutan' => 1,
            ]);
            echo Html::hiddenInput('konfig_referral_required', $modelKunjungan->konfig_referral_required);
        endif;
        ?>

        <!-- hidden field -->
        <?php
        echo $form->field($modelAdmisi, 'bookingkamar_id', [
            'inputOptions'=>[
                'id'=>'bookingkamar_id'
            ]
        ])->hiddenInput()->label(false);
        ?>
        <?php
        echo $form->field($modelAdmisi, 'kamarruangan_id', [
            'inputOptions'=>[
                'id'=>'kamarruangan_id'
            ]
        ])->hiddenInput()->label(false);
        ?>
        <?php
        echo $form->field($modelAdmisi, 'kamar_titipan_id', [
            'inputOptions'=>[
                'id'=>'kamar_titipan_id'
            ]
        ])->hiddenInput()->label(false);
        ?>
        <?php
        echo $form->field($modelAdmisi, 'kamartempattidur_id', [
            'inputOptions'=>[
                'id'=>'kamartempattidur_id'
            ]
        ])->hiddenInput()->label(false);
        ?>

        <?php
        echo $form->field($modelAdmisi, 'tempattidur_titipan_id', [
            'inputOptions'=>[
                'id'=>'tempattidur_titipan_id'
            ]
        ])->hiddenInput()->label(false);
        ?>

        <?=
        Html::hiddenInput('temp_ruangan_id', '', ['id'=>'temp_ruangan_id']);
        ?>

        <?php
            if (!empty($id_booking)) {
                echo $form->field($modelAdmisi, 'pendaftaran_id', [
                    'inputOptions' => [
                        'id' => 'pendaftaran-id'
                    ]
                ])->hiddenInput()->label(false);
            } else {
                echo Html::activeHiddenInput($modelAdmisi, 'pendaftaran_id', ['class'=>'pendaftaran-id']);
            }
        ?>
        <?=Html::activeHiddenInput($modelAdmisi, 'pasien_id', ['class'=>'pasien-id'])?>
        <?=Html::activeHiddenInput($modelAdmisi, 'instalasi_id', ['value'=>$instalasi_id])?>
        <?=Html::activeHiddenInput($modelAdmisi, 'asuransipasien_id', ['class'=>'asuransipasien-id'])?>
        <?=Html::activeHiddenInput($modelAdmisi, 'bpjs_id', ['class'=>'bpjs-id'])?>
        <?=Html::hiddenInput('HiddenPenanggungJawab', 0, ['id' => 'HiddenPenanggungJawab']) ?>
        <input type="hidden" name="pasientitipan" id="pasienTitipanValue" value="0">
        <input type="hidden" name="pasienaps" id="pasienApsValue" value="0">
        <input type="hidden" name="kelaspelayanan_selected" id="kelasPelayananSelected" value="">
        <input type="hidden" name="kelaspelayanantagihan_selected" id="kelasPelayananTagihanSelected" value="">
    </div>
    <div class="row">
        <div class="col-md-12">
            <br>
            <div id="tableKarcisWrapper" class="table-responsive"></div>
        </div>
    </div>
</div>

<?= Yii::$app->controller->renderPartial('/daftar/partial/component/form-pj',[
    'form' => $form,
    'modelPj' => $modelPj,
    'data_lookup' => $data_lookup,
]); ?>
<!-- Form Asuransi -->
<?= Yii::$app->controller->renderPartial('/daftar/partial/component/form-asuransi',[
    'form' => $form,
    'modelAsuransi' => $modelAsuransi,
    'kelaspelayanan' => $kelaspelayanan,
]); ?>
<!-- End of Form Asuransi -->

<!-- Form BPJS -->
<?= Yii::$app->controller->renderPartial('/daftar/partial/component/form-bpjs',[
    'form' => $form,
    'modelBpjs' => $modelBpjs
]); ?>
<!-- End of Form BPJS -->

<!-- Form MultiPayer -->
<?= Yii::$app->controller->renderPartial('/daftar/partial/component/multi-payer/_form-asuransi-first-payer',[
    'form' => $form,
    'multiPayer' => $multiPayer,
    'kelaspelayanan' => $kelaspelayanan,
]); ?>

<?= Yii::$app->controller->renderPartial('/daftar/partial/component/multi-payer/_form-asuransi-second-payer',[
    'form' => $form,
    'multiPayer' => $multiPayer,
    'kelaspelayanan' => $kelaspelayanan,
]); ?>
<!-- End of MultiPayer -->

<?php ActiveForm::end(); ?>


<div id="modalTempatTidur" class="modal fade in" data-backdrop="static">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5 class="modal-title">Tempat Tidur</h5>
            </div>
            <div class="modal-body">
                <div class="panel-button">
                    <div class="form-group titipan-aps" style="display: none;">
                        <input type="checkbox" name="kamarTitipan" id="kamarTitipanCheck" value="0">
                        <label for="kamar_titipan" style="font-weight: bold;font-size: 15px;">Kamar Titipan</label>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="isAps" id="isApsCheck" value="0">
                        <label for="is_aps" style="font-weight: bold;font-size: 15px;">APS</label>
                    </div><br>
                    <div class="row" id="filterHeader">
                    </div>
                </div>
                <hr>
                <div class="row table-responsive">
                    <div id="tableKamarWrapper" class="table-scroll">
                        <table class="table table-striped table-condensed table-hover table-pilih-kamar" style="width:100%" id="tableKamar">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="80">No</th>
                                    <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                                    <th><?=\Yii::t("fe", "Kamar");?></th>
                                    <th><?=\Yii::t("fe", "Kelas");?></th>
                                    <th><?=\Yii::t("fe", "No Tempat Tidur");?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div id="tableKamarTitipanWrapper" class="table-scroll" style="display:none;">
                        <table class="table table-striped table-condensed table-hover table-pilih-kamar" style="width:100%" id="tableKamarTitipan">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="80">No</th>
                                    <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                                    <th><?=\Yii::t("fe", "Kamar");?></th>
                                    <th><?=\Yii::t("fe", "Kelas");?></th>
                                    <th><?=\Yii::t("fe", "No Tempat Tidur");?></th>
                                    <th><?=\Yii::t("fe", "Harga");?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">Data tidak tersedia</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="tableKamarApsWrapper" class="table-scroll" style="display:none;">
                        <table class="table table-striped table-condensed table-hover table-pilih-kamar" style="width:100%" id="tableKamarAps">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="80">No</th>
                                    <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                                    <th><?=\Yii::t("fe", "Kamar");?></th>
                                    <th><?=\Yii::t("fe", "Kelas");?></th>
                                    <th><?=\Yii::t("fe", "No Tempat Tidur");?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">Data tidak tersedia</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalTempatTidurTitipan" class="modal fade in" data-backdrop="static">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5 class="modal-title">Kelas Tagihan</h5>
            </div>
            <div class="modal-body">
                <div class="panel-button">
                    <div class="row" id="filterHeaderTitipan"></div>
                </div>
                <hr>
                <div class="row table-responsive">
                    <div id="tableTitipanWrapper" class="table-scroll" style="display:none;">
                        <table class="table table-striped table-condensed table-hover table-pilih-kamar" style="width:100%" id="tableTitipan">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="80">No</th>
                                    <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                                    <th><?=\Yii::t("fe", "Kamar");?></th>
                                    <th><?=\Yii::t("fe", "Kelas");?></th>
                                    <th><?=\Yii::t("fe", "Harga");?></th>
                                    <th><?=\Yii::t("fe", "Aksi");?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">Data tidak tersedia</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerJs('
        const dataPenyakit = ' . json_encode($jeniskasus) .  '
        const dataKasus = ' . json_encode($kelaspelayanan) .  '
        const instalasiId = ' . $instalasi_id .  '
    ', View::POS_END)
?>
