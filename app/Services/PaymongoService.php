<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymongoService
{
    private $secretKey;
    private $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('paymongo.secret_key');
        $this->baseUrl = config('paymongo.base_url');
    }

    public function createPaymentIntent($amount, $currency = 'PHP', $description = 'Order Payment')
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post($this->baseUrl . '/payment_intents', [
                    'data' => [
                        'attributes' => [
                            'amount' => $amount * 100, // Convert to centavos
                            'payment_method_allowed' => ['gcash'],
                            'payment_method_options' => [
                                'card' => [
                                    'request_three_d_secure' => 'automatic'
                                ]
                            ],
                            'currency' => $currency,
                            'capture_type' => 'automatic',
                            'description' => $description,
                        ]
                    ]
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayMongo Payment Intent Creation Failed', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PayMongo Payment Intent Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function createPaymentMethod($paymentIntentId, $type = 'gcash')
    {
        try {
            $data = [
                'data' => [
                    'attributes' => [
                        'type' => $type
                    ]
                ]
            ];

            // Add proper details based on payment method type
            $data['data']['attributes']['redirect'] = [
                'success_url' => route('kiosk.paymentSuccess') . '?payment_intent_id=' . $paymentIntentId,
                'failed_url' => route('kiosk.paymentFailed') . '?payment_intent_id=' . $paymentIntentId
            ];

            $response = Http::withBasicAuth($this->secretKey, '')
                ->post($this->baseUrl . '/payment_methods', $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayMongo Payment Method Creation Failed', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PayMongo Payment Method Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function attachPaymentMethod($paymentIntentId, $paymentMethodId)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post($this->baseUrl . "/payment_intents/{$paymentIntentId}/attach", [
                    'data' => [
                        'attributes' => [
                            'payment_method' => $paymentMethodId,
                            'return_url' => route('kiosk.payment.success') . '?payment_intent_id=' . $paymentIntentId
                        ]
                    ]
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('PayMongo Payment Method Attachment Failed', [
                'response' => $response->json(),
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('PayMongo Payment Method Attachment Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function retrievePaymentIntent($paymentIntentId)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get($this->baseUrl . "/payment_intents/{$paymentIntentId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('PayMongo Retrieve Payment Intent Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}