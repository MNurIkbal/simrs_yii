<?php
use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;

$this->title = $title;
?>
<style type="text/css">
    .row-mcu {
        background-color: #FCF3CF !important;
        color: #000000;
    }
    .my-legend .legend-title {
        text-align: left;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 90%;
    }
    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        float: left;
        list-style: none;
    }
    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        width: 75px;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 80%;
        list-style: none;
    }
    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        height: 15px;
        width: 75px;
        border: solid 0.2px;
    }
    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }
    .my-legend a {
        color: #777;
    }
    .square-sukses {
        height: 30px;
        width: 120px;
        background-color: #26A65B;
        color: #ffffff;
        padding: 5px 0 5px 10px;
        margin-right: 20px;
    }
    .square-batal {
        height: 30px;
        width: 70px;
        background-color: #D24D57;
        color: #ffffff;
        padding: 5px 0 5px 10px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search' => [
                        'attributes' => [
                            'data-parent' => '.filter-paket-fisio'
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'data-parent' => '.filter-paket-fisio'
                        ]
                    ],
                    'tambah' => [
                        'title' => 'Tambah',
                        'icon' => 'fa fa-plus',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'click',
                            'data-render' => 'paket-fisio-create',
                            'data-tab' => 'tab-paket-fisio',
                            'data-target' => '#view-paket-fisio',
                        ]
                    ],
                    'update' => [
                        'title' => 'Ubah',
                        'icon' => 'fa fa-edit',
                        'attributes' => [
                            'id' => 'btn-edit-paket-fisio',
                            'class' => 'spa btn btn-info btn-labeled btn-xs btn-toolbar btn-edit-paket-fisio',
                            'data-options' => 'click',
                            'data-render' => 'paket-fisio-edit?id=',
                            'data-tab' => 'tab-paket-fisio',
                            'data-target' => '#view-paket-fisio',
                            'data-type' => 'wp'
                        ]
                    ],
                    'delete' => [
                        'attributes' => [
                            'id' => 'btn-delete-paket-fisio',
                            'url' => '/master/tindakan/paket-fisio-delete?id=',
                            'data-additional' => 'data-rm'
                        ]
                    ],
                    'pdf' => [
                        'attributes' => [
                            'data-target' => '/master/tindakan/paket-fisio-export-pdf?'
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => '/master/tindakan/paket-fisio-export-excel?'
                        ]
                    ],
                ], '#table-paket-fisio') ?>
            </div>
            <div class="panel-body">
                <div class="tab-paket-fisio"></div>
                <table id="table-paket-fisio" class="table table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1"></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9">Data tidak ditemukan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$phpVars = [
    'status' => $status,
    'column_names' => [
        'kode_paket' => "Kode Paket Fisioterapi"
    ],
    'form_filters' => [
        'is_active' => Html::dropDownList('is_active', '', $status, ['class' => 'form-control select2', 'prompt' => 'Pilih']),
        'is_mcu' => Html::dropDownList('is_mcu', '', [1 => 'Paket MCU', 0 => 'Paket Non MCU'], ['class' => 'form-control select2', 'prompt' => 'Pilih'])
    ]
];

$this->registerJsVar('phpVars', $phpVars);
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>