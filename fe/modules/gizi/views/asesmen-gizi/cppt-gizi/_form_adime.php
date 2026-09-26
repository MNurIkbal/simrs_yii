<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use dosamigos\ckeditor\CKEditor;
?>
<?php 
    $form = ActiveForm::begin([
        'id' => 'form-adime-'.$cppt['pagt_id'], 
        'action' => '/gizi/asesmen-gizi/simpan-adime',
        'options' => [
                'class' => 'form-horizontal', 
                'enableAjaxValidation' => true,
                'role' => 'form',
                'style' => 'padding: 0 10px 0 10px;'
            ],
    ]); 
?>
<?= Html::hiddenInput('pagt_id', $cppt['pagt_id'], ['id' => 'pagt-id-'.$cppt['pagt_id']]); ?>
<?= Html::hiddenInput('cpptadime_id', $cppt['cpptadime_id'], ['id' => 'temp-pendaftaran'.$cppt['pagt_id']]); ?>
<h3>Asesmen Gizi</h3>
    <?=
        CKEditor::widget([
            'id' => 'asesmen-gizi-'.$cppt['pagt_id'],
            'name' => 'asesmen_gizi',
            'value' => $cppt['asesmen_gizi'],
            'options' => ['rows' => 20],
            'preset' => 'basic',
            'clientOptions' => [
                'extraPlugins' => '',
            ]
        ]);
    ?>
<h3>Diagnosa Gizi</h3>
    <?=
        CKEditor::widget([
            'id' => 'diagnosa-gizi-'.$cppt['pagt_id'],
            'name' => 'diagnosa_gizi',
            'value' => $cppt['diagnosa_gizi'],
            'options' => ['rows' => 20],
            'preset' => 'basic',
            'clientOptions' => [
                'extraPlugins' => '',
            ]
        ]);
    ?>

<h3>Intervensi Gizi</h3>
    <?=
        CKEditor::widget([
            'id' => 'intervensi-gizi-'.$cppt['pagt_id'],
            'name' => 'intervensi_gizi',
            'value' => $cppt['intervensi_gizi'],
            'options' => ['rows' => 20],
            'preset' => 'basic',
            'clientOptions' => [
                'extraPlugins' => '',
            ]
        ]);
    ?>

<h3>Monitoring</h3>
    <?=
        CKEditor::widget([
            'id' => 'monitoring-'.$cppt['pagt_id'],
            'name' => 'monitoring',
            'value' => $cppt['monitoring'],
            'options' => ['rows' => 20],
            'preset' => 'basic',
            'clientOptions' => [
                'extraPlugins' => '',
            ]
        ]);
    ?>

<h3>Evaluasi</h3>
    <?=
        CKEditor::widget([
            'id' => 'evaluasi-'.$cppt['pagt_id'],
            'name' => 'evaluasi',
            'value' => $cppt['evaluasi'],
            'options' => ['rows' => 20],
            'preset' => 'basic',
            'clientOptions' => [
                'extraPlugins' => '',
            ]
        ]);
    ?>

<?php
    ActiveForm::end();
?>