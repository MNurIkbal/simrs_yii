<?php
/**
 * @author Ardi Pratama
 */

use yii\web\View;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\widgets\SirsTableWidget;

$this->title = isset($title) ? $title : "Pasien Fisioterapi";

$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("", "Fisioterapi")), 'url' => ['/fisioterapi']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("", "Informasi")), 'url' => ['/fisioterapi']];
$this->params['breadcrumbs'][] = $title;
?>

<style type="text/css">
    .p-datatable .p-datatable-header {
        background-color: #37474f;
        border-color: #37474f;
        color: #ffffff;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon") ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b><?= $title ?></b></h3>
                    <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                  </div>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    /*
                    'search',
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    */
                    'periksa' => [
                        'title' => \Yii::t('fe', 'Periksa'),
                        'icon'  => 'fa fa-stethoscope',

                        'attributes' => [
                            'data-target' => '/fisioterapi/pemeriksaan?id=',
                            'id'          => 'periksa' ,
                            'data-options' => 'click' 
                        ]
                    ],
                    'detail' => [
                        'title' => Yii::t('fe', 'Lihat'),
                        'icon' => 'fa fa-eye',

                        'attributes' => [
                            'data-target' => '/fisioterapi/pemeriksaan?id=',
                            'id'          => 'lihat',
                            'data-options'=> 'click'
                        ]
                    ]
                    // 'cancel'
                ], '#example') ?>
            </div>

            <div class="panel-body">

                <?= 
                    SirsTableWidget::widget([
                        'source' => '/fisioterapi/informasi-pasien-fisioterapi/kunjungan',
                        'columns' => [
                            [
                                'field' => 'status_periksa_nama',
                                'header' => 'Status Periksa',
                                'type' => 'text'
                            ],
                            [
                                'field' => 'tgl_pendaftaran',
                                'header' => 'Tanggal Pendaftaran',
                                'type' => 'date'
                            ],
                            [
                                'field' => 'nama_pasien',
                                'header' => 'Nama Pasien',
                                'type' => 'text'
                            ],
                            [
                                'field' => 'terapi_nama',
                                'header' => 'Terapi',
                                'type' => 'text'
                            ],
                            [
                                'field' => 'terapis_nama',
                                'header' => 'Terapis',
                                'type' => 'text'
                            ],
                            [
                                'field' => 'dokterperujuk_nama',
                                'header' => 'Dokter Perujuk',
                                'type' => 'text'
                            ],
                            [
                                'field' => 'carabayar_nama',
                                'header' => 'Cara Bayar',
                                'type' => 'text'
                            ],
                            [
                                'field' => 'statusbayar_nama',
                                'header' => 'Status Bayar',
                                'type' => 'text'
                            ]
                        ]
                    ]);

                ?>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    let rowSelected = {};
    let ngtable = document.querySelector("sirs-ng-table");
    ngtable.addEventListener("onSelectedRow", function(event) {
        rowSelected = event.detail;

        if (rowSelected.status_periksa_id == 4) {
            $("#periksa").attr("disabled", true);
        } else {
            $("#periksa").attr("disabled", false);
        }

        if (rowSelected.status_periksa_id == 4) {
            $("#lihat").attr("disabled", false);
        } else {
            $("#lihat").attr("disabled", true);
        }
    });
    $("#periksa").on("click",function(e){
        e.preventDefault();
        var primaryKey    = rowSelected.primary ? rowSelected.primary : null;
        
        if(!rowSelected.programterapi_id){
            $(this).attr("action","/fisioterapi/informasi-pasien-fisioterapi/pilih-program?pasien_id="+rowSelected.pasien_id+"&pendaftaran_id="+rowSelected.primary);
            $(this).attr("data-target","#modal_backdrop");
            $(this).attr("data-toggle","modal");
            $(this).attr("data-width","80%")
        }else if (primaryKey) {
            $("#periksa").removeAttr("action");
            $("#periksa").removeAttr("data-target");
            $("#periksa").removeAttr("data-toggle");
            var _url = "/fisioterapi/pemeriksaan?id=" + primaryKey; 
            $("#periksa").attr("data-options", "link");
            $("#periksa").attr("data-target", _url);
            $("#periksa").removeAttr("data-url");
        }
    });

    $("#lihat").on("click",function(e){
        e.preventDefault();
        if(rowSelected.programterapi_id && rowSelected.primary){
            $("#lihat").attr("data-options", "link");
            $("#lihat").attr("data-target", "/fisioterapi/pemeriksaan?id="+rowSelected.primary);
            $("#lihat").removeAttr("data-url"); 
        }
    });
    ', View::POS_END, 'js');
?>