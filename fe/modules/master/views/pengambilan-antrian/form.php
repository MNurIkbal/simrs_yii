<?php

/**
 * @Author: Arief Saputra
 * @Date:   2018-02-22 11:40:04
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-10-25 10:33:21
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\widgets\ActiveForm;
use yii\web\JsExpression;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pengambilan Antrian'), 'url' => ['/master/pengambilan-antrian']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                    <button type="button"
                    class="btn btn-info btn-labeled btn-xs data-simpan-konfig"
                    id="simpan-antrian">
                        <b><i class="fa fa-floppy-o"></i></b><?=Yii::t('fe', 'Simpan')?>
                    </button>
                    <?=DocoHelpers::generateToolbar([
                        'reset',
                    ]);?>
                    <?=DocoHelpers::generateToolbar([
                        'back',
                    ]);?>
            </div>
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id'=>'form-pengambilan-antrian',
                        'enableAjaxValidation'=>false,
                        'enableClientValidation'=>false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [

                        ]
                    ]);
                    if (!empty($jenisantrian_id)) :
                        echo $form->field($model, 'jenisantrian_id',[
                            'template' => '{input}',
                            'options' => [
                                    'tag' => false,
                                ],
                        ])->hiddenInput([
                           'id' => 'jenisantrian_id',
                        ])->label(false);
                        echo $form->field($model, 'jenisantrian_nama',[
                                                    'horizontalCssClasses' => [
                                                            'label' => 'text-left control-label col-sm-2',
                                                            'wrapper' => 'col-md-3'
                                                        ],
                                                    ])->textInput([
                                                        'class' => 'form-control ddljenis_antrian',
                                                        'readonly' => true
                                                    ]);
                    else :
                        echo $form->field($model, 'jenisantrian_id',[
                                                    'horizontalCssClasses' => [
                                                            'label' => 'text-left control-label col-sm-2',
                                                            'wrapper' => 'col-md-3'
                                                        ],
                                                    ])->dropDownList($jenis_antrian,[
                                                        'class' => 'form-control select2 ddljenis_antrian',
                                                        'id' => 'jenisantrian_id',
                                                        'prompt' => Yii::t('fe','--Pilih--')
                                                    ]);
                    endif;
                        // echo $form->field($model, 'fungsi_antrian_id',[
                        //                             'horizontalCssClasses' => [
                        //                                     'label' => 'text-left control-label col-sm-2',
                        //                                     'wrapper' => 'col-md-3'
                        //                                 ],
                        //                             ])
                        //             ->widget(DepDrop::classname(), [
                        //                         'options'=>[
                        //                             'id'=>'fungsi_antrian_id',
                        //                             'class' => 'select2'
                        //                         ],
                        //                         'pluginEvents' => [
                        //                             "depdrop:afterChange"=>"function(event, id, value, count,xhr) {
                        //                                 _fungsiVal = xhr.responseJSON.kode;
                        //                                 _onReady();
                        //                             }",
                        //                         ],
                        //                         'pluginOptions'=>
                        //                         [
                        //                             'depends'=> [
                        //                                 'jenisantrian_id'
                        //                             ],
                        //                             'placeholder'=>'Select...',
                        //                             'url'=>Url::to(['/master/pengambilan-antrian/get-fungsi',
                        //                                 'id' => $model->fungsi_antrian_id])
                        //                         ],
                        //                     ]);
                        
                        // if (in_array($jenisantrian_id, DocoConstants::$show_cara_bayar)) :
                        echo $form->field($model, 'groupcarabayar_id',[
                                                    'horizontalCssClasses' => [
                                                            'label' => 'text-left control-label col-sm-2',
                                                            'wrapper' => 'col-md-3'
                                                        ],
                                                    ])->dropDownList($cara_bayar,[
                                                        'class' => 'form-control select2',
                                                        'id' => 'groupcarabayar_id',
                                                        'prompt' => Yii::t('fe','--Pilih--')
                                                    ]);
                        // endif;
                        echo $form->field($model, 'klasifikasipasien_id',[
                                                    'horizontalCssClasses' => [
                                                            'label' => 'text-left control-label col-sm-2',
                                                            'wrapper' => 'col-md-3'
                                                        ],
                                                    ])->dropDownList(ArrayHelper::map($klasifikasi, 'klasifikasipasien_id', 'klasifikasipasien_nama'),[
                                                        'class' => 'form-control select2 ddljenis_antrian',
                                                        'id' => 'klasifikasipasien_id',
                                                        'prompt' => Yii::t('fe','--Pilih--')
                                                    ]);
                        // if (in_array($jenisantrian_id, DocoConstants::$show_instalasi)) :
                            // echo $form->field($model, 'instalasi_id',[
                            //                             'horizontalCssClasses' => [
                            //                                     'label' => 'text-left control-label col-sm-2',
                            //                                     'wrapper' => 'col-md-3'
                            //                                 ],
                            //                             ])->dropDownList([],[
                            //                                 'class' => 'form-control select2 ddljenis_antrian',
                            //                                 'id' => 'instalasi_id',
                            //                                 'prompt' => Yii::t('fe','--Pilih--')
                            //                             ]);

                            echo $form->field($model, 'instalasi_id',[
                                                    'horizontalCssClasses' => [
                                                            'label' => 'text-left control-label col-sm-2',
                                                            'wrapper' => 'col-md-3'
                                                        ],
                                                    ])->dropDownList($instalasi,[
                                                        'class' => 'form-control select2',
                                                        'id' => 'instalasi_id',
                                                        'prompt' => Yii::t('fe','--Pilih--')
                                                    ]);

                            echo $form->field($model, 'ruangan_id',[
                                                        'horizontalCssClasses' => [
                                                                'label' => 'text-left control-label col-sm-2',
                                                                'wrapper' => 'col-md-3'
                                                            ],
                                                        ])
                                        ->widget(DepDrop::classname(), [
                                                    'options'=>[
                                                        'id'=>'ruangan_id',
                                                        'class' => 'select2'
                                                    ],
                                                    'pluginOptions'=>
                                                    [
                                                        'depends'=> [
                                                            'instalasi_id'
                                                        ],
                                                        'initialize' => true,
                                                        'placeholder'=>'--Pilih--',
                                                        'url'=>Url::to([
                                                            '/master/pengambilan-antrian/get-ruangan',
                                                            'id' => $model->ruangan_id
                                                        ])
                                                    ],
                                                ]);

                            echo $form->field($model, 'pegawai_id',[
                                                        'horizontalCssClasses' => [
                                                                'label' => 'text-left control-label col-sm-2',
                                                                'wrapper' => 'col-md-3'
                                                            ],
                                                        ])
                                        ->widget(DepDrop::classname(), [
                                                    'options'=>[
                                                        'id'=>'pegawai_id',
                                                        'class' => 'select2'
                                                    ],
                                                    'pluginOptions'=>
                                                    [
                                                        'depends'=> [
                                                            'ruangan_id'
                                                        ],
                                                        'initialize' => true,
                                                        'placeholder'=>'--Pilih--',
                                                        'url'=>Url::to([
                                                            '/master/pengambilan-antrian/get-pegawai',
                                                            'id' => $model->pegawai_id
                                                        ])
                                                    ],
                                                ]);

                            echo $form->field($model, 'fungsi_antrian_id',[
                                                        'horizontalCssClasses' => [
                                                                'label' => 'text-left control-label col-sm-2',
                                                                'wrapper' => 'col-md-3'
                                                            ],
                                                        ])
                                        ->widget(DepDrop::classname(), [
                                                    'options'=>[
                                                        'id'=>'fungsi_antrian_id',
                                                        'class' => 'select2'
                                                    ],
                                                    'pluginOptions'=>
                                                    [
                                                        'depends'=> [
                                                            'jenisantrian_id'
                                                        ],
                                                        'initialize' => true,
                                                        'placeholder'=>'--Pilih--',
                                                        'url'=>Url::to([
                                                            '/master/pengambilan-antrian/get-fungsi-ruangan',
                                                            'id' => $model->fungsi_antrian_id
                                                        ])
                                                    ],
                                                ]);

                            
                        // endif;
                        echo $form->field($model, 'kode_antrian',[
                                                    'horizontalCssClasses' => [
                                                            'label' => 'text-left control-label col-sm-2',
                                                            'wrapper' => 'col-md-3'
                                                        ],
                                                    ])->textInput([
                                                        'placeholder' => $model->getAttributeLabel('kode_antrian'),
                                                        'class' => 'form-control ddljenis_antrian',
                                                        'id' => 'kode_antrian',
                                                        'prompt' => Yii::t('fe','--Pilih--')
                                                    ]);
                        echo $form->field($model, 'is_active',[
                                                    'horizontalCssClasses' => [
                                                            'label' => 'text-left control-label col-sm-2',
                                                            'wrapper' => 'col-md-3'
                                                        ],
                                                    ])->dropDownList($status,[
                                                        'class' => 'form-control select2',
                                                        'id' => 'is_active',
                                                        'prompt' => Yii::t('fe','--Pilih--')
                                                    ]);
                    ActiveForm::end();
                ?>

                <div class="row">
                    <div class="col-md-12">
                        <table id="table-pengambilanantrian" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="5%">No</th>
                                    <th><?=Yii::t('fe', 'Jenis Antrian'); ?></th>
                                    <th><?=Yii::t('fe', 'Kode Antrian'); ?></th>
                                    <th><?=Yii::t('fe', 'Instalasi'); ?></th>
                                    <th><?=Yii::t('fe', 'Ruangan'); ?></th>
                                    <th><?=Yii::t('fe', 'Dokter'); ?></th>
                                    <th><?=Yii::t('fe', 'Cara Bayar'); ?></th>
                                    <th><?=Yii::t('fe', 'Klasifikasi Pasien'); ?></th>
                                    <th><?=Yii::t('fe', 'Status'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$url = Url::to(['pengambilan-antrian/detail']);
$cond = "";
$_instalasi = json_encode($instalasi);
if (!$jenisantrian_id) {
    $cond = "api.rows({page:'current'}).data().each(function (data, i){
            var group = data.jenis_antrian;
            var groupLink = '<a href=\'".$url."?id='+data.jenisantrian_id+'\'>' + $('<div>').text(group).html() + '</a>';

            if (last !== group) {

                $(rows).eq(i).before(
                    '<tr class=\'group\'><td colspan=\'9\' style=\'BACKGROUND-COLOR:rgb(181, 216, 197);font-weight:700;color:#006232;\'>' + groupLink  + '</td></tr>'
                );

                last = group;
            }
        });";
}
$this->registerJs("
    var _instalasi = $_instalasi;
    var _fungsiVal = {};
    var _onReady = function () {
    //     var _jenisAntrian = $('#fungsi_antrian_id').val();
    //     var status_instalasi = docoHelper.showInstalasi.indexOf(parseInt(_jenisAntrian));
    //     var status_cara_bayar = docoHelper.showCaraBayar.indexOf(parseInt(_jenisAntrian));
    //     var _kode_antrial = $('#kode_antrian');
    //     var opt = new Option('--Pilih--', null, false, false);
    //     $('#instalasi_id').html(opt);
    //     $('#ruangan_id').html(opt);
    //     if (typeof _fungsiVal[_jenisAntrian] != 'undefined') {
    //         var _valFungsi = _fungsiVal[_jenisAntrian];
    //         var _getInstalasi = JSON.parse(_valFungsi.additional_data);
    //         console.log(_getInstalasi);
    //         if (_getInstalasi != null) {
    //             if (_getInstalasi.instalasi_id.length) {
    //                 var opt = new Option('--Pilih--', null, false, false);
    //                 $('#instalasi_id').append(opt).trigger('change');
    //                 $.each(_getInstalasi.instalasi_id, function (key,val) {
    //                     if (typeof _instalasi[val] != 'undefined') {
    //                         var opt = new Option(_instalasi[val], val, false, false);
    //                         $('#instalasi_id').append(opt).trigger('change');
    //                     }
    //                 })
    //             }
    //         }
    //         _kode_antrial.val(_valFungsi.lookup_value);
    //     }
    //     // if (status_cara_bayar >= 0) {
    //     //     $('.field-groupcarabayar_id').addClass('required');
    //     //     $('.field-groupcarabayar_id').show();
    //     // } else {
    //     //     $('.field-groupcarabayar_id').hide();
    //     // }
    //     if (status_instalasi >= 0) {
    //         $('.field-instalasi_id').addClass('required');
    //         $('.field-ruangan_id').addClass('required');
    //         $('.field-instalasi_id').show();
    //         $('.field-ruangan_id').show();
    //         return true;
    //     }
    //     // $('.field-instalasi_id').hide();
        $('.field-ruangan_id').hide();

    }
    // $(document).on('change','#fungsi_antrian_id',_onReady)
    $(document).on('click','#simpan-antrian',function (e) {
        e.preventDefault();
        var form = $('#form-pengambilan-antrian');
        var data = form.serializeArray();
        var action = form.attr('action');
        $().docoForm('click',{
            data : data,
            url : action,
            success : function(data) {
                setTimeout(function() {
                    table.draw();
                    $('.data-reset').trigger('click');
                    $('#kode_antrian').val(null);
                }, 1000);
            }
        });
    });

    $(document).ready(function () {
        // console.log(_instalasi);
        _onReady();
        $('#jenisantrian_id').trigger('change');
        table = $('#table-pengambilanantrian').docoTabel({
            filter: true,
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            sorting: [[1, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'master/pengambilan-antrian/get-data?jenis_antrian={$jenisantrian_id}&is_action=true',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Jenis antrian'))."', data: 'jenis_antrian'},
                {title: '".(\Yii::t('fe', 'Kode Antrian'))."', data: 'kode_antrian'},
                {title: '".(\Yii::t('fe', 'Instalasi'))."', data: 'instalasi_nama'},
                {title: '".(\Yii::t('fe', 'Ruangan'))."', data: 'ruangan_nama'},
                {title: '".(\Yii::t('fe', 'Dokter'))."', data: 'nama_dokter'},
                {title: '".(\Yii::t('fe', 'Cara Bayar'))."', data: 'group_carabayar'},
                {title: '".(\Yii::t('fe', 'Klasifikasi Pasien'))."', data: 'klasifikasipasien_nama',searchable: false},
                {title: '".(\Yii::t('fe', 'Status'))."', data: 'status', name: 'is_default'},
            ],
            order: [[ 1, 'desc' ]],
            responsive: true,
            drawCallback: function (settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;
                $cond
            }
        });

        $('.dataTables_filter').hide();
    });

");

$this->registerJs("
    var _antrian_pendaftaran = 177 ;
    var _antrian_penunjang = 179 ;
    var _antrian_farmasi = 176 ;
    var _antrian_poliklinik = 312 ;

    $('#jenisantrian_id').change(function(){
        if ($(this).val() == _antrian_pendaftaran || $(this).val() == '') {
            $('.field-groupcarabayar_id').show();
            $('.field-klasifikasipasien_id').show();
            $('.field-instalasi_id').hide();
            $('.field-fungsi_antrian_id').hide();
            $('.field-ruangan_id').hide();
            $('.field-groupcarabayar_id').addClass('required');
            $('.field-klasifikasipasien_id').addClass('required');


        } else if ($(this).val() == _antrian_penunjang) {
            $('.field-groupcarabayar_id').hide();
            $('.field-klasifikasipasien_id').hide();
            $('.field-instalasi_id').show();
            $('.field-instalasi_id').addClass('required');
            $('.field-fungsi_antrian_id').hide();
            // $('.field-ruangan_id').hide();
            $('.field-ruangan_id').show();
            $('.field-ruangan_id').addClass('required');
            $('.field-pegawai_id').hide();
        } else if ($(this).val() == _antrian_farmasi) {
            $('.field-groupcarabayar_id').hide();
            $('.field-klasifikasipasien_id').hide();
            $('.field-instalasi_id').show();
            $('.field-instalasi_id').addClass('required');
            $('.field-ruangan_id').show();
            $('.field-ruangan_id').addClass('required');
            $('.field-fungsi_antrian_id').show();
            $('.field-fungsi_antrian_id').addClass('required');
            $('.field-pegawai_id').hide();
        } else if ($(this).val() == _antrian_poliklinik) {
            $('.field-groupcarabayar_id').hide();
            $('.field-klasifikasipasien_id').hide();
            $('.field-instalasi_id').show();
            $('.field-instalasi_id').addClass('required');
            $('.field-ruangan_id').show();
            $('.field-ruangan_id').addClass('required');
            $('.field-pegawai_id').show();
            $('.field-pegawai_id').addClass('required');
            $('.field-fungsi_antrian_id').hide();
        } else {
            $('.field-groupcarabayar_id').hide();
            $('.field-klasifikasipasien_id').hide();
            $('.field-fungsi_antrian_id').hide();
            $('.field-ruangan_id').hide();
            $('.field-pegawai_id').hide();
        }
    });

    $(document).on('click', '.data-reset', function (event) {
        event.preventDefault();
        
        location.reload();
    });
");



?>
