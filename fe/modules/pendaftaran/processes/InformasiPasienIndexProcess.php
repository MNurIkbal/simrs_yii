<?php
/**
 * 
 * @author : Erlangga (erlangga@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\pendaftaran\processes;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\pendaftaran\components\Lookup;
use app\modules\pendaftaran\models\InfPencarianPasienForm;

class InformasiPasienIndexProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $_titleInfo = "Informasi Pasien";
        $jenis = $request->get('jenis', null);
        $getWorkspace = (new Lookup)->getValueFromLookupT(null, 'workspace_pendaftaran');
        if(empty($getWorkspace)) {
            Yii::error('workspace_pendaftaran pada tabel lookup transaksi belum tersedia!!', 'Mappingan Data');
            throw new \yii\web\HttpException(500);
        }
        $this->setWorkspaceByJenis($controller, $jenis, $getWorkspace);

        $active_workspace = $session->get('active_workspace');
        $controller->setPasienEditLinkBefore('/pendaftaran');
            if(isset($active_workspace['ruangan_id'])){
                $visible = false;
                $visible_mcu = true;
                foreach($getWorkspace as $k => $v) {
                    if($v['kode_id'] == $active_workspace['ruangan_id']) {
                        $jenis = isset($v['additional_value']) ? $v['additional_value'] : 'rajal';
                        $title = isset($v['kode_nama']) ? Yii::t('fe', $v['kode_nama']) : Yii::t('fe', 'Rawat jalan');
                        $singkatan = isset($v['kode_singkatan']) ? $v['kode_singkatan'] : DocoConstants::SINGKATAN_RJ;
                        if($singkatan == DocoConstants::SINGKATAN_RI) {
                            $visible = true;
                        } else if ($singkatan == DocoConstants::SINGKATAN_MCU) {
                            $visible_mcu = false;
                        }
                        break;
                    } else {
                        $singkatan =  DocoConstants::SINGKATAN_RJ;
                        $jenis = 'rajal';
                        $title = Yii::t('fe', 'Rawat jalan');
                    }
                }
            } else {
                $singkatan =  DocoConstants::SINGKATAN_RJ;
                $title = Yii::t('fe', 'Rawat jalan');
            }


            $title = Yii::t('fe',$_titleInfo).' '.$title;
            $rest = Yii::$app->docoRest->pendaftaran->post('allow/pack-informasi-pasien', [
                'form_params' => [
                    'singkatan' => $singkatan
                ]
            ]);
            $result = json_decode($rest->getBody(), true);
            $result = $result['response'];
            $carabayarList = isset($result['carabayar']) ? $result['carabayar'] : [];
            $ruanganList = isset($result['ruangan']['data']) ? ArrayHelper::map($result['ruangan']['data'], 'ruangan_id', 'ruangan_nama') : [];
            $statusList = isset($result['apawehatuhlah']) ? $result['apawehatuhlah'] : [];
            $listCaraBayar = isset($result['listCaraBayar']) ? $result['listCaraBayar'] : [];
            $statusPeriksaList = isset($result['listStatusPeriksa']) ? $result['listStatusPeriksa'] : [];

            // Request penjamin
            $request = Yii::$app->docoRest->master->get('penjamin/list-penjamin');
            $body = json_decode($request->getBody(), TRUE);
            $penjamin = $body['response'];

            return $controller->render('index', get_defined_vars());
    }

    protected function setWorkspaceByJenis($controller, $jenis = null, $getWorkspace)
    {
        if(!empty($jenis)) {
            foreach($getWorkspace as $k => $v) {
                if ($v['additional_value'] == $jenis) {
                    $controller::jumpRuanganWithoutSetMethod([
                        'roomID' => $v['kode_id']
                    ]);
                }
            }
        }
    }
}