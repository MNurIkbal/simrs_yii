<?php

namespace Integrasi\Service\Fisioterapi;

use Yii;
use Exception;
use Doco\Repositories\LookUpTransaksiRepositories;
use Integrasi\Service\Fisioterapi\Models\Pendaftaran;
use Integrasi\Service\Fisioterapi\Models\ProgramTerapi;

class UpdateStatusProgramClose extends \Integrasi\Contracts\DocoImplement
{
    const DIRAWAT = 5;

    public function execute()
    {
        $lookUpTransaksi = new LookUpTransaksiRepositories;
        $statusCloseProgramFisio = $lookUpTransaksi->getStatusCloseFisio();
        $statusOpenProgramFisio = $lookUpTransaksi->getStatusOpenProgramFisio();
        $instalasiRawatJalanId = $lookUpTransaksi->getInstalasiIdRj();
        $instalasiRawatInapId = $lookUpTransaksi->getInstalasiIdRi();
        $pasienId = $this->pasien_id;
        $pendaftaranId = $this->pendaftaran_id;
        $caraKeluarId = $this->carakeluar_id;
        // Validate Payload
        if (empty($pasienId) || empty($statusCloseProgramFisio) || empty($pendaftaranId)) {
            throw new Exception("Payload Tidak Sesuai");
        }
        // Validate Program Terapi
        $programPasien = ProgramTerapi::find()
            ->where(['pasien_id' => $pasienId])
            ->andWhere(['status_program_fisio' => "$statusOpenProgramFisio"])
            ->one();
        if (empty($programPasien)) {
            throw new Exception("Pasien Tidak Mempunyai Program");
        }
        // Get Pendaftaran By Pendaftaran ID
        $pendaftaran = Pendaftaran::find()
            ->where(['pendaftaran_id' => $pendaftaranId])
            ->orderBy(['tgl_pendaftaran' => SORT_DESC])
            ->one();
        if (empty($pendaftaran)) {
            throw new Exception("Pendaftaran Tidak Ditemukan");
        }
        // Instalasi From Pendaftaran
        $tipeInstalasi = $pendaftaran->instalasi_id;
        // Validate Instalasi Rawat Jalan
        if ($tipeInstalasi == $instalasiRawatJalanId) {
            if ($caraKeluarId != self::DIRAWAT) {
                throw new Exception("Selain Cara Keluar Dirawat Tidak Perlu Update Status Program Fisioterapi");
            }
            return $this->setStatusProgramFisioByPasienId($pasienId, $statusCloseProgramFisio);
        } else if ($tipeInstalasi == $instalasiRawatInapId)  {
            return $this->setStatusProgramFisioByPasienId($pasienId, $statusCloseProgramFisio);
        } else {
            throw new Exception("Hanya Instalasi Rawat Jalan & Rawat Inap");
        }
    }

    /**
     * @method setStatusProgramFisioByPasienId
     * @param Integer $pasienId
     * @param Integer $statusProgramId
     */
    private function setStatusProgramFisioByPasienId($pasienId, $statusProgramId)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            ProgramTerapi::updateAll(['status_program_fisio' => $statusProgramId], ['pasien_id' => $pasienId]);
            $transaction->commit();
            return json_encode([
                'service' => 'Fisioterapi-UpdateStatusProgramClose',
                'payload' => $this->attributes,
                'timestamp' => date('Y-m-d H:i:s'),
                'response' => 'Status Program Berhasil Diubah Menjadi Close'
            ]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e->getMessage();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e->getMessage();
        }
    }
}
