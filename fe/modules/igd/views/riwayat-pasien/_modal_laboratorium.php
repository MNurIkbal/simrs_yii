<?php

/**
 * @Author: Ardi
 * @Date:   2018-10-11 13:10
 */

use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\ArrayHelper;
?>

<style type="text/css">
    .modal-body {
    position: relative;
    padding-top: 5px !important;
}
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Riwayat Laboratorium - <?= ArrayHelper::getValue($infoPasien, 'nama_pasien', '-') . ' / ' . ArrayHelper::getValue($infoPasien, 'penjamin_nama', '-') . ' / ' . ArrayHelper::getValue($infoPasien, 'kelaspelayanan_nama', '-')?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-2">
                        Nama Pasien
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?=@$infoPasien['nama_pasien']?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-2">
                        Alamat Pasien
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?=@$infoPasien['alamat_pasien']?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-2">
                        Umur
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?=@$infoPasien['umur']?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-2">
                        No RM
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?=@$infoPasien['no_rekam_medik']?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table id="tabel-reseptur" class="table table-striped table-hover datatable-basic dataTable" style="width:100%;">
                <thead>
                    <tr class="bg-inverse">
                        <th>No</th>
                        <th><?=Yii::t('fe', 'No Pendaftaran')?></th>
						<th><?=Yii::t('fe', 'Ruangan / Kamar')?></th>
						<th><?=Yii::t('fe', 'No Rujukan')?></th>
						<th><?=Yii::t('fe', 'Dokter Penunjang')?></th>
						<th><?=Yii::t('fe', 'Tanggal Pemeriksaan')?></th>
						<th><?=Yii::t('fe', 'Nama Pemeriksaan')?></th>
						<th><?=Yii::t('fe', 'Status Order')?></th>
						<th><?=Yii::t('fe', 'Status Periksa')?></th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(count($listLab) > 0): ?>
                    <?php
                        $rowNum = 1;
                        foreach ($listLab as $group_rujukan) {
                            $count_rujukan = count($group_rujukan);
                            $i_detail = 1;
                            $flag_btn = true;
                            $count_btn = $count_rujukan;
                            foreach ($group_rujukan as $norujuk => $val_lab) {
                    ?>
                        <tr>
                            <?php 
                                $rowspan_rujukan = $count_rujukan;
                                if($i_detail == 1){
                                    $status_dipakai = '-';
                                    if ( $val_lab['tipe'] == 'wyna') {
                                        if ( $val_lab['is_hasil'] == true ) {
                                            $status_dipakai = DocoConstants::STATUS_LAB_SELESAI;
                                        } else {
                                            $status_dipakai = DocoConstants::STATUS_LAB_BELUM_DIPERIKSA;
                                        }
                                    } else {
                                        if(is_null($val_lab['stat_periksa'])){
                                            $status_dipakai = @$val_lab['stat_penunjang'];
                                        }else{
                                            $status_dipakai = @$val_lab['stat_periksa'];
                                        }
                                    }
                            ?>
                            <td rowspan="<?=$rowspan_rujukan?>"><?=$rowNum?></td>
                            <td rowspan="<?=$rowspan_rujukan?>"><?=@$val_lab['no_pendaftaran']?></td>
                            <td rowspan="<?=$rowspan_rujukan?>"><?=@$val_lab['ruangan_nama']?></td>
                            <td rowspan="<?=$rowspan_rujukan?>"><?=@$val_lab['no_rujukan']?></td>
							<td rowspan="<?=$rowspan_rujukan?>"><?=@$val_lab['dokter_penunjang']?></td>
                            <td rowspan="<?=$rowspan_rujukan?>"><?=DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($val_lab['tglmasukpenunjang'])), false, true)?></td>
                            <?php }?>
                            <td><?=@$val_lab['daftartindakan_nama']?></td>
                            <?php 
								$status_dipakai = '-';
								if(!is_null($val_lab['stat_penunjang'])){
									$status_dipakai = @$val_lab['stat_penunjang'];
								}else{
									$status_dipakai = @$val_lab['status_periksa_nama'];
								}
								?>

							<?php ?> 
                            <td><?=$status_dipakai?></td>
							<td><?=@$val_lab['status_periksa_nama']?></td>
                            <?php
                                if ($rowspan_rujukan == $count_btn) {
                                    if ($flag_btn) {
                            ?>
                            <td rowspan="<?=$rowspan_rujukan?>">
                            <?php
                                    $btn_kritis = 'btn-info';
                                    if(isset($val_lab['is_kritis']) && $val_lab['is_kritis'] == TRUE){
                                        $btn_kritis = 'btn-danger';
                                    }
                                    if ( $val_lab['tipe'] == 'wyna') {
                                        $url = '/laboratorium/integrasi-lis-hasil/cetak?id='.DocoHelpers::encrypt($val_lab['pasienmasukpenunjang_id']);
                                        $status_periksa = DocoConstants::STATUS_LAB_SELESAI;
                                    }else{
                                        $status_periksa = '-';
                                        if(strtoupper($val_lab['stat_periksa']) == DocoConstants::STATUS_LAB_SELESAI){
                                            $status_periksa = DocoConstants::STATUS_LAB_SELESAI;
                                        }
                                        $url = '/laboratorium/integrasi-lis-hasil/cetak?id='.DocoHelpers::encrypt($val_lab['pasienmasukpenunjang_id']);
                                    }
                                    if(strtoupper($status_dipakai) == $status_periksa){
                                        echo Html::button('Lihat Hasil',[
                                            'class'=>'btn '.$btn_kritis.' btn-sm btn-riwayat btn-cetak-penunjang',
                                            'data-url' => $url,
                                            'data-norujukan' => @$val_lab['no_rujukan']
                                            ]
                                        );
                                        
                                    }


                                    $flag_btn = false;
                            ?>
                            </td>
                            <?php
                                    }
                                }
                            ?>
                        </tr>
                    <?php
                            $count_btn--;
                            $i_detail++;
                            }
                        $rowNum++;
                        }
                    ?>
                    <?php else: ?>
                        <tr>
                            <td class="text-center" colspan="12"><?=\Yii::t("app", "Tidak ada data.");?></td>
                            
                        </tr>
                     <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/modal_lab.js'), View::POS_END);
?>