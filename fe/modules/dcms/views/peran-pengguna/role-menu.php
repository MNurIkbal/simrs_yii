<div class="tabbable nav-tabs-vertical nav-tabs-left">
    <ul class="nav nav-tabs nav-tabs-highlight">
        <?php
            $no = 1;
            $child = [];
            foreach ($data_menu as $key => $value) :
                $child[] = !empty($value['item']) 
                    ? $value['item'] 
                    : [
                        'kelmenu_nama' => $value['kelmenu_nama'],
                        'kelompokmenu_id' => $value['kelmenu_id'],
                        'data' => []
                    ];
                if (!isset($child[$key]['kelmenu_nama'])) {
                    $child[$key]['kelmenu_nama'] = $value['kelmenu_nama'];
                }
        ?>
            <li class="<?= $no == 1 ? 'active' : '' ?>">
                <a href="#<?= $value['kelmenu_id'] ?>" data-toggle="tab" aria-expanded="false">
                    <i class="<?= $value['kelmenu_icon'] ?> position-left"></i> 
                        <?= $value['kelmenu_nama'] ?>
                </a>
            </li>
        <?php
            $no++;
            endforeach;
        ?>

    </ul>
    <div class="tab-content">
        <?php 
            foreach ($child as $key => $value) :
                if (!isset($value['kelompokmenu_id'])) continue;
                $this->context->_html = '';
        ?>
            <div class="tab-pane has-padding <?= $key ? '' : 'active' ?>" id="<?= $value['kelompokmenu_id'] ?>">
                <input type="hidden" name="kelompokmenu_id[]" value="" class="kelompokmenu_id">
                <table class="table table-striped table-condensed table-hover data-table" 
                style="width:100%" data-index=<?= $key ?>>
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1" class="text-center">
                            <label>
                                <input type="checkbox" 
                                    name="" class="styled checked-table" name="test-checked"
                                    data-popup = "tooltip"
                                    title ="<?= Yii::t('fe', 'Ceklist semua') .' '. $value['kelmenu_nama'] ?>"
                                    >
                            </label>
                            </th>
                            <th class="text-center"><?=\Yii::t("fe", "Menu");?></th>
                            <th class="text-center"><?=\Yii::t("fe", "Hak Akses");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?= $this->context->renderMenu($value['data'],$akses_pengguna) ?>
                    </tbody>
                </table>
            </div>
        <?php
            endforeach;
        ?>
    </div>
</div>