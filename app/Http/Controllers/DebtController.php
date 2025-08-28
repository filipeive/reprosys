<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $query = Debt::with(['user', 'sale', 'order'])->latest();

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer')) {
            $query->where('customer_name', 'like', '%' . $request->customer . '%');
        }

        if ($request->filled('overdue_only')) {
            $query->overdue();
        }

        if ($request->filled('date_from')) {
            $query->whereDate('debt_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('debt_date', '<=', $request->date_to);
        }

        $debts = $query->paginate(15);

        // Estatísticas
        $stats = [
            'total_active' => Debt::where('status', '!=', 'paid')->sum('remaining_amount'),
            'total_overdue' => Debt::overdue()->sum('remaining_amount'),
            'count_active' => Debt::active()->count(),
            'count_overdue' => Debt::overdue()->count(),
            'count_paid_this_month' => Debt::paid()->whereMonth('updated_at', now()->month)->count()
        ];

        return view('debts.index', compact('debts', 'stats'));
    }

    public function create()
    {
        return view('debts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'customer_document' => 'nullable|string|max:20',
            'original_amount' => 'required|numeric|min:0.01',
            'debt_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:debt_date',
            'description' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $debt = Debt::create([
            'user_id' => auth()->id(),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_document' => $request->customer_document,
            'original_amount' => $request->original_amount,
            'remaining_amount' => $request->original_amount,
            'debt_date' => $request->debt_date,
            'due_date' => $request->due_date,
            'description' => $request->description,
            'notes' => $request->notes
        ]);

        return redirect()->route('debts.show', $debt)
            ->with('success', 'Dívida registrada com sucesso!');
    }

    public function show(Debt $debt)
    {
        $debt->load(['user', 'sale', 'order', 'payments.user']);
        return view('debts.show', compact('debt'));
    }

    public function edit(Debt $debt)
    {
        return view('debts.edit', compact('debt'));
    }

    public function update(Request $request, Debt $debt)
    {
        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'customer_document' => 'nullable|string|max:20',
            'due_date' => 'nullable|date',
            'description' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $debt->update($request->only([
            'customer_name', 'customer_phone', 'customer_document',
            'due_date', 'description', 'notes'
        ]));

        return redirect()->route('debts.show', $debt)
            ->with('success', 'Dívida atualizada com sucesso!');
    }

    public function destroy(Debt $debt)
    {
        if ($debt->payments()->exists()) {
            return redirect()->route('debts.index')
                ->with('error', 'Não é possível excluir uma dívida que já possui pagamentos.');
        }

        $debt->delete();

        return redirect()->route('debts.index')
            ->with('success', 'Dívida excluída com sucesso!');
    }

    public function addPayment(Request $request, Debt $debt)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $debt->remaining_amount,
            'payment_method' => 'required|in:cash,card,transfer,pix',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string'
        ]);

        if (!$debt->canAddPayment()) {
            return redirect()->route('debts.show', $debt)
                ->with('error', 'Não é possível adicionar pagamento a esta dívida.');
        }

        DB::transaction(function () use ($debt, $request) {
            $debt->addPayment(
                $request->amount,
                $request->payment_method,
                $request->notes
            );
        });

        $message = $debt->status === 'paid' ? 
            'Pagamento registrado! Dívida quitada completamente.' :
            'Pagamento registrado com sucesso!';

        return redirect()->route('debts.show', $debt)
            ->with('success', $message);
    }

    public function markAsPaid(Debt $debt)
    {
        if ($debt->status === 'paid') {
            return redirect()->route('debts.show', $debt)
                ->with('error', 'Dívida já está quitada.');
        }

        DB::transaction(function () use ($debt) {
            $debt->markAsPaid();
        });

        return redirect()->route('debts.show', $debt)
            ->with('success', 'Dívida marcada como paga!');
    }

    public function cancel(Debt $debt)
    {
        if ($debt->status === 'paid') {
            return redirect()->route('debts.show', $debt)
                ->with('error', 'Não é possível cancelar uma dívida já paga.');
        }

        $debt->update(['status' => 'cancelled']);

        return redirect()->route('debts.index')
            ->with('success', 'Dívida cancelada com sucesso!');
    }

    // Relatório de devedores
    public function debtorsReport(Request $request)
    {
        $query = Debt::with(['payments'])
            ->where('status', '!=', 'paid')
            ->selectRaw('customer_name, customer_phone, SUM(remaining_amount) as total_debt, COUNT(*) as debt_count, MIN(debt_date) as oldest_debt')
            ->groupBy('customer_name', 'customer_phone');

        if ($request->filled('customer')) {
            $query->where('customer_name', 'like', '%' . $request->customer . '%');
        }

        $debtors = $query->orderByDesc('total_debt')->paginate(20);

        return view('debts.debtors-report', compact('debtors'));
    }

    // Atualizar status de dívidas vencidas (comando/job)
    public function updateOverdueStatus()
    {
        $updatedCount = Debt::where('due_date', '<', now()->toDateString())
            ->where('status', 'active')
            ->update(['status' => 'overdue']);

        return response()->json([
            'message' => "Status atualizado para {$updatedCount} dívidas vencidas."
        ]);
    }

    // API para busca de clientes
    public function searchCustomers(Request $request)
    {
        $term = $request->get('term');
        
        $customers = Debt::select('customer_name', 'customer_phone')
            ->where('customer_name', 'like', '%' . $term . '%')
            ->orWhere('customer_phone', 'like', '%' . $term . '%')
            ->distinct()
            ->limit(10)
            ->get();

        return response()->json($customers);
    }
}