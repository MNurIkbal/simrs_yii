<?php 
namespace Doco\models\rsOnline;

use Yii;
use app\modules\v1\models\Lookup;
use Doco\components\DocoConstants;
use Doco\Services\ApiBPJSLZString;
use Doco\components\RsonlineLog;
use Doco\models\KlasifikasiKamarV;
use Doco\models\KamarTempatTidur;
use Doco\models\TempatTidurView; 
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstansId;
use Doco\models\KetersediaanKamarFnDet; 

class RsOnlineHitWs extends RsOnline
{
    // protected $type;
    // protected $kamarruangan_id;
    // protected $ruangan_id;
    // protected $klasifikasikamar_id;
    // protected $oldKlasifikasikamarId;

    // public function __construct($id, $type, array $params)
    // {
    //     $this->kamarruangan_id = $id;
    //     $this->type = $type;
    //     $this->ruangan_id = ArrayHelper::getValue($params, 'ruangan_id');
    //     $this->klasifikasikamar_id = ArrayHelper::getValue($params, 'klasifikasikamar_id');
    //     $this->oldKlasifikasikamarId = ArrayHelper::getValue($params, 'oldKlasifikasikamarId');
    // }

    // public function execute()
    // {
    //     if ($this->type == DocoConstants::TYPE_CREATE_APLICARE) {
            
    //     } else {

    //     }
    // }

    // protected function wsBtnTambah()
    // {
    //     /** sudah pernah di mapping */
    //     if (!empty($this->oldKlasifikasikamarId) && $this->oldKlasifikasikamarId != $this->klasifikasikamar_id) {
    //         /** update dengan old klasifikasi  */
    //         $klasifikasiKamar = KlasifikasiKamarV::find()->where([
    //             'ruangan_id' => $this->ruangan_id, 'klasifikasikamar_id' => $this->oldKlasifikasikamarId,
    //         ])->asArray()->all();
    //         /** delete jika kodeKlas rs sudah tidak ada di mappingan */
    //         if (empty($klasifikasiKamar)) {
    //             $this->deleteFasyankes($this->kamarruangan_id);
    //         } else {
    //             $updateRs = $this->postFasyankes($id, DocoConstants::TYPE_UPDATE_APLICARE, $ruangan_id, $oldKlasifikasikamarId);
    //         }
    //     }

    //     // return $this->setPayload($id, $ruangan_id, $klasifikasikamar_id);
    //     $existingData = $this->existingData($this->kamarruangan_id);
    //     $type = $existingData ? DocoConstants::TYPE_UPDATE_APLICARE : DocoConstants::TYPE_CREATE_APLICARE;
    //     $post = $this->postFasyankes($id, $type, $ruangan_id, $klasifikasikamar_id);
    //     $id_t_tt = $this->existingDataAndGetIdTTt($id);
    //     if ($id_t_tt) {
    //         KamarRuangan::updateAll(['id_t_tt_rsonline' => $id_t_tt],
    //             ['and', 
    //                 ['ruangan_id' => $ruangan_id],
    //                 ['klasifikasikamar_id' =>  $klasifikasikamar_id]
    //             ]
    //         );
    //     }
    // }
}