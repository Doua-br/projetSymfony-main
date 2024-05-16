<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240516110850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coach DROP FOREIGN KEY FK_3F596DCC7EE5403C');
        $this->addSql('ALTER TABLE coach ADD CONSTRAINT FK_3F596DCC7EE5403C FOREIGN KEY (administrateur_id) REFERENCES administrateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coach DROP FOREIGN KEY FK_3F596DCC7EE5403C');
        $this->addSql('ALTER TABLE coach ADD CONSTRAINT FK_3F596DCC7EE5403C FOREIGN KEY (administrateur_id) REFERENCES administrateur (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
