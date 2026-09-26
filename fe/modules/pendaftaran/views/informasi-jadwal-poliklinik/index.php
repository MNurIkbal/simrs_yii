<?php
// Author : Naufal Ziyad L

use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'Informasi Jadwal Poliklinik');
$this->params['breadcrumbs'][] = ['label' => 'Pendaftaran', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
              <div class="row">
                  <div class="column-1">
                      <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset' => [
                            'attributes' => [
                                'data-parent' => '.filter-form'
                            ]
                        ],
                        'pdf',
                        'excel',
                    ], "#table-jadwalpoliklinik");?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="table-jadwalpoliklinik" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?=Yii::t('fe', 'No.')?></th>
                            <th><?=Yii::t('fe', 'Ruangan')?></th>
                            <th><?=Yii::t('fe', 'Nama Ruangan')?></th>
                            <th><?=Yii::t('fe', 'Hari')?></th>
                            <th><?=Yii::t('fe', 'Nama Hari')?></th>
                            <th><?=Yii::t('fe', 'Shift')?></th>
                            <th><?=Yii::t('fe', 'Nama Shift')?></th>
                            <th><?=Yii::t('fe', 'Waktu Pelayanan')?></th>
                            <th><?=Yii::t('fe', 'Status')?></th>
                            <?php if ($konfigKuota == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK): ?>
                            <th><?= Yii::t('fe', 'Kuota Online') ?></th>
                            <th><?= Yii::t('fe', 'Kuota Offline') ?></th>
                            <?php else: ?>
                            <th class="hidden"><?= Yii::t('fe', 'Kuota Online') ?></th>
                            <th class="hidden"><?= Yii::t('fe', 'Kuota Offline') ?></th>
                            <?php endif ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8"><?=Yii::t('fe', 'Data tidak ditemukan.')?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var tableJadwalPoliklinik;
    var konfigKuota = '.$konfigKuota.';
    var konfigKuotaPoli = '.DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK.';

    $(document).on("click", ".data-reload", function() {
        tableJadwalPoliklinik.draw();
    });

    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {
        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/jadwal-poliklinik/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.Yii::t('fe', 'Konfirmasi').'",
            confirmMessage : "'.Yii::t('fe', 'Apa anda yakin ingin mengubah status data ini ?').'",
            success : function (data) {
                tableJadwalPoliklinik.draw();
            }
        });
        tableJadwalPoliklinik.draw();
    });

    $(document).ready(function() {
        tableJadwalPoliklinik = $("#table-jadwalpoliklinik").docoTabel({
            processing: true,
            serverSide: true,
            scrollX: true,
            filter: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            ajax: baseUrl+"pendaftaran/informasi-jadwal-poliklinik/get-data-jadwal-poliklinik",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.Yii::t('fe', 'Ruangan').'",
                    data: "ruangan_id",
                    visible:false
                },
                {
                    title: "'.Yii::t('fe', 'Nama Ruangan').'",
                    data: "ruangan_nama",
                    searchable:false
                },
                {
                    title: "'.Yii::t('fe', 'Hari').'",
                    data: "hari",
                    visible:false
                },
                {
                    title: "'.Yii::t('fe', 'hari'). '",
                    data: "hari_nama",
                    searchable:false
                },
                {
                    title: "'.Yii::t('fe', 'Shift').'",
                    data: "shift_id",
                    visible:false
                },
                {
                    title: "' . Yii::t('fe', 'Shift') . '",
                    data: "shift_nama",
                    searchable:false
                },
                {
                    title: "'.Yii::t('fe', 'waktu_pelayanan'). '",
                    data: "waktu_pelayanan",
                    name: "waktu_pelayanan",
                },
                {
                    title: "Status",
                    data: "is_active",
                },
                {
                    title: "'.Yii::t('fe', 'Kuota Offline').'",
                    data: "maxantrian_poli",
                    visible: konfigKuota == konfigKuotaPoli ? true : false,
                    searchable: konfigKuota == konfigKuotaPoli ? true : false,
                    orderable: konfigKuota == konfigKuotaPoli ? true : false,
                },
                {
                    title: "'.Yii::t('fe', 'Kuota Online').'",
                    data: "kuota_online",
                    visible: konfigKuota == konfigKuotaPoli ? true : false,
                    searchable: konfigKuota == konfigKuotaPoli ? true : false,
                    orderable: konfigKuota == konfigKuotaPoli ? true : false,
                },
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableJadwalPoliklinik,
            [
                [8, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih Status--')]))). '\'],

                [5, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('shift_id', '', $shift, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih Shift--')]))) . '\'],

                [3, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('hari', '', $hari, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih Hari--')]))).'\'],
                [1, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_id', '', $ruangan, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih Ruangan--')]))).'\']
            ], {
                1:0,
                3:1,
                5:2,
                7:3,
                8:4,
            },
        true);
    });
', View::POS_END, 'b-index');
?>
