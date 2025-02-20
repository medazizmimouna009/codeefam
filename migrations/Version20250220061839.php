<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220061839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE nom_cours (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, nom_cours_id INT DEFAULT NULL, texte LONGTEXT DEFAULT NULL, INDEX IDX_B6F7494EEB85BD39 (nom_cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, nom_cours_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, INDEX IDX_A412FA92EB85BD39 (nom_cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz_question (quiz_id INT NOT NULL, question_id INT NOT NULL, INDEX IDX_6033B00B853CD175 (quiz_id), INDEX IDX_6033B00B1E27F6BF (question_id), PRIMARY KEY(quiz_id, question_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, texte LONGTEXT DEFAULT NULL, est_correcte TINYINT(1) DEFAULT NULL, INDEX IDX_5FB6DEC71E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_reponse (id INT AUTO_INCREMENT NOT NULL, quiz_id INT DEFAULT NULL, user_id INT DEFAULT NULL, question_id INT DEFAULT NULL, reponse_id INT DEFAULT NULL, est_correcte TINYINT(1) DEFAULT NULL, INDEX IDX_7BBC0CD853CD175 (quiz_id), INDEX IDX_7BBC0CDA76ED395 (user_id), INDEX IDX_7BBC0CD1E27F6BF (question_id), INDEX IDX_7BBC0CDCF18BB82 (reponse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EEB85BD39 FOREIGN KEY (nom_cours_id) REFERENCES nom_cours (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92EB85BD39 FOREIGN KEY (nom_cours_id) REFERENCES nom_cours (id)');
        $this->addSql('ALTER TABLE quiz_question ADD CONSTRAINT FK_6033B00B853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz_question ADD CONSTRAINT FK_6033B00B1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC71E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE user_reponse ADD CONSTRAINT FK_7BBC0CD853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE user_reponse ADD CONSTRAINT FK_7BBC0CDA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_reponse ADD CONSTRAINT FK_7BBC0CD1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE user_reponse ADD CONSTRAINT FK_7BBC0CDCF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id)');
        $this->addSql('ALTER TABLE achat ADD id_achat INT AUTO_INCREMENT NOT NULL, ADD type_achat VARCHAR(255) NOT NULL, ADD date_achat DATETIME NOT NULL, ADD date_fin DATETIME NOT NULL, ADD statut VARCHAR(255) NOT NULL, ADD idOffre INT DEFAULT NULL, ADD idCours INT DEFAULT NULL, CHANGE id id_utilisateur INT NOT NULL, ADD PRIMARY KEY (id_achat)');
        $this->addSql('ALTER TABLE achat ADD CONSTRAINT FK_26A98456B842C572 FOREIGN KEY (idOffre) REFERENCES offre (id)');
        $this->addSql('ALTER TABLE achat ADD CONSTRAINT FK_26A98456EA0ECF81 FOREIGN KEY (idCours) REFERENCES cours (id)');
        $this->addSql('CREATE INDEX IDX_26A98456B842C572 ON achat (idOffre)');
        $this->addSql('CREATE INDEX IDX_26A98456EA0ECF81 ON achat (idCours)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EEB85BD39');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92EB85BD39');
        $this->addSql('ALTER TABLE quiz_question DROP FOREIGN KEY FK_6033B00B853CD175');
        $this->addSql('ALTER TABLE quiz_question DROP FOREIGN KEY FK_6033B00B1E27F6BF');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC71E27F6BF');
        $this->addSql('ALTER TABLE user_reponse DROP FOREIGN KEY FK_7BBC0CD853CD175');
        $this->addSql('ALTER TABLE user_reponse DROP FOREIGN KEY FK_7BBC0CDA76ED395');
        $this->addSql('ALTER TABLE user_reponse DROP FOREIGN KEY FK_7BBC0CD1E27F6BF');
        $this->addSql('ALTER TABLE user_reponse DROP FOREIGN KEY FK_7BBC0CDCF18BB82');
        $this->addSql('DROP TABLE nom_cours');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE quiz_question');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE user_reponse');
        $this->addSql('ALTER TABLE achat MODIFY id_achat INT NOT NULL');
        $this->addSql('ALTER TABLE achat DROP FOREIGN KEY FK_26A98456B842C572');
        $this->addSql('ALTER TABLE achat DROP FOREIGN KEY FK_26A98456EA0ECF81');
        $this->addSql('DROP INDEX IDX_26A98456B842C572 ON achat');
        $this->addSql('DROP INDEX IDX_26A98456EA0ECF81 ON achat');
        $this->addSql('DROP INDEX `primary` ON achat');
        $this->addSql('ALTER TABLE achat DROP id_achat, DROP type_achat, DROP date_achat, DROP date_fin, DROP statut, DROP idOffre, DROP idCours, CHANGE id_utilisateur id INT NOT NULL');
    }
}
