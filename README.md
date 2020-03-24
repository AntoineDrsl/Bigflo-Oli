# Bigflo-Oli

## Description :
Blog présentant la vie et les oeuvres des rappeurs Bigflo & Oli.

Les utilisateurs peuvent se créer un compte, modifier leurs informations, poster un commentaire et créer un article, soumis ensuite à la modération de l'admin.
L'admin peut modifier ses informations, créer un article (directement accepté du coup), valider ou refuser les articles proposés par les users, modifier et supprimer les articles et supprimer les commentaires.

## Pour lancer l'application :

Installer les plugins
`composer install`

Installer la base de données (Wamp ou autre ouvert)
`php bin/console doctrine:database:create`
`php bin/console doctrine:migrations:migrate`

Lancer le projet
`symfony server:start`

## Structure de l'appication :

Le projet est un projet Symfony classique.

On utilise 4 controllers:
- MainController, qui s'occupe des routes accessibles par tous (dossier 'main' des templates)
- AccountController, qui s'occupe des routes accessibles par les users et l'admin (dossier 'account' des templates)
- RegistrationController, qui s'occupe de la création de compte (dossier 'registration' des templates)
- SecurityController, qui s'occupe du login/logout (dossier 'security' des templates)

La bdd est composée de 3 entités:
- User, stockant les comptes utilisateurs
- Article, stockant tous les articles créés
- Comment, stockant tous les commentaires, leur user et l'article lié

Les routes sont définies au dessus de chaque fonction dans les controllers.
Les permissions des routes sont gérées dans 'config/packages/security.yaml'.
Les contraintes des formulaires et messages d'erreur liés sont gérées dans les entités, au dessus de chaque champ (excepté pour l'inscription et la modification d'informations, où les contraintes sont gérées directement dans les formulaires).

Chaque vue dispose de sa propre feuille de style CSS (dans 'public/assets/css') pour que le code soit plus lisible et optimisé. De même pour le JS.
Les vues d'erreur sont gérées dans 'templates/bundles/TwigBundle/Exception'.

## Licence :

Antoine Durussel