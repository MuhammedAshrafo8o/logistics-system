<?php

namespace App\Modules\Finance\Controllers;

use App\Modules\Finance\Models\Expense;
use App\Modules\Finance\Requests\ListExpensesRequest;
use App\Modules\Finance\Requests\StoreExpenseRequest;
use App\Modules\Finance\Requests\UpdateExpenseRequest;
use App\Modules\Finance\Resources\ExpenseResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExpenseController
{
    public function index(ListExpensesRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $expenses = Expense::query()
            ->with('createdBy')
            ->when(isset($validated['category']), fn ($query) => $query->where('category', $validated['category']))
            ->when(isset($validated['payment_method']), fn ($query) => $query->where('payment_method', $validated['payment_method']))
            ->when(isset($validated['date_from']), fn ($query) => $query->whereDate('expense_date', '>=', $validated['date_from']))
            ->when(isset($validated['date_to']), fn ($query) => $query->whereDate('expense_date', '<=', $validated['date_to']))
            ->latest()
            ->get();

        return ExpenseResource::collection($expenses);
    }

    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $expense = Expense::create([
            ...$request->validated(),
            'created_by' => $request->user()?->id,
        ]);

        return (new ExpenseResource($expense->load('createdBy')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Expense $expense): ExpenseResource
    {
        return new ExpenseResource($expense->load('createdBy'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): ExpenseResource
    {
        $expense->update($request->validated());

        return new ExpenseResource($expense->fresh()->load('createdBy'));
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted successfully',
        ]);
    }
}
