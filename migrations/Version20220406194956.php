<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220406194956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, id_shop_id INT NOT NULL, id_category_produit_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(500) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, featured INT NOT NULL, INDEX IDX_29A5EC27938B6DAD (id_shop_id), INDEX IDX_29A5EC276FC0B24E (id_category_produit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27938B6DAD FOREIGN KEY (id_shop_id) REFERENCES shop (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC276FC0B24E FOREIGN KEY (id_category_produit_id) REFERENCES category_produit (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE produit');
    }
}
