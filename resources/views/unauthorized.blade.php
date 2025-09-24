{{-- resources/views/unauthorized.blade.php --}}
@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-24 w-24 text-red-500">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                    </path>
                </svg>
            </div>
            
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Access Denied
            </h2>
            
            <p class="mt-2 text-sm text-gray-600">
                @if(session('error'))
                    {{ session('error') }}
                @else
                    You do not have permission to access this area.
                @endif
            </p>
            
            <div class="mt-6 space-y-4">
                <p class="text-sm text-gray-500">
                    Current Role: 
                    <span class="font-semibold text-gray-700">
                        {{ Auth::user()->getRoleDisplayName() }}
                    </span>
                </p>
                
                <div class="space-y-2">
                    <p class="text-xs text-gray-500 font-semibold">Available Areas:</p>
                    
                    @if(Auth::user()->canAccessCashier())
                        <a href="{{ route('cashier.index') }}" 
                           class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors mr-2 mb-2">
                            Cashier
                        </a>
                    @endif
                    
                    @if(Auth::user()->canAccessKitchen())
                        <a href="{{ route('kitchen.index') }}" 
                           class="inline-block bg-green-600 text-white px-4 py-2 rounded-md text-sm hover:bg-green-700 transition-colors mr-2 mb-2">
                            Kitchen
                        </a>
                    @endif
                    
                    @if(Auth::user()->canAccessDashboard())
                        <a href="{{ route('dashboard') }}" 
                           class="inline-block bg-purple-600 text-white px-4 py-2 rounded-md text-sm hover:bg-purple-700 transition-colors mr-2 mb-2">
                            Dashboard
                        </a>
                    @endif
                    
                    @if(Auth::user()->canAccessSales())
                        <a href="{{ route('sales') }}" 
                           class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-700 transition-colors mr-2 mb-2">
                            Sales
                        </a>
                    @endif
                    
                    @if(Auth::user()->canAccessProduct())
                        <a href="{{ route('product') }}" 
                           class="inline-block bg-yellow-600 text-white px-4 py-2 rounded-md text-sm hover:bg-yellow-700 transition-colors mr-2 mb-2">
                            Products
                        </a>
                    @endif
                    
                    @if(Auth::user()->canAccessAdmin())
                        <a href="{{ route('admin.users') }}" 
                           class="inline-block bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700 transition-colors mr-2 mb-2">
                            Admin Panel
                        </a>
                    @endif
                </div>
                
                @if(!Auth::user()->canAccessCashier() && !Auth::user()->canAccessKitchen() && !Auth::user()->canAccessDashboard())
                    <p class="text-xs text-gray-500 italic">
                        Please contact your administrator for access permissions.
                    </p>
                @endif
            </div>
            
            <div class="mt-6">
                <button onclick="history.back()" 
                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-400 transition-colors mr-4">
                    Go Back
                </button>
                
                <a href="{{ route('profile.edit') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                    Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection