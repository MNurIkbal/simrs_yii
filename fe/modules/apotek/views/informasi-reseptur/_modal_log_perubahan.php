<?php


use yii\web\View;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->params['breadcrumbs'][] = $title;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table
                class="table datatable-basic table-striped table-hover dataTable no-footer"
                id="log"
                style="width: 100%"
            >
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?= Yii::t('fe', 'Tanggal Perubahan') ?></th>
                        <th><?= Yii::t('fe', 'Jenis Perubahan') ?></th>
                        <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                        <th><?= Yii::t('fe', 'Data Sebelumnya') ?></th>
                        <th><?= Yii::t('fe', 'Data Setelah Perubahan') ?></th>
                        <th><?= Yii::t('fe', 'User') ?></th>
                    </tr>
                </thead>
                <tbody id="list-obat">
                    <tr>
                        <td colspan="8" class="text-center"><?= Yii::t('fe', 'Data tidak ditemukan') ?></td>
                    </tr>
                </tbody>
                <tfoot></tfoot>
            </table>
        </div>
    </div>
</div>
<?php
    $this->registerJs("
    var table;

    $(document).ready(function(){
        table = $('#log').docoTabel({
            filter: true,
            sorting: [],
            paging: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+'apotek/informasi-reseptur/get-data-log?id=" . $id . "&type=" . $type . "',
            columns: [
                {
                    width: '50px',
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Tanggal Perubahan'))."', data: 'tanggal_perubahan'},
                {title: '".(\Yii::t('fe', 'Jenis Perubahan'))."', data: 'jenis_perubahan'},
                {title: '".(\Yii::t('fe', 'Nama Obat Alkes'))."', data: 'obatalkes_nama'},
                {title: '".(\Yii::t('fe', 'Data Sebelumnya'))."', data: 'perubahan_sebelum'},
                {title: '".(\Yii::t('fe', 'Data Setelah Perubahan'))."', data: 'perubahan_setelah'},
                {title: '".(\Yii::t('fe', 'User'))."', data: 'pegawai_nama'}
            ],
        });
        $('.dataTables_filter').hide();
    });

    ", VIEW::POS_END, 'js-kunings');
?>
