<?php

namespace App\Http\Controllers;

use App\Models\Payutc;

class GoodiesController extends Controller
{
    public function getWinner()
    {
        $response = getListConsumer(); #il me manque donc à faire marcher cette fonction à l'aide de requete
        $listPicMember = getListPicMember(); #et celle la aussi
        $userIndex = random_int(0, count($response) - 1);

        $selectedUserLogin = $response[$userIndex]['login'];

        if (!in_array($selectedUserLogin, $excludedUserLogins)) {
            Winner::create(['Winner' => $selectedUserLogin, 'PickedUp' => false]);
        }

   public function isPickedUp(string $login)
    {
        $winner = Winner::where('Winner', $login)->first();

        if ($winner) {
            return $winner->PickedUp;
        }

        return null;
    }
