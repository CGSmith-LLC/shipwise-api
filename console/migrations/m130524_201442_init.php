<?php

use yii\db\Migration;

/**
 * Class m130524_201442_init
 *
 * This migration is to create a table for the list of API consumers.
 *
 */
class m130524_201442_init extends Migration
{
	/** {@inheritdoc} */
	public function safeUp()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%api_consumer}}', [
			'id'                 => $this->primaryKey(),
			'auth_key'           => $this->string(6)->notNull()->unique()
				->comment('API consumer key. Used for authentication'),
			'auth_secret'        => $this->string(32)->notNull()->unique()
				->comment('API consumer secret. Used for authentication'),
			'auth_token'         => $this->string(32)->defaultValue(null)
				->comment('The API token obtained during authentication'),
			'token_generated_on' => $this->dateTime()->defaultValue(null),
			'customer_id'        => $this->integer(11)->defaultValue(null),
			'status'             => $this->smallInteger()->notNull()->defaultValue(1)
				->comment('API consumer status. 1:active, 0:inactive'),
			'created_date'       => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
		], $tableOptions . " COMMENT 'List of API consumers'");

		$this->createIndex('api_consumer_auth_token_idx', '{{%api_consumer}}', 'auth_token');
	}

	/** {@inheritdoc} */
	public function safeDown()
	{
		$this->dropTable('{{%api_consumer}}');
	}
}
