<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rm'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-search"></i></b>'.Yii::t('fe', 'Cari'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa-refresh"></i></b>'.Yii::t('fe', ' Muat Ulang'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa-print"></i></b>'.Yii::t('fe', ' Print'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::a('<b><i class="fa fa-file-pdf-o"></i></b>'.Yii::t('fe', ' Cetak PDF'), 'javascript:void(0);',
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'onclick' => "_export_pdf(this.id,'.filter-form')",
                        'id' => 'pdf',
                        'data-sources' => "/rm/lap-kunjungan/export-pdf"
                    ]);
                ?>
                <?= Html::a('<b><i class="fa fa-file-excel-o"></i></b>'.Yii::t('fe', ' Unduh Excel'), 'javascript:void(0);',
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'onclick' => "_export_excel(this.id,'.filter-form')",
                        'id' => 'excel',
                        'data-sources' => "/rm/lap-kunjungan/export-excel"
                    ]);
                ?>
            
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                        <?= Yii::$app->controller->renderPartial('_search', array(
                            'kelompok_pegawai' => $kelompok_pegawai,
                        )) ?>
                    </div>
                </div>
                <table id="example" class="table datatable-basic table-striped table-hover dataTable no-footer" 
                data-source="<?=Url::home();?>rm/pegawai-ruangan/get-data?ruangan_id=<?= $ruangan_id ?>"
                data-filter=".form-filter"
                data-test="true">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?= Yii::t('fe', 'Rownum') ?></th>
                            <th><?= Yii::t('fe', 'Ruangan') ?></th>
                            <th><?= Yii::t('fe', 'Nama Pegawai') ?></th>
                            <th><?= Yii::t('fe', 'Kelompok Pegawai') ?></th>
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modal_backdrop" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<?php 
$this->registerJs("
    var tabel = $('#example').docoTabel({
        columns : [
            {data: 'rowNum', name : 'rowNum'},
            {data: 'ruangan_nama', name: 'ruangan_nama'},
            {data: 'nama_pegawai', name: 'nama_pegawai'},
            {data: 'kelompok_pegawai', name: 'kelompok_pegawai'},
            {
                title: 'Aksi',
                data: 'aksi',
                searchable: false,
                orderable: false,
                class: 'text-center'
            }
        ],
    });
    
    $(document).on('click','.data-delete', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            additional: 'data-rm',
            success : function (data) {
                tabel.reload();
            }
        });
    });

    $(document).on('click','.data-aktifasi', function(event) {
        $(this).docoForm('delete',{
            success : function (data) {
                tabel.reload();
            }
        });
    });

    $(document).on('click', '.data-reload', function (e) {
        e.preventDefault();
        tabel.reload();
    });

    $('form.form-filter').on('submit', function (e) {
        e.preventDefault();
        tabel.reload();
    });
",View::POS_END, 'Kunjungan');

?>