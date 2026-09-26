<?php

/**
 * @Author: [Dede Herdiana][dede.herdiana@sirs.co.id]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

    use yii\web\View;
    use yii\helpers\ArrayHelper;
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\Breadcrumbs;
    use app\components\DocoHelpers;
    use kartik\widgets\DatePicker;
    use kartik\widgets\DateTimePicker;
    use kartik\widgets\DepDrop;
?>
<style>
    .datepicker>div{
        display:block;
    }
    .btn-custom {
        padding : 5.5px 12px!important;
    }
    .modal {
      overflow: auto;
    }

    .modal-body {
      overflow: visible;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <?php
    $form = ActiveForm::begin([
            'id' => 'tindakan-form',
            'action' => '/penatajasa/inf-tagihan-pasien/save-tindakan?pendaftaran_id='.$pendaftaran_id,
            'enableAjaxValidation'=>false,
            'enableClientValidation'=>false,
            'type' => ActiveForm::TYPE_VERTICAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
        ]);
    ?>
        <?php if (!empty($dataPasien['tglpasienpulang'])) {
        ?>
        <div class="alert alert-warning" role="alert">
            <p>Informasi Pasien:</p>
            <p>Tanggal Pendaftaran Pasien : <b><?= date('d M Y H:i:s', strtotime($tglPendaftaran)) ?></b></p>
            <p>Pasien Pulang / Stop Akomodasi tanggal  : <b><?= date('d M Y H:i:s', strtotime($tglpasienpulang)) ?></b></p>
        </div>
        <?php
        } ?>
        <div class="row">
            <div class= "col-md-4">
                <?= $form->field($model, 'tanggal_tindakan', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ]
                ])->widget(DateTimePicker::classname(), [
                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                    'readonly' => true,
                    'language' => 'en',
                    'pluginOptions' => [
                        'startDate' => date('d-M-Y H:i:s', strtotime($startDate)),
                        'endDate' => date('d-M-Y H:i:s', strtotime($endDate)),
                        'autoclose' => true,
                        'todayBtn' => true,
                        'format' => 'dd-M-yyyy hh:ii:ss',
                    ]
                ]); ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'kelas_pelayanan',[
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-5'
                            ]
                        ])->dropDownList($kelasPelayanan,[
                                'class' => 'select2',
                                'id' => 'kelas_pelayanan',
                                'prompt' => '— Pilih —'
                        ]);
                ?>
            </div>
            <div class="col-md-4">
                <?= 
                $form->field($model, 'dokter_pj',[
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-5'
                            ]
                        ])->widget(DepDrop::classname(), [
                            'options' => [
                                'id'=>'dokter_pj',
                                'class' => 'form-control select2',
                            ],
                            'pluginOptions'=>[
                                'depends' => ['instalasi_id','ruangan_id'],
                                'placeholder' => Yii::t('fe', '— Pilih —'),
                                'url'=> Url::to(['inf-tagihan-pasien/get-dokter?ruangan_id='.$dataPasien['ruangan_id'].'&instalasi_id='.$dataPasien['instalasi_id'].'&dokter_pj='.$dataPasien['pegawai_id'] ]),
                                'prompt' => Yii::t('fe', '— Pilih —'),
                            ],
                        ]);
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'instalasi_id',[
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-5'
                            ]
                        ])->dropDownList($instalasi,[
                                'class' => 'select2',
                                'id' => 'instalasi_id',
                                'prompt' => '— Pilih —'
                        ]);
                ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'ruangan_id',[
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-5'
                            ]
                        ])->widget(DepDrop::classname(), [
                            'options' => [
                                'id'=>'ruangan_id',
                                'class' => 'form-control select2',
                                // 'disabled' => true
                            ],
                            'pluginOptions'=>[
                                'depends' => ['instalasi_id'],
                                'placeholder' => Yii::t('fe', '— Pilih Ruangan —'),
                                'initialize' => true,
                                'url'=> Url::to(['inf-tagihan-pasien/get-ruangan?ruangan_id='.$dataPasien['ruangan_id'].'&instalasi_id='.$dataPasien['instalasi_id']]),
                                'prompt' => Yii::t('fe', '— Pilih Ruangan —'),
                            ],
                        ]);
                ?>
            </div>
            <div class="col-md-4">
                <?= 
                $form->field($model, 'perawat',[
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-5'
                            ]
                        ])->widget(DepDrop::classname(), [
                            'options' => [
                                'id'=>'perawat',
                                'class' => 'form-control select2',
                                // 'disabled' => true
                            ],
                            'pluginOptions'=>[
                                'depends' => ['instalasi_id','ruangan_id'],
                                'placeholder' => Yii::t('fe', '— Pilih —'),
                                // 'initialize' => true,
                                'url'=> Url::to(['inf-tagihan-pasien/get-perawat?ruangan_id='.$dataPasien['ruangan_id'].'&instalasi_id='.$dataPasien['instalasi_id'].'&perawat='.$dataPasien['pegawai_id'] ]),
                                'prompt' => Yii::t('fe', '— Pilih —'),
                            ],
                        ]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
            </div>
            <div class="col-md-4 div_kamar_ruangan">
                <?= 
                $form->field($model, 'kamarruangan_id',[
                    'horizontalCssClasses' => [
                       'label' => 'text-left control-label col-sm-4',
                       'wrapper' => 'col-md-5'
                    ]
                    ])->widget(DepDrop::classname(), [
                    'options' => [
                       'id'=>'kamarruangan_id',
                       'class' => 'form-control select2',
                    ],
                    'pluginOptions'=>[
                       'depends' => ['ruangan_id','kelas_pelayanan'],
                       'placeholder' => Yii::t('fe', '— Pilih Kamar —'),
                       'url'=> Url::to(['inf-tagihan-pasien/get-kamar?ruangan_id='.$dataPasien['ruangan_id'].'&instalasi_id='.$dataPasien['instalasi_id'].'&kamarruangan_id='.$dataPasien['pegawai_id']]),
                       'prompt' => Yii::t('fe', '— Pilih Kamar —'),
                    ],
                  ]);
               ?> 
            </div>
            <div class="col-md-4 div_tempat_tidur">
                <?= 
                    $form->field($model, 'kamartempattidur_id',[
                          'horizontalCssClasses' => [
                             'label' => 'text-left control-label col-sm-4',
                             'wrapper' => 'col-md-5'
                          ]
                          ])->widget(DepDrop::classname(), [
                          'options' => [
                             'id'=>'kamartempattidur_id',
                             'class' => 'form-control select2',
                          ],
                          'pluginOptions'=>[
                             'depends' => ['kamarruangan_id','ruangan_id'],
                             'placeholder' => Yii::t('fe', '— Pilih No Tempat Tidur —'),
                             'url'=> Url::to(['inf-tagihan-pasien/get-no-tempat-tidur?ruangan_id='.$dataPasien['ruangan_id']]),
                             'prompt' => Yii::t('fe', '— Pilih No Tempat Tidur —'),
                             'params' => ['kamarruanganID'],
                             'loadingText' => Yii::t('fe', '— Pilih —'),
                          ],
                       ]);
                ?> 
            </div>
        </div>
    <hr>

        <div class="row">
            <div class='col-md-4'>
                <div id="div_tindakan">
                <?= $form->field($model, 'tindakan_pelayanan_id',[
                   'horizontalCssClasses' => [
                      'label' => 'text-left control-label col-sm-4',
                      'wrapper' => 'col-md-5',
                      // 'style' => 'max-width: 100px;',
                   ],
                   'addon' => [
                      'prepend' => [
                         'content' => Html::checkbox('Paket', false, [
                            'id' => 'is_paket', 
                            'label' => Yii::t('fe', 'Paket')
                         ]),
                      ],
                   ]
                ])->dropDownList([],[
                      'class' => 'select2',
                      'id' => 'tindakan_pelayanan_id',
                      'prompt' => '— Pilih —'
                ])->label(Yii::t('fe', 'Tindakan/Paket/Akomodasi'));
                ?>
                </div>
                <div id="div_paket">
                    <?= $form->field($model, 'paket_pelayanan_id',[
                       'horizontalCssClasses' => [
                          'label' => 'text-left control-label col-sm-4',
                          'wrapper' => 'col-md-5',
                          // 'style' => 'max-width: 100px;',
                       ],
                       'addon' => [
                          'prepend' => [
                             'content' => Html::checkbox('Paket', true, [
                                'id' => 'is_paket_tindakan', 
                                'label' => Yii::t('fe', 'Paket')
                             ]),
                          ],
                       ]
                    ])->dropDownList([],[
                          'class' => 'select2',
                          'id' => 'paket_pelayanan_id',
                          'prompt' => '— Pilih —'
                    ])->label(Yii::t('fe', 'Tindakan/Paket/Akomodasi'));
                    ?>
                </div>
            </div>
            <div class='col-md-4'>
                <?= $form->field($model, 'qty', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ]
                ])->textInput([
                    'placeholder' => Yii::t('fe', 'Qty'),
                    'id' => 'tindakan_qty',
                    'class' => 'form-control input-sm text-right doco-number',
                ]); ?>
            </div>
            <div class='col-md-4'>
                <?= $form->field($model, 'harga_satuan', [
                'addon' => ['prepend' => ['content'=>'Rp.']],
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ]
                ])->textInput([
                    'placeholder' => Yii::t('fe', 'Harga Satuan'),
                    'id' => 'harga_satuan',
                    'readonly' => $isHargaReadOnly,
                    'class' => 'form-control input-sm text-right doco-number',
                ]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 content-remarks">
                <?= $form->field($model, 'remarks')->textArea([
                    'placeholder' => Yii::t('fe', 'Keterangan'),
                    'id' => 'remarks',
                    'class' => 'form-control input-sm',
                    'rows' => 2,
                    'maxlength' => 30
                ])->label(Yii::t('fe', 'Keterangan (remarks)')); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'total', [
                'addon' => ['prepend' => ['content'=>'Rp.']],
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-5'
                    ]
                ])->textInput([
                    'placeholder' => Yii::t('fe', 'Sub Total'),
                    'id' => 'total',
                    'readonly' => true,
                    'class' => 'form-control input-sm text-right doco-number',
                ]); ?>
            </div>   
            <div class="col-md-4">
                <div class="row">
                    <div id="is_half_akomodasi" class="col-md-6">
                          <?= $form->field($model, 'is_half')->checkbox([
                             'id' => 'is_half',
                             'class' => 'styled',
                          ])->label(Yii::t('fe', 'Akomodasi 0,5 hari')); ?>
                    </div>        
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($model, 'is_cyto')->checkbox([
                            'id' => 'is_cyto',
                            'class' => 'styled',
                        ])->label(Yii::t('fe', 'Cyto')); ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'is_penyulit')->checkbox([
                            'id' => 'is_penyulit',
                            'class' => 'styled',
                        ])->label(Yii::t('fe', 'Penyulit')); ?>
                    </div>
                </div>
            </div>    
        </div>

        <?= Html::hiddenInput('TindakanForm[instalasi_nama]', '',['id' => 'instalasi_nama']); ?>
        <?= Html::hiddenInput('TindakanForm[ruangan_nama]', '',['id' => 'ruangan_nama']); ?>
        <?= Html::hiddenInput('TindakanForm[dokterdpjp_nama]', '',['id' => 'dokterdpjp_nama']); ?>
        <?= Html::hiddenInput('TindakanForm[tindakan_nama]', '',['id' => 'tindakan_nama']); ?>
        <?= Html::hiddenInput('TindakanForm[harga_tariftindakan]', '',['id' => 'harga_tariftindakan']); ?>
        <?= Html::hiddenInput('TindakanForm[persencyto_tindakan]', '',['id' => 'persencyto_tindakan']); ?>
        <?= Html::hiddenInput('TindakanForm[daftartindakan_id]', '',['id' => 'daftartindakan_id']); ?>
        <?= Html::hiddenInput('TindakanForm[tipepaket_id]', '',['id' => 'tipepaket_id']); ?>
        <?= Html::hiddenInput('TindakanForm[penjamin_id]', $penjamin_id,['id' => 'penjamin_id']); ?>
        <?= Html::hiddenInput('TindakanForm[no_pendaftaran]', $no_pendaftaran,['id' => 'no_pendaftaran']); ?>
        <?= Html::hiddenInput('TindakanForm[tglpasienpulang]', $tglpasienpulang,['id' => 'tglpasienpulang']); ?>
        <?= Html::hiddenInput('TindakanForm[tglPendaftaran]', $tglPendaftaran,['id' => 'tglPendaftaran']); ?>
        <?= Html::hiddenInput('TindakanForm[status_periksa]', $status_periksa,['id' => 'status_periksa']); ?>
        <?= Html::hiddenInput('TindakanForm[persen_penyulit]', '',['id' => 'persen_penyulit']); ?>
        <?= Html::hiddenInput('TindakanForm[jenis_pelayanan]', 'tindakan',['id' => 'jenis_pelayanan']); ?>
        <?= Html::hiddenInput('TindakanForm[harga_satuan_origin]', '',['id' => 'harga_satuan_origin']); ?>
        <?= Html::hiddenInput('TindakanForm[alasan_edit_harga]', '',['id' => 'alasan_edit_harga']); ?>
        <?= Html::hiddenInput('TindakanForm[tindakanpelayanan_id]', $tindakanPelayananId, ['id' => 'tindakanpelayanan_id']); ?>
    <hr>
    <div class="modal-footer" style="padding:0px !important;">
        <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
                'class' => 'btn btn-info btn-labeled btn-xs',
                'id' => 'simpan-tindakan'
            ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
                    'class' => 'btn btn-info btn-labeled btn-xs',
                    'data-dismiss' => 'modal',
                    'id' => 'close-tindakan'
            ]); ?>
        <?php  
            // Html::button("<b><i class='fa fa-refresh'></i></b>&nbsp;Bersihkan", [
            //     'class' => 'btn btn-info btn-labeled btn-xs',
            //     'id' => 'bersihkan-tindakan'
            // ]) 
        ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    var _id = '<?= $pendaftaran_id ?>';
    var _is_pasien_ri = '<?= $isPasienRI ?>';
    var _daftartindakan_nama_akomodasi = '<?= $tindakanAkomodasi['daftartindakan_nama'] ?>';
    var _daftartindakan_akomodasi = '<?= $tindakanAkomodasi['daftartindakan_id'] ?>';
    var isChangePrice = false;
    var modulId = '<?= $modul_id ?>';
    var _konfigKelompokTindakan = '<?= json_encode($newKonfigKelompok) ?>';
    var _tindakanPelayananId = '<?= $tindakanPelayananId ?>';
    var _valueTindakan = '<?= json_encode($valueTindakan) ?>';
    var _daftartindakanId = '<?= $model->daftartindakan_id ?>';
    var _kelompokTindakanId = '<?= $kelompokTindakanId ?>';
    var _hargaTarifTindakan = '<?= $model->harga_satuan_origin ?>';
    var isRemarks = false;

    $('.div_kamar_ruangan').hide()
    $('.div_tempat_tidur').hide()
    $('#is_half_akomodasi').hide()

    akomodasi_checkbox = $('.field-tindakan_pelayanan_id').find('.input-group-addon')
    akomodasi_checkbox_paket = $('.field-paket_pelayanan_id').find('.input-group-addon')
    akomodasi_checkbox.append('<label><input type="checkbox" id="is_akomodasi" name="akomodasi" value="1" '+ _is_pasien_ri +'> Akomodasi</label>')
    akomodasi_checkbox_paket.append('<label><input type="checkbox" id="is_akomodasi_paket" name="akomodasi" value="1" '+ _is_pasien_ri +'> Akomodasi</label>')

    $(document).ready(function () {
        $('#div_paket').hide()
        $('.content-remarks').addClass('hidden');
        $(".field-kamarruangan_id").find('label').after('<label" style="color:red"> * </label">')
        $(".field-kamartempattidur_id").find('label').after('<label" style="color:red"> * </label">')
        $("#jenis_pelayanan").focus();
        $('#is_paket').on('change',function(e){

            if($('#is_paket').is(':checked')){
                $('#div_paket').show()
                $('#div_tindakan').hide()
                $('#jenis_pelayanan').val('paket')
                $('#is_paket_tindakan').prop('checked',true)
                $('#is_akomodasi').prop('checked',false).trigger('change')
            } else {
                $('#div_paket').hide()
                $('#div_tindakan').show()
                $('#jenis_pelayanan').val('tindakan')
                $('#is_paket_tindakan').prop('checked',false)
            }
        })
        $('#is_paket_tindakan').on('change',function(e){
            if($('#is_paket_tindakan').is(':checked')){
                $('#div_paket').show()
                $('#div_tindakan').hide()
                $('#is_paket').prop('checked',false)
            } else {
                $('#div_paket').hide()
                $('#div_tindakan').show()
                $('#is_paket').prop('checked',false)
                $('#jenis_pelayanan').val('tindakan')
            }
        })

        $('#is_akomodasi').on('change', function(e){
           if($(this).is(':checked')){
              $('.div_kamar_ruangan').show()
              $('.div_tempat_tidur').show()
              $('#is_half_akomodasi').show()
              $('#is_cyto').prop("disabled",true)
              $('#is_penyulit').prop("disabled",true)
              $('#is_cyto').prop("checked",false)
              $('#is_penyulit').prop("checked",false)
              $('#is_paket').prop("checked",false)
              $('#is_paket_tindakan').prop("checked",false)
              $('#is_half').prop('checked',false)
              var akomodasiOption = new Option(_daftartindakan_nama_akomodasi, null, true, true);
              $('#tindakan_pelayanan_id').append(akomodasiOption)
              $('#tindakan_pelayanan_id').prop("readonly",true)
              $('#tindakan_pelayanan_id').prop("disabled",true)
              $('#tindakan_qty').val(1)
              $('#tindakan_qty').prop('readonly',true)
              $('#qty').val(1)
              $('#total').val(0)
              $('#harga_satuan').val(null)
              $('#harga_satuan_origin').val(null)
              isChangePrice = false
              $('#daftartindakan_id').val(_daftartindakan_akomodasi)
              $('#jenis_pelayanan').val('akomodasi')
            //   $('#tindakan_pelayanan_id').val('akomodasi')
              $('#tindakan_qty').prop('disabled',false)
           } else {
              closeFormAkomodasi()
           }
        })

        $('#is_akomodasi_paket').on('change', function(e){
           if($(this).is(':checked')){
               $('#is_akomodasi_paket').prop('checked',false)
               $('#is_paket_tindakan').prop('checked',false).trigger('change')
               $('#is_akomodasi').prop('checked',true).trigger('change')
               $('#is_akomodasi_paket').prop('checked',false)
           } else {
              closeFormAkomodasi()
           }
        })

        $('#is_half').on('change',function(e){
           if($(this).is(':checked')){
              $('#tindakan_qty').val(0.5)
              sub_total_akomodasi = 0.5 * docoHelper.convertToAngka($('#harga_satuan').val())
              $('#total').val(docoHelper.convertToRupiah(sub_total_akomodasi))
           } else {
              $('#tindakan_qty').val(1)
              $('#total').val($('#harga_satuan').val())
           }
        })

        $('#tindakan_pelayanan_id').docoPaginationSelec2({
            _api: baseUrl+"penatajasa/inf-tagihan-pasien/tindakan-paket-ruangan",
            placeholder: "— Pilih —",
            minimumInputLength: 0,
            ajax : {
                dataType: 'json',
                quietMillis: 250,
                data: function(params) { 
                    params.jenis_pelayanan = $('#jenis_pelayanan').val();
                    params.ruangan_id = $('#ruangan_id').val();
                    params.kelas_pelayanan = $('#kelas_pelayanan').val();

                    // params.jenis_pelayanan = 'tindakan';
                    // params.ruangan_id = 158;
                    // params.kelas_pelayanan = 18;
                    params.id = _id;
                    var query = {
                        search: params,
                    }
                    return {
                        _type:params._type,
                        jenis:params.jenis_pelayanan,
                        ruangan_id:params.ruangan_id,
                        kelas_pelayanan:params.kelas_pelayanan,
                        id:params.id,
                        q:params.term, 
                        page:params.page || 1
                    };
                },
            },
            cache: true
        })

        $('#paket_pelayanan_id').docoPaginationSelec2({
            _api: baseUrl+"penatajasa/inf-tagihan-pasien/get-paket-infinity",
            placeholder: "— Pilih —",
            minimumInputLength: 0,
            ajax : {
                dataType: 'json',
                quietMillis: 250,
                data: function(params) { 
                    params.jenis_pelayanan = $('#jenis_pelayanan').val();
                    params.ruangan_id = $('#ruangan_id').val();
                    params.kelas_pelayanan = $('#kelas_pelayanan').val();
                    params.id = _id;
                    var query = {
                        search: params,
                    }
                    return {
                        _type:params._type,
                        jenis_pelayanan:params.jenis_pelayanan,
                        ruangan_id:params.ruangan_id,
                        kelas_pelayanan:params.kelas_pelayanan,
                        id:params.id,
                        q:params.term, 
                        page:params.page || 1
                    };
                },
            },
            cache: true
        })

        if(_tindakanPelayananId != '') {
            var _dataTindakan = JSON.parse(_valueTindakan)
            var _dataOption = new Option(_dataTindakan.daftartindakan_nama, _dataTindakan.daftartindakan_id, true, true);
            $('#tindakan_pelayanan_id').append(_dataOption);
            $('#harga_satuan').trigger('change')
            $('#total').trigger('change')
            $('.content-remarks').removeClass('hidden');
            $("#daftartindakan_id").val(_dataTindakan.daftartindakan_id)
            
        }
    });

    $('#tindakan_pelayanan_id').on('change', function (event) {
        $('#is_cyto').prop("checked",false)
        $('#harga_satuan').val('')
        $('#harga_satuan_origin').val('')
        isChangePrice = false
        var _jenis = $("#jenis_pelayanan").val();
        var _dataTindakan = $("#tindakan_pelayanan_id option:selected").data()
        
        if(typeof _dataTindakan != 'undefined'){
            var _ruanganId = $('#ruangan_id').val();
            var _tindakanId = _dataTindakan.data.id;
            var _dataKelas = $("#kelas_pelayanan option:selected").data()
            var _idKelas = _dataKelas.data.id;
            var _dataDokter = $("#dokter_pj option:selected").data()
            var _idDokter = _dataDokter.data.id;
            // var _idDokter = 768;
            var _penjamin_id = $('#penjamin_id').val();
            var _kelompokId = null;
            
            if(_tindakanPelayananId != '' && _daftartindakanId != '') {
                if(_daftartindakanId == _tindakanId) {
                    _kelompokId = _kelompokTindakanId
                }
                else {
                    if(typeof _dataTindakan.data.datavalue != 'undefined' && _dataTindakan.data.datavalue != '') {
                        _kelompokId = _dataTindakan.data.datavalue.kelompoktindakan_id;
                    }
                }
            }
            else {
                if(typeof _dataTindakan.data.datavalue != 'undefined' && _dataTindakan.data.datavalue != '') {
                    _kelompokId = _dataTindakan.data.datavalue.kelompoktindakan_id;
                }
            }

            var _params = `daftartindakan_id=${_tindakanId}&ruangan_id=${_ruanganId}&penjamin_id=${_penjamin_id}&kelaspelayanan_id=${_idKelas}&dokter_id=${_idDokter}&jenis_pelayanan=${_jenis}`
            $("#daftartindakan_id").val(_tindakanId)
            if(_jenis == 'tindakan') {
                $("#daftartindakan_id").val(_tindakanId)
            }else {
                $("#tipepaket_id").val(_tindakanId)
            }

            if( _idDokter  != ''){
                var _tindakanId = _dataTindakan.data.id;
                $.ajax({
                url: `/penatajasa/inf-tagihan-pasien/get-tarif?${_params}`,
                type: 'GET',
                success: function(data) {
                    $('#tindakan_qty').val(1).trigger("change")
                    $('#total').val(0).trigger("change")
                    if(data.length !== 0) {
                        $('#harga_satuan').val(docoHelper.convertToRupiah(data.harga_tariftindakan))/* .trigger("change") */;
                        $('#harga_satuan_origin').val(data.harga_tariftindakan)
                        $('#harga_tariftindakan').val(data.harga_tariftindakan)
                        $('#persencyto_tindakan').val(data.persencyto_tindakan)
                        $('#penjamin_tindakan_id').val(data.penjamin_id)
                        $('#daftartindakan_id').val(data.daftartindakan_id)
                        $('#tipepaket_id').val(data.tipepaket_id)
                        $('#persen_penyulit').val(data.persen_penyulit);
                        isChangePrice = false;
                        _hitungTarif()
                    }
                    else {
                        $('#harga_satuan').val(docoHelper.convertToRupiah(0))
                        $('#harga_tariftindakan').val(0)
                        $('#harga_satuan_origin').val(0)
                    }
                }
                });
            }
            
            if(typeof _konfigKelompokTindakan != 'undefined' && _konfigKelompokTindakan != '') {
                if(_konfigKelompokTindakan.indexOf(_kelompokId) != -1) {
                    $('.content-remarks').removeClass('hidden');
                    $('#harga_satuan').prop('readonly', false)
                    isRemarks = true
                }
                else {
                    $('.content-remarks').val(null).trigger('change')
                    $('.content-remarks').addClass('hidden');
                    $('#harga_satuan').prop('readonly', true)
                }
            }
        }
    })

    $('#paket_pelayanan_id').on('change', function (event) {
        $('#is_cyto').prop("checked",false)
        $('#harga_satuan').val('')
        $('#harga_satuan_origin').val('')
        isChangePrice = false
        $('#tindakan_qty').val('')
        $('#total').val('')
        var harga = $("#paket_pelayanan_id option:selected").data()
        if(typeof harga.data !== "undefined"){
        var selected = harga.data.datavalue || {}
            $('#harga_satuan').val(docoHelper.convertToRupiah(selected.harga_tariftindakan))
            $('#harga_satuan_origin').val(selected.harga_tariftindakan)
            $('#harga_tariftindakan').val(selected.harga_tariftindakan)
            $('#persencyto_tindakan').val(selected.persencyto_tindakan)
            $('#penjamin_id').val(selected.penjamin_id)
            $('#tipepaket_id').val(selected.tipepaket_id)
        }
    })

    $('#tindakan_qty').on('change', function (event) {
        _hitungTarif()
    })

    $('#is_cyto').on('change', function (evelt) {
        _hitungTarif()
    })

    $('#is_penyulit').on('change', function (evelt) {
        _hitungTarif()
    })

    var _hitungTarif = function () {

        var tmp_jenis = $('#jenis_pelayanan').val()
        if (tmp_jenis === 'tindakan') {
            var data = $("#tindakan_pelayanan_id option:selected").data()
            return _hitungTarifTindakan()
        } else {
            var data = $("#paket_pelayanan_id option:selected").data()
        }
        var qty = $('#tindakan_qty').val() ? parseFloat($('#tindakan_qty').val()) : 0;
        var persenPenyulit = 0;
        var persencyto_tindakan = 0
        var selected = data.data.datavalue || {}
        var harga_satuan = parseFloat(selected.harga_tariftindakan)
        
        if(isChangePrice){
            harga_satuan = docoHelper.convertToAngka( $('#harga_satuan').val() )
        }
        if($('#is_penyulit').is(':checked')) {
            persen_penyulit = parseFloat(selected.persen_penyulit) / 100
            harga_satuan += parseFloat(harga_satuan*persen_penyulit)
        }

        if ($('#is_cyto').is(':checked')) {
            persencyto_tindakan = parseFloat(selected.persencyto_tindakan) / 100
            harga_satuan += parseFloat(harga_satuan*persencyto_tindakan)
        }

        jumlah = parseFloat(harga_satuan*qty)
        $('#total').val(docoHelper.convertToRupiah(jumlah))
    }

    var _hitungTarifTindakan = function () {
        var qty = $('#tindakan_qty').val() ? parseFloat($('#tindakan_qty').val()) : 0;
        var harga_satuan = parseFloat($('#harga_tariftindakan').val()) 
        var persen_penyulit = typeof $('#persen_penyulit').val() == "undefined" ? 0 : $('#persen_penyulit').val();
        var persencyto_tindakan = typeof $('#persencyto_tindakan').val() == "undefined" ? 0 : $('#persencyto_tindakan').val();
        var harga_cyto = harga_penyulit = 0;
        var _tindakanId = null;
        if(_tindakanPelayananId != '' && _hargaTarifTindakan != '') {
            var _dataTindakan = $("#tindakan_pelayanan_id option:selected").data()
            if(typeof _dataTindakan.data != 'undefined') {
                _tindakanId = _dataTindakan.data.id;
            }
            
            if(_daftartindakanId == _tindakanId) {
                $('#harga_satuan_origin').val(_hargaTarifTindakan);
            }
        }

        if($('#is_penyulit').is(':checked')) {
            harga_penyulit = parseFloat((persen_penyulit/ 100) * harga_satuan) 
            harga_satuan = parseFloat(harga_satuan + harga_penyulit)
        }

        if ($('#is_cyto').is(':checked')) {
            harga_cyto = parseFloat((persencyto_tindakan/100) * harga_satuan)
            harga_satuan = parseFloat(harga_satuan + harga_cyto)
        }

        jumlah = parseFloat(harga_satuan*qty);
        $('#total').val(docoHelper.convertToRupiah(jumlah))
    }

    $('#jenis_pelayanan').on('change', function (event) {
        var tmp_jenis = $(this).val()
        if (tmp_jenis === 'tindakan') {
            $('#div_tindakan').show()
            $('#div_paket').hide()
        }else{
            $('#div_tindakan').hide()
            $('#div_paket').show()
        }
        $('#tindakan_pelayanan_id').val("").trigger('change')
        $('#paket_pelayanan_id').val("").trigger('change')
        $('#is_cyto').prop("checked",false)
        $('#harga_satuan').val('')
        $('#harga_satuan_origin').val('')
        isChangePrice = false
        $('#tindakan_qty').val('')
        $('#total').val('')
    })

    $('#simpan-tindakan').on('click', function (event) {
        var checkContents = setInterval(function(){
         if ($("#confirm-dialog").length > 0  && isChangePrice){
           $('#confirm-dialog').css('height','450px')
           clearInterval(checkContents);
         }
       },100);

        var header = 'Perhatian !';
        var textarea = '<div class="form-group col-md-12"><div class="col-md-3"><label>Alasan </label><span style="color:red"> *</span></div><div class="col-md-6"><textarea id="edit-harga-alasan" class="form-control input-sm" name="alasan_edit_harga" rows="5" placeholder="Alasan" aria-required="true"></textarea></div></div>';
        var labelUsername = '<div class="col-md-3"><label>Username </label><span style="color:red"> *</span></div>';
        var labelPassword = '<div class="col-md-3"><label>Password </label><span style="color:red"> *</span></div>';
        if (isChangePrice) {
           add = $('#confirm-form').clone().removeClass('hidden');
           add.find('.input-pemakai').removeAttr('readonly');
           add.find('.input-pemakai').attr('value', '');
           add.find('.input-pemakai').attr('id', 'pemakai-validasi');
           add.find('.input-pemakai').attr('placeholder', 'Username');
           add.find('.input-pemakai').parent().addClass('col-md-12');
           add.find('.input-pemakai').before(labelUsername);
           add.find('.input-pemakai').wrap('<div class="col-md-6 input-username"></div>');
           add.find('.input-username').after('<br><br>');
           add.find('.input-sandi').attr('id', 'sandi-validasi');
           add.find('.input-sandi').parent().addClass('col-md-12 password-text-input');
           add.find('.input-sandi').before(labelPassword);
           add.find('.input-sandi').wrap('<div class="col-md-6 input-password"></div>');
           add.find('.input-password').after('<br><br>');
           add.find('.password-text-input').after(textarea);
           add.find('.delete-confirm-custom').removeClass('col-md-6 col-md-offset-3');
           add.find('.delete-confirm-custom').addClass('col-md-12');
           add = add.html();
           var message = 'Apakah anda yakin untuk mengedit tarif tagihan pada transaksi ini ?' + add;
           var label = {
                 buttons: {
                    'Yes': 'button-yes',
                    'No': 'button-no'
                 },
                 hidden: true
           };
        } else {
           var message = 'Apakah anda yakin untuk menyimpan data ini ?'
           var label = {
                 buttons: {
                    'Yes': 'button-yes',
                    'No': 'button-no'
                 }
           };
        }

        event.preventDefault();
        var instalasi_nama =  $("#instalasi_id option:selected").text();
        var ruangan_nama =  $("#ruangan_id option:sele cted").text();
        var kelas_pelayanan_nama =  $("#kelas_pelayanan option:selected").text();
        var tindakan_nama =  $("#tindakan_pelayanan_id option:selected").text();
        var paket_nama =  $("#paket_pelayanan_id option:selected").text();
        var dokterpj_nama =  $("#dokter_pj option:selected").text();

        $('#instalasi_nama').val(instalasi_nama);
        $('#ruangan_nama').val(ruangan_nama);
        $('#dokterdpjp_nama').val(dokterpj_nama);
        $('#tindakan_nama').val(tindakan_nama);
        $('#paket_nama').val(paket_nama);

        var _form = $('#tindakan-form');
        var _jenisPelayanan = $('#jenis_pelayanan').val();
        var _palayanan = $('#tindakan_pelayanan_id').val();
        var _paket = $('#paket_pelayanan_id').val();



        $.showQuestionDialog(header, message, label, function (reaction) {
         if (reaction == 'Yes') {
               if (isChangePrice) {
                  var user = $('#pemakai-validasi').val();
                  var pass = $('#sandi-validasi').val();
                  var edit_harga_alasan = $('#edit-harga-alasan').val();
                  if(edit_harga_alasan ==''){
                     docoNotification("warning","Proses Gagal","Alasan edit harga belum diisi")
                  } else {
                     $('#alasan_edit_harga').val(edit_harga_alasan)
                     $().docoForm('click', {
                        url: baseUrl + 'penatajasa/end-point/check-authorization',
                        skipConfirm: true,
                        skipSuccessNotif: true,
                        data: {
                              nama_pemakai: user,
                              katakunci_pemakai: pass,
                              modul_id: modulId,
                              akses: 'save-tmp-tagihan',
                        },
                        success: function (data) {
                           var response = data.response;
                           setTimeout(function () {
                               showLoader()
                           }, 100);
                           
                            $().docoForm('click',{
                                url : _form.attr('action'),
                                data : _form.serializeArray(),
                                skipConfirm: true,
                                success : function (res) {
                                    // $('#jenis_pelayanan').val("").trigger('change');
                                    // $('#tindakan_pelayanan_id').val("").trigger('change');
                                    $('#is_cyto').prop("checked",false);
                                    $('#tindakan_qty').val('');
                                    $("#jenis_pelayanan").focus();
                                    var pendaftaran_ids = res.response.pendaftaran_ids;
                                    var pendaftaranID = res.response.pendaftaran_id;
                                    table.draw();
                                    $("#modal_backdrop").modal('toggle');
                                }
                            });
                        }
                     })                     
                  }
               } else {
                $().docoForm('click',{
                    url : _form.attr('action'),
                    data : _form.serializeArray(),
                    success : function (res) {
                        // $('#jenis_pelayanan').val("").trigger('change');
                        // $('#tindakan_pelayanan_id').val("").trigger('change');
                        $('#is_cyto').prop("checked",false);
                        $('#tindakan_qty').val('');
                        $("#jenis_pelayanan").focus();
                        var pendaftaran_ids = res.response.pendaftaran_ids;
                        var pendaftaranID = res.response.pendaftaran_id;
                        table.draw();
                        $("#modal_backdrop").modal('toggle');
                    }
                });
               }
         }
         if (reaction == 'No') {
            hideQuestionDialog();
            $('[data-popup="tooltip"]').tooltip();

         }
        })
    })
    $("#close-tindakan").click(function(e){
        setTimeout(function () {
            $(".table-tagihan-tindakan").attr("style", "width: 1188px !important;");
        }, 500);
    });   

    function data_audit(pendaftaran_id){
        $.getJSON(baseUrl+"penatajasa/inf-tagihan-pasien/data-audit?id="+pendaftaran_id, function(res){
            if(res == true){
                window.onbeforeunload = confirmExit;
                function confirmExit()
                {
                    return "Do you want to leave this page without saving ?";
                }
            }
        });
    }
    
    function closeFormAkomodasi(){
      $('.div_kamar_ruangan').hide()
      $('.div_tempat_tidur').hide()
      $('#is_half_akomodasi').hide()
      $('#is_akomodasi').prop("checked",false)
      $('#is_cyto').prop("disabled",false)
      $('#is_penyulit').prop("disabled",false)
      $('#tindakan_pelayanan_id').prop('disabled',false)
      $('#tindakan_pelayanan_id').val(null)
      $('#tindakan_pelayanan_id').text(null)
      $('#kamartempattidur_id').val(null).trigger('change')
      $('#tindakan_qty').prop('readonly',false)
      $('#tindakan_qty').val("")
      $('#harga_satuan').val(0)
      $('#harga_satuan_origin').val(null)
      isChangePrice = false
      if($('#is_paket').is(':checked')){
         $('#jenis_pelayanan').val('paket')
      } else {
        $('#jenis_pelayanan').val('tindakan')
      }
      $('#daftartindakan_id').val("")
      $('#total').val("")
      $('#is_half_akomodasi').prop('checked',false)
   }

   $('#kamarruangan_id').on('change', function(e){
      $('#harga_satuan').val(null)
      $('#harga_satuan_origin').val(null)
      isChangePrice = false
      $('#total').val(0)
   })

    $('#kamartempattidur_id').on('change',function(e){
        kamar_ruangan = $('#kamarruangan_id').val()
        tempat_tidur =  $('#kamartempattidur_id').val()
        penjamin_id =  $('#penjamin_id').val()
        ruangan_id =  $('#ruangan_id').val()
        kelas_pelayanan =  $('#kelas_pelayanan').val()
        _tindakanId = _daftartindakan_akomodasi;

        if(kamar_ruangan != null && tempat_tidur != null && ruangan_id != null){
            $('#tindakan_qty').val(1)
            $('#tindakan_qty').prop('readonly',true)

            var _params = `daftartindakan_id=${_tindakanId}&ruangan_id=${ruangan_id}&penjamin_id=${penjamin_id}&kelaspelayanan_id=${kelas_pelayanan}&kamarruangan_id=${kamar_ruangan}&kamartempattidur_id=${tempat_tidur}&is_akomodasi=1`

            if(tempat_tidur != null && tempat_tidur != '' && typeof tempat_tidur != 'undefined' ){
                $().docoForm('click',{
                    url: `/penatajasa/inf-tagihan-pasien/get-tarif?${_params}`,
                    skipSuccessNotif : true,
                    skipConfirm : true,
                    success : function (res) {
                        if(res.harga_tariftindakan != null && typeof res.harga_tariftindakan != 'undefined'){
                            $('#harga_satuan').val(docoHelper.convertToRupiah(res.harga_tariftindakan))
                            sub_total_akomodasi = $('#is_half').is(':checked') ? 0.5* res.harga_tariftindakan : res.harga_tariftindakan
                            if($('#is_half').is(':checked')){
                                $('#tindakan_qty').val(0.5)
                            } else {
                            $('#tindakan_qty').val(1)
                            }
                            $('#total').val(docoHelper.convertToRupiah(sub_total_akomodasi))
                            $('#tindakan_akomodasi').val(res.daftartindakan_nama)
                            $('#harga_satuan_origin').val(res.harga_tariftindakan)
                        } else {
                            docoNotification("error","Proses Gagal","Tarif kamar tidak ditemukan")
                        }
                    }
                });
            }
        } 
    })

    $('#harga_satuan').on('change', function (e){
        if(docoHelper.convertToAngka($('#harga_satuan').val()) != $('#harga_tariftindakan').val()){
            isChangePrice = true;
        }
        if($('#is_akomodasi').is(':checked') && ( $('#kamarruangan_id').val() == '' || $('#kamartempattidur_id').val() == '') ){
        docoNotification("error","Proses Gagal","No Tempat Tidur belum dipilih")
        $('#harga_satuan').val(null)
        return false;
        } 
        if(_tindakanPelayananId == '' && ($('#tindakan_pelayanan_id').val() == null || $('#tindakan_pelayanan_id').val() == '') && ( $('#paket_pelayanan_id').val() == null || $('#paket_pelayanan_id').val() == '') && !$('#is_akomodasi').is(':checked')){
        docoNotification("error","Proses Gagal","Belum ada tindakan/paket yang dipilih")
        $('#harga_satuan').val(null)
        return false;
        } 
        $('#harga_tariftindakan').val(docoHelper.convertToAngka($('#harga_satuan').val()))
        _hitungTarif()
        
        if(_tindakanPelayananId != '') {
            _dataTindakan = JSON.parse(_valueTindakan)
            var _dataDokter = _idDokter = _tindakanId = _dataKelas = _idKelas = ''
            var _ruanganId = $('#ruangan_id').val();
            var _penjamin_id = $('#penjamin_id').val();
            var _kelompokId = null;
            var _jenis = $("#jenis_pelayanan").val();
            if (!$.isNumeric(_ruanganId)) {
                _ruanganId = ''
            }
            if(typeof $("#dokter_pj option:selected").data() != 'undefined') {
                _dataDokter = $("#dokter_pj option:selected").data()
                if(typeof _dataDokter.data != 'undefined') {
                    _idDokter = _dataDokter.data.id;
                }
            }
            
            if(typeof _dataTindakan != 'undefined') {
                _tindakanId = _dataTindakan.daftartindakan_id;
            }

            if(typeof $("#tindakan_pelayanan_id option:selected").data() != 'undefined') {
                _dataTindakan = $("#tindakan_pelayanan_id option:selected").data()
                if(typeof _dataTindakan.data != 'undefined') {
                    _tindakanId = _dataTindakan.data.id;
                }
            }
            
            if(typeof $("#kelas_pelayanan option:selected").data() != 'undefined') {
                _dataKelas = $("#kelas_pelayanan option:selected").data()
                if(typeof _dataKelas.data != 'undefined') {
                    _idKelas = _dataKelas.data.id;
                }
            }
        
            var _params = `daftartindakan_id=${_tindakanId}&ruangan_id=${_ruanganId}&penjamin_id=${_penjamin_id}&kelaspelayanan_id=${_idKelas}&dokter_id=${_idDokter}&jenis_pelayanan=${_jenis}`
            if( _idDokter != '' && _tindakanId != '' && _ruanganId != '' && _penjamin_id != '' && _idKelas != '' && _jenis != '') {
                $.ajax({
                url: `/penatajasa/inf-tagihan-pasien/get-tarif?${_params}`,
                type: 'GET',
                success: function(data) {
                    if(typeof $('#tindakan_qty').val() == 'undefined' || $('#tindakan_qty').val() == '') {
                        $('#tindakan_qty').val(1).trigger("change")
                        $('#total').val(0).trigger("change")
                    }
                    
                    if(data.length !== 0) {
                        var _hargaEdit = docoHelper.convertToAngka($('#harga_satuan').val())
                        var qty = $('#tindakan_qty').val() ? parseFloat($('#tindakan_qty').val()) : 0;
                        $('#harga_satuan_origin').val(parseFloat(data.harga_tariftindakan))
                        $('#harga_tariftindakan').val(docoHelper.convertToAngka(_hargaEdit))
                        var total = _hargaEdit * qty
                        $('#total').val(docoHelper.convertToRupiah(total))
                    }
                    else {
                        $('#harga_satuan').val(docoHelper.convertToRupiah(0))
                        $('#harga_tariftindakan').val(0)
                        $('#harga_satuan_origin').val(0)
                    }
                }
                });
            }
        }
   })

</script>
