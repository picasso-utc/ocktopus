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
            if (in_array($categoryId, [10, 11])) {    // Si c'est une bière, on remplace par le prix du marché
                $currentPrice = $this->getCurrentMarketPrice($articleId);
                $replacementArticleId = $this->mapPriceToBeerArticle($currentPrice);
                $mappedItems[] = [$replacementArticleId, $quantity];
                $this->updateMarket($articleId, $quantity);
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
        return Articles::where('article_id', $articleId)->first()->category_id;
    }

    private function getCurrentMarketPrice($articleId)
    {
        return MarketPrices::where('article_id', $articleId)
            ->first()
            ->price ?? 1.0;
    }

    private function mapPriceToBeerArticle($price)
    {
        $rounded = number_format(round($price, 2), 1, '.', '');
        $priceToId = [
            '0.1' => 20445,
            '0.2' => 20446,
            '0.3' => 21003,
            '0.4' => 20682,
            '0.5' => 23596,
            '0.6' => 23597,
            '0.7' => 23598,
            '0.8' => 23599,
            '0.9' => 23600,
            '1.0' => 23601,
            '1.1' => 23602,
            '1.2' => 23603, 
            '1.3' => 23604,
            '1.4' => 23605,
        ];
        return $priceToId[$rounded] ?? 23601;
    }

    private function updateMarket($articleId, $quantity)
    {
        $maxPrice = 1.4;
        $minPrice = 0.1;
        $priceStep = 0.05;
        $fluctuationRange = 0.02;

        $article = Articles::where('article_id', $articleId)->first();
        if (!$article) return;

        $categoryId = $article->category_id;

        $allArticles = Articles::where('category_id', $categoryId)->get();

        foreach ($allArticles as $otherArticle) {
            $currentPrice = MarketPrices::firstOrCreate(
                ['article_id' => $otherArticle->article_id],
            );

            $newPrice = $currentPrice->price;

            if ($otherArticle->article_id == $articleId) {
                $newPrice += $priceStep * $quantity;
            } else {
                $newPrice -= $priceStep * 0.5;
            }

            $fluctuation = rand(-100, 100) / 100 * $fluctuationRange;
            $newPrice += $fluctuation;

            $newPrice = max($minPrice, min($maxPrice, $newPrice));

            $currentPrice->price = $newPrice;
            $currentPrice->updated_at = now();
            $currentPrice->save();
        }
    }
}
