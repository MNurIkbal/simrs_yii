<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">T. Masalah/Diagnosa Keperawatan</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'diagnosa_keperawatan')
                            ->label(false)->textArea(['cols' => 5]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
