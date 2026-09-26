<?php

use yii\db\Migration;

/**
 * Class m211130_043336_migrate_pembayaran_t
 */
class m211130_043336_migrate_pembayaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."pembayaran_t" 
                            ADD COLUMN if not exists "total_discountadm" float8 DEFAULT 0;');

          $this->execute('ALTER TABLE "public"."pembayaran_t" 
                            ADD COLUMN if not exists "pembulatan" float8 DEFAULT 0;');

          $this->execute('COMMENT ON COLUMN "public"."pembayaran_t"."total_discountadm" IS \'total discount adm dibayar\';');

          $this->execute('COMMENT ON COLUMN "public"."pembayaran_t"."total_administrasi" IS \'total administrasi sudah plus diskon\';');

          $this->execute('COMMENT ON COLUMN "public"."pembayaran_t"."total_pembulatan" IS \'total pembulatan gabungan\';');

          $this->execute('COMMENT ON COLUMN "public"."pembayaran_t"."pembulatan" IS \'pembulatan tagihan utk pasien\';');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211130_043336_migrate_pembayaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211130_043336_migrate_pembayaran_t cannot be reverted.\n";

        return false;
    }
    */
}
