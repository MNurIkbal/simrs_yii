<?php

namespace Doco\models\Bedah;

class MappingPosisiOperasi extends \Doco\components\DocoActiveRecord
{
    /**
     * Retrieve table name
     * 
     * @return String
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function tableName()
    {
        return 'tindakanoperasi_mp';
    }

    /**
     * Retrieve rules of table
     * 
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function rules()
    {
        return [
            [['daftartindakan_id', 'timoperasi_id'], 'required'],
            [['created_date', 'last_modified_date', 'deleted_date', 'daftartindakan_id', 'timoperasi_id', 'is_active'], 'safe'],
        ];
    }

    /**
     * Retrieve label attribute
     * 
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function attributeLabels()
    {
        return [
            'daftartindakan_id' => 'Daftar Tindakan',
            'timoperasi_id' => 'Nama Tim Operasi',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }

    /**
     * This function to create or update utd questions
     * 
     * @param Array $payload
     * @return Array => ['result' => Boolean, 'message' => String]
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function createOrUpdate($payload, $daftarTindakanId = null, $timOperasiId = null)
    {
        // checking if its update or create
        $isUpdate = !empty($timOperasiId) && !empty($daftarTindakanId);
        $record = [];
        if ($isUpdate) {
            $record = self::find(true)->andWhere(['daftartindakan_id' => $daftarTindakanId, 'timoperasi_id' => $timOperasiId])->select(['daftartindakan_id', 'timoperasi_id', 'is_active'])->one();
        }
        // checking record if update
        if ($isUpdate && empty($record)) {
            return [
                'result' => false,
                'message' => 'Data tidak ditemukan'
            ];
        }
        if (($isUpdate && $payload['timoperasi_id'] != $timOperasiId) || !$isUpdate) {
            $existingTeam = self::find(true)->select(['timoperasi_id'])->andWhere(['timoperasi_id' => $payload['timoperasi_id']]);

            if ($isUpdate && $payload['timoperasi_id'] != $timOperasiId) {
                $existingTeam->andWhere(['!=', 'timoperasi_id', $timOperasiId]);
            }

            $existingTeam = $existingTeam->asArray()->one();
            if (!empty($existingTeam)) {
                return [
                    'result' => false,
                    'message' => 'Posisi operasi telah termapping, silakan cek kembali posisi operasi.'
                ];
            }
        }

        if (!$isUpdate) {
            $record = new MappingPosisiOperasi;
        }
        $record->daftartindakan_id = $payload['daftartindakan_id'];
        $record->timoperasi_id = $payload['timoperasi_id'];
        $record->is_active = isset($payload['is_active']) && !empty($payload['is_active']) && $payload['is_active'] != '0';
        $record->save();
        return [
            'result' => true
        ];
    }
}
