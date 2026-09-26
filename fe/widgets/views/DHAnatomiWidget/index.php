<?php
    use yii\web\View;
    use yii\helpers\ArrayHelper;
    use yii\helpers\html;
?>

<div class="row detail">
  <div class="col-md-<?= $size_image ?>">
    <div class="image-frame-<?= $name_id['lower'] ?>"><?php echo Html::img($image, ['class' => '']); ?></div>
    <div class="tag-<?= $name_id['lower'] ?>" style="display: none" data-show="1">
        <span style="box-sizing: border-box;position: absolute;border: 6px solid #b93d3d;border-color: transparent transparent #ff0000 #ff0000;transform-origin: 0 0;transform: rotate(135deg);box-shadow: -3px 3px 3px -3px rgba(0,0,0,0.3);margin-left: 18px;"></span>
        <div class="well well-sm-<?= $name_id['lower'] ?>" style="min-height:130px;">
            <div class="form-group">
                <label class="col-lg-3">Bagian<sup style="color: red">*</sup></label>
                <div class="col-lg-9">
                    <?php echo Html::dropDownList(null,  null,  $option,array(
                            'class' => 'form-control bagian-tubuh-'. $name_id['lower'],
                            'style' => 'padding : 9px 12px !important;',
                            'empty' => '-- Pilih --',
                        )
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-3">Catatan<sup style="color: red">*</sup></label>
                <div class="col-lg-9">
                    <input type="text" placeholder="catatan.." class="form-control add-caption-<?= $name_id['lower'] ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-lg-12">
                    <p class="helper-text ">(tekan <b>enter</b> untuk selesai)</p>
                </div>
            </div>
        </div>
    </div>
  </div>
  <div class="col-md-<?= $size_table ?>"><br>
    <table class="table datatable-basic table-bordered table-striped table-hover dataTable no-footer table-framed tabel-anggotatubuh-<?= $name_id['lower'] ?> doco-wrap-table-text" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th>No</th>
                <th><?= Yii::t('fe', 'Bagian ' . $name_id['ucWord']) ?></th>
                <th><?= Yii::t('fe', 'Keterangan') ?></th>
                <th><?= Yii::t('fe', 'Aksi') ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
  </div>
  <?= Html::hiddenInput("is_updated_" . $name_id['lower'], 'false');?>
</div>

<?php
$phpVars = [
    'tmpData' => $dataAnatomi,
    'nameId' => $name_id['lower'],
    'nameIdUc' => $name_id['ucWord'],
    'bagianTubuh' => $option,
];

$option = json_encode($option);
$script = <<< JS
$(document).ready(function (){
    var nameId = "{$name_id['lower']}"
    var nameIdUc = "{$name_id['ucWord']}"
    var bagianTubuh = {$option}
    var tmpData = {$dataAnatomi}
    var sumbuX = 0;
    var sumbuY = 0;
    var dataAnatomiTable = [];
    tmpHasil[nameId] = tmpData;
    var firstTmpHasil = {...tmpHasil[nameId] };

    var collectObject = function (){
        for (let i = 0; i < Object.keys(tmpData).length; i++) {
            $('.image-frame-' + nameId).append('<div style=\"top: '+ tmpData[i]["koordinat_y"]+'px; left: '+ tmpData[i]["koordinat_x"] +'px;z-index:3;position:absolute;\" class=\"tag-image-'+nameId+' counter-'+nameId+'-'+ tmpData[i]["counters"] +'\"><span class=\"badge bg-warning-400\">'+ tmpData[i]["counters"] +'</span></div>');
            tmpData[i]["buttonAksi"] = '<button type="button" style="padding-left: 9px !important;" class=\"btn btn-danger btn-xs hapus-item-'+nameId+'\" data-counter=\"'+ tmpData[i]["counters"] +'\">'+'<i class=\"fa fa-trash\"></i></button>'
            dataAnatomiTable.push(tmpData[i])
        }
        refreshTable();
    }

    var refreshTable = function () {
        let _tabel = $('.tabel-anggotatubuh-'+nameId).DataTable({
            dom: "tip",
            data: dataAnatomiTable,
            columns: [
                { title: 'No', data: 'counters' },
                { title: 'Bagian Tubuh', data: 'bagian' },
                { title: 'Keterangan', data: 'catatan_tubuh' },
                { title: 'Aksi', data: 'buttonAksi'
            },
            ],
            destroy: true
        });
    }

    var newDeleteRow = function(id) {
        let tempArray = [];
        let newCounter = 1
        $('.counter-'+nameId+'-'+id).remove();
        $('.tag-image-'+nameId).remove();
        dataAnatomiTable.map((item) => {
            if(item.counters != id){
                item.counters = newCounter++
                item.buttonAksi = '<button type="button" style="padding-left: 9px !important;" class=\"btn btn-danger btn-xs hapus-item-'+nameId+'\" data-counter=\"'+ item.counters +'\">'+'<i class=\"fa fa-trash\"></i></button>'
                $('.image-frame-'+nameId).append('<div style=\"top: '+ item.koordinat_y+'px; left: '+ item.koordinat_x +'px;z-index:3;position:absolute;\" class=\"tag-image-'+nameId+' counter-'+nameId+'-'+ item.counters +'\"><span class=\"badge bg-warning-400\">'+ item.counters +'</span></div>');
                tempArray.push(item)
            }
        })
        dataAnatomiTable = tempArray
        let newtmpData = Object.assign({}, dataAnatomiTable)
        tmpData = newtmpData
        if(dataAnatomiTable.length == 0){
            tmpData = {}
        }
        tmpHasil[nameId] = tmpData;
        checkUpdate();
        console.log("delete")
        refreshTable();
    }

    var addRow = function () {
        let data = Object.values(tmpData).pop()
        $('.image-frame-'+nameId).append('<div style=\"top: '+ data.koordinat_y+'px; left: '+ data.koordinat_x +'px;z-index:3;position:absolute;\" class=\"tag-image-'+nameId+' counter-'+nameId+'-'+ data.counters +'\"><span class=\"badge bg-warning-400\">'+ data.counters +'</span></div>');
        data["buttonAksi"] = '<button type="button" style="padding-left: 9px !important;" class=\"btn btn-danger btn-xs hapus-item-'+nameId+'\" data-counter=\"'+ data.counters +'\">'+'<i class=\"fa fa-trash\"></i></button>'
        dataAnatomiTable.push(data)
        refreshTable();
        tmpHasil[nameId] = tmpData;
        checkUpdate();
    }

    // cek perubahan data anatomi
    var isDiffDataAnatomi = function (obj1, obj2) {
        const keys1 = Object.keys(obj1)
        const keys2 = Object.keys(obj2)

        if (keys1.length !== keys2.length) return true

        for (idx of keys2) {            
            if (obj2[idx]['bagiantubuh_id'] != obj1[idx]['bagiantubuh_id']) return true
            if (obj2[idx]['catatan_tubuh'] != obj1[idx]['catatan_tubuh']) return true
            if (obj2[idx]['koordinat_x'] != obj1[idx]['koordinat_x']) return true
            if (obj2[idx]['koordinat_y'] != obj1[idx]['koordinat_y']) return true
        }

        return false // kedua obj sama atau tidak ada perbedaan
    }

    // update input hidden is_updated_nameId
    var checkUpdate = function () {
        if (isDiffDataAnatomi(firstTmpHasil, tmpHasil[nameId])) {
            $('input[name="is_updated_' + nameId + '"]').val('true').trigger('change');
        } else {
            $('input[name="is_updated_' + nameId + '"]').val('false').trigger('change');
        }
    }

    if(Object.keys(tmpData).length != 0 && tmpData.constructor === Object){
        collectObject()
    }

    $(document).off('click', '.hapus-item-'+ nameId)
    $(document).on('click','.hapus-item-'+ nameId, function(){
        let id = $(this).attr("data-counter")
        newDeleteRow(id)
    });

    $('.add-caption-'+ nameId).on('keyup',function(e){
        e.preventDefault();

        $(document).ready(function(){
            $("span.tag.label.label-info").attr('style','display:block');
        });

        var tabel = $('.tabel-anggotatubuh-'+ nameId);
        var _contentParent = $(this).closest('.well-sm-'+ nameId);
        var valBagian = $('.bagian-tubuh-'+ nameId).val();
        if (e.keyCode == 13 || e.keyCode == 27) {
            if (e.keyCode == 13 && (/[\w\d]+/.test($(this).val())) && valBagian != '') {
                if(Object.keys(tmpData).length === 0){
                    counter = 1
                }else{
                    var counter = Object.keys(tmpData).length + 1;
                }
                var bagian = typeof bagianTubuh[valBagian] != 'undefined' ? bagianTubuh[valBagian] : '-';
                tmpData[counter-1] = {
                    counters : counter,
                    bagian : bagian,
                    bagiantubuh_id : valBagian,
                    jenis : nameIdUc,
                    koordinat_y : sumbuY,
                    koordinat_x : sumbuX,
                    catatan_tubuh : $(this).val()
                }
                addRow();
                counter++;
            }
            $(this).val('');
            $('.bagian-tubuh-'+ nameId).prop("selectedIndex", 0).trigger('change');
            $('.tag-'+ nameId).attr({
                style : 'display:none;',
            });
            $('.tag-'+ nameId).data('show',1);
            return false;
        }

    });

    $('.image-frame-'+ nameId).unbind('click');
    $('.image-frame-'+ nameId).bind('click', function(event) {
        console.log('.tag-'+ nameId)
        event.preventDefault();
        var posX = (event.pageX - $(this).offset().left),
            posY = (event.pageY - $(this).offset().top) - 10,
            tag = $('.tag-'+ nameId);
            sumbuX = posX;
            sumbuY = posY;
        if (tag.data('show') != 1) {
            tag.attr({
                style : 'display:none;',
            });
            tag.data('show',1);
        } else {
            tag.attr({
                style : 'top: '+ (posY + 20) +'px; left: '+ posX +'px;width:500px;z-index:3;position:absolute;'
            });
            tag.data('show',2);
        }
    });
    pageFormId = $("#form-fisik :not([readonly]):not('.disable-get-change')"); // get form id page | declare di pemeriksaan js
    pageFormDataValues = pageFormId.serializeArray(); 
});
JS;
$this->registerJs($script, View::POS_END, $name_id['lower']);
?>