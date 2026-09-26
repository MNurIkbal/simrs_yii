<?php

namespace app\modules\mcu\components\traits;

use Yii;
use app\components\DocoHelpers;
use app\modules\mcu\models\PemeriksaanFisikForm;

trait PemeriksaanFisikTrait
{
  public function actionPemeriksaanFisik()
  {
    $request = Yii::$app->request;
    $pendaftaran_id = $request->get('id', null);
    $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
    $pasien_id = $request->get('pasien_id', null);
    $title = Yii::t('fe', 'Pemeriksaan Fisik');
    $modelFisik = new PemeriksaanFisikForm;
    $option = [
      1 => Yii::t('fe', 'Normal'),
      0 => Yii::t('fe', 'Tidak')
    ];
    if ($modelFisik->load($request->post())) {
      $data = $request->post('PemeriksaanFisikForm');
      $modelFisik->attributes = $data;
      $modelFisik->pendaftaran_id = $pendaftaran_id;
      $request = $this->_restMcu->post('pemeriksaan/save-pemeriksaan-fisik', [
        'form_params' => $modelFisik->attributes
      ]);

      $response = json_decode($request->getBody(), true);
      return DocoHelpers::response($response);
    } else {
        $modelFisik->mata_kanan = $modelFisik->mata_kiri = $modelFisik->telinga_kanan =
        $modelFisik->telinga_kiri = $modelFisik->jantung = $modelFisik->ekg = $modelFisik->paru =
        $modelFisik->hepar = $modelFisik->lien = $modelFisik->ginjal = $modelFisik->motorik =
        $modelFisik->sensorik = 1;

      $cekExistData = $this->cekExistData($pendaftaran_id);
      if ($cekExistData) {
        $modelFisik->berat_badan = $cekExistData['berat_badan'];
        $modelFisik->tinggi_badan = $cekExistData['tinggi_badan'];
        $modelFisik->td_sistolik = $cekExistData['td_sistolik'];
        $modelFisik->td_diastolik = $cekExistData['td_diastolik'];
        $modelFisik->pernafasan = $cekExistData['pernafasan'];
        $modelFisik->detak_nadi = $cekExistData['detak_nadi'];
        $modelFisik->suhu = $cekExistData['suhu'];
        $pemeriksaan_fisik = json_decode($cekExistData['pemeriksaan_fisik'], true);

        // ? get attribute mata
        $mata = isset($pemeriksaan_fisik['mata']) ? $pemeriksaan_fisik['mata'] : '';
        $mata_kanan = isset($mata['mata_kanan']) ? $mata['mata_kanan'] : '';
        $mata_kanan_catatan = isset($mata['mata_kanan_catatan']) ? $mata['mata_kanan_catatan'] : '';
        $mata_kiri = isset($mata['mata_kiri']) ? $mata['mata_kiri'] : '';
        $mata_kiri_catatan = isset($mata['mata_kiri_catatan']) ? $mata['mata_kiri_catatan'] : '';

        $modelFisik->mata_kanan = $mata_kanan;
        $modelFisik->mata_kanan_catatan = $mata_kanan_catatan;
        $modelFisik->mata_kiri = $mata_kiri;
        $modelFisik->mata_kiri_catatan = $mata_kiri_catatan;

        // ? get attribute telinga
        $telinga = isset($pemeriksaan_fisik['telinga']) ? $pemeriksaan_fisik['telinga'] : '';
        $telinga_kanan = isset($telinga['telinga_kanan']) ? $telinga['telinga_kanan'] : '';
        $telinga_kanan_catatan = isset($telinga['telinga_kanan_catatan']) ? $telinga['telinga_kanan_catatan'] : '';
        $telinga_kiri = isset($telinga['telinga_kiri']) ? $telinga['telinga_kiri'] : '';
        $telinga_kiri_catatan = isset($telinga['telinga_kiri_catatan']) ? $telinga['telinga_kiri_catatan'] : '';

        $modelFisik->telinga_kanan = $telinga_kanan;
        $modelFisik->telinga_kanan_catatan = $telinga_kanan_catatan;
        $modelFisik->telinga_kiri = $telinga_kiri;
        $modelFisik->telinga_kiri_catatan = $telinga_kiri_catatan;

        // ? get attribute thorax
        $thorax = isset($pemeriksaan_fisik['thorax']) ? $pemeriksaan_fisik['thorax'] : '';
        $jantung = isset($thorax['jantung']) ? $thorax['jantung'] : '';
        $jantung_catatan = isset($thorax['jantung_catatan']) ? $thorax['jantung_catatan'] : '';
        $ekg = isset($thorax['ekg']) ? $thorax['ekg'] : '';
        $ekg_catatan = isset($thorax['ekg_catatan']) ? $thorax['ekg_catatan'] : '';
        $paru = isset($thorax['paru']) ? $thorax['paru'] : '';
        $paru_catatan = isset($thorax['paru_catatan']) ? $thorax['paru_catatan'] : '';

        $modelFisik->jantung = $jantung;
        $modelFisik->jantung_catatan = $jantung_catatan;
        $modelFisik->ekg = $ekg;
        $modelFisik->ekg_catatan = $ekg_catatan;
        $modelFisik->paru = $paru;
        $modelFisik->paru_catatan = $paru_catatan;

        // ? get attribute abdomen
        $abdomen = isset($pemeriksaan_fisik['abdomen']) ? $pemeriksaan_fisik['abdomen'] : '';
        $hepar = isset($abdomen['hepar']) ? $abdomen['hepar'] : '';
        $hepar_catatan = isset($abdomen['hepar_catatan']) ? $abdomen['hepar_catatan'] : '';
        $lien = isset($abdomen['lien']) ? $abdomen['lien'] : '';
        $lien_catatan = isset($abdomen['lien_catatan']) ? $abdomen['lien_catatan'] : '';
        $ginjal = isset($abdomen['ginjal']) ? $abdomen['ginjal'] : '';
        $ginjal_catatan = isset($abdomen['ginjal_catatan']) ? $abdomen['ginjal_catatan'] : '';

        $modelFisik->hepar = $hepar;
        $modelFisik->hepar_catatan = $hepar_catatan;
        $modelFisik->lien = $lien;
        $modelFisik->lien_catatan = $lien_catatan;
        $modelFisik->ginjal = $ginjal;
        $modelFisik->ginjal_catatan = $ginjal_catatan;

        // ? get attribute ektremitas
        $ektremitas = isset($pemeriksaan_fisik['ektremitas']) ? $pemeriksaan_fisik['ektremitas'] : '';
        $motorik = isset($ektremitas['motorik']) ? $ektremitas['motorik'] : '';
        $motorik_catatan = isset($ektremitas['motorik_catatan']) ? $ektremitas['motorik_catatan'] : '';
        $sensorik = isset($ektremitas['sensorik']) ? $ektremitas['sensorik'] : '';
        $sensorik_catatan = isset($ektremitas['sensorik_catatan']) ? $ektremitas['sensorik_catatan'] : '';

        $modelFisik->motorik = $motorik;
        $modelFisik->motorik_catatan = $motorik_catatan;
        $modelFisik->sensorik = $sensorik;
        $modelFisik->sensorik_catatan = $sensorik_catatan;
        $modelFisik->pemeriksaan_lainnya = isset($cekExistData['pemeriksaan_lainnya']) ? $cekExistData['pemeriksaan_lainnya'] : '';
      }
      return $this->renderAjax('__pemeriksaan_fisik', [
        'pendaftaran_id' => $pendaftaran_id,
        'pasien_id' => $pasien_id,
        'title' => $title,
        'modelFisik' => $modelFisik,
        'option' => $option
      ]);
    }
  }

  private function cekExistData($pendaftaran_id)
  {
    $response = $this->_restMcu->get('pemeriksaan/cek-pemeriksaan-fisik?pendaftaran_id=' . $pendaftaran_id);
    $response = json_decode($response->getBody(), true);
    $response = $response["response"];

    return $response;
  }
}
