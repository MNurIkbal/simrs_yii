<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use kartik\widgets\DepDrop;
    use app\components\DocoConstants;
?>
<div id="form-input-kunjugan" style="display: none;">
    <div class="form-group" id="form-kunjungan-content">
        <div class="col-sm-6">
            <?php
                echo $form->field($modelKunjungan, 'tgl_pendaftaran', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ],
                    'addon' => ['append' => [
                            'content' => '<i class="fa fa-calendar"></i>'
                        ]
                    ]
                ])->textInput([
                        'placeholder' => $modelKunjungan->getAttributeLabel('tgl_pendaftaran'),
                        'class' => 'form-control input-sm pickadate',
                        'id' => 'datetime',
                        'autocomplete' => "off",
                        'readonly' => true
                ]);
            ?>
            <?php
                echo $form->field($modelKunjungan, 'ruangan_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList($ruangan, [
                    'class' => 'select2 selectRuangan',
                    'id'=>'ruangan_id',
                    'prompt' => '-',
                    'data-urutan' => 1,
                    'options' => $optionsRuangan
                ]);

                echo $form->field($modelKunjungan, 'jeniskasuspenyakit_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->widget(DepDrop::classname(), [
                    'name' => 'jeniskasuspenyakit_id',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2',
                    ],
                    'pluginOptions' => [
                        'depends' => ['ruangan_id'],
                        'placeholder' => Yii::t('fe', '-- Pilih --'),
                        'url' => Url::to(['daftar/get-jenis-kasus-penyakit']),
                        'allowClear' => true,
                    ],
                    'pluginEvents'=>[
                        "depdrop:afterChange"=>"function(event, id, value) {
                            if ($('#selectCarabayar').is(':focus')) {
                                $('#kunjunganform-jeniskasuspenyakit_id').focus();
                            }
                        }",
                    ]
                ]);
                echo $form->field($modelKunjungan, 'kelaspelayanan_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->widget(DepDrop::classname(), [
                    'name' => 'jenis_kasus_penyakit_id',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2 selectKp',
                    ],
                    'pluginOptions' => [
                        'depends' => ['ruangan_id'],
                        'placeholder' => Yii::t('fe', '-- Pilih --'),
                        'url' => Url::to(['daftar/get-kelas-pelayanan'])
                    ]
                ]);
            ?>
            <?= $form->field($modelKunjungan, 'dokter_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
                ])->widget(DepDrop::classname(), [
                    'name' => 'dokter_id',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2 selectDokter',
                    ],
                    'pluginOptions' => [
                        'depends' => ['ruangan_id'],
                        'placeholder' => Yii::t('fe', '-- Pilih --'),
                        'url' => Url::to(['daftar/get-dokter'])
                    ]
                ]);
            ?>
            <?php if (isset($is_nourut) && $is_nourut) { ?>
                <?php if($ruanganId != DocoConstants::WS_MCU) { ?>
            <?= $form->field($modelKunjungan, 'nomor_urut', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->widget(DepDrop::classname(), [
                'name' => 'nomor_urut',
                'options' => [
                    'class' => 'form-control select2',
                ],
                'pluginOptions' => [
                    'depends' => ['kunjunganform-dokter_id'],
                    'placeholder' => Yii::t('fe', '-- Pilih --'),
                    'url' => Url::to(['daftar/get-nomor-urut']),
                    'params' => ['ruangan_id','selectCarabayar']
                ]
            ]) ?>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="col-sm-6">
            <?php
                echo $form->field($modelKunjungan, 'keadaan_masuk', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($data_lookup['keadaan_masuk'], 'lookup_id', 'lookup_value'), [
                    'class' => 'select2',
                    'prompt' => '-'
                ]);

                echo $form->field($modelKunjungan, 'transportasi', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($data_lookup['transportasi'], 'lookup_id', 'lookup_value'), [
                    'class' => 'select2',
                    'prompt' => '-'
                ]);

                echo $form->field($modelKunjungan, 'keterangan', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textArea([], [
                    'rows' => '6',
                ]);
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
                    'class' => 'select2 selectReferal',
                    'id'=>'referal',
                    'prompt' => '-',
                    'data-urutan' => 1,
                ]);
                echo Html::hiddenInput('konfig_referral_required', $modelKunjungan->konfig_referral_required);
            endif;
            ?>
        </div>
        <?php if($ruanganId == DocoConstants::WS_MCU) : ?>
        <?= Html::hiddenInput('instalasi_id', $instalasi_id, ['class'=>'selectInstalasi']); ?>
        <div class="col-sm-12">
            <br>
            <fieldset>
                <legend class="title-rencana"><?=Yii::t('fe','Rencana Pemeriksaan')?></legend>
                <div class="row">
                    <div class="col-md-6">
                        <div style="padding: 10px">
                            <button type="button" class="btn-pemeriksaan-tambah btn btn-info btn-labeled btn-xs btn-toolbar" action="/pendaftaran/daftar/modal-pemeriksaan-lab?instalasi_id=<?=$instalasi_id?>" data-width="60%"  data-toggle="modal" data-target="#modal_backdrop" data-options="click"><b><i class="fa fa-plus"></i></b><?=Yii::t('fe', 'Tambah')?></button>
                            <button type="button" class="btn-pemeriksaan-clear btn btn-info btn-labeled btn-xs btn-toolbar" data-options="click"><b><i class="fa fa-trash"></i></b><?=Yii::t('fe', 'Kosongkan')?></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="table-paket-mcu" class="table datatable-basic table-striped table-hover dataTable no-footer table-paket-mcu">
                            <thead>
                                <tr class="bg-inverse">
                                    <th><?=Yii::t('fe', 'No')?></th>
                                    <th><?=Yii::t('fe', 'Nama Paket / Item')?></th>
                                    <th><?=Yii::t('fe', 'Qty')?></th>
                                    <th><?=Yii::t('fe', 'Harga')?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody> 
                                <tr class="row-default">
                                    <td colspan="7" class="text-center">Belum ada data yang ditambahkan</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"><b>Total</b></td>
                                    <td colspan="2" class="total-paket-mcu"><b>Rp. 0</b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
            </fieldset>
        </div>
        <?php endif; ?>
        <div class="col-sm-12">
            <table id="tbl-karcis" class="table table-striped table-condensed table-hover table-karcis" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="10%">No</th>
                        <th class="karcis-title"><?=\Yii::t("fe", $data_lookup['title_pendaftaran'][0]['lookup_value']);?></th>
                        <th class="konsultasi-title"><?=\Yii::t("fe", "Konsultasi");?></th>
                        <th class="harga-title"><?=\Yii::t("fe", "Harga");?></th>
                        <th><?=\Yii::t("fe", "Aksi");?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center"><?=Yii::t('fe','Data tidak tersedia')?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th style="text-align:right">Total:</th>
                        <th colspan="4"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

