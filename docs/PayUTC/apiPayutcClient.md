# Service ApiPayutc
Le service ApiPayutc permet d'interagir avec l'API Payutc de weez pour obtenir des informations sur les utilisateurs et les badges. Cette documentation explique les méthodes disponibles dans ce service, ainsi que la manière de l'utiliser dans des contrôleurs.

**Vous pouvez trouver le code ici : app/Services/ApiPayutcClient.php**
### Initialisation du Service
Le service nécessite l'initialisation avec les informations APP_KEY, SYSTEM_ID, PASSWORD, LOGIN, FUNDATION_ID, API_URL. Ces informations sont récupérées à partir des variables d'environnement. Assurez-vous que ces variables d'environnement sont définies dans votre configuration (fichier .env).

```php
use App\Services\PayUtcClient;

// Initialisation du service GingerClient

    private ApiPayutcClient $client;

    public function __construct(PayUtcClient $client)
    {
        $this->client = $client;
    }
```

### Méthodes du Service GingerClient
**1. `makePayutcRequest($method, $endpoint, $options = []): qui renvoit un json`**

Cette méthode permet de faire n'importe quel requête vers la nouvelle API de weezpay (documentation ici : https://docapi.weezevent.com/openapi.html?weezpay).

Paramètres :

`$method` : la méthode que l'on souhaite utiliser dans notre requête (POST, GET etc...) à priori presque toujours GET.

`$endpoint` : la terminaison de l'URL de la requête que l'on souhaite utiliser (voir sur la doc). 

`$option` : pour ajouter des querys paremeters que l'on peut voir dans la doc, par exemple ajouter des dates pour faire une requête plus précise.

**Attention il y a parfois des path parameters, ceux la ne vont pas dans option mais dans endpoint**

Exemple d'utilisation :

```php
        $response = $this->client->makePayutcRequest('GET', 'transactions', [
            'created__gt' => "2023-11-15T07:15:00.000000Z",
            'created__lt' => "2023-11-15T16:10:00.000000Z",
        ]);
        $responseData = $response->getContent();
        $jsonData = json_decode($responseData, true);
```

**2. `getSession(): string`**

Cette méthode permet de récupérer un id de session qui aura les droits pour faire certaines requêtes à weez, cette méthode n'a pas besoin d'être utilisé.
En effet elle est intégré à la méthode précédente makePayutcRequest. 

Pour avoir cet id de session on utilise actuellement encore l'ancienne API (documentation ici : https://apidoc.nemopay.net/Base_Service/#loginApp)

Exemple d'utilisation :
```php
$session_id = $this->getSession();
```

### Utilisation dans un Contrôleur
Voici comment vous pouvez utiliser le service GingerClient dans un contrôleur Laravel.

```php
use App\Services\PayUtcClient;

class GoodiesController extends Controller
{
    private PayUtcClient $client;

    public function __construct(PayUtcClient $client)
    {
        $this->client = $client;
    }
    
    public function getWinner()
    {
        $response = $this->client->makePayutcRequest('GET', 'transactions', [
            'created__gt' => "2023-11-15T07:15:00.000000Z",
            'created__lt' => "2023-11-15T16:10:00.000000Z",
        ]);
        $responseData = $response->getContent();
        $jsonData = json_decode($responseData, true);
    }
}
```
Dans l'exemple ci dessus $jsonData contient donc les données de réponses de la requête. 
Cet exemple ne sert qu'à faire une requête précises il peut être intéressant de mettre les options en paramêtres. 
Assurez-vous d'ajouter les routes correspondantes dans le fichier web.php ou api.php pour ces méthodes du contrôleur.
