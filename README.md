# TRTConseil [Lien]([https://trt-conseil-recrutement.herokuapp.com/)

J'ai réalisé ce projet, dans le cadre de ma formation de Développeur Web, pour valider mes compétences acquises. 

L’objectif du projet est d’analyser les besoins et de développer l’application. Seul la partie backend de mon site a été évalué. Les langages utilisés sont principalement du PHP avec le framework Symfony.

Toujours en cours de développement(la partie candidat, admin et quelques fonctionnalités) mais le site est en ligne.
## Code d'Accès 
---
    Recruteur
        id  : recruteur1@exemple.fr
        mdp : recruteur1

    Candidat
        id  : candidat1@exemple.fr
        mdp : candidat1

---

## Projet

La société TRTConseil(Société Fictive) souhaite progressivement mettre en place un outil permettant à un plus grand nombre de recruteurs et de candidats de trouver leur bonheur.

TRT Conseil désire avoir un produit minimum viable afin de tester si la demande est réellement présente. L’agence souhaite proposer pour l’instant une simple interface avec une authentification.

Cette application est accessible par tous mais si vous souhaitez postuler ou recruter, une inscription est obligatoire.

### Analyse des besoins
Il y a 4 acteurs:

    -   Les recruteurs => entreprises qui recherchent un employé.

    -   Les candidats => personnes à la recherche d'un emploi.

    -   Les consultants => missionnés par TRTConseil pour gérer la laison sur le back-office entre candidats et recruteurs. 
    Ils valideront les créations de compte et les offres d'emploi.

    -   L'administrateur => Personne en charge de la maintenance de l'application.


Les fonctionnalités désirées sont :

    -   US1. Création de compte d'utilisateur (recruteurs, candidats).
    -   US2. Se connecter (4 acteurs).
    -   US3. Compléter son profil (recruteurs, candidats).
    -   US4. Publier une annonce (recruteurs).
    -   US5. Postuler à une annonces (candidats).
    -   US6. Création d'un consultant (administrateur).

Le client souhaite une simple interface avec une authentification.

J'ai pu créé les personas de l'application afin de les intégrer aux Users Stories. Les Mockups ont été réalisé grâce aux Wireframes et à la planche de style.

Pour m'assurer que les besoins ont bien été identifiés, j'ai retranscrit les fonctionnalités sous forme de diagrammes (cas d'utilisation, de séquence) et j’ai utilisé la méthode Merise pour bien construire ma base de données.


### Outils utilisés

Le design pattern MVC m'a permis de bien séparer les différentes parties du code de mon application.

---
    -   Pour le traitement des données ou de l'accès au données (Model)
            Doctrine - ORM


    -   Pour la mise en forme des données (View)
            Twig - Moteur de template


    -   Pour permettre de gérer la logique du code associé à une page (Controller)
            PHP et Symfony
---





