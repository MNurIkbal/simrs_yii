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
    use kartik\widgets\FileInput;
    use app\components\DocoConstants;
    use kartik\typeahead\Typeahead;
?>

<style>
    .dt-pasien {
        margin-top: 2px !important;
        margin-bottom: 4px !important;
        text-align: left !important;
    }
    .dd-pasien {
        margin-top: 2px !important;
        margin-bottom: 4px !important;
        padding-top: 2px;
    }
    .input-group-btn.dropdown-list {
        min-width:75px !important;
        text-align:left !important;
    }
    .input-group {
        width: 100%;
    }
    .panel-info-btn {
        padding: 5px 13px 0px 13px;
    }
    
    .utama {
        margin-left: -10px;
    }

    .add-1 {
        margin-top: 121px; margin-left: -100px;
    }

    .add-2 {
        margin-top: 121px; margin-left: -25px;
    }
</style>

<?php
    $form = ActiveForm::begin([
        'id' => 'tipe-pasien',
        'type' => ActiveForm::TYPE_VERTICAL,
        'enableClientValidation'=>false,
        'enableAjaxValidation'=>false,
        // 'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]);

?>
<div class="row">
    <div class="form-group">
        <div class="row">
            <div class="col-md-12"> 
                <button type="button" class="btn eligible-peserta btn-info btn-labeled btn-xs" style="display: none;"  data-toggle="modal" data-target="#modal_backdrop" data-width="90%" ><b><i class="fa fa-eye"></i></b></button></div>
            </div>
        </div>
        <div class="col-md-3" id="form-parent-info" style="display: none;">
            <div class="content-group">
                <div class="bg-indigo-300 border-radius-top text-right panel-info-btn">
                    <?php
                        echo Html::button('<b><i class="fa fa-pencil"></i></b>'.Yii::t('fe', 'Edit Pasien'),[
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'id'=>'btn-edit-info-pasien',
                            'data-target' => Url::home().Yii::$app->controller->module->id.'/informasi-pencarian-pasien/update?id=',
                        ]);
                    ?>
                </div>
                <div class="panel-body bg-indigo-300 text-center" style="color: #000000!important;font-size: 14px;">
                    <div class="content-group-sm">
                        <h6 class="text-semibold no-margin-bottom nama-pasien">
                            Setyabudi Dwisandi Arifin
                        </h6>

                        <span class="display-block rm-pasien">0000089</span>
                    </div>

                    <a href="#" class="display-inline-block content-group-sm">
                        <img src="/media/img/icon-app/default.jpg" class="img-circle img-responsive" 
                        alt="" style="width: 110px; height: 110px;">
                    </a>
                </div>
                <ul class="nav nav-tabs nav-justified no-margin no-border-radius bg-teal-400 border-top border-top-teal-300">
                    <li class="active">
                        <a href="#info" class="text-size-small text-uppercase text-semibold" data-toggle="tab" aria-expanded="true">
                            Info
                        </a>
                    </li>

                    <li class="">
                        <a href="#kunjungan" id="informasi-data-kunjungan"class="text-size-small text-uppercase text-semibold" data-toggle="tab" aria-expanded="false">
                            Kunjungan
                        </a>
                    </li>
                    <li class="" id="ket-bpjs" style="display: none;">
                        <a href="#bpjs" class="text-size-small text-uppercase text-semibold" data-toggle="tab" aria-expanded="false">
                            Info BPJS
                        </a>
                    </li>
                </ul>
                <div class="tab-content panel-body" style="border: 1px solid #dddddd;background:#f2fdf7;">
                    <div class="tab-pane fade active in" id="info">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-md-6">Jenis Kelamin</div>
                                <div class="col-md-6 kelamin-pasien">:&nbsp;-</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-md-6">Tempat lahir</div>
                                <div class="col-md-6 tempat-lahir-pasien">:&nbsp;-</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-md-6">Tanggal lahir</div>
                                <div class="col-md-6 tanggal-lahir-pasien">:&nbsp;-</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-md-6">Golongan Darah</div>
                                <div class="col-md-6 darah-pasien">:&nbsp;-</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-md-6">Nama Ibu</div>
                                <div class="col-md-6 ibu-pasien">:&nbsp;-</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-md-6">No Tlp</div>
                                <div class="col-md-6 tlp-pasien">:&nbsp;-</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-md-6">Alamat</div>
                                <div class="col-md-6 alamat-pasien">:&nbsp;-</div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade in" id="bpjs">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-md-6">NIK</div>
                                <div class="col-md-6" id="bpjsnew_detail_nik">:&nbsp;-</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-md-6">Tanggal Lahir</div>
                                <div class="col-md-6" id="bpjsnew_detail_tgl_lahir">:&nbsp;-</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-md-6">Jenis Peserta</div>
                                <div class="col-md-6" id="bpjsnew_detail_jenis_peserta">:&nbsp;-</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-md-6">Hak Kelas</div>
                                <div class="col-md-6" id="bpjsnew_detail_hak_kelas">:&nbsp;-</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-md-6">TMT/TAT</div>
                                <div class="col-md-6" id="bpjsnew_detail_tmt_tat">:&nbsp;-</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-md-6">Kode/Provinsi</div>
                                <div class="col-md-6" id="bpjsnew_detail_ppk_rujukan">:&nbsp;-</div>
                            </div>
                            <div class="col-sm-12">
                                <div class="col-md-6">Status Peserta</div>
                                <div class="col-md-6" id="bpjsnew_detail_status_peserta">:&nbsp;-</div>
                            </div>
                        </div>
                        <hr>
                        <div align="center">
                            <button type="button" class="btn btn-detail-bpjs btn-info btn-labeled btn-xs" action="<?= Url::home() ?>pendaftaran/daftar-igd/detail-history-bpjs?no_kartu=" data-toggle="modal" data-target="#modal_backdrop" data-width="90%"><b><i class="fa fa-eye"></i></b>History BPJS</button>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="kunjungan">
                        <div class="list-group" id="list-history" style="border: 1px solid #3c9a6630!important; border-radius:3px!important; padding:0px;margin-bottom:4px!important;">
                                <div class="form-group">
                                    <div class="col-sm-12 text-center">
                                        <h8 class="list-group-item-heading">
                                            <b><u class="history-pendaftaran-id">1002R0010719V000187</u></b>
                                        </h8>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <div class="col-sm-5">
                                            Instalasi
                                        </div>
                                        <div class="col-sm-7 history-instalasi">:</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="col-sm-5">
                                            Ruangan
                                        </div>
                                        <div class="col-sm-7 history-ruangan">:</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="col-sm-5">
                                            Dokter
                                        </div>
                                        <div class="col-sm-7 history-dokter">:</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="col-sm-5">
                                            Tgl. Masuk
                                        </div>
                                        <div class="col-sm-7 history-tgl-pendaftaran">:</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="col-sm-5">
                                            Tgl. Keluar
                                        </div>
                                        <div class="col-sm-7 history-keluar">:</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="col-sm-5">
                                            Cara Keluar
                                        </div>
                                        <div class="col-sm-7 history-cara-keluar">:</div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="col-sm-5">
                                            Penanggung Biaya
                                        </div>
                                        <div class="col-sm-7 history-penanggung-biaya">:</div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-group" id="info-piutang" style="display:none">
                <div class="panel panel-danger">
                    <div class="panel-heading text-uppercase text-semibold" style="text-align: center"><strong>Piutang Pasien</strong></div>
                    <div class="panel-body" style="text-align: center">Rp. <b id="val-piutang"></b></div>
                </div>
            </div>
            <div class="content-group" id="info-catatan" style="display:none">
                <div class="panel panel-danger">
                    <div class="panel-heading text-uppercase text-semibold" style="text-align:center;"><strong>Catatan Penting Pasien</strong></div>
                    <div class="panel-body" style="text-align:left;"><b id="val-catatan"></b></div>
                </div>
            </div>
        </div>
        <div class="col-md-12" id="form-parent">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">Form Pendaftaran</h6>
                </div>
            <div class="steps-basic">
                <h6>Tipe Pasien</h6>
                <fieldset>
                    <div class="row">
                        <div class="col-md-4">
                            <?php $form_multi = $form->field($tipePasien, 'is_multi_payer', [
                                'options' => [
                                    'tag' => false,
                                ],
                                'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-12'
                                ]
                            ])->checkbox([
                                'label' => @$support_multipayer ? 'Multi Payer' : '',
                                'value' => 1,
                                'class' => 'styled action-checked',
                            ])->label(false);
                            echo @$support_multipayer ? $form_multi : $form_multi->hiddenInput(); 
                            ?>
                            
                            <?= $form->field($tipePasien, 'is_bbl', [
                                    'options' => [
                                        'tag' => false,
                                    ],
                                ])->checkbox([
                                    'label' => 'Pendaftaran Bayi',
                                    'value' => 1,
                                    'class' => 'styled action-checked',
                                    'data-urutan' => 1
                                ]);
                            ?>
                            <?php
                                $dataOptions = [];
                                if (!empty($rujukRanap)) {
                                    $noRm = isset($rujukRanap['pendaftaran']['no_rekam_medik']) ? $rujukRanap['pendaftaran']['no_rekam_medik'] : null;
                                    $value = $noRm;
                                    $value .= ' / ' . (isset($rujukRanap['pendaftaran']['nama_pasien']) ? ($rujukRanap['pendaftaran']['nama_pasien']) : null);
                                    $value .= ' / ' . (isset($rujukRanap['pendaftaran']['tanggal_lahir']) ? ($rujukRanap['pendaftaran']['tanggal_lahir']) : null);
                                    $dataOptions[$noRm] = $value;
                                }
                                echo $form->field($tipePasien, 'no_rekam_medik', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-7'
                                    ],
                                ])->dropDownList($dataOptions, [
                                    'class' => 'select2',
                                    'prompt' => '-',
                                    'id' => 'no_rekam_medik',
                                    'placeholder' => Yii::t('fe','No rm').' / '.Yii::t('fe', 'Nama pasien'). ' / ' .Yii::t('fe', 'Tanggal lahir')
                                ]);
                            ?>
                            <div class="nextRow col-md-9 utama">
                                <?php
                                    echo Html::hiddenInput('pasien_id_hidden', '', ['id' => 'pasien_id_hidden']);
                                    echo $form->field($tipePasien, 'carabayar_id', [
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

                                    echo $form->field($tipePasien, 'penjamin_id', [
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-7'
                                        ]
                                    ])->dropDownList([], [
                                        'class' => 'select2 penjaminId',
                                        'id'=>'penjamin_id'
                                    ]);

                                    echo $form->field($tipePasien, 'asalrujukan_id', [
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-7'
                                        ]
                                    ])->dropDownList(ArrayHelper::map($asal_rujukan, 'asalrujukan_id', 'asalrujukan_nama'), [
                                        'class' => 'select2 selectRujukan',
                                        'prompt' => '-',
                                        'id'=>'asalrujukan_id'
                                    ]);

                                    echo $form->field($tipePasien, 'no_asuransi', [
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-7'
                                        ],
                                        'options' => [
                                            'class' => 'required'
                                        ],
                                        'addon' => ['append' => [
                                                'content' => '<i class="fa fa-refresh" id="refreshAsuransiIcon"></i>'
                                            ]
                                        ]
                                        ])->widget(Typeahead::classname(),[
                                            'pluginOptions' => [
                                                'highlight' => true,
                                                'minLength' => 3,
                                                'limit' => 10,
                                            ],
                                            'dataset' => [
                                                [
                                                    'limit' => 10,
                                                    'display' => 'value',
                                                    'delay' => 5000,
                                                    'remote' => [
                                                        'url' => Url::to(['/pendaftaran/end-point/get-data-asuransi']) . '?groupedByNoAsuransi=true&no_asuransi=',
                                                        'wildcard' => '%QUERY',
                                                        'replace' => new JsExpression('function (url, uriEncodedQuery) {
                                                            var _penjamin = $("#penjamin_id").val();
                                                            return url + uriEncodedQuery +"&penjamin_id=" + encodeURIComponent(_penjamin);
                                                        }')
                                                    ]
                                                ]
                                            ],
                                            'pluginEvents' => [
                                                "typeahead:selected" => "function(obj, item) {
                                                    $('#tipepasienform-no_asuransi').prop('readonly', true)
                                                    $('.field-tipepasienform-no_asuransi').find('#note-asuransi').html('Atas Nama : ' + item.namapemilikasuransi + '<br>Pasien Pengguna Asuransi : ' + item.pasien.nama_pasien + '<br> Nomor Rekam Medik Pasien : ' + item.pasien.no_rekam_medik)
                                                    $('input[name=\'selectedNoAsuransi\']').val(item.nokartuasuransi)
                                                    Object.assign(_formPendaftaran.tmpAsuransi, item)
                                                    _formPendaftaran.tipePasien.no_asuransi = item.nokartuasuransi
                                                    showLoader()
                                                    setTimeout(() => {
                                                        $('.field-tipepasienform-no_asuransi').find('#note-asuransi').show()
                                                        hideLoader()
                                                    }, 1000)
                                                }",
                                            ]
                                        ])->textInput([
                                                'placeholder' => $tipePasien->getAttributeLabel('no_asuransi'),
                                                'class' => 'form-control input-sm typeahead',
                                                'autocomplete' => "off"
                                        ])->hint('<span id="note-asuransi" style="color:green;"></span>');
                                ?>
                                <input type="text" name="selectedNoAsuransi" id="tipepasienform-nokartuasuransi" class="hidden">
                            </div>

                            <div id="form-bpjs" class="col-md-9 utama" style="display: none">
                                <?= $form->field($modelBpjs, 'jenis_rujukan',[
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-7'
                                        ]
                                    ])->radioList(
                                        [
                                            // '1'=> Yii::t('fe', 'Rujukan'),
                                            '2'=> Yii::t('fe', 'Rujukan Manual / IGD'),
                                        ],
                                        [
                                            'id'=>'jenis_rujukan',
                                            'inline' => true,
                                            'item' => function($index, $label, $name, $checked, $value) {
                                                $return = '<label class="radio-inlineo">';
                                                    $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" class="styled">';
                                                    $return .= '<i></i>';
                                                    $return .= '<span style="margin-left:5px;">' . $label . '</span>';
                                                $return .= '</label>';

                                                return $return;
                                            }
                                        ]
                                    );
                                ?>
                                <?= $form->field($modelBpjs, 'tanggal_sep', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-7'
                                    ],
                                    'inputOptions'=>['id'=>'tanggal_sep_1'],
                                    'addon' => [
                                        'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                                    ]
                                ]); ?>
                            
                                <div id="base-rujukan">
                                    <?php $modelBpjs->asal_rujukan = 1; ?>
                                    <?= $form->field($modelBpjs, 'asal_rujukan',[
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->dropDownList(
                                            [
                                                '1'=>Yii::t('fe', 'Faskes tingkat 1'),
                                                '2'=>Yii::t('fe', 'Faskes tingkat 2 (RS)'),
                                            ],
                                            [
                                                'id'=>'asal_rujukan_1',
                                                'class' => 'select2',
                                                'prompt'=>'— PILIH —',
                                            ]
                                        );
                                    ?>

                                    <?php
                                    echo $form->field($modelBpjs, 'no_rujukan_f', [
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-7'
                                        ],
                                        'inputOptions'=>['id'=>'no_rujukan'],
                                        'addon' => [
                                            'append' => [
                                                'content' => Html::a('<i class="fa fa-search"></i>',null, [
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#modal_pencarian_identitas',
                                                    'data-width' => '1000px',
                                                    'data-popup' => "tooltip",
                                                    'id' => 'btn-pencarian-identitas',
                                                    'action' =>'/pendaftaran/daftar/pencarian-identitas',
                                                    'title' => Yii::t("fe","Pencarian Identitas")
                                                ])
                                            ]
                                        ]
                                    ])->hint('<div class="text-danger err-no-rujukan"></div>');
                                    ?>
                                </div>

                                <div id="base-rujukan-manual" style="display:none;">
                                    <?php 
                                    $modelBpjs->jenis_pelayanan = 1;
                                    echo $form->field($modelBpjs, 'jenis_pelayanan',[
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->dropDownList(
                                            [
                                                // '2'=>Yii::t('fe', 'Rawat Jalan'),
                                                '1'=>Yii::t('fe', 'Rawat Inap'),
                                            ],
                                            [
                                                'id'=>'jenis_pelayanan',
                                                'class' => 'select2',
                                                'prompt'=>'— PILIH —',
                                            ]
                                        );
                                    
                                    $modelBpjs->jenis_kartu = 1;
                                    echo $form->field($modelBpjs, 'jenis_kartu',[
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-7'
                                        ]
                                    ])->radioList(
                                        [
                                            '1'=> Yii::t('fe', 'No BPJS'),
                                            '2'=> Yii::t('fe', 'NIK'),
                                        ],
                                        [
                                            'id' => 'jenis_kartu',
                                            'inline' => true,
                                            'item' => function($index, $label, $name, $checked, $value) {
                                                $return = '<label class="radio-inlineo">';
                                                    $return .= '<input type="radio" 
                                                            name="' . $name . '" 
                                                            value="' . $value . '" 
                                                            class="styled"'. ($checked ? 'checked' : '') .'>';
                                                    $return .= '<i></i>';
                                                    $return .= '<span style="margin-left:5px;">' . $label . '</span>';
                                                $return .= '</label>';

                                                return $return;
                                            }
                                        ]
                                    );

                                    echo $form->field($modelBpjs, 'no_kartu', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ],
                                        'inputOptions'=>['id'=>'no_kartu'],
                                    ])->hint('<div class="text-danger err-no-kartu"></div>');
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 nextRow add-1">
                            <div class="col-md-9 form-multi-carabayar" id="form-multi-carabayar" style="display: none;">
                                <!-- <hr> -->
                                <div class="form-group" id="form-multi-carabayar-content">
                                    <div class="row">
                                        <div class="default">
                                            <?php
                                            echo $form->field($multiPayer, 'add_carabayar_id_1', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'col-md-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->dropDownList($carabayar, [
                                                'class' => 'select2 selectCarabayar2',
                                                'id' => 'addSelectCarabayar2',
                                                'prompt' => '-',
                                                'options' => $carabayarOptions,
                                            ]);

                                            echo $form->field($multiPayer, 'add_penjamin_id_1', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'col-md-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->widget(DepDrop::classname(), [
                                                'name' => 'add_penjamin_id_1',
                                                'options' => [
                                                    'disabled' => false,
                                                    'class' => 'form-control select2 selectPenjamin',
                                                    'id' => 'add_penjamin_id_1',
                                                ],
                                                'pluginOptions' => [
                                                    'depends' => ['addSelectCarabayar2'],
                                                    'placeholder' => Yii::t('fe', '-- Pilih --'),
                                                    'url' => Url::to(['daftar/get-penjamin'])
                                                ],
                                            ]);

                                            echo $form->field($multiPayer, 'add_no_asuransi_1', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ],
                                                'options' => [
                                                    'class' => 'required',
                                                    'style' => 'display: none'
                                                ],
                                                'addon' => [
                                                    'append' => [
                                                        'content' => '<i class="fa fa-refresh" id="refresh-first-asuransi"></i>'
                                                    ]
                                                ]
                                            ])->widget(Typeahead::classname(), [
                                                'pluginOptions' => [
                                                    'highlight' => true,
                                                    'minLength' => 3,
                                                    'limit' => 10
                                                ],
                                                'dataset' => [
                                                    [
                                                        'limit' => 10,
                                                        'display' => 'value',
                                                        'delay' => 5000,
                                                        'remote' => [
                                                            'url' => Url::to(['/pendaftaran/end-point/get-data-asuransi']) . '?groupedByNoAsuransi=true&no_asuransi=',
                                                            'wildcard' => '%QUERY',
                                                            'replace' => new JsExpression('function (url, uriEncodedQuery) {
                                                                var _penjamin = $("#add_penjamin_id_1").val();
                                                                return url + uriEncodedQuery +"&penjamin_id=" + encodeURIComponent(_penjamin);
                                                            }')
                                                        ]
                                                    ]
                                                ],
                                                'pluginEvents' => [
                                                    "typeahead:selected" => "function(obj, item) {
                                                                                            $('.field-multicarabayarform-add_no_asuransi_1').find('#first-note-asuransi').html('<i>Atas Nama : ' + item.namapemilikasuransi + '<br>Pasien Pengguna Asuransi : ' + item.nama_pasien + '<br> Nomor Rekam Medik Pasien : ' + item.no_rekam_medik);
                                            
                                                                                            Object.assign(_formPendaftaran.firstTmpAsuransi, item);
                                                                                            $('#multicarabayarform-add_no_asuransi_1').prop('readonly', false);
                                                                                        }",
                                                ]
                                            ])->textInput([
                                                'placeholder' => $multiPayer->getAttributeLabel('no_asuransi'),
                                                'class' => 'form-control input-sm typeahead',
                                                'autocomplete' => "off"
                                            ])->hint('<span id="first-note-asuransi" style="color:green;"></span>');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-1 form-multi-carabayar cs1 " style="display: none;">
                                <!-- <hr> -->
                                <div class="form-group highlight-addon has-size-sm">
                                    <label class="control-label"><b style="color:white;">Aksi</b></label>
                                    <?= Html::button('+', ['class' => 'btn btn-success tambah-cara-bayar']); ?>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 nextRow add-2">
                            <div class="col-md-9 form-multi-carabayar-second" id="form-multi-carabayar-second" style="display: none;">
                                <!-- <hr> -->
                                <div class="form-group" id="form-multi-carabayar-content">
                                    <div class="row">
                                        <div class="default">
                                            <?php
                                            echo $form->field($multiPayer, 'add_carabayar_id_2', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'col-md-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->dropDownList($carabayar, [
                                                'class' => 'select2 selectCarabayar3',
                                                'id' => 'addSelectCarabayar3',
                                                'prompt' => '-',
                                                'options' => $carabayarOptions,
                                            ]);

                                            echo $form->field($multiPayer, 'add_penjamin_id_2', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'col-md-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->widget(DepDrop::classname(), [
                                                'name' => 'add_penjamin_id_2',
                                                'options' => [
                                                    'disabled' => false,
                                                    'class' => 'form-control select2 selectPenjamin',
                                                    'id' => 'add_penjamin_id_2',
                                                ],
                                                'pluginOptions' => [
                                                    'depends' => ['addSelectCarabayar3'],
                                                    'placeholder' => Yii::t('fe', '-- Pilih --'),
                                                    'url' => Url::to(['daftar/get-penjamin'])
                                                ],
                                            ]);

                                            // echo $form->field($multiPayer, 'add_asalrujukan_id_2', [
                                            //     'horizontalCssClasses' => [
                                            //         'label' => 'col-md-4',
                                            //         'wrapper' => 'col-md-8'
                                            //     ]
                                            // ])->dropDownList(ArrayHelper::map($asal_rujukan, 'asalrujukan_id', 'asalrujukan_nama'), [
                                            //     'class' => 'select2 selectRujukan',
                                            //     'prompt' => '-',
                                            //     'id'=>'add_asalrujukan_id_2',
                                            //     'disabled' => ($instalasi_id == DocoConstants::INSTALASI_MCU) ? false : false,
                                            // ]);

                                            echo $form->field($multiPayer, 'add_no_asuransi_2', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ],
                                                'options' => [
                                                    'class' => 'required',
                                                    'style' => 'display: none'
                                                ],
                                                'addon' => [
                                                    'append' => [
                                                        'content' => '<i class="fa fa-refresh" id="refresh-second-asuransi"></i>'
                                                    ]
                                                ]
                                            ])->widget(Typeahead::classname(), [
                                                'pluginOptions' => [
                                                    'highlight' => true,
                                                    'minLength' => 3,
                                                    'limit' => 10
                                                ],
                                                'dataset' => [
                                                    [
                                                        'limit' => 10,
                                                        'display' => 'value',
                                                        'delay' => 5000,
                                                        'remote' => [
                                                            'url' => Url::to(['/pendaftaran/end-point/get-data-asuransi']) . '?groupedByNoAsuransi=true&no_asuransi=',
                                                            'wildcard' => '%QUERY',
                                                            'replace' => new JsExpression('function (url, uriEncodedQuery) {
                                                                var _penjamin = $("#add_penjamin_id_2").val();
                                                                return url + uriEncodedQuery +"&penjamin_id=" + encodeURIComponent(_penjamin);
                                                            }')
                                                        ]
                                                    ]
                                                ],
                                                'pluginEvents' => [
                                                    "typeahead:selected" => "function(obj, item) {
                                                                                            $('.field-multicarabayarform-add_no_asuransi_2').find('#second-note-asuransi').html('<i>Atas Nama : ' + item.namapemilikasuransi + '<br>Pasien Pengguna Asuransi : ' + item.nama_pasien + '<br> Nomor Rekam Medik Pasien : ' + item.no_rekam_medik);
                                            
                                                                                            Object.assign(_formPendaftaran.secondTmpAsuransi, item);
                                                                                            $('#multicarabayarform-add_no_asuransi_2').prop('readonly', false);
                                                                                        }",
                                                ]
                                            ])->textInput([
                                                'placeholder' => $multiPayer->getAttributeLabel('no_asuransi'),
                                                'class' => 'form-control input-sm typeahead',
                                                'autocomplete' => "off"
                                            ])->hint('<span id="second-note-asuransi" style="color:green;"></span>');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-1 form-multi-carabayar-second" style="display: none;">
                                <!-- <hr> -->
                                <div class="form-group highlight-addon has-size-sm">
                                    <label class="control-label"><b style="color:white;">Aksi</b></label>
                                    <?= Html::button('-', ['class' => 'btn btn-danger hapus-cara-bayar']); ?>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </fieldset>
            </div>
            </div>

        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
    $form = ActiveForm::begin([
        'id' => 'form-daftar-rajal',
        'type' => ActiveForm::TYPE_VERTICAL,
        'enableClientValidation'=>false,
        'enableAjaxValidation'=>false,
        'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]);
?>
<!-- Widget Pasien -->
<div class="panel panel-flat inf-pasien" style="display: none;">
    <div class="panel-heading">
        <h6 class="panel-title">
            <i class="fa fa-user-circle"></i><span class='inf-pasien-title-nama'><strong>Tn. Nama Pasien</strong></span>
            <small class='inf-pasien-title-norm'>No. Rekam Medis : 0000001</small>
            <?= Html::hiddenInput('antrian_id', '', ['class'=>'antrian-id']); ?>
            <?= Html::hiddenInput('pasien_id', '', ['class'=>'pasien-id']); ?>
            <?= Html::hiddenInput('norm', '', ['id'=>'norm']); ?>
            <a class="heading-elements-toggle"><i class="icon-more"></i></a>
        </h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse" class="collapse-pasien"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-toolbar inf-pasien-toolbar clearfix">
        <div class="row">
            <div class="col-md-6">

            </div>
            <div class="col-md-6 text-right">
                <?=DocoHelpers::generateToolbar([
                    'Batal' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Batal'),
                        'icon' => 'fa fa-close',
                        'attributes' => [
                            'class' => 'btn-pasien-inf-batal',
                            'data-options'=>'click',
                        ]
                    ],
                ])?>
            </div>
        </div>
    </div>

    <div class="panel-body inf-pasien-body">
        <div class='col-md-4 col-sm-12'>
            <dl class="dl-horizontal">
                <dt class="dt-pasien"><?= Yii::t('fe', 'Jenis identitas'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-jenisidentitas'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'No identitas'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-noidentitas'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Nama depan'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-namadepan'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Nama pasien'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-namapasien'>lorem ipsum</dd>
                <!-- <dt class="dt-pasien"> --><?php #= Yii::t('fe', 'Nama panggilan'); ?><!-- </dt> -->
                <!-- <dd class="dd-pasien" id='inf-pasien-namapanggilan'>lorem ipsum</dd> -->
                <dt class="dt-pasien"><?= Yii::t('fe', 'Tempat lahir'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-tempatlahir'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Tanggal lahir'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-tanggallahir'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Umur'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-umur'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Jenis kelamin'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-jeniskelamin'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Golongan darah'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-golongandarah'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Status perkawinan'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-statusperkawinan'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Nama ibu'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-namaibu'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Nama ayah'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-namaayah'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Anak ke'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-anakke'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Jumlah bersaudara'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-jumlahbersaudara'>lorem ipsum</dd>
            </dl>
        </div>
        <div class='col-md-4 col-sm-12'>
            <dl class="dl-horizontal">
                <dt class="dt-pasien"><?= Yii::t('fe', 'Alamat pasien'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-alamatpasien'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'RT / RW'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-rtrw'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Kelurahan'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-kelurahan'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Kecamatan'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-kecamatan'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Kota'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-kota'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Propinsi'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-propinsi'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'No telepon'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-notelepon'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'No mobile'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-nomobile'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Alamat email'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-alamatemail'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Warga negara'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-warganegara'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Pendidikan'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-pendidikan'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Pekerjaan'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-pekerjaan'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Suku'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-suku'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Agama'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-agama'>lorem ipsum</dd>
            </dl>
        </div>
        <!-- photo here -->
        <div class='col-md-4 col-sm-12'>
            <!-- image -->
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="thumbnail no-padding">
                        <div class="thumb" id="pasang_image">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Widget Pasien -->

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
        </div>
    </div>
</div>
<?= Yii::$app->controller->renderPartial('/daftar/partial/component/form-rujukan',[
    'form' => $form,
    'modelRujukan' => $modelRujukan
]); ?>
<div class="row select-no-rm">
    <div class="col-md-6">
    </div>
    <div class="col-md-6">

    </div>
</div>


<!-- Form Pasien -->
<div class='panel panel-flat form-data-pasien' style="display: none;">
    <div class="panel-heading">
        <h6 class="panel-title"><span class='inf-pasien-title-nama'><strong>Tn. Nama Pasien</strong></span>
            <small class='inf-pasien-title-norm'>No. Rekam Medis : 0000001</small>
            <a class="heading-elements-toggle"><i class="icon-more"></i></a>
        </h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-toolbar clearfix">
        <div class="row">
            <div class="col-md-1">
                <?=DocoHelpers::generateToolbar([
                    'update' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-pencil',
                        'attributes' => [
                            'class' => 'btn-pasien-simpan-ubah',
                            'data-options'=>'click',
                        ]
                    ],
                    'reset2' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Muat ulang'),
                        'icon' => 'fa fa-refresh',
                        'attributes' => [
                            'class' => 'btn-pasien-reset',
                            'data-options'=>'click',
                            'style'=>'display:none;'
                        ],
                    ],
                ]);?>
            </div>
            <div class="col-md-5">
                <?= $form->field($modelPasien, 'nopeserta_bpjs', [
                    'inputOptions' => [
                        'id' => 'frm-pasien-nopeserta_bpjs',
                        'placeholder' => 'Cari berdasarkan no BPJS',
                        'style' => 'display: none;'
                    ],
                ])->label(false); ?>
            </div>
            <div class="col-md-6 text-right">
                <?=DocoHelpers::generateToolbar([
                    'batal' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Batal'),
                        'icon' => 'fa fa-close',
                        'attributes' => [
                            'class' => 'btn-pasien-batal',
                            'data-options'=>'click',
                        ]
                    ],
                ])?>
            </div>
        </div>
        <?php //echo Html::resetINput('reset', ['class'=>'btn']);?>
    </div>
    <div class="panel-body">
        <div id="form-input-pasien" style="display: none;">
            <div class="form-group" id="form-pasien-content">
                <div class="div-pasien">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-sm-6">
                                <!-- <?= $form->field($modelPasien, 'namadepan')->dropDownList(ArrayHelper::map($data_lookup['nama_depan'], 'lookup_id', 'lookup_value'), [
                                    'class' => 'select2 select2pasien',
                                    'id'=>'frm-pasien-namadepan',
                                    'prompt' => '— PILIH —',
                                ])->label(Yii::t('fe', 'Nama Depan')); ?> -->
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'nama_pasien', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-nama_pasien',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'alamat_pasien')->textArea(['id' => 'frm-pasien-alamat_pasien'] ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'tempat_lahir', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-tempat_lahir',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]) ?>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group required">
                                    <label class="control-label has-star">Tanggal Lahir</label>
                                    <div class="input-group inline-datepicker">
                                            <input type="text" id="frm-pasien-tanggal_lahir" class="form-control" name="PasienForm[tanggal_lahir]" data-mask="99-99-9999">
                                            <span class="input-group-addon"><i id="btn_addon_tgllahir" class="fa fa-calendar "></i></span>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'rt', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-rt',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]) ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'rw', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-rw',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'umur', [
                                    'inputOptions'=>['id' => 'frm-pasien-umur', 'readonly' => true]]); ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'nama_ibu', [
                                    'inputOptions' => ['id' => 'frm-pasien-nama_ibu']
                                ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'nama_panggilan', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-nama_panggilan',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= 
                                $form->field($modelPasien, 'is_pj', [
                                    'options' => [
                                                'tag' => false,
                                            ],
                                ])->checkbox([
                                    'label' => 'Penanggung Jawab',
                                    'value' => 1,
                                    'class' => 'styled action-checked',
                                ])->label(false);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'jeniskelamin')
                                ->radioList(
                                    ArrayHelper::map($data_lookup['jenis_kelamin'], 'lookup_id', 'lookup_value'),
                                    ['inline'=>true, 'id'=>'frm-pasien-jeniskelamin']
                                )
                                ->label(Yii::t('fe', 'Jenis Kelamin'));
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'nama_ayah', [
                                'inputOptions' => ['id' => 'frm-pasien-nama_ayah']
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'golongandarah')
                                ->dropDownList(
                                    ArrayHelper::map($data_lookup['golongan_darah'], 'lookup_id', 'lookup_value'),
                                    [
                                        'id'=>'frm-pasien-golongandarah',
                                        'class' => 'select2',
                                        'prompt' => '-- PILIH --'
                                    ]
                                )
                                ->label(Yii::t('fe', 'Golongan Darah'));
                            ?>
                        </div>
                        <div class="col-md-6">
                            <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'anakke', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-anakke',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]) ?>
                            </div>
                            <div class="col-sm-6">
                                <?= $form->field($modelPasien, 'jumlah_bersaudara', [
                                    'inputOptions' => [
                                        'id' => 'frm-pasien-jumlah_bersaudara',
                                        'class' => 'form-control input-sm'
                                    ]
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'propinsi_id')
                                ->dropDownList(
                                    ArrayHelper::map($data_master['propinsi'], 'propinsi_id', 'propinsi_nama'),
                                    [
                                        'id'=>'frm-pasien-propinsi_id',
                                        'class'=>'select2 select2pasien',
                                        'prompt'=>'— PILIH —',
                                        'options'=>$optionsProv
                                    ]
                                )
                                ->label(Yii::t('fe', 'Propinsi'));
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'alamatemail', [
                            'inputOptions' => ['id' => 'frm-pasien-alamatemail']
                            ])->label(Yii::t('fe', 'Alamat Email')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'kabupaten_id')->widget(DepDrop::classname(), [
                                'options'=>['id'=>'frm-pasien-kabupaten_id','class'=>'select2 select2pasien',],
                                'pluginOptions'=>[
                                    'depends'=>['frm-pasien-propinsi_id', 'frm-pasien-jenisidentitas', 'frm-pasien-no_identitas_pasien'],
                                    'placeholder'=>'-- PILIH --',
                                    'url'=>Url::to(['/pendaftaran/end-point/list-kabupaten'])
                                ],
                                'pluginEvents'=>[
                                    "depdrop:afterChange"=>"function(event, id, value) {
                                        if ($('#frm-pasien-no_telepon_pasien').is(':focus')) {
                                            $('#frm-pasien-kabupaten_id').focus();
                                        }
                                    }",
                                ]
                            ])->label(Yii::t('fe', 'Kabupaten')); ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'pendidikan_id')
                                ->dropDownList(
                                    ArrayHelper::map($data_master['pendidikan'], 'pendidikan_id', 'pendidikan_nama'),
                                    ['id'=>'frm-pasien-pendidikan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                                )->label(Yii::t('fe', 'Pendidikan'))
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'kecamatan_id')->widget(DepDrop::classname(), [
                                'options'=>['id'=>'frm-pasien-kecamatan_id', 'class'=>'select2 select2pasien',],
                                'pluginOptions'=>[
                                    'depends'=>['frm-pasien-kabupaten_id', 'frm-pasien-jenisidentitas', 'frm-pasien-no_identitas_pasien'],
                                    'placeholder'=>'-- PILIH --',
                                    'url'=>Url::to(['/pendaftaran/end-point/list-kecamatan'])
                                ],
                                'pluginEvents'=>[
                                    "depdrop:afterChange"=>"function(event, id, value) {
                                        if ($('#frm-pasien-kabupaten_id').is(':focus') || $('#frm-pasien-no_telepon_pasien').is(':focus')) {
                                            $('#frm-pasien-kecamatan_id').focus();
                                        }
                                    }",
                                ]
                            ])->label(Yii::t('fe', 'Kecamatan')); ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'pekerjaan_id')
                                ->dropDownList(
                                    ArrayHelper::map($data_master['pekerjaan'], 'pekerjaan_id', 'pekerjaan_nama'),
                                    ['id'=>'frm-pasien-pekerjaan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                                )->label(Yii::t('fe', 'Pekerjaan'))
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'kelurahan_id')->widget(DepDrop::classname(), [
                                'options'=>['id'=>'frm-pasien-kelurahan_id', 'class'=>'select2 select2pasien',],
                                'pluginOptions'=>[
                                    'depends'=>['frm-pasien-kecamatan_id'],
                                    'placeholder'=>'-- PILIH --',
                                    'url'=>Url::to(['/pendaftaran/end-point/list-kelurahan'])
                                ],
                                'pluginEvents'=>[
                                    "depdrop:afterChange"=>"function(event, id, value) {
                                        if ($('#frm-pasien-kecamatan_id').is(':focus') || $('#frm-pasien-no_telepon_pasien').is(':focus')) {
                                            $('#frm-pasien-kelurahan_id').focus();
                                        }
                                    }",
                                ]
                            ])->label(Yii::t('fe', 'Kelurahan')); ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'suku_id')
                                ->dropDownList(
                                    ArrayHelper::map($data_master['suku'], 'suku_id', 'suku_nama'),
                                    ['id'=>'frm-pasien-suku_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                                )
                                ->label(Yii::t('fe', 'Suku'));
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'no_telepon_pasien', [
                                'inputOptions' => ['id' => 'frm-pasien-no_telepon_pasien']
                            ]); ?>
                        </div>
                        <div class="col-md-6">
                            <?php $modelPasien->warga_negara = '308'; ?>
                            <?= $form->field($modelPasien, 'warga_negara')
                                ->dropDownList(
                                    ArrayHelper::map($data_lookup['warga_negara'], 'lookup_id', 'lookup_value'),
                                    [
                                        'id'=>'frm-pasien-warga_negara',
                                        'class'=>'select2 select2pasien',
                                        'prompt'=>'— PILIH —'
                                    ]
                                )
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'agama')
                                ->dropDownList(
                                    ArrayHelper::map($data_lookup['agama'], 'lookup_id', 'lookup_value'),
                                    ['id'=>'frm-pasien-agama', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                                )
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelPasien, 'statusperkawinan')
                                ->dropDownList(
                                    ArrayHelper::map($data_lookup['status_perkawinan'], 'lookup_id', 'lookup_value'),
                                    ['id'=>'frm-pasien-statusperkawinan', 'class'=>'select2  select2pasien', 'prompt'=>'— PILIH —']
                                )
                                ->label(Yii::t('fe', 'Status Perkawinan'))
                            ?>
                        </div>
                        <div class="row col-md-6">
                            <?= $form->field($modelPasien, 'catatanpenting_pasien')->textArea([
                                'id' => 'frm-pasien-catatanpenting_pasien'
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End of Form Pasien -->


<?php ActiveForm::end(); ?>