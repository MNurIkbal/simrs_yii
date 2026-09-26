<?php

use yii\db\Migration;

/**
 * Class m211213_053556_migrate_konfirmasiunit_v
 */
class m211213_053556_migrate_konfirmasiunit_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.konfirmasiunit_v;');
        
        $this->execute("
            CREATE VIEW \"public\".\"konfirmasiunit_v\" AS  SELECT konfirmasiunit_t.pendaftaran_id,
    konfirmasiunit_t.instalasi_id,
    instalasi_m.instalasi_nama,
    konfirmasiunit_t.is_konfirmasi
   FROM konfirmasiunit_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON konfirmasiunit_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON konfirmasiunit_t.instalasi_id = instalasi_m.instalasi_id;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211213_053556_migrate_konfirmasiunit_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211213_053556_migrate_konfirmasiunit_v cannot be reverted.\n";

        return false;
    }
    */
}
