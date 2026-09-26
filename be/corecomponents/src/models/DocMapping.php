<?php

namespace Doco\models;

use Yii;
use Doco\models\DocFooter;
use Doco\models\DocHeader;
use Doco\models\JenisKertas;

/**
 * This is the model class for table "docmapping_k".
 *
 * @property int $docmapping_id
 * @property int $docheader_id
 * @property string $docbody_text
 * @property int $docfooter_id
 * @property string $controller
 * @property string $doc_key
 * @property string $nama_doc
 * @property string $kode_doc
 * @property int $kertas_id
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property int $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class DocMapping extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    protected $xssProtected = [
        'kode_doc',
        'nama_doc'
    ];
    
    public static function tableName()
    {
        return 'docmapping_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['docbody_text'], 'required'],
            [[
                'docheader_id', 
                'docfooter_id'
            ], 'default', 'value' => null],
            [[
                'docmapping_id', 
                'docheader_id', 
                'docfooter_id'
            ], 'integer'],
            [['controller'], 'string'],
            [[
                'docbody_text',
                'kode_doc',
                'kertas_id',
                'additional_data',
                'created_date',
                'created_date',
                'created_by',
                'modified_count',
                'last_modified_date',
                'last_modified_by',
                'is_deleted',
                'is_active',
                'deleted_date',
                'deleted_by'
            ], 'safe'],
            [['doc_key', 'nama_doc'], 'string', 'max' => 255],
            [['docmapping_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'docmapping_id' => 'Docmapping ID',
            'docheader_id' => 'Docheader ID',
            'docfooter_id' => 'Docfooter ID',
            'controller' => 'Controller',
            'doc_key' => 'Doc Key',
            'nama_doc' => 'Nama Doc',
        ];
    }

    public function getFooter()
    {
        return $this->hasOne(DocFooter::className(),['docfooter_id' => 'docfooter_id']);
    }

    public function getHeader()
    {
        return $this->hasOne(\Doco\models\DocHeader::className(),['docheader_id' => 'docheader_id']);
    }

    public function getKertas()
    {
        return $this->hasOne(JenisKertas::className(),['kertas_id' => 'kertas_id']);
    }

    public function getReport()
    {
        return $this->hasOne(Report::className(),['docmapping_id'=>'docmapping_id']);
    }
}
