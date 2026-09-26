<?php
    use app\components\DocoHelpers;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>



<?php
    foreach ($data as $key => $value) :
?>
    <li class="dd-item dd3-item" data-id="<?= DocoHelpers::encrypt($value['menu_id']) ?>">
        <div class="dd-handle dd3-handle"></div>
        <div class="dd3-content">
            <?php 
                $menu_key = DocoHelpers::decrypt($value['menu_key']);
                $get_name_service = explode("-", $menu_key);
                $name_service = isset($get_name_service[0]) ? $get_name_service[0] : '';
            ?>
            <span class="label border-left-info label-striped">Service - <?= $name_service ?></span>
            &nbsp;&nbsp;<?= $value['menu_namalainnya'] ?>
            <span class="pull-right">
            <?php 
                echo Html::button(
                    "<i class='fa fa-pencil'></i>",
                    [
                        'class' => 'btn btn-dark-turquise btn-xs data-update',
                        'style' => 'margin-right:5px',
                        'action' => Url::to([
                                            '/dcms/menu-modul/update', 
                                            'id_parent' => DocoHelpers::encrypt($value['menu_id'])
                                    ]),
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Ubah'),
                        'data-toggle'  => 'modal',
                        'data-target' => '#modal_backdrop'
                    ]
                );
                echo Html::button(
                    "<i class='fa fa-trash'></i>",
                    [
                        'class' => 'btn btn-danger btn-xs delete',
                        'style' => 'margin-right:5px',
                        'action' => Url::to([
                                            '/dcms/menu-modul/delete', 
                                            'id' => DocoHelpers::encrypt($value['menu_id'])
                                    ]),
                        'data-popup' => "tooltip",
                        'data-placement' => 'bottom',
                        'data-original-title' => Yii::t('fe', 'Hapus'),
                    ]
                );
            ?>
            </span>
        </div>
        <?php
            if ($this->context->renderMenus($key)) :
        ?>
            <ol class="dd-list">
                <?= $this->context->renderMenus($key) ?>
            </ol>
        <?php
            endif;
        ?>
    </li>
<?php
    endforeach;
?>