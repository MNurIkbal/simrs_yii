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

<div class="col-md-6">
    <div class="panel panel-white">
        <div class="panel-heading">
            <h5 class="panel-title"><?= Yii::t("fe", "Formulir Kunjungan") ?></h5>
        </div>
        <div class="panel-body">
            <?php
                // Tgl Pendaftaran
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

                echo $form->field($modelKunjungan, 'ruangan_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList($ruangan, [
                    'class' => 'select2 selectRuangan',
                    'id'=>'ruangan_id',
                    'prompt' => '-'
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
                        'url' => Url::to(['daftar/get-jenis-kasus-penyakit'])
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

            <?php if ($pemilihanDokter): ?>
                <?= $form->field($modelKunjungan, 'dokter_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                    ])->widget(DepDrop::classname(), [
                        'name' => 'dokter_id',
                        'options' => [
                            'disabled' => false,
                            'class' => 'form-control select2',
                        ],
                        'pluginOptions' => [
                            'depends' => ['ruangan_id'],
                            'placeholder' => Yii::t('fe', '-- Pilih --'),
                            'url' => Url::to(['daftar/get-dokter',/*'param'=>$param*/])
                        ]
                    ]);
                ?>
            <?php endif ?>

            <?php
                echo $form->field($modelKunjungan, 'carabayar_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList($carabayar, [
                    'class' => 'select2 selectCarabayar',
                    'id'=>'selectCarabayar',
                    'prompt' => '-',
                    'options'=> $carabayarOptions
                ]);

                echo $form->field($modelKunjungan, 'penjamin_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->widget(DepDrop::classname(), [
                    'name' => 'penjamin_id',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2 selectPenjamin',
                        'id' => 'penjamin_id',
                    ],
                    'pluginOptions' => [
                        'depends' => ['selectCarabayar'],
                        'placeholder' => Yii::t('fe', '-- Pilih --'),
                        'url' => Url::to(['daftar/get-penjamin'])
                    ],
                    'pluginEvents'=>[
                        "depdrop:afterChange"=>"function(event, id, value) {
                            if ($('#selectCarabayar').is(':focus')) {
                                $('#penjamin_id').focus();
                            }
                            getKarcis();
                        }",
                    ]
                ]);

                echo $form->field($modelKunjungan, 'asalrujukan_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($asal_rujukan, 'asalrujukan_id', 'asalrujukan_namalainnya'), [
                    'class' => 'select2 selectRujukan',
                    'prompt' => '-',
                    'id'=>'asalrujukan_id'
                ]);

                echo $form->field($modelKunjungan, 'keadaan_masuk', [
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
                ]);;

                echo $form->field($modelKunjungan, 'keterangan', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textArea([], [
                    'rows' => '6',
                ]);

                echo Html::activeHiddenInput($modelKunjungan, 'instalasi_id', ['value'=>$instalasi_id]);
                echo Html::activeHiddenInput($modelKunjungan, 'rujukan_id', ['class'=>'rujukan-id']);
                echo Html::activeHiddenInput($modelKunjungan, 'asuransipasien_id', ['class'=>'asuransipasien-id']);
                echo Html::activeHiddenInput($modelKunjungan, 'bpjs_id', ['class'=>'bpjs-id']);
                echo Html::hiddenInput('HiddenPenanggungJawab', 0, ['id' => 'HiddenPenanggungJawab']);
                echo Html::hiddenInput('hidden_no_bpjs', '', ['id' => 'hidden_no_bpjs', 'readonly' => 'readonly']);
            ?>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="panel panel-white panel-collapsed">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe','Penanggung jawab pasien')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse" class="toggle-list-pjawab"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-body">
            <?php
                echo $form->field($modelKunjungan, 'pj_pengantar', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($data_lookup['pengantar'], 'lookup_id', 'lookup_value'), [
                    'class' => 'select2',
                    'prompt' => '-'
                ]);

                echo $form->field($modelKunjungan, 'pj_nama', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput();

                echo $form->field($modelKunjungan, 'pj_jk', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->radioList(
                    ArrayHelper::map($data_lookup['jenis_kelamin'], 'lookup_id', 'lookup_value'),
                    ['inline'=>true]
                );

                $form->field($modelKunjungan, 'pj_jenis_identitas', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(
                    ArrayHelper::map($data_lookup['jenis_identitas'], 'lookup_id', 'lookup_value'), [
                    'class' => 'select2',
                    'prompt' => '-'
                ]);

                echo $form->field($modelKunjungan, 'pj_no_identitas', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput();

                echo $form->field($modelKunjungan, 'pj_hubungan', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($data_lookup['hubungan_keluarga'], 'lookup_id', 'lookup_value'), [
                    'class' => 'select2',
                    'prompt' => '-'
                ]);

                echo $form->field($modelKunjungan, 'pj_tempat_lahir', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput();

                echo $form->field($modelKunjungan, 'pj_tanggal_lahir', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ],
                    'addon' => [
                        'append' => [
                            ['content' => '<i class="fa fa-calendar "></i>'],
                        ],
                    ]
                ])->textInput(['class'=>'pickadate-w-month dateusia']);

                echo $form->field($modelKunjungan, 'pj_umur', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput(['class'=>'umurtext', 'readonly'=>true]);

                echo $form->field($modelKunjungan, 'pj_alamat', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textArea();

                echo $form->field($modelKunjungan, 'pj_no_telepon', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ],
                ])->textInput();

             ?>
        </div>
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
            ajax: baseUrl+"pendaftaran/daftar-ranap/get-data-sepuluh-terakhir?param="+$(".params-header").val(),
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