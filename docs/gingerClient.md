# Service GingerClient
Le service GingerClient permet d'interagir avec l'API Ginger du SIMDE pour obtenir des informations sur les utilisateurs et les badges. Cette documentation explique les méthodes disponibles dans ce service, ainsi que la manière de l'utiliser dans des contrôleurs.

### Initialisation du Service
Le service nécessite l'initialisation avec les informations d'URL et de clé Ginger. Ces informations sont récupérées à partir des variables d'environnement GINGER_URI et GINGER_KEY respectivement. Assurez-vous que ces variables d'environnement sont définies dans votre configuration.

```php
use App\Services\GingerClient;

// Initialisation du service GingerClient
$gingerClient = new GingerClient();
```

### Méthodes du Service GingerClient
**1. `getUserInfo(string $username): array`**

Cette méthode permet de récupérer des informations sur un utilisateur en se basant sur son nom d'utilisateur.

Paramètres :

`$username` : Nom d'utilisateur de l'utilisateur dont vous souhaitez obtenir les informations.


Exemple d'utilisation :

```php
$userInfo = $gingerClient->getUserInfo('john_doe');
```

**2. `getBadgeInfo(string $badgeId): array`**

Cette méthode permet de récupérer des informations sur un badge en se basant sur son identifiant.

Paramètres :

`$badgeId` : Identifiant du badge dont vous souhaitez obtenir les informations.

Exemple d'utilisation :
```php
$badgeInfo = $gingerClient->getBadgeInfo('123456');
```

**3. `apiCall(string $method, string $path, array $data = null, array $parameters = null): array`**
   
Cette méthode permet de réaliser un appel à l'API Ginger/v1 avec le type de méthode spécifié (GET, POST, etc.).

Paramètres :
`$method` : Type de méthode HTTP (GET, POST, etc.).
`$path` : Le chemin de l'API Ginger/v1.
`$data` : Les données à envoyer dans le corps de la requête (pour les méthodes POST, PUT, etc.).
`$parameters` : Les paramètres à inclure dans l'URL.

Exemple d'utilisation :
```php
$response = $gingerClient->apiCall('GET', '/user/john_doe');
```

### Utilisation dans un Contrôleur
Voici comment vous pouvez utiliser le service GingerClient dans un contrôleur Laravel.

```php
use App\Services\GingerClient;

class UserController extends Controller
{
private $gingerClient;

    public function __construct(GingerClient $gingerClient)
    {
        $this->gingerClient = $gingerClient;
    }

    public function getUserInfo($username)
    {
        $userInfo = $this->gingerClient->getUserInfo($username);

        // Utilisez $userInfo pour traiter les données obtenues de Ginger/v1
        // ...

        return response()->json($userInfo);
    }

    public function getBadgeInfo($badgeId)
    {
        $badgeInfo = $this->gingerClient->getBadgeInfo($badgeId);

        // Utilisez $badgeInfo pour traiter les données obtenues de Ginger/v1
        // ...

        return response()->json($badgeInfo);
    }
}
```

Assurez-vous d'ajouter les routes correspondantes dans le fichier web.php ou api.php pour ces méthodes du contrôleur.
