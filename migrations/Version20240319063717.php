<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240319063717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_variations DROP FOREIGN KEY FK_C8D400754584665A');
        $this->addSql('DROP TABLE product_variations');
        $this->addSql('ALTER TABLE inventory DROP FOREIGN KEY FK_B12D4A364584665A');
        $this->addSql('DROP INDEX IDX_B12D4A364584665A ON inventory');
        $this->addSql('ALTER TABLE inventory DROP product_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_variations (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, variant VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, stock_level INT NOT NULL, value NUMERIC(10, 2) NOT NULL, INDEX IDX_C8D400754584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE product_variations ADD CONSTRAINT FK_C8D400754584665A FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE inventory ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE inventory ADD CONSTRAINT FK_B12D4A364584665A FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B12D4A364584665A ON inventory (product_id)');
    }
}
