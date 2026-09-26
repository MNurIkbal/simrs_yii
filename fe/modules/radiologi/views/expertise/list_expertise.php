<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-31 10:43:46
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-01 18:00:45
 */

use yii\helpers\Html;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?php 
    if(count($result['options'])){
        ?>
    <div class="form-group">
        <label class="control-label col-sm-4"><?=Yii::t('fe,','List template')?></label>
        <div class="col-sm-6">
            <?=Html::radioList('listExpertise', null, $result['options'], ['separator'=>"<br>"])?>
        </div>
    </div>
    <?php
    }else{
        ?>
        <h3 class="text-center">Data tidak ditemukan</h3>
        <?php
    }
    ?>

    <br><br><br>
</div>
<div class="modal-footer">
    <div class="row">
        <div class="col-md-12">
            <a class="btn btn bg-teal btn-xs btn-labeled btn-template" data-toggle="modal" data-width="50%" data-target="#modal-template" action="/radiologi/expertise/create-template-baru"><b><i class="fa fa-plus"></i></b> Template Baru</a>
            <?=Html::button(\Yii::t('fe', '<b><i class="fa fa-floppy-o"></i></b> Pilih'), ['class' => 'btn btn bg-teal btn-xs btn-labeled btn-pilih']); ?>
            <?=Html::button(\Yii::t('fe', '<b><i class="fa fa-arrow-left"></i></b> Kembali'),['class' => 'btn bg-slate btn-xs btn-labeled', 'data-dismiss' => 'modal']); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    var data = <?=json_encode($result['data'])?>;
    var _val = {};
    $('input[name="listExpertise"]').change(function(){
        _val = data[$(this).val()];
    })
    $('.btn-pilih').on('click', function(){
        $('#modal_backdrop').modal('toggle')
        $('#inputexpertiseform-expertise_id').val(_val.expertise_id)
        CKEDITOR.instances.ck_hasilexpertise.setData(_val.hasil_expertise)
        CKEDITOR.instances.ck_kesan.setData(_val.kesan)
        CKEDITOR.instances.ck_kesimpulan.setData(_val.kesimpulan)
        
    })
    $('.btn-template').on('click', function(){
        $('#modal_backdrop').modal('toggle')
    })
</script>