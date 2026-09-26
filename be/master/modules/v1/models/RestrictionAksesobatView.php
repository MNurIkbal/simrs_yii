<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "restriction_aksesobat_v".
 *
 * @property integer $restriction_obat_id
 * @property string $restriction_obat_nama
 * @property string $restriction_obat_namalainnya
 * @property string $instalasi_id (now aggregated as comma-separated string)
 * @property string $instalasi_nama (now aggregated as comma-separated string)
 * @property string $penjamin_id (now aggregated as comma-separated string)
 * @property string $penjamin_nama (now aggregated as comma-separated string)
 * @property string $carabayar_id
 * @property string $carabayar_nama
 * @property integer $jml
 * @property bool $is_active
 */
class RestrictionAksesobatView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'restriction_aksesobat_v';
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['restriction_obat_id'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['restriction_obat_id', 'restriction_obat_nama', 'restriction_obat_namalainnya', 'instalasi_id', 'instalasi_nama', 'penjamin_id', 'penjamin_nama', 'carabayar_id', 'carabayar_nama', 'jml'], 'default', 'value' => null],
            [['restriction_obat_id', 'jml'], 'integer'],
            // All these fields are now strings (comma-separated) from the view
            [['instalasi_id', 'instalasi_nama', 'penjamin_id', 'penjamin_nama'], 'string'],
            [['restriction_obat_nama', 'restriction_obat_namalainnya', 'carabayar_id', 'carabayar_nama'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'is_active'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'restriction_obat_id' => Yii::t('app', 'Restriction Obat ID'),
            'restriction_obat_nama' => Yii::t('app', 'Nama Restriction Obat'),
            'restriction_obat_namalainnya' => Yii::t('app', 'Nama Lain Restriction Obat'),
            'instalasi_id' => Yii::t('app', 'Instalasi ID'),
            'instalasi_nama' => Yii::t('app', 'Nama Instalasi'),
            'penjamin_id' => Yii::t('app', 'Penjamin ID'),
            'penjamin_nama' => Yii::t('app', 'Nama Penjamin'),
            'carabayar_id' => Yii::t('app', 'Cara Bayar ID'),
            'carabayar_nama' => Yii::t('app', 'Nama Cara Bayar'),
            'jml' => Yii::t('app', 'Jumlah'),
            'is_active' => Yii::t('app', 'Status'),
        ];
    }
    
    public static function getRestrictionAksesObat($instalasi_id = null) {
        $sql = "
        SELECT 
            restriction_obat_id,
            restriction_obat_nama,
            restriction_obat_namalainnya,
            instalasi_id,
            instalasi_nama,
            penjamin_id,
            penjamin_nama,
            carabayar_id,
            carabayar_nama,
            jml
        FROM restriction_aksesobat_v
        ";
        
        $params = [];
        if ($instalasi_id !== null) {
            // Handle comma-separated instalasi_id in the new view structure
            $sql .= " WHERE instalasi_id LIKE :instalasi_id OR instalasi_id LIKE :instalasi_id_start OR instalasi_id LIKE :instalasi_id_end OR instalasi_id LIKE :instalasi_id_middle";
            $params[':instalasi_id'] = $instalasi_id;
            $params[':instalasi_id_start'] = $instalasi_id . ',%';
            $params[':instalasi_id_end'] = '%,' . $instalasi_id;
            $params[':instalasi_id_middle'] = '%,' . $instalasi_id . ',%';
        }
        
        $sql .= " ORDER BY restriction_obat_nama";
        
        $list = Yii::$app->db->createCommand($sql, $params)->queryAll();
        return $list;
    }
    
    public static function getRestrictionObatWithCount() {
        $sql = "
        SELECT DISTINCT
            restriction_obat_id,
            restriction_obat_nama,
            restriction_obat_namalainnya,
            jml
        FROM restriction_aksesobat_v
        ORDER BY restriction_obat_nama
        ";
        $list = Yii::$app->db->createCommand($sql)->queryAll();
        return $list;
    }

    /**
     * Helper method untuk memformat data instalasi yang diagregasi
     */
    public function formatInstalasiDisplay($instalasiData)
    {
        return $instalasiData;
    }

    /**
     * Helper method untuk memformat data penjamin yang diagregasi
     */
    public function formatPenjaminDisplay($penjaminData)
    {
        return $penjaminData;
    }

    /**
     * Helper method untuk memisahkan string yang diagregasi menjadi array
     */
    public function parseAggregatedString($aggregatedString)
    {
        if (is_string($aggregatedString) && strpos($aggregatedString, ',') !== false) {
            return array_map('trim', explode(',', $aggregatedString));
        }
        
        return [$aggregatedString];
    }

    /**
     * Helper method untuk mendapatkan array instalasi ID dari string yang diagregasi
     */
    public function getInstalasiIdsArray()
    {
        return $this->parseAggregatedString($this->instalasi_id);
    }

    /**
     * Helper method untuk mendapatkan array penjamin ID dari string yang diagregasi
     */
    public function getPenjaminIdsArray()
    {
        return $this->parseAggregatedString($this->penjamin_id);
    }

    /**
     * Helper method untuk mendapatkan array instalasi nama dari string yang diagregasi
     */
    public function getInstalasiNamaArray()
    {
        return $this->parseAggregatedString($this->instalasi_nama);
    }

    /**
     * Helper method untuk mendapatkan array penjamin nama dari string yang diagregasi
     */
    public function getPenjaminNamaArray()
    {
        return $this->parseAggregatedString($this->penjamin_nama);
    }

    /**
     * Override method untuk mendapatkan data yang diformat untuk display
     */
    public function getFormattedData()
    {
        return [
            'restriction_obat_id' => $this->restriction_obat_id,
            'restriction_obat_nama' => $this->restriction_obat_nama,
            'restriction_obat_namalainnya' => $this->restriction_obat_namalainnya,
            'instalasi_id' => $this->instalasi_id,
            'instalasi_nama' => $this->formatInstalasiDisplay($this->instalasi_nama),
            'penjamin_id' => $this->penjamin_id,
            'penjamin_nama' => $this->formatPenjaminDisplay($this->penjamin_nama),
            'carabayar_id' => $this->carabayar_id,
            'carabayar_nama' => $this->carabayar_nama,
            'jml' => $this->jml,
            'instalasi_ids_array' => $this->getInstalasiIdsArray(),
            'penjamin_ids_array' => $this->getPenjaminIdsArray(),
            'instalasi_nama_array' => $this->getInstalasiNamaArray(),
            'penjamin_nama_array' => $this->getPenjaminNamaArray(),
        ];
    }

    /**
     * Method untuk mencari berdasarkan instalasi ID dengan struktur view baru
     */
    public static function findByInstalasiId($instalasiId)
    {
        return self::find()
            ->where(['OR',
                ['LIKE', 'instalasi_id', $instalasiId],
                ['LIKE', 'instalasi_id', $instalasiId . ',%'],
                ['LIKE', 'instalasi_id', '%,' . $instalasiId],
                ['LIKE', 'instalasi_id', '%,' . $instalasiId . ',%']
            ])
            ->all();
    }

    /**
     * Method untuk mencari berdasarkan penjamin ID dengan struktur view baru
     */
    public static function findByPenjaminId($penjaminId)
    {
        return self::find()
            ->where(['OR',
                ['LIKE', 'penjamin_id', $penjaminId],
                ['LIKE', 'penjamin_id', $penjaminId . ',%'],
                ['LIKE', 'penjamin_id', '%,' . $penjaminId],
                ['LIKE', 'penjamin_id', '%,' . $penjaminId . ',%']
            ])
            ->all();
    }

    /**
     * Method untuk mendapatkan data dengan format yang siap untuk frontend
     */
    public static function getFormattedList($instalasiId = null, $penjaminId = null)
    {
        $query = self::find();
        
        if ($instalasiId !== null) {
            $query->andWhere(['OR',
                ['LIKE', 'instalasi_id', $instalasiId],
                ['LIKE', 'instalasi_id', $instalasiId . ',%'],
                ['LIKE', 'instalasi_id', '%,' . $instalasiId],
                ['LIKE', 'instalasi_id', '%,' . $instalasiId . ',%']
            ]);
        }
        
        if ($penjaminId !== null) {
            $query->andWhere(['OR',
                ['LIKE', 'penjamin_id', $penjaminId],
                ['LIKE', 'penjamin_id', $penjaminId . ',%'],
                ['LIKE', 'penjamin_id', '%,' . $penjaminId],
                ['LIKE', 'penjamin_id', '%,' . $penjaminId . ',%']
            ]);
        }
        
        $models = $query->all();
        $formattedData = [];
        
        foreach ($models as $model) {
            $formattedData[] = $model->getFormattedData();
        }
        
        return $formattedData;
    }
}