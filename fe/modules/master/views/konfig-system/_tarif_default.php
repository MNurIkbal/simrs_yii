<?php

/**
 * @author Muhammad Rivaldi Irawan
 * @todo View Konfigurasi Tarif Default
 * @copyright 24 Agustus 2023
 */

use yii\web\View;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-body">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table
                            class="table table-striped table-condensed table-hover"
                            id="table-konfig-tarif-default" style="width: 100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">&nbsp;</th>
                                    <th><?= Yii::t('fe', 'No') ?></th>
                                    <th><?=Yii::t('fe', 'Cara Bayar'); ?></th>
                                    <th><?=Yii::t('fe', 'Penjamin'); ?></th>
                                    <th><?=Yii::t('fe', 'Action'); ?></th>
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
</div>
<?php
$this->registerJs($this->render('js/tarifDefault.js'), View::POS_END);
?>