<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250304114508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours DROP FOREIGN KEY FK_FDCA8C9C4CC8505A');
        $this->addSql('DROP INDEX IDX_FDCA8C9C4CC8505A ON cours');
        $this->addSql('ALTER TABLE cours DROP offre_id');
        $this->addSql('ALTER TABLE offre ADD cour_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE offre ADD CONSTRAINT FK_AF86866FB7942F03 FOREIGN KEY (cour_id) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_AF86866FB7942F03 ON offre (cour_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cours ADD offre_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cours ADD CONSTRAINT FK_FDCA8C9C4CC8505A FOREIGN KEY (offre_id) REFERENCES offre (id)');
        $this->addSql('CREATE INDEX IDX_FDCA8C9C4CC8505A ON cours (offre_id)');
        $this->addSql('ALTER TABLE offre DROP FOREIGN KEY FK_AF86866FB7942F03');
        $this->addSql('DROP INDEX IDX_AF86866FB7942F03 ON offre');
        $this->addSql('ALTER TABLE offre DROP cour_id');
    }
}
