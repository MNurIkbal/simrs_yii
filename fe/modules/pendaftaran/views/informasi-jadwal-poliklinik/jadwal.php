<?php
// Author : Naufal Ziyad L

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

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
                <!-- <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div> -->
            </div>

            <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'search',
                'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                // 'print',
            ]);?>
            <button type="button" class="btn btn-info btn-labeled btn-xs btn-pdf" data-target="/pendaftaran/informasi-jadwal-poliklinik/export-pdf" data-options="pdf" data-table="#table-informasi-jadwal-poliklinik"><b><i class="fa fa-file-pdf-o"></i></b>Cetak PDF</button>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-body">
                            <?php
                            $form = ActiveForm::begin([
                                'id' => 'filter-form-jadwal',
                                'options' => [
                                    // 'class' => 'form-inline',
                                    'role' => 'form',
                                ],
                            ]);
                            ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label"><?=Yii::t('fe', 'Ruangan')?></label>
                                    <?= Html::activeDropDownList($model, 'ruangan_id',
                                        ArrayHelper::map($ruangan, 'ruangan_id', 'ruangan_nama'), [
                                            'class' => 'select2 b',
                                            'prompt' => Yii::t('fe', '-- Pilih Ruangan --')
                                        ])
                                    ?>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label"><?=Yii::t('fe', 'Jam Mulai')?></label>
                                    <p><input type="text" class="form-control" name="JadwalPoliklinikForm[jam_mulai]" id="jamMulai" placeholder="Jam Mulai"></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label"><?=Yii::t('fe', 'Jam Selesai')?></label>
                                    <p><input type="text" class="form-control " name="JadwalPoliklinikForm[jam_tutup]" id="jamTutup" placeholder="Jam Tutup"></p>
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
                        <div class="">
                            <?php
                                $phrase = Yii::t('fe', 'Menampilkan _jmljadwal_ data jadwal dari _jmlpoli_ Poliklinik');
                            ?>
                            <p><?=str_replace(['_jmljadwal_','_jmlpoli_'],['<span id="jmlJadwal"></span>','<span id="jmlPoli"></span>'],$phrase);?><p>
                        </div>
                <table id="table-informasi-jadwal-poliklinik" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
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
                    <tbody class="content_jadwal" id="list_jadwal">
                        <!-- <tr>
                            <td colspan="8">Data tidak ditemukan</td>
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
        min: [0,0],
        max: [23,30],
    });

    $("#jamTutup").pickatime({
        format: "HH:i",
        min: [0,0],
        max: [23,30],
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
    });
', View::POS_END, 'b-index');


$this->registerJs($this->render('jadwalpoli.js'));
$this->registerJs($this->render('jadwal.js'));

?>
