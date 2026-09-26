<?php

namespace Doco\models;

use Yii;

class Cathlab extends \Doco\components\DocoActiveRecord
{
    /**
     * @var Array $types
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public static $types = ['koroangiografi', 'pci', 'dsa'];

    /**
     * @var Array $generalFields
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public $generalFields = [
        'indikasi',
        'approach',
        'kesimpulan',
        'saran',
        'operator_id',
        'tgl_prosedure'
    ];

    /**
     * @var Array $koroangiografiFields
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public $koroangiografiFields = [
        'koroner',
        'lm',
        'lad',
        'lcx',
        'rca',
        'lain_lain',
        'cum_air_kerma',
        'cum_dap',
        'kontras',
        'fluo_time',
        'procedure_time'
    ];

    /**
     * @var Array $pciFields
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public $pciFields = [
        'indikasi',
        'approach',
        'target',
        'lm',
        'lad',
        'lcx',
        'rca',
        'laporan_pci',
        'cum_air_kerma',
        'cum_dap',
        'kontras',
        'fluo_time',
        'procedure_time',
    ];

    /**
     * @var Array $dsaFields
     * @author Tsani Nashrullah (tsani@docotel.com)
     */
    public $dsaFields = [
        'indikasi',
        'approach',
        'laporan_dsa',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cathlab_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'cathlab_id',
                    'pendaftaran_id',
                    'pasienadmisi_id',
                    'tipe',
                    'tgl_cathlab',
                    'indikasi',
                    'approach',
                    'target',
                    'koroner',
                    'lm',
                    'lad',
                    'lcx',
                    'rca',
                    'laporan_pci',
                    'laporan_dsa',
                    'lain_lain',
                    'kesimpulan',
                    'saran',
                    'cum_air_kerma',
                    'cum_dap',
                    'fluo_time',
                    'kontras',
                    'procedure_time',
                    'operator_id',
                    'tgl_prosedure',
                    'additional_data',
                    'created_date',
                    'created_by',
                    'modified_count',
                    'last_modified_date',
                    'last_modified_by',
                    'is_deleted',
                    'is_active',
                    'deleted_date',
                    'deleted_by'
                ],
                'safe'
            ],
            [
                [
                    'cum_air_kerma',
                    'cum_dap',
                    'fluo_time',
                    'kontras',
                    'procedure_time',
                ], 
                'string', 'max' => 50
            ],
        ];
    }

    /**
     * This function will mapping data to type
     * 
     * @param Array $payload
     * @return Class
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function mapPayload($payload)
    {
        $additionalFields = [];
        $fields = $this->fieldsByType($this->tipe);
        $this->tgl_cathlab = date("Y-m-d H:i:s");
        foreach ($fields as $field) {
            if (isset($payload[$field])) {
                $this[$field] = $field == 'tgl_prosedur' ? date("Y-m-d H:i:s", strtotime($payload[$field])) : $payload[$field];
            }
        }
        return $this;
    }

    /**
     * this function get all fields by it type
     * 
     * @param String $type
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function fieldsByType($type = null)
    {
        $thisClass = new self;
        if (!empty($type)) {
            $thisClass->tipe = $type;
        }
        switch ($thisClass->tipe) {
            case 'koroangiografi':
                $additionalFields = $thisClass->koroangiografiFields;
                break;
            case 'pci':
                $additionalFields = $thisClass->pciFields;
                break;
            case 'dsa':
                $additionalFields = $thisClass->dsaFields;
                break;
        }
        return array_merge($thisClass->generalFields, $additionalFields);
    }

    /**
     * This function will return data cathlab by type and by it fields
     * 
     * @param String $type
     * @param String $key
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function existingRecordOf($type, $key)
    {
        $clause = [];
        if (isset($key['pasienadmisi_id'])) {
            $clause = ['pasienadmisi_id' => $key['pasienadmisi_id']];
        } else if (isset($key['pendaftaran_id']) || is_string($key)) {
            $clause = ['pendaftaran_id' => $key['pendaftaran_id'], 'pasienadmisi_id' => null];
        }
        if (!empty($clause) && in_array($type, Cathlab::$types)) {
            $fields = self::fieldsByType($type);
            return self::find()
                ->select(array_merge(['cathlab_id', 'pendaftaran_id', 'pasienadmisi_id', 'tgl_cathlab', 'pegawai_m.nama_pegawai as operator_nama'], $fields))
                ->leftJoin('pegawai_m','pegawai_m.pegawai_id = cathlab_t.operator_id')
                ->andWhere($clause)
                ->andWhere([
                    'tipe' => $type
                ])
                ->asArray()
                ->one();
        } else {
            return [];
        }
    }
}
