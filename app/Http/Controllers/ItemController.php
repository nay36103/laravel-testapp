<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function randomItems()
    {
        $itemsJson = File::get(resource_path('json/items.json'));
        $itemList = json_decode($itemsJson, true);

        $result = [];
        $selectedItems = [];
        $totalWeight = array_sum(array_column($itemList, 'chance')) * 100;

        $num = 1;
        for ($i = 0; $i < 100; $i++) {
            $getItem = false;
            do {
                $rand = mt_rand(1, $totalWeight);
                echo "round " . intval($i + 1) . " rand = " . $rand;

                foreach ($itemList as &$item) {
                    $rand -= $item['chance'] * 100;
                    if ($rand <= 0 && $item['stock'] > 0) {
                        $foundItem = false;
                        foreach ($result as &$resultItem) {
                            if ($resultItem['name'] === $item['name']) {
                                $resultItem['amount']++;
                                $foundItem = true;
                                break;
                            }
                        }

                        if (!$foundItem) {
                            $result[] = ['name' => $item['name'],'game_item_id' => $item['game_item_id'], 'amount' => 1];
                        }

                        echo nl2br(' ' . $num . '>' . $item['name'] . "\n");
                        $num += 1;
                        $item['stock']--;
                        $getItem = true;
                        break;
                    }
                }
            } while ($getItem === false);
        }
        echo nl2br("\n");
        foreach($result as $row){
            echo $row['name'] . '=>' . $row['amount'] . nl2br("\n");
        }

        return null;
    }
}
