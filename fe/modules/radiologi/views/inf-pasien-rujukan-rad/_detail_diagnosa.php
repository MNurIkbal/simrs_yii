<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
			<div class="panel-toolbar clearfix">
				<div class="panel-body">
            <h6>Diagnosa Utama :</h6>
            <p><?= $diagnosa_utama; ?></p>

            <h6>Diagnosa Penyerta :</h6>
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

            <h6>Catatan Klinis Dokter :</h6>
            <p><?= nl2br($catatan_dokterpengirim); ?></p>

			</div>
		</div>
	</div>
</div>

