<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class PdfController extends Controller
{
    // public function __invoke(SalesOrder $order)
    // {
    //     return Pdf::loadView('pdf', ['record' => $order])
    //         ->download($order->number . '.pdf');
    // }

    public function invoice(Request $request)
    {
        // $data = SalesOrder::where('id', $request->id());

        // $pdf = Pdf::loadView('pdf');



        $data = [
            [
                'number' => '123',
                'customer_name' => 'joni',
                'status' => 'proses',
                'quantity' => 'proses',
                'description' => 'proses',
                'total_price' => 1000,
                'price' => 1000,
                'order_date' => date('d M Y'),
            ]
        ];

        // $pdf = Pdf::loadView('pdf',  $data);
        $pdf = Pdf::loadView('pdf', ['record' => $data]);

        // return $pdf->download();
        // return $pdf->download($order->order_number . '.pdf');
        return $pdf->stream();
    }
}
