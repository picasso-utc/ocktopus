# Les TVs

## Les services

Gestion des télévisions et de leur affichage

## Models

- Les tvs
    - les attributs : id{primary key}, link, lind_id, name
    - 2 instances le 01/10/2023
    - Exemple :
    ```json
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
    ```

- Les liens :
    - les attributs : id {primary key}, url, name
    - Exemple :
```json 
    {
        "id": 8,
        "url": "https://webtv.picasso-utc.fr/duelbrasseur",
        "name": "Duel Des brasseurs"
    }
```    
- Les médias :
    - les attributs : id {primary key}, media, name, type, activate, times
    - Exemple :
```json
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

Il y a deux tv (premier model), une au niveau du bar, une dans la salle principale. Chacune pointe vers un lien (deuxième model). Il y a deux types de liens iprincipaux
- default "https://webtv.picasso-utc.fr/content"
- Les autres (exemple : https://webtv.picasso-utc.fr/duelbrasseur)

Pour *les autres* liens, c'est assez classique, c'est ce qui est contenu dans ce lien qui sera affiché sur la tv.
Pour le lien *defaut*, ce qui est affiché est en lien avec *les médias* (modèles 3). Parmi la liste de toutes les instances médias, seulement cette où l'attribut *activate* : true afficheront le contenu contenu dans l'*attribut* média. Si plusieurs instances sont activés, alors ce qui est affiché alterne entre les différents contenus, avec une alternance déterminé par l'attribut *times*.

## Lien avec le site du Pic'asso:
Chaque modèle peut être géré depuis le site web, il existe en effet un onglet gestion des tv. Dans cet onglet, il y a 3 sous-onglets, chacun permettant de gérer un des trois modèles.
* Configuration : pour chacune des deux télévisions, on peut changer l'attribut link
* Média : on peut créer une nouvelle instance, consulter le contenu d'une instance déjà existante, faire varier l'attribut activate entre *true* et *false* ou modifier n'importe quel autre attribut (times, média, name).
* URL : enfin, on peut créer, supprimer, ou modifier des links

