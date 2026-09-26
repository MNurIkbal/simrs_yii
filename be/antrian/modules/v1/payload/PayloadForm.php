<?php 
namespace app\modules\v1\payload;

use Yii;


class PayloadForm extends \yii\base\Model
{
    public $jenisantrian_id;
    public $jadwalbukapoli_id;
    public $instalasi_id;
    public $ruangan_id;
    public $hari_id;
    public $antrian_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'jenisantrian_id',
                'jadwalbukapoli_id',
                'instalasi_id',
                'ruangan_id',
                'hari_id',
                'antrian_id',
            ],'safe'],
            [[
                'jenisantrian_id',
                'jadwalbukapoli_id',
                'instalasi_id',
                'ruangan_id',
                'hari_id',
                'antrian_id',
            ], 'integer']
        ];
    }
}