{{-- resources/views/components/navigation.blade.php --}}
@php
    use App\Helpers\NavigationHelper;
    $navigationItems = NavigationHelper::getNavigationItems();
    $quickActions = NavigationHelper::getQuickActions();
@endphp

<nav class="bg-white shadow-lg border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <img class="h-8 w-8" src="/images/logo.png" alt="POS Logo">
                </div>
                <div class="hidden md:block ml-4">
                    <span class="text-xl font-bold text-gray-800">POS System</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-4">
                @foreach($navigationItems as $item)
                    <a href="{{ route($item['route']) }}" 
                       class="flex items-center px-3 py-2 rounded-md text-sm font-medium 
                              {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') 
                                 ? 'bg-blue-100 text-blue-700 border border-blue-200' 
                                 : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}
                              transition-colors duration-200"
                       title="{{ $item['description'] }}">
                        <i class="fas fa-{{ $item['icon'] }} mr-2"></i>
                        {{ $item['name'] }}
                    </a>
                @endforeach
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                <!-- Role Badge -->
                <div class="hidden md:block">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                 @if(Auth::user()->isAdministrator()) bg-red-100 text-red-800
                                 @elseif(Auth::user()->isManager()) bg-purple-100 text-purple-800
                                 @elseif(Auth::user()->isCashier()) bg-green-100 text-green-800
                                 @elseif(Auth::user()->isKitchenStaff()) bg-orange-100 text-orange-800
                                 @else bg-gray-100 text-gray-800
                                 @endif">
                        {{ Auth::user()->getRoleDisplayName() }}
                    </span>
                </div>

                <!-- User Dropdown -->
                <div class="relative">
                    <button type="button" 
                            class="flex items-center text-sm rounded-full bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            id="user-menu-button" 
                            aria-expanded="false" 
                            aria-haspopup="true"
                            onclick="toggleDropdown('user-menu')">
                        <span class="sr-only">Open user menu</span>
                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                        <span class="ml-2 text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down ml-1 text-gray-400"></i>
                    </button>

                    <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 hidden"
                         id="user-menu"
                         role="menu" 
                         aria-orientation="vertical" 
                         aria-labelledby="user-menu-button">
                        <!-- User Info Section -->
                        <div class="px-4 py-3">
                            <p class="text-sm text-gray-900 font-medium">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ Auth::user()->getRoleDisplayName() }}</p>
                        </div>

                        <!-- Quick Actions -->
                        @if(count($quickActions) > 0)
                        <div class="py-1">
                            <div class="px-4 py-2">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Quick Actions</p>
                            </div>
                            @foreach($quickActions as $action)
                                <a href="{{ route($action['route']) }}" 
                                   class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                   role="menuitem">
                                    <i class="fas fa-{{ $action['icon'] }} mr-3 text-gray-400 group-hover:text-gray-500"></i>
                                    {{ $action['name'] }}
                                </a>
                            @endforeach
                        </div>
                        @endif

                        <!-- Profile & Settings -->
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" 
                               class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                               role="menuitem">
                                <i class="fas fa-user-cog mr-3 text-gray-400 group-hover:text-gray-500"></i>
                                Profile Settings
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" 
                                        class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                        role="menuitem">
                                    <i class="fas fa-sign-out-alt mr-3 text-gray-400 group-hover:text-gray-500"></i>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button type="button" 
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100"
                        onclick="toggleDropdown('mobile-menu')">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="md:hidden hidden" id="mobile-menu">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 border-t border-gray-200 bg-gray-50">
            <!-- Role Badge Mobile -->
            <div class="px-3 py-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                             @if(Auth::user()->isAdministrator()) bg-red-100 text-red-800
                             @elseif(Auth::user()->isManager()) bg-purple-100 text-purple-800
                             @elseif(Auth::user()->isCashier()) bg-green-100 text-green-800
                             @elseif(Auth::user()->isKitchenStaff()) bg-orange-100 text-orange-800
                             @else bg-gray-100 text-gray-800
                             @endif">
                    {{ Auth::user()->getRoleDisplayName() }}
                </span>
            </div>

            <!-- Mobile Navigation Links -->
            @foreach($navigationItems as $item)
                <a href="{{ route($item['route']) }}" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium
                          {{ request()->routeIs($item['route']) || request()->routeIs($item['route'].'.*') 
                             ? 'bg-blue-100 text-blue-700' 
                             : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <i class="fas fa-{{ $item['icon'] }} mr-3"></i>
                    {{ $item['name'] }}
                </a>
            @endforeach

            <!-- Mobile User Links -->
            <div class="border-t border-gray-200 pt-4">
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                    <i class="fas fa-user-cog mr-3"></i>
                    Profile Settings
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="flex w-full items-center px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
function toggleDropdown(elementId) {
    const element = document.getElementById(elementId);
    element.classList.toggle('hidden');
    
    // Close other dropdowns
    if (elementId === 'user-menu') {
        document.getElementById('mobile-menu').classList.add('hidden');
    } else if (elementId === 'mobile-menu') {
        document.getElementById('user-menu').classList.add('hidden');
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const userMenu = document.getElementById('user-menu');
    const mobileMenu = document.getElementById('mobile-menu');
    const userMenuButton = document.getElementById('user-menu-button');
    
    if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
        userMenu.classList.add('hidden');
    }
    
    if (!event.target.closest('#mobile-menu') && !event.target.closest('button')) {
        mobileMenu.classList.add('hidden');
    }
});
</script>