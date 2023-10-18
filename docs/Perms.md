# Les perms



## Les services

**Importante**
* Gérer le planning des perms du semestre
    * Gérer les perms
        * En créer
        * En supprimer (si n'est pas associé à des créneaux)
    * Associer des perms à des créneaux
        * Ajouter, supprimer (si la perm n'a pas encore eu lieu)
    * Affichage de la liste des perms ainsi que du planning associé
* Gérer les astreintes :
    * Shotgun des atreintes
    * Supprimer, ajouter des astreintes manuellement

**Secondaire**
* Envoie de mail automatisé
* Notation des perms par les astreinteurs
* La vente d'articles d'une Perm

**Inutil(e)(isé)**
* Gérer les demandes de perms -> à la place la team anim utilise aujourd'hui un couple google form/sheet
* La vente d'articles d'une Perm (dernière vente d'un *article* date de 2021)
* Gérer les astreintes :
    * Un onglet pour supprimer, ajouter des gens manuellement






## Les models

La perm est un modèle assez explicite, elle conceptualise une équipe de permanenciers avec les informations suivantes :
* Les créneaux tenu par la perm sous la forme (date-type-id du créneau)
* le nom de la perm
* si c'est une asso ou non (si oui avec leur mail)
* les resps et leur mail
* le semestre
  sur le site :
  ![](https://md.picasoft.net/uploads/upload_46f1c49177984384f584f1a093a47b57.png)


### Les perms :

*Exemple* :
```{
{
        "id": 1515,
        "creneaux": [
            "2023-11-10:S:2939",
            "2023-11-10:D:2938",
            "2023-11-10:M:2937"
        ],
        "nom": "Latina II",
        "asso": false,
        "nom_resp": "Alanna ACOSTA CHILELLI",
        "mail_resp": "alanna.acosta",
        "nom_resp_2": "Amélie CHAILLEY ORTEGA",
        "mail_resp_2": "amelie.chailley",
        "mail_asso": "",
        "semestre": 24
    }
```

**Remarques** :
* On ne peut supprimer sur le site que les perms qui n'ont pas de créneaux attribués

### Les créneaux :

Un créneau correspond à un temps (matin / déjeuner / soir) ,associé à un jour, associé à une perm.

*sur le site :*
![](https://md.picasoft.net/uploads/upload_7562fc7f3afb10efff4f4103d8b12d64.png)
Sur cette image on a 3 créneaux par exemple. Un du matindu 10 Novembre fait par *Latina II*, un du déjeuner 10 novembre fait par *Latina II*,un du soir 10 novembre fait par *Latina II*.



*Exemple* :
```{
{
        "id": 2939, 
        "perm": {
            "id": 1515, 
            "creneaux": [
                "2023-11-10:S:2939",
                "2023-11-10:D:2938",
                "2023-11-10:M:2937"
            ],
            "nom": "Latina II",
            "asso": false,
            "nom_resp": "Alanna ACOSTA CHILELLI",
            "mail_resp": "alanna.acosta",
            "nom_resp_2": "Amélie CHAILLEY ORTEGA",
            "mail_resp_2": "amelie.chailley",
            "mail_asso": "",
            "semestre": 24
        },
        "facturerecue_set": [],
        "article_set": [],
        "perm_id": 1515,
        "date": "2023-11-10",
        "creneau": "S",
        "state": "N",
        "montantTTCMaxAutorise": null
    }
```
Cet exemple représente le créneau à l'ID 2939, qui est créneau du soir du 11 octobre 2023. Ce créneau est tenu par la perm (autre modèle imbriqué donc) dont on a pour information l'ID(1515), les autres créneaux fait par cette perm, le nom, les resp ect. L'état nous indique que la perm n'a pas encore eu lieu (stat : "N")

**Remarques** :
* L'imbrication paraît farfelu, on a pas mal de répétitions d'infos, est-ce que c'est vraiment utile d'avoir toutes ces infos. D'un autre côté, au moins, toutes les informations sont présentes
* Un créneau est crée si et seulement si il est assigné à une perm.
* RESTE A COMPRENDRE "facturerecue_set": [],"article_set": [],

### Les requested perm (perms demandées)

C'est un formulaire que doivent remplir ce qui demandent une perm. **Mais aujourd'hui ce n'est pas utilisé**.

sur kraken/perm/model



```class RequestedPerm(models.Model):
    nom = models.CharField(max_length=255)
    asso = models.BooleanField(default=True)  # true if asso
    mail_asso = models.CharField(null=True, default=None, max_length=255, blank=True)
    nom_resp = models.CharField(null=True, default=None, max_length=255)
    mail_resp = models.CharField(null=True, default=None, max_length=255)
    nom_resp_2 = models.CharField(null=True, default=None, max_length=255)
    mail_resp_2 = models.CharField(null=True, default=None, max_length=255)
    theme = models.CharField(null=True, default=None, max_length=255)
    description = models.TextField(null=True, default=None)
    membres = models.CharField(null=True, default=None, max_length=255)
    periode = models.TextField(null=True, default=None)
    added = models.BooleanField(default=False)
    founder_login = models.CharField(max_length=8, default=None)
    ambiance = models.IntegerField(default=0)
    semestre = models.ForeignKey(core_models.Semestre, on_delete=models.SET_NULL, null=True, default=get_current_semester)
```

**Remarques** :
* Ce n'est pas utilisé -> il faut essayer de comprend pourquoi (pas complétement implémenté ? pas pratique ? la team anim est au courant de cette fonctionalité -> réponse : non.

---


### Les articles

Un article a un identifiant, un nom, correspond à un créneau et compose un menu. IL a un prix ainsi qu'un identifiant payut. On a aussi l'information du stock restant et du nombre de vente.

```    {
     {
        "id": 1504,
        "creneau": 1686,
        "menu": [
            "209"
        ],
        "tva": 0.0,
        "prix": 6.0,
        "id_payutc": 16792,
        "stock": 22,
        "ventes": 0,
        "ventes_last_update": "2021-10-12T18:25:20.334944+02:00",
        "nom": "Raclette"
    }
```
### Les menus
Un menu est juste composé d'article

```{
    {
        "article": {
            "id": 1504,
            "creneau": 1686,
            "menu": [
                "209"
            ],
            "tva": 0.0,
            "prix": 6.0,
            "id_payutc": 16792,
            "stock": 22,
            "ventes": 0,
            "ventes_last_update": "2021-10-12T18:25:20.334944+02:00",
            "nom": "Raclette"
        }
    }
```
est lié aux TVs, plus précisment au lien : https://webtv.picasso-utc.fr/menu

![](https://md.picasoft.net/uploads/upload_49b2be093d21238dc866b1ba946a2139.png)

---
### Les astreintes

Une astreinte correspond à un créneau, tenu par UN astreinteur (member), qui correspond à un certain type d'astreinte, et qui a différentes notes réalisés par l'astreinteur.

Dans l'exemple suivant
- On a une astreinte qui a un ID de 2798 qui permet de l'identifier.
- On a le créneau qui est le soir, le 2023-10-23
    - Dans ce créneau est aussi imbrique une perm (ici celle de MeetPic)
- L'astreinteur est Noam Seuret
- Et vu qu'il n'a pas encore noté la perm, toutes les notes sont à 0, et le commentaire est *null*.
```{
    {
        "id": 2798,
        "creneau": {
            "id": 2914,
            "perm": {
                "id": 1512,
                "creneaux": [
                    "2023-10-13:S:2914",
                    "2023-10-13:D:2913",
                    "2023-10-13:M:2912"
                ],
                "nom": "MeetPIC",
                "asso": false,
                "nom_resp": "Lola CAIGNET",
                "mail_resp": "lola.caignet@etu.utc.fr",
                "nom_resp_2": "Alanna ACOSTA CHILELLI",
                "mail_resp_2": "alanna.acosta",
                "mail_asso": "",
                "semestre": 24
            },
            "facturerecue_set": [],
            "article_set": [],
            "perm_id": 1512,
            "date": "2023-10-13",
            "creneau": "S",
            "state": "N",
            "montantTTCMaxAutorise": null
        },
        "creneau_id": 2914,
        "member": {
            "userright": {
                "id": 168,
                "login": "seuretno",
                "right": "A",
                "last_login": null,
                "name": "Noam SEURET"
            }
        },
        "member_id": 253,
        "astreinte_type": "S2",
        "note_deco": 0,
        "note_orga": 0,
        "note_anim": 0,
        "note_menu": 0,
        "commentaire": null
    }
```
**Remarque** :
- Il faut bien comprendre qu'un objet astreinte correspond à un créneau d'astreinte avec UN astreinteur. Il y a donc X objets astreintes pour un seul créneau astreinte
- Les imbricrations semblent douteuses et très lourdes (à voir comment on peut optimiser ça avec Laravel)

### Le shotgun (des astreintes)

Les astreintes sont décidées par un shotgun. Il y a un onglet du site dédié à ça sur le site :

![](https://md.picasoft.net/uploads/upload_b5d36c9ee1c7833397fa22748610e0e6.png)

Pas une compréhension fine du modèle. J'imagine qu'il fut un temps ou quelqu'un devait lancer le shotgun avant que tout le monde puisse prendre sa place, mais ce n'est pas le cas aujourd'hui.
```{
class Shotgun(models.Model):
    date = models.DateField(primary_key=True)
    launched_by = models.ForeignKey(core_models.Member, on_delete=models.CASCADE)
```
Remarque :
* bien pensé : une fois que quelqu'un a cliqué, on ne plus le remplacer contrairement à un google sheet.
* PAS utilisé car le serveur ne supportait pas toutes les requêtes d'un coup
* Pour modifier le shotgun, le seul moyen est de passer par l'onglet de gestion des astreintes

#### Perm halloween :
```
class PermHalloween(models.Model):
    article_id = models.IntegerField(default=0)
    login = models.CharField(null=True, default=None, max_length=10)
```
Très peu d'informations sur ça mais n'a pas l'air important

UML Perms
===
Les classes/objet en rouge ne sont pas utilisés actuellement et ne seront très certainement pas implémentés dans la prochaine version du serveur

![](https://md.picasoft.net/uploads/upload_2a4a1e7d39fc13c3ecb3a7bb3281d5bd.png)


```plantuml

skinparam roundcorner 20

@startuml
'https://plantuml.com/class-diagram

!define MyBackgroundColor #lightblue
!define MyBorderColor #005f87




skinparam class {
  BackgroundColor MyBackgroundColor
  BorderColor MyBorderColor
}
package Perms {
class Article #back:red;header:red
    {

}

class Menu #back:red;header:red 
    {

}

class Astreinte{

}
  
class Perm{

}
class RequestedPerm{

}


class Creneaux{

}

Perm "0...1" o--o "0..3" Creneaux
Creneaux "1" --o "0..3" Astreinte
Article "1...*" o---o "0..*" Menu
Creneaux "1" o--o "0...1" Menu
Creneaux "1" o---o "0...*" Article
}

package TVs {

    class liens{

        }

      
class webTvMenu #back:red;header:red
{

}
    webTvMenu .. liens: instance de >

webTvMenu -  Menu 
 } 
}
@enduml










