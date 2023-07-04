<?php

declare(strict_types=1);

namespace Skar\LaminasDoctrineORM;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230704104939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__image AS SELECT id, name, data, width, height, density, orientation, format, depth, colour_space, size FROM image');
        $this->addSql('DROP TABLE image');
        $this->addSql('CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(200) NOT NULL, data BLOB NOT NULL, width INTEGER DEFAULT NULL, height INTEGER DEFAULT NULL, density CLOB DEFAULT NULL --(DC2Type:json)
        , orientation INTEGER DEFAULT NULL, format VARCHAR(255) DEFAULT NULL, depth VARCHAR(255) DEFAULT NULL, colour_space VARCHAR(255) DEFAULT NULL, size INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO image (id, name, data, width, height, density, orientation, format, depth, colour_space, size) SELECT id, name, data, width, height, density, orientation, format, depth, colour_space, size FROM __temp__image');
        $this->addSql('DROP TABLE __temp__image');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045FADF3F363 ON image (data)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045F5E237E06 ON image (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__image AS SELECT id, name, data, width, height, density, orientation, format, depth, colour_space, size FROM image');
        $this->addSql('DROP TABLE image');
        $this->addSql('CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(200) NOT NULL, data BLOB NOT NULL, width INTEGER DEFAULT NULL, height INTEGER DEFAULT NULL, density CLOB DEFAULT NULL, orientation VARCHAR(255) DEFAULT NULL, format VARCHAR(255) DEFAULT NULL, depth VARCHAR(255) DEFAULT NULL, colour_space VARCHAR(255) DEFAULT NULL, size INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO image (id, name, data, width, height, density, orientation, format, depth, colour_space, size) SELECT id, name, data, width, height, density, orientation, format, depth, colour_space, size FROM __temp__image');
        $this->addSql('DROP TABLE __temp__image');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045F5E237E06 ON image (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C53D045FADF3F363 ON image (data)');
    }
}
