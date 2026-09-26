<?php 
use app\components\DocoHelpers;
    if (isset($value['action'])) : 
        $menu_key = DocoHelpers::decrypt($value['menu_key']);
        $get_name_service = explode("-", $menu_key);
        $name_service = isset($get_name_service[0]) ? $get_name_service[0] : '';
?>
<tr>
    <td class="text-center">
        <label>
            <input type="checkbox" 
            name="" class="styled checked-all" 
            name="test-checked"
            data-popup = "tooltip"
            title ="<?= Yii::t('fe', 'Ceklist semua aksi') .' ' . $value['menu_namalainnya'] ?>"
            >
        </label>
    </td>
    <td>
        <span class="label border-left-info label-striped">Service - <?= $name_service ?></span>&nbsp;&nbsp;
        <?= $value['menu_namalainnya']?>
    </td>
    <td>
        <?php
            // Ini Untuk group Berdasarkan service
            $groupByService = [];
            foreach ($value['action'] as $val) :
                $secretKey = DocoHelpers::decrypt($val['menu_key']);
                $explode = explode("-", $secretKey);
                // name service
                $ns = isset($explode[0]) ? $explode[0] : '';
                // name Controller
                $nc = isset($explode[1]) ? str_replace(" ", "", $explode[1]) : '';
                $nameOfService = $ns . ' ' . $nc;
                $groupByService[$nameOfService][] = $val;
            endforeach;

            $labelService = '';
            // extract hasil dari grouping
            foreach ($groupByService as $key => $item) :
                if ($labelService != $key) :
            ?>
                <div style="margin-bottom:10px;margin-top:5px;">
                    <span class="label border-left-info label-striped" style="text-transform:inherit!important;">
                        Service - <?=  ucfirst($key) ?>
                    </span>
                </div>
            <?php
                endif;
                foreach ($item as $attr) :
            ?>
                <div>
                    <label>
                        <input type="checkbox" name="action_akses[]" 
                        value="<?= DocoHelpers::encrypt($attr['menu_id']) ?>" 
                        class="styled action-checked" 
                        <?= in_array($attr['menu_id'], $akses_pengguna) ? "checked" : "" ?>>
                        <?= ucfirst($attr['menu_namalainnya'])?>
                    </label>
                </div>
            <?php
                endforeach;
        ?>
        <?php
                $labelService = $key;
            endforeach;
        ?>
    </td>
</tr>
<?php
    endif;
?>
