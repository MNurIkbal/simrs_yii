<?php

/**
 * @author Chacha Nurholis <chacha@sirs.co.id>
 * @method Update Status Program By Program Terapi ID <Service Internal>
 */

namespace Integrasi\Service\Fisioterapi;

use Yii;
use Exception;
use Integrasi\Contracts\DocoImplement;
use Integrasi\Service\Fisioterapi\Models\InfoProgramFisioterapiView;
use Integrasi\Service\Fisioterapi\Models\ProgramTerapi;
use yii\helpers\ArrayHelper;

class UpdateStatusProgramCloseByProgramTerapiId extends DocoImplement
{
    /**
     * @method execute
     * @param Integer $status_program_id (lookup_id)
     * @param Integer $programterapi_id (programterapi_id)
     */
    public function execute()
    {
        // Get Payload
        $statusProgramId = $this->status_program_id;
        $programTerapiId = $this->programterapi_id;

        // Validate Payload
        if (empty($programTerapiId) || empty($statusProgramId)) {
            throw new Exception("Payload Tidak Sesuai");
        }

        // Get Program Terapi
        $programPasien = ProgramTerapi::find()
            ->select('sisa')
            ->where(['programterapi_id' => $programTerapiId])
            ->one();

        // Check Program
        if (empty($programPasien)) {
            throw new Exception("Program Tidak Ditemukan");
        }

        // Get Sisa
        $sisa = ArrayHelper::getValue($programPasien, 'sisa');

        // Check Sisa
        if ($sisa == 0) {
            // Update Status Program Jika Sisa Frekuensi Sudah Habis
            return $this->setStatusProgramFisioByProgramTerapiId($programTerapiId, $statusProgramId);
        }
    }

    /**
     * @method setStatusProgramFisioByProgramTerapiId
     * @param Integer $programTerapiId
     * @param Integer $statusProgramId
     */
    private function setStatusProgramFisioByProgramTerapiId($programTerapiId, $statusProgramId)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            // Get Program Terapi
            $programTerapi = ProgramTerapi::find()
                ->where(['programterapi_id' => $programTerapiId])
                ->one();

            // Update Status Program
            $programTerapi->status_program_fisio = $statusProgramId;
            $programTerapi->save();
            
            $transaction->commit();

            return json_encode([
                'service' => 'Fisioterapi-UpdateStatusProgramCloseByProgramTerapiId',
                'payload' => $this->attributes,
                'timestamp' => date('Y-m-d H:i:s'),
                'response' => 'Status Program Berhasil Diubah'
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
