<?php
use yii\web\View;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use yii\web\JsExpression;
?>
<style>
    .badge {
        background-color: #575c59;
        color:#ffffff;
    }
</style>

<div class="panel panel-white">
    <div class="panel-heading">
        <div class="row">
            <div class="column-1">
            </div>
            <div class="column-2">
                <h6 class="panel-title">
                    <b>Resend Order Lis</b>
                </h6>
            </div>
        </div>
        <div class="heading-elements">
        </div>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin([
            'id' => 'form-resend-order-lis',
            'type' => ActiveForm::TYPE_VERTICAL,
            'action' => '/dcms/dev-tools/resend-order-lis',
            'method' => 'POST',
            'formConfig' => [
                'labelSpan' => 5,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ]
        ]) ?>
        <div class="row">
            <div class="col-md-4">
            <?= $form->field($model, 'reference')->widget(Select2::classname(), [
                'options' => [
                    'placeholder' => 'Cari No.RM/No.Pendaftaran/No.Penunjang',
                    'class' => 'form-control input-sm select2 selectReference'
                ],
                'pluginOptions' => [
                    'allowClear'=>true,
                    'minimumInputLength'=>3,
                    'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                    ],
                    'ajax' => [
                    'url' => \yii\helpers\Url::to(['/dcms/dev-tools/get-pasien-penunjang']),
                    'dataType' => 'json',
                    'data' => new JsExpression('
                        function(params) {
                            return {
                                q: params.term,
                                page:params.page || 1,
                                limit:params.limit || 10,
                            }; 
                        }
                    '),
                    'processResults' => new JsExpression('
                        function (data, params) {
                            params.page = params.page || 1;
                            return {
                                results: data.results,
                                pagination: {
                                    more: data.pagination.more
                                }
                            }
                        }
                    ')
                    ],
                    'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                    'templateResult' => new JsExpression('function(res){ return res.text;}'),
                    'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                ],
            ]);?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 btn-component-resend" style="display:none;">
                <button type="button" id="btn-resend-integrasi-lis" class="btn btn-info btn-sm btn-simpan" ><b><?=Yii::t('fe','Resend')?></b></button>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        
        <div class="row mt-5 riwayat-integrasi" style="display:none;">
            <div class="col-md-12">
                <h2>Riwayat Integrasi LIS</h2>
                <div class="table-responsive">
                    <table id="table-riwayat-integrasi" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th>pasienmasukpenunjang_id</th>
                                <th>pendaftaran_id</th>
                                <th>Payload</th>
                                <th>Sync Respon</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
$('.selectReference').on('select2:unselect',function(){
    $('.riwayat-integrasi').hide();
    $('.btn-component-resend').hide();
    $('#table-riwayat-integrasi').find('tbody').html('');
});
$('.selectReference').on('select2:select',function(){
    $.ajax({
        method: 'GET',
        url: '/dcms/dev-tools/get-riwayat-integrasi-lis',
        data: {pasienmasukpenunjang_id:$(this).val()},
        success: function(response){
            bodyTable = $('#table-riwayat-integrasi').find('tbody');
            $('.riwayat-integrasi').show();
            $('.btn-component-resend').show();
            var row = '';
            $.each(response.response.data,function(idx,val){
                row = $('<tr></tr>');

                col = $('<td>' + val.pasienmasukpenunjang_id + '</td>');
                row.append(col);
                
                col = $('<td>' + val.pendaftaran_id + '</td>');
                row.append(col);

                col = $('<td class=\'jsonfield\'><span class=\'json_less\'>' + val.payload.substr(1,25) + '</span><span class=\'badge btn_more\'> more </span><span class=\'json_more\' style=\'display:none;\'><pre>'+JSON.stringify(JSON.parse(val.payload),undefined,2)+'></pre></span></td>');
                row.append(col);

                col = $('<td class=\'jsonfield\'><span class=\'json_less\'>' + val.sync_respon.substr(1,25) + '</span><span class=\'badge btn_more\'> more </span><span class=\'json_more\' style=\'display:none;\'><pre>'+JSON.stringify(JSON.parse(val.sync_respon),undefined,2)+'></pre></span></td>');
                row.append(col);
                
                col = $('<td>' + val.created_date + '</td>');
                row.append(col);
            });
            bodyTable.append(row);
        }
    });
});
$(document).ready(function(){
    $(document).off('click','span.btn_more');
    $(document).on('click','span.btn_more',function(){
        elemLess = $(this).siblings('.json_less');
        elemMore = $(this).siblings('.json_more');
        elemLess.toggle();
        elemMore.toggle();
    });
});
$('#btn-resend-integrasi-lis').on('click',function(){
    let id = $('.selectReference').val();
    $.ajax({
        method:'GET',
        url: '/dcms/dev-tools/resend-order-lis',
        data: {pasienmasukpenunjang_id:id},
        success:function(res){
            docoNotification('success',res.response.title,res.response.text);
        }
    });
});
",View::POS_END, 'index');
?>