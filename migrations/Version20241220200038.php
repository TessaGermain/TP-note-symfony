<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241220200038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE song DROP FOREIGN KEY FK_33EDEEA14296D31F');
        $this->addSql('ALTER TABLE song DROP FOREIGN KEY FK_33EDEEA1B7970CF8');
        $this->addSql('DROP INDEX IDX_33EDEEA14296D31F ON song');
        $this->addSql('DROP INDEX IDX_33EDEEA1B7970CF8 ON song');
        $this->addSql('ALTER TABLE song ADD contributors VARCHAR(255) DEFAULT NULL, DROP genre_id, DROP artist_id, DROP language');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE song ADD genre_id INT NOT NULL, ADD artist_id INT NOT NULL, ADD language VARCHAR(255) NOT NULL, DROP contributors');
        $this->addSql('ALTER TABLE song ADD CONSTRAINT FK_33EDEEA14296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE song ADD CONSTRAINT FK_33EDEEA1B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_33EDEEA14296D31F ON song (genre_id)');
        $this->addSql('CREATE INDEX IDX_33EDEEA1B7970CF8 ON song (artist_id)');
    }
}
