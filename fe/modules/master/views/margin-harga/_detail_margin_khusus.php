<?php 
/**
 * @author : Ardi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */
use yii\web\View;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
			<div class="panel-toolbar clearfix">
				<div class="panel-body">
					<table class="table table-striped table-condensed table-hover" 
					id="table-detail-khusus-<?= $id ?>" style="width:100%">
					    <thead>
					        <tr class="bg-inverse">
					            <th style="width: 10%">No</th>
					            <th style="width: 45%"><?=\Yii::t("app", "Jenis Obat Alkes");?></th>
					            <th style="width: 45%"><?=\Yii::t("app", "Harga Margin");?></th>
					        </tr>
					    </thead>
					    <tbody>
					        <tr>
					            <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
        tableDetail = $("#table-detail-khusus-"+ id).docoTabel({
            filter: false,
            ordering: false,  
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: "margin-harga/margin-khusus-detail-get-data?id=" + id,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                	title: "'.(\Yii::t("fe", "Jenis Obat Alkes")).'", 
                	data: "jenisobatalkes_m.jenisobatalkes_nama",
                	searchable: false,
                	orderable: false
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
