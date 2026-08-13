<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260813214759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ligne_devis (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) NOT NULL, quantite VARCHAR(255) NOT NULL, prix_unitaire_ht NUMERIC(10, 2) NOT NULL, taux_tva NUMERIC(5, 2) NOT NULL, montant_ht NUMERIC(10, 2) NOT NULL, devis_id INT DEFAULT NULL, INDEX IDX_888B2F1B41DEFADA (devis_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE ligne_devis ADD CONSTRAINT FK_888B2F1B41DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE devis CHANGE numero numero VARCHAR(50) NOT NULL, CHANGE montant_ht montant_ht NUMERIC(10, 2) NOT NULL, CHANGE montant_tva montant_tva NUMERIC(10, 2) NOT NULL, CHANGE montant_ttc montant_ttc NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8B27C52BF55AE19E ON devis (numero)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ligne_devis DROP FOREIGN KEY FK_888B2F1B41DEFADA');
        $this->addSql('DROP TABLE ligne_devis');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A76ED395');
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52B19EB6921');
        $this->addSql('DROP INDEX UNIQ_8B27C52BF55AE19E ON devis');
        $this->addSql('ALTER TABLE devis CHANGE numero numero VARCHAR(255) NOT NULL, CHANGE montant_ht montant_ht VARCHAR(255) NOT NULL, CHANGE montant_tva montant_tva VARCHAR(255) NOT NULL, CHANGE montant_ttc montant_ttc VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649979B1AD6');
    }
}
