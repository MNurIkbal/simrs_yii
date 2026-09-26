<?php

use yii\db\Migration;

/**
 * Class m220214_114438_migrate_APD115_asuransipenjamin_penerimaanKlaim
 */
class m220214_114438_migrate_APD115_asuransipenjamin_penerimaanKlaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {       
        $this->execute('DROP VIEW if exists public.infoterimabayarklaimdetail_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infoterimabayarklaimdetail_v\" AS
            SELECT terimabayarklaimdetail_t.terimabayarklaimdetail_id,
            terimabayarklaimdetail_t.terimabayarklaim_id,
            terimabayarklaim_t.no_terimabayarklaim,
            terimabayarklaimdetail_t.pengajuanklaim_id,
            pengajuanklaim_t.no_pengajuanklaim,
            pengajuanklaim_t.tgl_pengajuanklaim,
            terimabayarklaimdetail_t.total_pengajuan,
            terimabayarklaimdetail_t.total_terbayar,
            terimabayarklaimdetail_t.pembayaran,
            terimabayarklaimdetail_t.total_sisapiutang,
            terimabayarklaimdetail_t.is_deleted,
            terimabayarklaimdetail_t.is_alokasi
            FROM ((terimabayarklaimdetail_t
            JOIN terimabayarklaim_t ON ((terimabayarklaimdetail_t.terimabayarklaim_id = terimabayarklaim_t.terimabayarklaim_id)))
            JOIN pengajuanklaim_t ON ((terimabayarklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id)))
            WHERE (terimabayarklaimdetail_t.is_deleted = false)
            ;");
        $this->execute('
            ALTER TABLE public.infoterimabayarklaimdetail_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220214_114438_migrate_APD115_asuransipenjamin_penerimaanKlaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220214_114438_migrate_APD115_asuransipenjamin_penerimaanKlaim cannot be reverted.\n";

        return false;
    }
    */
}
