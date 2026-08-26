<div align="center">

# Workflow Platform

**Moteur de workflow métier configurable** — circuits de validation
dynamiques, formulaires paramétrables et routage conditionnel, sans
écrire une ligne de code pour chaque nouveau processus
*Saint-Gobain Maroc — Stage au sein du département IT*

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)
![Tailwind](https://img.shields.io/badge/Tailwind%20CSS-4-06B6D4?logo=tailwindcss&logoColor=white)
![Status](https://img.shields.io/badge/statut-pr%C3%AAt%20pour%20d%C3%A9ploiement-success)

</div>

---

## Auteurs

**Mariam LACHGAR** — Génie Informatique
**Mohammed SADIK** — Ingénierie des Données

*(Ètudiants à l'ENSA KHOURIBGA)*

**Cadre du stage**

Stage réalisé en binôme du 01/07/2026 au 31/08/2026, sous l’encadrement de **M.KHATTABI FILALI Amine**, IT Manager au sein du département IT de Saint-Gobain Maroc.

L’analyse fonctionnelle et la modélisation Merise ont été réalisées conjointement par les deux stagiaires. À l’issue de cette phase, le développement a été réparti entre les deux membres du binôme selon les différents domaines techniques du projet.

## Contexte et objectif

Dans le cadre de ses activités, **Saint-Gobain Maroc s’appuie déjà sur différents systèmes et outils informatiques pour accompagner ses processus métiers**. Toutefois, selon les domaines et les types de demandes, les modalités de traitement peuvent varier : certains processus sont pris en charge par des applications existantes, tandis que d’autres reposent encore sur des échanges de documents, des e-mails ou des interventions manuelles entre les différents acteurs concernés.

Le projet **Workflow Platform** s’inscrit dans cette perspective d’optimisation. Il vise à proposer une solution permettant de **structurer, centraliser et automatiser les processus métiers nécessitant plusieurs niveaux de validation**, en offrant un cadre homogène pour leur conception et leur suivi.

Le premier cas d’usage porte sur la **gestion des demandes d’ouverture et de modification de comptes clients**. L’objectif n’est pas simplement de remplacer un formulaire existant par une version numérique, mais de mettre en place un **véritable moteur de workflow configurable**.

Depuis une interface dédiée, **un Administrateur métier** peut notamment :

définir la structure et les champs d’un formulaire ;
configurer les différentes étapes du processus de validation ;
définir les acteurs responsables de chaque étape selon leur fonction, leur niveau hiérarchique ou leur appartenance à une entité ;
paramétrer des règles de routage conditionnel en fonction des caractéristiques de la demande, telles que des seuils ou la marque du client ;
suivre l’état et l’avancement des demandes au sein du processus.

Cette approche permet de **faire évoluer les processus métiers par configuration**, sans nécessiter de développement spécifique à chaque modification ou création d’un nouveau circuit de validation.

Conçu dès son origine comme une solution **générique, configurable et réutilisable**, Workflow Platform ne se limite donc pas au traitement des demandes liées aux comptes clients. Son architecture permet d’envisager son adaptation à différents processus de l’entreprise nécessitant des circuits de validation structurés, notamment dans les domaines des **ressources humaines, des achats, des contrats ou d’autres processus internes**.

L'objectif est ainsi de fournir une **plateforme commune de gestion des workflows**, capable de s'intégrer dans l'écosystème applicatif existant et de compléter les solutions déjà en place, tout en améliorant la standardisation, la traçabilité et la visibilité des processus concernés.

## Sommaire

- [Points forts techniques](#points-forts-techniques)
- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Architecture](#architecture)
- [Sécurité](#sécurité)
- [Installation en local](#installation-en-local)
- [Configuration de l'e-mail](#configuration-de-lemail)
- [Tâche planifiée (rappels quotidiens)](#tâche-planifiée-rappels-quotidiens)
- [Déploiement en production](#déploiement-en-production)
- [Structure du projet](#structure-du-projet)
- [Pistes d'évolution](#pistes-dévolution)

## Points forts techniques

Quelques décisions de conception qui vont au-delà d'un CRUD classique :

- **Moteur de résolution de validateur unifié** — un même service
  (`ValidatorResolverService`) résout indifféremment un validateur par
  Fonction Métier, par Utilisateur désigné, par Responsable
  hiérarchique (N+1), ou par Responsable d'Entité/Département. Chaque
  Type de validateur est un cas du même moteur, pas une bifurcation de
  code séparée — ajouter un nouveau type de routage à l'avenir se fait
  à un seul endroit.
- **Escalade hiérarchique correcte sur plusieurs étapes** — quand un
  workflow enchaîne plusieurs étapes "Responsable N+1", chacune
  escalade réellement d'un niveau hiérarchique supplémentaire (calculé
  à partir de l'historique réel de validation de la demande), au lieu
  de redemander systématiquement le même responsable direct.
- **Conditions de transition génériques** — le routage conditionnel
  (ex. seuil de montant, marque du client) n'est pas codé en dur : un
  Administrateur définit ces règles depuis l'interface, sur n'importe
  quel champ numérique ou liste déroulante d'un formulaire.
- **Formulaires versionnés et non modifiables une fois publiés** — une
  fois qu'une Demande a été créée à partir d'un Formulaire, sa
  structure ne peut plus changer sous elle ; toute évolution passe par
  une duplication, garantissant l'intégrité des données historiques.
- **Séparation stricte des responsabilités** — Repository (accès
  données) / Service (règles métier) / Policy (autorisation) / DTO
  (transport de données validées) / Exception métier dédiée par cas
  d'erreur, plutôt que de la logique dispersée dans les contrôleurs.
- **Auto-inscription sécurisée** — un compte créé via le formulaire
  public reste inactif tant qu'un Administrateur ne l'approuve pas
  explicitement (avec possibilité d'ajuster son rattachement
  organisationnel et son rôle) ; l'attribution de rôle applicatif n'est
  **jamais** laissée au choix de la personne qui s'inscrit.

## Fonctionnalités

| Domaine | Détail |
|---|---|
| **Workflows** | Étapes illimitées, 5 types de validateur, transitions conditionnelles avec seuils, transition par défaut, notifications de clôture configurables |
| **Formulaires** | 8 types de champ (texte, nombre, montant, date, e-mail, mot de passe, liste déroulante avec option libre, fichier), regroupement par section, réutilisation d'un formulaire existant |
| **Demandes** | Brouillon avec sauvegarde automatique, assistant de saisie par section pour les formulaires longs, pièces jointes, suivi de statut en temps réel |
| **Validation** | File d'attente personnalisée par validateur, historique de décisions filtrable, notifications automatiques à chaque étape |
| **Utilisateurs** | Rôles applicatifs multiples avec bascule de session, hiérarchie N+1, inscription publique avec approbation, suivi de dernière connexion |
| **Organisation** | Entités, Départements, Fonctions Métier, avec responsables assignables |
| **Pilotage** | Tableaux de bord par rôle, historique d'audit complet, filtres et recherche partout |

## Stack technique

| Choix | Justification |
|---|---|
| **Laravel 12** | Framework PHP le plus utilisé et documenté du marché ; écosystème mature (validation, autorisation, ORM, planification de tâches) évitant de réinventer des briques critiques (sécurité, sessions) |
| **MySQL** | Standard d'entreprise, compatible avec la quasi-totalité des infrastructures d'hébergement |
| **Blade + Tailwind CSS** | Rendu côté serveur (pas de build front séparé complexe), interface cohérente et rapide à faire évoluer |
| **Vite** | Rechargement instantané en développement, build optimisé en production |

## Architecture

```
app/
├── Http/Controllers/       # Un contrôleur = une responsabilité HTTP, aucune règle métier
│   ├── Workflow/           # Workflows, Formulaires, Demandes, Validations
│   └── Organisation/       # Utilisateurs, Entités, Départements, Auth
├── Services/                # Toute la logique métier (WorkflowEngineService,
│                             # ValidatorResolverService, UserService...)
├── Repositories/            # Accès aux données, derrière une interface (testable,
│                             # remplaçable sans toucher aux Services)
├── Policies/                # Qui a le droit de faire quoi, centralisé
├── DataTransferObjects/      # Données validées transportées entre les couches
├── Exceptions/               # Une exception métier dédiée par cas d'erreur réel
│   │                          # (jamais d'exception générique avalée silencieusement)
├── Enums/                    # États et types représentés par le système de types
│   │                          # PHP, pas par des chaînes de caractères libres
└── Console/Commands/          # Tâches planifiées et commandes d'administration
```

Cette séparation permet à chaque couche d'être testée et modifiée
indépendamment — un changement de règle métier se fait dans un
Service, jamais dans un Contrôleur ou une Vue.

## Sécurité

- Mots de passe hashés (bcrypt), jamais stockés ni journalisés en clair.
- Autorisation vérifiée à chaque action via les Policies Laravel — un
  Validateur ne peut décider que sur les Demandes qu'il est
  effectivement habilité à traiter, un Administrateur ne peut pas
  modifier un Workflow déjà publié directement (BR-26).
- Auto-inscription : impossible de s'attribuer soi-même un rôle
  applicatif (le formulaire public ne propose plus ce choix, le compte
  reste inactif jusqu'à approbation).
- Domaine e-mail d'inscription restreint (`WORKFLOW_COMPANY_EMAIL_DOMAINS`).
- Fichiers joints servis via un contrôleur authentifié (jamais un accès
  disque direct) — accès révérifié à chaque téléchargement.
- Protection CSRF (native Laravel) sur tous les formulaires.

## Installation en local

```bash
git clone https://github.com/Mariam0106/workflow_platform.git
cd workflow_platform

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configurer dans `.env` :
- `DB_*` avec les identifiants d'une base MySQL locale
- `WORKFLOW_COMPANY_EMAIL_DOMAINS=saint-gobain.com` (domaines autorisés
  à l'inscription)

```bash
php artisan migrate
php artisan app:create-admin   # crée le premier compte Administrateur
```

Lancer l'application (deux terminaux séparés) :
```bash
php artisan serve
npm run dev
```

## Configuration de l'e-mail

L'application envoie de vrais e-mails (approbation/refus d'inscription,
rappels de validation) via SMTP standard — **aucun code à modifier**,
tout se règle dans `.env` :

```env
MAIL_MAILER=smtp
MAIL_HOST=<hôte du service choisi>
MAIL_PORT=<port>
MAIL_USERNAME=<identifiant>
MAIL_PASSWORD=<mot de passe>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@saint-gobain.com"
MAIL_FROM_NAME="Workflow Platform"
```

En développement, ce projet a été testé avec
[Mailtrap](https://mailtrap.io) (boîte de réception factice, aucun
e-mail réel envoyé, sans risque pour de vrais collaborateurs pendant
les tests). En production, remplacer par le service SMTP réellement
choisi par l'entreprise (serveur interne, ou un service comme
SendGrid/Amazon SES/Mailgun) — même méthode, seules ces variables
changent.


## Tâche planifiée (rappels quotidiens)

Un rappel automatique (e-mail + notification) est envoyé chaque jour à
9h à tout Validateur ayant des demandes en attente
(`workflow:check-reminders`, déclarée dans `routes/console.php`,
réutilisant la même logique de résolution que l'écran "Mes
validations" pour rester cohérente avec ce que voit réellement chaque
Validateur).

Pour que ceci s'exécute réellement en production, le serveur doit
avoir **une seule ligne** dans son crontab :

```cron
* * * * * cd /chemin/vers/le/projet && php artisan schedule:run >> /dev/null 2>&1
```

Testable manuellement à tout moment, sans attendre 9h ni configurer de
planificateur :
```bash
php artisan workflow:check-reminders
```

## Déploiement en production

Ce dépôt est prêt à être déployé. La mise en production sur
l'infrastructure de l'entreprise (choix du serveur, durcissement
réseau, sauvegardes) relève naturellement de l'équipe IT/infra ; voici
les étapes attendues côté application.

1. **Prérequis serveur**
   - PHP 8.2 ou supérieur
   - MySQL 8+
   - Composer
   - Node.js (uniquement pour la build des assets, pas nécessaire à
     l'exécution)
   - Un vrai serveur web (Apache ou Nginx) — `php artisan serve` est un
     outil de développement, jamais utilisé en production

2. **Récupération et préparation du code**
   ```bash
   git clone https://github.com/Mariam0106/workflow_platform.git
   cd workflow_platform
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   ```

3. **Configuration `.env` de production**
   - `APP_ENV=production`
   - `APP_DEBUG=false` — **impératif**, sans quoi les erreurs internes
     (requêtes SQL, chemins serveur) seraient visibles publiquement
   - `APP_URL=https://<domaine réel>`
   - Identifiants MySQL réels
   - Configuration e-mail réelle (voir section précédente)

4. **Initialisation de la base**
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan app:create-admin
   ```

5. **Optimisation** (production uniquement, jamais en développement)
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. **Serveur web** : la racine documentaire doit pointer vers le
   dossier `public/` du projet, jamais la racine du dépôt.

7. **HTTPS obligatoire** — l'application manipule mots de passe et
   données clients ; un certificat SSL (Let's Encrypt convient,
   gratuit) est non négociable.

8. **Tâche planifiée** — voir la section précédente (une ligne de
   crontab).

## Structure du projet

```
app/
├── Console/Commands/        # Commandes d'administration et tâches planifiées
├── DataTransferObjects/     # Objets de transfert de données validées
├── Enums/                   # États et types métier (13 énumérations)
├── Events/ Listeners/       # Notifications déclenchées par les événements du workflow
├── Exceptions/              # Exceptions métier dédiées (22 classes)
├── Http/Controllers/
│   ├── Organisation/        # Utilisateurs, Entités, Départements, Authentification
│   └── Workflow/            # Workflows, Formulaires, Demandes, Validations
├── Http/Requests/           # Validation des entrées, une classe par action
├── Mail/                    # Mailables (inscription, rappels)
├── Models/                  # Modèles Eloquent
├── Policies/                # Autorisation, centralisée (11 classes)
├── Repositories/            # Accès aux données, derrière une interface
├── Services/                # Logique métier
└── ValueObjects/            # Types métier immuables (email d'entreprise, référence...)

database/migrations/         # Historique complet des évolutions de schéma (34 migrations)
resources/views/             # Vues Blade, organisées par domaine métier
routes/                      # Routes HTTP, groupées par domaine
```

## Pistes d'évolution

- **Escalade de validation** — permettre à un Validateur de transmettre
  une décision à son propre responsable hiérarchique lorsqu'elle
  dépasse son niveau de décision, avec justification obligatoire
  (conception validée, en attente d'arbitrage métier avant
  implémentation).
- Notifications par canal supplémentaire (SMS, Teams).
- Export des historiques de demandes (PDF/Excel).

---

<div align="center">

*Projet développé dans le cadre d'un stage — Département IT, Saint-Gobain Maroc*

</div>