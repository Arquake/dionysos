<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240610214346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE non_reservation_encaisse_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE non_reservation_encaisse (id INT NOT NULL, date DATE NOT NULL, article JSON NOT NULL, couverts INT NOT NULL, horraire TIME(0) WITHOUT TIME ZONE NOT NULL, total NUMERIC(10, 2) NOT NULL, marge NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE non_reservation_encaisse_id_seq CASCADE');
        $this->addSql('DROP TABLE non_reservation_encaisse');
    }
}
