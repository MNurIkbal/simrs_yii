<?php

namespace Doco\Traits;
use Yii;
use Doco\models\master\PlafonBpjs;

trait AutoPlafonTrait
{
    /**
     * Mengisi plafon pasien BPJS secara otomatis saat pendaftaran.
     * @param int $instalasiId ID instalasi yang dipilih saat pendaftaran
     * @param int $kelasPelayananId ID kelas pelayanan pasien
     * @param int $ruanganId ID ruangan pasien
     * @return int plafon yang di-set (0 jika tidak ditemukan)
     */
    public function setAutoPlafon($instalasiId, $kelasPelayananId, $ruanganId)
    {
        $records = PlafonBpjs::find()
            ->where([
                'instalasi_id' => $instalasiId,
                'kelaspelayanan_id' => $kelasPelayananId,
                'is_deleted' => false,
                'is_active' => true,
            ])
            ->all();

        if (!$records) {
            return 0;
        }

        $plafonDefault = 0;

        foreach ($records as $record) {
            $plafon = ($record->plafon ? $record->plafon : 0);
            $plafonRuangan = $this->extractPlafonRuangan($record);
            $listRuanganIds = $this->parseListRuangan($record->list_ruangan_id);

            if ($this->ruanganMatch($ruanganId, $listRuanganIds)) {
                return $plafonRuangan ? $plafonRuangan : $plafon;
            }

            $plafonDefault = $plafon;
        }

        return $plafonDefault;
    }

    private function extractPlafonRuangan($record)
    {
        if (empty($record->additional_data)) {
            return 0;
        }

        $additionalData = json_decode($record->additional_data, true);
        return isset($additionalData['plafon_ruangan']) ? $additionalData['plafon_ruangan'] : 0;
    }

    private function parseListRuangan($listRuanganId)
    {
        if (empty($listRuanganId)) {
            return [];
        }

        return array_map('trim', explode(',', $listRuanganId));
    }

    private function ruanganMatch($ruanganId, $listRuanganIds)
    {
        if (empty($ruanganId) || empty($listRuanganIds)) {
            return false;
        }

        return in_array($ruanganId, $listRuanganIds);
    }

}