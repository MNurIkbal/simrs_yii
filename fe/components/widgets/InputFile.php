<?php
namespace app\components\widgets;

use Yii;

class InputFile extends \yii\base\Widget
{
    public $model;
    public $data;
    public $dokumen;
    public $dokumen_eklaim;
    public $parent_id;
    public $pendaftaran_id;
    public $is_pasienid;
    public $isDisabled;
    public $isHide;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        parent::run();
        return $this->render('inputfile', [
            'model' => $this->model,
            'data'  => $this->data,
            'dokumen' => $this->dokumen,
            'dokumen_eklaim' => $this->dokumen_eklaim,
            'pendaftaran_id' => $this->pendaftaran_id,
            'parent_id' => $this->parent_id,
            'is_pasienid' => $this->is_pasienid,
            'isDisabled' => $this->isDisabled,
            'isHide' => $this->isHide
        ]);
    }
}
