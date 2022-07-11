<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220708105235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document_content_tag (document_content_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_34CBD61C256308BB (document_content_id), INDEX IDX_34CBD61CBAD26311 (tag_id), PRIMARY KEY(document_content_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_content_category (document_content_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_3599C0FC256308BB (document_content_id), INDEX IDX_3599C0FC12469DE2 (category_id), PRIMARY KEY(document_content_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_content_tag ADD CONSTRAINT FK_34CBD61C256308BB FOREIGN KEY (document_content_id) REFERENCES document_content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE document_content_tag ADD CONSTRAINT FK_34CBD61CBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE document_content_category ADD CONSTRAINT FK_3599C0FC256308BB FOREIGN KEY (document_content_id) REFERENCES document_content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE document_content_category ADD CONSTRAINT FK_3599C0FC12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE document_content_tag');
        $this->addSql('DROP TABLE document_content_category');
    }
}
