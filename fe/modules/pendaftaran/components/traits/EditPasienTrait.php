<?php

namespace app\modules\pendaftaran\components\traits;

use Yii;

trait EditPasienTrait {
  public function setPasienEditLinkBefore($link){
    $session = Yii::$app->session;
    $tmpData = ['link_before' => $link];
    $session->set('edit_pasien',$tmpData);
  }
  public function getPasienEditLinkBefore() {
    $session = Yii::$app->session;
    $hasil = $session->get('edit_pasien');
    $hasil = $hasil['link_before'];
    return $hasil;
  }
}
