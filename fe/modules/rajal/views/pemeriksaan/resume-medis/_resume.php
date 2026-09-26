<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-01-17 15:24:43
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-01-23 11:01:37
 */

use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>
<div class="row">
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe', 'Resume Medis')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'pdf' => [
                    'attributes' => [
                        'data-target' => '/rajal/pemeriksaan/cetak-resume?pendaftaran_id='. $pendaftaran_id .'&pasien_id='. $pasien_id,
                        'data-options' => 'click',
                        'id' => 'btn-cetak-resume'
                    ],
                ],
            ]);
            ?>
        </div>
        <div class="panel-body">
            <!-- Panel Diagnosa -->
            <div class="panel panel-default">
                <div class="panel-heading"> 
                    <h5 class="panel-title"><?=Yii::t('fe', 'Diagnosa')?></h5>
                </div>
                <div class="panel-body">
                    <br>
                    <table id="tabel-diagnosa-resume" class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" width="100%" style="overflow-x: scroll;">
                        <thead>
                            <tr class="bg-inverse">
                                <th><?=Yii::t('fe', 'No')?></th>
                                <th><?=Yii::t('fe', 'kelompokdiagnosa_nama')?></th>
                                <th><?=Yii::t('fe', 'diagnosa_kode')?></th>
                                <th><?=Yii::t('fe', 'diagnosa_nama')?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <br>
                </div>
            </div>
            <!-- End Panel Diagnosa -->

            <!-- Panel Anamnesa -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><?=Yii::t('fe', 'Anamnesa Dan Penyakit Fisik')?></h5>
                </div>
                <div class="panel-body">
                   <div class="row">
                       <div class="col-md-3">
                           <table class="table borderless-table">
                               <tr>
                                   <td style="border: none"><b>Tekanan Darah</b></td>
                               </tr>
                               <tr>
                                   <td style="border: none"><?=isset($datapemeriksaan['tekanandarah']) ? $datapemeriksaan['tekanandarah'].' MmHg' : '-'?></td>
                               </tr>
                           </table>
                       </div>
                       <div class="col-md-3">
                           <table class="table">
                               <tr>
                                   <td style="border: none"><b>Pernafasan</b></td>
                               </tr>
                               <tr>
                                   <td style="border: none"><?=isset($datapemeriksaan['pernapasan']) ? $datapemeriksaan['pernapasan'].' x/m' : '-'?></td>
                               </tr>
                           </table>
                       </div>
                       <div class="col-md-3">
                           <table class="table">
                               <tr>
                                   <td style="border: none"><b>Berat Badan</b></td>
                               </tr>
                               <tr>
                                   <td style="border: none"><?=isset($datapemeriksaan['beratbadan_kg']) ? $datapemeriksaan['beratbadan_kg'].' kg' : '-'?></td>
                               </tr>
                           </table>
                       </div>
                       <div class="col-md-3">
                           <table class="table">
                               <tr>
                                   <td style="border: none"><b>Tinggi Badan</b></td>
                               </tr>
                               <tr>
                                   <td style="border: none"><?=isset($datapemeriksaan['tinggibadan_cm']) ? $datapemeriksaan['tinggibadan_cm'].' cm' : '-'?></td>
                               </tr>
                           </table>
                       </div>
                   </div>
                   <div class="row">
                       <div class="col-md-3">
                           <table class="table">
                               <tr>
                                   <td style="border: none"><b>Nadi</b></td>
                               </tr>
                               <tr>
                                   <td style="border: none"><?=isset($datapemeriksaan['detaknadi']) ? $datapemeriksaan['detaknadi'].' x/m' : '-'?></td>
                               </tr>
                           </table>
                       </div>
                       <div class="col-md-3">
                           <table class="table">
                               <tr>
                                   <td style="border: none"><b>Suhu</b></td>
                               </tr>
                               <tr>
                                   <td style="border: none"><?=isset($datapemeriksaan['suhutubuh']) ? $datapemeriksaan['suhutubuh'].' c' : '-'?></td>
                               </tr>
                           </table>
                       </div>
                       <div class="col-md-3">
                           <table class="table">
                               <tr>
                                   <td style="border: none"><b>Nyeri</b></td>
                               </tr>
                               <tr>
                                   <td style="border: none"><?=isset($datapemeriksaan['anamnesa']['is_nyeri']) ? $datapemeriksaan['anamnesa']['is_nyeri'] ? \Yii::t('fe', 'Ya') .', Skala '.$datapemeriksaan['anamnesa']['skala_nyeri']  : \Yii::t('fe', 'Tidak') : '-'?></td>
                               </tr>
                           </table>
                       </div>
                       <div class="col-md-3">
                           <table class="table">
                               <tr>
                                   <td style="border: none"><b>Resiko Jatuh</b></td>
                               </tr>
                               <tr>
                                   <td style="border: none"><?=isset($datapemeriksaan['anamnesa']['is_resikojatuh']) ? ($datapemeriksaan['anamnesa']['is_resikojatuh']) ? Yii::t('fe', 'Ya') : Yii::t('fe', 'Tidak') : '-'?></td>
                               </tr>
                           </table>
                       </div>
                   </div>
                </div>
            </div>
            <!-- End Panel Anamnesa -->

            <!-- Panel Terapi -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><?=Yii::t('fe', 'Terapi')?></h5>
                </div>
                <div class="panel-body">
                    <br>
                   <div class="row">
                       <div class="col-md-6">
                           <legend style="font-weight:bold;"><?= Yii::t('fe', 'Tindakan Medis') ?></legend>
                           <table id="tabel-tindakan-terapi-resume" class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" width="100%" style="overflow-x: scroll;">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th><?=Yii::t('fe', 'No')?></th>
                                        <th><?=Yii::t('fe', 'Nama Tindakan / Paket')?></th>
                                        <th><?=Yii::t('fe', 'Jumlah')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                       </div>
                       <div class="col-md-6">
                           <legend style="font-weight:bold;"><?= Yii::t('fe', 'Pemakaian Alkes / Obat') ?></legend>
                           <table id="tabel-obat-terapi-resume" class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" width="100%" style="overflow-x: scroll;">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th><?=Yii::t('fe', 'No')?></th>
                                        <th><?=Yii::t('fe', 'Nama Tindakan')?></th>
                                        <th><?=Yii::t('fe', 'Obat / Alkes')?></th>
                                        <th><?=Yii::t('fe', 'Jumlah')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                       </div>
                   </div>
                   <br>
                </div>
            </div>
            <!-- End Panel Terapi -->

            <!-- Panel Penunjang -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><?=Yii::t('fe', 'Penunjang')?></h5>
                </div>
                <div class="panel-body">
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <legend style="font-weight:bold;"><?= Yii::t('fe', 'Laboratorium') ?></legend>
                            <table id="tabel-lab-resume" class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" width="100%" style="overflow-x: scroll;">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th><?=Yii::t('fe', 'No')?></th>
                                        <th><?=Yii::t('fe', 'Tanggal Pemeriksaan')?></th>
                                        <th><?=Yii::t('fe', 'Jenis Pemeriksaan')?></th>
                                        <th><?=Yii::t('fe', 'Nama Pemeriksaan')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <legend style="font-weight:bold;"><?= Yii::t('fe', 'Radiologi') ?></legend>
                            <table id="tabel-rad-resume" class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" width="100%" style="overflow-x: scroll;">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th><?=Yii::t('fe', 'No')?></th>
                                        <th><?=Yii::t('fe', 'Tanggal Pemeriksaan')?></th>
                                        <th><?=Yii::t('fe', 'Jenis Pemeriksaan')?></th>
                                        <th><?=Yii::t('fe', 'Nama Pemeriksaan')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <legend style="font-weight:bold;"><?= Yii::t('fe', 'Bedah Sentral') ?></legend>
                            <table id="tabel-bedah-resume" class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" width="100%" style="overflow-x: scroll;">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th><?=Yii::t('fe', 'No')?></th>
                                        <th><?=Yii::t('fe', 'Tanggal Pemeriksaan')?></th>
                                        <th><?=Yii::t('fe', 'Jenis Pemeriksaan')?></th>
                                        <th><?=Yii::t('fe', 'Nama Pemeriksaan')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <legend style="font-weight:bold;"><?= Yii::t('fe', 'Rehab Medik') ?></legend>
                            <table id="tabel-rehab-resume" class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" width="100%" style="overflow-x: scroll;">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th><?=Yii::t('fe', 'No')?></th>
                                        <th><?=Yii::t('fe', 'Tanggal Pemeriksaan')?></th>
                                        <th><?=Yii::t('fe', 'Jenis Pemeriksaan')?></th>
                                        <th><?=Yii::t('fe', 'Nama Pemeriksaan')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
            <!-- End Panel Penunjang -->

            <!-- Panel Obat -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><?=Yii::t('fe', 'Obat')?></h5>
                </div>
                <div class="panel-body">
                    <br>
                    <table id="tabel-obat-resume" class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" width="100%" style="overflow-x: scroll;">
                        <thead>
                            <tr class="bg-inverse">
                                <th><?=Yii::t('fe', 'No')?></th>
                                <th><?=Yii::t('fe', 'Racikan / Non Racikan')?></th>
                                <th><?=Yii::t('fe', 'R Ke-')?></th>
                                <th><?=Yii::t('fe', 'Nama Obat')?></th>
                                <th><?=Yii::t('fe', 'Satuan Kecil')?></th>
                                <th><?=Yii::t('fe', 'Signa')?></th>
                                <th><?=Yii::t('fe', 'Qty')?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <br>
                </div>
            </div>
            <!-- End Panel Obat -->
        </div>
    </div>
</div>

<?php 

$this->registerJs("
    var pendaftaran_id = '".$pendaftaran_id."';
    var pasien_id = '".$pasien_id."';
  ".$this->render('js/index.js'), View::POS_END, 'js');

?>