<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use kartik\widgets\DepDrop;
    use app\components\DocoConstants;
    use yii\helpers\ArrayHelper;
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?php
        $form = ActiveForm::begin([
            'id' => 'approval-form',
            'type' => ActiveForm::TYPE_VERTICAL,
            'enableAjaxValidation'=>false, 
            'enableClientValidation'=>false,
            'action' => '/pendaftaran/reservasi-mcu/setujui',
            'formConfig' => [
                'labelSpan' => 4, 
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
        ]);
    ?>
    <div class="form-group">
        <div class="col-sm-6">
            <?php
                $title_karcis = $data_lookup['title_pendaftaran'][0]['lookup_value'];

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
                    'data-urutan' => 1
                ]);
            ?>

            <!-- <?php
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
            ?> -->

            <?php
                echo $form->field($modelKunjungan, 'kelaspelayanan_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->widget(DepDrop::classname(), [
                    'name' => 'jenis_kasus_penyakit_id',
                    'options' => [
                        'disabled' => true,
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
                        'class' => 'form-control select2',
                    ],
                    'pluginOptions' => [
                        'depends' => ['ruangan_id'],
                        'placeholder' => Yii::t('fe', '-- Pilih --'),
                        'url' => Url::to(['daftar/get-dokter?param=penunjang'])
                    ]
                ]);
            ?>
            <!-- <?php if (isset($is_nourut) && $is_nourut) { ?>
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
                    'params' => ['ruangan_id']
                ]
            ]) ?>
            <?php } ?> -->
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
            <!-- <?= $form->field($modelKunjungan, 'is_pj', [
                    'options' => [
                                'tag' => false,
                            ],
                ])->checkbox([
                    'label' => 'Penanggung Jawab',
                    'value' => 1,
                    'class' => 'styled action-checked',
                ])->label(false); ?> -->
        </div>
        <!-- <div class="col-sm-12">
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
        </div> -->
    </div>
    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save btn-save-approval']); ?>
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    var listTarif = [];
    var totalTarif;
    var title_karcis = "<?= $title_karcis ?>";
    var ruangan_mcu = "<?= $default_ruangan ?>";

    $(document).ready(function() {
        $('#ruangan_id').val(ruangan_mcu).trigger('change');
        $('#ruangan_id').trigger('depdrop:change');
    });
    $(function () {
        $('.doco-number').trigger('change')
    })

    $('.pickadate').pickadate({
        format: 'dd mmm, yyyy',
        selectMonths: true,
        selectYears: 99,
        max: true,
        formatSubmit: 'yyyy-mm-dd',
    });
    $("#datetime").AnyTime_noPicker();
    $("#datetime").AnyTime_picker({
        format: "%d-%m-%Y %H:%i",
    });
    $("#datetime").val(function () {
        var d = new Date();
        return ("0" + d.getDate()).slice(-2) + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + d.getFullYear() + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2);
    });

    $('#kunjunganform-kelaspelayanan_id').on('depdrop:afterChange', function (event, id, value) {
        listTarif = [];

        const bindCheckboxWithSpace = (parentElement) => {
            parentElement.find('input[type="checkbox"]').on('keyup', (e) => {
                const { delegateTarget } = e
                if (e.keyCode === 32) {
                    $(delegateTarget).trigger('click')
                }
            })
        }

        const getKarcis = function () {
            var _params = {
                ruangan_id: $('.selectRuangan').val(),
                kp_id: $('.selectKp').val(),
                status: 0,
                penjamin_id: table.row(".selected").data().penjamin_id
            };
            _params = $.param(_params);
            var tbl;
            tbl = $('#tbl-karcis').docoTabel({
                filter: true,
                destroy: true,
                paging: false,
                sorting: [[0, 'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                ajax: baseUrl + 'pendaftaran/daftar-rajal/get-karcis?' + _params,
                initComplete: function (row, data) {
                    var api = this.api();
                    $.each(api.rows().data(), function (key, val) {
                        listTarif.push(val);
                    });
                    $('.check-aksi').on('click', function (event) {
                        var _this = $(this);
                        var isChecked = this.checked;
                        var rowData = api.rows($(this).closest("tr"));
                        var data = rowData.data()[0];
                        var allData = api.rows().data();
                        var cancelChecked = false;

                        if (data.is_konsultasi) {
                            if (isChecked) {
                                allData.each(function (value, index) {
                                    if (value.is_konsultasi == true && value.checked == true && value.daftartindakan_id != data.daftartindakan_id) {
                                        cancelChecked = true;
                                    }
                                });

                                if (cancelChecked) {
                                    _this.parent().removeClass("checked");
                                    _this.prop("checked", false);
                                    rowData.data()[0].checked = false;
                                    docoNotification("warning", "Peringatan!", "Tidak boleh memilih " + title_karcis + " konsultasi lebih dari satu.");
                                } else {
                                    rowData.data()[0].checked = true;
                                }
                            } else {
                                rowData.data()[0].checked = false;
                            }
                        }

                        if (cancelChecked === false) {
                            var key = _this.attr('data-key');
                            if (_this.is(':checked')) {
                                total_tarif = parseInt(totalTarif) + parseInt(listTarif[key].harga_tariftindakan);
                            } else {
                                total_tarif = totalTarif - (listTarif[key].harga_tariftindakan);
                            }
                            totalTarif = total_tarif;
                            $('.kolom-total-tarif').html('Rp. ' + docoHelper.convertToRupiah(total_tarif));
                        }
                    });
                },
                drawCallback: () => {
                    $('#tbl-karcis').find('.styled, .check-aksi input').uniform({
                        radioClass: 'choice'
                    });
                    bindCheckboxWithSpace($('#tbl-karcis'))
                },
                fnFooterCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    var intVal = function (i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
                    totalTarif = api
                        .column(5)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                    $(api.column(2).footer()).addClass('kolom-total-tarif').html('Rp. ' + docoHelper.convertToRupiah(totalTarif));
                },
                columns: [
                    {
                        title: 'No',
                        data: 'number',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: title_karcis,
                        data: 'daftartindakan_nama',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Konsultasi',
                        data: 'konsultasi',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Harga',
                        data: 'tmp_view',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: '',
                        data: 'aksi',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'tmp_total',
                        visible: false,
                        searchable: false,
                        orderable: false
                    }
                ],

            });
            $('.dataTables_filter').hide();
        }

        // getKarcis()
    });

    $(".btn-save-approval").on("click", function(e) {
        var dataTarif = [];
        var _data = $('#approval-form').serializeArray();

        // $.each($('.check-aksi'), function () {
        //     if ($(this).is(':checked')) {
        //         var _ke = $(this).attr('data-key');
        //         if (typeof listTarif[_ke] != 'undefined') {
        //             dataTarif.push(listTarif[_ke].daftartindakan_id);
        //         }
        //     }
        // });
        _data.push({
            name: 'list_tindakan',
            value: JSON.stringify(dataTarif)
        });

        var dataPasienMcu = table.rows(".selected").data();
        _data.push({
            name: 'listPasienMcu',
            value: JSON.stringify(dataPasienMcu)
        });

        const simpanData = function (dataPost, extra = {}) {
            var attr = {
                url: '/pendaftaran/reservasi-mcu/setujui',
                data: dataPost,
                success: function (data) {
                    $("#modal_backdrop").modal("toggle");
                    // table.draw();
                    var interval = setInterval(function() {
                        table.draw();
                        clearInterval(interval);
                    },2000);
                }, error(data) {
                    console.log(data);
                },
            };
            
            $('#approval-form').docoForm("submit", attr);
        }
        simpanData(_data)
    });
    // $("#kunjungan-form").docoForm("submit",{
    //     success : function(data) {
    //         table.draw();
    //         $("#modal_backdrop").modal("toggle");
    //     },
    // });
</script>