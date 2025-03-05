<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302132033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE achat (id_achat INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, type_achat VARCHAR(255) NOT NULL, date_achat DATETIME NOT NULL, date_fin DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, idOffre INT DEFAULT NULL, idCours INT DEFAULT NULL, INDEX IDX_26A98456B842C572 (idOffre), INDEX IDX_26A98456EA0ECF81 (idCours), PRIMARY KEY(id_achat)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaire (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, contenu VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, INDEX IDX_67F068BCA76ED395 (user_id), INDEX IDX_67F068BC4B89032C (post_id), INDEX IDX_67F068BC727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cours (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, prix DOUBLE PRECISION NOT NULL, id_utilisateur INT DEFAULT NULL, type_achat VARCHAR(255) NOT NULL, id_offre INT NOT NULL, id_cours INT NOT NULL, date_achat DATETIME NOT NULL, datefin DATE NOT NULL, satus VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nom_cours (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, duree VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, id_user_id INT DEFAULT NULL, contenu VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_5A8A6C8D79F37AE5 (id_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, nom_cours_id INT DEFAULT NULL, texte LONGTEXT DEFAULT NULL, INDEX IDX_B6F7494EEB85BD39 (nom_cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, nom_cours_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, INDEX IDX_A412FA92EB85BD39 (nom_cours_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz_question (quiz_id INT NOT NULL, question_id INT NOT NULL, INDEX IDX_6033B00B853CD175 (quiz_id), INDEX IDX_6033B00B1E27F6BF (question_id), PRIMARY KEY(quiz_id, question_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reaction (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, commentaire_id INT DEFAULT NULL, emoji VARCHAR(10) NOT NULL, INDEX IDX_A4D707F7A76ED395 (user_id), INDEX IDX_A4D707F74B89032C (post_id), INDEX IDX_A4D707F7BA9CD190 (commentaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, texte LONGTEXT DEFAULT NULL, est_correcte TINYINT(1) DEFAULT NULL, INDEX IDX_5FB6DEC71E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, role VARCHAR(20) NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_de_naissance DATE NOT NULL, num_tel VARCHAR(20) NOT NULL, adresse VARCHAR(255) NOT NULL, bio LONGTEXT DEFAULT NULL, photo_de_profil VARCHAR(255) DEFAULT NULL, date_inscrit DATETIME NOT NULL, discriminator VARCHAR(255) NOT NULL, specialite VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_reponse (id INT AUTO_INCREMENT NOT NULL, quiz_id INT DEFAULT NULL, user_id INT DEFAULT NULL, question_id INT DEFAULT NULL, reponse_id INT DEFAULT NULL, est_correcte TINYINT(1) DEFAULT NULL, INDEX IDX_7BBC0CD853CD175 (quiz_id), INDEX IDX_7BBC0CDA76ED395 (user_id), INDEX IDX_7BBC0CD1E27F6BF (question_id), INDEX IDX_7BBC0CDCF18BB82 (reponse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE achat ADD CONSTRAINT FK_26A98456B842C572 FOREIGN KEY (idOffre) REFERENCES offre (id)');
        $this->addSql('ALTER TABLE achat ADD CONSTRAINT FK_26A98456EA0ECF81 FOREIGN KEY (idCours) REFERENCES cours (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BC727ACA70 FOREIGN KEY (parent_id) REFERENCES commentaire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D79F37AE5 FOREIGN KEY (id_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EEB85BD39 FOREIGN KEY (nom_cours_id) REFERENCES nom_cours (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92EB85BD39 FOREIGN KEY (nom_cours_id) REFERENCES nom_cours (id)');
        $this->addSql('ALTER TABLE quiz_question ADD CONSTRAINT FK_6033B00B853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz_question ADD CONSTRAINT FK_6033B00B1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F7A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F74B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE reaction ADD CONSTRAINT FK_A4D707F7BA9CD190 FOREIGN KEY (commentaire_id) REFERENCES commentaire (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC71E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE user_reponse ADD CONSTRAINT FK_7BBC0CD853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE user_reponse ADD CONSTRAINT FK_7BBC0CDA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_reponse ADD CONSTRAINT FK_7BBC0CD1E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE user_reponse ADD CONSTRAINT FK_7BBC0CDCF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achat DROP FOREIGN KEY FK_26A98456B842C572');
        $this->addSql('ALTER TABLE achat DROP FOREIGN KEY FK_26A98456EA0ECF81');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA76ED395');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC4B89032C');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BC727ACA70');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D79F37AE5');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EEB85BD39');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92EB85BD39');
        $this->addSql('ALTER TABLE quiz_question DROP FOREIGN KEY FK_6033B00B853CD175');
        $this->addSql('ALTER TABLE quiz_question DROP FOREIGN KEY FK_6033B00B1E27F6BF');
        $this->addSql('ALTER TABLE reaction DROP FOREIGN KEY FK_A4D707F7A76ED395');
        $this->addSql('ALTER TABLE reaction DROP FOREIGN KEY FK_A4D707F74B89032C');
        $this->addSql('ALTER TABLE reaction DROP FOREIGN KEY FK_A4D707F7BA9CD190');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC71E27F6BF');
        $this->addSql('ALTER TABLE user_reponse DROP FOREIGN KEY FK_7BBC0CD853CD175');
        $this->addSql('ALTER TABLE user_reponse DROP FOREIGN KEY FK_7BBC0CDA76ED395');
        $this->addSql('ALTER TABLE user_reponse DROP FOREIGN KEY FK_7BBC0CD1E27F6BF');
        $this->addSql('ALTER TABLE user_reponse DROP FOREIGN KEY FK_7BBC0CDCF18BB82');
        $this->addSql('DROP TABLE achat');
        $this->addSql('DROP TABLE commentaire');
        $this->addSql('DROP TABLE cours');
        $this->addSql('DROP TABLE nom_cours');
        $this->addSql('DROP TABLE offre');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE quiz_question');
        $this->addSql('DROP TABLE reaction');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_reponse');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
