<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2CustomUrls\helpers;

class ArrayHelper
{
    /**
     * @todo: Rewrite. Method work but ugly.
     * @param array $firstArray
     * @param array $secondArray
     * @return bool
     */
    public static function isArrayKeysIsInIdenticalPosition(array $firstArray, array $secondArray): bool
    {
        $status = true;
        foreach ($firstArray as $firstKey => $item) {
            $secondKey = key($secondArray);
            next($secondArray);
            if ($firstKey === $secondKey) {
                if (is_array($item)) {
                    $status = static::isArrayKeysIsInIdenticalPosition(
                        $firstArray[ $firstKey ], $secondArray[ $secondKey ]
                    );
                    if (! $status) {
                        break;
                    }
                }
            } else {
                return false;
            }
        }
        return $status;
    }
}