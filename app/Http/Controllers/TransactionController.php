<?php 

namespace App\Http\Controllers;

use App\Models\Articles;
use App\Models\MarketPrices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function handle(Request $request)
    {
        $badgeId = $request->input('badge_id');
        $items = $request->input('items'); // [[article_id, quantity], ...]
        $sessionId = $request->header('session_id');

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
        $rounded = round($price, 2);
        $priceToId = [
            0.10 => 20445,
            0.20 => 20446,
            0.30 => 21003,
            0.40 => 20682,
            0.50 => 23596,
            0.60 => 23597,
            0.70 => 23598,
            0.80 => 23599,
            0.90 => 23600,
            1.00 => 23601,
            1.10 => 23602,
            1.20 => 23603, 
            1.30 => 23604,
            1.40 => 23605,
        ];
        return $priceToId[$rounded] ?? 23601;
    }

    private function updateMarket($articleId, $quantity)
    {
        $lastPrice = $this->getCurrentMarketPrice($articleId);
        $newPrice = min(1.40, $lastPrice + 0.10 * $quantity);

        MarketPrices::firstOrCreate([
            'article_id' => $articleId,
            'price' => $newPrice,
            'updated_at' => now(),
        ]);
    }
}
