<?php

/**
 * @Author: afil
 * @Date:   2018-01-18 13:53:15
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-02-20 12:11:50
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
?>

<div class="panel-body">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe', 'Riwayat resep')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <!--button back-->
        <div class="panel-toolbar clearfix">
            <?=
            DocoHelpers::generateToolbar([
                'cppt' => [
                    'title' => 'CPPT',
                    'icon' => 'fa fa-arrow-left',
                    'attributes' => [
                        'href' => !empty($statepulang) ? '/rajal/inf-pasien-pulang' : '/rajal/pemeriksaan/',
                        'data-options' => 'click',
                        'id' => 'btn-reseptur-back'
                    ]
                ],
            ]) ?>
        </div>
        <div class="panel-body">
            <table width="100%" id="tabel-riwayatreseptur" class="table datatable-basic table-striped table-hover dataTable no-footer table-framed">
                <thead>
                    <tr class="bg-inverse">
                        <th><?=Yii::t('fe', 'Tanggal')?></th>
                        <th><?=Yii::t('fe', 'No reseptur')?></th>
                        <th><?=Yii::t('fe', 'Status')?></th>
                        <th><?=Yii::t('fe', 'Aksi')?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel panel-default <?=$classTrxReseptur?>">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe', 'Tambah resep')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-body">

            <?= $this->render('_form_tambah_resep', [
                'modelReseptur' => $modelReseptur,
                'list_data_apotek' => $list_data_apotek,
                'data_template' => $data_template,
                'pendaftaran_id' => $pendaftaran_id,
                'penjamin_id' => isset($data_pasien['penjamin_id']) ? $data_pasien['penjamin_id'] : 1,
                'idDiagnosa' => $idDiagnosa,
            ]); ?>

            <?= $this->render('_form_non_racikan', [
                'modelResepturDetailNonRacikan' => $modelResepturDetailNonRacikan,
                'list_data_signa' => $list_data_signa,
                'pendaftaran_id' => $pendaftaran_id,
                'kelaspelayanan_id' => $kelaspelayanan_id,
            ]); ?>

            <?= $this->render('_form_racikan', [
                'modelResepturDetailRacikan' => $modelResepturDetailRacikan,
                'list_data_signa' => $list_data_signa,
                'pendaftaran_id' => $id,
                'kelaspelayanan_id' => $kelaspelayanan_id,
            ]); ?>

            <?= $this->render('_table_reseptur', [
                'pendaftaran_id' => $pendaftaran_id,
            ]); ?>

        </div>
    </div>
</div>

<?php
$this->registerJs("
var default_depo = ". $default_depo .";
".$this->render('js/__reseptur.js'), View::POS_END);
?>
