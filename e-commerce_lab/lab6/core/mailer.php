<?php
function send_order_confirmation($toEmail, $toName, $order)
{
    $toEmail = str_replace(["\r", "\n"], '', (string) $toEmail);

    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL) || empty($order)) {
        return false;
    }

    $lines   = [];
    $lines[] = 'Hello ' . str_replace(["\r", "\n"], '', (string) $toName) . ',';
    $lines[] = '';
    $lines[] = 'Thank you for your order. Your payment was received.';
    $lines[] = '';
    $lines[] = 'Invoice No: ' . $order['invoice_no'];
    $lines[] = 'Date: ' . $order['order_date'];
    $lines[] = '';

    foreach ($order['items'] as $item) {
        $lines[] = sprintf(
            '%s x %d @ %s%.2f = %s%.2f',
            $item['product_title'], $item['qty'],
            CURRENCY_SYMBOL, $item['product_price'],
            CURRENCY_SYMBOL, $item['line_total']
        );
    }

    $lines[] = '';
    $lines[] = 'Total paid: ' . CURRENCY_SYMBOL . number_format($order['total'], 2);
    $lines[] = '';
    $lines[] = 'shoppn';

    $headers = "From: shoppn <no-reply@shoppn.local>\r\nContent-Type: text/plain; charset=UTF-8";

    $sent = @mail($toEmail, 'Your shoppn order #' . $order['invoice_no'], implode("\r\n", $lines), $headers);

    if (!$sent) {
        error_log('Order confirmation email could not be sent to ' . $toEmail);
    }

    return $sent;
}
?>
