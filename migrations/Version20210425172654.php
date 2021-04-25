<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210425172654 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_list DROP FOREIGN KEY FK_A8FBFABA3D3D2749');
        $this->addSql('ALTER TABLE card_list DROP FOREIGN KEY FK_A8FBFABA3D8E604F');
        $this->addSql('DROP INDEX IDX_A8FBFABA3D8E604F ON card_list');
        $this->addSql('DROP INDEX IDX_A8FBFABA3D3D2749 ON card_list');
        $this->addSql('ALTER TABLE card_list ADD parent_id INT DEFAULT NULL, DROP parent, DROP children_id');
        $this->addSql('ALTER TABLE card_list ADD CONSTRAINT FK_A8FBFABA727ACA70 FOREIGN KEY (parent_id) REFERENCES card_list (id)');
        $this->addSql('CREATE INDEX IDX_A8FBFABA727ACA70 ON card_list (parent_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_list DROP FOREIGN KEY FK_A8FBFABA727ACA70');
        $this->addSql('DROP INDEX IDX_A8FBFABA727ACA70 ON card_list');
        $this->addSql('ALTER TABLE card_list ADD children_id INT DEFAULT NULL, CHANGE parent_id parent INT DEFAULT NULL');
        $this->addSql('ALTER TABLE card_list ADD CONSTRAINT FK_A8FBFABA3D3D2749 FOREIGN KEY (children_id) REFERENCES card_list (id)');
        $this->addSql('ALTER TABLE card_list ADD CONSTRAINT FK_A8FBFABA3D8E604F FOREIGN KEY (parent) REFERENCES card_list (id)');
        $this->addSql('CREATE INDEX IDX_A8FBFABA3D8E604F ON card_list (parent)');
        $this->addSql('CREATE INDEX IDX_A8FBFABA3D3D2749 ON card_list (children_id)');
    }
}
