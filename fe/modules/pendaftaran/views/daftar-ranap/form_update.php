<?php

/**
 * @author Rizal
 * @description UI Update Pendaftaran Rajal
**/

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                <?php echo Breadcrumbs::widget([
                      'homeLink' => [ 
                                      'label' => Yii::t('fe', 'Home'),
                                      'url' => Yii::$app->homeUrl,
                                 ],
                      'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                   ]); 
                ?>
            </div>
            <div class="panel-body" style="padding:10px;">
            <?php 
                $form = ActiveForm::begin([
                    'id' => 'form-update-rajal', 
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['showErrors' => false, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
                    'enableClientValidation'=>false,
                    'enableAjaxValidation'=>false,
                ]); 
            ?>
            <fieldset class="content-group">
                <legend class="text-bold">Data Pasien</legend>

                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-6">	
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Detail Pasien</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?= $form->field($model, 'nama_pasien')->textInput(['readonly'=>true]); ?>
                                <?= $form->field($model, 'no_rekam_medik')->textInput(['readonly'=>true]); ?>
                                <?= $form->field($model, 'no_pendaftaran')->textInput(['readonly'=>true]); ?>
                                <?= $form->field($model, 'jeniskasuspenyakit_id')->dropDownList($penyakitList, ['id'=>'jeniskasuspenyakit_id','prompt'=>'— PILIH —']) ?>
                                <?= $form->field($modelPasienAdmisi, 'pegawai_id')->dropDownList($dokterList, ['id'=>'pegawai_id','prompt'=>'—pilih dokter—']) ?>
                                <?= $form->field($modelPasienAdmisi, 'carabayar_id')->dropDownList($carabayarList, ['id'=>'carabayar_id','prompt'=>'— pilih cara bayar —']) ?>
                                <?= $form->field($modelPasienAdmisi, 'penjamin_id')->widget(DepDrop::classname(), [
                                    'options'=>['id'=>'penjamin_id'],
                                    'data'=>$penjaminList,
                                    'pluginOptions'=>[
                                        'depends'=>['carabayar_id'],
                                        'initialize' => true,
                                        'loadingText' => Yii::t('fe', 'Memuat...'),
                                        'placeholder'=>'--Pilih penjamin--',
                                        'url'=>Url::to(['/master/penjamin/list-penjamin'])
                                    ]
                                ]); ?>
                                <div class="text-right">
                                    <?=Html::submitButton('<i class="fa fa-save"></i> '.Yii::t('fe', 'Simpan'), ['class' => 'btn btn-success btn-sm']); ?>
                                    <?=
                                    Html::a(
                                        '<i class="fa fa-back"></i> '.Yii::t('fe', 'Kembali'), 
                                        '/pendaftaran/informasi-pasien/rajal',
                                        ['class' => 'btn bg-slate btn-sm']
                                    );
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 bpjs-panel" style='display:none;'>
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">BPJS</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?= $form->field($modelBpjs, 'nokartuasuransi', [
                                    'inputOptions'=>['id'=>'nokartuasuransi'],
                                    'addon' => [
                                        'append' => [
                                            [
                                                'content' => Html::button(Yii::t('fe', 'Cari peserta'), ['class'=>'btn btn-default cariPeserta']), 
                                                'asButton' => true
                                            ],
                                        ],
                                    ]
                                ])->hint('<div class="text-danger err-nokartuasuransi"></div>'); ?>
                                <?= $form->field($modelBpjs, 'tglsep', [
                                    'inputOptions'=>['id'=>'pickadateSep'],
                                    'addon' => [
                                        'append' => ['content'=>'<i class="glyphicon glyphicon-calendar"></i>'],
                                    ]
                                ]); ?>
                                <?= $form->field($modelBpjs, 'nama_peserta', [
                                    'inputOptions'=>['readonly'=>true],
                                ]); ?>
                                <?= $form->field($modelBpjs, 'kdjenispeserta', [
                                    'inputOptions'=>['readonly'=>true],
                                ]); ?>
                                <?= $form->field($modelBpjs, 'nmjenispeserta', [
                                    'inputOptions'=>['readonly'=>true],
                                ]); ?>
                                <?= $form->field($modelBpjs, 'kdkelastanggungan', [
                                    'inputOptions'=>['readonly'=>true],
                                ]); ?>
                                <?= $form->field($modelBpjs, 'nmkelastanggungan', [
                                    'inputOptions'=>['readonly'=>true],
                                ]); ?>
                                <?= $form->field($modelBpjs, 'klsrawat', [
                                    'inputOptions'=>['readonly'=>true],
                                ]); ?>
                                <?= $form->field($modelBpjs, 'norujukan', [
                                    'inputOptions'=>['id'=>'norujukan'],
                                    'addon' => [
                                        'append' => [
                                            [
                                                'content' => Html::button(Yii::t('fe', 'Cari rujukan'), ['class'=>'btn btn-default cariRujukan']), 
                                                'asButton' => true
                                            ],
                                        ],
                                    ]
                                ])->hint('<div class="text-danger err-norujukan"></div>'); ?>
                                <?= $form->field($modelBpjs, 'tglrujukan', [
                                    'inputOptions'=>['id'=>'pickadateRujukan'],
                                    'addon' => [
                                        'append' => ['content'=>'<i class="glyphicon glyphicon-calendar"></i>'],
                                    ]
                                ]); ?>
                                <?= $form->field($modelBpjs, 'ppkrujukan'); ?>
                                <?= $form->field($modelBpjs, 'nmppkrujukan'); ?>
                                <?= $form->field($modelBpjs, 'ppkpelayanan'); ?>
                                <?= $form->field($modelBpjs, 'jnspelayanan')->widget(Select2::classname(), [
                                    'data' => [
                                        '1'=>Yii::t('fe', 'Rawat inap'),
                                        '2'=>Yii::t('fe', 'Rawat jalan'),
                                    ],
                                    'options' => [
                                        'placeholder' => Yii::t('fe', '--pilih jenis pelayanan--')
                                    ],
                                ]); ?>
                                <?= $form->field($modelBpjs, 'diagnosaawal'); ?>
                                <?php
                                echo $form->field($modelBpjs, 'politujuan')->widget(Select2::classname(), [
                                    'data' => [],
                                    'options' => [
                                        'id' => 'poliTujuan',
                                        'placeholder' => Yii::t('fe', 'Poli tujuan')
                                    ],
                                ]);
                                ?>
                                <?= 
                                $form->field($modelBpjs, 'lakalantas')->checkbox(
                                    [
                                        'label'=>Yii::t('fe', 'Kasus kecelakaan'),
                                    ]
                                ); 
                                ?>
                                <?= $form->field($modelBpjs, 'lokasilaka', [
                                    'options'=>['style'=>'display:none;'],
                                ]); ?>
                                <?= $form->field($modelBpjs, 'catatansep')->textArea([
                                        'placeholder' => Yii::t('fe', 'Catatan sep'), 
                                        'rows' => 4
                                    ]); ?>
                                <hr>
                                <?= $form->field($modelBpjs, 'nosep', [
                                    'inputOptions'=>['id'=>'nosep'],
                                    'addon' => [
                                        'append' => [
                                            [
                                                'content' => Html::button(Yii::t('fe', 'Buat SEP'), ['class'=>'btn btn-info createSep']), 
                                                'asButton' => true
                                            ],
                                            [
                                                'content' => Html::button(Yii::t('fe', 'Cetak SEP'), ['class'=>'btn btn-print printSep', 'style'=>'display:none;']), 
                                                'asButton' => true
                                            ],
                                        ],
                                    ]
                                ])->hint('<div class="text-danger err-nosep"></div>'); ?>
                            </div>
                            <div class="panel-footer">
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

<?php 
$this->registerJs("
    $('#pickadateSep, #pickadateRujukan').pickadate({
        format: 'dd mmm yyyy',
        formatSubmit: 'yyyy-mm-dd',
        onStart: function() {
            var date = new Date();
            this.set('select', [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
        }
    });

    $('#carabayar_id').change(function(){
        if ($('#carabayar_id option:selected').text().split(' - ')[0].trim() == 'BPJS') {
            $('.bpjs-panel').slideDown();
        } else {
            $('.bpjs-panel').slideUp();
        }
    });

    $('.cariPeserta').click(function(){
        var is_ktp = 0;
        if ($('#nokartuasuransi').length > 15) {
            is_ktp = 1;
        }
        $.ajax({
            type: 'POST',
            url: window.location.origin + '/api/bpjs/peserta',
            data: {
                'nokartuasuransi': $('#nokartuasuransi').val(),
                'isktp': is_ktp
            },
            dataType: 'JSON',
            beforeSend: function(){
                var _html = i18next.t('Memuat...');
                $('.cariPeserta').html(_html).attr('disabled', true);
                
                $('#bpjsform-nama_peserta').val('');
                $('#bpjsform-kdjenispeserta').val('');
                $('#bpjsform-nmjenispeserta').val('');
                $('#bpjsform-kdkelastanggungan').val('');
                $('#bpjsform-nmkelastanggungan').val('');
                $('#bpjsform-klsrawat').val('');
                $('.err-nokartuasuransi').html('');
            },
            success: function(res) {
                if (res.metadata.status == 200) {
                    var result = res.response.metadata;
                    if (result.code == 200) {
                        var response = res.response.response;
                        var peserta = response.peserta;

                        $('#bpjsform-nama_peserta').val(peserta.nama);
                        $('#bpjsform-kdjenispeserta').val(peserta.jenisPeserta.kdJenisPeserta);
                        $('#bpjsform-nmjenispeserta').val(peserta.jenisPeserta.nmJenisPeserta);
                        $('#bpjsform-kdkelastanggungan').val(peserta.kelasTanggungan.kdKelas);
                        $('#bpjsform-nmkelastanggungan').val(peserta.kelasTanggungan.nmKelas);
                        $('#bpjsform-klsrawat').val(peserta.kelasTanggungan.kdKelas);

                        $('#bpjsform-jnspelayanan').val(2); // rawat jalan

                        getRujukanPeserta();
                        getListPoli();
                    } else {
                        $('.err-nokartuasuransi').html(result.message);
                    }
                } 
            },
            complete: function() {
                $('.cariPeserta').html(i18next.t('Cari peserta')).removeAttr('disabled');
                $('.select2-container--default').css('width','100'); 
            }
        });
    });

    function getRujukanPeserta() {
        $.ajax({
            type: 'POST',
            url: window.location.origin + '/api/bpjs/rujukan-peserta',
            data: {
                'nokartuasuransi': $('#nokartuasuransi').val()
            },
            dataType: 'JSON',
            beforeSend: function(){
                var _html = i18next.t('Memuat...');
                $('.err-noruhjukan').html('');
            },
            success: function(resRujukan) {
                if (resRujukan.metadata.status == 200) {
                    var result = resRujukan.response.metadata;
                    if (result.code == 200) {
                        var response = resRujukan.response.response;
                        if (response) {
                            var item = response.item;
                            $('#bpjsform-norujukan').val(item.noKunjungan);
                            $('#bpjsform-ppkrujukan').val(item.provKunjungan.kdProvider);
                            $('#bpjsform-nmppkrujukan').val(item.provKunjungan.nmProvider);
                            $('#bpjsform-tglrujukan').val(item.tglKunjungan);
                            $('#bpjsform-ppkpelayanan').val('default RS');
                            $('#bpjsform-diagnosaawal').val(item.diagnosa.kdDiag);
                            $('#bpjsform-politujuan').val(item.poliRujukan.kdPoli);
                        }
                    } else {
                        $('.err-norujukan').html(result.message);
                    }
                } else {
                }
                
            },
            complete: function() {
                $('.cariRujukan').html(i18next.t('Cari rujukan')).removeAttr('disabled');
            }
        });
    }


    function getListPoli() {
        $.ajax({
            type: 'POST',
            url: window.location.origin + '/api/bpjs/list-poli',
            dataType: 'JSON',
            beforeSend: function(){
                var _html = i18next.t('Memuat...');
            },
            success: function(res) {
                if (res.metadata.status == 200) {
                    var result = res.response.metadata;
                    if (result.code == 200) {
                        var response = res.response.response;
                        if (response) {
                            var list = response.list;
                            $('#poliTujuan option').remove();
                            data = [];
                            $.each(list, function(i, item) {
                                data.push({ id: item.kdPoli, text: item.nmPoli});
                            });
                            $('#poliTujuan').select2({
                                // placeholder: i18next.t('--pilih poli--'),
                                data: data,
                                // type: 'GET',
                                // quietMillis: 50,
                                // minimumInputLength: 1,
                            })
                        }
                    } else {
                    }
                } else {
                }
                
            },
            complete: function() {
            }
        });
    }

    $('.cariRujukan').click(function(){
        var isrujukanrs = 0;
        if ($('#nokartuasuransi').length > 15) {
            isrujukanrs = 1;
        }
        $.ajax({
            type: 'POST',
            url: window.location.origin + '/api/bpjs/rujukan',
            data: {
                'norujukan': $('#norujukan').val(),
                'isrujukanrs': isrujukanrs
            },
            dataType: 'JSON',
            beforeSend: function(){
                var _html = i18next.t('Memuat...');
                $('.cariRujukan').html(_html).attr('disabled', true);

                $('#bpjsform-norujukan').val('');
                $('#bpjsform-ppkrujukan').val('');
                $('#bpjsform-nmppkrujukan').val('');
                $('#bpjsform-tglrujukan').val('');
                $('#bpjsform-ppkpelayanan').val('');
                $('#bpjsform-diagnosaawal').val('');
                $('.err-norujukan').html('');

            },
            success: function(res) {
                if (res.metadata.status == 200) {
                    var result = res.response.metadata;
                    if (result.code == 200) {
                        var response = res.response.response;
                        if (response) {
                            var item = response.item;
                            $('#bpjsform-norujukan').val(item.noKunjungan);
                            $('#bpjsform-ppkrujukan').val(item.provKunjungan.kdProvider);
                            $('#bpjsform-nmppkrujukan').val(item.provKunjungan.nmProvider);
                            $('#bpjsform-tglrujukan').val(item.tglKunjungan);
                            $('#bpjsform-ppkpelayanan').val('default RS');
                            $('#bpjsform-diagnosaawal').val(item.diagnosa.kdDiag);
                            $('#bpjsform-politujuan').val(item.poliRujukan.kdPoli);
                        }
                    } else {
                        $('.err-norujukan').html(result.message);
                    }
                }
                
            },
            complete: function() {
                $('.cariRujukan').html(i18next.t('Cari rujukan')).removeAttr('disabled');
            }
        });

    });

    $('#bpjsform-diagnosaawal').select2();  

    $('#bpjsform-lakalantas').click(function(){
        var lakalantas = $(this).prop('checked');
        if (lakalantas == true) {
            $('.field-bpjsform-lokasilaka').slideDown().addClass('form-group');
        } else {
            $('.field-bpjsform-lokasilaka').slideUp().removeClass('form-group');
        }
    });

    $('.createSep').click(function() {
        var nokartuasuransi = $('#nokartuasuransi').val();
        var tglsep = $('#pickadateSep').val();
        var tglrujukan = $('#pickadateRujukan').val();
        var norujukan = $('#norujukan').val();
        var ppkrujukan = $('#bpjsform-ppkrujukan').val();
        var ppkpelayanan = $('#bpjsform-ppkpelayanan').val();
        var jnspelayanan = '2';
        var catatansep = $('#bpjsform-catatansep').val();
        var diagnosaawal = $('#bpjsform-diagnosaawal').val();
        var politujuan = $('#poliTujuan').val();
        var klsrawat = $('#bpjsform-klsrawat').val();
        var lakalantas = $('#bpjsform-lakalantas').prop('checked');
        var lokasilaka = $('#bpjsform-lokasilaka').val();
        var no_rekam_medik = $('#pendaftaranform-no_rekam_medik').val();

        $.ajax({
            type: 'POST',
            url: window.location.origin + '/api/bpjs/create-sep',
            data: {
                nokartuasuransi:nokartuasuransi,
                tglsep:tglsep,
                tglrujukan:tglrujukan,
                norujukan:norujukan,
                ppkrujukan:ppkrujukan,
                ppkpelayanan:ppkpelayanan,
                jnspelayanan:jnspelayanan,
                catatansep:catatansep,
                diagnosaawal:diagnosaawal,
                politujuan:politujuan,
                klsrawat:klsrawat,
                lakalantas:lakalantas,
                lokasilaka:lokasilaka,
                no_rekam_medik:no_rekam_medik,
            },
            dataType: 'JSON',
            beforeSend: function(){
                var _html = i18next.t('Memuat...');
                $('.createSep').html(_html).attr('disabled', true);
                $('#bpjsform-nosep').val('');
            },
            success: function(res) {
                if (res.metadata.status == 200) {
                    var result = res.response.metadata;
                    var response = res.response.response;
                    if (result.code == 200) {
                        $('.err-nosep').html('');
                        $('#bpjsform-nosep').val(response);
                        $('.createSep').hide();
                        $('.printSep').show();
                    } else {
                        $('.err-nosep').html(result.message);
                        $('.createSep').show();
                        $('.printSep').hide();
                    }
                } 
            },
            complete: function() {
                $('.createSep').html(i18next.t('Buat SEP')).removeAttr('disabled');
            }
        });
    });



    $('.cek').click(function() {
        alert($('#BpjsForm_politujuan').val());
    });

    
",View::POS_END,'daftar-rajal');
?>