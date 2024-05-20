<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('pdf.css') }}" type="text/css">
    <title>Invoice</title>
    <style>
        h4 {
            margin: 0;
        }

        .w-full {
            width: 100%;
        }

        .w-half {
            width: 50%;
        }

        .margin-top {
            margin-top: 1.25rem;
        }

        .footer {
            font-size: 0.875rem;
            padding: 1rem;
            background-color: rgb(241 245 249);
        }

        table {
            width: 100%;
            border-spacing: 0;
        }

        table,
        tr,
        td {
            border: dotted black 1px;
        }

        table.products {
            font-size: 0.875rem;
        }

        table.products tr {
            background-color: white;
        }

        table.products th {
            color: #000000;
            padding: 0.5rem;
        }

        table tr.items {
            background-color: rgb(241 245 249);
        }

        table tr.items td {
            padding: 0.5rem;
        }

        .total {
            text-align: right;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
    </style>
</head>

<body>
    <table class="w-full">
        <tr>
            <td class="w-half">
                {{-- <img src="{{ asset('favicon.ico') }}" alt="laravel daily" width="200" /> --}}
            </td>
            <td class="w-half">
                <h2>Invoice ID: 834847473</h2>
            </td>
        </tr>
    </table>

    <div class="margin-top">
        <table class="w-full">
            <tr>
                <td class="w-half">
                    <div>
                        <h4>To:</h4>
                    </div>
                    <div>John Doe</div>
                    <div>123 Acme Str.</div>
                </td>
                <td class="w-half">
                    <div>
                        <h4>From:</h4>
                    </div>
                    <div>PT ABADI</div>
                    <div>Jakarta Barat</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="margin-top">
        <table class="products">
            <tr>
                <th>Qty</th>
                <th>Description</th>
                <th>Price</th>
            </tr>
            <tr class="items">
                {{-- @foreach ($record as $item)
                    <td>
                        {{ $item->quantity }}
                    </td>
                    <td>
                        {{ $item->description }}
                    </td>
                    <td>
                        {{ $item->price }}
                    </td>
                @endforeach --}}
            </tr>
        </table>
    </div>

    <div class="total">
        Total: $129.00 USD
    </div>

    <div class="footer margin-top">
        <div>Thank you</div>
        <div>&copy; Laravel </div>
    </div>
</body>

</html>
