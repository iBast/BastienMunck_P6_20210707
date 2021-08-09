<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210809094225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture ADD main_to_trick_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89AB5BE816 FOREIGN KEY (main_to_trick_id) REFERENCES trick (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16DB4F89AB5BE816 ON picture (main_to_trick_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8F0A91E989D9B62 ON trick (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89AB5BE816');
        $this->addSql('DROP INDEX UNIQ_16DB4F89AB5BE816 ON picture');
        $this->addSql('ALTER TABLE picture DROP main_to_trick_id');
        $this->addSql('DROP INDEX UNIQ_D8F0A91E989D9B62 ON trick');
    }
}
