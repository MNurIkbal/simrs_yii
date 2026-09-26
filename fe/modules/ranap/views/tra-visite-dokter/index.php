<?php

/**
 * @Author: sunarko
 * @Date:   2018-06-05 14:00:43
 * @Last Modified by:
 * @Description:
 */

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Transaksi visite dokter');
$title2 = Yii::t('fe', 'Visite Dokter');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat inap'), 'url' => ['/ranap/inf-pasien-ranap']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$title2); ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                    'search' => [
                        'attributes' => [
                            'id' => 'button-cari',
                        ]
                    ],
                    // 'simpen' => [
                    //     'title' => \Yii::t('fe', 'Simpan'),
                    //     'icon' => 'fa fa-stethoscope',
                        
                    //     'attributes' => [
                    //         'data-option' => 'click',
                    //         'data-target'=> 'visitedokterForm',
                    //     ]
                    // ],
                    'saved' => [
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-save',
                        'attributes' => [
                            'data-target' => 'visitedokterForm',
                            'data-options' => 'click',
                            'id'=>'button-simpan'
                            ]
                        ],
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                ], '#example');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <hr>
                <br>
                <div class="form-group">
                    <div class="col-md-12">
                        <?php 
                            $form = ActiveForm::begin([
                                'id' => 'visitedokterForm',
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => ['labelSpan' => 4,'showErrors'=>false, 'deviceSize' => ActiveForm::SIZE_SMALL]
                            ]); 
                        ?>
                        <div class="col-sm-4">
                            <div class="col-sm-4">
                                <label class="control-label">Jenis Visite <b style="color:red;">*</b></label>
                            </div>
                            <div class="col-sm-8">
                                <?=Html::activeDropDownList($modelTra, 'daftartindakan_id', 
                                    ArrayHelper::map($datajenis_visite, 'daftartindakan_id', 'kelompoktindakan_nama'),
                                    [
                                        'id' => 'filter_jenisvisite',
                                        'label' => 'Jenis Visite',
                                        'class' => 'form-control select2',
                                        'prompt' => \Yii::t('fe', '-- Nama Visite --'),
                                    ]
                                )?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="col-sm-4">
                                <label class="control-label">Dokter Visite <b style="color:red;">*</b></label>
                            </div>
                            <div class="col-sm-8">
                                <?=Html::activeDropDownList($modelTra, 'dokterpenanggungjawab_id',
                                    ArrayHelper::map($datadokter_visite, 'pegawai_id', 'nama_pegawai'),
                                    [
                                        'id' => 'filter_doktervisite',
                                        'label' => 'Jenis Visite',
                                        'class' => 'form-control select2',
                                        'prompt' => \Yii::t('fe', '-- Dokter Ruangan --'),
                                    ]
                                )?>
                                <?= $form->field($modelV, 'cppt_id')->hiddenInput(['id'=>'cppt_id'])->label(false);?>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div><br><br>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed"
                    id="example"
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php

$this->registerJs("
    var table;

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {

        // Generate Table
        table = $('#example').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ 
                {
                    visible: true,
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0,
                },
            ],
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl + 'ranap/tra-visite-dokter/get-data',
            columns: [
                {
                    title: '',
                    data: null,
                    defaultContent: '',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal Admisi'))."', data: 'tgl_admisi',searchable: false},
                {title: '".(\Yii::t('fe', 'Tanggal Isi SOAP'))."', data: 'tgl_cppt'},
                {title: '".(\Yii::t('fe', 'No Pendaftaran'))."', data: 'no_pendaftaran',searchable: false},
                {title: '".(\Yii::t('fe', 'No Rekam Medik'))."', data: 'no_rekam_medik'},
                {title: '".(\Yii::t('fe', 'Nama Pasien'))."', data: 'nama_pasien'},
                {title: '".(\Yii::t('fe', 'Jenis Kelamin'))."', data: 'jenis_kelamin',searchable: false},
                {title: '".(\Yii::t('fe', 'Cara Bayar / Penjamin'))."', data: 'carabayar_penjamin',searchable: false},
                {title: '".(\Yii::t('fe', 'Kasus Penyakit'))."', data: 'jeniskasuspenyakit_nama',searchable: false},
                {title: '".(\Yii::t('fe', 'Ruangan - Kamar'))."', data: 'ruangan_kamar',searchable: false},
                {title: '".(\Yii::t('fe', 'Dokter Penanggungjawab'))."', data: 'dokter_admisi',searchable: false},
                // filter needed
                {title: '".(\Yii::t('fe', 'Kamar'))."', data: 'kamarruangan_id',visible: false},
                {title: '".(\Yii::t('fe', 'id'))."', data: 'idnya',visible: false,searchable: false},
                {title: '".(\Yii::t('fe', 'Dokter Penanggung Jawab'))."', data: 'dokter_admisi_id',visible: false},
            ],
        });

        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            [
                3,
                \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
            [
                5,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                    Html::textInput('no_rekam_medik', '', 
                            [
                                'class' => 'form-control no_rekam_medik docoNumberOnly', 
                                'prompt' => 'No. Rekam Medik', 
                                'placeholder'=> 'No. Rekam Medik'
                            ]
                        )
                    )
                )."<div>\"
            ],
            [
                6,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                    Html::textInput('nama_pasien', '', 
                            [
                                'class' => 'form-control nama_pasien', 
                                'prompt' => 'Nama Pasien', 
                                'placeholder'=> 'Nama Pasien'
                            ]
                        )
                    )
                )."<div>\"
            ],
            [
                12,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('kamarruangan_id', '',
                        ArrayHelper::map($resMaster['kamarruangan'], 'kamarruangan_id', 'kamarruangan_nokamar'),
                        [
                            'id' => 'filter_kamarruangan',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Kamar --'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                14,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('dokter_admisi_id', '',
                        ArrayHelper::map($resMaster['dokter'], 'pegawai_id', 'nama_pegawai'),
                        [
                            'id' => 'filter_dokteradmisi',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih Dokter Penanggung Jawab --'),
                        ]
                    )
                ))."<div>\"
            ],
            
        ], 
        {
            3:0,
            5:1,
            6:2,
            12:3,
            14:4
        });


        dateRangeHelper('.startDate','.endDate','.targetDate', true);

        table.on( 'select', function ( e, dt, type, indexes ) {
            var dataid = table.rows( indexes ).data()[0].idnya;
            $('#cppt_id').val(dataid);
            // var data = table.rows( { selected: true }).data();
            //  console.log(dataid);
            $.ajax({
                type: 'GET',
                url: '/ranap/tra-visite-dokter/get-data-jenis-visite?id='+dataid,
                success: function(response){
                    // console.log(response);
                    var select = $('#filter_jenisvisite');
                    select.children().remove();
                    $('#filter_jenisvisite').append($('<option>', { value : '' }).text('-- Nama Visite --'));
                    $.each(response.result, function(index, item) {
                        // console.log(item);
                        $('#filter_jenisvisite').append($('<option>', { value : item.id }).text(item.text));
                   });
                }
           });
        });

        table.on( 'deselect', function ( e, dt, type, indexes ) {
            var select = $('#filter_jenisvisite');
            select.children().remove();
            $('#filter_jenisvisite').append($('<option>', { value : '' }).text('-- Nama Visite --'));
        });

    });

    $('#button-simpan').on('click',function(){
        var tabledata = table.row('.selected').data();
        if (typeof tabledata !== 'undefined' ) {
            $('#visitedokterForm').submit();
        }else{
            docoNotification('error','Terjadi kesalahan','Belum ada data yg dipilih');
        }
    });


    $('#visitedokterForm').docoForm('submit',{
        success : function(data) {
            table.draw();
            // window.location.reload();
        }
    });
    
    ", View::POS_END, 'b-index');
?>
