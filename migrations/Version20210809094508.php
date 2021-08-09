<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210809094508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89AB5BE816');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89AB5BE816 FOREIGN KEY (main_to_trick_id) REFERENCES trick (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89AB5BE816');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89AB5BE816 FOREIGN KEY (main_to_trick_id) REFERENCES trick (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
