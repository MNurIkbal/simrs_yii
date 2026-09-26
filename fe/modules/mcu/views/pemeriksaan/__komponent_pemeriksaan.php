<div class="panel-body">
    <h5><strong>ANTROPOMETRI</strong></h5>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'berat_badan', [
            'addon' => ['append' => ['content' => 'Kg']],
          ])->textInput([
            'class' => 'form-control input-sm doco-decimal-wcomma',
          ])->label(Yii::t('fe', 'BB')); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'tinggi_badan', [
            'addon' => ['append' => ['content' => 'cm']],
          ])->textInput([
            'class' => 'form-control input-sm doco-decimal-wcomma',
          ])->label(Yii::t('fe', 'TB')); ?>
        </div>
      </div>
    <h5><strong>TANDA VITAL</strong></h5>
      <div class="row">
        <div class="col-lg-6">
          <div class="col-lg-6">
            <?= $form->field($model, 'td_sistolik', [
              'addon' => ['append' => ['content' => 'mmHg']],
            ])->textInput([
              'class' => 'form-control input-sm doco-number',
            ])->label(Yii::t('fe', 'TD Sistolik')); ?>
          </div>
          <div class="col-lg-6">
            <?= $form->field($model, 'td_diastolik', [
              'addon' => ['append' => ['content' => 'mmHg']],
            ])->textInput([
              'class' => 'form-control input-sm doco-number',
            ])->label(Yii::t('fe', 'TD Diastolik')); ?>
          </div>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'detak_nadi', [
            'addon' => ['append' => ['content' => 'x/menit']],
          ])->textInput([
            'class' => 'form-control input-sm doco-number',
          ])->label(Yii::t('fe', 'Nadi')); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6">
          <?= $form->field($model, 'pernafasan', [
            'addon' => ['append' => ['content' => 'x/menit']],
          ])->textInput([
            'class' => 'form-control input-sm doco-number',
          ]); ?>
        </div>
        <div class="col-lg-6">
          <?= $form->field($model, 'suhu', [
            'addon' => ['append' => ['content' => '&#8451;']],
          ])->textInput([
            'class' => 'form-control input-sm doco-decimal-wcomma',
          ]); ?>
        </div>
      </div>
  </div>