<?php
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?=$title;?> </b></h3>
                        <?=Breadcrumbs::widget([
                            'homeLink' => [ 
                                'label' => Yii::t('yii', 'Home'),
                                'url' => Yii::$app->homeUrl,
                            ],
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        ]);?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'back',
                        'create-invoice' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Buat Invoice'),
                            'icon' => 'fa fa-plus',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id' => 'create-invoice',
                                'method' => 'json',
                                'data-options' => 'link'
                            ]
                         ],
                         'cetak-invoice' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Cetak Invoice'),
                            'icon' => 'fa fa-print',
                            'attributes' => [
                               'id' => 'cetak-invoice',
                               'method' => 'json',
                               'data-options' => 'link'
                            ],
                         ],
                         'cetak-kesimpulan' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Cetak Kesimpulan'),
                            'icon' => 'fa fa-print',
                            'attributes' => [
                               'id' => 'cetak-kesimpulan',
                               'method' => 'json',
                               'data-options' => 'link'
                            ],
                         ],
                    ]);?>
                </div>
            </div>
            <div class="panel-body">
                <fieldset class="scheduler-border">
                    <center><legend class="scheduler-border">Detail <br> <?=$this->title;?></legend></center>
                    <table width="100%">
                        <tr>
                            <td width="15%"><strong> No Order </strong></td>
                            <td><strong> : <?= isset($header['no_order']) ? $header['no_order'] : '-' ?> </strong></td>
                            <td width="15%"><strong> Penjamin </strong></td>
                            <td><strong> : <?= isset($header['penjamin']) ? $header['penjamin'] : '-' ?> </strong></td>
                            <td width="15%"><strong> Pasien Order </strong></td>
                            <td><strong> : <?= isset($header['pasien_order']) ? $header['pasien_order'] : '-' ?> </strong></td>
                        </tr>
                        <tr>
                            <td width="15%"><strong> Tanggal Order </strong></td>
                            <td><strong> : <?= isset($header['tgl_order']) ? date('d M Y',strtotime($header['tgl_order'])) : '-'  ?> </strong></td>
                            <td width="15%"><strong> Paket </strong></td>
                            <td><strong> : <?= isset($header['paket_mcu']) ? $header['paket_mcu'] : '-' ?> </strong></td>
                            <td width="15%"><strong> Pasien Periksa </strong></td>
                            <td><strong> : <?= isset($header['pasien_periksa']) ? $header['pasien_periksa'] : '-' ?> </strong></td>
                        </tr>
                        <tr>
                            <td width="15%"><strong> Status </strong></td>
                            <td><strong> : <?= ($header['sisa_pasien'] == 0) ? 'Close' : 'Open' ?> </strong></td>
                            <td></td>
                            <td></td>
                            <td width="15%"><strong> Sisa Pasien </strong></td>
                            <td><strong> : <?= isset($header['sisa_pasien']) ? $header['sisa_pasien'] : '-' ?> </strong></td>
                        </tr>
                    </table>
                </fieldset>
                <?= Yii::$app->controller->renderPartial('partial/_listPasien', [
                    'status_reservasi' => $status_reservasi,
                    'no_order' => $id,
                    'token' => $token,
                ]) ?>
            </div>
        </div>
    </div>
</div>