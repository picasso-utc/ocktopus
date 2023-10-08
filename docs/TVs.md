TO DO :
* Perms hallowen
* Articles
  * est liée avec Menu je crois
* Lien entre shotgun et perms
  * Liée mais pourquoi existe aussi shotgun alors ?
* Goodies -> pq les goodies sont dans perm ??




# Les perms



## Les services

**Importante**
* Notation des perms par les astreinteurs + affichage
* Gérer le planning des perms du semestre
  * Gérer les perms
    * En créer (leur nom, asso ou non, et le nom des responsables )
    * En supprimer (si n'est pas associé à des créneaux)
  * Associer des perms à des créneaux
    * Ajouter, supprimer (si la perm n'a pas encore eu lieu)
  * Affichage de la liste des perms ainsi que du planning associé

**Secondaire**
* Envoie de mail automatisé

**Inutilisé**
* Gérer les demandes de perms (voir requested Perms) -> à la place la team anim utilise aujourd'hui un couple google form/sheet
* La vente d'articles d'une Perm (dernière vente d'un *article* date de 2021)


**Inutile**


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
* Ce n'est pas utilisé -> il faut essayer de comprend pourquoi (pas complétement implémenté ? pas pratique ? la team anim est au courant de cette fonctionalité ?)

---


### Les articles

C'est liée au modèle menu

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



UML Perms
===
Les classes/objet en rouge ne sont pas utilisés actuellement et ne seront très certainement pas implémentés dans la prochaine version du serveur

![](https://md.picasoft.net/uploads/upload_c789ade4316e1557fa0c5dfc3536ff2f.png)

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

class Perm{

}
    
class RequestedPerm{

}


class Creneaux{

}

Perm "0...1" o--o "0..3" Creneaux
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
    webTvMenu = liens: instance de <

webTvMenu -  Menu 
 } 
}
@enduml










