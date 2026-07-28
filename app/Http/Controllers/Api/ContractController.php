<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $query = Contract::with(['motorcycle', 'user', 'witnesses', 'guarantors', 'payments']);
        if ($request->user()->role === 'user') {
            $query->where('user_id', $request->user()->id);
        }
        return response()->json($query->latest()->get());
    }

    public function show($id)
    {
        return response()->json(
            Contract::with(['motorcycle', 'user', 'witnesses', 'guarantors', 'payments'])->findOrFail($id)
        );
    }

    // Manager generates contract after approving request
    public function store(Request $request)
    {
        $request->validate([
            'contract_request_id' => 'required|exists:contract_requests,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'total_amount' => 'required|numeric|min:0',
            'witness' => 'required|array',
            'witness.full_name' => 'required|string',
            'witness.nida_number' => 'required|string',
            'witness.phone' => 'required|string',
            'witness.address' => 'required|string',
            'guarantor' => 'required|array',
            'guarantor.full_name' => 'required|string',
            'guarantor.phone' => 'required|string',
            'guarantor.address' => 'required|string',
            'guarantor.nida_number' => 'required|string',
        ]);

        return DB::transaction(function () use ($request) {
            $cr = ContractRequest::findOrFail($request->contract_request_id);

            $contract = Contract::create([
                'user_id' => $cr->user_id,
                'motorcycle_id' => $cr->motorcycle_id,
                'contract_request_id' => $cr->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'total_amount' => $request->total_amount,
                'paid_amount' => 0,
                'balance' => $request->total_amount,
                'status' => 'active',
            ]);

            $contract->witnesses()->create($request->witness);
            $contract->guarantors()->create($request->guarantor);

            return response()->json($contract->load('witnesses', 'guarantors'), 201);
        });
    }

    public function terminate(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);
        $contract->update(['status' => 'terminated']);
        $contract->motorcycle->update(['status' => 'available']);
        return response()->json($contract);
    }
}