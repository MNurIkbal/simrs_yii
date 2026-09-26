<?php
//author: Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Detail Pemesanan');
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
                    ], "#table-dok-keluar-detail");?>  
            </div>
            <div class="panel-body">                
                <!-- Informasi Resep -->
                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title text-center"><?= Yii::t('fe', 'BUKTI PEMESANAN DOKUMEN REKAM MEDIK') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <h4 class="panel-title text-center"><?=@$info_pemesanan['ruangan_pemesan']?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                    </div>
                    <div class="panel-body">
                        <div class="row form-horizontal">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label col-lg-4"><b><?= Yii::t('fe', 'Tanggal Pemesanan') ?></b></label>
                                    <div class="col-lg-8">
                                        <div class="form-control-static"><?=@$info_pemesanan['tgl_pesandokrm']?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label col-lg-4"><b><?= Yii::t('fe', 'Instalasi Asal Pemesanan') ?></b></label>
                                    <div class="col-lg-8">
                                        <div class="form-control-static"><?=@$info_pemesanan['instalasi_pemesan']?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row form-horizontal">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label col-lg-4"><b><?= Yii::t('fe', 'Tanggal Minta Dikirim') ?></b></label>
                                    <div class="col-lg-8">
                                        <div class="form-control-static"><?=@$info_pemesanan['tgl_mintakirim']?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label col-lg-4"><b><?= Yii::t('fe', 'Ruangan Asal Pemesanan') ?></b></label>
                                    <div class="col-lg-8">
                                        <div class="form-control-static"><?=@$info_pemesanan['ruangan_pemesan']?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table width="100%" class="table table-striped table-condensed table-hover" id="table-dok-keluar-detail">
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
                        <br>
                        <br>
                        <div class="clear"><br></div>
                        <?= Html::a('<i class="fa fa-print"></i> '. Yii::t('fe', 'Print'), 
                                ['/informasi/pemesanan-dokumen-masuk/print-pemesanan?id='.$id], 
                                [
                                    'class' => 'btn btn-dodger-blue btn-custom-table reseptur',
                                    'title' => Yii::t('fe', 'Print'),
                                    'data-tooltip' => 'tooltip'
                                ]
                            );
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script src=""></script>
<?php 
    $this->registerJs("
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
        });
        ");
?>

<script>
</script>
