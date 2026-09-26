<?php

/**
 * 
 * @author : Erlangga (librantara.erlangga@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

use Doco\models\Ruangan;
use Doco\models\JadwalPoliklinik;

class PendaftaranOnlineGetRuanganProcess extends \Doco\components\DocoBaseProcessExtension
{
    /** @var int prefix hari lookup*/
    public $hariId;

    protected function processFlow()
    {
        $this->setHariId();
        $ruangan = $this->getJadwalPoli()->all();      

        if (!empty($ruangan)) {
            $modelRuangan = $this->getRuangan();

            foreach ($ruangan as $key => $value) {
                $ruanganIds[] = $value['ruangan_id'];
            }
            $modelRuangan->andWhere(['ruangan_id' => $ruanganIds]);

            $ruangan = $modelRuangan->all();

            if (count($ruangan) == 0) {
                throw new ValidationException(422, $this->_error, [
                    'text' => 'Data ruangan tidak ditemukan.'
                ]);
            } else {
                return $ruangan;
            }
        } else {
            throw new ValidationException(422, $this->_error, [
                'text' => 'Data ruangan tidak ditemukan.'
            ]);
        }
    }

    /**
     * @return void
     */
    protected function setHariId()
    {
        $this->hariId = $this->_requestData->get('hari_id', null);
        if (is_null($this->hariId )) {
            $this->hariId  = DocoHelpers::getIdHariIni();
        }
    }

    protected function getJadwalPoli()
    {
        return JadwalPoliklinik::find()->where([
            'hari'      => $this->hariId ,
            'is_active' => true,
        ]);
    }

    protected function getRuangan()
    {
        return Ruangan::find()->where([
            'instalasi_id' => DocoConstants::VAR_I_RJ, //VAR_I_RJ = 1
            'is_active'    => true,
            'is_deleted'   => false,
        ]);
    }
}