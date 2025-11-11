<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DynamicFinanceData;
use App\Events\FinanceDataUpdated;
use Maatwebsite\Excel\Facades\Excel;

class FinanceController extends Controller
{
    public function uploadPage()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'type' => 'nullable|string'
        ]);

        $type = $request->input('type', 'default');

        // Baca file Excel
        $collection = Excel::toCollection(null, $request->file('file'))->first();
        $data = $collection->toArray();

        // Ambil baris pertama sebagai header
        $headers = array_shift($data);

        $formattedData = [];
        foreach ($data as $index => $row) {
            if (is_array($row) && count($headers) == count($row)) {
                $rowData = array_combine($headers, $row);

                // Jika Department kosong di baris terakhir → "TOTAL"
                if (
                    (empty($rowData['Department']) || $rowData['Department'] === null)
                    && $index === count($data) - 1
                ) {
                    $rowData['Department'] = 'TOTAL';
                }

                $formattedData[] = $rowData;
            }
        }

        // Cek apakah type sudah ada
        $existingRecord = DynamicFinanceData::where('type', $type)->first();

        if ($existingRecord) {
            // 🔁 Update data lama
            $existingRecord->update([
                'meta' => ['headers' => $headers],
                'data' => $formattedData,
            ]);

            $message = "Data untuk type '{$type}' berhasil diperbarui.";
            $record = $existingRecord;
        } else {
            // ➕ Buat data baru
            $record = DynamicFinanceData::create([
                'type' => $type,
                'meta' => ['headers' => $headers],
                'data' => $formattedData
            ]);

            $message = "File Excel untuk type '{$type}' berhasil diupload dan disimpan!";
        }

        // Broadcast event (opsional)
        broadcast(new FinanceDataUpdated($record));

        // Kirim hasil JSON ke view
        $jsonString = json_encode($formattedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return back()
            ->with('success', $message)
            ->with('uploadedType', $type)
            ->with('uploadedJson', $jsonString);
    }



    public function getData($type = 'default')
    {
        $latestRecord = DynamicFinanceData::where('type', $type)->latest()->first();

        if (!$latestRecord) {
            return response()->json([]);
        }

        return response()->json([
            'meta' => $latestRecord->meta,
            'data' => $latestRecord->data
        ]);
    }

    public function getTotalOnly($type = 'default')
    {
        $latestRecord = DynamicFinanceData::where('type', $type)->latest()->first();

        if (!$latestRecord) {
            return response()->json([]);
        }

        $allData = $latestRecord->data;
        $totalRow = collect($allData)->firstWhere('Department', 'TOTAL');

        return response()->json($totalRow ?? []);
    }

    public function list(Request $request)
    {
        $query = DynamicFinanceData::query();

        if ($search = $request->get('search')) {
            $query->where('type', 'like', "%{$search}%");
        }

        $records = $query->latest()->paginate(10);

        return view('list', compact('records'));
    }
}