<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<div class="body">
    <div class="form-group">
        <div class="col-lg-12">
            <table border="1" style="width:100%; border-collapse: collapse;">
				<thead>
					<tr class="bg-inverse">
						<th><?= Yii::t('app', 'No') ?></th>
						<th><?= Yii::t('app', 'Kode Supplier') ?></th>
						<th><?= Yii::t('app', 'Nama Supplier') ?></th>
						<th><?= Yii::t('app', 'Alamat') ?></th>
						<th><?= Yii::t('app', 'No Telphone') ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($model)): ?>
						<?= $no = 1; ?>
						<?php foreach ($model as $index => $value): ?>
						<tr>
							<td><?= $no ?></td>
							<td><?= $value->supplier_kode ?></td>
							<td><?= $value->supplier_nama ?></td>
							<td><?= $value->supplier_alamat ?></td>
							<td><?= $value->no_tlp ?></td>
						</tr>
						<?= $no++; ?>
						<?php endforeach ?>
					<?php endif ?>
				</tbody>
			</table>
        </div>
    </div>
</div>