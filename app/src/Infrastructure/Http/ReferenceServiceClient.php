<?php

namespace App\Infrastructure\Http;

final class ReferenceServiceClient extends BaseHttpClient
{
    /**
     * GET /reference/countries
     *
     * @return array{countries: array<int, array{code: string, name_en: string}>}
     */
    public function getCountries(): array
    {
        $response = $this->request('GET', '/reference/countries');
        return $this->decodeResponse($response);
    }

    /**
     * GET /reference/currencies
     *
     * @return array{currencies: array<int, array{code: string, name_en: string, symbol: string}>}
     */
    public function getCurrencies(): array
    {
        $response = $this->request('GET', '/reference/currencies');
        return $this->decodeResponse($response);
    }
}
