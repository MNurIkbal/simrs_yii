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

use Doco\models\pendaftaran\InfoJadwalDokterView;
use Doco\models\pendaftaran\JadwalCuti;
use Doco\Services\RegistrationService;

class PendaftaranOnlineGetDokterProcess extends \Doco\components\DocoBaseProcessExtension
{
    /** @var int prefix hari lookup*/
    public $hariId;

    public $id;

    public $ruanganId;

    public $tanggal;

    protected function processFlow()
    {
        $this->setParams();
        $dokter = $this->getJadwalDokter()->distinct()->all();

        $pegawaiCuti = (new RegistrationService)->getDokterCuti([
                            'id' => $this->ruanganId,
                            'data' => $dokter,
                            'tanggal' => $this->tanggal
                        ]);

        return $pegawaiCuti;

        if (!empty($pegawaiCuti)) {
            if (count($pegawaiCuti) == 0) {
                throw new ValidationException(422, $this->_error, [
                    'text' => 'Data dokter tidak ditemukan.'
                ]);
            } else {
                return $pegawaiCuti;
            }
        } else {
            throw new ValidationException(422, $this->_error, [
                'text' => 'Data dokter tidak ditemukan.'
            ]);
        }
    }

    /**
     * @return void
     */
    protected function setParams()
    {
        $this->hariId = $this->_requestData->get('hari_id', null);
        $this->ruanganId = $this->_requestData->get('ruangan_id', null);
        $this->tanggal = $this->_requestData->get('tanggal', null);
        if (is_null($this->hariId )) {
            $this->hariId  = DocoHelpers::getIdHariIni();
        }
        if (is_null($this->ruanganId )) {
            throw new ValidationException(422, $this->_error, [
                'text' => 'ruangan tidak ditemukan.'
            ]);
        }
    }

    protected function getJadwalDokter()
    {
        return InfoJadwalDokterView::find()->select([
            'pegawai_id', 'nama_pegawai', 'kuota_tersedia as kuota', 'jadwaldokter_id', 'waktu_mulai', 'waktu_selesai'
        ])->where([
            'is_active'       => true,
            'ruangan_id'      => $this->ruanganId,
            'hari_jadwalbuka' => $this->hariId
        ]);
    }
}
