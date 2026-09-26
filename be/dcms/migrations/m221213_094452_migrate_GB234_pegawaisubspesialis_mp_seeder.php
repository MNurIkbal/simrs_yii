<?php

use yii\db\Migration;

/**
 * Class m221213_094452_migrate_GB234_pegawaisubspesialis_mp_seeder
 */
class m221213_094452_migrate_GB234_pegawaisubspesialis_mp_seeder extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // TODOS:
        // Sub-Spesialis di hard-code 4, (karena tidak ada penjelasan harus ke subspesialis saja)
        // Point nya pengecheckan hanya pegawai isSubSpesialis

        // Delete Old Data
        $this->execute("DELETE from pegawaisubspesialis_mp WHERE pegawai_id=474 AND subspesialis_id=4 AND is_deleted = FALSE;");
        $this->execute("DELETE from pegawaisubspesialis_mp WHERE pegawai_id=507 AND subspesialis_id=4 AND is_deleted = FALSE;");
        $this->execute("DELETE from pegawaisubspesialis_mp WHERE pegawai_id=567 AND subspesialis_id=4 AND is_deleted = FALSE;");
        $this->execute("DELETE from pegawaisubspesialis_mp WHERE pegawai_id=469 AND subspesialis_id=4 AND is_deleted = FALSE;");
        $this->execute("DELETE from pegawaisubspesialis_mp WHERE pegawai_id=434 AND subspesialis_id=4 AND is_deleted = FALSE;");
        $this->execute("DELETE from pegawaisubspesialis_mp WHERE pegawai_id=476 AND subspesialis_id=4 AND is_deleted = FALSE;");

        // Insert New Data
        $this->execute("
            INSERT INTO public.pegawaisubspesialis_mp (pegawai_id,subspesialis_id,additional_data,created_date,created_by,modified_count,last_modified_date,last_modified_by,is_deleted,is_active,deleted_date,deleted_by)
            VALUES
            (474,4,NULL,'2022-12-14 16:00:40.498',NULL,NULL,NULL,NULL,false,true,NULL,NULL),
            (507,4,NULL,'2022-12-14 16:00:40.498',NULL,NULL,NULL,NULL,false,true,NULL,NULL),
            (567,4,NULL,'2022-12-14 16:00:40.498',NULL,NULL,NULL,NULL,false,true,NULL,NULL),
            (469,4,NULL,'2022-12-14 16:00:40.498',NULL,NULL,NULL,NULL,false,true,NULL,NULL),
            (434,4,NULL,'2022-12-14 16:00:40.498',NULL,NULL,NULL,NULL,false,true,NULL,NULL),
            (476,4,NULL,'2022-12-14 16:00:40.498',NULL,NULL,NULL,NULL,false,true,NULL,NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221213_094452_migrate_GB234_pegawaisubspesialis_mp_seeder cannot be reverted.\n";
        return false;
    }
}
