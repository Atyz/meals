<?php

namespace App\Service;

use Doctrine\Common\Collections\Collection;

class ArrayHelper
{
    public function combine(array $datas): array
    {
        $combinations = [[]];

        foreach ($datas as $key => $items) {
            if ($items instanceof Collection) {
                $items = $items->toArray();
            }

            if (0 === count($items)) {
                continue;
            }

            $tmp = [];

            foreach ($combinations as $olditem) {
                foreach ($items as $item) {
                    $tmp[] = array_merge($olditem, [$key => $item]);
                }
            }

            $combinations = $tmp;
        }

        return $combinations;
    }
}
