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
                        <th><?= Yii::t('fe', 'Tanggal') ?></th>
                        <th><?= Yii::t('fe', 'Jenis') ?></th>
                        <th><?= Yii::t('fe', 'Catatan') ?></th>
                        <th><?= Yii::t('fe', 'Nama Barang') ?></th>
                        <th><?= Yii::t('fe', 'Perubahan Harga') ?></th>
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
            sorting: [[1, 'desc']],
            paging: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            cacheFilter: false,
            ajax: baseUrl+'master/barang/get-data-log?id=" . $id . "',
            columns: [
                {
                    width: '50px',
                    title: 'No',
                    // data: 'rowNum',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'tgl_baranghistory',
                    render: function (data, type, row, meta) {
                        return moment(data).format('DD/MM/YYYY HH:mm:ss')
                    }
                },
                {data: 'keterangan', searchable: false, orderable: false},
                {data: 'catatan', searchable: false, orderable: false},
                {data: 'barang_nama', searchable: false, orderable: false},
                {
                    data: 'harga_dasar',
                    searchable: false,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return 'Rp.' + docoHelper.convertToRupiah(data)
                    }
                },
                {title: '".(\Yii::t('fe', 'User'))."', data: 'nama_pegawai', searchable: false, orderable: false}
            ],
        });
        $('.dataTables_filter').hide();
    });

    ", VIEW::POS_END, 'js-kunings');
?>
