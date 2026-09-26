<?php

/**
 * @Author: Aris
 * @Date:   2021-01-14 00:00:00
 * @Description: 
 */

namespace app\modules\ranap\models;

use Yii;

class CathlabForm extends \yii\base\Model
{
	public $cathlab_id;
	public $pendaftaran_id;
	public $pasienadmisi_id;
	public $tgl_chatlab;
	public $indikasi;
	public $approach;
	public $target;
	public $koroner;
	public $lm;
	public $lad;
	public $lcx;
	public $rca;
	public $laporan_pci;
	public $laporan_dsa;
	public $lain_lain;
	public $kesimpulan;
	public $saran;
	public $cum_air_kerma;
	public $cum_dap;
	public $fluo_time;
	public $kontras;
	public $procedure_time;
	public $operator_id;
	public $tgl_prosedure;
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
					'tgl_chatlab',
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'cathlab_id'      => Yii::t('fe', 'Cathlab Id'),     
			'pendaftaran_id'  => Yii::t('fe', 'Pendaftaran Id'), 
			'pasienadmisi_id' => Yii::t('fe', 'Pasienadmisi Id'),
			'tgl_chatlab'     => Yii::t('fe', 'Tanggal Cathlab'),    
			'indikasi'        => Yii::t('fe', 'INDIKASI'),       
			'approach'        => Yii::t('fe', 'APPROACH'),       
			'target'          => Yii::t('fe', 'TARGET'),         
			'koroner'         => Yii::t('fe', 'KORONER'),        
			'lm'              => Yii::t('fe', 'LM'),             
			'lad'             => Yii::t('fe', 'LAD'),            
			'lcx'             => Yii::t('fe', 'LCX'),            
			'rca'             => Yii::t('fe', 'RCA'),            
			'laporan_pci'     => Yii::t('fe', 'LAPORAN PCI'),    
			'laporan_dsa'     => Yii::t('fe', 'LAPORAN DSA'),    
			'lain_lain'       => Yii::t('fe', 'Lain-lain'),      
			'kesimpulan'      => Yii::t('fe', 'KESIMPULAN'),     
			'saran'           => Yii::t('fe', 'SARAN'),          
			'cum_air_kerma' => Yii::t('fe', 'Cum Air Kerma'),
			'cum_dap'         => Yii::t('fe', 'Cum DAP'),        
			'fluo_time'       => Yii::t('fe', 'Fluo Time'),      
			'kontras'         => Yii::t('fe', 'Kontras'),        
			'procedure_time'  => Yii::t('fe', 'Procedure Time'), 
			'operator_id'     => Yii::t('fe', ''),    
			'tgl_prosedure'   => Yii::t('fe', 'TGL PROSEDURE')
        ];
    }
}