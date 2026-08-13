<p align="center">
  <img src="ressources/logo.png" alt="Logo Meteastro" width="200">
</p>

# Contribuer à Meteastro

Merci de l'intérêt porté à **Meteastro** ! Ce document explique comment contribuer au projet, que ce soit au site web ou à l'application (PWA).

## Sommaire

- [À propos du projet](#à-propos-du-projet)
- [Stack technique](#stack-technique)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Structure du projet](#structure-du-projet)
- [Schéma de versioning](#schéma-de-versioning)
- [Workflow de contribution](#workflow-de-contribution)
- [Conventions de code](#conventions-de-code)
- [Signaler un bug](#signaler-un-bug)
- [Proposer une fonctionnalité](#proposer-une-fonctionnalité)

## À propos du projet

Meteastro est un projet composé de deux volets distincts :

- **Le site web**
- **L'application / PWA** (Progressive Web App)

Ces deux volets partagent une base commune mais ont chacun leur propre version (voir la section [Schéma de versioning](#schéma-de-versioning)).

## Stack technique

- **Back-end** : PHP
- **Front-end** : JavaScript vanilla (pas de framework)
- **App/PWA** : Service Worker + JavaScript vanilla

## Prérequis

- PHP (version à préciser selon l'environnement du projet)
- Un serveur local (ex. serveur intégré PHP, Apache, ou autre)
- Un navigateur récent supportant les Service Workers (pour tester la PWA)

## Installation

```bash
# Cloner le dépôt
git clone <url-du-dépôt>
cd meteastro

# Lancer un serveur PHP local
php -S localhost:8000
```

> Adaptez cette section selon la configuration réelle du projet (base de données, variables d'environnement, etc.) au fur et à mesure que la structure se précise.

## Structure du projet

La structure du dépôt n'est pas encore figée. Elle sera documentée ici au fur et à mesure de sa stabilisation (dossiers `src/`, `app/`, `docs/`, etc.).

## Schéma de versioning

Meteastro utilise un schéma de versioning spécifique, à respecter pour toute contribution touchant à une release :

- **Version du site** : affichée en 3 chiffres, ex. `2.4.1`. Elle est modifiée **manuellement** dans le code (aucun outil type `standard-version` n'est utilisé).
- **Version de l'app/PWA** : reprend la version du site en y ajoutant un 4e chiffre propre à l'app, ex. `2.4.1.0`.
  - Ce 4e chiffre s'incrémente indépendamment lors des mises à jour spécifiques à l'app/PWA.
  - Le `CACHE_NAME` du Service Worker et l'`appVersion` en JS doivent toujours être **synchronisés** sur cette version à 4 chiffres.
- **`version.php`** doit également contenir la **date de mise à jour** (jour de la semaine, numéro du jour, mois, année).

**Checklist avant toute release :**

- [ ] Mettre à jour la version du site (3 chiffres) si le site est impacté
- [ ] Mettre à jour la version de l'app (4e chiffre) si l'app/PWA est impactée
- [ ] Synchroniser `CACHE_NAME` (Service Worker) et `appVersion` (JS)
- [ ] Mettre à jour la date dans `version.php`

## Workflow de contribution

1. **Forkez** le dépôt et créez votre branche à partir de `main` (ou de la branche de développement en vigueur).
2. Nommez votre branche de façon descriptive, ex. `fix/nom-du-bug` ou `feature/nom-fonctionnalite`.
3. Effectuez vos modifications en respectant les [conventions de code](#conventions-de-code).
4. Testez vos changements localement, sur le site **et** sur l'app/PWA si pertinent (y compris le comportement du Service Worker).
5. Mettez à jour la version et `version.php` si votre contribution justifie une nouvelle release (voir [Schéma de versioning](#schéma-de-versioning)).
6. Committez avec un message clair et explicite.
7. Ouvrez une **Pull Request** en décrivant :
   - le problème résolu ou la fonctionnalité ajoutée,
   - les éventuels impacts sur le site et/ou l'app,
   - comment tester les changements.

## Conventions de code

- Code **PHP** et **JS vanilla** : privilégier un style clair et cohérent avec le code existant.
- Éviter d'introduire des dépendances/frameworks lourds sans discussion préalable, le projet restant volontairement en vanilla.
- Commenter les portions de code non triviales, notamment tout ce qui touche au Service Worker et à la gestion du cache.

## Signaler un bug

Avant d'ouvrir une issue :

- Vérifiez qu'un problème similaire n'a pas déjà été signalé.
- Précisez si le bug concerne le **site**, l'**app/PWA**, ou les deux.
- Indiquez la version concernée (site et/ou app), votre navigateur, et les étapes de reproduction.

## Proposer une fonctionnalité

Ouvrez une issue décrivant :

- le besoin ou le problème que la fonctionnalité résout,
- si elle concerne le site, l'app, ou les deux,
- toute contrainte technique connue.

---

Merci de contribuer à Meteastro ! 🌠
