<?php
/**
 * 
 * @author : Erlangga (erlangga@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\pendaftaran\InformasiPasien;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DHtml;

class InformasiPasienSty extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $jenis = $request->get('jenis', null);
        if(!empty($jenis)) {
            $ruanganId = null;
            foreach(DocoConstants::PARAM_DFTR as $key => $value) {
                if($jenis == $value) {
                    $ruanganId = $key;
                    break;
                }
            }

            $controller::jumpRuanganWithoutSetMethod([
                'roomID' => $ruanganId
            ]);
        }
        $_titleInfo = DHtml::getTitleMenu();
        $_titleInfo = !empty($_titleInfo) ? $_titleInfo : "Kunjungan";
        $active_workspace = $session->get('active_workspace');
        // dump($active_workspace);die;
        $controller->setPasienEditLinkBefore('/pendaftaran');
            if(isset($active_workspace['ruangan_id'])){
                $visible = false;
                $visible_mcu = true;
                switch ($active_workspace['ruangan_id']) {
                    case DocoConstants::WS_RAJAL:
                        $singkatan =  DocoConstants::SINGKATAN_RJ;
                        $jenis = 'rajal';
                        $title = Yii::t('fe', 'Rawat Jalan');
                        break;
                    case DocoConstants::WS_IGD:
                        $singkatan =  DocoConstants::SINGKATAN_RD;
                        $jenis = 'igd';
                        $title = Yii::t('fe', 'Rawat Darurat');
                        break;
                    case DocoConstants::WS_PENUNJANG:
                        $singkatan =  DocoConstants::SINGKATAN_RJ;
                        $jenis = 'penunjang';
                        $title = Yii::t('fe', 'Penunjang');
                        break;
                    case DocoConstants::WS_RANAP:
                        $visible = true;
                        $singkatan =  DocoConstants::SINGKATAN_RI;
                        $jenis = 'ranap';
                        $title = Yii::t('fe', 'Rawat Inap');
                        break;
                    case DocoConstants::WS_MCU:
                        $visible_mcu = false;
                        $singkatan =  DocoConstants::SINGKATAN_MCU;
                        $jenis = 'mcu';
                        $title = Yii::t('fe', 'MCU');
                        break;
                    default:
                        $singkatan =  DocoConstants::SINGKATAN_RJ;
                        $jenis = 'rajal';
                        $title = Yii::t('fe', 'Rawat Jalan');
                        break;
                }
            } else {
                $singkatan =  DocoConstants::SINGKATAN_RJ;
                $title = Yii::t('fe', 'Rawat Jalan');
            }

            $title = Yii::t('fe',$_titleInfo).' '.$title;
            $result = $controller->guzzleExec(Yii::$app->docoRest->pendaftaran, [
                'url' => 'allow/pack-informasi-pasien',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'singkatan' => $singkatan
                    ]
                ],
            ]);
            $carabayarList = isset($result['carabayar']) ? $result['carabayar'] : [];
            $ruanganList = isset($result['ruangan']['data']) ? ArrayHelper::map($result['ruangan']['data'], 'ruangan_id', 'ruangan_nama') : [];
            $statusList = isset($result['apawehatuhlah']) ? $result['apawehatuhlah'] : [];
            $listCaraBayar = isset($result['listCaraBayar']) ? $result['listCaraBayar'] : [];
            $statusPeriksaList = isset($result['listStatusPeriksa']) ? $result['listStatusPeriksa'] : [];

            $penjamin = $controller->guzzleExec(Yii::$app->docoRest->master, [
                'url' => 'penjamin/list-penjamin',
            ]);

            $countSyncData = $controller->guzzleExec(Yii::$app->docoRest->pendaftaran, [
                'url' => 'inf-pasien/get-data-sync',
            ]);
            
            return $controller->render('@app/extensions/pendaftaran/views/informasi-pasien/indexSty', get_defined_vars());
    }
}