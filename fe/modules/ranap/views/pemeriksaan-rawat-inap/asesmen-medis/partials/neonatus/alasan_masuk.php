<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">A. Alasan Masuk</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'keluhan_utama')->label()->textInput(['class' => 'form-control input-sm']); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'riwayat_keluhan')->label()->textArea(['class' => 'form-control input-sm']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>