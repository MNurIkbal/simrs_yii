<?php

use yii\db\Migration;

/**
 * Class m200205_041453_struktur_sync_20200205_1
 */
class m200205_041453_struktur_sync_20200205_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."mutasibarang_t" ALTER COLUMN "status_mutasi" SET DEFAULT 401;');

        $this->execute('ALTER TABLE "public"."pembayaran_t" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');
        
        $this->execute('ALTER TABLE "public"."pembayaranmetode_t" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');
        
        $this->execute('ALTER TABLE "public"."pembayaranpenjamin_t" ALTER COLUMN "pembayaranpenjamin_id" TYPE int4 USING "pembayaranpenjamin_id"::int4;');
        
        $this->execute('ALTER TABLE "public"."pembayaranpenjamin_t" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');
        
        $this->execute('ALTER TABLE "public"."pembayaranpiutang_t" ALTER COLUMN "tgl_pembayaranpiutang" SET DEFAULT (\'now\'::text)::date;');
        
        $this->execute('ALTER TABLE "public"."pembayaranpiutang_t" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');

        $this->execute('ALTER TABLE "public"."pemberianpiutang_t" ALTER COLUMN "tgl_pemberianpiutang" SET DEFAULT (\'now\'::text)::date;');
        
        $this->execute('ALTER TABLE "public"."pemberianpiutang_t" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');
        
          
        $this->execute('ALTER TABLE "public"."rujukan_t" ALTER COLUMN "created_date" SET NOT NULL;');
        
        $this->execute('ALTER TABLE "public"."tindakanbmhp_mp" ALTER COLUMN "created_date" SET DEFAULT (\'now\'::text)::date;');
        
        $this->execute('ALTER TABLE "public"."tindakanbmhp_mp" ALTER COLUMN "last_modified_date" SET DEFAULT (\'now\'::text)::date;');
        
        $this->execute('ALTER TABLE "public"."tindakanbmhp_mp" ALTER COLUMN "deleted_date" SET DEFAULT (\'now\'::text)::date;');
        
        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ALTER COLUMN "additional_riwayat" TYPE text COLLATE "pg_catalog"."default" USING "additional_riwayat"::text;');
        
       

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200205_041453_struktur_sync_20200205_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200205_041453_struktur_sync_20200205_1 cannot be reverted.\n";

        return false;
    }
    */
}
