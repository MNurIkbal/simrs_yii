<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;

$this->title = \Yii::t('fe', 'Profil Rumah Sakit');
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
               
               <?= DocoHelpers::generateToolbar([
                    'tambah' => [
                        'title' => \Yii::t('fe', 'Tambah'),
                        'icon' => 'fa fa-plus',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'link',
                            'id' => 'btn-tambah',
                            // 'data-content' => 'content-perda',
                            'data-target' => '/master/profil-rumah-sakit/tambah',
                            // 'disabled' => 'disabled'
                        ]
                    ],
                    'update' => [
                        'title' => \Yii::t('fe', 'Update'),
                        'icon' => 'fa fa-pencil',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'link',
                            'id' => 'btn-update',
                            // 'data-content' => 'content-perda',
                            'data-target' => '/master/profil-rumah-sakit/update?id=',
                            'data-pesan-error' => '',
                            'disabled' => 'disabled'
                        ]
                    ],
                    /*'hapus' => [
                        'title' => \Yii::t('fe', 'Hapus'),
                        'icon' => 'fa fa-trash',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'link',
                            'id' => 'btn-hapus',
                            // 'data-content' => 'content-perda',
                            'data-target' => '/master/profil-rumah-sakit/hapus?id=',
                            'data-pesan-error' => '',
                            'disabled' => 'disabled'
                        ]
                    ],*/
                ], '#example') ?>

            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <!-- <th width="1">No</th> -->
                            <th><?= \Yii::t('fe', "Tanggal registrasi"); ?></th>
                            <th><?= \Yii::t("fe", "Nama rumah sakit"); ?></th>
                            <th><?= \Yii::t("fe", "Jenis profil"); ?></th>
                            <th><?= \Yii::t("fe", "Kelas rumah sakit"); ?></th>
                            <th><?= \Yii::t("fe", "Nama kepemilikan RS"); ?></th>
                            <th><?= \Yii::t("fe", "Status akreditas"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    // Global Var
    var table;
    const updateUrl = "/master/profil-rumah-sakit/update?id=";
    const hapusUrl = "/master/profil-rumah-sakit/delete?id=";
    
    $("#btn-tambah").hide();
    $("#btn-update").show();
    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Delete
    $(document).on("click", ".data-delete", function(e) {
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
        table = $("#example").docoTabel({
            method: "POST",
            filter: false,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            scrollX: true,
            drawCallback: function() {
                var total_data = $("#example").DataTable().column(0).data().length;
                if(total_data == 0){
                    $("#btn-tambah").show();
                    $("#btn-update").hide();
                }
                console.log(total_data);
            },
            ajax: baseUrl+"master/profil-rumah-sakit/get-data",
            columns: [

                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                },
                {title: "'.(\Yii::t("fe", "Tanggal registrasi")). '", data: "tglregistrasi"},
                {title: "'.(\Yii::t("fe", "Nama rumah sakit")). '", data: "nama_rumahsakit"},
                {title: "'.(\Yii::t("fe", "Jenis")). '", data: "jenis_rs"},
                {title: "'.(\Yii::t("fe", "Kelas rumah sakit")). '", data: "kelas_rs"},
                {title: "'.(\Yii::t("fe", "Nama kepemilikan RS")). '", data: "nama_penyelenggara"},
                {title: "'.(\Yii::t("fe", "Status akreditas")). '", data: "status_penyelenggara"}
            ]
        });
        $(".dataTables_filter").hide();
        
        // $(".filter-form").datatableBootstrapFilter(table, [[6, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $options['status'], ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Status')]))).'\']]);
    });
', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
