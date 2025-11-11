<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Data Keuangan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        h3 {
            color: #D3242B;
            font-weight: 700;
            margin-bottom: 20px;
        }

        table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        th {
            background-color: #f6821f;
            color: white;
            font-weight: 600;
        }

        td, th {
            vertical-align: middle !important;
        }

        .copy-btn {
            background-color: #D3242B;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .copy-btn:hover {
            background-color: #f6821f;
            transform: scale(1.05);
        }

        .endpoint-url {
            font-family: Consolas, monospace;
            color: #0056b3;
        }

        /* Modal styling */
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }
        pre {
            background-color: #1e1e1e;
            color: #d4d4d4;
            border-radius: 8px;
            padding: 15px;
            font-family: Consolas, monospace;
            font-size: 14px;
            max-height: 500px;
            overflow: auto;
        }

        .search-input {
            max-width: 300px;
        }
    </style>
</head>
<body class="p-5">

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Daftar Data Endpoint</h3>
        <a href="{{ route('upload.page') }}" class="btn btn-primary">Upload Baru</a>
    </div>

    <!-- 🔍 Search Bar -->
    <form method="GET" action="{{ route('finance.list') }}" class="mb-3">
        <input type="text" name="search" class="form-control search-input" placeholder="Cari berdasarkan tipe..." value="{{ request('search') }}">
    </form>

    @if($records->isEmpty())
        <div class="alert alert-warning">Belum ada data endpoint yang diupload.</div>
    @else
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Tipe Data</th>
                    <th>Jumlah Baris</th>
                    <th>Tanggal Upload</th>
                    <th>Endpoint JSON</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $i => $record)
                    <tr>
                        <td class="text-center">{{ $records->firstItem() + $i }}</td>
                        <td>{{ $record->type }}</td>
                        <td class="text-center">{{ count($record->data ?? []) }}</td>
                        <td>{{ $record->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <code class="endpoint-url">{{ route('finance.data', ['type' => $record->type]) }}</code>
                        </td>
                        <td class="text-center">
                            <button class="copy-btn" onclick="copyToClipboard('{{ route('finance.data', ['type' => $record->type]) }}', this)">Copy</button>
                            <button class="btn btn-sm btn-outline-primary" onclick="showJsonModal('{{ $record->type }}')">Lihat</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $records->appends(['search' => request('search')])->links() }}
        </div>
    @endif
</div>

<!-- Modal -->
<div class="modal fade" id="jsonModal" tabindex="-1" aria-labelledby="jsonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Data JSON: <span id="modalType"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <pre id="jsonContent">Memuat data...</pre>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        btn.textContent = 'Copied!';
        btn.style.backgroundColor = '#28a745';
        setTimeout(() => {
            btn.textContent = 'Copy';
            btn.style.backgroundColor = '#D3242B';
        }, 2000);
    });
}

// 🚀 Fetch JSON dan tampilkan di modal
function showJsonModal(type) {
    const modal = new bootstrap.Modal(document.getElementById('jsonModal'));
    const content = document.getElementById('jsonContent');
    const modalType = document.getElementById('modalType');
    content.textContent = 'Memuat data...';
    modalType.textContent = type;

    fetch(`/api/finance/${type}/data`)
        .then(res => res.json())
        .then(json => {
            content.textContent = JSON.stringify(json, null, 2);
        })
        .catch(err => {
            content.textContent = 'Gagal memuat data: ' + err;
        });

    modal.show();
}
</script>

</body>
</html>
