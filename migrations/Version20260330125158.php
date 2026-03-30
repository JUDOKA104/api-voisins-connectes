<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260330125158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announce_helpers (annonce_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY (annonce_id, user_id))');
        $this->addSql('CREATE INDEX IDX_E9F79ABD8805AB2F ON announce_helpers (annonce_id)');
        $this->addSql('CREATE INDEX IDX_E9F79ABDA76ED395 ON announce_helpers (user_id)');
        $this->addSql('ALTER TABLE announce_helpers ADD CONSTRAINT FK_E9F79ABD8805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE announce_helpers ADD CONSTRAINT FK_E9F79ABDA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE annonce DROP CONSTRAINT fk_f65593e5d7693e95');
        $this->addSql('DROP INDEX idx_f65593e5d7693e95');
        $this->addSql('ALTER TABLE annonce DROP helper_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announce_helpers DROP CONSTRAINT FK_E9F79ABD8805AB2F');
        $this->addSql('ALTER TABLE announce_helpers DROP CONSTRAINT FK_E9F79ABDA76ED395');
        $this->addSql('DROP TABLE announce_helpers');
        $this->addSql('ALTER TABLE annonce ADD helper_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT fk_f65593e5d7693e95 FOREIGN KEY (helper_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_f65593e5d7693e95 ON annonce (helper_id)');
    }
}
