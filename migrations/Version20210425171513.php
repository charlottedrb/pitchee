<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210425171513 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_list DROP FOREIGN KEY FK_A8FBFABA6DC9160B');
        $this->addSql('DROP INDEX IDX_A8FBFABA6DC9160B ON card_list');
        $this->addSql('ALTER TABLE card_list DROP childs_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_list ADD childs_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE card_list ADD CONSTRAINT FK_A8FBFABA6DC9160B FOREIGN KEY (childs_id) REFERENCES card_list (id)');
        $this->addSql('CREATE INDEX IDX_A8FBFABA6DC9160B ON card_list (childs_id)');
    }
}
