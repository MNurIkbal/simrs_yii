<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-04-18 13:30:04
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-11-29 10:32:46
 * @Description:
 */

namespace Doco\apotek\models;

use Yii;

class StokOpnameForm extends \yii\base\Model
{
    public $stokopname_id;
    public $ruangan_id;
    public $formulirstokopname_id;
    public $tglstokopname;
    public $nostokopname;
    public $is_stokawal;
    public $jenisstokopname;
    public $keterangan_opname;
    public $totalharga_fisik;
    public $totalharga_sistem;
    public $mengetahui_id;
    public $petugas1_id;
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

    public $detail;
    public $inputan_so;
    public $list_obat_baru;
    // additional attributes
    public $selisihharga;
    public $formstokopname_id;
    public $totalstok_sistem;
    public $totalstok_fisik;
    public $totalstok_selisih;

    // public static function tableName()
    // {
    //     return 'stokopname_t';
    // }

    public function rules()
    {
        return [
            [['inputan_so'], 'required', 'message' => '{attribute}' . \Yii::t('fe', 'Tidak Boleh Kosong!')],
            [['ruangan_id', 'formulirstokopname_id', 'mengetahui_id', 'petugas1_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'formulirstokopname_id', 'mengetahui_id', 'petugas1_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglstokopname', 'ruangan_id','detail', 'list_obat_baru', 'created_date', 'last_modified_date', 'deleted_date','inputan_so'], 'safe'],
            [['nostokopname', 'jenisstokopname', 'keterangan_opname', 'additional_data'], 'string'],
            [['is_stokawal', 'is_deleted', 'is_active'], 'boolean'],
            [['totalharga_fisik', 'totalharga_sistem'], 'number'],
            [['nostokopname'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stokopname_id' => 'Stokopname ID',
            'ruangan_id' => 'Ruangan ID',
            'formulirstokopname_id' => 'Formulirstokopname ID',
            'tglstokopname' => 'Tglstokopname',
            'nostokopname' => 'Nostokopname',
            'is_stokawal' => 'Is Stokawal',
            'jenisstokopname' => \Yii::t('fe','Jenis Stok Opname'),
            'keterangan_opname' => \Yii::t('fe', 'Keterangan Opname'),
            'totalharga_fisik' => \Yii::t('fe', 'Total Harga Fisik'),
            'totalharga_sistem' => \Yii::t('fe', 'Total Harga Sistem'),
            'selisihharga' => \Yii::t('fe', 'Selisih Harga'),
            'mengetahui_id' => 'Mengetahui ID',
            'petugas1_id' => 'Petugas1 ID',
            'totalstok_fisik' => 'Total Stok Fisik',
            'totalstok_sistem' => 'Total Stok Sistem',
            'totalstok_selisih' => 'Total Stok Selisih',
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

    public function validInputSo($attribute, $params)
    {
        $request = Yii::$app->request;
        $inputan_so = $request->post('inputan_so');
        if (!empty($inputan_so) && is_array($inputan_so)) {
            foreach ($inputan_so as $value) {
                $data = json_decode($value,true);
                if (empty($data['stok_fisik'])) {
                    $this->addError('inputan_so',Yii::t('fe','Stok Fisik tidak boleh kosong'));
                    return false;
                }

                if (empty($data['kondisi'])) {
                    $this->addError('inputan_so',Yii::t('fe','Kondisi tidak boleh kosong'));
                    return false;
                }
            }
        }
        return true;
    }
}
