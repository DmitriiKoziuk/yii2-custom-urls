<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2CustomUrls\services;

use DmitriiKoziuk\yii2Base\exceptions\StringDoesNotMatchException;
use DmitriiKoziuk\yii2CustomUrls\helpers\ArrayHelper;
use DmitriiKoziuk\yii2CustomUrls\exceptions\AddingDuplicateParamException;
use DmitriiKoziuk\yii2CustomUrls\exceptions\AddingDuplicateParamValueException;

final class UrlFilterService
{
    private $_filterMark = '/filter:';
    private $_paramsDelimiter = ';';
    private $_paramNameValuesDelimiter = '=';
    private $_paramValuesDelimiter = ',';
    private $_parsedParams = [];
    private $_userAddedParams = [];
    private $_userIgnoreParams = [];

    public function getFilterMark(): string
    {
        return $this->_filterMark;
    }

    /**
     * @param string $name
     * @return array|null
     */
    public function getParsedParamValues(string $name): ?array
    {
        return $this->_parsedParams[ $name ] ?? null;
    }

    /**
     * @return array
     */
    public function getParsedParams(): array
    {
        return $this->_parsedParams;
    }

    /**
     * Return params that user adding.
     * @return array
     */
    public function getAddedParams(): array
    {
        return $this->_userAddedParams;
    }

    /**
     * @param string  $name      Param name
     * @param string  $value     Param value
     * @param boolean $overwriteExistParamValue default false. Throw exception if you adding duplicate param value.
     * @return $this
     * @throws AddingDuplicateParamValueException
     * @throws StringDoesNotMatchException
     */
    public function addParam(string $name, string $value, bool $overwriteExistParamValue = false): self
    {
        $this->_checkParamNameForConsistency($name);
        $this->_checkParamValueForConsistency($value);
        if (empty($this->_userAddedParams[ $name ][ $value ]) || $overwriteExistParamValue) {
            $this->_userAddedParams[ $name ][ $value ] = $value;
        } else {
            throw new AddingDuplicateParamValueException(
                "Param '{$name}' with value '{$value}' already exist."
            );
        }
        return $this;
    }

    /**
     * Massive params adding.
     * [
     *     'p1' => 'v1',
     *     'p2' => [
     *         'v1',
     *         'v2',
     * ]
     * @param array   $params
     * @param boolean $overwriteExistParamValues
     * @return $this
     * @throws AddingDuplicateParamValueException
     * @throws StringDoesNotMatchException
     */
    public function addParams(array $params, bool $overwriteExistParamValues = false): self
    {
        foreach ($params as $paramName => $paramValues) {
            if (is_array($paramValues)) {
                foreach ($paramValues as $paramValue) {
                    $this->addParam($paramName, $paramValue, $overwriteExistParamValues);
                }
            } else {
                $this->addParam($paramName, $paramValues, $overwriteExistParamValues);
            }
        }
        return $this;
    }

    /**
     * @param string $name
     * @param string|null $value ignore all param if value is null
     * @return $this
     * @throws StringDoesNotMatchException
     */
    public function addIgnoredParam(string $name, string $value = null): self
    {
        $this->_checkParamNameForConsistency($name);
        if (empty($value)) {
            /** Ignore all param */
            $this->_userIgnoreParams[ $name ] = $value;
        } else {
            $this->_checkParamValueForConsistency($value);
            /** Ignore param value */
            $this->_userIgnoreParams[ $name ][ $value ] = null;
        }
        return $this;
    }

    /**
     * Cut out get params from url before use this method.
     * @param string $url
     * @throws AddingDuplicateParamException
     * @throws AddingDuplicateParamValueException
     * @throws StringDoesNotMatchException
     */
    public function parseUrl(string $url): void
    {
        $filterMark = $this->getFilterMark();
        $isFilterMarkPosition = mb_strpos($url, $filterMark);
        if (false !== $isFilterMarkPosition) {
            $parsedString = mb_substr($url, ($isFilterMarkPosition + mb_strlen($filterMark)));
            $this->_checkParsedStringForConsistency($parsedString);
            $filterParts = explode($this->_paramsDelimiter, $parsedString);
            foreach ($filterParts as $part) {
                if (false !== mb_strpos($part, $this->_paramNameValuesDelimiter)) {
                    list($paramName, $valueString) = explode($this->_paramNameValuesDelimiter, $part);
                    if ($this->_isParsedParamAlreadyExist($paramName)) {
                        throw new AddingDuplicateParamException("Url filter adding duplicate param '{$paramName}'.");
                    }
                    $values = explode($this->_paramValuesDelimiter, $valueString);
                    foreach ($values as $value) {
                        $this->_addParsedParam($paramName, $value);
                    }
                }
            }
        }
    }

    /**
     * Check is params and values in alphabetical order.
     * Very important for SEO that params and values was in alphabetical order, because url
     * /some-page-name/filter:param1=value1;param2=value1,value2
     * is not the same as
     * /some-page-name/filter:param2=value2,value1;param1=value1
     * for Google and other search engines.
     * They will assume that these are different pages with duplicate content.
     * @return bool
     */
    public function isParamsInTheAlphabeticalOrder(): bool
    {
        $tmp = $this->_parsedParams;
        $tmp = $this->_sortParamsInTheRightOrder($tmp);
        return ArrayHelper::isArrayKeysIsInIdenticalPosition($this->_parsedParams, $tmp);
    }

    /**
     * @return string like '/filter:paramName=value1;paramName2=value1,value2,value3'.
     */
    public function getFilterString(): string
    {
        $string = '';
        $preparedParams = $this->_getPrepareParams();
        if (! empty($preparedParams)) {
            $string .= $this->getFilterMark();
            foreach ($preparedParams as $paramName => $paramValues) {
                $string .= $paramName . $this->_paramNameValuesDelimiter;
                foreach ($paramValues as $paramValue) {
                    $paramValueDelimiter = count($paramValues) > 1 ? $this->_paramValuesDelimiter : '';
                    $string .= $paramValue . $paramValueDelimiter;
                }
                $string = rtrim($string, $this->_paramValuesDelimiter);
                $string .= $this->_paramsDelimiter;
            }
            $string = rtrim($string, $this->_paramsDelimiter);
        }
        return $string;
    }

    public function clearAddedParams(): void
    {
        $this->_userAddedParams = [];
    }

    public function clearIgnoredParams(): void
    {
        $this->_userIgnoreParams = [];
    }

    /**
     * @return string
     * @see getFilterString
     */
    public function __toString(): string
    {
        return $this->getFilterString();
    }

    /**
     * Combine parsed params from url and params that user adding and then
     * remove params that user wished to be ignored.
     * @return array
     */
    private function _getPrepareParams(): array
    {
        $params = array_merge($this->_parsedParams, $this->_userAddedParams);
        foreach ($this->_userIgnoreParams as $ignoreParamName => $ignoreParamValues) {
            if (empty($ignoreParamValues)) {
                unset($params[ $ignoreParamName ]);
            } else {
                foreach ($ignoreParamValues as $ignoreParamValueName => $value) {
                    unset($params[ $ignoreParamName ][ $ignoreParamValueName ]);
                }
            }
        }
        return $this->_sortParamsInTheRightOrder($params);
    }

    /**
     * @param string $name
     * @param string $value
     * @throws AddingDuplicateParamValueException
     * @throws StringDoesNotMatchException
     */
    private function _addParsedParam(string $name, string $value): void
    {
        $this->_checkParamNameForConsistency($name);
        $this->_checkParamValueForConsistency($value);
        if (empty($this->_parsedParams[ $name ][ $value ])) {
            $this->_parsedParams[ $name ][ $value ] = $value;
        } else {
            throw new AddingDuplicateParamValueException(
                "Url filter adding duplicate param '{$name}' value '{$value}'."
            );
        }
    }

    private function _isParsedParamAlreadyExist(string $name): bool
    {
        return empty($this->_parsedParams[ $name ]) ? false : true;
    }

    /**
     * Sort params in the right order.
     * Very important for SEO that params was in the right order, because url
     * /some-page-name/filter:param1=value1;param2=value1,value2
     * is not the same as
     * /some-page-name/filter:param2=value2,value1;param1=value1
     * for Google and other search engines.
     * They will assume that these are different pages with duplicate content.
     * @param array $params
     * @return array sorted params
     */
    private function _sortParamsInTheRightOrder(array $params): array
    {
        ksort($params);
        foreach ($params as $name => $values) {
            ksort($params[ $name ]);
        }
        return $params;
    }

    /**
     * @param string $parsedString
     * @throws StringDoesNotMatchException
     */
    private function _checkParsedStringForConsistency(string $parsedString): void
    {
        if (! preg_match('/^[\w-]+=([\w-]+|[\w-]+(,[\w-]+)*)(;[\w-]+=([\w-]+|[\w-]+(,[\w-]+)*))*$/', $parsedString)) {
            throw new StringDoesNotMatchException('Filter can not parse string that does not match pattern.');
        }
    }

    /**
     * Param name cannot consist only of numbers.
     * @param string $string
     * @throws StringDoesNotMatchException
     */
    private function _checkParamNameForConsistency(string $string): void
    {
        if (preg_match('/^[[:digit:]]$/', $string)) {
            throw new StringDoesNotMatchException('Param name cannot consist only of numbers.');
        }
    }

    /**
     * @param string $string
     * @throws StringDoesNotMatchException
     */
    private function _checkParamValueForConsistency(string $string): void
    {
        if (! preg_match('/^[\w-]+$/', $string)) {
            throw new StringDoesNotMatchException('Param name does not comply with the rules.');
        }
    }
}