<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
			<div class="panel-toolbar clearfix">
				<div class="panel-body">
                    <p style="margin-bottom:0">Diagnosa Utama :</p>
                    <p><?= $diagnosa_utama; ?></p>

                    <p style="margin-bottom:0">Diagnosa Penyerta :</p>
                    <?php use yii\helpers\ArrayHelper; if (count($diagnosa_penyerta)): ?>
                        <?php if (count($diagnosa_penyerta) < 2): ?>
                            <?php foreach ($diagnosa_penyerta as $penyerta): ?>
                                <?= ArrayHelper::getValue($penyerta, 'text', '-'); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <ol style="padding-left:15px">
                                <?php foreach ($diagnosa_penyerta as $penyerta): ?>
                                    <li><?= ArrayHelper::getValue($penyerta, 'text', '-'); ?></li>
                                <?php endforeach; ?>
                            </ol> 
                        <?php endif; ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
			</div>
		</div>
	</div>
</div>
