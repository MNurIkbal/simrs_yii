<?php 
use yii\web\View;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
			<div class="panel-toolbar clearfix">
				<div class="panel-body">
					<table class="table table-striped table-condensed table-hover" 
					id="table-detail-<?= $id ?>" style="width:100%">
					    <thead>
					        <tr class="bg-inverse">
					            <th width="1">No</th>
					            <th><?=\Yii::t("app", "Harga Min");?></th>
					            <th><?=\Yii::t("app", "Harga Max");?></th>
					            <th><?=\Yii::t("app", "Harga Margin");?></th>
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
    var tableDetail;
    $(document).ready(function() {
        tableDetail = $("#table-detail-"+ id).docoTabel({
            filter: false,
            sorting: [[1, "asc"]],  
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: "margin-harga/data-detail?id=" + id,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                	title: "'.(\Yii::t("fe", "Harga Min (Rp.)")).'", 
                	data: "harga_min",
                	searchable: false,
                	orderable: false,
                	class: "text-right"
                },
                {
                	title: "'.(\Yii::t("fe", "Harga Max (Rp.)")).'", 
                	data: "harga_max",
                	searchable: false,
                	orderable: false,
                	class: "text-right"
                },
                {
                	title: "'.(\Yii::t("fe", "Margin (%)")).'", 
                	data: "margin",
                	searchable: false,
                	orderable: false,
                	class: "text-right"
                },
            ],
        });
    });
    ',VIEW::POS_END);
?>
