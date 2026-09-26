<div class="panel panel-default" style="margin: 10px">
    <div class="panel-heading">
        <h5 class="panel-title"><?= Yii::t('fe', 'Catatan Dokter') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>

    <div class="panel-body" id="resep_racikan" style="margin: 10px 0;">
		<?php foreach ($data_racikan as $key => $value) { ?>
			<div class="row" style="padding: 10px">
				<div class="col-md-1"><?= $value['type'] == 'OR' ? $value['rke'] : 'OTHERS' ?></div>
				<div class="col-md-10" style='white-space:pre;margin-left:-20px;'><?= $value['racikan'] ?></div>
			</div>
		<?php } ?>
	</div>
</div>