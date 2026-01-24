<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BitfinexService
{
    private $apiKey;
    private $apiSecret;
    private $baseUrl = 'https://api.bitfinex.com';

    public function __construct()
    {
        $this->apiKey = config('services.bitfinex.apikey');
        $this->apiSecret = config('services.bitfinex.apisecret');
    }

    public function getOrdersHistory()
    {
        // 1. Nonce exacto como en Python (* 1,000,000)
        $nonce = (string) intval(microtime(true) * 1000000);
        
        $endpoint = "/v2/auth/r/orders/hist";
        $body = "{}";

        // 2. Payload idéntico al de Python: /api + endpoint + nonce + body
        $signaturePayload = "/api{$endpoint}{$nonce}{$body}";
        
        // 3. Firma HMAC SHA384
        $signature = hash_hmac('sha384', $signaturePayload, $this->apiSecret);

        // 4. Petición usando el cliente HTTP de Laravel
        $response = Http::withHeaders([
            'bfx-apikey'    => $this->apiKey,
            'bfx-nonce'     => $nonce,
            'bfx-signature' => $signature,
            'Content-Type'  => 'application/json'
        ])
        ->withoutVerifying()
        ->withBody($body, 'application/json') // Enviamos el body crudo como en Python
        ->post($this->baseUrl . $endpoint);

        // LOG para ver detalles si falla
        if ($response->failed()) {
            Log::error("Bitfinex Auth Fail: " . $response->body());
        }

        return $response->json();
    }
}