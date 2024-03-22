<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240316125008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE inventory_item (id INT AUTO_INCREMENT NOT NULL, shop_id INT NOT NULL, product_id INT NOT NULL, name VARCHAR(255) NOT NULL, quantity INT NOT NULL, INDEX IDX_55BDEA304D16C4DD (shop_id), INDEX IDX_55BDEA304584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inventory_item ADD CONSTRAINT FK_55BDEA304D16C4DD FOREIGN KEY (shop_id) REFERENCES shops (id)');
        $this->addSql('ALTER TABLE inventory_item ADD CONSTRAINT FK_55BDEA304584665A FOREIGN KEY (product_id) REFERENCES products (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE inventory_item DROP FOREIGN KEY FK_55BDEA304D16C4DD');
        $this->addSql('ALTER TABLE inventory_item DROP FOREIGN KEY FK_55BDEA304584665A');
        $this->addSql('DROP TABLE inventory_item');
    }
}
