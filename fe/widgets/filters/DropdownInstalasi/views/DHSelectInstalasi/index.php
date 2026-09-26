<?php

use yii\web\View;
use yii\helpers\Html;

    $className = "form-control";
    if ($isDepToParent) $className .= " select2";
    if ($isDepToChild) $className .= " select2 dep-to-child select2-hidden-accessible";
?>

<div class="form-group" >
<?php
    if($isDepToChild === true || $isDepToParent === true){
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
if(!$isDepToParent && !$isDepToChild){
    $this->registerJs("
            var configInstalasi = {
                url: `/api/master/get-instalasi`,
                additionalOption: {
                    placeholder: `-- Pilih Instalasi --`,
                    allowClear: true
                },
                callbackProccess: (data) => {
                    var results = []; 
                    $.each(data.results.result, function (index, instalasi) {
                        results.push({
                            id: instalasi.instalasi_id,
                            text: instalasi.instalasi_nama
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
            $(`#$id`).select2InfinityScroll(configInstalasi);
        ", View::POS_READY);
}
?>