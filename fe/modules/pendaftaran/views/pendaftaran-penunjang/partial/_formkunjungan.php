<?php
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
 ?>
<?php 
$date = date('H' , strtotime('NOW'));
?>
<?php
$form = ActiveForm::begin([
    'id' => 'ranap-form',
    'type' => ActiveForm::TYPE_VERTICAL,
    'enableClientValidation'=>false,
    'enableAjaxValidation'=>false,
    'formConfig' => [
        'showErrors' => true, 
        'labelSpan' => 4, 
        'deviceSize' => ActiveForm::SIZE_SMALL
    ]
]);
?>


<!-- Form BPJS -->
<?= Yii::$app->controller->renderPartial('/pendaftaran-rajal/partial/component/form-bpjs',[
    'form' => $form,
    'modelBpjs' => $modelBpjs
]); ?>
<!-- End of Form BPJS -->

<!-- Form Asuransi -->
<?= Yii::$app->controller->renderPartial('/pendaftaran-rajal/partial/component/form-asuransi',[
    'form' => $form,
    'modelAsuransi' => $modelAsuransi,
    'kelaspelayanan' => $kelaspelayanan,
]); ?>
<!-- End of Form Asuransi -->


<!-- Form Penangung Jawab -->
<?= Yii::$app->controller->renderPartial('/pendaftaran-rajal/partial/component/form-pj',[
    'form' => $form,
    'modelPj' => $modelPj,
    'data_lookup' => $data_lookup,
]); ?>
<!-- End of Form Penangung Jawab -->

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
            
                echo $form->field($modelKunjungan, 'instalasi_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList($instalasi, [
                    'class' => 'select2 selectInstalasi',
                    'id'=>'instalasi_id',
                    'prompt' => '-'
                ])->label(Yii::t('fe', 'Penunjang Medis'));
                echo $form->field($modelKunjungan, 'styrujukaninstalasi_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList($styrujukandari, [
                    'class' => 'select2 selectRujukanDari',
                    'style' => 'text-transform: uppercase',
                    'id'=>'styrujukandari_id',
                    'prompt' => '-'
                ])->label(Yii::t('fe', 'Rujukan Dari'));
                echo $form->field($modelKunjungan, 'ruangan_id', [
                    'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                    ]
                ])->widget(DepDrop::classname(), [
                    'data'=>[],
                    'options'=>['class'=>'select2 selectRuangan', 'id'=>'ruangan_id'],
                    'pluginOptions'=>[
                        'class'=>'select2',
                        'depends'=>['styrujukandari_id'],
                        'placeholder'=>'',
                        'url'=>Url::to(['get-ruangan'])
                    ]
                ])->label(Yii::t('fe', 'Ruangan/Klinik'));
            ?>

            <div class="hidden">
            <?php
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
            </div>
            <?= 
                $form->field($modelKunjungan, 'dokterpengirim_id',[
                   'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                   ]
                ])->dropDownList([], [
                    'prompt' => '-- Pilih --',
                    'placeholder' => Yii::t('fe','Dokter Pengirim')
                ]);
            ?>

            <?= $form->field($modelKunjungan, 'pegawai_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
                ])->widget(DepDrop::classname(), [
                    'name' => 'pegawai_id',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2',
                    ],
                    'pluginOptions' => [
                        'depends' => ['instalasi_id'],
                        'placeholder' => Yii::t('fe', '-- Pilih --'),
                        'url' => Url::to(['daftar/get-dokter?param=penunjang&is_instalasi=true'])
                    ]
                ]);
            ?>

            <?= $form->field($modelKunjungan, 'dokter_pengganti_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
                ])->widget(DepDrop::classname(), [
                    'name' => 'dokter_pengganti_id',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2',
                    ],
                    'pluginOptions' => [
                        'depends' => ['ruangan_id'],
                        'placeholder' => Yii::t('fe', '-- Pilih --'),
                        'url' => Url::to(['daftar/get-dokter'])
                    ]
                ]);
            ?>
        </div>
        <div class="col-sm-6">
            <?php
                // $modelKunjungan->keadaan_masuk = 159;
                /*echo $form->field($modelKunjungan, 'keadaan_masuk', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($data_lookup['keadaan_masuk'], 'lookup_id', 'lookup_value'), [
                    'class' => 'select2',
                    'prompt' => '-'
                ]);;

                echo $form->field($modelKunjungan, 'transportasi', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($data_lookup['transportasi'], 'lookup_id', 'lookup_value'), [
                    'class' => 'select2',
                    'prompt' => '-'
                ]);*/

                echo $form->field($modelKunjungan, 'keterangan', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textArea([], [
                    'rows' => '6',
                ]);

                if (isset($is_limit_tagihan) && $is_limit_tagihan) {
                    echo $form->field($modelKunjungan, 'limit_tagihan', [
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
                    ]);
                }
            ?>

                
            <div class="hidden">
            <?= $form->field($modelKunjungan, 'is_skd', [
                    'options' => [
                                'tag' => false,
                            ],
                ])->checkbox([
                    'label' => 'Surat Keterangan Dokter',
                    'value' => 1,
                    'id' => 'surat-keterangan-dokter',
                    'class' => 'styled action-checked',
                ])->label(false); ?>
            <?= $form->field($modelKunjungan, 'is_pj', [
                    'options' => [
                                'tag' => false,
                            ],
                ])->checkbox([
                    'label' => 'Penanggung Jawab',
                    'value' => 1,
                    'id' => 'kunjunganform-is_pj',
                    'class' => 'styled action-checked',
                ])->label(false); ?>
            </div>

            <?php 
                if((int) $date >= 14) {
                    $modelKunjungan->waktu_kunjungan = 2;
                } else {
                    $modelKunjungan->waktu_kunjungan = 1;
                }
            ?>
            <?= $form->field($modelKunjungan, 'waktu_kunjungan')->radioList(
                [
                    '1'=> Yii::t('fe', 'Pagi'),
                    '2'=> Yii::t('fe', 'Sore'),
                ],
                [
                    'id'=>'waktu_kunjungan',
                    'inline' => true
                ]
            );
            ?>

            <?= $form->field($modelKunjungan, 'status_kunjungan')->radioList(
                [
                    '180'=> Yii::t('fe', 'Kunjungan Baru'),
                    '181'=> Yii::t('fe', 'Kunjungan Ulang'),
                ],
                [
                    'id'=>'status_kunjungan',
                    'inline' => true,
                    'item' => function($index, $label, $name, $checked, $value) {
                        $return = '<label class="radio-inlineo">';
                            $return .= '<input type="radio" id="status_kunjungan_'.$value.'" name="' . $name . '" value="' . $value . '" class="styled">';
                            $return .= '<i></i>';
                            $return .= '<span style="margin-left:5px;">' . $label . '</span>';
                        $return .= '</label>';

                        return $return;
                    }
                ]
            );
            ?>

            <div class="hidden diagnosa">
                <?= 
                    $form->field($modelKunjungan, 'diagnosa', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-4',
                            'wrapper' => 'col-md-7'
                        ]
                    ])->textArea(); 
                ?>
            </div>

        </div>
        <!-- <div class="col-sm-12">
            <br>
            <fieldset>
                <legend class="title-rencana"><?=Yii::t('fe','Rencana Pemeriksaan')?></legend>
                <div class="row">
                    <div class="col-md-6">
                        <div style="padding: 10px">
                            <button type="button" class="btn-pemeriksaan-tambah btn btn-info btn-labeled btn-xs btn-toolbar" action="/pendaftaran/daftar/modal-pemeriksaan-lab" data-width="90%"  data-toggle="modal" data-target="#modal_backdrop" data-options="click"><b><i class="fa fa-plus"></i></b><?=Yii::t('fe', 'Tambah')?></button>
                            <button type="button" class="btn-pemeriksaan-clear btn btn-info btn-labeled btn-xs btn-toolbar" data-options="click"><b><i class="fa fa-trash"></i></b><?=Yii::t('fe', 'Kosongkan')?></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="table-pemeriksaan" class="table datatable-basic table-striped table-hover dataTable no-footer table-pemeriksaan">
                            <thead>
                                <tr class="bg-inverse">
                                    <th><?=Yii::t('fe', 'No')?></th>
                                    <th><?=Yii::t('fe', 'Jenis pemeriksaan')?></th>
                                    <th><?=Yii::t('fe', 'Nama pemeriksaan')?></th>
                                    <th><?=Yii::t('fe', 'Qty')?></th>
                                    <th><?=Yii::t('fe', 'Cyto')?></th>
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
                                    <td colspan="5"><b>Total</b></td>
                                    <td colspan="2" class="total-pemeriksaan"><b>Rp. 0</b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
            </fieldset>
        </div>
        <div class="col-sm-12">
            <table id="tbl-karcis" class="table table-striped table-condensed table-hover table-karcis" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="10%">No</th>
                        <th class="karcis-title"><?=\Yii::t("fe", "Karcis");?></th>
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
        </div> -->
    </div>
</div>


<?php ActiveForm::end(); ?>

<?php
$this->registerJs('
    var tableDaftarTerakhir;

    // Event Ready
    $(document).ready(function() {
        tableDaftarTerakhir = $("#table-daftar-terakhir").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets: 0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            oLanguage: {
                sLengthMenu: "'.(\Yii::t('fe', 'dt_length_menu')).'",
                sZeroRecords: "'.(\Yii::t('fe', 'dt_zero_records')).'",
                sEmptyTable: "'.(\Yii::t('fe', 'dt_empty_table')).'",
                sInfoFiltered: "'.(\Yii::t('fe', 'dt_info_filtered')).'",
                sInfoEmpty: "'.(\Yii::t('fe', 'dt_info_empty')).'",
                sInfo: "'.(\Yii::t('fe', 'dt_info')).'",
                oPaginate: {
                    sFirst: "'.(\Yii::t('fe', 'dt_first_page')).'",
                    sPrevious: "'.(\Yii::t('fe', 'dt_previous_page')).'",
                    sNext: "'.(\Yii::t('fe', 'dt_next_page')).'",
                    sLast: "'.(\Yii::t('fe', 'dt_last_page')).'"
                }
            },
            ajax: baseUrl+"pendaftaran/daftar-rajal/get-data-sepuluh-terakhir?param="+$(".params-header").val(),
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.Yii::t('fe', 'Tanggal Pendaftaran').'",  data: "tgl_pendaftaran"},
                {title: "'.Yii::t('fe', 'No Pendaftaran').'",  data: "no_pendaftaran"},
                {title: "'.Yii::t('fe', 'No Rekam Medik').'", data: "no_rekam_medik"},
                {title: "'.Yii::t('fe', 'Nama Pasien').'", data: "nama_pasien"},
                {title: "'.Yii::t('fe', 'Umur').'", data: "umur"},
                {title: "'.Yii::t('fe', 'Jenis Kelamin').'", data: "jenis_kelamin"},
                {title: "'.Yii::t('fe', 'Poliklinik').'", data: "ruangan_nama"},
                {title: "'.Yii::t('fe', 'Dokter').'", data: "nama_pegawai"},
                {title: "'.Yii::t('fe', 'Cara Bayar').'", data: "carabayar_nama"},
                {title: "'.Yii::t('fe', 'Penjamin').'", data: "penjamin_nama"},
                {
                    data: "primaryPasien",
                    searchable: false,
                    orderable: false,
                    visible: false,
                },
                {
                    data: "primaryPendaftaran",
                    searchable: false,
                    orderable: false,
                    visible: false,
                },

            ]
        });

        $(".dataTables_filter").hide();
        $(".dataTables_length").hide();
        $(".dataTables_info").hide();
        $(".dataTables_paginate").hide();
    });

    $(document).on("click", "#table-daftar-terakhir tbody tr", function () {

        $("#btn-print-sep").attr("disabled", true);
        var bpjs_id = null;
        try {
            bpjs_id = tableDaftarTerakhir.row(".selected").data().bpjs_id ? tableDaftarTerakhir.row(".selected").data().bpjs_id : null;
        } catch (e) {
            bpjs_id = null;
        }

        if (bpjs_id != null ) {
            $("#btn-print-sep").attr("disabled", false);
        }
    });

    $(document).on("click",".btn-print-pasien-terakhir",function(e){
        e.preventDefault();
        var tableData = tableDaftarTerakhir.row(".selected").data();
        //console.log(tableData);
        if (typeof tableData !== "undefined") {
            if("primaryPasien" in tableData && "primaryPendaftaran" in tableData){
                var target = $(this).attr("data-target");
                var primaryPendaftaran = tableData.primaryPendaftaran;
                var primaryPasien = tableData.primaryPasien;
                var carabayar_nama = tableData.carabayar_nama;

                window.open(target+"?pasien_id="+primaryPasien+"&pendaftaran_id="+primaryPendaftaran);
            }else{
                docoNotification("warning", "Terjadi Kesalahan", "Primary Tidak Didefinisikan");
            }
        }else{
            docoNotification("warning", "Terjadi Kesalahan", "Belum ada data yang dipilih!");
        }
    });

', View::POS_END, 'js-daftar-terakhir');

?>