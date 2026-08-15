<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260815125038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des tables facture et ligne_facture';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, numero VARCHAR(255) NOT NULL, date_emission DATETIME NOT NULL, date_echeance DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, montant_ht VARCHAR(255) NOT NULL, montant_tva VARCHAR(255) NOT NULL, montant_ttc VARCHAR(255) NOT NULL, conditions_paiement VARCHAR(255) DEFAULT NULL, client_id INT DEFAULT NULL, devis_id INT DEFAULT NULL, INDEX IDX_FE86641019EB6921 (client_id), INDEX IDX_FE86641041DEFADA (devis_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ligne_facture (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) NOT NULL, quantite NUMERIC(10, 2) NOT NULL, prix_unitaire_ht NUMERIC(10, 2) NOT NULL, taux_tva NUMERIC(5, 2) NOT NULL, facture_id INT DEFAULT NULL, INDEX IDX_611F5A297F2DEE08 (facture_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE86641019EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE86641041DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id)');
        $this->addSql('ALTER TABLE ligne_facture ADD CONSTRAINT FK_611F5A297F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE86641019EB6921');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE86641041DEFADA');
        $this->addSql('ALTER TABLE ligne_facture DROP FOREIGN KEY FK_611F5A297F2DEE08');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE ligne_facture');
    }
}
