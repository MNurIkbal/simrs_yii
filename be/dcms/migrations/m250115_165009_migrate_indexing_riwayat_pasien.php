<?php

use yii\db\Migration;

/**
 * Class m250115_165009_migrate_indexing_riwayat_pasien
 */
class m250115_165009_migrate_indexing_riwayat_pasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE INDEX IF NOT EXISTS "instindakan_pendaftaran_idx" ON "public"."instruksitindakan_t" USING btree ("pendaftaran_id");');
        $this->execute('CREATE INDEX IF NOT EXISTS  "instindakan_pasien_id_idx" ON "public"."instruksitindakan_t" USING btree ("pasien_id");');
		$this->execute('CREATE INDEX IF NOT EXISTS  "pasienpulang_kondisikeluar_id_idx" ON "public"."pasienpulang_t" USING btree ("kondisikeluar_id");');
		$this->execute('CREATE INDEX IF NOT EXISTS  "catatankeoerawatan_pendaftaran_idx" ON "public"."catatankeperawatan_t" USING btree ("pendaftaran_id");');
		$this->execute('CREATE INDEX IF NOT EXISTS  "catatankeperawatan_pasienadmisis_idx" ON "public"."catatankeperawatan_t" USING btree ("pasienadmisi_id");');
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250115_165009_migrate_indexing_riwayat_pasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250115_165009_migrate_indexing_riwayat_pasien cannot be reverted.\n";

        return false;
    }
    */
}
