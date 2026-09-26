<?php

/**
 * @Author: Sigit
 * @Date:   2018-10-19 10:32:18
 */

use yii\helpers\Html;
?>
<div class="lupa-password">
    <p><?= Yii::t('app', 'Hello ').$nama_pemakai.',' ?></p>
    <p><?= Yii::t('app', 'Terimakasih telah mendaftar sebagai user di aplikasi mobile BRIMOB, berikut adalah data diri Anda :') ?></p>
    <p><?= Yii::t('app', 'Username      : ').$nama_pemakai ?></p>
    <p><?= Yii::t('app', 'Email         : ').$email ?></p>
    <p><?= Yii::t('app', 'Silakan melakukan login ke aplikasi mobile BRIMOB dengan menggunakan username diatas dan password yang telah Anda masukkan ketika mendaftar.') ?></p>
</div>