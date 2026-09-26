<?php

use yii\db\Migration;

/**
 * Class m231101_031758_migrate_unduh_dokumen_konfigunduhdokumen_k
 */
class m231101_031758_migrate_unduh_dokumen_konfigunduhdokumen_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigunduhdokumen_k" DROP COLUMN "status_dokumen";');
        $this->execute("ALTER TABLE konfigunduhdokumen_k ADD COLUMN IF NOT EXISTS last_modified_date timestamp(6) NULL");
        $this->execute("ALTER TABLE konfigunduhdokumen_k ADD COLUMN IF NOT EXISTS last_modified_by int4 NULL");
        $this->execute("ALTER TABLE konfigunduhdokumen_k ADD COLUMN IF NOT EXISTS modified_count int4 NULL");
        $this->execute("ALTER TABLE konfigunduhdokumen_k ADD COLUMN IF NOT EXISTS deleted_by int4 NULL");
        $this->execute("ALTER TABLE konfigunduhdokumen_k ADD COLUMN IF NOT EXISTS deleted_date timestamp(6) NULL");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231101_031758_migrate_unduh_dokumen_konfigunduhdokumen_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231101_031758_migrate_unduh_dokumen_konfigunduhdokumen_k cannot be reverted.\n";

        return false;
    }
    */
}
