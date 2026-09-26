<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\EsselonForm;
use Doco\master\controllers\EsselonController;

$this->title = \Yii::t('fe', 'Akses Pengguna');
$this->params['breadcrumbs'][] = ['label' => 'Dcms', 'url' => ['index']];
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
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'payment' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-pencil',
                            'method' => 'not exist',
                            'attributes' => [
                                'class' => 'data-payment',
                                'id' => 'tambah',
                                'data-target'=>"/dcms/akses-pengguna/update?id=",
                                // 'href' => 'javascript:void(0)',

                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-aksespengguna');?>

            </div>


            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-aksespengguna" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Peran pengguna nama");?></th>
                            <th><?=\Yii::t("fe", "Hak Akses");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
    var tableAksesPengguna;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tableAksesPengguna.draw();
    });


    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableAksesPengguna = $("#table-aksespengguna").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                id: "cb",
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
            scrollX: true,
            ajax: baseUrl+"dcms/akses-pengguna/get-data",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render : function () {
                        return null;
                    }
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Peran pengguna nama")).'",
                    data: "nama_pemakai"
                },
                {
                    title: "'.(\Yii::t("fe", "Hak Akses")).'",
                    data: "akses_pengguna",
                    name: "peranpengguna_k.peranpenggunanama"
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableAksesPengguna);
    });


    // $("#table-aksespengguna tbody").on("click", "tr", function(){
    //         try {
    //             primaryKey = tableAksesPengguna.row(".selected").data().primary ? tableAksesPengguna.row(".selected").data().primary : null;
    //         } catch (e) {
    //             primaryKey = false;
    //         }
    //         console.log(primaryKey);

    //         if (primaryKey) {
    //             $("#tambah").attr("href",);
    //         } else {
    //             $("#tambah").attr("href","javascript:void(0)");
    //         }

    //     });
', View::POS_END, 'b-index');
?>
