<?php
require_once __DIR__ . '/paystack_config.php';

function paystack_verify($reference)
{
    $result = ['success' => false, 'amount' => 0, 'currency' => '', 'error' => ''];

    $ch = curl_init('https://api.paystack.co/transaction/verify/' . rawurlencode($reference));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
            'Cache-Control: no-cache'
        ]
    ]);

    $body  = curl_exec($ch);
    $curlE = curl_error($ch);
    curl_close($ch);

    if ($body === false) {
        error_log('Paystack verify cURL error: ' . $curlE);
        $result['error'] = 'Could not reach Paystack to confirm the payment.';
        return $result;
    }

    $json = json_decode($body, true);

    if (!is_array($json) || empty($json['status']) || !isset($json['data']['status'])) {
        error_log('Paystack verify bad response: ' . $body);
        $result['error'] = 'Paystack could not confirm this payment.';
        return $result;
    }

    if ($json['data']['status'] !== 'success') {
        $result['error'] = 'Payment was not successful (' . $json['data']['status'] . ').';
        return $result;
    }

    $result['success']  = true;
    $result['amount']   = (int) $json['data']['amount'];
    $result['currency'] = (string) $json['data']['currency'];
    return $result;
}
?>
