<?php 

namespace App\Http\Controllers;

use App\Models\Articles;
use App\Models\MarketPrices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    public function handle(Request $request)
    {
        $badgeId = $request->input('badge_id');
        $items = $request->input('items'); // [[article_id, quantity], ...]
        $sessionId = $request->input('session_id');

        $systemId = env('WEEZEVENT_SYSTEM_ID');
        $appKey = env('WEEZEVENT_APP_KEY');
        $fundId = env('WEEZEVENT_FUND_ID');

        $mappedItems = [];
        foreach ($items as [$articleId, $quantity]) {
            $categoryId = $this->getCategoryFromArticle($articleId);
            if ($categoryId == 11 || $categoryId == 10) {    // Si c'est une bière pression ou bouteille, on remplace par le prix du marché
                $firstTransaction = Http::withHeaders([  // Première transaction de l'article (qui aura été mis à 0e) pour les stats
                    'accept-language' => 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
                ])->post('https://api.nemopay.net/services/POSS3/transaction?system_id='.$systemId.'&app_key='.$appKey.'&sessionid='.$sessionId, [
                    'badge_id' => $badgeId,
                    'obj_ids' => [[$articleId, $quantity]],
                    'fun_id' => $fundId,
                ])->throw();

                $currentPrice = $this->getCurrentMarketPrice($articleId);
                $replacementArticleId = $this->mapPriceToBeerArticle($currentPrice);
                for ($i = 0; $i < $quantity; $i++) {
                    $currentPrice = $this->getCurrentMarketPrice($articleId);
                    $replacementArticleId = $this->mapPriceToBeerArticle($currentPrice);
                    
                    $mappedItems[] = [$replacementArticleId, 1];  // On passe les articles 1 par 1 au cas où le prix augmente 
                    $this->updateMarket($articleId, 1);
                }
            } else {
                $mappedItems[] = [$articleId, $quantity];
            }
        }

        $response = Http::withHeaders([
            'accept-language' => 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
        ])->post('https://api.nemopay.net/services/POSS3/transaction?system_id='.$systemId.'&app_key='.$appKey.'&sessionid='.$sessionId, [
            'badge_id' => $badgeId,
            'obj_ids' => $mappedItems,
            'fun_id' => $fundId,
        ])->throw();

        return response()->json($response->json()); 
    }

    private function getCategoryFromArticle($articleId)
    {
        $article = Articles::where('article_id', $articleId)->first() ?? null;
        return $article ? $article->category_id : null;
    }

    private function getCurrentMarketPrice($articleId)
    {
        return MarketPrices::where('article_id', $articleId)
            ->first()
            ->price ?? 1.80;
    }

    private function mapPriceToBeerArticle($price)  // Calcule l'id de la bière sachant que 60 centimes c'est l'id 23658 et qu'on augmente l'id de 1 par centime
    {
        $rounded = number_format($price, 2, '.', '');
        $basePrice = 0.60;
        $baseId = 23658;

        $diff = ($rounded - $basePrice) * 100; // nb centimes d'écart
        $id = $baseId + (int)round($diff);  // l'ID part de baseId + centimes d'écart

        if ($id < $baseId || $id > 23658 + 180) {  // Si le prix est inférieur à 0.60 ou spérieur à 2.40
            return 23778; // Prix inconnu ? -> 1e80
        }

        return $id;
    }

    private function updateMarket($articleId, $quantity)
    {
        $maxPrice = 2.4;
        $minPrice = 0.6;
        $priceStep = 0.07;      // Plus ou moins de fluctuation sur le prix du marché
        $balanceMarket = 1.80;   // Le marché se balance autour de cette valeur

        $article = Articles::where('article_id', $articleId)->first();
        if (!$article) return;

        $categoryId = $article->category_id;
        $allArticles = Articles::where('category_id', $categoryId)->get();
        $nbArticles = $allArticles->count();

        if ($nbArticles <= 1) return;

        $weights = [];
        $totalWeight = 0;
        foreach ($allArticles as $otherArticle) {
            if ($otherArticle->article_id == $articleId) continue;

            $w = mt_rand(50, 150);
            $weights[$otherArticle->article_id] = $w;
            $totalWeight += $w;
        }

        foreach ($allArticles as $otherArticle) {
            $currentPrice = MarketPrices::firstOrNew(
                ['article_id' => $otherArticle->article_id]
            );

            if (!$currentPrice->exists) {
                $currentPrice->price = 1.00;
            }

            $newPrice = $currentPrice->price;

            if ($otherArticle->article_id == $articleId) {
                $newPrice += $priceStep * $quantity;
            } else {
                $weight = $weights[$otherArticle->article_id];
                $share = ($priceStep * $quantity) * ($weight / $totalWeight);
                $newPrice -= $share;
            }

            $newPrice = max($minPrice, min($maxPrice, round($newPrice, 2)));

            $currentPrice->price = $newPrice;
            $currentPrice->updated_at = now();
            $currentPrice->save();
        }

        $sum = 0;
        foreach ($allArticles as $article) {
            $marketPrice = MarketPrices::where('article_id', $article->article_id)->first();
            $sum += $marketPrice ? $marketPrice->price : 1.00;
        }

        $mean = $sum / $nbArticles;
        $delta = $balanceMarket - $mean;
        $correction = round($delta, 4);

        foreach ($allArticles as $article) {
            $marketPrice = MarketPrices::where('article_id', $article->article_id)->first();
            if (!$marketPrice) continue;

            $adjusted = max($minPrice, min($maxPrice, round($marketPrice->price + $correction, 2)));
            $marketPrice->price = $adjusted;
            $marketPrice->save();
        }
    }

    public function getPrices()
    {
        $articles = MarketPrices::all();

        foreach ($articles as $article) {
            $articleModel = Articles::where('article_id', $article->article_id)->first();
            $article->article_name = $articleModel ? $articleModel->article_name : null;
            $article->category_id = $articleModel ? $articleModel->category_id : null;
        }

        $response = [
            'refresh' => 5000,
            'data' => $articles,
        ];

        return response()->json($response)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}
