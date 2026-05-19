@extends('layouts.app')

@section('page-title', 'Billing & Subscription Settings')

@section('content')
@include('admin.settings.partials.billing-subscription', [
    'isInTrial' => $isInTrial,
    'remainingTrialDays' => $remainingTrialDays,
    'shouldShowPaymentDue' => $shouldShowPaymentDue,
    'company' => $company,
    'currentPlan' => $currentPlan,
    'daysUntilExpiry' => $daysUntilExpiry,
    'isPaymentAvailable' => $isPaymentAvailable,
    'plans' => $plans,
    'paymentHistory' => $paymentHistory
])
@endsection
