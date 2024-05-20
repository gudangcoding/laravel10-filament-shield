<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class InvoiceController extends Controller
{

    public function index(Request $request)
    {
        echo "";
    }

    public function print(Invoice $invoice)
    {
        // Tentukan nama printer yang terhubung di sistem operasi
        // $printerName = "USB001";
        $printerName = "EPSON LX-310 ESC/P";

        // Buat konektor untuk printer
        $connector = new WindowsPrintConnector($printerName);

        // Buat instance Printer
        $printer = new Printer($connector);

        // Mulai mencetak
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("laravel daily\n");
        $printer->feed();

        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("Invoice ID: " . $invoice->id . "\n\n");

        $printer->text("To:\n");
        $printer->text("John Doe\n");
        $printer->text("123 Acme Str.\n\n");

        $printer->text("From:\n");
        $printer->text("PT ABADI\n");
        $printer->text("Jakarta Barat\n\n");

        $printer->text("Qty  Description  Price\n");
        // Tambahkan detail item di sini, misalnya
        $printer->text("1    Example Item  $129.00\n\n");

        $printer->text("Total: $" . $invoice->amount . " USD\n\n");

        $printer->text("Thank you\n");
        $printer->text("© Laravel\n");

        // Potong kertas (jika printer mendukung)
        $printer->cut();

        // Tutup koneksi printer
        $printer->close();

        // return redirect()->route('invoices.index')->with('success', 'Invoice printed successfully!');
    }
}
