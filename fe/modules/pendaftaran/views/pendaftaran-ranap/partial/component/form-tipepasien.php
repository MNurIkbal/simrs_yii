<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use kartik\widgets\DepDrop;
    use kartik\select2\Select2;
    use kartik\typeahead\Typeahead;
    use yii\web\JsExpression;
    use yii\web\View;
    use app\components\DocoConstants;
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
    min-width: 75px !important;
    text-align: left !important;
}

.input-group {
    width: 100%;
}

.dokter-perujuk {
    margin-top: 7px;
}

.bpjs-penunjang {
    margin-left: 85px;
    margin-top: 7px;
}

.tipe-penunjang {
    float: left;
    margin-left: 10px;
}

.js .inputfile {
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    position: absolute;
    z-index: -1;
}

#file-upload {
    display: none;
    margin: 10px;
}

.inputfile+label {
    max-width: 100%;
    font-size: 1.25rem;
    /* 20px */
    font-weight: normal;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: pointer;
    display: inline-block;
    overflow: hidden;
    /* padding: 0.625rem 1.25rem; */
    padding: 7px 25px;
    width: auto;
    /* 10px 20px */
}

.no-js .inputfile+label {
    display: none;
}

.inputfile:focus+label,
.inputfile.has-focus+label {
    outline: 1px dotted #000;
    outline: -webkit-focus-ring-color auto 5px;
}

.inputfile+label * {
    /* pointer-events: none; */
    /* in case of FastClick lib use */
}

.inputfile+label svg {
    width: 1em;
    height: 1em;
    vertical-align: middle;
    fill: currentColor;
    margin-top: -0.25em;
    /* 4px */
    margin-right: 0.25em;
    /* 4px */
}

.inputfile-1+label {
    color: #ffff;
    background-color: #4CAF50;

}

.inputfile-1:focus+label,
.inputfile-1.has-focus+label,
.inputfile-1+label:hover {
    background-color: #078448;
}

.my-legend-cst .legend-title-cst {
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 12px;
}

.my-legend-cst .legend-scale-cst ul {
    margin: 0;
    padding: 0;
    list-style: none;
}

.my-legend-cst .legend-scale-cst ul li {
    display: block;
    float: left;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: center;
    font-size: 12px;
    list-style: none;
}

.my-legend-cst ul.legend-labels-cst li span {
    display: block;
    float: left;
    border: 1px solid #616161;
    padding: 4px 10px;
    color: #191919;
}

.my-legend-cst .legend-source-cst {
    font-size: 12px;
    color: #999;
    clear: both;
}

.my-legend-cst a {
    color: #777;
}

.panel-info-btn {
    padding: 5px 13px 0px 13px;
}
</style>
<div class="row">
    <div class="form-group">
        <div class="col-md-3" id="form-parent-info" style="display: none;">
            <div class="content-group">
                <div class="bg-indigo-300 text-right panel-info-btn">
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
                            -
                        </h6>

                        <span class="display-block rm-pasien">-</span>
                    </div>

                    <!-- <a href="#" class="display-inline-block content-group-sm">
                        <img src="/media/img/icon-app/default.jpg" class="img-circle img-responsive"
                        alt="" style="width: 110px; height: 110px;">
                    </a> -->
                </div>
                <ul
                    class="nav nav-tabs nav-justified no-margin no-border-radius bg-teal-400 border-top border-top-teal-300">
                    <li class="active">
                        <a href="#info" class="text-size-small text-uppercase text-semibold" data-toggle="tab"
                            aria-expanded="true">
                            Info
                        </a>
                    </li>

                    <li class="">
                        <a href="#kunjungan" class="text-size-small text-uppercase text-semibold" data-toggle="tab"
                            aria-expanded="false">
                            Kunjungan
                        </a>
                    </li>
                    <li class="" id="ket-bpjs" style="display: none;">
                        <a href="#bpjs" class="text-size-small text-uppercase text-semibold" data-toggle="tab"
                            aria-expanded="false">
                            BPJS
                        </a>
                    </li>
                </ul>
                <div class="tab-content panel-body" style="border: 1px solid #dddddd;background:#f2fdf7;">
                    <div class="tab-pane fade active in" id="info">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-md-6">NIK</div>
                                <div class="col-md-6 nik-pasien">:&nbsp;-</div>
                            </div>
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
                                <div class="col-md-6 ortu-pasien">:&nbsp;-</div>
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
                                <div class="col-md-6">No Kartu</div>
                                <div class="col-md-6" id="bpjsnew_detail_nokartu">:&nbsp;-</div>
                            </div>
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
                            <button type="button" class="btn btn-detail-bpjs btn-info btn-labeled btn-xs"
                                action="<?= Url::home() ?>pendaftaran/daftar-igd/detail-history-bpjs?no_kartu="
                                data-toggle="modal" data-target="#modal_backdrop" data-width="90%"
                                style="display:none"><b><i class="fa fa-eye"></i></b>History BPJS</button>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="kunjungan">
                        <div class="list-group" id="list-history"
                            style="border: 1px solid #3c9a6630!important; border-radius:3px!important; padding:0px;margin-bottom:4px!important;">
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
                    <div class="panel-heading text-uppercase text-semibold" style="text-align: center"><strong>Piutang
                            Pasien</strong></div>
                    <div class="panel-body" style="text-align: center">Rp. <b id="val-piutang"></b></div>
                </div>
            </div>
            <div class="content-group" id="info-catatan" style="display:none">
                <div class="panel panel-danger">
                    <div class="panel-heading text-uppercase text-semibold" style="text-align:center;"><strong>Catatan
                            Penting Pasien</strong></div>
                    <div class="panel-body" style="text-align:left;"><b id="val-catatan"></b></div>
                </div>
            </div>
        </div>
        <div class="col-md-12" id="form-parent">
            <div class="panel panel-white">
                <!-- <div class="panel-heading">
                    <h6 class="panel-title">Form Pendaftaran</h6>
                </div> -->
                <div class="steps-basic">
                    <h6>Tipe Pasien</h6>
                    <fieldset>
                        <?php
                            if (!empty($is_penunjang)) :
                            $jenis = ['1'=>'APS', '0'=>'Pasien RS'];
                            $tipePasien->is_aps = 1;
                        ?>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label class="control-label col-sm-4"><?=Yii::t('fe', 'Jenis pendaftaran')?></label>
                                    <?= Html::activeRadioList($tipePasien, 'is_aps', $jenis , [
                                            'inline' => true,
                                            'type' => 'checkbox',
                                            'class' => 'jenis_pendaftaran tipe-penunjang'
                                    ] ) ?>
                                </div>
                            </div>
                        </div>

                        <?php endif; ?>

                        <?php
                            if (!is_array($instalasi_id) && $instalasi_id == DocoConstants::INSTALASI_MCU) :
                            $jenis = ['0'=>'Individu', '1'=>'Kolektif'];
                            $tipePasien->is_kolektif = 0;
                        ?>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <?= Html::activeRadioList($tipePasien, 'is_kolektif', $jenis , [
                                            'inline' => true,
                                            'type' => 'checkbox',
                                    ] ) ?>
                                </div>
                            </div>
                        </div>

                        <?php endif; ?>
                        <div class="row pasien-aps">
                            <div class="col-sm-12">
                            <!-- Hide Daftar Bayi -->
                                <?php
                                // $form->field($tipePasien, 'is_bbl', [
                                //     'options' => [
                                //         'tag' => false,
                                //     ],
                                // ])->checkbox([
                                //     'label' => 'Pendaftaran Bayi',
                                //     'value' => 1,
                                //     'class' => 'styled action-checked',
                                //     'data-urutan' => 1
                                // ]);
                                ?>
                            </div>
                            <div class="col-sm-5">
                                <?php 
                                echo Html::activeHiddenInput($modelKunjungan, 'group_carabayar');
                                echo Html::hiddenInput('pasien_id_hidden', '', ['id' => 'pasien_id_hidden']);
                                echo Html::hiddenInput('pendaftaran_id_hidden', '', ['id' => 'pendaftaran_id_hidden']);
                                echo Html::hiddenInput('pasien_ol_status', empty($penOl['status_pasien']) ? "" : $penOl['status_pasien'], ['id' => 'pasien_ol_status']);
                                ?>
                                <?php
                                echo $form->field($tipePasien, 'no_rekam_medik', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-7'
                                    ],
                                    'addon' => [
                                        'prepend' => [
                                            'content'=> Html::checkbox('chk-statuspasien', true, ['class' => 'notUniform']) . ' ' . Yii::t('fe', 'Pasien lama'),
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
                                    'id' => 'no_rekam_medik',
                                    'placeholder' => Yii::t('fe','No rm').' / '.Yii::t('fe', 'Nama pasien'). ' / ' .Yii::t('fe', 'Tanggal lahir')
                                ]);
                                ?>
                                <?php
                                echo '<div class="nextRow">';
                                    echo $form->field($tipePasien, 'carabayar_id', [
                                        'horizontalCssClasses' => [
                                            'label' => 'col-md-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->dropDownList($carabayar, [
                                        'class' => 'select2 selectCarabayar',
                                        'id'=>'selectCarabayar',
                                        'prompt' => '-',
                                        'options'=> $carabayarOptions,

                                    ])->label('Penanggung Biaya');

                                    echo $form->field($tipePasien, 'penjamin_id', [
                                        'horizontalCssClasses' => [
                                            'label' => 'col-md-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->widget(DepDrop::classname(), [
                                        'name' => 'penjamin_id',
                                        'options' => [
                                            'class' => 'form-control select2 selectPenjamin',
                                            'id' => 'penjamin_id',
                                            'disabled' => false,
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
                                                
                                            }",
                                        ]
                                    ]);
                                if(!is_array($instalasi_id) && $instalasi_id == DocoConstants::INSTALASI_MCU) {
                                    echo Html::hiddenInput('asalrujukan_id_hidden', $default_asal_rujukan);
                                }
                                echo $form->field($tipePasien, 'asalrujukan_id', [
                                    'horizontalCssClasses' => [
                                        'label' => 'col-md-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->dropDownList(ArrayHelper::map($asal_rujukan, 'asalrujukan_id', 'asalrujukan_nama'), [
                                    'class' => 'select2 selectRujukan',
                                    'prompt' => '-',
                                    'id'=>'asalrujukan_id',
                                    'disabled' => ($instalasi_id == DocoConstants::INSTALASI_MCU) ? false : false,
                                ])->label('Cara Datang');
                                echo '</div>';
                                ?>

                                <div id="form-bpjs" style="display: none">
                                    <!-- <div class="col-sm-5"> -->
                                    <?= $form->field($modelBpjs, 'jenis_rujukan')->radioList(
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
                                    <!-- </div> -->
                                </div>
                                <?php $modelPj->pj_pengantar = 990 ?>
                                <div id="radio-penanggung_jawab" style="display:none;">
                                    <?= $form->field($modelPj, 'pj_pengantar')
                                        ->radioList(
                                            ArrayHelper::map($data_lookup['pengantar_sy'], 'lookup_id', 'lookup_value'),
                                            ['inline'=>true, 'id'=>'form-pj-pengantar']
                                        )
                                        ->label(Yii::t('fe', 'Penanggung Jawab'));
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-6" id="form-asuransi" style="display:none;">
                            <div class="col-sm-12">
                                    <?php 
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
                                                'autocomplete' => "off",
                                                'maxlength' => 20
                                        ])->hint('<span id="note-asuransi" style="color:green;"></span>');
                                ?>
                                <input type="text" name="selectedNoAsuransi" id="tipepasienform-nokartuasuransi" class="hidden">
                                </div>
                                <!-- <div class="col-sm-12">
                                    <?=
                                        $form->field($modelAsuransi, 'nama_asuransi', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3',
                                                'wrapper' => 'col-md-9',
                                            ]
                                        ])->textInput()->label(Yii::t('fe', 'Nama Asuransi'));
                                    ?>
                                </div> -->
                                <div class="col-sm-12">
                                    <?=
                                        $form->field($modelAsuransi, 'namapemilikasuransi', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3',
                                                'wrapper' => 'col-md-9',
                                                'autofocus' => 'autofocus'
                                            ]
                                        ])->textInput([
                                            'data-urutan' => 1,
                                        ])->label(Yii::t('fe', 'Peserta'));
                                    ?>
                                </div>
                                <div class="col-sm-12">
                                    <?=
                                        $form->field($modelAsuransi, 'nomorpokokperusahaan', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3',
                                                'wrapper' => 'col-md-9'
                                            ]
                                        ])->textInput()->label(Yii::t('fe', 'Nomor Jaminan'));
                                    ?>
                                </div>
                                <div class="col-sm-12">
                                    <?=
                                        $form->field($modelAsuransi, 'namaperusahaan', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3',
                                                'wrapper' => 'col-md-9'
                                            ]
                                        ])->textInput()->label(Yii::t('fe', 'Nama Badan Usaha'));
                                    ?>
                                </div>
                                <div class="hidden">
                                    <?=
                                        $form->field($modelAsuransi, 'kelastanggungan_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3',
                                                'wrapper' => 'col-md-9'
                                            ]
                                        ])->dropDownList($kelaspelayanan, [
                                            'prompt' => '-'
                                        ])->label(Yii::t('fe', 'Kelas Tanggungan'));
                                    ?>
                                </div>
                                <div class="col-sm-12">
                                    <?=
                                        $form->field($modelAsuransi, 'masaberlakukartu', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3',
                                                'wrapper' => 'col-md-9'
                                            ],
                                            'addon' => [
                                                'append' => [
                                                    ['content' => '<i id="btn_addon_tgllahir" class="fa fa-calendar "></i>'],
                                                ],
                                            ]
                                        ])->textInput([
                                            'class'=>'pickadate-w-month',
                                            'data-mask' => '99-99-9999',
                                            'id' => 'masaberlakukartu'
                                        ])->label(Yii::t('fe', 'Tanggal Berlaku'));
                                    ?>
                                </div>
                                <div class="hidden">
                                    <?=
                                        $form->field($modelAsuransi, 'status_konfirmasi', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-3',
                                                'wrapper' => 'col-md-9'
                                            ]
                                        ])->checkbox([
                                            'class' => 'styled action-checked',
                                        ]);
                                    ?>
                                </div>
                            </div>
                            <div id="form-penanggung" style="display:none;">
                                <div class="col-sm-6">
                                    <div class="col-sm-12" id="form-penanggung-instansi" style="display:none;">
                                        <?= $form->field($modelPenanggungBiaya, 'instansi', [
                                            'inputOptions' => [
                                                'id' => 'form-tipepasien-instansi',
                                                'class' => 'form-control input-sm'
                                            ]
                                        ]); ?>
                                    </div>
                                    <div class="col-sm-12">
                                        <?= $form->field($modelPenanggungBiaya, 'penanggungbiaya_nama', [
                                            'inputOptions' => [
                                                'id' => 'form-tipepasien-penanggungbiaya_nama',
                                                'class' => 'form-control input-sm'
                                            ]
                                        ])->label('Nama'); ?>
                                    </div>
                                    <!-- <div class="col-sm-12">
                                        <?= $form->field($modelPenanggungBiaya, 'namabagian', [
                                            'horizontalCssClasses' => [
                                                'label' => 'col-md-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($bagianOptions, [
                                            'class' => 'select2 selectBagian',
                                            'id'=>'form-tipepasien-namabagian',
                                            'prompt' => '-'
                                        ])->label('Nama Bagian'); ?>
                                    </div> -->
                                    <div class="col-sm-12" id="div-namabagian">
                                        <?= $form->field($modelPenanggungBiaya, 'namabagian', [
                                            'inputOptions' => [
                                                'id' => 'form-tipepasien-namabagian',
                                                'class' => 'form-control input-sm'
                                            ]
                                        ])->label('Nama Bagian'); ?>
                                    </div>
                                    <div class="col-sm-12" id="div-ruangcarabayar_id">
                                        <?= $form->field($modelPenanggungBiaya, 'ruangcarabayar_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'col-md-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->widget(DepDrop::classname(), [
                                            'name' => 'ruangcarabayar_id',
                                            'options' => [
                                                'class' => 'form-control select2 selectRuangCaraBayar',
                                                'id' => 'ruangcarabayar_id',
                                            ],
                                            'pluginOptions' => [
                                                'depends' => ['selectCarabayar'],
                                                'placeholder' => Yii::t('fe', '-- PILIH --'),
                                                'url' => Url::to(['end-point/list-bagian'])
                                            ],
                                        ])->label('Nama Bagian'); ?>
                                    </div>
                                    <div class="col-sm-12" id="form-penanggung-noindukkaryawan" style="display:none;">
                                        <?= $form->field($modelPenanggungBiaya, 'noindukkaryawan', [
                                            'inputOptions' => [
                                                'id' => 'form-tipepasien-noindukkaryawan',
                                                'class' => 'form-control input-sm',
                                                'maxlength' => 8
                                            ]
                                        ])->label('NIP'); ?>
                                    </div>
                                    <div class="hidden" id="form-penanggung-jpkm">
                                        <?= $form->field($modelPenanggungBiaya, 'jpkm', [
                                            'inputOptions' => [
                                                'id' => 'form-tipepasien-jpkm',
                                                'class' => 'form-control input-sm'
                                            ]
                                        ])->label('JPKM'); ?>
                                    </div>
                                </div>
                            </div>
                            <div id="form-bpjs-1" style="display:none;">
                                <div class="col-sm-6">
                                    <?= $form->field($modelBpjs, 'tanggal_sep', [
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
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
                                                    'wrapper' => 'col-md-8'
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
                                            )->label('Cara Datang');
                                        ?>

                                        <?php
                                        echo $form->field($modelBpjs, 'no_rujukan_f', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ],
                                            'inputOptions'=>['id'=>'no_rujukan'],
                                        ])->hint('<div class="text-danger err-no-rujukan"></div>');
                                        ?>
                                    </div>
                                    <div id="base-rujukan-manual" style="display:none;">
                                        <?php
                                        $modelBpjs->jenis_pelayanan = 2;
                                        echo $form->field($modelBpjs, 'jenis_pelayanan',[
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->dropDownList(
                                                [
                                                    // '2'=>Yii::t('fe', 'Rawat Jalan'),
                                                    '1'=>Yii::t('fe', 'Rawat Inap'),
                                                ],
                                                [
                                                    'id'=>'jenis_pelayanan',
                                                    'class' => 'select2',
                                                    // 'prompt'=>'— PILIH —',
                                                ]
                                            );
                                        // Dropdown pelayanan diganti jadi field biasa
                                        // echo $form->field($modelBpjs, 'jenis_pelayanan',[
                                        //         'horizontalCssClasses' => [
                                        //             'label' => 'text-left control-label col-sm-4',
                                        //             'wrapper' => 'col-md-7'
                                        //         ],
                                        //     'inputOptions'=>['id'=>'jenis_pelayanan'],
                                        // ]);

                                        $modelBpjs->jenis_kartu = 1;
                                        echo $form->field($modelBpjs, 'jenis_kartu',[
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
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
                                                    'wrapper' => 'col-md-8'
                                                ],
                                            'inputOptions'=>['id'=>'no_kartu', 'maxlength' => 13],
                                        ])->hint('<div class="text-danger err-no-kartu"></div>');
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div id="form-pasien" style="display:none;">
                                <div class="col-sm-4">
                                    <div class="col-sm-4">
                                        <?= $form->field($modelPj, 'pj_namadepan')->dropDownList(ArrayHelper::map($data_lookup['nama_depan'], 'lookup_id', 'lookup_value'), [
                                            'class' => 'select2 select2pasien',
                                            'id'=>'form-tipepasien-namadepan',
                                            'prompt' => '— PILIH —',
                                        ])->label(Yii::t('fe', 'Sebutan')); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= $form->field($modelPj, 'pj_nama', [
                                            'inputOptions' => [
                                                'id' => 'form-tipepasien-nama_pasien',
                                                'class' => 'form-control input-sm'
                                            ]
                                        ]); ?>
                                    </div>
                                    <div class="col-sm-12">
                                        <?= $form->field($modelPj, 'pj_jk', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->radioList(
                                            ArrayHelper::map($data_lookup['jenis_kelamin'], 'lookup_id', 'lookup_value'),
                                            [
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
                                    </div>
                                    <div id="error_PjpasienFormpj_jk" style="margin-left:178px;"></div>
                                    <div class="col-sm-12" style="padding:0;">
                                        <div class="col-sm-6">
                                            <?= $form->field($modelPj, 'pj_propinsi_id')
                                                ->dropDownList(
                                                    ArrayHelper::map($data_master['propinsi'], 'propinsi_id', 'propinsi_nama'),
                                                    [
                                                        'id'=>'form-tipepasien-propinsi_id',
                                                        'class'=>'select2 select2pasien',
                                                        'options'=>$optionsProv,
                                                        'prompt'=>'— PILIH —'
                                                    ]
                                                )
                                                ->label(Yii::t('fe', 'Propinsi'));
                                            ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <?= $form->field($modelPj, 'pj_kabupaten_id')
                                                ->widget(DepDrop::classname(), [
                                                    'data'=>$ddlkabupaten,
                                                    'options'=>['id'=>'form-tipepasien-kabupaten_id','class'=>'select2'],
                                                    'pluginOptions'=>[
                                                        'depends'=>['form-tipepasien-propinsi_id'],
                                                        'placeholder'=>'-- PILIH --',
                                                        'url'=>Url::to(['/master/kabupaten/list-kabupaten?selected='.$modelPj->pj_kabupaten_id])
                                                    ]
                                                ]);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-12" style="padding:0;">
                                        <div class="col-sm-6">
                                            <?= $form->field($modelPj, 'pj_kecamatan_id')
                                                ->widget(DepDrop::classname(), [
                                                        'data'=>$ddlkecamatan,
                                                        'options'=>['id'=>'form-tipepasien-kecamatan_id','class'=>'select2'],
                                                        'pluginOptions'=>[
                                                        'depends'=>['form-tipepasien-kabupaten_id'],
                                                        'placeholder'=>'-- PILIH --',
                                                        'url'=>Url::to(['/master/kecamatan/list-kecamatan?selected='.$modelPj->pj_kecamatan_id])
                                                    ]
                                                ]);
                                            ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <?= $form->field($modelPj, 'pj_kelurahan_id')
                                                ->dropDownList(
                                                    [],
                                                    ['id'=>'form-tipepasien-kelurahan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                                                )->label(Yii::t('fe', 'Kelurahan'))
                                                ->widget(DepDrop::classname(), [
                                                        'options'=>['id'=>'form-tipepasien-kelurahan_id','class'=>'select2'],
                                                        'pluginOptions'=>[
                                                        'depends'=>['form-tipepasien-kecamatan_id'],
                                                        'placeholder'=>'-- PILIH --',
                                                        'url'=>Url::to(['/master/kelurahan/list-kelurahan?selected='.$modelPj->pj_kelurahan_id])
                                                    ]
                                                ]);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <?= $form->field($modelPj, 'pj_rt', [
                                            'inputOptions' => [
                                                'id' => 'form-tipepasien-rt',
                                                'class' => 'form-control input-sm'
                                            ]
                                        ]) ?>
                                    </div>
                                    <div class="col-sm-3">
                                        <?= $form->field($modelPj, 'pj_rw', [
                                            'inputOptions' => [
                                                'id' => 'form-tipepasien-rw',
                                                'class' => 'form-control input-sm'
                                            ]
                                        ]) ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($modelPj, 'pj_kode_pos', [
                                            'inputOptions' => [
                                                'id' => 'form-tipepasien-kode_pos',
                                                'class' => 'form-control input-sm'
                                            ]
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="col-sm-12">
                                        <?= $form->field($modelPj, 'pj_alamat')->textArea([
                                            'id' => 'form-tipepasien-alamat_pasien',
                                            'rows' => 2,
                                        ]); ?>
                                    </div>
                                    <div class="col-sm-12">
                                        <?= $form->field($modelPj, 'pj_no_telepon', [
                                            'inputOptions' => ['id' => 'form-tipepasien-no_telepon_pasien']
                                        ]); ?>
                                    </div>
                                    <div class="col-sm-12">
                                        <?= $form->field($modelPj, 'pj_pekerjaan_id')
                                            ->dropDownList(
                                                ArrayHelper::map($data_master['pekerjaan'], 'pekerjaan_id', 'pekerjaan_nama'),
                                                ['id'=>'form-tipepasien-pekerjaan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                                            )->label(Yii::t('fe', 'Pekerjaan'))
                                        ?>
                                    </div>
                                    <div class="col-sm-12">
                                        <?= $form->field($modelPj, 'pj_pt', [
                                            'inputOptions' => ['id' => 'form-tipepasien-pt']
                                        ]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                            if (!is_array($instalasi_id) && $instalasi_id == DocoConstants::INSTALASI_MCU) :
                        ?>
                        <div id="row-template" class="row" hidden>
                            <div class="col-sm-1" style="margin-left: 0;">
                                <button style="background:#4CAF50 !important;width:150px !important" type="button"
                                    id="unduh-template" class="btn btn-success btn-labeled btn-sm" data-target=""
                                    data-options="click"><b><i class="fa fa-file-excel-o"></i></b>Unduh
                                    Template</button>
                            </div>
                            <div class="col-sm-3" style="margin-left: 60px;">
                                <input type="file" name="TipePasienForm[upload_file]" id="file-upload"
                                    class="form-control inputfile inputfile-1"
                                    data-multiple-caption="{count} files selected">
                                <label for="file-upload">
                                    <i class="fa fa-upload"></i>
                                    <span id="label-file">Unggah Berkas</span>
                                </label>
                                <p class="format-upload">format :xls, xlsx</p>
                            </div>
                        </div>

                        <?php endif; ?>

                        <div class="row pasien-rs" style="display: none;">
                            <div class="col-sm-5">
                                <?php
                                echo $form
                                     ->field($tipePasien, 'pendaftaran_id',[
                                        'horizontalCssClasses' => [
                                            'label' => 'col-md-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                     ])
                                     ->widget(Select2::classname(), [
                                        'options' => [
                                            'placeholder'=>Yii::t('fe','Masukkan No. Pendaftaran')
                                        ],
                                        'pluginOptions' => [
                                            'minimumInputLength' => 3,
                                            'ajax' => [
                                                'url' => \yii\helpers\Url::to(['/pendaftaran/daftar-penunjang/search-pasien-select2']),
                                                'dataType' => 'json',
                                                'delay' => 300,
                                                'data'=> new JsExpression('function(params) {
                                                    var query = {
                                                        no_pendaftaran: params.term
                                                    }

                                                    return query;
                                                }'),
                                                'processResults'=> new JsExpression('function(result) {
                                                    _pasien = result.data;
                                                    return {
                                                        results: result.list
                                                    }
                                                }')
                                            ],
                                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                            'templateResult' => new JsExpression(
                                                'function(result) {
                                                    return result.text;
                                                }'),
                                            'templateSelection' => new JsExpression(
                                                'function(result) {
                                                    return result.text;
                                                }')
                                        ]
                                     ]);
                                ?>
                                <input type="hidden" name="pendaftaran_id" class="form-control">
                                <input type="hidden" name="pasien_id">

                                <?php
                                echo $form
                                     ->field($tipePasien, 'pasien',[
                                        'horizontalCssClasses' => [
                                            'label' => 'col-md-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                     ])->textInput(['readonly'=>true]);
                                ?>
                                <?php
                                echo Html::hiddenInput('pasienrs_pasien_id_hidden', '', ['id' => 'pasienrs_pasien_id_hidden']);
                                echo Html::hiddenInput('pasienrs_carabayar_id_hidden', '', ['id' => 'pasienrs_carabayar_id_hidden']);
                                echo Html::hiddenInput('pasienrs_penjamin_id_hidden', '', ['id' => 'pasienrs_penjamin_id_hidden']);
                                echo Html::hiddenInput('pasienrs_group_carabayar_hidden', '', ['id' => 'pasienrs_group_carabayar_hidden']);
                                echo Html::hiddenInput('pasienrs_no_pendaftaran_hidden', '', ['id' => 'pasienrs_no_pendaftaran_hidden']);
                                ?>


                                <?php
                                echo $form
                                     ->field($tipePasien, 'dokter_perujuk',[
                                        'horizontalCssClasses' => [
                                            'label' => 'col-md-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                     ])
                                     ->widget(Select2::classname(), [
                                        'options' => [
                                            'placeholder'=>Yii::t('fe','Pilih Dokter')
                                        ],
                                        'pluginOptions' => [
                                            'minimumInputLength' => 3,
                                            'ajax' => [
                                                'url' => \yii\helpers\Url::to(['/pendaftaran/daftar-penunjang/list-dokter']),
                                                'dataType' => 'json',
                                                'delay' => 300,
                                                'data'=> new JsExpression('function(params) {
                                                    var query = {
                                                        search: {
                                                            value: params.term
                                                        },
                                                        "advanced-filter[nama_pegawai]": params.term
                                                    }
                                                    return query;
                                                }'),
                                                'processResults'=> new JsExpression('function(result) {
                                                    return {
                                                        results: result
                                                    }
                                                }')
                                            ],
                                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                            'templateResult' => new JsExpression(
                                                'function(result) {
                                                    return result.text;
                                                }'),
                                            'templateSelection' => new JsExpression(
                                                'function(result) {
                                                    return result.text;
                                                }')
                                        ]
                                     ]);
                                ?>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$instalasi = 0;
if(!is_array($instalasi_id)){
    $instalasi = $instalasi_id;
}
$this->registerJs("
    var instalasiMcu = '".DocoConstants::INSTALASI_MCU."';
    var instalasi = '".$instalasi."';
", View::POS_END);

$this->registerJs($this->render('js/index.js'), View::POS_END);
$this->registerJs($this->render('js/shortcut-tab.js'), View::POS_END);
$this->registerJs($this->render('js/bpjs-helper.js'), View::POS_END);
$this->registerJs($this->render('js/pasien-rs.js'), View::POS_END);
?>