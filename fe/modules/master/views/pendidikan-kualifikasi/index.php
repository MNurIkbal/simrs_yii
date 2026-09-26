<?php
// Author : Ramdhan Nurrachman
// Modify : Naufal Ziyad L - 11/01/2018 ::: 14:53

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Pendidikankualifikasi');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
 <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/#/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/#/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-pendidikan-kualifikasi');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-pendidikan-kualifikasi"></div>
                </div>
                <div class="form-group">
                </div>
                <table id="table-pendidikan-kualifikasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th >No</th>
                            <th><?=Yii::t('fe', 'Pendidikan')?></th>
                            <th><?=Yii::t('fe', 'Kelompok Pegawai')?></th>
                            <th><?=Yii::t('fe', 'Pendidikankualifikasi')?></th>
                            <th><?=Yii::t('fe', 'Nama Lainnya')?></th>
                            <th>Kebutuhan Pria</th>
                            <th>Kebutuhan Wanita</th>
                            <th>Status</th>
                            <th ></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
    </div>
</div>
<div id="modal_pend_kualifikasi" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<script>
    var table2 = $('#table-pendidikan-kualifikasi').docoTabel({
        filter: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            // stateSave: true,
            //scrollX: true,
            ajax: baseUrl+"master/pendidikan-kualifikasi/get-data-pend-kualifikasi",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=Yii::t('fe', 'Pendidikan')?>",  data: "pendidikan_m.pendidikan_nama"},
                {title: "<?=Yii::t('fe', 'Kelompok Pegawai')?>", data: "kelompokpegawai_m.kelompokpegawai_nama"},
                {title: "<?=Yii::t('fe', 'Pendidikankualifikasi')?>", data: "pendkualifikasi_nama"},
                {title: "<?=Yii::t('fe', 'Nama Lainnya')?>", data: "pendkualifikasi_namalainnya"},
                {title: "<?=Yii::t('fe', 'Kebutuhan Pria')?>", data: "jmlkeblaki",  searchable: false},
                {title: "<?=Yii::t('fe', 'Kebutuhan Wanita')?>", data: "jmlkebperempuan", searchable: false},
                {title: "Status",  data: "is_active"},
                {
                    title: "<?=Yii::t('fe', 'Aksi')?>",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ]
        });
     $(".dataTables_filter").hide();
     $(".filter-form-pendidikan-kualifikasi").datatableBootstrapFilter(table2,
            [
                [7, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>']
            ]
        );


    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table2.draw();
    });
</script>
