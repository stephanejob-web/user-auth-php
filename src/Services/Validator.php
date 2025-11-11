<?php
/**
 * ============================================================================
 * CLASSE VALIDATOR - SERVICE DE VALIDATION
 * ============================================================================
 *
 * Cette classe centralise toutes les validations de données.
 * Elle utilise des méthodes statiques pour faciliter l'utilisation.
 *
 * Avantages :
 * - Code réutilisable et centralisé
 * - Facile à tester
 * - Facilite la maintenance
 * - Évite la duplication de code de validation
 */

namespace App\Services;

class Validator
{
    /**
     * Tableau pour stocker les erreurs de validation
     * @var array
     */
    private array $errors = [];

    /**
     * Valide un email
     *
     * @param string $email Email à valider
     * @param string $fieldName Nom du champ (pour les messages d'erreur)
     * @return bool True si valide, False sinon
     */
    public function validateEmail(string $email, string $fieldName = 'Email'): bool
    {
        // Vérifier si l'email est vide
        if (empty($email)) {
            $this->errors[] = "$fieldName is required.";
            return false;
        }

        // Vérifier le format de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid $fieldName format.";
            return false;
        }

        return true;
    }

    /**
     * Valide un mot de passe selon les règles de sécurité
     *
     * Règles :
     * - Minimum 8 caractères
     * - Au moins une majuscule
     * - Au moins un caractère spécial
     *
     * @param string $password Mot de passe à valider
     * @param string $fieldName Nom du champ (pour les messages d'erreur)
     * @return bool True si valide, False sinon
     */
    public function validatePassword(string $password, string $fieldName = 'Password'): bool
    {
        // Vérifier si le mot de passe est vide
        if (empty($password)) {
            $this->errors[] = "$fieldName is required.";
            return false;
        }

        // Vérifier la longueur minimale (8 caractères)
        if (strlen($password) < 8) {
            $this->errors[] = "$fieldName must be at least 8 characters long.";
            return false;
        }

        // Vérifier la présence d'au moins une majuscule
        if (!preg_match('/[A-Z]/', $password)) {
            $this->errors[] = "$fieldName must contain at least one uppercase letter.";
            return false;
        }

        // Vérifier la présence d'au moins un caractère spécial
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $this->errors[] = "$fieldName must contain at least one special character.";
            return false;
        }

        return true;
    }

    /**
     * Vérifie que deux mots de passe correspondent
     *
     * @param string $password Premier mot de passe
     * @param string $confirmPassword Confirmation du mot de passe
     * @return bool True si identiques, False sinon
     */
    public function validatePasswordMatch(string $password, string $confirmPassword): bool
    {
        if ($password !== $confirmPassword) {
            $this->errors[] = "Passwords do not match.";
            return false;
        }

        return true;
    }

    /**
     * Vérifie qu'une chaîne n'est pas vide
     *
     * @param string $value Valeur à vérifier
     * @param string $fieldName Nom du champ
     * @return bool True si non vide, False sinon
     */
    public function validateRequired(string $value, string $fieldName): bool
    {
        if (empty($value)) {
            $this->errors[] = "$fieldName is required.";
            return false;
        }

        return true;
    }

    /**
     * Vérifie qu'un nombre est positif
     *
     * @param int $value Valeur à vérifier
     * @param string $fieldName Nom du champ
     * @return bool True si positif, False sinon
     */
    public function validatePositiveInteger(int $value, string $fieldName): bool
    {
        if ($value <= 0) {
            $this->errors[] = "$fieldName must be a positive integer.";
            return false;
        }

        return true;
    }

    /**
     * Retourne toutes les erreurs de validation
     *
     * @return array Tableau des erreurs
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Retourne la première erreur (pour affichage simple)
     *
     * @return string|null Première erreur ou null si aucune
     */
    public function getFirstError(): ?string
    {
        return $this->errors[0] ?? null;
    }

    /**
     * Vérifie s'il y a des erreurs
     *
     * @return bool True s'il y a des erreurs, False sinon
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Réinitialise les erreurs
     */
    public function clearErrors(): void
    {
        $this->errors = [];
    }

    /**
     * Nettoie une chaîne (trim + htmlspecialchars)
     *
     * @param string $value Valeur à nettoyer
     * @return string Valeur nettoyée
     */
    public static function sanitize(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Nettoie un email (trim + strtolower)
     *
     * @param string $email Email à nettoyer
     * @return string Email nettoyé
     */
    public static function sanitizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }
}

/*
===============================================================================
NOTES PÉDAGOGIQUES - CLASSE VALIDATOR
===============================================================================

1. POURQUOI UNE CLASSE VALIDATOR ?

   Avant (code procédural) :
   - Code de validation dupliqué dans chaque page
   - Difficile à maintenir et à tester
   - Incohérence possible entre les pages

   Après (POO) :
   - Validation centralisée en un seul endroit
   - Code réutilisable
   - Facile à tester et à maintenir
   - Cohérence garantie

2. COMMENT UTILISER CETTE CLASSE ?

   use App\Services\Validator;

   // Créer une instance
   $validator = new Validator();

   // Valider un email
   if (!$validator->validateEmail($email)) {
       echo $validator->getFirstError();
   }

   // Valider un mot de passe
   if (!$validator->validatePassword($password)) {
       echo $validator->getFirstError();
   }

   // Valider que les mots de passe correspondent
   if (!$validator->validatePasswordMatch($password, $confirmPassword)) {
       echo $validator->getFirstError();
   }

   // Vérifier s'il y a des erreurs
   if ($validator->hasErrors()) {
       $errors = $validator->getErrors();
       foreach ($errors as $error) {
           echo $error . "<br>";
       }
   }

3. MÉTHODES STATIQUES vs MÉTHODES D'INSTANCE

   Méthodes d'instance (validateEmail, validatePassword, etc.) :
   - Nécessitent une instance : $validator = new Validator()
   - Peuvent stocker un état (tableau $errors)
   - Utilisées pour des validations qui accumulent des erreurs

   Méthodes statiques (sanitize, sanitizeEmail) :
   - Peuvent être appelées sans instance : Validator::sanitize($value)
   - Ne peuvent pas stocker d'état
   - Utilisées pour des opérations utilitaires simples

4. GESTION DES ERREURS

   La classe stocke toutes les erreurs dans un tableau :
   - $this->errors[] = "message" : ajoute une erreur
   - getErrors() : retourne toutes les erreurs
   - getFirstError() : retourne la première erreur
   - hasErrors() : vérifie s'il y a des erreurs
   - clearErrors() : réinitialise les erreurs

5. EXEMPLE COMPLET D'UTILISATION

   // Dans register.php
   use App\Services\Validator;

   $validator = new Validator();

   // Valider l'email
   $validator->validateEmail($email);

   // Valider le mot de passe
   $validator->validatePassword($password);

   // Valider la correspondance
   $validator->validatePasswordMatch($password, $confirmPassword);

   // Vérifier s'il y a des erreurs
   if ($validator->hasErrors()) {
       $error = $validator->getFirstError();
       // Afficher l'erreur
   } else {
       // Procéder à l'inscription
   }

6. SANITIZATION (NETTOYAGE)

   sanitize() : nettoie une chaîne de caractères
   - trim() : supprime les espaces
   - htmlspecialchars() : échappe les caractères HTML (protection XSS)

   sanitizeEmail() : nettoie un email
   - trim() : supprime les espaces
   - strtolower() : convertit en minuscules

7. AVANTAGES DE CETTE APPROCHE

   ✅ Code DRY (Don't Repeat Yourself)
   ✅ Facile à tester unitairement
   ✅ Facile à étendre (ajouter de nouvelles validations)
   ✅ Messages d'erreur cohérents
   ✅ Séparation des responsabilités

===============================================================================
*/
