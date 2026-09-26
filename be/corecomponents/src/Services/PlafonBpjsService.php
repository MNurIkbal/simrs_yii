<?php

/**
 * @author : Budi (budi.sirs@gmail.com)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\Services;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\Services\BaseService;

class PlafonBpjsService extends BaseService
{
    protected $pendaftaranId;
    protected $totalTarif;

    public function __construct($pendaftaranId, $totalTarif = 0)
    {
        $this->pendaftaranId = $pendaftaranId;
        $this->totalTarif = $totalTarif;
    }

    public function validasiPlafon()
    {
        $dataPendaftaran = $this->validPendaftaran($this->pendaftaranId);
        $limitTagihan = ArrayHelper::getValue($dataPendaftaran, 'limit_tagihan', 0);
        $isValid = true;
        $tagihanSaatIni = 0;
        if($limitTagihan > 0) {
            $groupCaraBayarId = ArrayHelper::getValue($dataPendaftaran, 'group_carabayar');
            $isBpjs = $groupCaraBayarId === DocoConstants::GROUP_BPJS;
            if($isBpjs) {
                $tagihanSaatIni = $this->tagihanPasien($this->pendaftaranId) + $this->totalTarif;
                if($tagihanSaatIni > $limitTagihan) {
                    $isValid = false;
                }
            }
        }
        if(!$isValid) {
            $errorMessage = 'Tagihan Sementara lebih besar daripada Plafon BPJS Pasien';
            return [
                'isValid' => $isValid,
                'message' => $errorMessage,
            ];
        }

        return [
            'isValid' => $isValid,
            'message' => 'Valid',
        ];
    }

    protected function validPendaftaran($pendaftaranId)
    {
        return Yii::$app->db->createCommand("
            SELECT cm.groupcarabayar_id as group_carabayar, t.limit_tagihan
            from  pendaftaran_t t
            join carabayar_m cm on t.carabayar_id = cm.carabayar_id
            where t.pendaftaran_id = :pendaftaran_id
            and t.pasienadmisi_id is null
        ")->bindValue(':pendaftaran_id', $pendaftaranId)->queryOne();
    }

    protected function tagihanPasien($pendaftaranId)
    {
        $query = "
            SELECT
                pendaftaran_id,
                (COALESCE(cek_tagihan.total_tindakan, 0::numeric) + COALESCE(cek_tagihan.total_obat, 0::numeric))::double precision AS total_tagihan
            FROM (
            SELECT
                pendaftaran_id,
                CASE
                        WHEN (EXISTS ( SELECT 1
                            FROM tindakanpelayanan_t
                            WHERE tindakanpelayanan_t.is_deleted IS FALSE AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                        LIMIT 1)) THEN ( SELECT COALESCE(round(sum(a.tarif_tindakan)::numeric, 2), 0::numeric) AS tarif_tindakan
                            FROM tindakanpelayanan_t a
                            WHERE a.is_deleted IS FALSE AND a.tindakansudahbayar_id IS NULL AND a.pendaftaran_id IS NOT NULL AND pendaftaran_t.pendaftaran_id = a.pendaftaran_id)
                        ELSE 0::numeric
                END AS total_tindakan,
                CASE
                        WHEN (EXISTS ( SELECT 1
                            FROM obatalkespasien_t
                            WHERE obatalkespasien_t.is_deleted IS FALSE AND obatalkespasien_t.obatsudahbayar_id IS NULL AND pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
                        LIMIT 1)) THEN ( SELECT COALESCE(round(sum(a.hargajual_oa)::numeric, 2), 0::numeric) AS coalesce
                            FROM obatalkespasien_t a
                            WHERE a.is_deleted IS FALSE AND a.obatsudahbayar_id IS NULL AND a.pendaftaran_id IS NOT NULL AND pendaftaran_t.pendaftaran_id = a.pendaftaran_id)
                        ELSE 0::numeric
                END AS total_obat
            FROM pendaftaran_t
            ) AS cek_tagihan
            WHERE pendaftaran_id = :pendaftaran_id";
        $command = Yii::$app->db->createCommand($query)->bindValue(':pendaftaran_id', $pendaftaranId)->queryOne();
        return ArrayHelper::getValue($command, 'total_tagihan', 0);
    }
}
