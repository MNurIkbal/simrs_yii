<?php
namespace Integrasi\Service\Odoo;

use Yii;
use Integrasi\Service\Odoo\Models\ServiceGroup as Model;
use Integrasi\Components\Object\AdditionalResponseObject;

class ServiceGroup extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $qCateg = Model::find()->asArray()->all();
        foreach ($qCateg as $value) {
            $idCateg = $value['servicegroup_id'];
            $additional = !empty($value['additional_data']) ? json_decode($value['additional_data'], true) : [];
            $model = new AdditionalResponseObject;
            $model->setAttributes($additional);
            if (!empty($model->is_sending)) {
                
            } else {
                $model->is_sending = true;
            }

            Model::updateAll([
                'additional_data' => json_encode($model->buildArray())
            ], [
                'servicegroup_id' => $idCateg
            ]);
        }
        return json_encode([
            'service' => 'Odoo-ServiceGroup',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

}