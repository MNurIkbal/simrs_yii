<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Laboratorium', 'url' => ['/laboratorium']];
$this->params['breadcrumbs'][] = $title;

?>

<style>
    .dataTables_scroll {
        max-height: 99999em !important
    }
</style>

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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
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
                <?= DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'pdf',
                        'export-excel-serconn' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-serconn',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-url' => Url::home() . 'laboratorium/lap-pemeriksaan/show-popup-excel?',
                                'data-width' => '75%'
                            ]
                        ],
                    ],'#table-pasien-lab');
                ?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="table-pasien-lab">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?=Yii::t('fe', 'No'); ?></th>
                            <th><?=Yii::t('fe', 'Tanggal masuk'); ?></th>
                            <th><?=Yii::t('fe', 'No Pendaftaran'); ?></th>
                            <th><?=Yii::t('fe', 'No Rekam Medis'); ?></th>
                            <th><?=Yii::t('fe', 'Nama Pasien'); ?></th>
                            <th><?=Yii::t('fe', 'Nama dokter'); ?></th>
                            <th><?=Yii::t('fe', 'Dokter DPJP'); ?></th>
                            <th><?=Yii::t('fe', 'Kelas Pelayanan'); ?></th>
                            <th><?=Yii::t('fe', 'Kelompok pemeriksaan'); ?></th>
                            <th><?=Yii::t('fe', 'Jenis pemeriksaan'); ?></th>
                            <th><?=Yii::t('fe', 'Nama pemeriksaan'); ?></th>
                            <th><?=Yii::t('fe', 'Qty'); ?></th>                            
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
$phpVars = [
    'columnsLabel' => [
        'tanggalMasuk' => \Yii::t("fe", "Tanggal masuk"),
        'noPendaftaran' => \Yii::t("fe", "No Pendaftaran"),
        'noRekamMedis' => \Yii::t("fe", "No Rekam Medis"),
        'namaPasien' => \Yii::t("fe", "Nama Pasien"),
        'namaDokter' => \Yii::t("fe", "Nama dokter"),
        'namaDokterDPJP' => \Yii::t("fe", "Dokter DPJP"),
        'kelasPelayanan' => \Yii::t("fe", "Kelas Pelayanan"),
        'kelompokPemeriksaan' => \Yii::t("fe", "Kelompok pemeriksaan"),
        'jenisPemeriksaan' => \Yii::t("fe", "Jenis pemeriksaan"),
        'namaPemeriksaan' => \Yii::t("fe", "Nama pemeriksaan"),
        'hargaSatuan' => \Yii::t("fe", "Harga satuan (Rp.)"),
        'qty' => \Yii::t("fe", "Qty"),
        'hargaCyto' => \Yii::t("fe", "Cyto (Rp.)"),
        'totalHarga' => \Yii::t("fe", "Total (Rp.)"),
    ],
    'filter' => [
        'tglMasuk' => '<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="'.date('d-M-Y').'"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate" value="'.date('d-M-Y').'"/><input type="text" style="display:none" class="targetDate" col-index=2></div>',
        'selectDokter' => Html::dropDownList('nama_dokter', '', [], [
            'class' => 'form-control select2 selectDokter', 
            'prompt' => \Yii::t('fe', 'Pilih Nama Dokter')
        ]),
        'selectKelasPelayanan' => Html::dropDownList('kelaspelayanan_nama', '', $listkelaspelayanan, [
            'class' => 'form-control select2',
            'prompt' => \Yii::t('fe', 'Pilih Kelas Pelayanan')
        ]),
        'selectKelompokPemeriksaan' => Html::dropDownList('kelompokpemeriksaanlab_id', '', ['' => '-- Pilih --'], [
            'id' => 'select-kelompok',
            'class' => 'form-control select2 selectKelompok',
            'prompt' => \Yii::t('fe', 'Pilih Kelompok Pemeriksaan')
        ]),
        'selectJenisPemeriksaan' => Html::dropDownList('jenispemeriksaanlab_id', '', [], [
            'class' => 'form-control select2 selectJenis',
            'prompt' => \Yii::t('fe', 'Pilih Jenis Pemeriksaan')
        ]),
        'selectNamaPemeriksaan' => Html::dropDownList('daftartindakan_id', '', [], [
            'class' => 'form-control select2 selectPemeriksaan',
            'prompt' => \Yii::t('fe', 'Pilih Nama Pemeriksaan')
        ]),
    ]
];
$this->registerJsVar('phpVars', $phpVars);
$this->registerJs($this->render('_index_mhsb.js'), View::POS_END);
?>