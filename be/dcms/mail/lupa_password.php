<?php

/**
 * @Author: Sigit
 * @Date:   2018-10-01 11:30:52
 */

use Doco\components\DocoHelpers;
use yii\helpers\Html;

$server = @parse_ini_file('../config/env/.server', true);

$resetLink = isset($server['server']['url']) 
			 ? $server['server']['url'] ."/dcms/v1/auth/reset-password?id=".$id."&pass=".DocoHelpers::encrypt($generated_password)
			 : Yii::$app->urlManager->createAbsoluteUrl(['v1/auth/reset-password', 'id' => $id, 'pass' => DocoHelpers::encrypt($generated_password)]);

?>
<div class="lupa-password">
    <p><?= Yii::t('app', 'Hello ').$email.',' ?></p>
    <p><?= Yii::t('app', 'Anda telah mengaktifkan fitur lupa password, berikut detail dari akun anda:') ?></p>
    <p><?= Yii::t('app', 'Username      : ').$nama_pemakai ?></p>
    <p><?= Yii::t('app', 'Email         : ').$email ?></p>
    <p><?= Yii::t('app', 'Password Baru : ').$generated_password ?></p>
    <p><?= Yii::t('app', 'Klik tombol berikut untuk mengubah password Anda :') ?></p>
    <p><?= Html::a(Yii::t('app', 'Reset Password'), $resetLink, ['style' => 'background-color: #4CAF50; border: none; color: white; padding: 15px 32px; text-align: center; display: inline-block; font-size: 12px; letter-spacing: 1px; text-decoration: none; text-shadow: 0px 2px 2px #fff; font-family: Arial, Helvetica, sans-serif;']) ?></p>
    <p><?= Yii::t('app', 'Note: Anda hanya bisa menggunakan button sebanyak satu kali. Login ke aplikasi mobile BRIMOB dengan menggunakan password default diatas, Kami merekomendasikan Anda untuk mengubah password Anda pada aplikasi mobile BRIMOB di menu -> ubah password.') ?></p>
</div>