<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240608175421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation_encaisse ADD couverts INT NOT NULL');
        $this->addSql('ALTER TABLE reservation_encaisse ADD nom VARCHAR(48) NOT NULL');
        $this->addSql('ALTER TABLE reservation_encaisse ADD prenom VARCHAR(48) NOT NULL');
        $this->addSql('ALTER TABLE reservation_encaisse ADD date_reservation DATE NOT NULL');
        $this->addSql('ALTER TABLE reservation_encaisse ADD telephone VARCHAR(17) NOT NULL');
        $this->addSql('ALTER TABLE reservation_encaisse ADD email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reservation_encaisse ADD comments TEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reservation_encaisse DROP couverts');
        $this->addSql('ALTER TABLE reservation_encaisse DROP nom');
        $this->addSql('ALTER TABLE reservation_encaisse DROP prenom');
        $this->addSql('ALTER TABLE reservation_encaisse DROP date_reservation');
        $this->addSql('ALTER TABLE reservation_encaisse DROP telephone');
        $this->addSql('ALTER TABLE reservation_encaisse DROP email');
        $this->addSql('ALTER TABLE reservation_encaisse DROP comments');
    }
}
