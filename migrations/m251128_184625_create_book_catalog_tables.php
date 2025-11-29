<?php

use yii\db\Migration;

class m251128_184625_create_book_catalog_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%author}}', [
            'id' => $this->primaryKey(),
            'full_name' => $this->string(255)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'year' => $this->integer()->notNull(),
            'description' => $this->text(),
            'isbn' => $this->string(32),
            'cover_image_path' => $this->string(255),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%book_author}}', [
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
        ]);
        $this->addPrimaryKey('pk_book_author', '{{%book_author}}', ['book_id', 'author_id']);
        $this->addForeignKey(
            'fk_book_author_book',
            '{{%book_author}}',
            'book_id',
            '{{%book}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_book_author_author',
            '{{%book_author}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('{{%author_subscription}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'phone' => $this->string(32)->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);
        $this->createIndex(
            'idx_author_subscription_author_phone',
            '{{%author_subscription}}',
            ['author_id', 'phone'],
            true
        );
        $this->addForeignKey(
            'fk_author_subscription_author',
            '{{%author_subscription}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropForeignKey('fk_author_subscription_author', '{{%author_subscription}}');
        $this->dropTable('{{%author_subscription}}');

        $this->dropForeignKey('fk_book_author_author', '{{%book_author}}');
        $this->dropForeignKey('fk_book_author_book', '{{%book_author}}');
        $this->dropTable('{{%book_author}}');

        $this->dropTable('{{%book}}');
        $this->dropTable('{{%author}}');
    }
}
