<?php

namespace app\modules\master\models;

use app\components\DocoHelpers;
use Yii;
use app\components\DocoBaseModel;

/**
 *
 * @property integer $penjamin_id
 * @property integer $carabayar_id
 * @property string $penjamin_nama
 * @property string $penjamin_namalainnya
 * @property string $groupmargin_id
 * @property string $alamat_penjamin
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class PenjaminForm extends DocoBaseModel
{
    public $carabayar_id;
    public $penjamin_nama;
    public $penjamin_namalainnya;
    public $groupmargin_id;
    public $is_active;
    public $type_method;
    public $is_online;
    public $penjamin_kode;
    public $konfigasuransi_id;

    /**
     * @inheritdoc
     */
    protected $xssProtected = [
        'penjamin_nama',
        // 'penjamin_namalainnya',
        'groupmargin_id'
    ];

    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [['carabayar_id', 'groupmargin_id', 'penjamin_kode'], 'required'],
            [['penjamin_nama'], 'checkValidatePenjaminNama', 'on' => 'create'],
            [['penjamin_nama'], 'required', 'on' => 'edit'],
            [['penjamin_namalainnya'], 'safe', 'on' => 'edit'],
            [['penjamin_nama', 'penjamin_kode'], 'checkUnique', 'on' => 'create'],
            [['carabayar_id'], 'integer'],
            [['groupmargin_id', 'penjamin_namalainnya', 'konfigasuransi_id'], 'safe'],
            [['penjamin_nama'], 'string', 'max' => 70, 'on' => 'edit'],
            [['penjamin_namalainnya'], 'string', 'max' => 70, 'on' => 'edit'],
            [['penjamin_namalainnya'], 'checkValidatePenjaminNamaLain', 'on' => 'create'],
            [['penjamin_nama', 'groupmargin_id'], 'default', 'value' => null],
            [['is_active', 'is_online'], 'boolean']
        ];
    }

    public function checkValidatePenjaminNama($attribute, $params)
    {
        // validate string array penjamin_nama

        foreach ($this->$attribute as $key => $value) {
            if (strlen($value) > 70) {
                DocoHelpers::multipleParseError($this, 'Nama melebihi 70 karakter', 'penjamin_nama', $key);
            }
            if (strpos(substr($value, 0, 1), ' ') !== FALSE) {
                DocoHelpers::multipleParseError($this, 'Nama mengandung spasi di awal kata', 'penjamin_nama', $key);
            }
            if (preg_match("/'/", $value) == 1) {
                DocoHelpers::multipleParseError($this, 'Nama mengandung kutip', 'penjamin_nama', $key);
            }
            if (empty($value)) {
                DocoHelpers::multipleParseError($this, 'Data ini harus di isi.', 'penjamin_nama', $key);
            }
        }
    }

    public function checkValidatePenjaminNamaLain($attribute, $params)
    {
        // validate string array penjamin_namalainnya

        foreach ($this->$attribute as $key => $value) {
            if (strlen($value) > 70) {
                DocoHelpers::multipleParseError($this, 'Nama lain melebihi 70 karakter', 'penjamin_nama', $key);
            }
            if (strpos(substr($value, 0, 1), ' ') !== FALSE) {
                DocoHelpers::multipleParseError($this, 'Nama lain mengandung spasi di awal kata', 'penjamin_namalainnya', $key);
            }
            if (preg_match("/'/", $value) == 1) {
                DocoHelpers::multipleParseError($this, 'Nama lain mengandung kutip', 'penjamin_namalainnya', $key);
            }
        }
    }

    public function checkUnique($attribute, $params)
    {
        $penjamin_nama = $this->penjamin_nama;
        $penjamin_kode = $this->penjamin_kode;
        foreach ($this->penjamin_nama as $key => $value) {
            $search = preg_grep("/^" . $this->penjamin_nama[$key] . "$/i", $penjamin_nama);
            if (count($search) > 1) {
                DocoHelpers::multipleParseError($this, 'Nama penjamin tidak boleh sama', 'penjamin_nama', $key);
                return false;
            }
        }
        foreach ($this->penjamin_kode as $key => $value) {
            $search = preg_grep("/^" . $this->penjamin_kode[$key] . "$/i", $penjamin_kode);
            if (count($search) > 1) {
                DocoHelpers::multipleParseError($this, 'Kode penjamin tidak boleh sama', 'penjamin_kode', $key);
                return false;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'carabayar_id'         => \Yii::t('fe', 'Cara Bayar'),
            'penjamin_nama'        => Yii::t('app', 'Nama penjamin'),
            'penjamin_kode'        => Yii::t('app', 'Kode penjamin'),
            'penjamin_namalainnya' => Yii::t('app', 'Nama lainnya'),
            'groupmargin_id'       => Yii::t('app', 'Group Margin'),
            'is_active'            => \Yii::t('fe', 'Status'),
        ];
    }
}
