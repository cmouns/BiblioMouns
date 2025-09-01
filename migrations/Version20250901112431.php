<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250901112431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book_sub_category (book_id INT NOT NULL, sub_category_id INT NOT NULL, INDEX IDX_E5023DC016A2B381 (book_id), INDEX IDX_E5023DC0F7BFE87C (sub_category_id), PRIMARY KEY(book_id, sub_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_sub_category ADD CONSTRAINT FK_E5023DC016A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_sub_category ADD CONSTRAINT FK_E5023DC0F7BFE87C FOREIGN KEY (sub_category_id) REFERENCES sub_category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_sub_category DROP FOREIGN KEY FK_E5023DC016A2B381');
        $this->addSql('ALTER TABLE book_sub_category DROP FOREIGN KEY FK_E5023DC0F7BFE87C');
        $this->addSql('DROP TABLE book_sub_category');
    }
}
