<?php

use yii\db\Migration;

/**
 * Class m201217_093243_hotfix_add_index_table_tarif
 */
class m201217_093243_hotfix_add_index_table_tarif extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE INDEX IF NOT EXISTS "idx_daftartindakan_id" ON "public"."tindakanruangan_mp" USING btree ("daftartindakan_id");
        ');

        $this->execute('
            CREATE INDEX IF NOT EXISTS "idx_ruangan_id" ON "public"."tindakanruangan_mp" USING btree ("ruangan_id");
        ');

        $this->execute('
           CREATE INDEX IF NOT EXISTS "idx_tarif_daftartindakan_id" ON "public"."tariftindakan_m" ("daftartindakan_id");
        ');

        $this->execute('
           CREATE INDEX IF NOT EXISTS "idx_tarif_kelaspelayanan_id" ON "public"."tariftindakan_m" ("kelaspelayanan_id");
        ');

        $this->execute('
           CREATE INDEX IF NOT EXISTS "idx_tarif_komponen" ON "public"."tariftindakan_m" ("komponentarif_id");
        ');

        $this->execute('
           CREATE INDEX IF NOT EXISTS "idx_tarif_penjamin_id" ON "public"."tariftindakan_m" ("penjamin_id");
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201217_093243_hotfix_add_index_table_tarif cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201217_093243_hotfix_add_index_table_tarif cannot be reverted.\n";

        return false;
    }
    */
}
