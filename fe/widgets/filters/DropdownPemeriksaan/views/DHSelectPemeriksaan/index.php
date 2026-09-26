<?php

use yii\web\View;
use yii\helpers\Html;

    $className = "form-control";
    if ($isDepToParent || $independent) $className .= " select2";
    if ($isDepToChild) $className .= " select2 dep-to-child select2-hidden-accessible";
?>

<div class="form-group" >
<?php
    if($isDepToChild === true || $isDepToParent === true || $independent === true){
        echo Html::dropDownList($id, '', 
            $datas, 
            [
                'id'                 => $id, 
                'class'              => $className, 
                'prompt'             => $prompt,
                'data-url'           => $depUrl,
                'data-depend_id'     => $idDepChild,
                'data-depend_prompt' => $dataDependPrompt,
                'data-storage'       => $dataStorage,
                'data-key'           => $dataKey,
            ]
            );
    }else{
        ?>
            <select class="<?= $className ?>" id="<?= $id ?>"></select>
        <?php
    }
    ?>
</div>

<?php
if(!$isDepToParent && !$isDepToChild && !$independent){
    $this->registerJs("
            var configPemeriksaan = {
                url: `/api/master/get-pemeriksaan-fisioterapi-dep`,
                additionalOption: {
                    placeholder: `-- Pilih Pemeriksaan --`,
                    allowClear: true
                },
                callbackProccess: (data) => {
                    var results = []; 
                    $.each(data.results.result, function (index, pemeriksaan) {
                        results.push({
                            id: pemeriksaan.daftartindakan_id,
                            text: pemeriksaan.daftartindakan_nama
                        });
                    });
                    return {
                        results: results,
                        pagination: {
                            more: data.results.pagination.more
                        },
                        incomplete_results: false,
                    };    
                },
                callbackData: (params) => {
                    return {
                        term: params.term,
                        page: params.page || 1,
                        limit: params.limit,
                    }
                }
            }
            $(`#$id`).select2InfinityScroll(configPemeriksaan);
        ", View::POS_READY);
}
?>