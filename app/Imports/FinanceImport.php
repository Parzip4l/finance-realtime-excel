<?php

namespace App\Imports;

use App\Models\DynamicFinanceData;
use App\Events\FinanceDataUpdated;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FinanceImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Simpan sebagai JSON supaya dinamis
        $finance = DynamicFinanceData::create([
            'data' => $row
        ]);

        // Broadcast ke Next.js agar dashboard update realtime
        broadcast(new FinanceDataUpdated($finance->toArray()));

        return $finance;
    }
}
