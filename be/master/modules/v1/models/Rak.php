<?php

namespace app\modules\v1\models;

/**
 * This is the model class for table "manufaktur_m".
 *
 * @property int $rakobat_id
 * @property string $rakobat_nama
 * @property int $ruangan_id
 * @property int $parentrakobat_id
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */

class Rak extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'rakobat_m';
    }

    public function rules(){
        
        return [
            [
                ['rakobat_nama', 'ruangan_id', 'parentrakobat_id', 'additional_data', 'created_by', 'modified_count', 'last_modified_by', 'deleted_date', 'deleted_by'],
                'default', 
                'value' => null
            ],
            [
                ['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 
                'integer'
            ],
            [
                ['created_date', 'last_modified_date', 'deleted_date'], 
                'safe'
            ],
            [
                ['is_deleted', 'is_active'], 
                'boolean'
            ],
            [
                ['rakobat_nama'], 'string', 'max' => 255
            ],
            [
                ['rakobat_nama'],
                'required'
            ],
            [
                ['rakobat_nama'],
                'uniqueNama'
            ],
        ];
    }

    public function uniqueNama($attribute, $params) 
    {
        $message = null;
        $nama = $this->rakobat_nama;
        $query = Rak::find()->where([
            'LOWER (rakobat_nama)' => strtolower($nama)
        ]);
        if (!empty($this->ruangan_id) && !empty($this->parentrakobat_id)) {
            $query->andWhere(['ruangan_id' => $this->ruangan_id]);
            $query->andWhere(['parentrakobat_id' => $this->parentrakobat_id]);
            if (!empty($query->one())) {
                $message = 'Rak Obat Nama Sudah ada.';
            } else {
                $query = Rak::find()->where([
                    'LOWER (rakobat_nama)' => strtolower($nama)
                ]);
                $query->andWhere(['ruangan_id' => $this->ruangan_id]);
                $query->andWhere(['is', 'parentrakobat_id', null]);
                if (!empty($query)) {
                    $message = 'Rak Obat Nama Sudah ada.';
                } else {
                    $query = Rak::find()->where([
                        'LOWER (rakobat_nama)' => strtolower($nama)
                    ]);
                    $query->andWhere(['ruangan_id' => $this->ruangan_id]);
                    $query->andWhere(['rakobat_id' => $this->parentrakobat_id]);
                    $message = 'Rak Obat Nama Sudah ada.';
                }
            }
        } else {
            if (!empty($this->ruangan_id)) {
                $query->andWhere(['ruangan_id' => $this->ruangan_id]);
                $message = 'Rak Obat Nama Sudah ada.';
            }
        }
        $result = $query->one();
        if (!empty($result) && !empty($message)) {
            if ($this->rakobat_id != $result->rakobat_id) {
                $this->addError('rakobat_nama', $message);
                return false;
            }
        }
        return true;

    }
}

?>