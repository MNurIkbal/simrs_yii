<?php
//author: Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Pengiriman Dokumen Rekam Medik');
// $this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$this->title?></h3>
                <?= Breadcrumbs::widget([
                        'homeLink' => [ 
                            'label' => Yii::t('yii', 'Home'),
                            'url' => Yii::$app->homeUrl,
                        ],
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]);
                ?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'back',
                        'save-kirim' => [
                            'type' => 'button',
                            'method' => 'ss',
                            'icon' => 'fa fa-floppy-o',
                            'title' => \Yii::t('fe', 'Simpan'),
                            'attributes' => [
                                'id' => 'btn-simpan-kirim',
                                'form_id' => 'kirim-form',
                                'class' => 'btn btn-success btn-labeled btn-xs', 
                                'data-options' => 'click',
                                'disabled' => $disabled     
                            ] 
                        ],
                        'pdf' => [
                            'type' => 'button',
                            'icon' => 'fa fa-file-pdf-o',
                            'title' => 'Print',
                            'attributes' => [
                                'data-options' => 'click',
                                'data-target' => Url::to(['/informasi/pemesanan-dokumen-masuk/print-pengiriman?id='.$id.'&']),
                            ]
                        ]
                    ], "#table-dok-keluar-detail");?>  
                <div class="pull-right"> 
                    <?=DocoHelpers::generateToolbar([
                        'reset',
                    ], "#table-dok-keluar-detail");?>  
                </div>
            </div>
            <div class="panel-body">     
                <?php 
                    $form = ActiveForm::begin([
                        'class'=>'kirim-form',
                        'id'=>'kirim-form',      
                    ]);
                ?>           
                <!-- Informasi Resep -->
                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    
                    <div class="panel-body">
                        <div class="row form-horizontal">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Instalasi Tujuan') ?></label>
                                    <div class="col-lg-6">
                                        <?= $form->field($model, 'instalasi_tujuan')
                                            ->textInput([
                                                'placeholder' => $model->getAttributeLabel('instalasi_tujuan'),
                                                'readonly'=>true,
                                                'class' => 'form-control input-sm'])->label(false); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Tanggal Pengiriman') ?></label>
                                    <div class="col-lg-6">
                                        <?= $form->field($model, 'tgl_kirim')
                                            ->textInput([
                                                'placeholder' => $model->getAttributeLabel('tgl_kirim'),
                                                'readonly' => true,
                                                'value' => date('Y-m-d'),
                                                'class' => 'form-control input-sm'])->label(false); ?>
                                    </div>
                            </div>
                        </div>
                        <div class="row form-horizontal">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Ruangan Tujuan') ?></label>
                                    <div class="col-lg-6">
                                        <?= $form->field($model, 'ruangan_tujuan')
                                            ->textInput([
                                                'placeholder' => $model->getAttributeLabel('ruangan_tujuan'),
                                                'readonly'=>true,
                                                'class' => 'form-control input-sm'])->label(false); ?>
                                        <?= $form->field($model, 'ruanganpemesan_id')
                                            ->hiddenInput()->label(false); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label col-lg-3"><?= Yii::t('fe', 'Nama Pengirim') ?></label>
                                    <div class="col-lg-6">
                                        <?=
                                            $form->field($model, 'pegawaipengirim_id')
                                            ->dropDownList($list_pegawai, [
                                                'class' => 'select2 selectPegawai',
                                                'id' => 'pegawaipengirim_id',
                                                'prompt' => Yii::t('fe', '-- Pilih --')
                                            ])->label(false);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-condensed table-hover" id="table-dok-keluar-detail" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?= Yii::t('fe', 'Tanggal Rekam Medik') ?></th>
                                    <th><?= Yii::t('fe', 'Lokasi Rak') ?></th>
                                    <th><?= Yii::t('fe', 'Lokasi Sub Rak') ?></th>
                                    <th><?= Yii::t('fe', 'Nomor Rekam Medik') ?></th>
                                    <th><?= Yii::t('fe', 'Nama Pasien') ?></th>
                                    <th><?= Yii::t('fe', 'Warna Dokumen') ?></th>
                                </tr>
                                </tr>
                            </thead>
                            <tbody>                                
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php 
    
    $this->registerJs("
        var id = '".$id."';
        var disabled = '".$disabled_print."';

        $('.data-pdf').prop('disabled', disabled);
        $(document).ready(function(){
            table = $('#table-dok-keluar-detail').docoTabel({
                filter: false,
                sorting: [[1,'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                oLanguage: {
                    sLengthMenu: '".(\Yii::t('fe', 'dt_length_menu'))."',
                    sZeroRecords: '".(\Yii::t('fe', 'dt_zero_records'))."',
                    sEmptyTable: '".(\Yii::t('fe', 'dt_empty_table'))."',
                    sInfoFiltered: '".(\Yii::t('fe', 'dt_info_filtered'))."',
                    sInfoEmpty: '".(\Yii::t('fe', 'dt_info_empty'))."',
                    sInfo: '".(\Yii::t('fe', 'dt_info'))."',
                    oPaginate: {
                        sFirst: '".(\Yii::t('fe', 'dt_first_page'))."',
                        sPrevious: '".(\Yii::t('fe', 'dt_previous_page'))."',
                        sNext: '".(\Yii::t('fe', 'dt_next_page'))."',
                        sLast: '".(\Yii::t('fe', 'dt_last_page'))."'
                    }
                },
                ajax: baseUrl+'informasi/pemesanan-dokumen-keluar/get-data-detail?id=".$_GET['id']."',
                columns: [
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Tanggal Rekam Medik')."',
                        data: 'tglrekammedis',                        
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Lokasi Rak')."',
                        data: 'lokasirak_nama',                        
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Lokasi Sub Rak")."',
                        data: 'subrak_nama',                
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Nomor Rekam Medik")."',
                        data: 'no_rekam_medik', 
                        searchable: false,
                        orderable: false,                       
                    },
                    {
                        title: '".\Yii::t("fe", "Nama Pasien")."',
                        data: 'nama_pasien',                        
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Warna Dokumen")."',
                        data: 'warnadokrm_namawarna',                        
                        searchable: false,
                        orderable: false,
                    },
                ]
            });

            $('.dataTables_filter').hide();

            $(document).on('click','#btn-simpan-kirim', function(e){
                e.preventDefault();
                $('#kirim-form').submit();
            });
            $('#kirim-form').docoForm('submit',{
                success : function(data) {
                    // $('#modal_backdrop').modal('toggle');
                    // table.draw();
                    $('#btn-simpan-kirim').prop('disabled', true);
                    $('.data-pdf').prop('disabled', false);
                }
            });
            $(document).on('click', '.data-pdf', function(e){
                e.preventDefault();
                window.open('/informasi/pemesanan-dokumen-masuk/print-pengiriman?id='+ id);
                return false;
            });
        });
        ");
?>

<script>
</script>
