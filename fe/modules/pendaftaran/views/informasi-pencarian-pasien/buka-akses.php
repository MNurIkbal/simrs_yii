<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use app\components\DocoConstants;
    use app\components\DocoHelpers;
?>
<style>
    #table-buka-akses tr td {
        vertical-align: top;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Pemberian Akses Edit Form</h5>
</div>
<div class="modal-body">
    <?php
        $form = ActiveForm::begin([
            'id' => 'buka-akses-form',
            'type' => ActiveForm::TYPE_HORIZONTAL,
        ]);
    ?>
    <table width="100%" id="table-buka-akses">
        <tr>
            <td width="20%">Nama Pasien</td>
            <td width="1%">:</td>
            <td width="29%"><?= $body['pasien']['nama_pasien']?></td>
            <td width="20%">Tempat, Tanggal Lahir</td>
            <td width="1%">:</td>
            <td width="29%"><?= $body['pasien']['tempat_lahir'].", ".DocoHelpers::convertDate($body['pasien']['tanggal_lahir'])?></td>
        </tr>
        <tr>
            <td>No Rekam Medik</td>
            <td>:</td>
            <td><?= $body['pasien']['no_rekam_medik']?></td>
            <td>Alamat</td>
            <td>:</td>
            <td><?= $body['pasien']['alamat_pasien']?></td>
        </tr>
    </table>
    <br>
    <table class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="20">No</th>
                <th><?=\Yii::t("fe", "Nama Form");?></th>
                <th><?=\Yii::t("fe", "Akses Edit");?></th>
            </tr>
        </thead>
        <tbody>
            
            <?php 
                $no = 1;
                foreach ($arrayConfig as $key => $value) {?>
                <tr>
                    <td><?= $no?></td>
                    <td>
                        <?= $value['lookup_name'];?>
                    </td>
                    <td>
                        <?=$form
                            ->field($model, 'akses['.$value['lookup_value'].']', ['labelOptions' => ['class' => 'text-left']])
                            ->checkbox([
                                'class' => 'switch',
                                'label' => false,
                                // 'checked' => $model->akses,
                                'data-on-color' => 'success',
                                'data-off-color' => 'danger', 'data-size' => 'mini',
                                'data-on-text' => 'On',
                                'data-off-text' => 'Off',
                            ])
                            ->label($model->getAttributeLabel('akses'));
                        ?>
                    </td>
                </tr>
            <?php 
                $no++;
            }?>
            
        </tbody>
    </table>
    <br><br>
    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save']); ?>

        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
    
    
    

<script type="text/javascript">
    
    $(".switch").bootstrapSwitch();
    $(document).on("switchChange.bootstrapSwitch", ".switch", function (e, state) {
        if (e.target.checked == true) {
            $value = '1';
            $('input.prop_state').val($value);
        } else {
            $value = '0';
            $('input.prop_state').val($value);
        }
    });

    $("#buka-akses-form").docoForm("submit",{
        success : function(data) {
            $("#modal_backdrop").modal("toggle");
        },
    });
</script>