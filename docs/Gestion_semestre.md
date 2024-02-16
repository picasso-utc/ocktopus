# Gestion des semestres - Documentation Ocktopus

## Les services 
* Déterminer le semestre actif et connaître ses dates de début et de fin 
* Pouvoir créer tous les créneaux correspondant à un semestre

## Le fonctionnement général
Les semestres permettent d'organiser le back-office. Un seul semestre peut en effet être actif, et beaucoup de choses dans le back-office s'organise en fonction de ça, le planning, les astreintes, les perms ect.
Il doit donc pouvoir être possible de créer un semestre afin de l'initialiser, on termine l'initialisation en créant des semestres (qui ne peuvent être crées qu'une fois), et il doit être possible de rendre un semestre actif, ce qui les rend obligatoirements tous les autres semestres inactif

## Le modèle
Un semestre est caractérisé par un *state* (A23 ect.), une date de début, une date de fin et surtout un boolén actif, qui détermine si le semestre est actif. Notons qu'un seul semestre doit pouvoir être actif au maximum.
```php=
 Schema::create('semestres', function (Blueprint $table) {
    $table->id();
    $table->string('state', 3)->unique();
    $table->date('startOfSemestre')->unique();
    $table->date('endOfSemestre')->unique();
    $table->boolean('activated')->default(0);
    $table->timestamps();
```

### Filament Resource (SemestreResource) : 

 Cette ressource est utilisée pour gérer les semestres, afficher des informations, et effectuer des actions associées.
 
 #### Attributs spécifiquees

- **$model** : Le modèle associé à cette ressource (ici, `Semestre::class`).
- **$navigationIcon** : L'icône à afficher dans la barre de navigation (ici, 'heroicon-o-arrow-path').
- **$navigationGroup** : Le groupe auquel appartient la ressource dans la barre de navigation (ici, 'General').


#### Actions personnalisées

La ressource déclare deux actions personnalisées :

1. **CreateCreneaux** : Action permettant de créer des créneaux pour le semestre actuel.
2. **MakeActif** : Action permettant de rendre le semestre spécifié actif, désactivant les autres.

##### `handleCreateSemestre($record)`

Cette méthode gère la création des créneaux pour le semestre actuel. Elle vérifie d'abord s'il existe déjà des créneaux pour la date de début du semestre, et si non, elle utilise le contrôleur `CreneauController` pour les créer. Des notifications sont affichées en fonction du résultat.

##### `handleMakeActif($record)`

Cette méthode rend le semestre spécifié actif en désactivant tous les autres semestres.
