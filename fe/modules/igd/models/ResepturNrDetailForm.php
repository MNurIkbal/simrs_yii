<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-12 11:56:02
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-19 16:48:02
 */

namespace app\modules\igd\models;

use Yii;

class ResepturNrDetailForm extends \yii\base\Model
{

    public $resepturdetail_id;
    public $obatalkes_id;
    public $racikan_id;
    public $satuankecil_id;
    public $sumberdana_id;
    public $reseptur_id;
    public $r;
    public $rke;
    public $permintaan_reseptur;
    public $jmlkemasan_reseptur;
    public $kekuatan_reseptur;
    public $satuankekuatan;
    public $qty_reseptur;
    public $hargasatuan_reseptur;
    public $signa_reseptur;
    public $harganetto_reseptur;
    public $hargajual_reseptur;
    public $etiket;
    public $iter;
    public $satuansediaan;
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

    // other attributes
    public $satuankecil_nama;
    public $cppt_id;
    public $jenis_racikan;
    public $harga_konversi;
    public $stok_sisa;
    public $qty_konversi;

    // constants
    const SCENARIO_SESSION = 'session'; //scenario add session
    const VC_RC = 'OR'; //constant reseptur type racikan
    const VC_NRC = 'NR'; //constant reseptur type non racikan

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'qty_reseptur', 'signa_reseptur','satuankecil_id'], 'required'],
            [['obatalkes_id', 'racikan_id', 'satuankecil_id', 'sumberdana_id', 'reseptur_id', 'rke', 'permintaan_reseptur', 'jmlkemasan_reseptur', 'kekuatan_reseptur', 'iter', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'cppt_id'], 'default', 'value' => null],
            [['obatalkes_id', 'racikan_id', 'satuankecil_id', 'sumberdana_id', 'reseptur_id', 'rke', 'permintaan_reseptur', 'jmlkemasan_reseptur', 'kekuatan_reseptur', 'iter', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'cppt_id'], 'integer'],
            [['hargasatuan_reseptur', 'harganetto_reseptur', 'hargajual_reseptur'], 'number'],
            [['additional_data'], 'string'],
            [[
                'created_date', 
                'last_modified_date', 
                'deleted_date', 
                'racikan_id', 
                'sumberdana_id', 
                'reseptur_id', 
                'qty_reseptur', 
                'harga_konversi', 
                'stok_sisa',
                'qty_konversi'
            ], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['r'], 'string', 'max' => 2],
            [['satuankekuatan'], 'string', 'max' => 20],
            [['signa_reseptur'], 'string', 'max' => 30],
            [['etiket'], 'string', 'max' => 100],
            [['satuansediaan'], 'string', 'max' => 50],
            [['qty_reseptur'], 'validasiQty'],
        ];
    }

    // override scenarios
    public function scenarios()
    {
        return [
            self::SCENARIO_SESSION => [
                'obatalkes_id', 
                'racikan_id', 
                'qty_reseptur', 
                'signa_reseptur',
                'satuankecil_id',
                'qty_konversi',
                'stok_sisa',
                'etiket', 'required'],
        ];
    }
    public function validasiQty()
    {
        $stok = str_replace(".","",$this->stok_sisa);
        $stok = str_replace(",",".",$stok);
        if (str_replace(",", ".", $this->qty_reseptur) > $stok) {
            $this->addError('qty_reseptur', 'Stok tidak mencukupi');
        }
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'resepturdetail_id' => 'Resepturdetail ID',
            'obatalkes_id' => 'Nama Obat',
            'racikan_id' => 'Racikan ID',
            'satuankecil_id' => 'Satuan',
            'sumberdana_id' => 'Sumberdana ID',
            'reseptur_id' => 'Reseptur ID',
            'qty_konversi' => 'Qty Konversi',
            'r' => 'R',
            'rke' => 'R -',
            'permintaan_reseptur' => Yii::t('fe', 'Permintaan'),
            'jmlkemasan_reseptur' => Yii::t('fe', 'Jumlah kemasan'),
            'kekuatan_reseptur' => Yii::t('fe', 'Kekuatan'),
            'satuankekuatan' => Yii::t('fe', 'Satuan'),
            'qty_reseptur' => Yii::t('fe', 'Jumlah'),
            'hargasatuan_reseptur' => Yii::t('fe', 'Harga per'),
            'signa_reseptur' => Yii::t('fe', 'Signa'),
            'harganetto_reseptur' => Yii::t('fe', 'Harga neto'),
            'hargajual_reseptur' => Yii::t('fe', 'Harga jual'),
            'etiket' => Yii::t('fe', 'Catatan'),
            'iter' => 'Iter',
            'satuansediaan' => Yii::t('fe', 'Satuan sediaan'),
            'additional_data' => Yii::t('fe', 'additional_data'),
            'created_date' => Yii::t('fe', 'created_date'),
            'created_by' => Yii::t('fe', 'created_by'),
            'modified_count' => Yii::t('fe', 'modified_count'),
            'last_modified_date' => Yii::t('fe', 'last_modified_date'),
            'last_modified_by' => Yii::t('fe', 'last_modified_by'),
            'is_deleted' => Yii::t('fe', 'is_deleted'),
            'is_active' => Yii::t('fe', 'is_active'),
            'deleted_date' => Yii::t('fe', 'deleted_date'),
            'deleted_by' => Yii::t('fe', 'deleted_by'),
        ];
    }
}
