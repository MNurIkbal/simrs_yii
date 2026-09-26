<?php
use yii\web\View;
use kartik\widgets\DatePicker;
?>
<div class="panel panel-white">
    <div class="panel-heading">
        <div class="row">
            <div class="column-1">
            </div>
            <div class="column-2">
                <h6 class="panel-title">
                    <b>List Peserta Finger Print</b>
                </h6>
            </div>
        </div>
        <div class="heading-elements">
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-3">
                <div class="advanced-filter">
                    <?php
                        echo '<label class="form-label">Tanggal Pelayanan</label>';
                        echo DatePicker::widget([
                            'id' => 'tglPelayanan',
                            'name' => 'tglPelayanan',
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                            'readonly' => true,
                            'language' => 'en',
                            'value' => date('Y-m-d'),
                            'pluginOptions' => [
                                'endDate' => '0d',
                                'format' => 'yyyy-mm-dd',
                                'autoclose' => true,
                                'todayBtn' => true
                            ],
                            'pluginEvents' => [
                                "changeDate" => "function(e) {
                                    table.ajax.url('/pendaftaran/tools-bpjs/get-listfinger?tglPelayanan='+$('#tglPelayanan-kvdate').datepicker('getFormattedDate')).load();
                                }",
                            ]
                        ]);
                    ?>
                </div>
            </div>
        </div>
        <table id="table-peserta-finger" class="table table-striped" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th><?= Yii::t("fe", "No. Kartu") ?></th>
                    <th><?= Yii::t("fe", "No. SEP") ?></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<?php 
$this->registerJs('
var table;
var data = [];
$(document).ready(function() {
    table = $("#table-peserta-finger").DataTable({
        filter:false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: "/pendaftaran/tools-bpjs/get-listfinger?tglPelayanan="+$("#tglPelayanan-kvdate").datepicker("getFormattedDate"),
        columns: [
            {title: "No. Kartu", data: "noKartu"},
            {title: "No. Sep", data: "noSEP"},
        ]
    });
});
',View::POS_END, 'index');
?>