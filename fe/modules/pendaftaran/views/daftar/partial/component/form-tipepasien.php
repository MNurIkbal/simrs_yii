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
        min-width:75px !important;
        text-align:left !important;
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
        float:left;
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
        display:none;
        margin: 10px;
    }

    .inputfile + label {
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
    .no-js .inputfile + label {
        display: none;
    }

    .inputfile:focus + label,
    .inputfile.has-focus + label {
        outline: 1px dotted #000;
        outline: -webkit-focus-ring-color auto 5px;
    }

    .inputfile + label * {
        /* pointer-events: none; */
        /* in case of FastClick lib use */
    }

    .inputfile + label svg {
        width: 1em;
        height: 1em;
        vertical-align: middle;
        fill: currentColor;
        margin-top: -0.25em;
        /* 4px */
        margin-right: 0.25em;
        /* 4px */
    }
    .inputfile-1 + label {
        color: #ffff;
        background-color: #4CAF50;

    }
    .inputfile-1:focus + label,
    .inputfile-1.has-focus + label,
    .inputfile-1 + label:hover {
        background-color: #078448;
    }
    .my-legend-cst .legend-title-cst {
        margin-bottom: 8px ;
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
        padding:4px 10px;
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

    .tambah-cara-bayar {
        margin-left: 0px;
        margin-top: 15px;
    }
    .tambah-cara-bayar-pasien {
        margin-left: 20px;
        margin-top: -5px;
    }

    .hapus-cara-bayar {
        margin-left: -30px;
        margin-top: 15px;
    }
    .chkbox-multi {
        margin-left: 13px;
    }

    .form-multi-carabayar {
        margin-left: 70px;
    }

    .cs1{
        margin-left: -20px;
    }
    .panel-info-btn {
        padding: 5px 13px 0px 13px;
    }
</style>
<div class="row">
    <div class="form-group">
        <div class="col-md-3" id="form-parent-info" style="display: none;">
            <div class="content-group">
                <div class="bg-indigo-300 text-right border-radius-top panel-info-btn">
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
                        <a href="#kunjungan" id="informasi-data-kunjungan" class="text-size-small text-uppercase text-semibold" data-toggle="tab" aria-expanded="false">
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
                        </div><hr>
                        <div align="center">
                        <button type="button" class="btn btn-detail-bpjs btn-info btn-labeled btn-xs" action="<?= Url::home() ?>pendaftaran/daftar-igd/detail-history-bpjs?no_kartu=" data-toggle="modal" data-target="#modal_backdrop" data-width="90%" style="display:none"><b><i class="fa fa-eye"></i></b>History BPJS</button></div>
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
                        <?php
                            if (!empty($is_penunjang)) :
                            $jenis = ['1'=>'APS', '0'=>'Pasien RS'];
                            $tipePasien->is_aps = 1;
                        ?>
                        <div class="row">
                            <div class="col-sm-3">
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
                            <div class="col-sm-3">
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
                            <div class="row chkbox-multi">
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
                            <?php 
                                if (@$is_Igd == true) {
                                    $form_bayi = $form->field($tipePasien, 'is_bbl', [
                                    'options' => [
                                        'tag' => false,
                                    ],
                                    ])->checkbox([
                                        'label' => 'Pendaftaran Bayi',
                                        'value' => 1,
                                        'class' => 'styled action-checked',
                                        'data-urutan' => 1
                                    ]);
                                    echo @$is_Igd == true ? $form_bayi : $form_bayi->hiddenInput(); 
                                }
                            ?>
                            </div>

                            <div class="col-sm-3">
                                <?php
                                echo Html::hiddenInput('pasien_id_hidden', '', ['id' => 'pasien_id_hidden']);
                                echo Html::hiddenInput('pasien_ol_status', empty($penOl['status_pasien']) ? "" : $penOl['status_pasien'], ['id' => 'pasien_ol_status']);
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

                                ]);

                                echo $form->field($tipePasien, 'penjamin_id', [
                                    'horizontalCssClasses' => [
                                        'label' => 'col-md-4',
                                        'wrapper' => 'col-md-8'
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
                                        'url' => Url::to(['daftar/get-penjamin?selected='.(isset($penOl['penjamin_id']) ? $penOl['penjamin_id'] : null)])
                                    ],
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
                                ]);
                                ?>
                                <?php
                                    $isRanap = 0;
                                    $dataOptions = [];
                                    if (!empty($penOl)) {
                                        $noRm = isset($penOl['no_rekam_medik']) ? $penOl['no_rekam_medik'] : null;
                                        $value = $noRm;
                                        $value .= ' / ' . (isset($penOl['nama_pasien']) ? ($penOl['nama_pasien']) : null);
                                        $value .= ' / ' . (isset($penOl['tanggal_lahir']) ? ($penOl['tanggal_lahir']) : null);
                                        $dataOptions[$noRm] = $value;
                                    }
                                    echo $form->field($tipePasien, 'no_asuransi', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-7',
                                    ],
                                    'options' => [
                                        'class' => $instalasi_workspace != DocoConstants::WS_IGD ? 'required' : '',
                                    ],
                                    'addon' => ['append' => [
                                            'content' => '
                                            <div style="display: flex; align-items: center">
                                                <div>
                                                    <i class="fa fa-refresh" style="margin-right: 15px" id="refresh-asuransi"></i>
                                                </div>
                                                <div style="display: inline-block; border-left: 1px solid black; height: 15px; margin-right: 10px"></div>
                                                <div>
                                                    <button style="padding-left: 8px !important; min-width:0px !important" class="btn-xs btn bg-info" disabled type="button"
                                                        action="/pendaftaran/daftar/pencarian-asuransi" 
                                                        data-toggle="modal" 
                                                        data-target="#modal_pencarian_lanjutan" data-width="1000px" 
                                                        data-popup="tooltip"
                                                        id="btn-pencarian-asuransi"><i class="fa fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>'
                                        ]
                                    ]])->widget(Typeahead::classname(),[
                                        'pluginOptions' => [
                                            'highlight' => true,
                                            'minLength' => 3,
                                            'limit' => 10
                                        ],
                                        'dataset' => [
                                            [
                                                'limit' => 10,
                                                'display' => 'value',
                                                'remote' => [
                                                    'url' => Url::to(['/pendaftaran/daftar-rajal/get-asuransi']) . '?q=',
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
                                                $('.field-tipepasienform-no_asuransi').find('#note-asuransi').html('<i>Atas Nama : ' + item.namapemilikasuransi + '<br>Pasien Pengguna Asuransi : ' + item.nama_pasien + '<br> Nomor Rekam Medik Pasien : ' + item.no_rekam_medik);

                                                Object.assign(_formPendaftaran.tmpAsuransi, item);
                                                $('#tipepasienform-no_asuransi').prop('readonly', false);
                                            }",
                                        ]
                                    ])->textInput([
                                        'placeholder' => $tipePasien->getAttributeLabel('no_asuransi'),
                                        'class' => 'form-control input-sm typeahead',
                                        'autocomplete' => "off"
                                    ])->hint('<span id="note-asuransi" style="color:green;"></span>');
                                ?>
                                <?php echo $form->field($tipePasien, 'no_rekam_medik', [
                                    'horizontalCssClasses' => [
                                        'label' => 'col-md-4',
                                        'wrapper' => 'col-md-8'
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
                                    ])->dropDownList($dataOptions, [
                                        'class' => 'select2',
                                        'prompt' => '-',
                                        'id' => 'no_rekam_medik',
                                        'placeholder' => Yii::t('fe','No rm').' / '.Yii::t('fe', 'Nama pasien'). ' / ' .Yii::t('fe', 'Tanggal lahir')
                                    ]);
                                ?>
                            </div>

                            <div class="col-sm-3 form-multi-carabayar" id="form-multi-carabayar" style="display: none;">
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
                                            'id'=>'addSelectCarabayar2',
                                            'prompt' => '-',
                                            'options'=> $carabayarOptions,
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

                                        // echo $form->field($multiPayer, 'add_asalrujukan_id_1', [
                                        //     'horizontalCssClasses' => [
                                        //         'label' => 'col-md-4',
                                        //         'wrapper' => 'col-md-8'
                                        //     ]
                                        // ])->dropDownList(ArrayHelper::map($asal_rujukan, 'asalrujukan_id', 'asalrujukan_nama'), [
                                        //     'class' => 'select2 selectRujukan',
                                        //     'prompt' => '-',
                                        //     'id'=>'add_asalrujukan_id_1',
                                        //     'disabled' => ($instalasi_id == DocoConstants::INSTALASI_MCU) ? false : false,
                                        // ]);

                                        echo $form->field($multiPayer, 'add_no_asuransi_1', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7'
                                            ],
                                            'options' => [
                                                'class' => $instalasi_workspace == DocoConstants::WS_IGD ? '' : 'required',
                                                'style' => 'display: none'
                                            ],
                                            'addon' => ['append' => [
                                                    'content' => '<i class="fa fa-refresh" id="refresh-first-asuransi"></i>'
                                                ]
                                            ]])->widget(Typeahead::classname(),[
                                                'pluginOptions' => [
                                                    'highlight' => true,
                                                    'minLength' => 3,
                                                    'limit' => 10
                                                ],
                                                'dataset' => [
                                                    [
                                                        'limit' => 10,
                                                        'display' => 'value',
                                                        'remote' => [
                                                            'url' => Url::to(['/pendaftaran/daftar-rajal/get-asuransi']) . '?q=',
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

                            <div class="col-sm-1 form-multi-carabayar cs1 "style="display: none;">
                            <!-- <hr> -->
                                <div class="form-group highlight-addon has-size-sm">
                                    <label class="control-label"><b style="color:white;">Aksi</b></label>
                                    <?= Html::button('+', ['class' => 'btn btn-success tambah-cara-bayar']); ?>
                                    <div class="help-block"></div>
                                </div>
                            </div>

                            <div class="col-sm-3 form-multi-carabayar-second" id="form-multi-carabayar-second" style="display: none;">
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
                                            'id'=>'addSelectCarabayar3',
                                            'prompt' => '-',
                                            'options'=> $carabayarOptions,
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
                                                'class' => $instalasi_workspace == DocoConstants::WS_IGD ? '' : 'required',
                                                'style' => 'display: none'
                                            ],
                                            'addon' => ['append' => [
                                                    'content' => '<i class="fa fa-refresh" id="refresh-second-asuransi"></i>'
                                                ]
                                            ]])->widget(Typeahead::classname(),[
                                                'pluginOptions' => [
                                                    'highlight' => true,
                                                    'minLength' => 3,
                                                    'limit' => 10
                                                ],
                                                'dataset' => [
                                                    [
                                                        'limit' => 10,
                                                        'display' => 'value',
                                                        'remote' => [
                                                            'url' => Url::to(['/pendaftaran/daftar-rajal/get-asuransi']) . '?q=',
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


                        <?php
                            if (!is_array($instalasi_id) && $instalasi_id == DocoConstants::INSTALASI_MCU) :
                        ?>
                        <div id="row-template" class="row" hidden>
                            <div class="col-sm-1" style="margin-left: 0;">
                                <button style="background:#4CAF50 !important;width:150px !important"type="button" id="unduh-template" class="btn btn-success btn-labeled btn-sm" data-target="" data-options="click"><b><i class="fa fa-file-excel-o"></i></b>Unduh Template</button>
                            </div>
                            <div class="col-sm-3" style="margin-left: 60px;">
                                <input type="file" name="TipePasienForm[upload_file]" id="file-upload" class="form-control inputfile inputfile-1" data-multiple-caption="{count} files selected">
                                <label for="file-upload">
                                    <i class="fa fa-upload"></i>
                                    <span id="label-file">Unggah Berkas</span>
                                </label>
                                <p class="format-upload">format :xls, xlsx</p>
                            </div>
                        </div>

                        <?php endif; ?>

                        <div class="row pasien-rs" style="display: none;">
                            <div class="col-sm-3">
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
                                            'placeholder'=>Yii::t('fe','No. Pendaftaran / No. Rekam Medik')
                                        ],
                                        'pluginOptions' => [
                                            'minimumInputLength' => 3,
                                            'ajax' => [
                                                'url' => \yii\helpers\Url::to(['/pendaftaran/daftar-penunjang/search-pasien-select2']),
                                                'dataType' => 'json',
                                                'delay' => 300,
                                                'data'=> new JsExpression('function(params) {
                                                    var query = {
                                                        q: params.term
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

                        <div class="row" id="form-bpjs" style="display: none">
                            <div class="col-sm-3">
                                <?php
                                    if(isset($penOl)){
                                        if(isset($penOl['all']['data_bpjs']) || ArrayHelper::getValue($penOl,'carabayar_id') == DocoConstants::CARA_BAYAR_BPJS) {
                                            $modelBpjs->jenis_rujukan = 1;
                                        } else {
                                            $modelBpjs->jenis_rujukan = NULL;
                                        }
                                    }
                                ?>
                                <?= $form->field($modelBpjs, 'jenis_rujukan')->radioList(
                                    [
                                        '1'=> Yii::t('fe', 'Rujukan'),
                                        '2'=> Yii::t('fe', 'Rujukan Manual / IGD'),
                                    ],
                                    [
                                        'id'=>'jenis_rujukan',
                                        'inline' => true,
                                        'item' => function($index, $label, $name, $checked, $value) {
                                            $return = '<label class="radio-inlineo">';
                                                $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" class="styled" '.($checked ? 'checked' : '').'>';
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
                                        'wrapper' => 'col-md-8'
                                    ],
                                    'inputOptions'=>['id'=>'tanggal_sep_1'],
                                    'addon' => [
                                        'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                                    ]
                                ]); ?>

                                <div id="asal_rujukan_manual">
                                    <?php
                                        $modelBpjs->asal_rujukan = 1;
                                        if(isset($penOl)){
                                            if(isset($penOl['all']['data_bpjs'])) {
                                                $dataBpjs = json_decode($penOl['all']['data_bpjs'], true);
                                                $modelBpjs->asal_rujukan = $dataBpjs['asal_rujukan'];
                                            }else if(ArrayHelper::getValue($penOl,'all.jeniskunjungan') == DocoConstants::JENIS_KUNJUNGAN_KONTROL){
                                                $modelBpjs->asal_rujukan = 2;
                                            } else {
                                                $modelBpjs->asal_rujukan = 1;
                                            }
                                        }
                                    ?>
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
                                        );
                                    ?>
                                </div>

                                <div id="base-rujukan">
                                    <?php
                                    $modelBpjs->no_rujukan_f = isset($penOl['all']['no_rujukan']) ? trim($penOl['all']['no_rujukan']) : '';
                                    echo $form->field($modelBpjs, 'no_rujukan_f', [
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ],
                                        'inputOptions'=>['id'=>'no_rujukan', 'style' => 'text-transform: uppercase;'],
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
                                    $modelBpjs->jenis_pelayanan = 2;
                                    echo $form->field($modelBpjs, 'jenis_pelayanan',[
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList(
                                            [
                                                '2'=>Yii::t('fe', 'Rawat Jalan'),
                                                // '1'=>Yii::t('fe', 'Rawat Inap'),
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
                                        'inputOptions'=>['id'=>'no_kartu'],
                                    ])->hint('<div class="text-danger err-no-kartu"></div>');
                                    ?>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <button type="button" class="btn eligible-peserta btn-info btn-labeled btn-xs" style="display: none;"  data-toggle="modal" data-target="#modal_backdrop" data-width="90%" ><b><i class="fa fa-eye"></i></b></button></div>
        </div>
    </div>
</div>
<?php
$instalasi = 0;
if(!is_array($instalasi_id)){
    $instalasi = $instalasi_id;
}
$this->registerJs("
    var instalasiId = '".$instalasi_workspace."';
    var instalasiMcu = '".DocoConstants::INSTALASI_MCU."';
    var instalasi = '".$instalasi."';
    var title_karcis = '".$data_lookup['title_pendaftaran'][0]['lookup_value']."';
    var is_validasipendaftaran = '". (!empty($is_validasipendaftaran) ? 1 : 0) ."';
    var is_Igd = '".@$is_Igd."';
", View::POS_END);

$this->registerJs($this->render('js/index.js'), View::POS_END);
$this->registerJs($this->render('js/shortcut-tab.js'), View::POS_END);
$this->registerJs($this->render('js/bpjs-helper.js'), View::POS_END);
$this->registerJs($this->render('js/pasien-rs.js'), View::POS_END);
?>
