<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260330132043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annonce_helpers (annonce_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY (annonce_id, user_id))');
        $this->addSql('CREATE INDEX IDX_2A87D19C8805AB2F ON annonce_helpers (annonce_id)');
        $this->addSql('CREATE INDEX IDX_2A87D19CA76ED395 ON annonce_helpers (user_id)');
        $this->addSql('ALTER TABLE annonce_helpers ADD CONSTRAINT FK_2A87D19C8805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE annonce_helpers ADD CONSTRAINT FK_2A87D19CA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE announce_helpers DROP CONSTRAINT fk_e9f79abd8805ab2f');
        $this->addSql('ALTER TABLE announce_helpers DROP CONSTRAINT fk_e9f79abda76ed395');
        $this->addSql('DROP TABLE announce_helpers');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announce_helpers (annonce_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY (annonce_id, user_id))');
        $this->addSql('CREATE INDEX idx_e9f79abda76ed395 ON announce_helpers (user_id)');
        $this->addSql('CREATE INDEX idx_e9f79abd8805ab2f ON announce_helpers (annonce_id)');
        $this->addSql('ALTER TABLE announce_helpers ADD CONSTRAINT fk_e9f79abd8805ab2f FOREIGN KEY (annonce_id) REFERENCES annonce (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE announce_helpers ADD CONSTRAINT fk_e9f79abda76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE annonce_helpers DROP CONSTRAINT FK_2A87D19C8805AB2F');
        $this->addSql('ALTER TABLE annonce_helpers DROP CONSTRAINT FK_2A87D19CA76ED395');
        $this->addSql('DROP TABLE annonce_helpers');
    }
}
