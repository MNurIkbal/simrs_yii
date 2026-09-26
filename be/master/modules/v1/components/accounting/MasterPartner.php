<?php

namespace app\modules\v1\components\accounting;

use Yii;

class MasterPartner extends BaseModel
{
    public function getTableName()
    {
        return 'master_partner';
    }
    
    public function getAttributes()
    {
        return [
            "sync_type",
            'sync_id_api',
            'parent_doctor_id',
            'doctor_code',
            'name',
            'display_name',
            'spec',
            'department',
            'type_employment',
            'type_doctor',
            'designation',
            'hrprofile',
            'gender',
            'phone',
            'mobile',
            'fax',
            'email',
            'website',
            'street',
            'street2',
            'street3',
            'city',
            'zip',
            'specialise_api_id',
            'wipro_block',
            'empblocked',
            'customer_type_api',
            'vendor_wipro',
            'costumer_wipro',
            'supplier',
            'customer',
            'insurance',
            'patient',
            'doctor',
            'active',
        ];
    }

    public function extract($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        $sync_type as sync_type,
        sync_id_api,
        parent_doctor_id,
        doctor_code,
        name,
        display_name,
        spec,
        department,
        type_employment,
        type_doctor,
        designation,
        hrprofile,
        gender,
        phone,
        mobile,
        fax,
        email,
        website,
        street,
        street2,
        street3,
        city,
        zip,
        specialise_api_id,
        wipro_block,
        empblocked,
        customer_type_api,
        vendor_wipro,
        costumer_wipro,
        supplier,
        customer,
        insurance,
        patient,
        doctor,
        active
        FROM int_partner")
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_id_api',
            'sync_type',
            'vendor_code',
            'doctor_code',
            'name',
            'spec',
            'type_employment',
            'hrprofile',
            'gender',
            'contact_person',
            'phone',
            'email',
            'website',
            'street',
            'city',
            'customer_type_api',
            'supplier',
            'customer',
            'insurance',
            'patient',
            'doctor',
            'is_deleted',
        ];
    }

    public function extractCsv($sync_type)
    {
        return $this->db->createCommand("
        SELECT
        sync_id_api,
        $sync_type as sync_type,
        vendor_code,
        doctor_code,
        name,
        spec,
        type_employment,
        hrprofile,
        gender,
        contact_person,
        phone,
        email,
        website,
        street,
        city,
        customer_type_api,
        supplier,
        customer,
        insurance,
        patient,
        doctor,
        wipro_block as is_deleted
        FROM int_partner")
        ->queryAll();
    }
}