
TO DO :
* comprendre les urls QRcode et Survey
* Explique le webTvElo


# Les TVs

## Les services

Gestion des télévisions et de leur affichage

## Models

- Les tvs
  - les attributs : id{primary key}, link, lind_id, name
  - 2 instances le 01/10/2023
  - Exemple :
    ``` {
        {
        "id": 1,
        "link": {
            "id": 4,
            "url": "https://webtv.picasso-utc.fr/content",
            "name": "Défault"
        },
        "link_id": 4,
        "name": "Pic Bar"
    }

- Les liens :
  - les attributs : id {primary key}, url, name
  - Exemple :
```    {
        {
        "id": 8,
        "url": "https://webtv.picasso-utc.fr/duelbrasseur",
        "name": "Duel Des brasseurs"
    }
```    
- Les médias :
  - les attributs : id {primary key}, media, name, type, activate, times
  - Exemple :
```            {
        {
        "id": 252,
        "media": "tv/RIPPOLAR.png",
        "name": "RIP POLAR",
        "media_type": "I",
        "activate": false,
        "times": 90
    }
```

## Le fonctionnement :

Il y a deux tv (premier model), une au niveau du bar, une dans la salle principale. Chacune pointe vers un lien (deuxième model). Voici les principaux :
1. default (https://webtv.picasso-utc.fr/content)
2. Menu ("https://webtv.picasso-utc.fr/menu")
3. Elo ("https://webtv.picasso-utc.fr/elo")
4. Les autres (exemple : https://webtv.picasso-utc.fr/duelbrasseur)

Explications :
1. Pour le lien *defaut*, ce qui est affiché est en lien avec *les médias* (modèles 3). Parmi la liste de toutes les instances médias, seulement cette où l'attribut *activate* : true afficheront le contenu contenu dans l'*attribut* média. Si plusieurs instances sont activés, alors ce qui est affiché alterne entre les différents contenus, avec une alternance déterminé par l'attribut *times*.
2. Pour le lien *menu*, il affiche les prochaines menus à servir de la manière suivant :
   ![](https://md.picasoft.net/uploads/upload_a60a8701fcad958433a2140b21a38c28.png)
3. pour le lien *elo*
4. Pour *les autres* liens, c'est assez classique, c'est ce qui est contenu dans ce lien qui sera affiché sur la tv.

## Inutil(e)(isé)

L'instance webtv/elo est aujourd'hui inutilisé mais nous souhaitons la rendre de nouveau opérationel
https://webtv.picasso-utc.fr/elo

L'instance webtv/menu est aujourd'hui inutilisé et ne sera certainement pas implémenté dans la nouvelle version
https://webtv.picasso-utc.fr/menu


## Lien avec le site du Pic'asso:
Chaque modèle peut être géré depuis le site web, il existe en effet un onglet gestion des tv. Dans cet onglet, il y a 3 sous-onglets, chacun permettant de gérer un des trois modèles.
* Configuration : pour chacune des deux télévisions, on peut changer l'attribut link
* Média : on peut créer une nouvelle instance, consulter le contenu d'une instance déjà existante, faire varier l'attribut activate entre *true* et *false* ou modifier n'importe quel autre attribut (times, média, name).
* URL : enfin, on peut créer, supprimer, ou modifier des links

UML
===

Les classes/objet en rouge ne sont pas utilisés actuellement et ne seront très certainement pas implémentés dans la prochaine version du serveur.
Les classes/objet en gris ne sont pas utilisés actuellement MAIS seront très certainement implémentés dans la prochaine version du serveur.

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
skinparam object {
  BackgroundColor White
  BorderColor Black
  FontColor Red
}


package TVs {

    class liens{

        }
    class TVs{

    }

    
     class medias{
         
    }

interface webTvMenu #back:red;header:red
{

}
    
interface webTvElo #back:red;header:red
{

}
    webTvMenu == liens: instance de <
    webTvContent = liens: instance de <
    webTvElo = liens: instance de <
    medias "0..*" --o "0..1" webTvContent
    liens "1" --o "0...2" TVs
    }

package Perm{
    
    class Menu #back:red;header:red 
    {

    }    
}

Menu - webTvMenu

package Elo{
    
    class elo #back:grey;header:grey {
        
    }
    
}

elo -- webTvElo

@enduml

