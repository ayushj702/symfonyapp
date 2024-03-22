<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320083217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C8D40075F9038C4 ON product_variations');
        $this->addSql('ALTER TABLE product_variations ADD uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE sku sku VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C8D40075D17F50A6 ON product_variations (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C8D40075D17F50A6 ON product_variations');
        $this->addSql('ALTER TABLE product_variations DROP uuid, CHANGE sku sku BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C8D40075F9038C4 ON product_variations (sku)');
    }
}
