<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style>
    table {
        border-collapse: collapse;
    }
   .bg-inverse th, .td-inverse td {
    border: 1px solid #000000;
    padding: 10px;
    text-align: left;
  }
  tr:nth-child(even) {
    background-color: #eee;
  }
  tr:nth-child(odd) {
    background-color: #fff;
  }  
</style>
<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">
            <table cellSpacing="2" width="100%" border="0">
                <thead>
                    <tr class="bg-inverse">
						<th><?= Yii::t('app', 'No') ?></th>
						<th><?= Yii::t('app', 'Nama Ruangan') ?></th>						
						<th><?= Yii::t('app', 'Jenis Kasus Penyakit') ?></th>						
						<th><?= Yii::t('app', 'Status') ?></th>
					</tr>
				</thead>
				<tbody>
						<?php foreach ($data as $index => $value): ?>
						<tr class="td-inverse">
							<td><?= $value['rowNum'] ?></td>
							<td><?= $value['ruangan_nama'] ?></td>
							<td><?= $value['jeniskasuspenyakit_nama'] ?></td>
							<td><?= $value['is_active']  ?></td>
						</tr>
						<?php endforeach ?>
				</tbody>
            </table>
        </div>
    </div>
</div>