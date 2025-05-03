<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Bread;
use App\Models\Transaction;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            // Calculate totals directly here to be absolutely sure
            $totalItems = 0;
            $totalPrice = 0;

            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    if (isset($item['quantity']) && isset($item['bread_id'])) {
                        $bread = Bread::find($item['bread_id']);
                        if ($bread) {
                            $price = (float)$bread->price;
                            $quantity = (int)$item['quantity'];
                            $totalItems += $quantity;
                            $totalPrice += $quantity * $price;
                        }
                    }
                }
            }

            // Create the transaction with the calculated totals
            $transaction = new Transaction();
            $transaction->total_items = $totalItems;
            $transaction->total_price = round($totalPrice, 2); // Ensure proper decimal formatting
            $transaction->payment = isset($data['payment']) ? round((float)$data['payment'], 2) : 0;
            $transaction->change = isset($data['payment']) ? round(max((float)$data['payment'] - $totalPrice, 0), 2) : 0;
            $transaction->save();

            // Now handle the items separately
            if (isset($data['items']) && is_array($data['items'])) {
                $sortOrder = 1;
                foreach ($data['items'] as $item) {
                    if (isset($item['bread_id']) && isset($item['quantity'])) {
                        $bread = Bread::find($item['bread_id']);
                        if ($bread) {
                            // Create the transaction item
                            $transaction->items()->create([
                                'bread_id' => $item['bread_id'],
                                'quantity' => (int)$item['quantity'],
                                'price'    => round((float)$bread->price, 2),
                                'sort'     => $sortOrder++,
                            ]);

                            // Update bread stock
                            $bread->stock -= (int)$item['quantity'];
                            $bread->save();
                        }
                    }
                }
            }

            return $transaction;
        });
    }
}
