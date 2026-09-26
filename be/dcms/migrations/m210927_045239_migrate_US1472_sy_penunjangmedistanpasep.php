<?php

use yii\db\Migration;

/**
 * Class m210927_045239_migrate_US1472_sy_penunjangmedistanpasep
 */
class m210927_045239_migrate_US1472_sy_penunjangmedistanpasep extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
          ADD COLUMN IF NOT EXISTS "is_skip_bpjs_penunjang" bool DEFAULT false;
        ');

        $this->execute('ALTER TABLE "public"."bpjs_t" 
          ALTER COLUMN "tglsep" DROP NOT NULL,
          ALTER COLUMN "nosep" DROP NOT NULL,
          ALTER COLUMN "nokartuasuransi" DROP NOT NULL,
          ALTER COLUMN "tglrujukan" DROP NOT NULL,
          ALTER COLUMN "norujukan" DROP NOT NULL,
          ALTER COLUMN "ppkrujukan" DROP NOT NULL,
          ALTER COLUMN "jnspelayanan" DROP NOT NULL,
          ALTER COLUMN "diagnosaawal" DROP NOT NULL,
          ALTER COLUMN "politujuan" DROP NOT NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210927_045239_migrate_US1472_sy_penunjangmedistanpasep cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210927_045239_migrate_US1472_sy_penunjangmedistanpasep cannot be reverted.\n";

        return false;
    }
    */
}
