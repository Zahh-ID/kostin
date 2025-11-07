<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    private $serverKey;
    private $baseUrl;
    private $isProduction;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->baseUrl = config('midtrans.base_url');
        $this->isProduction = config('midtrans.is_production');

        if (empty($this->serverKey)) {
            throw new Exception('Midtrans Server Key is not configured');
        }
    }

    /**
     * Generate Authorization Header (Base64 encoded server key)
     */
    private function getAuthHeader()
    {
        return 'Basic ' . base64_encode($this->serverKey . ':');
    }

    /**
     * Create QRIS Payment Transaction
     * 
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function createQrisTransaction($params)
    {
        try {
            $url = $this->baseUrl . '/v2/charge';

            $orderId = Arr::get($params, 'order_id', Arr::get($params, 'transaction_details.order_id'));
            $grossAmount = Arr::get($params, 'gross_amount', Arr::get($params, 'transaction_details.gross_amount'));

            if (empty($orderId) || empty($grossAmount)) {
                throw new Exception('Midtrans transaction requires order_id and gross_amount.');
            }

            $payload = [
                'payment_type' => 'qris',
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => Arr::get($params, 'customer_name', Arr::get($params, 'customer_details.first_name', 'Customer')),
                    'email' => Arr::get($params, 'customer_email', Arr::get($params, 'customer_details.email', '')),
                    'phone' => Arr::get($params, 'customer_phone', Arr::get($params, 'customer_details.phone', '')),
                ],
                'item_details' => Arr::get($params, 'item_details', []),
            ];

            Log::info('Creating Midtrans QRIS transaction', [
                'url' => $url,
                'order_id' => $orderId,
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $this->getAuthHeader(),
            ])->post($url, $payload);

            if (! $response->successful()) {
                $error = $response->json();

                Log::error('Midtrans API Error', [
                    'status' => $response->status(),
                    'error' => $error,
                    'body' => $response->body(),
                ]);

                $errorMessage = $error['error_messages'][0]
                    ?? $error['status_message']
                    ?? $error['message']
                    ?? $response->body()
                    ?? 'Unknown error';

                if ($response->status() === 401) {
                    $errorMessage = 'Konfigurasi Midtrans tidak valid. Mohon periksa server key.';
                }

                throw new Exception("Midtrans Error: {$errorMessage}");
            }

            $data = $response->json();

            // Extract QRIS string from actions
            $qrImageUrl = null;
            if (isset($data['actions']) && is_array($data['actions'])) {
                foreach ($data['actions'] as $action) {
                    if ($action['name'] === 'generate-qr-code') {
                        $qrImageUrl = $action['url'];
                        break;
                    }
                }
            }

            return [
                'transaction_id' => $data['transaction_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
                'qr_image_url' => $qrImageUrl,
                'qris_string' => $qrImageUrl, // backward compatibility
                'qr_string' => $data['qr_string'] ?? null,
                'transaction_status' => $data['transaction_status'] ?? 'pending',
                'transaction_time' => $data['transaction_time'] ?? null,
                'acquirer' => $data['acquirer'] ?? null,
                'raw_response' => $data,
            ];

        } catch (Exception $e) {
            Log::error('Create QRIS Transaction Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Create Bank Transfer Transaction
     * 
     * @param array $params
     * @return array
     */
    public function createBankTransferTransaction($params)
    {
        try {
            $url = $this->baseUrl . '/v2/charge';

            $payload = [
                'payment_type' => 'bank_transfer',
                'transaction_details' => [
                    'order_id' => $params['order_id'],
                    'gross_amount' => $params['gross_amount'],
                ],
                'bank_transfer' => [
                    'bank' => $params['bank'] ?? 'bca', // bca, bni, bri, permata
                ],
                'customer_details' => [
                    'first_name' => $params['customer_name'] ?? 'Customer',
                    'email' => $params['customer_email'] ?? '',
                    'phone' => $params['customer_phone'] ?? '',
                ],
                'item_details' => $params['item_details'] ?? [],
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $this->getAuthHeader(),
            ])->post($url, $payload);

            if (!$response->successful()) {
                $error = $response->json();
                $errorMessage = $error['error_messages'][0] ?? 'Unknown error';
                throw new Exception("Midtrans Error: {$errorMessage}");
            }

            $data = $response->json();

            return [
                'transaction_id' => $data['transaction_id'] ?? null,
                'order_id' => $data['order_id'] ?? null,
                'va_numbers' => $data['va_numbers'] ?? [],
                'transaction_status' => $data['transaction_status'] ?? 'pending',
                'raw_response' => $data,
            ];

        } catch (Exception $e) {
            Log::error('Create Bank Transfer Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create GoPay Transaction
     */
    public function createGopayTransaction($params)
    {
        try {
            $url = $this->baseUrl . '/v2/charge';

            $payload = [
                'payment_type' => 'gopay',
                'transaction_details' => [
                    'order_id' => $params['order_id'],
                    'gross_amount' => $params['gross_amount'],
                ],
                'customer_details' => [
                    'first_name' => $params['customer_name'] ?? 'Customer',
                    'email' => $params['customer_email'] ?? '',
                    'phone' => $params['customer_phone'] ?? '',
                ],
                'item_details' => $params['item_details'] ?? [],
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $this->getAuthHeader(),
            ])->post($url, $payload);

            if (!$response->successful()) {
                $error = $response->json();
                throw new Exception($error['error_messages'][0] ?? 'Unknown error');
            }

            return $response->json();

        } catch (Exception $e) {
            Log::error('Create GoPay Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Verify Transaction Status
     * 
     * @param string $orderId
     * @return array
     * @throws Exception
     */
    public function getTransactionStatus($orderId)
    {
        try {
            $url = $this->baseUrl . '/v2/' . $orderId . '/status';

            Log::info('Getting transaction status', [
                'url' => $url,
                'order_id' => $orderId
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $this->getAuthHeader(),
            ])->get($url);

            if (!$response->successful()) {
                $error = $response->json();
                Log::error('Get Status Error', [
                    'status' => $response->status(),
                    'error' => $error
                ]);
                
                $errorMessage = $error['error_messages'][0] ?? 'Transaction not found';
                throw new Exception("Midtrans Error: {$errorMessage}");
            }

            $data = $response->json();

            return [
                'order_id' => $data['order_id'] ?? null,
                'transaction_id' => $data['transaction_id'] ?? null,
                'transaction_status' => $data['transaction_status'] ?? null,
                'fraud_status' => $data['fraud_status'] ?? null,
                'payment_type' => $data['payment_type'] ?? null,
                'gross_amount' => $data['gross_amount'] ?? null,
                'transaction_time' => $data['transaction_time'] ?? null,
                'settlement_time' => $data['settlement_time'] ?? null,
                'status_code' => $data['status_code'] ?? null,
                'raw_response' => $data,
            ];

        } catch (Exception $e) {
            Log::error('Get Transaction Status Error', [
                'order_id' => $orderId,
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Cancel Transaction
     */
    public function cancelTransaction($orderId)
    {
        try {
            $url = $this->baseUrl . '/v2/' . $orderId . '/cancel';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $this->getAuthHeader(),
            ])->post($url);

            if (!$response->successful()) {
                throw new Exception('Failed to cancel transaction');
            }

            return $response->json();

        } catch (Exception $e) {
            Log::error('Cancel Transaction Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Approve Transaction (for challenge status)
     */
    public function approveTransaction($orderId)
    {
        try {
            $url = $this->baseUrl . '/v2/' . $orderId . '/approve';

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $this->getAuthHeader(),
            ])->post($url);

            if (!$response->successful()) {
                throw new Exception('Failed to approve transaction');
            }

            return $response->json();

        } catch (Exception $e) {
            Log::error('Approve Transaction Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Verify Webhook Signature
     */
    public function verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)
    {
        $serverKey = $this->serverKey;
        $input = $orderId . $statusCode . $grossAmount . $serverKey;
        $hash = hash('sha512', $input);

        return $hash === $signatureKey;
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

    public function chargeQris(array $payload): array
    {
        return $this->createQrisTransaction($payload);
    }
}
