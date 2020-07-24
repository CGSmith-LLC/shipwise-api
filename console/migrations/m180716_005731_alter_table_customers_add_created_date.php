<?php

use yii\db\Migration;

/**
 * Class m180716_005731_alter_table_customers_add_created_date
 */
class m180716_005731_alter_table_customers_add_created_date extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		$this->addColumn('{{%customers}}', 'created_date',
			$this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		$this->dropColumn('{{%customers}}', 'created_date');
	}
}
