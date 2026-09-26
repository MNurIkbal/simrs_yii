<?php 
use yii\web\View;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <!-- <div class="panel-heading">
                <div class="row">
                    <div class="column-2">
                        <h5 class="panel-title"><b>
                            Group <?//= $attributes['groupinacbg_nama'] ?></b>
                        </h5>
                    </div>
                </div>
            </div> -->
            <div class="panel-toolbar clearfix">
                <div class="panel-body">
                    <table class="table table-striped table-condensed table-hover" 
                    id="table-detail-<?= $id ?>" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1">No</th>
                                <th><?=\Yii::t("app", "Tindakan");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="6"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs('
    var id = "'.$id.'";
    var type = "'.$type.'";
    var tableDetail;
    $(document).ready(function() {
        tableDetail = $("#table-detail-"+ id).docoTabel({
            filter: false,
            sorting: [[1, "asc"]],  
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: "tindakan/data-detail-tindakan-inacbg?id=" + id + "&type="+type,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.($type == 0 ? \Yii::t("fe", "Tindakan") : \Yii::t("fe", "Obat")).'", 
                    data: "text",
                    searchable: false,
                    name: "'.($type == 0 ? 'daftartindakan_nama' : 'obatalkes_nama').'"
                },
            ],
        });
    });
    ',VIEW::POS_END);
?>
