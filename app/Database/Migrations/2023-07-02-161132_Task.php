<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Task extends Migration
{
    public function up()
    {
    // Membuat kolom/field untuk tabel task
		$this->forge->addField([
			'id'          => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true
			],
			'judul'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255'
			],
			'status'      => [
				'type'           => 'INT',
				'default'        => 0,
			],
		]);

		// Membuat primary key
		$this->forge->addKey('id', TRUE);

		// Membuat tabel task
		$this->forge->createTable('task', TRUE);
	}

	//-------------------------------------------------------

	public function down()
	{
		// menghapus tabel task
		$this->forge->dropTable('task');
	}
}
