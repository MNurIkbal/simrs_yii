<?php 

use yii\helpers\Html;
?>

<div class="col-md-3">
    <div class="panel panel-default panel-bordered">
        <div class="panel-heading">
            <h6 class="panel-title"><?= Yii::t('fe', 'Detail Pembayaran') ?></h6>
            <div class="heading-elements">
                <ul class="icons-list"></ul>
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <label class="text-left control-label" style="font-size: 0.9vw;font-weight: bold;">
                        <strong><?= $model->getAttributeLabel('biaya_administrasi') ?></strong>
                    </label>
                    <div class="">
                        <?= $form->field($model, 'biaya_administrasi',[
                            'addon' => ['prepend' => ['content'=>'Rp.']],
                        ])->textInput([
                            'class' => 'form-control input-sm text-right doco-number',
                            'style' => 'font-size:1vw;font-weight: bold',
                            'autocomplete' => "off",
                            'id' => 'biaya_administrasi',
                            'readonly' => true
                        ])->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="text-left control-label" style="font-size: 0.9vw;font-weight: bold;">
                        <strong>Tagihan</strong>
                    </label>
                    <div class="">
                        <?php $model->total_tagihan = str_replace(".", ",", $model->total_tagihan) ?>
                        <?= $form->field($model, 'total_tagihan', [ 
                            'addon' => ['prepend' => ['content'=>'Rp.']],
                        ])->textInput([
                            'class' => 'form-control input-sm text-right doco-number',
                            'style' => 'font-size:1vw;font-weight: bold',
                            'autocomplete' => "off",
                            'id' => 'total_tagihan',
                            'readonly' => true
                        ])->label(false); ?>
                    </div>
                    <input type="hidden" id="total_tagihan_pembulatan">
                    <input type="hidden" id="total_tagihan_markup_pembulatan">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="text-left control-label" style="font-size: 0.9vw;font-weight: bold;">
                        <strong>Diskon Dokter</strong>
                    </label>
                    <div class="">
                        <?= $form->field($model, 'diskon_dokter',[
                            'addon' => [
                                'prepend' => ['content' => 'Rp.'],
                                'append' => [
                                    [
                                        'content' => Html::button('<i class="fa fa-plus"></i>', [
                                            'class'=>'btn btn-info', 
                                            'id' => 'add-diskon-dokter', 
                                            'action' => '/kasir/pembayaran-tagihan/diskon-dokter?id='.$id.'&kelompok='.$kelompok,
                                            'data-toggle' => 'modal',
                                            'data-target' => '#modal_backdrop',
                                            'data-width' => '95%',
                                        ]),
                                        'asButton' => true
                                    ],
                                ],
                            ]
                        ])->textInput([
                            'class' => 'form-control input-sm text-right doco-number',
                            'style' => 'font-size:1vw;font-weight: bold',
                            'autocomplete' => "off",
                            'id' => 'diskon-dokter',
                            'readonly' => true
                        ])->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="text-left control-label" style="font-size: 0.9vw;font-weight: bold;">
                        <strong>Diskon Total</strong>
                    </label>
                </div>
                <div class="col-md-6">
                    <div class="">
                        <?= $form->field($model, 'persen', [
                            'addon' => [
                                'prepend' => [
                                    'content' => Html::checkbox('persen_chk', false, [
                                        'id' => 'persen_chk', 
                                        'label' => '&nbsp;<i class="fa fa-percent" aria-hidden="true"></i>'
                                    ])
                                ],
                            ]
                        ])->textInput([
                            'class' => 'form-control input-sm text-right doco-number',
                            'style' => 'font-size:1vw;font-weight: bold',
                            'autocomplete' => "off",
                            'id' => 'persen',
                        ])->label(false); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="">
                        <?= $form->field($model, 'total_diskon', [])->textInput([
                            'class' => 'form-control input-sm text-right doco-number',
                            'style' => 'font-size:1vw;font-weight: bold',
                            'autocomplete' => "off",
                            'id' => 'total_diskon',
                        ])->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="text-left control-label" style="font-size: 0.9vw;font-weight: bold;">
                        <strong>Dijamin</strong>
                    </label>
                    <div class="">
                        <?= $form->field($model, 'subsidi_asuransi',[
                            'addon' => [
                                'prepend' => ['content' => 'Rp.'],
                                // 'append' => [
                                //     [
                                //         'content' => Html::button('<i class="fa fa-plus"></i>', [
                                //             'class'=>'btn btn-info', 
                                //             'id' => 'add-tagihan', 
                                //             'action' => '/kasir/pembayaran-tagihan/multi-penjamin',
                                //             'data-toggle' => 'modal',
                                //             'data-target' => '#modal_backdrop',
                                //             'data-width' => '80%',
                                //         ]),
                                //         'asButton' => true
                                //     ],
                                // ],
                            ]
                        ])->textInput([
                            'class' => 'form-control input-sm text-right doco-number',
                            'style' => 'font-size:1vw;font-weight: bold',
                            'autocomplete' => "off",
                            'id' => 'subsidi-asuransi',
                            'readonly' => true
                        ])->label(false); ?>
                    </div>
                    <input type="hidden" id="subsidi-asuransi_pembulatan">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="text-left control-label" style="font-size: 0.9vw;font-weight: bold;">
                        <strong>Balance RS</strong>
                    </label>
                    <div class="">
                        <?= $form->field($model, 'total_balance_rs',[
                            'addon' => ['prepend' => ['content'=>'Rp.']],
                        ])->textInput([
                            'class' => 'form-control input-sm text-right doco-number',
                            'style' => 'font-size:1vw;font-weight: bold',
                            'autocomplete' => "off",
                            'id' => 'total_balance_rs',
                            'readonly' => true
                        ])->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="text-left control-label" style="font-size: 0.9vw;font-weight: bold;">
                        <strong>Uang Muka</strong>
                    </label>
                    <div class="">
                        <?= $form->field($model, 'jumlah_uangmuka',[
                            'addon' => ['prepend' => ['content'=>'Rp.']],
                        ])->textInput([
                            'class' => 'form-control input-sm text-right doco-number',
                            'style' => 'font-size:1vw;font-weight: bold',
                            'autocomplete' => "off",
                            'id' => 'jumlah_uangmuka',
                            'readonly' => true
                        ])->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h3 style="font-weight: bold;">Ditagihkan ke Pasien</h3>
                    <p style="text-align: right;font-size: 25px;font-weight: bold;background-color: #ABEBC6">
                        <strong><span id="tagihan_pasien"></span></strong>
                    </p>
                    <input type="hidden" id="total-pembulatan">
                    <input type="hidden" id="pembulatan">
                    <input type="hidden" id="satuan_pembulatan">
                    <input type="hidden" id="diskon_adm_data">
                    <input type="hidden" id="is_diskon_adm_payer">
                    <input type="hidden" id="harga_admin">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="text-left control-label" style="font-size: 0.9vw;font-weight: bold;">
                        <strong>Tunai</strong>
                    </label>
                    <div class="">
                        <?= $form->field($model, 'total_dibayar', [
                            'addon' => [
                                'prepend' => ['content' => 'Rp.'],
                            ]
                        ])->textInput([
                            'class' => 'form-control input-sm text-right doco-number',
                            'style' => 'font-size:1vw;font-weight: bold',
                            'autocomplete' => "off",
                            'id' => 'total_dibayar',
                        ])->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="text-left control-label" style="font-size: 0.9vw;font-weight: bold;">
                        <strong>Non Tunai</strong>
                    </label>
                    <div class="">
                        <?= $form->field($model, 'total_nontunai', [
                            'addon' => [
                                'prepend' => ['content' => 'Rp.'],
                                'append' => [
                                    [
                                        'content' => Html::button('<i class="fa fa-plus"></i>', [
                                            'class'=>'btn btn-info', 
                                            'id' => 'add-dibayar',
                                            'action' => '/kasir/pembayaran-tagihan/multi-pembayaran/',
                                            'data-toggle' => 'modal',
                                            'data-target' => '#modal_backdrop',
                                            'data-width' => '98%',
                                        ]),
                                        'asButton' => true
                                    ],
                                ],
                            ]
                        ])->textInput([
                            'class' => 'form-control input-sm text-right doco-number',
                            'style' => 'font-size:1vw;font-weight: bold',
                            'autocomplete' => "off",
                            'readonly' => true,
                            'id' => 'total_nontunai',
                        ])->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="text-left control-label" style="font-size: 0.9vw;font-weight: bold;">
                        <strong>Piutang</strong>
                    </label>
                    <div class="">
                        <?= $form->field($model, 'total_piutang',[
                            'addon' => ['prepend' => ['content'=>'Rp.']],
                        ])->textInput([
                            'class' => 'form-control input-sm text-right doco-number',
                            'autocomplete' => "off",
                            'id' => 'total_piutang',
                            'style' => 'font-size:1vw;font-weight: bold',
                            'readonly' => true,
                        ])->label(false); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label class="text-left control-label" style="font-size: 0.9vw;font-weight: bold;">
                        <strong>Sisa</strong>
                    </label>
                    <div class="">
                        <?= $form->field($model, 'total_sisa_piutang',[
                            'addon' => ['prepend' => ['content'=>'Rp.']],
                        ])->textInput([
                            'class' => 'form-control input-sm text-right doco-number',
                            'autocomplete' => "off",
                            'id' => 'total_sisa_piutang',
                            'style' => 'font-size:1vw;font-weight: bold',
                            'readonly' => true,
                        ])->label(false); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
