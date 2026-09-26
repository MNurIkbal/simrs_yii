<?php
use app\components\DocoConstants;
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

$this->title = Yii::t('fe', $title);
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
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'back',
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']]
                ], '#tb-terra');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-forms"></div>
                </div>
                <div class="advanced-filter">
                </div>
                <div class="row">
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-body">
                        <table class="table table-bordered datatable-basic dataTable" id="tb-terra" style="width:100%;">
                            <thead>
                                <tr class="bg-inverse">
                                    <th colspan="3" class="text-center"><?= Yii::t('fe', 'SOAP / Verbal Order') ?></th>
                                    <th colspan="2" class="text-center"><?= Yii::t('fe', 'Terapi') ?></th>
                                </tr>
                                <tr class="bg-inverse">
                                    <th width="8px">No</th>
                                    <th><?= Yii::t('fe', 'Ruang / Tanggal dan Jam / Profesi') ?></th>
                                    <th><?= Yii::t('fe', 'Hasil Asesmen Penatalaksanaan Pasien') ?></th>
                                    <th><?= Yii::t('fe', 'Instruksi DPJP Termasuk Pasca Bedah') ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs("
    var pasien_terra = '.$pasien_terra.';

    // Tabel
    var tabel;

    // Initiate page
    $(document).ready(function () {
        $('.flex-1').addClass('hidden')
        $('#filter_dokter').select2('val', '');

        // Generate Table
        tabel = $('#tb-terra').docoTabel({
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            paging: true,
            ajax: baseUrl + 'rajal/pemeriksaan/get-data-history-cppt-terra-medik?pasien_terra=' + pasien_terra,
            columns: [
                {
                    title: 'No',
                    data: 'no',
                    searchable: false,
                    orderable: false
                },
                { 
                    title: 'Ruang / Tanggal dan Jam / Profesi',
                    data: 'ruang', 
                    searchable: false, 
                    orderable: false 
                },
                { 
                    title: 'Hasil Asesmen Penatalaksanaan Pasien',
                    data: 'soap', 
                    searchable: false, 
                    orderable: false 
                },
                {
                    title: 'Instruksi DPJP Termasuk Pasca Bedah',
                    data: 'resep',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Dokter',
                    data: 'dokter_id',
                    searchable: true,
                    orderable: false,
                },
            ],
        });

        // Hide filter
        $('.dataTables_filter').hide();

        $('.filter-forms').datatableBootstrapFilter(tabel, [
            [
                4,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('dokter', '',[],
                        [
                            'id' => 'filter_dokter',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih Dokter--'),
                        ]
                    )
                ))."<div>\"
            ],
        ],{
            4:0,
        });

        $('#filter_dokter').select2InfinityScroll({
            url: '/rajal/pemeriksaan/all-dokter-list',
        })

        $('.data-reset').on('click', function(){
            setTimeout(function() {
                $('.data-filter').trigger('click');
            }, 500);
        })
        
    });

", View::POS_END, 'index');


?>