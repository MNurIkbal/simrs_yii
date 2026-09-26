<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi loket
 * @copyright 12 Desember 2018 aweutist
 */

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Informasi Loket Pendaftaran');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/pendaftaran']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white" style="min-height: 600px;">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b>
                        <?= Yii::t('fe', 'Informasi Loket Pendaftaran'); ?>
                       </b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= 
                    DocoHelpers::generateToolbar([
                        'unset' => [
                            'title' => \Yii::t('fe', 'Keluar Loket'),
                            'icon' => 'fa fa-power-off',
                            'attributes' => [
                                'data-options' => 'click',
                                'data-target' => '/pendaftaran/informasi-loket/unset-loket?id=',
                                'id' => 'btn-unset'
                            ]
                        ],
                    ], '#table-data-loket');
                ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>

                <div class="row">
                    <div class="col-sm-12"><br><br>
                        <table id="table-data-loket" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="5%"></th>
                                    <th width="5%">No</th>
                                    <th><?=\Yii::t("fe", "Nama Loket");?></th>
                                    <th><?=\Yii::t("fe", "Nama Pegawai");?></th>
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
$this->registerJs("
var table;
// Event Ready
$(document).ready(function() {
    // Generate Table
    table = $('#table-data-loket').DataTable({
        bInfo : false,
        displayLength: 10,
        filter: true,
        processing: true,
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets:   0
        }],
        select: {
            style: 'os',
            selector: 'tr'
        },
        sorting: [[2, 'asc']],
        serverSide: true,
        ajax: baseUrl+'pendaftaran/informasi-loket/get-data-loket',
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
            {
                title: '".(\Yii::t('fe', 'Nama Loket'))."',
                data: 'loket_nama',
                searchable:false
            },
            {title: '".(\Yii::t('fe', 'Nama Pegawai'))."', data: 'nama_pegawai',searchable:false},
        ]
    });

    $('.dataTables_filter').hide();

    $(document).on('click', '#btn-unset', function(e) {
        e.preventDefault();
        let tableLoket = $('#table-data-loket').DataTable();
        let tableData = tableLoket.row('.selected').data();
        if (typeof tableData !== 'undefined') {
            const primaryId = tableData.primary;
            const link = '/pendaftaran/informasi-loket/unset-loket?id=' + primaryId;
            console.log(link)
            $(this).docoForm('click', {
                url: link,
                confirmMessage: i18next.t('Apakah anda yakin akan keluar loket ini ?'),
                success: function (data) {
                    table.draw();
                }
            });
        } else {
            docoNotification('warning', i18next.t('Terjadi Kesalahan'), i18next.t('Belum ada data yang dipilih!'));
            return false;
        }
    })


});", View::POS_END, 'js-kuning');
