<?php
// Author : Faidzin

use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\web\View;
use yii\Helpers\Html;
use yii\Helpers\Url;

$this->title = \Yii::t('fe', 'Rincian tagihan pasien penunjang');
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$dataHeader = $data['response']['header'];
$dataHeader['tgl_pendaftaran'] = date('Y-m-d', strtotime($dataHeader['tgl_pendaftaran']));
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
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
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
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'back',
                        'print-pdf'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Cetak PDF'),
                            'icon' => 'fa fa-file-pdf-o',
                            // 'method' => '',
                            'attributes' => [
                                'id'=>'btn-print-pdf',
                                'class'=>'btn-print-detail',
                                'data-options'=>'click',
                                'data-url'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-pdf?id=' . DocoHelpers::encrypt($dataHeader['pendaftaran_id']).'&ruangan='.$ruangan,
                            ]
                        ],
                    ]);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="row row-eq-height " style="margin-top:10px;">
                    <div class="col-md-8" id="informasi-pendaftaran">
                        <div class="panel panel-default">
                            <a id="info-heading-pendaftaran" data-toggle="collapse" href="#infopendaftaran" role="button" aria-expanded="false" aria-controls="infopendaftaran" >
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><?= Yii::t('fe', 'Informasi Pasien') ?></h6>
                                    <p class="p-data" id="data-pendaftaran">
                                        <?= isset($dataHeader['no_rekam_medik']) ? $dataHeader['no_rekam_medik'] : '-' ?> - 
                                        <b class="font" ><?= isset($dataHeader['nama_pasien']) ? $dataHeader['nama_pasien'] : '-' ?></b>
                                        (<?= isset($dataHeader['tanggal_lahir']) ? date('d M Y', strtotime($dataHeader['tanggal_lahir'])) : '-' ?>) 
                                    </p>
                                    
                                    <ul class="icons-list">
                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                    </ul>          
                                </div>
                            </a>
                            <div class='panel-body collapse multi-collapse info-card"' id="infopendaftaran">
                                <div class="col-xs-2">
                                    <div class="border-img">
                                        <?php 
                                        $filename = isset($dataHeader['photopasien']) ? !empty($dataHeader['photopasien']) ? '/media/img/pasien/'.$dataHeader['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                                        ?>
                                        <?=Html::img($filename, [ 'style'=>'width: 100%;height: auto;max-width: 114px;', 'class'=>'img-responsive'])?>
                                    </div>
                                </div>
                                <div class="col-xs-10">
                                    <div class="row">
                                        <br>
                                        <div class="col-xs-6">
                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "No Rekam Medik") ?></b>
                                            <br>
                                            <p>
                                                <?= isset($dataHeader['no_rekam_medik']) ? $dataHeader['no_rekam_medik'] : '-' ?> -
                                                <?= isset($dataHeader['nama_pasien']) ? $dataHeader['nama_pasien'] : '-' ?>
                                            </p>
                                            
                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Tanggal Lahir") ?></b>
                                            <br>
                                            <p>
                                                <?= isset($dataHeader['tanggal_lahir']) ? date('d M Y', strtotime($dataHeader['tanggal_lahir'])) : '-' ?> - 
                                                (<?= isset($dataHeader['umur']) ? $dataHeader['umur'] : '-' ?>)
                                            </p>
                                        </div>
                                        <div class="col-xs-6">
                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Tanggal pendaftaran") ?></b>
                                            <p>
                                                (<?= isset($dataHeader['tgl_pendaftaran']) ? date('d-M-Y', strtotime($dataHeader['tgl_pendaftaran'])) : '-' ?>) - 
                                                <?= isset($dataHeader['no_pendaftaran']) ? $dataHeader['no_pendaftaran'] : '-' ?>
                                            </p>

                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Kelas pelayanan") ?></b>
                                            <p>
                                                <?= isset($dataHeader['kelaspelayanan_nama']) ? $dataHeader['kelaspelayanan_nama'] : '-' ?> -
                                                <?= isset($dataHeader['carabayar_nama']) ? $dataHeader['carabayar_nama'] : '-' ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <a id="info-heading" data-toggle="collapse" href="#infodetail" role="button" aria-expanded="false" aria-controls="infopasien" >
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Informasi Pasien'); ?></b></h6>
                                    <ul class="icons-list">
                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                    </ul>         
                                </div>
                            </a>
                            <div class="panel-body column-info collapse multi-collapse info-card" id="infodetail">
                                <div class="row row-eq-height">
                                    <br>
                                    <div class="col-xs-6">
                                        <b class="text-left control-label font-design"><?= Yii::t("fe", " Penyakit") ?></b>
                                        <p>
                                            <?= isset($dataHeader['jeniskasuspenyakit_nama']) ? $dataHeader['jeniskasuspenyakit_nama'] : '-' ?> 
                                        </p>
                                        
                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Dokter") ?></b>
                                        <p>
                                            <?= isset($dataHeader['dokter']) ? $dataHeader['dokter'] : '-' ?>
                                        </p>
                                    </div>
                                    
                                    <div class="col-xs-6">
                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Ruangan") ?></b>
                                        <p> 
                                            <?= !empty($dataHeader['ruangan_nama']) ? $dataHeader['ruangan_nama'] : null ?> 
                                        </p>

                                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Status") ?></b>
                                        <p>
                                            <?= isset($dataHeader['jumlah_tagihan']) ? $dataHeader['jumlah_tagihan'] 
                                                ? Yii::t('fe', 'Belum lunas') 
                                                : Yii::t('fe', 'Lunas') : '-' ?>  
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>

                
                <?php foreach ($detailPerRuangan as $key=>$details) : ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class='panel panel-default'>
                                    <div class="panel-heading">
                                        <h6 class="panel-title"><?= Yii::t('fe','Pemeriksaan ' . $key); ?></h6>
                                    </div>
                                    <div class="panel-body">
                                        <table width="100%" class="table table-striped table-condensed table-hover" style="margin-top: 10px">
                                            <thead>
                                                <tr class="bg-inverse">
                                                    <th><?= Yii::t('fe', 'No'); ?></th>
                                                    <th><?= Yii::t('fe', 'Tanggal pemeriksaan'); ?></th>
                                                    <th><?= Yii::t('fe', 'Nama pemeriksaan'); ?></th>
                                                    <th><?= Yii::t('fe', 'Tarif satuan'); ?></th>
                                                    <th><?= Yii::t('fe', 'Tarif satuan cyto'); ?></th>
                                                    <th><?= Yii::t('fe', 'Qty'); ?></th>
                                                    <th><?= Yii::t('fe', 'Jumlah'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $no = 1;
                                            $total = 0;
                                            ?>
                                            <?php foreach ($details as $detail): ?>
                                                <tr>
                                                    <td><?= $no; ?></td>
                                                    <td><?= date('d-M-Y H:i:s',strtotime($detail['tglmasukpenunjang'])); ?></td>
                                                    <td><?= $detail['daftartindakan_nama']; ?></td>
                                                    <td><?= DocoHelpers::rupiahDisplay($detail['tarif_satuan']); ?></td>
                                                    <td><?= DocoHelpers::rupiahDisplay($detail['tarifcyto_tindakan']); ?></td>
                                                    <td><?= $detail['qty_tindakan']; ?></td>
                                                    <td><?= DocoHelpers::rupiahDisplay($detail['tarif_tindakan']); ?></td>
                                                </tr>
                                                <?php
                                                $no++; 
                                                $total += $detail['tarif_tindakan']; 
                                                ?>
                                            <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan=4 class="text-right"></td>
                                                    <td><b><?= Yii::t('fe', 'Total'); ?></b></td>
                                                    <td></td>
                                                    <td><b><?= DocoHelpers::rupiahDisplay($total); ?></b></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <br />
                                </div>
                            </div>
                        </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs("
$(document).on('click', '#btn-print-pdf', function(){
    window.open($(this).data('url'), '_blank');
});
$(document).ready(function(){
  $('#info-heading-pendaftaran').click(function(){
    $('#data-pendaftaran').toggle();
  });
});
", View::POS_END, 'b-index');
?>
