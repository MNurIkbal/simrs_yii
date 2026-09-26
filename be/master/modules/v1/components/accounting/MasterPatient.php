<?php

namespace app\modules\v1\components\accounting;

use Yii;

class MasterPatient extends BaseModel
{
    public function getTableName()
    {
        return 'master_patient';
    }
    
    public function getAttributes()
    {
        return [
            "sync_type",
            'cutoff_date',
            'sync_id_api',
            'registration_code',
            'name',
            'display_name',
            'gender',
            'title_name',
            'city',
            'date_of_birth',
            'street',
            'street2',
            'street3',
            'zip',
            'phone',
            'mobile',
            'fax',
            'email',
            'contact_person',
            'patient',
            'passport',
            'ktp',
            'active',
            'wipro_block',
            'keterangan',
            'tgl_proses',
            'pasien_id',
            'id'
        ];
    }

    public function extract($date,$sync_type)
    {
        return $this->db->createCommand("
        select
        $sync_type as sync_type,
        date(tgl_proses) as cutoff_date,
        sync_id_api,
        registration_code,
        name,
        display_name,
        gender,
        title_name,
        city,
        date_of_birth,
        street,
        street2,
        street3,
        zip,
        phone,
        mobile,
        fax,
        email,
        contact_person,
        patient,
        passport,
        ktp,
        active,
        wipro_block,
        keterangan,
        tgl_proses,
        pasien_id,
        id
        from newodoo_pasien_v where date(tgl_proses) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }

    public function headerCsv()
    {
        return [
            'sync_id_api',
            'sync_type',
            'registration_code',
            'name',
            'date_of_birth',
            'gender',
            'gender_id',
            'mobile',
            'contact_person',
            'fax',
            'email',
            'street',
            'city',
            'zip',
            'passport',
            'ktp',
            'is_deleted',
            'keterangan',
            'tgl_proses',
        ];
    }

    public function extractCsv($date,$sync_type)
    {
        return $this->db->createCommand("
        select
        sync_id_api,
        $sync_type as sync_type,
        registration_code,
        name,
        date_of_birth,
        gender,
        gender_id,
        mobile,
        contact_person,
        fax,
        email,
        street,
        city,
        zip,
        passport,
        ktp,
        wipro_block as is_deleted,
        keterangan,
        tgl_proses
        from newodoo_pasien_v where date(tgl_proses) = :date")
        ->bindValue(':date',$date)
        ->queryAll();
    }
}