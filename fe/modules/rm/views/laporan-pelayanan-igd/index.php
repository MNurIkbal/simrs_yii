<?php

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

$this->title = isset($title) ? $title : Yii::t('fe', 'Rekam Medis');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
.table-border_left {
    border-left: 2px solid white;
}
.dataTables_scroll {
    max-height: 99999em !important
}

</style>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                <div class="column-2">
                    <h3 class="panel-title">
                        <b>
                            <?php
                            // echo Yii::$app->docoVars->workspace("modul_alias");
                            echo $this->title;
                            ?>
                        </b>
                    </h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                </div>
            </div>
            <!-- end -->
        </div>

        <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'search',
                'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                'excel' => [
                    'title' => Yii::t('fe', 'Excel'),
                    'attributes' => [
                        'data-target'=>Url::home().'rm/laporan-pelayanan-igd/export-excel?'
                    ]
                ],
                'pdf',
        ], '#laporan-pelayanan-igd');?>
        </div>

        <div class="panel-body">
            <div class="row">
                <!--<div class="col-md-12 filter-form"></div>-->
            </div>
            <div class="advanced-filter">
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table id="laporan-pelayanan-igd" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th rowspan="2">No</th>
                                <th rowspan="2" class="text-center"><?=\Yii::t("fe", "Jenis Pelayanan");?></th>
                                <th colspan="2" class="text-center table-border_left"><?=\Yii::t("fe", "Pasien Masuk");?></th>
                                <th colspan="2" class="text-center table-border_left"><?=\Yii::t("fe", "Pasien Baru/Lama");?></th>
                                <th colspan="2" class="text-center table-border_left"><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                                <th colspan="5" class="text-center table-border_left"><?=\Yii::t("fe", "Triase");?></th>
                                <th colspan="7" class="text-center table-border_left"><?=\Yii::t("fe", "Tindakan Lanjut");?></th>
                                <th rowspan="2" class="text-center"><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            </tr>
                            <tr class="bg-inverse">
                                <th class="table-border_left"><?=\Yii::t("fe", "Rujukan");?></th>
                                <th><?=\Yii::t("fe", "Non Rujukan");?></th>
                                <th class="table-border_left"><?=\Yii::t("fe", "Pasien Baru");?></th>
                                <th><?=\Yii::t("fe", "Pasien Lama");?></th>
                                <th class="table-border_left"><?=\Yii::t("fe", "Laki-Laki");?></th>
                                <th><?=\Yii::t("fe", "Perempuan");?></th>
                                <th class="table-border_left"><?=\Yii::t("fe", "Resusitasi");?></th>
                                <th><?=\Yii::t("fe", "Emergent");?></th>
                                <th><?=\Yii::t("fe", "Urgent");?></th>
                                <th><?=\Yii::t("fe", "Non Urgent");?></th>
                                <th><?=\Yii::t("fe", "False Emergency");?></th>
                                <th class="table-border_left"><?=\Yii::t("fe", "Dipulangkan");?></th>
                                <th><?=\Yii::t("fe", "Dirujuk Ke RS Lain");?></th>
                                <th><?=\Yii::t("fe", "Pulang Paksa");?></th>
                                <th><?=\Yii::t("fe", "Meninggal");?></th>
                                <th><?=\Yii::t("fe", "Dirujuk Rawat Inap");?></th>
                                <th><?=\Yii::t("fe", "Lain-lain");?></th>
                                <th><?=\Yii::t("fe", "Melarikan Diri");?></th>
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


<?php

$this->registerJs("
    var table;


    $(document).ready(function(){
        $('.flex-1').addClass('hidden')
        // Generate Table
        table = $('#laporan-pelayanan-igd').docoTabel({
            filter: true,
            columnDefs: [
                { targets: [0,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19], className: 'text-center'}
            ],
            displayLength: 10,
            lengthChange: false,
            paging: false,
            info: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'rm/laporan-pelayanan-igd/get-data',
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                }, //0
                {title: '".(\Yii::t('fe', "Jenis Pelayanan"))."', data: 'jenis_pelayanan', searchable: false, orderable: false}, //1
                {title: '".(\Yii::t('fe', "Rujukan"))."', data: 'pasienmasukrujukan', searchable: false, orderable: false}, //2
                {title: '".(\Yii::t('fe', "Non Rujukan"))."', data: 'pasienmasuknonrujukan', searchable: false, orderable: false}, //3
                {title: '".(\Yii::t('fe', "Pasien Baru"))."', data: 'pasien_baru', searchable: false, orderable: false}, //4
                {title: '".(\Yii::t('fe', "Pasien Lama"))."', data: 'pasien_lama', searchable: false, orderable: false}, //5
                {title: '".(\Yii::t('fe', "Laki-Laki"))."', data: 'pasien_laki', searchable: false, orderable: false}, //6
                {title: '".(\Yii::t('fe', "Perempuan"))."', data: 'pasien_perempuan', searchable: false, orderable: false}, //7
                {title: '".(\Yii::t('fe', "Resusitasi"))."', data: 'triase_resusitasi', searchable: false, orderable: false}, //8
                {title: '".(\Yii::t('fe', "Emergent"))."', data: 'triase_emergent', searchable: false, orderable: false}, //9
                {title: '".(\Yii::t('fe', "Urgent"))."', data: 'triase_urgent', searchable: false, orderable: false}, //10
                {title: '".(\Yii::t('fe', "Non Urgent"))."', data: 'triase_nonurgent', searchable: false, orderable: false}, //11
                {title: '".(\Yii::t('fe', "False Emergency"))."', data: 'triase_falseemergency', searchable: false, orderable: false}, //12
                {title: '".(\Yii::t('fe', "Dipulangkan"))."', data: 'pasientindaklanjut_dipulangkan', searchable: false, orderable: false}, //13
                {title: '".(\Yii::t('fe', "Dirujuk Ke RS Lain"))."', data: 'pasientindaklanjut_dirujukrslain', searchable: false, orderable: false}, //14
                {title: '".(\Yii::t('fe', "Pulang Paksa"))."', data: 'pasientindaklanjut_pulangpaksa', searchable: false, orderable: false}, //15
                {title: '".(\Yii::t('fe', "Meninggal"))."', data: 'pasientindaklanjut_meninggal', searchable: false, orderable: false}, //16
                {title: '".(\Yii::t('fe', "Dirujuk Rawat Inap"))."', data: 'pasientindaklanjut_dirujukri', searchable: false, orderable: false}, //17
                {title: '".(\Yii::t('fe', "Lain-lain"))."', data: 'pasientindaklanjut_lainlain', searchable: false, orderable: false}, //18
                {title: '".(\Yii::t('fe', "Melarikan Diri"))."', data: 'pasientindaklanjut_melarikandiri', searchable: false, orderable: false}, //19
                {title: '".(\Yii::t('fe', "Tanggal Pendaftaran"))."', data: 'tgl_pendaftaran', visible: false, searchable: true}, //20
            ],
        });
        
        $('.dataTables_filter').hide();

        //Filter berdasarkan Tanggal Pendaftaran
        $('.filter-form').datatableBootstrapFilter(table, 
        [
            [
                20,
                \"<div class='input-group'><input value=".date('d-M-Y')." type='text' id='rangeDemoStart' class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input value=".date('d-M-Y')." type='text' id='rangeDemoFinish' class='form-control endDate' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
            ],
        ],
        {
            //posisi kolom dan grid
            20:0,
        });
        
        dateRangeHelper('.startDate','.endDate','.targetDate');

    });
",View::POS_END)
?>
