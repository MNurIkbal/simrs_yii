<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = 'All New Simrs';
?>
<div class="page-container" style="min-height:700px">

    <!-- Page content -->
    <div class="page-content">

        <!-- Main content -->
        <div class="content-wrapper">

            <!-- Error title -->
            <div class="text-center content-group">
                <p class="error-title" style="margin-top:148px!important;font-size:50px!important;">
                    <?= strtoupper(nl2br(Html::encode($message))) ?>
                </p>
                <?=
                Html::a(
                    '<b><i class="fa fa-arrow-left"></i></b>' . Yii::t('fe', 'Kembali'),
                    Yii::$app->request->referrer,
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs'
                    ]
                );
                ?>

            </div>
            <!-- /error title -->
            <!-- /error wrapper -->

        </div>
        <!-- /main content -->

    </div>
    <!-- /page content -->

</div>
