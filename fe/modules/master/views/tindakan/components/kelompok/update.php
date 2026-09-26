<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Tindakan Ruangan
 * @copyright 26 April 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\CaraBayarForm;
use Doco\master\controllers\CaraBayarController;
use kartik\widgets\ActiveForm;


// $this->title = $title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'simpan' => [
                            'title' => \Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-save',
                            'attributes' => [
                                'class' => 'spa',
                                'action' => '/master/tindakan/map-kelompok',
                                'data-options' => 'click',
                                'form-id' => 'antrian-form',
                                'data-render' => 'map-kelompok',
                                'data-tab' => 'tab-kelompok',
                                'data-target' => '#view-kelompok',
                                'id' => 'btn-save'
                            ]
                        ],
                        'kembali' => [
                            'title' => \Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'kelompok',
                                'data-tab' => 'tab-kelompok',
                                'data-target' => '#view-kelompok',
                            ]
                        ],
                    ],'#table-kelompok');
                ?>
            </div>
            <div class="panel-body">
                ID : <?= $id ?>
            </div>
        </div>
    </div>
</div>
<?php
    // $this->registerJs($this->render('js/kelompok.js'), View::POS_END);
?>