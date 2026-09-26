<?php

use yii\db\Migration;

/**
 * Class m251115_064111_migrate_DSV_2158_lookup_m
 */
class m251115_064111_migrate_DSV_2158_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        INSERT INTO \"public\".\"lookup_m\" (
            \"lookup_id\",
            \"lookup_type\",
            \"lookup_name\",
            \"lookup_value\",
            \"lookup_urutan\",
            \"lookup_kode\",
            \"additional_data\",
            \"created_date\",
            \"created_by\",
            \"modified_count\",
            \"last_modified_date\",
            \"last_modified_by\",
            \"is_deleted\",
            \"is_active\",
            \"deleted_date\",
            \"deleted_by\" 
        )
        VALUES
            (
                4035,
                'status_verifikasi_bantaran',
                'Belum Verifikasi',
                'Belum Verifikasi',
                NULL,
                NULL,
                NULL,
                '2025-05-20 00:00:00',
                NULL,
                NULL,
                NULL,
                NULL,
                'f',
                't',
                NULL,
                NULL 
            ), 
            (
                4036,
                'status_verifikasi_bantaran',
                'Sudah Verifikasi',
                'Sudah Verifikasi',
                NULL,
                NULL,
                NULL,
                '2025-05-20 00:00:00',
                NULL,
                NULL,
                NULL,
                NULL,
                'f',
                't',
                NULL,
                NULL 
            ),
            (
                4037,
                'status_verifikasi_bantaran',
                'Ditolak',
                'Ditolak',
                NULL,
                NULL,
                NULL,
                '2025-05-20 00:00:00',
                NULL,
                NULL,
                NULL,
                NULL,
                'f',
                't',
                NULL,
                NULL 
            );
        ");

        $this->execute("
        INSERT INTO \"public\".\"lookup_m\" (
	\"lookup_id\",
	\"lookup_type\",
	\"lookup_name\",
	\"lookup_value\",
	\"lookup_urutan\",
	\"lookup_kode\",
	\"additional_data\",
	\"created_date\",
	\"created_by\",
	\"modified_count\",
	\"last_modified_date\",
	\"last_modified_by\",
	\"is_deleted\",
	\"is_active\",
	\"deleted_date\",
	\"deleted_by\" 
)
VALUES
	(
		4038,
		'status_pelayanan_bantaran',
		'Dalam Pelayanan',
		'Dalam Pelayanan',
		NULL,
		NULL,
		NULL,
		'2025-05-20 00:00:00',
		NULL,
		NULL,
		NULL,
		NULL,
		'f',
		't',
		NULL,
NULL 
	),
	(
		4039,
		'status_pelayanan_bantaran',
		'Selesai Pelayanan',
		'Selesai Pelayanan',
		NULL,
		NULL,
		NULL,
		'2025-05-20 00:00:00',
		NULL,
		NULL,
		NULL,
		NULL,
		'f',
		't',
		NULL,
NULL 
	)
	;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251115_064111_migrate_DSV_2158_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251115_064111_migrate_DSV_2158_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
