<?php

namespace app\modules\master\models;

use Yii;

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

 class RakForm extends \yii\base\Model
 {
     // Public Property

    public $rakobat_id;
    public $rakobat_nama;
    public $ruangan_id;
    public $parentrakobat_id;
    public $additional_data;
	public $created_date;
	public $created_by;
	public $modified_count;
	public $last_modified_date;
	public $last_modified_by;
	public $is_deleted;
	public $is_active;
	public $deleted_date;
	public $deleted_by;

    	/**
	 * {@inheritdoc}
	 */
    protected $xssProtected = [
        'rakobat_nama',
    ];

    public function rules()
    {
        return [
            [
                ['ruangan_id', 'parentrakobat_id', 'additional_data', 'created_by', 'modified_count', 'last_modified_by', 'deleted_date', 'deleted_by'],
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
            ]
        ];
    }
    /**
	 * {@inheritdoc}
	 */

    public function attributeLabels()
    {
        return [
            'rakobat_id' => 'Rak Obat ID',
            'rakobat_nama' => Yii::t('fe','Rak Obat Nama'),
            'rakobat_id' => 'Rakobat ID',
            'ruangan_id' => 'Nama Ruangan',
            'parentrakobat_id' => 'Parent Rakobat ID',
        ];
    }
}

?>