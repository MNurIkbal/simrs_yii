<?php

/**
 * @author Rizal
 * @description UI Update Pendaftaran Rajal
**/

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use kartik\typeahead\Typeahead;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$disabled = $isOnlyCreateSep ? true : false;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
            </div>

            <div class="panel panel-white">
                <div class="panel-toolbar clearfix">
                    <?= Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'btn-save'
                    ]) ?>
                    <?= Html::button('<b><i class="fa fa-arrow-left"></i></b>' . \Yii::t('fe', 'Kembali'), [
                        'class' => 'btn btn-labeled btn-xs btn-info',
                        'id' => 'btn-kembali'
                    ]) ?>
                    <?= Html::button('<b><i class="fa fa-pencil"></i></b>' . \Yii::t('fe', 'Buat SEP'), [
                        'class' => 'btn btn-labeled btn-xs btn-info',
                        'id' => 'btn-create-sep'
                    ]) ?>
                    <?php
                    if($notMcuPenunjang):
                        echo  Html::button('<b><i class="fa fa-history"></i></b>' . Yii::t('fe','Log Pendaftaran'),[
                            'id' => 'btn-log-pendaftaran',
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'tabindex' => -1,
                            'action' => Url::home().'pendaftaran/informasi-pasien/riwayat-perubahan-data-pendaftaran?id='.$primaryKey,
                            'data-width' => '50%',
                        ]);
                    endif;
                    ?>
                </div>
            </div>

            <div class="panel-body" style="padding:10px;">
                <!--Informasi Pasien-->
                <div class="row row-eq-height " style="margin-top:10px;">
                    <div class="col-md-12" id="informasi">
                        <div class="panel panel-default">
                            <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="false" aria-controls="infopasien" >
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><?= Yii::t('fe', 'Informasi Pasien') ?></h6>

                                    <p class="p-data" id="data-pasien">
                                        <?= isset($model->no_rekam_medik) ? $model->no_rekam_medik : '-' ?> -
                                        <b class="font" ><?= isset($model->nama_pasien) ? $model->nama_pasien : '-' ?></b>

                                    </p>

                                    <ul class="icons-list">
                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                    </ul>

                                </div>
                            </a>

                            <div class="panel-body collapse multi-collapse info-card" id="infopasien">
                                <div class="col-xs-2">
                                    <div class="border-img">
                                        <?php
                                        $filename = isset($model->photopasien) ? !empty($model->photopasien) ? '/media/img/pasien/'.$model->photopasien: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                                        ?>
                                        <?=Html::img($filename, [ 'style'=>'width: 100%;height: auto;max-width: 114px;', 'class'=>'img-responsive'])?>
                                    </div>
                                </div>

                                <div class="col-xs-9">
                                    <div class="row">
                                        <br>
                                        <div class="col-xs-6">
                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Pasien") ?></b>
                                            <br>
                                            <p>
                                                <?= isset($model->no_rekam_medik) ? $model->no_rekam_medik : '-' ?> -
                                                <?= isset($model->nama_pasien) ? $model->nama_pasien : '-' ?> -
                                                <?= isset($model->jenis_kelamin) ? $model->jenis_kelamin : '-' ?>
                                            </p>

                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Tanggal Lahir") ?></b>
                                            <p>
                                                <?= isset($model->tanggal_lahir) ? date('d-M-Y', strtotime($model->tanggal_lahir)) : '-' ?>
                                            </p>

                                        </div>
                                        <div class="col-xs-6">
                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Pendaftaran") ?></b>
                                            <p>
                                                <?= isset($model->no_pendaftaran) ? $model->no_pendaftaran : '-' ?> -
                                                <?php if($model->jenis == 'ranap') : ?>
                                                (<?= isset($model->tgl_admisi) ? date('d-M-Y H:i:s', strtotime($model->tgl_admisi)) : '-' ?>)
                                                <?php else : ?>
                                                (<?= isset($model->tgl_pendaftaran) ? date('d-M-Y H:i:s', strtotime($model->tgl_pendaftaran)) : '-' ?>)
                                                <?php endif; ?>
                                            </p>

                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Kelas pelayanan") ?></b>
                                            <p>
                                                <?= isset($model->kelaspelayanan_nama) ? $model->kelaspelayanan_nama : '-' ?> -
                                                <?= isset($model->carabayar_nama) ? $model->carabayar_nama : '-' ?> -
                                                <?= isset($model->penjamin_nama) ? $model->penjamin_nama : '-' ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end-->
            <?php
                $form = ActiveForm::begin([
                    'id' => 'form-update-rajal',
                    'type' => ActiveForm::TYPE_VERTICAL,
                    'enableClientValidation'=>false,
                    'enableAjaxValidation'=>false,
                ]);
            ?>

            <?= Html::activeHiddenInput($model, 'pendaftaran_id')?>
            <?= Html::activeHiddenInput($model, 'group_carabayar')?>
            <?= Html::activeHiddenInput($model, 'jenis', ['id' => 'jenis']); ?>
            <?= Html::hiddenInput('bpjsKelas', "", ['id' => 'bpjsKelas']); ?>
            <?= Html::hiddenInput('EditPendaftaranForm[allow-bpjs]', 0, ['id' => 'allow-bpjs']); ?>

            <fieldset class="content-group">
                <?php if ($disabledCaraBayarPenjamin) : ?>
                    <div class="alert alert-info" role="alert" visible="false">
                        Pasien sudah pernah melakukan pembayaran, perubahan hanya dapat dilakukan pada field Dokter DPJP dan Jenis Kasus Penyakit.
                    </div>
                <?php endif; ?>
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-6">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Ubah Pendaftaran Pasien</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">

                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?php if($jenis_pendaftaran == 'ranap') : ?>
                                <?php echo Yii::$app->controller->renderPartial('form_update_ranap', [
                                    'form' => $form,
                                    'model' => $model,
                                    'penyakitList' => $penyakitList,
                                    'kelasList' => $kelasList,
                                    'dokterList' => $dokterList,
                                    'instalasi_id' => $instalasi_id,
                                    'masterWarnaTempatTidur' => $masterWarnaTempatTidur,
                                    'disabled' => $disabled,
                                    'status_periksa' => $status_periksa,
                                    'jeniskelamin_id' => $jeniskelamin_id,
                                    'class' => $class,
                                    'status_periksa' => $status_periksa,
                                    'disabledCaraBayarPenjamin' => $disabledCaraBayarPenjamin
                                ]) ?>
                                <?php else : ?>
                                <?php echo Yii::$app->controller->renderPartial('form_update_rajal', [
                                    'form' => $form,
                                    'model' => $model,
                                    'penyakitList' => $penyakitList,
                                    'dokterList' => $dokterList,
                                    'disabled' => $disabled,
                                    'nomorUrutList' => $nomorUrutList,
                                    'isNomorUrut' => $isNomorUrut,
                                    'jenis_pendaftaran' => $jenis_pendaftaran,
                                    'ruanganList' => $ruanganList,
                                    'disabledCaraBayarPenjamin' => $disabledCaraBayarPenjamin
                                ]) ?>
                                <?php endif; ?>

                                <?= $form->field($model, 'carabayar_id')
                                ->dropDownList($carabayarList, [
                                    'class' => 'select2 selectCarabayar carabayar-select2',
                                    'id'=>'selectCarabayar',
                                    'prompt' => '-',
                                    'disabled' => $disabled,
                                    'options'=> $carabayarOptions
                                ]); ?>
                                <?= $form->field($model, 'penjamin_id')->widget(DepDrop::classname(), [
                                    'options' => [
                                        'id' => 'penjamin_id',
                                        'class' => 'form-control select2',
                                        'disabled' => $disabled,
                                    ],
                                    'data'=>$penjaminList,
                                    'pluginOptions'=>[
                                        'depends'=>['selectCarabayar'],
                                        'initialize' => true,
                                        'loadingText' => Yii::t('fe', 'Memuat...'),
                                        'placeholder'=>'--Pilih Penjamin--',
                                        'url'=>Url::to(['/master/penjamin/list-penjamin'])
                                    ]
                                ]); ?>
                                <?php
                                    if($showReferral) :

                                        echo $form->field($model, 'referal', [
                                            'options' => [
                                                'class' => 'form-group '.($konfigReferralRequired ? 'required' : '')
                                            ],
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4 '.($konfigReferralRequired ? 'has-star' : ''),
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->dropDownList($listReferral, [
                                            'class' => 'select2 selectReferal',
                                            'id'=>'referal',
                                            'prompt' => '-',
                                            'data-urutan' => 1,
                                        ]);
                                        echo Html::hiddenInput('konfig_referral_required', $konfigReferralRequired);
                                    endif;
                                ?> 
                                <?= $form->field($model, 'nosep', [
                                    'inputOptions' => [
                                        'id' => 'no_sep',
                                        'readonly' => !empty($model->nosep) ? true : false,
                                        'style' => 'text-transform: uppercase',
                                        'data-required' => ($controller->_is_SEP_mandatory ? 1 : 0),
                                        'disabled' => $disabled,
                                    ],
                                    'addon' => [
                                        'append' => [
                                            [
                                                'content' =>Html::button('<i class="fa fa-search"></i> Cari No SEP', [
                                                    'class'=>'btn btn-default',
                                                    'id' => 'cari-nosep',
                                                ]),
                                                'asButton' => true
                                            ],
                                        ],
                                    ],
                                ])->hint('<div class="text-danger err-nosep"></div>');
                                ?>

                                <?= $form->field($modelAsuransi, 'nokartuasuransi', [
                                    'options' => [
                                        'class' => 'required',
                                        'disabled' => $disabled,
                                    ],
                                    'addon' => ['append' => [
                                            'content' => '<i class="fa fa-refresh"></i>'
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
                                                        if(_penjamin == "") {
                                                            docoNotification("warning", "Peringatan", "Penjamin harus dipilih terlebih dahulu!");

                                                            return false;
                                                        }
                                                        return url + uriEncodedQuery +"&penjamin_id=" + encodeURIComponent(_penjamin);
                                                    }')
                                                ]
                                            ]
                                        ],
                                        'pluginEvents' => [
                                            "typeahead:selected" => "function(obj, item) {
                                                console.log(item);
                                                var _no_asuransi = item.value.split(' - ');
                                                var no_asuransi = '';
                                                var nama = '';
                                                var tgl_lahir = '';
                                                var _tgl_konfirmasi = '';

                                                if(item.tgl_konfirmasi != null) {
                                                    var tgl_konfirmasi = item.tgl_konfirmasi.split(/[- :]/);

                                                    var tgl = tgl_konfirmasi[2];
                                                    var bln = tgl_konfirmasi[1];
                                                    var thn = tgl_konfirmasi[0];
                                                    _tgl_konfirmasi = tgl + '-' + bln + '-' + thn;
                                                }

                                                if(_no_asuransi.length != 0) {
                                                    no_asuransi = _no_asuransi[0];
                                                    nama = _no_asuransi[1];
                                                    tgl_lahir = _no_asuransi[2];
                                                }

                                                $('#no_asuransi_hidden').val(no_asuransi);
                                                $('#nama_pemilik_asuransi').val(nama);
                                                $('#asuransiform-namapemilikasuransi').val(item.namapemilikasuransi);
                                                $('#asuransiform-nomorpokokperusahaan').val(item.nomorpokokperusahaan);
                                                $('#asuransiform-kelastanggungan_id').val(item.kelastanggunganasuransi_id).trigger('change');
                                                $('#asuransiform-namaperusahaan').val(item.namaperusahaan);
                                                $('#tgl_konfirmasi').val(_tgl_konfirmasi);
                                                $('#asuransipasien_id').val(item.asuransipasien_id);
                                                $('#asuransiform-nokartuasuransi').prop('readonly', true);
                                            }",
                                        ]
                                    ])->textInput([
                                        'placeholder' => $modelAsuransi->getAttributeLabel('nokartuasuransi'),
                                        'class' => 'form-control input-sm typeahead',
                                        'readonly' => !empty($modelAsuransi->nokartuasuransi) ? true : false
                                    ])->hint('<span id="note-asuransi" style="color:green;"></span>');
                                ?>

                                <div class="form-group chkbox-multi">
                                <?php $form_multi = $form->field($model, 'is_multi_payer', [
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
                                <div class="text-warning" id="multiPayerInfoMessage" style="display:none">*Multipayer hanya bisa jika main payer bukan umum</div>
                                </div>

                                <div class="form-group form-multi-carabayar" id="form-multi-carabayar-content" style="display:none">
                                    <?php
                                    echo $form->field($multiPayer, 'add_carabayar_id_1', [
                                        'horizontalCssClasses' => [
                                            'label' => 'col-md-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->dropDownList($carabayarSecondList, [
                                        'class' => 'select2 selectCarabayar2 carabayar-select2',
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
                                        'data'=>$penjaminList,
                                        'pluginOptions'=>[
                                            'depends'=>['addSelectCarabayar2'],
                                            'initialize' => true,
                                            'loadingText' => Yii::t('fe', 'Memuat...'),
                                            'placeholder'=>'--Pilih Penjamin--',
                                            'url'=>Url::to(['/master/penjamin/list-penjamin'])
                                        ]
                                    ]);


                                    echo $form->field($multiPayer, 'add_no_asuransi_1', [
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-7'
                                        ],
                                        'options' => [
                                            'class' => 'required',
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
                                                            let _penjamin = $("#add_penjamin_id_1").val();
                                                            if(_penjamin == "") {
                                                                docoNotification("warning", "Peringatan", "Penjamin harus dipilih terlebih dahulu!");

                                                                return false;
                                                            }
                                                            return url + uriEncodedQuery +"&penjamin_id=" + encodeURIComponent(_penjamin);
                                                        }')
                                                    ]
                                                ]
                                            ],
                                            'pluginEvents' => [
                                                "typeahead:selected" => "function(obj, item) {
                                                    let _no_asuransi = item.value.split(' - ');
                                                    let no_asuransi = '';
                                                    let nama = '';
                                                    let tgl_lahir = '';
                                                    let _tgl_konfirmasi = '';

                                                    if(item.tgl_konfirmasi != null) {
                                                        var tgl_konfirmasi = item.tgl_konfirmasi.split(/[- :]/);

                                                        let tgl = tgl_konfirmasi[2];
                                                        let bln = tgl_konfirmasi[1];
                                                        let thn = tgl_konfirmasi[0];
                                                        _tgl_konfirmasi = tgl + '-' + bln + '-' + thn;
                                                    }

                                                    if(_no_asuransi.length != 0) {
                                                        no_asuransi = _no_asuransi[0];
                                                        nama = _no_asuransi[1];
                                                        tgl_lahir = _no_asuransi[2];
                                                    }

                                                    $('#no_asuransi_hidden').val(no_asuransi);
                                                    $('#multicarabayarform-add_no_asuransi_1').val(nama);
                                                    $('#multicarabayarform-add_namapemilikasuransi_1').val(item.namapemilikasuransi);
                                                    $('#multicarabayarform-add_nomorpokokperusahaan_1').val(item.nomorpokokperusahaan);
                                                    $('#multicarabayarform-add_kelastanggungan_id_1').val(item.kelastanggunganasuransi_id).trigger('change');
                                                    $('#multicarabayarform-add_namaperusahaan_1').val(item.namaperusahaan);
                                                    $('#multicarabayarform-add_tgl_konfirmasi_1').val(_tgl_konfirmasi);
                                                    $('#asuransipasien_id').val(item.asuransipasien_id);
                                                    $('#multicarabayarform-nokartuasuransi').prop('readonly', true);
                                                }",
                                            ]
                                        ])->textInput([
                                            'placeholder' => $multiPayer->getAttributeLabel('no_asuransi'),
                                            'class' => 'form-control input-sm typeahead',
                                            'readonly' => !empty($multiPayer->add_no_asuransi_1) ? true : false
                                        ])->hint('<span id="first-note-asuransi" style="color:green;"></span>');
                                    ?>
                                  </div>

                                <?= $form->field($model, 'keterangan')->textArea(['disabled' => $disabled,], ['rows' => '6']);
                                ?>

                                <?php if (isset($isLimitTagihan) && $isLimitTagihan) { ?>
                                    <?= $form->field($model, 'limit_tagihan', [
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
                                            'placeholder' => $model->getAttributeLabel('limit_tagihan'),
                                            'class' => 'form-control input-sm doco-number',
                                            'id' => 'limit_tagihan',
                                            'disabled' => $disabled,
                                    ]) ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 detail_peserta">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Informasi Peserta</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?php echo Yii::$app->controller->renderPartial('_form_bpjs', [
                                    'model' => $model,
                                    'nama_peserta' => $nama_peserta,
                                    'no_kartu' => $no_kartu,
                                    'jenis_pelayanan' => $jenis_pelayanan,
                                    'poli_tujuan' => $poli_tujuan,
                                    'kelas_rawat' => $kelas_rawat,
                                    'tgl_lahir' => $tgl_lahir,
                                    'nmjenispeserta' => $nmjenispeserta
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 detail_asuransi">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Informasi Asuransi</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?php echo Yii::$app->controller->renderPartial('_form_asuransi', [
                                    'form' => $form,
                                    'modelAsuransi' => $modelAsuransi,
                                    'kelasList' => $kelasList,
                                    'disabledCaraBayarPenjamin' => $disabledCaraBayarPenjamin
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 detail_asuransi-2">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Informasi Asuransi 2</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?php echo Yii::$app->controller->renderPartial('_form_asuransi_multicarabayar', [
                                    'form' => $form,
                                    'modelMultiCaraBayar' => $multiPayer,
                                    'kelasList' => $kelasList,
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<div id="modal_log_pendaftaran" class="modal fade" style="z-index:1064;" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Log Pendaftaran</h5>
            </div>
            </div>
        </div>
    </div>

<div id="modal_progress" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Rekalkulasi Transaksi</h5>
            </div>
            <div class="modal-body">
                <hr class="hr1 hidden">
                <span class="help-block label-total"></span>
                <span class="help-block label-processed"></span>
                <span class="help-block label-time"></span>
                <hr class="hr2 hidden">
                <span class="help-block label-tindakan-obat"></span>
                <span class="help-block label-finish"></span>
                <button type="button" class="btn-selesai hidden btn-info btn-sm" data-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>

<div id="modal_validation" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title text-center">Konfirmasi Ubah Penjamin Integrasi</h5>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <h5>Pasien Asuransi Ter-Integrasi</h5>
                    <div style="padding: 10px; display: flex; justify-content: center">
                        <div style="width: 70%;" class="text-center">
                            <h5>Untuk mengubah data penjamin diharuskan ubah penjamin pada dashboard informasi pasien asuransi terintegrasi</h5>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-danger" data-dismiss="modal"><b>Kembali</b></button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-block btn-success" data-dismiss="modal"><b>Confirm</b></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
var _penjamin_id = '".$model->penjamin_id."';
var _nosep = '".$model->nosep."';
var _noasuransi = '".$modelAsuransi->nokartuasuransi."';
var _namapemilik = '".addslashes($modelAsuransi->namapemilikasuransi)."';
var _namaperusahaan = '".addslashes($modelAsuransi->namaperusahaan)."';
var _nomorpokokperusahaan = '".addslashes($modelAsuransi->nomorpokokperusahaan)."';
var _kelastanggungan_id = '".$modelAsuransi->kelastanggungan_id."';
var _tgl_konfirmasi = '".$modelAsuransi->tgl_konfirmasi."';
var _status_konfirmasi = '".$modelAsuransi->status_konfirmasi."';
var ruangan_id = '".$model->ruangan_id."';
var primaryKey = '".$primaryKey."';
var supportMultipayer = '".isset($support_multipayer)."';
var jenisPendaftaran = '".$jenis_pendaftaran."';
var selectedCarabayar = [];
var modelMultiPayer = '".json_encode($multiPayer->attributes)."';
var caraBayarGroupUmum = '".DocoConstants::GROUP_UMUM."';
var pendaftaranId = '".$id."';
var disabledCaraBayarPenjamin = '".$disabledCaraBayarPenjamin."';
", View::POS_END);
$this->registerJs($this->render('js/form_update.js'), View::POS_END);
?>
