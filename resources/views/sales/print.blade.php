<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprovante de Venda #{{ $sale->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Comprovante de Venda</h2>
        <p><strong>Venda #{{ $sale->id }}</strong></p>
        <p>Data: {{ $sale->sale_date->format('d/m/Y H:i') }}</p>
    </div>
    <p><strong>Cliente:</strong> {{ $sale->customer_name ?? '-' }}</p>
    <p><strong>Telefone:</strong> {{ $sale->customer_phone ?? '-' }}</p>
    <p><strong>Método de Pagamento:</strong> {{ ucfirst($sale->payment_method) }}</p>
    <hr>
    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Qtd</th>
                <th>Preço Unit.</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Produto removido' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td>{{ number_format($item->total_price, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="total">Total: {{ number_format($sale->total_amount, 2, ',', '.') }}</p>
    <p><strong>Observações:</strong> {{ $sale->notes ?? '-' }}</p>
    <hr>
    <p style="text-align:center;">Obrigado pela preferência!</p>
    {{-- esconder na hora de imprimir e fechar a janela --}}
    @if(!request()->has('print'))
        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button onclick="window.print()" class="btn btn-primary">Imprimir</button>
            <button onclick="window.close()" class="btn btn-secondary">Fechar</button>
        </div>
    @endif

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
        }
        window.onafterprint = function() {
            window.close();
        }
        //clicar automaticamte o botao fechar depois de imprimir ou cancelar a impressao num interval de 1 segundo
        setTimeout(function() {
            window.close();
        }, 1000);

    </script>
</body>
</html>