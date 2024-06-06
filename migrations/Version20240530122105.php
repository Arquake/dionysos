<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240530122105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD telephone VARCHAR(17) NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD comments TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation ADD civilite VARCHAR(5) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reservation DROP telephone');
        $this->addSql('ALTER TABLE reservation DROP email');
        $this->addSql('ALTER TABLE reservation DROP comments');
        $this->addSql('ALTER TABLE reservation DROP civilite');
    }
}
