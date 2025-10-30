<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class MidtransService
{
    private Client $client;

    private string $serverKey;

    private bool $isProduction;

    public function __construct(?Client $client = null)
    {
        $config = config('services.midtrans');

        $this->serverKey = (string) ($config['server_key'] ?? '');
        $this->isProduction = (bool) ($config['is_production'] ?? false);
        $baseUri = $this->isProduction
            ? 'https://api.midtrans.com/v2/'
            : 'https://api.sandbox.midtrans.com/v2/';

        $this->client = $client ?? new Client([
            'base_uri' => $baseUri,
            'timeout' => 10,
            'auth' => [$this->serverKey, ''],
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function chargeQris(array $payload): array
    {
        if ($this->serverKey === '') {
            throw new RuntimeException('Midtrans server key is not configured.');
        }

        try {
            $response = $this->client->post('charge', [
                'json' => $payload,
            ]);
        } catch (GuzzleException $exception) {
            Log::error('Midtrans charge request failed', [
                'message' => $exception->getMessage(),
            ]);

            throw new RuntimeException('Cannot connect to Midtrans.');
        }

        $data = json_decode((string) $response->getBody(), true);

        if (! is_array($data)) {
            throw new RuntimeException('Invalid response from Midtrans.');
        }

        return $data;
    }

    public function buildQrisPayload(string $orderId, string $description, int $amount, string $customerName, string $customerEmail): array
    {
        return [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'item_details' => [
                [
                    'id' => $orderId,
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => $description,
                ],
            ],
            'customer_details' => [
                'first_name' => $customerName,
                'email' => $customerEmail,
            ],
        ];
    }

    public function isValidSignature(string $signatureKey, string $orderId, string $statusCode, string $grossAmount): bool
    {
        if ($this->serverKey === '') {
            return false;
        }

        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$this->serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }

    public function normalizeNotification(array $payload): array
    {
        return [
            'order_id' => Arr::get($payload, 'order_id'),
            'transaction_status' => Arr::get($payload, 'transaction_status'),
            'payment_type' => Arr::get($payload, 'payment_type'),
            'gross_amount' => (int) Arr::get($payload, 'gross_amount', 0),
            'settlement_time' => Arr::get($payload, 'settlement_time'),
            'status_code' => Arr::get($payload, 'status_code'),
            'signature_key' => Arr::get($payload, 'signature_key'),
        ];
    }
}
