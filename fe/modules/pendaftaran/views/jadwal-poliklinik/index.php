<?php
// Author : Naufal Ziyad L

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('fe', 'Informasi Jadwal Poliklinik');
$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
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
                <!-- Filter Form Akan digunakan ketika Backend deploy
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div> -->
                <div class="row">
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?= Yii::t('fe', 'Pencarian') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="panel-body">
                            <?php
                            $form = ActiveForm::begin([
                                'id' => 'informasi-form',
                                'options' => [
                                    'class' => 'form-horizontal',
                                    'role' => 'form',
                                ],
                            ]);
                            ?>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <div class="col-md-4">
                                        <?= Html::activeDropDownList($model, 'ruangan_id',
                                            ArrayHelper::map([], 'ruangan_id', 'name'), [
                                                'class' => 'select2 b',
                                                'prompt' => Yii::t('fe', '-- Pilih Ruangan --')
                                            ])
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <div class="col-md-4">
                                    <p><input type="text" class="form-control " id="jamMulai" placeholder="Jam Mulai"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4">
                                    <div class="col-md-4">
                                    <p><input type="text" class="form-control " id="jamTutup" placeholder="Jam Tutup"></p>
                                    </div>
                                </div>
                            </div>


                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?= Yii::t('fe', 'Tabel Jadwal Poliklinik') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>

                <table id="table-informasi-jadwal-poliklinik" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=Yii::t('fe', 'Poliklinik')?></th>
                            <th><?=Yii::t('fe', 'Senin')?></th>
                            <th><?=Yii::t('fe', 'Selasa')?></th>
                            <th><?=Yii::t('fe', 'Rabu')?></th>
                            <th><?=Yii::t('fe', 'Kamis')?></th>
                            <th><?=Yii::t('fe', 'Jumat')?></th>
                            <th><?=Yii::t('fe', 'Sabtu')?></th>
                            <th><?=Yii::t('fe', 'Minggu')?></th>
                        </tr>
                    </thead>
                    <!-- /*sementara data statis */ -->
                    <tbody>
                            <tr>
                               <td>1</td>
                                <td>Polikinik Anak</td>
                                <td> Jam Buka: 07.00 s/d 10/30  | Max Antrian : 50
                                <?php
                                        echo Html::a('<i class="fa fa-pencil"></i>',
                                            '/pendaftaran/jadwal-poliklinik/update',
                                            [
                                                'class' => 'btn btn-primary btn-xs data-update-asalrujukan',
                                                'title' => Yii::t('fe', 'Ubah'),
                                                'data-tooltip' => 'tooltip',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_jadwalpoliklinik'
                                            ]
                                        );
                                    ?></td>
                                <td> Jam Buka: 07.00 s/d 10/30  | Max Antrian : 50
                                <?php
                                        echo Html::a('<i class="fa fa-pencil"></i>',
                                            '/pendaftaran/jadwal-poliklinik/update',
                                            [
                                                'class' => 'btn btn-primary btn-xs data-update-asalrujukan',
                                                'title' => Yii::t('fe', 'Ubah'),
                                                'data-tooltip' => 'tooltip',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_jadwalpoliklinik'
                                            ]
                                        );
                                    ?></td>
                                <td> Jam Buka: 07.00 s/d 10/30  | Max Antrian : 50
                                <?php
                                        echo Html::a('<i class="fa fa-pencil"></i>',
                                            '/pendaftaran/jadwal-poliklinik/update',
                                            [
                                                'class' => 'btn btn-primary btn-xs data-update-asalrujukan',
                                                'title' => Yii::t('fe', 'Ubah'),
                                                'data-tooltip' => 'tooltip',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_jadwalpoliklinik'
                                            ]
                                        );
                                    ?></td>
                                <td> Jam Buka: 07.00 s/d 10/30  | Max Antrian : 50
                                <?php
                                        echo Html::a('<i class="fa fa-pencil"></i>',
                                            '/pendaftaran/jadwal-poliklinik/update',
                                            [
                                                'class' => 'btn btn-primary btn-xs data-update-asalrujukan',
                                                'title' => Yii::t('fe', 'Ubah'),
                                                'data-tooltip' => 'tooltip',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_jadwalpoliklinik'
                                            ]
                                        );
                                    ?></td>
                                <td> Jam Buka: 07.00 s/d 10/30  | Max Antrian : 50
                                <?php
                                        echo Html::a('<i class="fa fa-pencil"></i>',
                                            '/pendaftaran/jadwal-poliklinik/update',
                                            [
                                                'class' => 'btn btn-primary btn-xs data-update-asalrujukan',
                                                'title' => Yii::t('fe', 'Ubah'),
                                                'data-tooltip' => 'tooltip',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_jadwalpoliklinik'
                                            ]
                                        );
                                    ?></td>
                                <td> Jam Buka: 07.00 s/d 10/30  | Max Antrian : 50
                                <?php
                                        echo Html::a('<i class="fa fa-pencil"></i>',
                                            '/pendaftaran/jadwal-poliklinik/update',
                                            [
                                                'class' => 'btn btn-primary btn-xs data-update-asalrujukan',
                                                'title' => Yii::t('fe', 'Ubah'),
                                                'data-tooltip' => 'tooltip',
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_jadwalpoliklinik'
                                            ]
                                        );
                                    ?></td>
                                <td> </td>
                                </tr>
                            <!-- <tr>
                                <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr> -->
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal_jadwalpoliklinik" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<!-- <script type="text/javascript">

</script> -->

<?php
$this->registerJs('
     $("#jamMulai").pickatime({
        format: "HH:i",
        min: [7,0],
        max: [21,00],
    });

    $("#jamTutup").pickatime({
        format: "HH:i",
        min: [7,0],
        max: [21,00],
    });


    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Delete
    $(document).on("click", ".data-delete-jadwalpoliklinik", function(e) {
        e.preventDefault();
        $(this).docoForm("delete",{
            success : function (data) {
                table.draw()
            }
        });
        return false;
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#table-informasi-jadwal-poliklinik").docoTabel({
            filter: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            ajax: baseUrl+"pendaftaran/jadwal-poliklinik/get-data-jadwal-poliklinik",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.Yii::t('fe', 'Nama Ruangan').'",  data: "ruangan_m.ruangan_nama"},
                {title: "'.Yii::t('fe', 'hari').'", data: "hari"},
                {title: "'.Yii::t('fe', 'waktu_pelayanan').'", data: "waktu_pelayanan"},
                {title: "'.Yii::t('fe', 'jam_mulai').'", data: "jam_mulai"},
                {title: "'.Yii::t('fe', 'jam_tutup').'", data: "jam_tutup"},
                {
                    title: "'.Yii::t('fe', 'Aksi').'",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table,
            [
                [6, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\']
            ]
        );
    });
', View::POS_END, 'b-index');
?>
