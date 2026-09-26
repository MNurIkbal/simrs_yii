<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Bedah sentral', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                        <div class="column-1">
                                <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                        </div>
                        <div class="column-2">
                                <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                                <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                        </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Setuju'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'setuju-pasien', 
                        'disabled' => ArrayHelper::getValue($data, 'status_penunjang', '') == 470 || ArrayHelper::getValue($data, 'status_penunjang', '') == 692 ? null : 'disabled',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa-remove"></i></b>'.Yii::t('fe', ' Tolak'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'tolak-pasien',
                        'disabled' => ArrayHelper::getValue($data, 'status_penunjang', '') == 470 || ArrayHelper::getValue($data, 'status_penunjang', '') == 692 ? null : 'disabled',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa-calendar"></i></b>'.Yii::t('fe', ' Reschedule'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'btn-reschedule',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'action' => '/bedah/jadwal-operasi/reschedule?id='.$id,
                        'data-width' => '80%',
                        'disabled' => ArrayHelper::getValue($data, 'status_penunjang', '') == 470 || ArrayHelper::getValue($data, 'status_penunjang', '') == 692 ? null : 'disabled',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa-list"></i></b>'.Yii::t('fe', ' Riwayat Jadwal Operasi'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'btn-riwayat-operasi',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'action' => '/bedah/jadwal-operasi/riwayat-operasi?pasienkirimkeunitlain_id='.$pasienkirimkeunitlain_id,
                        'data-width' => '80%',
                    ]);
                ?>
                <?= DocoHelpers::generateToolbar([
                    'back',
                ]) ?>
            </div>
            <div class="panel-body">
               <!-- pannel detail pasien -->
               <div class="col-md-12">
                    <?=
                        $this->render('partial/_infopasien', ['data'=>$data])
                    ?>
               </div>
               <!-- pannel detail pasien -->
               <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Operasi'); ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Tanggal Permintaan") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= $data['created_date'] ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Dokter Perujuk") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['dok_perujuk']) ? $data['dok_perujuk'] : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Dokter Operator") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['dok_operator']) ? $data['dok_operator']  : '-' ?> </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "No Rujukan") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['no_orderkeunitlain']) ? $data['no_orderkeunitlain'] : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Jam Mulai") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['jam_rencana_mulai']) ? $data['jam_rencana_mulai'] : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jam Selesai") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['jam_rencana_selesai']) ? $data['jam_rencana_selesai'] : '-' ?> </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Dokter Anastesi") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['dok_anastesi']) ? $data['dok_anastesi'] : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Catatan Klinis") ?></b></label>
                                            <div id="c_klinis" class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['catatan_klinis']) ? $data['catatan_klinis'] : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group highlight-addon has-size-sm field-jadwaloperasiform-ruangan_id required">
                                                <label class="text-left control-label col-sm-5 has-star"><b><?= Yii::t("fe", "Ruangan") ?></b></label>
                                                <div class="col-sm-7">
                                                    <select name="JadwalOperasiForm[ruangan_id]" id="ruangan-form" class="form-control"></select>
                                                </div>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Pemakaian Implant") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['pemakaian_implant']) ? $data['pemakaian_implant'] : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Sewa Alat RS") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['sewa_alat_rs']) ? $data['sewa_alat_rs'] : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group highlight-addon has-size-sm field-jadwaloperasiform-kamarruangan_id required">
                                                <label class="text-left control-label col-sm-5 has-star"><b><?= Yii::t("fe", "No Kamar Bedah") ?></b></label>
                                                <div class="col-sm-7">
                                                    <select name="JadwalOperasiForm[kamarruangan_id]" id="kamar-form" class="form-control"></select>
                                                </div>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Jenis Operasi") ?></b></label>
                                            <div class="col-sm-5">
                                            <?php
                                                if ($data['jenis_operasi_cyto'] == true){ ?>
                                                    <p><b>-</b>&nbsp;Operasi Cyto </p>
                                            <?php    
                                            } 
                                            ?>
                                            <?php
                                                if ($data['jenis_operasi_elektif'] == true){ ?>
                                                    <p><b>-</b>&nbsp;Operasi Elektif </p>
                                            <?php    
                                            } 
                                            ?>
                                            <?php
                                                if ($data['jenis_operasi_odc'] == true){ ?>
                                                    <p><b>-</b>&nbsp;Operasi ODC </p>
                                            <?php    
                                            }
                                            else{     
                                            ?>
                                             <p><b>:</b>&nbsp;<?=  $data['kegiatanoperasi_nama'] ?> </p>
                                            <?php
                                                   } ?>
                                                   
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Tanggal Operasi") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?php echo $data['tgl_permintaan'] ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <?php
                                            if ($data['jenis_operasi_odc']){ ?>
                                            <div class="form-group highlight-addon has-size-sm field-jadwaloperasiform-pendaftaran_id required">
                                                <label class="text-left control-label col-sm-5 has-star"><b><?= Yii::t("fe", "No Pendaftaran") ?></b></label>
                                                <div class="col-sm-7">
                                                    <select name="JadwalOperasiForm[pendaftaran_id]" id="pendaftaranid-form" class="select2 form-control"> </select>
                                                </div>
                                                <div class="help-block"></div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="text-left control-label col-sm-5"><b>
                                                <?= Yii::t("fe", "Status Jadwal Operasi") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?php echo ucwords(strtolower(ArrayHelper::getValue($data, 'status', ''))); ?> </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Jenis Pemeriksaan");?></th>
                                        <th><?=\Yii::t("fe", "Nama Pemeriksaan");?></th>
                                        <th><?=\Yii::t("fe", "Cyto");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <?php 
                                $form = ActiveForm::begin([
                                    'id' => 'ajax-form', 
                                    'action' => '/apotek/pemakaian-obat-alkes/set-list-item',
                                    'enableAjaxValidation'=>false, 
                                    'enableClientValidation'=>false,
                                    'type' => ActiveForm::TYPE_VERTICAL,
                                    'formConfig' => [
                                        'labelSpan' => 3, 
                                        'deviceSize' => ActiveForm::SIZE_SMALL
                                    ],
                                    'options' => [
                                        'skip-confirm' => "true"
                                    ]
                                ]); 
                            ?>
                           <?= $form->field($model, 'catatan', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-4'
                                            ]
                                        ])->textArea([
                                            'class' => 'form-control input-sm ',
                                            'autocomplete' => "off",
                                            'rows' => 6
                                        ]); ?>
                                        
                            <?=Html::activeHiddenInput($model, 'jenis_operasi_odc',[
                                'value' => !empty($data['jenis_operasi_odc']) ? $data['jenis_operasi_odc'] : false,
                            ])?>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>

<?php
$list_pendaftaran = json_encode($list_pendaftaran);
$this->registerJs("
    var _listPendaftaran = '{$list_pendaftaran}';
    var _tabel;
    const surgeryId = '{$id}'
    const kelasPelayananId = '{$data["kelaspelayanan_id"]}'
    const ruanganId = '{$data["ruangan_id"]}'
    const ruanganNama = '{$data["ruangan_nama"]}'
    const kamarRuanganId = '{$data["kamarruangan_id"]}'
    const kamarRuanganText = '{$data["kamarruangan_nokamar"]}'

    $('#tolak-pasien').on('click', function (event) {
        event.preventDefault();
        $().docoForm('click',{
            url : '/bedah/jadwal-operasi/tolak?id={$id}',
            data : $('#ajax-form').serializeArray(),
            confirmMessage : 'Apakah anda yakin, untuk menolak pasien ini ?',
            success : function (data) {
                setTimeout(function(){ 
                    var href = $('.data-back').attr('href');
                    window.location.href = href;
                }, 1000);
            }
        });
    });

    $(document).ready(function(){
        table = $('#example').docoTabel({
            filter: true,
            sorting: false, 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: function(data, callback, settings){
                $.ajax({
                    url: 'get-data-pemeriksaan?id=$id',
                    data: data,
                    error: function (data) {
                        if (typeof data.responseJSON.metaData != 'undefined') {
                            var message = data.responseJSON.metaData.message;
                            docoNotification('warning', message, '');
                        }
                        tabelErrorHandling('example');
                    },
                    success: function(data)
                    {
                        if(data.order_tindakan == false){
                            tabelErrorHandling('example');
                        }else{
                            callback(data);
                        }
                    }
                });

            },
           
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Jenis Pemeriksaan',
                    data: 'jenis_pemeriksaan',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Nama Pemeriksaan',
                    data: 'operasi_nama',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Cyto',
                    data: 'is_cyto',
                    searchable: false,
                    orderable: false
                },
            ],
        });
        $('.dataTables_filter').hide();
    });

    function tabelErrorHandling(id) {
        $('#' + id + '_processing').hide();
        $('.dataTables_empty').html('Data tidak ditemukan.');
    }

", VIEW::POS_END, 'js-kunings');

$this->registerJs($this->render('js/view.js'), View::POS_END, 'js');
