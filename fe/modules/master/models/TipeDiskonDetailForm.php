<?php

/**
 * @author Dede Herdiana
 * @todo Master Kontrak Manajemen
 * @copyright 18 Juni 2021
 */

namespace app\modules\master\models;
use app\components\DocoBaseModel;

use Yii;
use yii\db\Query;
use app\components\DocoConstants;


/**
 * This is the model class for table "kontrakpenjamindetail_m".
 *
 * @property int $tipediskon_id
 * @property int $jenislayanan_id
 * @property int $layanan_id
 * @property int $is_ditagihkan
 * @property int $disc_persen
 * @property int $max_dijamin
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
 * @property string $layanan
 */

class TipeDiskonDetailForm extends DocoBaseModel
{
    public $tipediskon_id;
    public $jenislayanan_id;
    public $layanan_id;
    public $is_ditagihkan;
    public $disc_persen;
    public $max_dijamin;
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
    public $layanan;
    public $is_change;

    /**
     * @todo Master Tipe Diskon
     * @return void
     */
    public function rules()
    {
        return [
            [['jenislayanan_id'], 'required'],
            [['tipediskon_id', 'jenislayanan_id', 'layanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['created_date', 'disc_persen', 'max_dijamin', 'is_deleted', 'is_active', 'is_change'], 'safe'],
            [['layanan'], 'string'],
        ];
    }

    public function validateCustomRequired(){
        $status = true;
        $message = "Layanan tidak boleh kosong!";
        if($this->jenislayanan_id == DocoConstants::TD_KELAS){
            $message = "Kelas tidak boleh kosong!";
        }else if($this->jenislayanan_id == DocoConstants::TD_KELOMPOK){
            $message = "Kategori tidak boleh kosong!";
        }else if($this->jenislayanan_id == DocoConstants::TD_TINDAKAN){
            $message = "Tindakan tidak boleh kosong!";
        }

        if(empty($this->layanan_id)){
            $this->addError('layanan_id',$message);
            $status = false;
        }
        if(empty($this->disc_persen)){
            $this->addError('disc_persen', 'Diskon persen tidak boleh kosong!');
            $status = false;
        }
        if(empty($this->max_dijamin)){
            $this->addError('max_dijamin', 'Maksimum dijamin tidak boleh kosong!');
            $status = false;
        }
        return $status;
    }

}
