<?php

namespace App\Modules\Finance\Services;

class InvoicePdfService
{
    /**
     * @param array<string, mixed> $preview
     */
    public function render(array $preview): string
    {
        $lines = [
            'Merchant Invoice',
            'Invoice Number: '.$preview['invoice_number'],
            'Merchant: '.$preview['merchant']['name'],
            'Phone: '.($preview['merchant']['phone'] ?? '-'),
            'Period: '.($preview['period']['start'] ?? '-').' to '.($preview['period']['end'] ?? '-'),
            'Status: '.$preview['status'],
            'Total COD: '.$preview['totals']['total_cod'],
            'Total Shipping Fees: '.$preview['totals']['total_shipping_fees'],
            'Total Warehouse Charges: '.$preview['totals']['total_warehouse_charges'],
            'Total Payable: '.$preview['totals']['total_payable'],
            'Warehouse Charges:',
        ];

        foreach ($preview['warehouse_charges'] as $charge) {
            $lines[] = sprintf(
                '%s | %s | Qty %s | Unit %s | Amount %s',
                $charge['charge_date'] ?? '-',
                $charge['type'],
                $charge['quantity'],
                $charge['unit_price'],
                $charge['amount']
            );
        }

        $lines[] = 'Shipments:';

        foreach ($preview['shipments'] as $shipment) {
            $lines[] = sprintf(
                '%s | %s | %s | COD %s | Fee %s',
                $shipment['shipment_number'],
                $shipment['customer_name'],
                $shipment['payment_type'],
                $shipment['cod_amount'],
                $shipment['shipping_fee']
            );
        }

        $contentLines = [];
        $y = 790;

        foreach ($lines as $line) {
            $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line);
            $contentLines[] = sprintf('BT /F1 10 Tf 40 %d Td (%s) Tj ET', $y, $escaped);
            $y -= 14;

            if ($y < 40) {
                break;
            }
        }

        $stream = implode("\n", $contentLines);
        $length = strlen($stream);

        $objects = [];
        $objects[] = "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj";
        $objects[] = "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj";
        $objects[] = "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj";
        $objects[] = "4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj";
        $objects[] = "5 0 obj << /Length {$length} >> stream\n{$stream}\nendstream endobj";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object."\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer << /Size ".(count($objects) + 1)." /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }
}
