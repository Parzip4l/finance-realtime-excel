<!DOCTYPE html>
<html>
<head>
    <title>Upload Excel Keuangan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        /* Menggunakan font yang lebih modern dari Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa; /* Background sedikit abu-abu */
        }

        .container {
            max-width: 900px; /* Kontainer lebih lebar */
        }

        /* --- Style Baru Sesuai Permintaan --- */

        h3 {
            color: #D3242B; /* Warna merah utama */
            font-weight: 700;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        h4 {
            color: #f6821f; /* Warna oranye aksen */
            font-weight: 600;
        }

        /* Override Tombol Bootstrap */
        .btn-primary {
            background-color: #D3242B;
            border-color: #D3242B;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }
        .btn-primary:hover {
            background-color: #a01c21; /* Versi lebih gelap saat hover */
            border-color: #a01c21;
            transform: translateY(-2px); /* Efek hover modern */
        }

        /* --- Style Blok Kode (JSON & Endpoint) --- */

        .code-container {
            position: relative;
            background-color: #1e1e1e; /* Warna background VS Code */
            border-radius: 8px;
            overflow: hidden;
            margin-top: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .code-container pre {
            color: #d4d4d4; /* Warna teks default */
            padding: 20px;
            margin: 0;
            font-family: Consolas, 'Courier New', monospace;
            font-size: 14px;
            max-height: 500px; /* Batasi tinggi, bisa di-scroll */
            overflow: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        /* URL Endpoint di dalam pre */
        .code-container pre code.endpoint-url {
            color: #9cdcfe; /* Warna biru untuk URL */
            font-weight: 600;
        }

        .copy-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #D3242B; /* Tombol copy pakai warna merah */
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            opacity: 0.85;
            transition: all 0.2s ease;
        }
        .copy-btn:hover {
            opacity: 1;
            background-color: #f6821f; /* Ganti ke oranye saat hover */
            transform: scale(1.05);
        }
    </style>
</head>
<body class="p-5">
<div class="container">
    <h3 class="mb-4">Upload File Excel</h3>

    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('upload.excel') }}" enctype="multipart/form-data" class="mt-3 card card-body shadow-sm border-0">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">Pilih file Excel (.xlsx, .xls):</label>
            <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls" required>
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Tipe Data:</label>
            <input type="text" name="type" id="type" class="form-control" placeholder="contoh: budget_2025" required>
        </div>
        <button class="btn btn-primary">Upload & Proses</button>
    </form>

    @if(session('uploadedJson'))
    
        <h4 class="mt-5">API Endpoint (Live)</h4>
        <div class="code-container">
            <button class="copy-btn" id="copyEndpointButton">Copy</button>
            <pre><code id="endpointUrl" class="endpoint-url">
            {{ route('finance.data', ['type' => session('uploadedType', 'default')]) }}
            </code></pre>
        </div>

        <h4 class="mt-4">Raw JSON Berhasil Dibuat</h4>
        <div class="code-container">
            <button class="copy-btn" id="copyJsonButton">Copy</button>
            
            <pre><code id="jsonOutput">{{ session('uploadedJson') }}</code></pre>
        </div>
    @endif

</div>

<script>
    function copyToClipboard(textToCopy, buttonElement) {
        navigator.clipboard.writeText(textToCopy).then(function() {
            const originalText = buttonElement.textContent;
            buttonElement.textContent = 'Copied!';
            buttonElement.style.backgroundColor = '#28a745'; // Warna sukses (hijau)

            setTimeout(function() {
                buttonElement.textContent = originalText;
                buttonElement.style.backgroundColor = '#D3242B'; // Kembali ke warna merah
            }, 2000);
        }).catch(function(err) {
            console.error('Gagal menyalin teks: ', err);
            buttonElement.textContent = 'Gagal';
            setTimeout(function() {
                buttonElement.textContent = 'Copy';
            }, 2000);
        });
    }

    // Ambil tombol copy JSON
    const copyJsonButton = document.getElementById('copyJsonButton');
    if (copyJsonButton) {
        copyJsonButton.addEventListener('click', function() {
            const jsonText = document.getElementById('jsonOutput').textContent;
            copyToClipboard(jsonText, copyJsonButton);
        });
    }

    // Ambil tombol copy Endpoint
    const copyEndpointButton = document.getElementById('copyEndpointButton');
    if (copyEndpointButton) {
        copyEndpointButton.addEventListener('click', function() {
            const endpointText = document.getElementById('endpointUrl').textContent;
            copyToClipboard(endpointText, copyEndpointButton);
        });
    }
</script>

</body>
</html>