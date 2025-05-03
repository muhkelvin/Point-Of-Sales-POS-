<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Bread;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('bread_id')
                            ->label('Bread')
                            ->options(Bread::all()->pluck('name', 'id'))
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set, $livewire) {
                                $bread = Bread::find($state);
                                if ($bread) {
                                    $set('price', $bread->price);
                                }

                                // Recalculate totals
                                static::recalculateTotals($livewire);
                            })
                            ->searchable(),

                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function ($state, $livewire) {
                                // Recalculate totals whenever quantity changes
                                static::recalculateTotals($livewire);
                            }),

                        Forms\Components\TextInput::make('price')
                            ->disabled()
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->columns(3)
                    ->createItemButtonLabel('Add Item')
                    ->orderable()
                    ->collapsible()
                    ->afterStateUpdated(function ($state, $livewire) {
                        // Recalculate totals whenever items are added or removed
                        static::recalculateTotals($livewire);
                    }),

                Forms\Components\Section::make('Rincian Pesanan')
                    ->schema([
                        Forms\Components\Placeholder::make('items_detail')
                            ->label('Daftar Item')
                            ->content(function ($livewire) {
                                $itemsHtml = '<div class="space-y-2">';

                                if (isset($livewire->data['items']) && is_array($livewire->data['items'])) {
                                    $counter = 1;
                                    foreach ($livewire->data['items'] as $index => $item) {
                                        if (!isset($item['bread_id']) || !isset($item['quantity']) || !isset($item['price'])) {
                                            continue;
                                        }

                                        $bread = Bread::find($item['bread_id']);
                                        $breadName = $bread ? $bread->name : 'Roti';
                                        $qty = $item['quantity'];
                                        $price = $item['price'];
                                        $subtotal = $qty * $price;

                                        $itemsHtml .= "<div>{$counter}. {$breadName} - Quantity: {$qty} - Total: Rp " . number_format($subtotal, 0, ',', '.') . "</div>";
                                        $counter++;
                                    }
                                }

                                $itemsHtml .= '</div>';
                                return new \Illuminate\Support\HtmlString($itemsHtml);
                            })
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('total_items')
                            ->label('Total Item')
                            ->numeric()
                            ->disabled(),

                        Forms\Components\TextInput::make('total_price')
                            ->label('Total Harga')
                            ->numeric()
                            ->disabled()
                            ->prefix('Rp'),
                    ])->columns(2),

                Forms\Components\Section::make('Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('payment')
                            ->label('Jumlah Bayar')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->debounce('3000ms') // Menambahkan jeda 1 detik
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $total = $get('total_price');
                                $change = $state - $total;
                                $set('change', max($change, 0));
                            }),

                        Forms\Components\TextInput::make('change')
                            ->label('Kembalian')
                            ->numeric()
                            ->disabled()
                            ->prefix('Rp'),
                    ])->columns(2),
            ]);
    }

    protected static function recalculateTotals($livewire): void
    {
        $data = $livewire->data;

        if (! isset($data['items']) || ! is_array($data['items'])) {
            return;
        }

        $totalItems = 0;
        $totalPrice = 0;

        foreach ($data['items'] as $item) {
            if (isset($item['quantity']) && isset($item['price'])) {
                $totalItems += $item['quantity'];
                $totalPrice += $item['quantity'] * $item['price'];
            }
        }

        $livewire->data['total_items'] = $totalItems;
        $livewire->data['total_price'] = $totalPrice;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('total_items'),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR', true),
                Tables\Columns\TextColumn::make('payment')
                    ->money('IDR', true),
                Tables\Columns\TextColumn::make('change')
                    ->money('IDR', true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit'   => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}

