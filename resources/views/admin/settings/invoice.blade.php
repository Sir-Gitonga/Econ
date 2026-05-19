@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card mt-5">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Invoice - {{ $payment->invoice_number }}</h3>
                </div>
                <div class="card-body">

                    <!-- Payment Not Available Alert -->
                    @if($payment->status === 'pending' && !$payment->company->isPaymentAvailable())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="mb-3">
                            <strong><i class="fa fa-exclamation-circle"></i> Payment Not Available</strong>
                        </div>
                        <p class="mb-2">
                            Payment cannot be processed at this time because your subscription expires in 
                            <strong>{{ $payment->company->getDaysUntilExpiry() }}</strong> days.
                        </p>
                        <p class="mb-0">
                            <small>
                                For security and billing policy reasons, we do not accept payments within 10 days of your subscription expiry. 
                                <br>Please <strong>contact support</strong> if you need special assistance or want to extend your subscription early.
                            </small>
                        </p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <!-- Invoice Details Row -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold mb-3">Invoice Details</h5>
                            <p><strong>Invoice Number:</strong> {{ $payment->invoice_number }}</p>
                            <p><strong>Date:</strong> {{ $payment->created_at->format('M d, Y') }}</p>
                            <p><strong>Status:</strong> 
                                @if($payment->status === 'completed')
                                    <span class="badge badge-success">{{ ucfirst($payment->status) }}</span>
                                @elseif($payment->status === 'pending')
                                    <span class="badge badge-warning">{{ ucfirst($payment->status) }}</span>
                                @elseif($payment->status === 'processing')
                                    <span class="badge badge-info">{{ ucfirst($payment->status) }}</span>
                                @else
                                    <span class="badge badge-danger">{{ ucfirst($payment->status) }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold mb-3">Company Details</h5>
                            <p><strong>Company:</strong> {{ $payment->company->company_name }}</p>
                            <p><strong>Email:</strong> {{ $payment->company->email }}</p>
                            <p><strong>Phone:</strong> {{ $payment->company->phone }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Subscription Details -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="font-weight-bold mb-3">Subscription Details</h5>
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Plan Name</th>
                                        <th>Period</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $payment->plan->name }}</td>
                                        <td>{{ $payment->payment_for_period_start->format('M d, Y') }} - {{ $payment->payment_for_period_end->format('M d, Y') }}</td>
                                        <td class="text-right">Ksh {{ number_format($payment->amount) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Total Amount -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="text-right">
                                <h5 class="font-weight-bold">
                                    Total Amount: 
                                    <span class="text-success">Ksh {{ number_format($payment->amount) }}</span>
                                </h5>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Payment Completed Alert -->
                    @if($payment->status === 'completed')
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading">
                            <i class="fa fa-check-circle"></i> Payment Completed
                        </h5>
                        <p class="mb-1"><strong>Paid on:</strong> {{ $payment->paid_at->format('M d, Y H:i A') }}</p>
                        <p class="mb-0"><strong>M-Pesa Receipt:</strong> {{ $payment->mpesa_receipt_number }}</p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <!-- Payment Details (for pending payments) -->
                    @if($payment->status === 'pending' && $payment->company->isPaymentAvailable())
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading"><i class="fa fa-info-circle"></i> Payment Details</h5>
                        <p class="mb-2">This invoice is pending payment. Click the button below to proceed with payment via M-Pesa.</p>
                        <small class="text-muted">Amount Due: <strong>Ksh {{ number_format($payment->amount) }}</strong></small>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="mt-4">
                        <a href="{{ route('admin.settings.billing', [], false) }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Billing
                        </a>
                        <button onclick="window.print()" class="btn btn-info">
                            <i class="fa fa-print"></i> Print Invoice
                        </button>
                        
                        @if($payment->status === 'pending' && $payment->company->isPaymentAvailable())
                        <a href="{{ route('admin.settings.payment', ['subdomain' => request()->route('subdomain'), 'payment' => $payment->id], false) }}" class="btn btn-success">
                            <i class="fa fa-credit-card"></i> Pay Now
                        </a>
                        @elseif($payment->status === 'completed')
                        <a href="{{ route('admin.settings.download-invoice', ['subdomain' => request()->route('subdomain'), 'payment' => $payment->id], false) }}" class="btn btn-success">
                            <i class="fa fa-download"></i> Download PDF
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
