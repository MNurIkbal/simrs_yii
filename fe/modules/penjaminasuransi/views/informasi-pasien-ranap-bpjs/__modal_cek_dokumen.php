<?php
    use yii\web\View;
    use app\components\DocoHelpers;
    use kartik\select2\Select2;
    use kartik\widgets\ActiveForm;
    use yii\widgets\Breadcrumbs;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\JsExpression;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" id="keluar" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Cek Dokumen Eklaim</h5>
</div>
<div class="modal-body">
    <?=DocoHelpers::generateToolbar([
        'search',
        'reset'=>['attributes'=>['data-parent'=>'.filter-cek-dokumen']],
    ], '#cek-dokumen');?>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12 filter-cek-dokumen"></div>
            </div>
            <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="cek-dokumen" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th><?= Yii::t('fe', 'No') ?></th>
                        <th><?= Yii::t('fe', 'Nama Dokumen') ?></th>
                        <th><?= Yii::t('fe', 'Status') ?></th>
                        <th><?= Yii::t('fe', 'Tanggal Terakhir Unduh') ?></th>
                    </tr>
                </thead>
                <tbody id="data">
                    <tr>
                        <td colspan="8" class="text-center"><?= Yii::t('fe', 'Data tidak ditemukan') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
</div>
<?php
$this->registerJs("
    $(document).ready(function(){
        data = $('#cek-dokumen').docoTabel({
            filter: true,
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            scrollY: true,
            ajax: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-cek-unduh-dokumen?id='+pendaftaran,
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '".(\Yii::t('fe', 'Nama Dokumen'))."',
                    data: 'nama_dokumen',
                },
                {
                    title: '".(\Yii::t('fe', 'Status'))."',
                    data: 'status',
                    searchable: false,
                    orderable: true
                },
                {
                    title: '".(\Yii::t('fe', 'Tanggal Terakhir Unduh'))."',
                    data: 'last_unduh',
                    searchable: false,
                    orderable: true
                },
            ],

        });

        $('.dataTables_filter').hide();
        $('.filter-cek-dokumen').datatableBootstrapFilter(data, [
            [
                1,
                '<div class=\"form-group\">".(preg_replace("/[\n\t\r]/i", '',
                        Html::dropDownList('nama_dokumen', '',
                            $listDokumen,
                            [
                                'class' => 'form-control select2 nama_dokumen',
                                'prompt' => \Yii::t('fe', 'All'),
                                'id' => 'nama_dokumen'
                            ]
                        )
                    ))."</div>'

            ],
        ], {0:1}, true);

        $('#keluar').click(function(){
            $('.data-reset').trigger('click');
            console.log('3')
        });
    });
",VIEW::POS_END, 'js-kuning');
?>
