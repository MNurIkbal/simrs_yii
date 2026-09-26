
<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Identitas Pasien</h5>
        </div>
        <div class="panel-body select2-md">
            <div class="row form-row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'agama_id')->dropDownList([], ['id' => 'agamaFormHistory']) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'tinggi_badan', ['addon' => ['append' => ['content' => 'cm']]])->textInput(['class' => 'doco-decimal-w-comma imt_field', 'disabled' => true]) ?>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'pendidikan_id')->dropDownList([], ['id' => 'pendidikanFormHistory']) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'berat_badan', ['addon' => ['append' => ['content' => 'kg']]])->textInput(['class' => 'doco-decimal-w-comma imt_field', 'disabled' => true]) ?>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'pekerjaan_id')->dropDownList([], ['id' => 'pekerjaanFormHistory']) ?>
                </div>
                <div class="col-sm-6">
                    <?=$form->field($model, 'bb_ideal', ['addon' => ['append' => ['content' => 'kg']]])->textInput(['class' => 'form-control input-sm berat-badan', 'disabled' => true]); ?>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'caramasuk_id')->dropDownList([], ['id' => 'caraMasukFormHistory']) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'imt', ['addon' => ['append' => ['content' => 'kg/m2']]])->textInput(['class' => 'doco-decimal', 'disabled' => true]) ?>
                </div>
            </div>
            <div class="row form-row">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <?=$form->field($model, 'ket_imt', [])->textInput(['class' => 'form-control input-sm', 'readonly' => 'readonly',]); ?>
                </div>
            </div>
        </div>
    </div>
</div>