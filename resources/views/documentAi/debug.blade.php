@extends('layouts.app')

@section('content')
<div class="container">

    <h2>Document AI Debug</h2>

    <p><strong>File:</strong> {{ $invoice->drive_file_name }}</p>
    <p><strong>Project:</strong> {{ $invoice->project->name }}</p>

    <hr>

    <h4>Extracted Fields</h4>
    <ul>
        <li>Supplier: {{ $invoice->supplier }}</li>
        <li>Invoice Number: {{ $invoice->invoice_number }}</li>
        <li>Date: {{ $invoice->invoice_date }}</li>
        <li>Currency: {{ $invoice->currency }}</li>
        <li>Total: {{ $invoice->total_amount }}</li>
    </ul>

    <hr>

    <h4>Raw JSON</h4>
    <pre style="background:#111;color:#0f0;padding:20px;border-radius:10px;max-height:600px;overflow:auto;">
{{ $json ?? "NO DEBUG DATA FOUND" }}
    </pre>

    <hr>

    <h4>PDF</h4>
    <iframe 
        src="https://drive.google.com/file/d/{{ $invoice->drive_file_id }}/preview"
        style="width:100%;height:700px;border:0;">
    </iframe>

</div>
@endsection
