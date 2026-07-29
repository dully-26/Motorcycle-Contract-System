<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContractRequest;
use App\Models\Motorcycle;
use App\Services\NotificationService;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class ContractRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ContractRequest::with(['user', 'motorcycle', 'contract']);

        if ($request->user()->role === 'user') {
            $query->where('user_id', $request->user()->id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->get());
    }

    public function show($id)
    {
        return response()->json(ContractRequest::with(['user', 'motorcycle'])->findOrFail($id));
    }

    public function store(Request $request)
    {
        $request->validate([
            'motorcycle_id' => 'required|exists:motorcycles,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $motorcycle = Motorcycle::findOrFail($request->motorcycle_id);

        if ($motorcycle->listing_type !== 'contract') {
            return response()->json(['message' => 'This motorcycle is not available for contract'], 422);
        }
        if ($motorcycle->status !== 'available') {
            return response()->json(['message' => 'Motorcycle is not available'], 422);
        }

        $existing = ContractRequest::where('user_id', $request->user()->id)
            ->where('motorcycle_id', $motorcycle->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You already have a pending request for this motorcycle'], 422);
        }

        $reqModel = ContractRequest::create([
            'user_id' => $request->user()->id,
            'motorcycle_id' => $request->motorcycle_id,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        AuditLogger::log($request->user()->id, 'submitted_contract_request', 'ContractRequest', $reqModel->id,
            "Requested {$motorcycle->brand} {$motorcycle->model}");

        return response()->json($reqModel->load('motorcycle'), 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);

        $cr = ContractRequest::with('motorcycle')->findOrFail($id);

        if ($cr->status !== 'pending') {
            return response()->json(['message' => 'This request has already been reviewed'], 422);
        }

        $cr->update([
            'status' => $request->status,
            'reviewed_by' => $request->user()->id,
        ]);

        if ($request->status === 'approved') {
            $cr->motorcycle->update(['status' => 'rented']);
        }

        AuditLogger::log(
            $request->user()->id,
            "contract_request_{$request->status}",
            'ContractRequest',
            $cr->id,
            "{$cr->motorcycle->brand} {$cr->motorcycle->model} request {$request->status}"
        );

        NotificationService::send(
            $cr->user_id,
            'Contract Request ' . ucfirst($request->status),
            "Your request for {$cr->motorcycle->brand} {$cr->motorcycle->model} was {$request->status}.",
            'contract'
        );

        return response()->json($cr);
    }
}