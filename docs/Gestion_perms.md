# Gestion des perms - Documentation Ocktopus

*Dans cette doc, vous trouverez :*
- Modèles de données :
        Vous avez des modèles pour les Perms, les Créneaux, les Astreintes et les Semestres. Chaque modèle est bien défini avec ses attributs et ses relations.

- Ressources Filament :
        Vous avez des ressources Filament pour la gestion des Perms, des Créneaux et des Astreintes. Chaque ressource définit la manière dont les données sont affichées et gérées dans l'interface d'administration.

- Fonctionnalités :
        La documentation couvre plusieurs fonctionnalités telles que la validation des Perms, la planification des Créneaux, le shotgun des Astreintes et la notation des Astreintes.

- Diagramme UML :
        Un diagramme UML est fourni pour visualiser les relations entre les différentes entités du système.


## Les services : 

* **Général** : gérer les astreintes et les permanances du semestre
* **Précisement** : 
    * Gérer la demande de permanances des UTCéens (validation)
    * Assigner des permanances à des créneaux
    * Assigner (Shotgun) des astreinteurs à des astreintes
    * Faire la notation de ces astreintes 
    * (Envoie de mail) 



## Fonctionnement général : 
Tout d'abord il faut se renseigner sur les semestres, en effet tout démarre avec le choix du semestre actif. 

Le planning affiche tous les créneaux qui font parties du semestre actif (entre la date de début et la date de fin). Dans ce planning il est possible d'associer une perm qui a été validé et qui est du semestre actif à chacun des créneaux.

Pour les perms, on a une page qui contient trois onglets, un pour les perms "en attente", qui ont donc été demandés mais pas encore validés. Un pour les perms "validées", et un pour "toutes". Bien sûr de manière générales les perms ont été fitrés pour correspondre au semestre actif.

On a une page notation qui permet d'évaluer chacune de ces astreintes.

On a une page shotgun astreinte qui permet aux membres du pic de shotgun un créneau d'astreinte.

---

## Les perms - validation : 

### le modèle : 

Ce modèle contient toutes les informations sur le contenu de la permanance (le thème, l'ambiance voulu, les membres ect.), des informations sur les organisateurs (responsable 1 et 2) et un boolean validated pour savoir si la perm a été validée. Enfin, on a pour information le semestre auquel appartient la perm
```php=

Schema::create('perms', function (Blueprint $table) {
    $table->id();
    $table->string('nom', 255);
    $table->string('theme', 255);
    $table->text('description')->nullable();
    $table->text('periode')->nullable();
    $table->text('membres')->nullable();
    $table->integer('ambiance')->default(0);
    $table->boolean('asso')->default(true);
    $table->string('nom_resp', 255)->nullable();
    $table->string('mail_resp', 255)->nullable();
    $table->string('nom_resp_2', 255)->nullable();
    $table->string('mail_resp_2', 255)->nullable();
    $table->string('mail_asso', 255)->nullable();
    $table->boolean('validated')->default(false);
    $table->foreignIdFor(\App\Models\Semestre::class, 'semestre');
    $table->timestamps();
});
```
### Filament Resource (PermResource) : 
La classe PermResource est une ressource Filament utilisée pour la gestion des permanences (Perm). Elle définit la structure du formulaire, de la table, et des infolists associées à cette ressource. 

#### Attributs spécifiquees
+ ```$model``` : Cette propriété statique définit le modèle Eloquent associé à cette ressource. Dans ce cas, il s'agit du modèle Perm, indiquant que cette resource est liée à la table des permanences dans la base de données.

+ ```$navigationIcon``` : Cette propriété statique définit l'icône à utiliser dans le menu de navigation pour représenter cette ressource. Ici, l'icône "heroicon-o-user-group" est utilisée.

+ ```$navigationGroup``` : Cette propriété statique définit le groupe dans lequel cette ressource doit être affichée dans le menu de navigation de l'interface Filament. Dans cet exemple, la ressource est groupée sous "Gestion des perms".

+ ```$navigationLabel``` : Cette propriété statique définit le libellé à utiliser dans le menu de navigation pour représenter cette ressource. Ici, le libellé est "Validations des perms".

#### Fonctions principales :

* `form(Form $form): Form` : Définit le formulaire pour la création et la modification des validations de perms.

*Le formulaire permet de définir les paramètres suivants pour chaque validation de perm :*

- Nom de la permanence
- Thème de la permanence
- Nom du responsable de la permanence
- Adresse mail du responsable de la permanence
- Nom du sous-responsable
- Adresse mail du sous-responsable
- Géré par une asso
- Adresse mail de l'association
- Description de la permanence
- Ambiance de la perm (entre 1 et 5)
- Période souhaitée pour la permanence
- Membres (ajoutés via la touche Entrée)

* `table(Table $table): Table` : Définit les colonnes et la configuration du tableau pour l'affichage des validations de perms.
Le tableau affiche la liste des validations de perms actuellement disponibles, avec les colonnes suivantes :

- Validation
- Nom
- Thème
- Asso
- Ambiance
- Période
- Nombre de créneaux

Les actions disponibles dans le tableau permettent de visualiser chaque perm.
La liste d'informations détaillées affiche le nom, le thème, la description, la période, les membres, l'ambiance, le nom du responsable, le nom du sous-responsable, l'adresse mail du responsable et l'adresse mail du sous-responsable.

#### Le filtrage 

Les perms sont filtrés dans le fichier ```ListPerm``` par la fonction :  ```public function getTabs(): array```
*Onglets de Filtrage* :

* **En attente** : Affiche les validations de perms en attente de validation pour le semestre actif. Cette tab modifie la requête pour récupérer les enregistrements non validés du semestre actif.
* **Validées** : Affiche les validations de perms déjà validées pour le semestre actif. Cette tab modifie la requête pour récupérer les enregistrements validés du semestre actif.
* **Toutes** : Affiche toutes les validations de perms pour le semestre actif, qu'elles soient validées ou en attente. Cette tab modifie la requête pour récupérer tous les enregistrements du semestre actif.


### Notes des perms : Filament Widget (RankingAstreinte)

Ce widget affiche un tableau de perms avec différentes notes.

Le widget présente un tableau de perms, avec des colonnes pour le nom, la note globale et les notes spécifiques pour l'organisation, la décoration, l'animation et le menu. Les notes sont affichées à l'aide d'icônes et de couleurs différentes en fonction de leurs moyennes.

#### Fonctionnalités principales :

* Affichage d'un tableau de perms avec différentes notes.
* Calcul et affichage de la note globale et des notes spécifiques.
* Utilisation d'icônes et de couleurs pour représenter les différentes notes.

#### Méthodes importantes :

* `table(Table $table): Table` : Configure et renvoie la table affichée dans le widget. Cette méthode définit les colonnes du tableau et leur contenu, y compris les notes globales et spécifiques.
* `nbNotation($record): string` : Calcule et renvoie le nombre de notations avec une note non nulle pour l'organisation et le total des notations pour un enregistrement donné.
* `handlColorNoted($record)` : Détermine la couleur en fonction du ratio de notations avec une note d'organisation par rapport au nombre total de notations pour un enregistrement donné.
* `noteIcon($record, string $type): string` : Calcule la note moyenne et détermine l'icône à afficher en fonction de la note calculée pour un type spécifié (organisation, décoration, animation, menu).
* `noteColor($record, string $type): string` : Détermine la couleur à appliquer en fonction de la note moyenne pour le type spécifié.


## Les créneaux - le planning : 

### le modèle : 
Un créneau est caractérisé par un date et un créneau qui doivent être unique, on ne veut pas de doublon. Et en plus de cela on une clef étrangère vers une perm qui nous permettra d'assoicer un créneau à une perm 
```php=
Schema::create('creneau', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perm_id')->nullable()
        ->constrained()->onDelete('cascade');
    $table->date('date');
    $table->enum('creneau', ['M','D','S']);
    $table->timestamps();

    $table->unique(['date', 'creneau']);
        });
```

### Filament Resource (CreneauResource) : 
La classe CreneauResource est une ressource Filament utilisée pour gérer les créneaux dans le panneau d'administration Filament dédié à la planification des perms.

*Remarque :* pas affiché sous forme de tab mais sous forme de Grid - Stack


#### Attributs Spécifiés
+ ```$model```: Creneau::class - Modèle Eloquent associé à cette ressource.
+ ```$navigationIcon```: 'heroicon-o-calendar-days' - Icône utilisée dans le panneau de navigation.
+ ```$navigationGroup```: 'Gestion des perms' - Groupe de navigation.
+ ```$navigationLabel```: 'Planning' - Étiquette de navigation.
+ ```$pluralLabel```: 'Planning' - Libellé au pluriel pour la ressource.

#### Fonctions principales

**```table(Table $table)```**
+ Description : Définit la structure de la table associée à cette ressource pour la liste et la gestion des créneaux.
+ Colonnes :
    + creneau: Affiche le créneau.
    + perm.nom: Affiche le nom de la perm associée en tant que badge.
    + perm_id: Sélection de la perm associée (options filtrées pour le semestre actif).
    + creneau: Affiche les astreinteurs du créneau.
+ Groupes :
    + Groupe par date : Affiche la date du créneau sous forme de groupe collapsible.
+ Filtres :
    + Par perm_id : Filtrage par perm.
    + Libre : Filtrage des créneaux sans perm associée.
+ Actions :
    + Libérer : Dissocie la perm associée du créneau.

**```dissociatePerm($record)```**
Est activé par láction ```Libérer``` : Dissocie la perm associée d'un créneau spécifique.

#### Méthodes importantes
**```getStateSemester()```**
+ Type : string
+ Description : Récupère le non du semestre actif.
+ Retourne : Une chaîne de caractères représentant l'état du semestre actif (ex: "A23", "P24").

**```getStartSemester()```**
+ Type : Carbon (ou string si nécessaire)
+ Description : Récupère la date de début du semestre actif.
+ Retourne : Un objet Carbon représentant la date de début du semestre actif.

**```getEndSemester()```**
+ Type : Carbon (ou string si nécessaire)
+ Description : Récupère la date de fin du semestre actif.
+ Retourne : Un objet Carbon représentant la date de fin du semestre actif.

**Le filtrage des créneaux affichées**

La classe ListCreneaus est associée à la ressource CreneauResource. Elle affiche une liste de créneaux avec la possibilité de filtrer les créneaux en fonction de l'état du semestre actif. Les onglets permettent de filtrer les créneaux en fonction des dates comprises entre le début et la fin du semestre actif. Cette page offre une vue organisée des créneaux en fonction du semestre actif.


### Filament Ressource (AstreinteShotgun)

Gérer rapidement leurs shotgun des astreintes pour des créneaux spécifiques.

#### Methode principale

#### Méthodes auxiliaires
```dissociatePerm($record)``` : Dissocie la permission associée à un créneau spécifique.

```handleshotgun1($record)``` : Gère l'action "shotgun1" pour un créneau spécifique, permettant aux utilisateurs de s'inscrire rapidement à un créneau.

```handleshotgun2($record)``` : Gère l'action "shotgun2" pour un créneau spécifique, permettant aux utilisateurs de s'inscrire rapidement à un créneau.

```determineColor1($record)``` : Détermine la couleur de l'action "shotgun1" en fonction de l'état actuel de l'astreinte pour ce créneau.

```determineColor2($record)``` : Détermine la couleur de l'action "shotgun2" en fonction de l'état actuel de l'astreinte pour ce créneau.

```getDateSamediAvant()``` : Renvoie la date du samedi précédent par rapport à aujourd'hui.

```getDateSamediApres()``` : Renvoie la date du samedi suivant par rapport à aujourd'hui.




## Les astreintes - la notation : 
### Le modèle : 

Une astreinte a besoin de ces infos de bases pour être créée : un id d'un membre qui est en réalité l'astreinteur, un créneau d'un id qui permet de savoir quand se situe l'astreinte, cela est complété par astreinte type. Enfin on a plein d'infomartions sur la notation de la perm qui doivent être remplies par l'astreinteur 

```php=
Schema::create('astreintes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id');
    $table->foreignId('creneau_id');
    $table->enum('astreinte_type', AstreinteType::choices());
    $table->integer('note_deco')->default(0);
    $table->integer('note_orga')->default(0);
    $table->integer('note_anim')->default(0);
    $table->integer('note_menu')->default(0);
    $table->string('commentaire')->nullable();
    $table->timestamps();
});
```

#### Méthode getPointsAttribute

La méthode `getPointsAttribute` est un "accessor" utilisé dans le modèle Astreinte pour calculer et fournir dynamiquement la valeur de l'attribut virtuel "points". Cet attribut représente les points attribués à une astreinte en fonction de son type.
```php
public function getPointsAttribute()
```

*Exemple d'utilisation*
```php
$astreinte = new Astreinte([
    'astreinte_type' => 'Matin 1',
    // ... autres attributs
]);
$points = $astreinte->points; // Retourne 1
```

### FilamentResource (AstreinteResource) : 

La classe `AstreinteResource` est une ressource Filament utilisée pour définir le comportement de l'interface utilisateur dans le panneau d'administration Filament dédié à la gestion des astreintes (plus précisement la notation

#### Les attributs spécifiés

```$model```
+ Type : string|null
+ Description : Définit le modèle Eloquent associé à cette ressource. Dans ce cas, le modèle est Astreinte::class.

```$navigationIcon```
+ Type : string|null
+ Description : Icône utilisée pour représenter cette ressource dans le panneau de navigation Filament. Dans cet exemple, l'icône utilisée est "heroicon-o-pencil-square".

```$navigationGroup```
+ Type : string|null
+ Description : Groupe de navigation auquel cette ressource appartient. Dans cet exemple, la ressource appartient au groupe "Gestion des perms".

```$navigationLabel```
+ Type : string|null
+ Description : Étiquette de navigation pour cette ressource. Dans cet exemple, l'étiquette est définie comme "Notation".

#### Les méthodes

```table(Table $table): Table```
+ Paramètre : $table (Type : Table) - Instance de la table associée à cette ressource.
+ Description : Définit la structure de la table associée à cette ressource, y compris les colonnes, filtres, actions et actions groupées.


**Le filtrage des astreintes affichées**

La classe **```ListAstreintes```** représente la page de liste pour les astreintes dans le panneau d'administration Filament. Cette page permet de visualiser et de filtrer les astreintes en attente de notation ainsi que les notes personnelles.

*Méthode de filtrage*

```getTabs(): array```
+ Retour : array - Tableau contenant les onglets de filtrage pour les enregistrements. Retourne deux onglets : "En attente de notation" et "Vos notes".

+ Onglet "En attente de notation"
    + Filtre : Les astreintes en attente de notation pour l'utilisateur authentifié (membre_id), où la note d'organisation est nulle, associées à un créneau qui a une permanence pendant le semestre actif.
+ Onglet "Vos notes"
    + Filtre : Les astreintes notées pour l'utilisateur authentifié (membre_id), associées à un créneau qui a une permanence pendant le semestre actif.


**Edition d´une astreinte (notation)**

La classe **```EditAstreinte```** représente la page d'édition pour une astreinte dans le panneau d'administration Filament. Cette page est dédiée à la notation des astreintes. Cette classe étend la classe EditRecord fournie par Filament et permet de personnaliser la structure du formulaire en fonction du type d'astreinte. Les méthodes formMatin, formMidi et formSoir définissent les champs spécifiques à chaque type.


*Méthodes*

```formMatin(Form $form): Form```
+ Paramètre : $form (Type : Form) - Instance du formulaire associé à cette page pour le type "Matin".
+ Description : Définit la structure du formulaire pour le type "Matin" de l'astreinte, y compris les champs de note d'organisation et de commentaire.

```formMidi(Form $form): Form```
+ Paramètre : $form (Type : Form) - Instance du formulaire associé à cette page pour le type "Midi".
+ Description : Définit la structure du formulaire pour le type "Midi" de l'astreinte, y compris les champs de note de menu, de note d'organisation et de commentaire.

```formSoir(Form $form): Form```
+ Paramètre : $form (Type : Form) - Instance du formulaire associé à cette page pour le type "Soir".
+ Description : Définit la structure du formulaire pour le type "Soir" de l'astreinte, y compris les champs de note de menu, de note de décoration, de note d'animation et d'ambiance, de note d'organisation et de commentaire.

```form(Form $form): Form```
+ Paramètre : $form (Type : Form) - Instance du formulaire associé à cette page.
+ Description : Détermine la structure du formulaire en fonction du type d'astreinte. Appelle l'une des méthodes formMatin, formMidi ou formSoir en fonction du type d'astreinte.


## UML 

```plantuml

skinparam roundcorner 20

@startuml

class Semestre{

}

class Astreinte{

}
  
class Perm{

}

class Creneaux{

}

Perm "0...1" -- "0..3" Creneaux
Creneaux "1" -- "0..3" Astreinte
Semestre "1" -- "*" Perm


@enduml
