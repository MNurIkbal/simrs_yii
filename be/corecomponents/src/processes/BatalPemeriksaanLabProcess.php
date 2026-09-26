<?php
/**
 * @author : Dede Herdiana
 * A product of PT. Citraraya Nuasatama
 * Powered by Sirs
 */
 
namespace Doco\processes;
use Yii;
use Doco\exceptions\ValidationException;
use Doco\components\DocoMessages;
use Doco\Services\KasirService;
use app\modules\v1\models\InfoPasienLabDetailView;
use app\modules\v1\models\RekapCancelWyn;
use app\modules\v1\models\PasienMasukPenunjangT;

class BatalPemeriksaanLabProcess extends \Doco\components\DocoBaseProcessExtension
{
    protected $detail_tindakan;
    protected $new_detail_tindakan;

    protected function processFlow(){
        $detail_tindakan = $this->_requestData->post('detail_tindakan', []);
        return [
            'status' => true,
            'fail_tindakan' => '',
            'new_detail_tindakan' => $this->detail_tindakan
        ];
    }
}