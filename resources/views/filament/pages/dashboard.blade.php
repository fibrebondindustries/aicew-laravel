<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Welcome to AICEW Admin Panel</h1>
                    <p class="text-amber-100 mt-2">Manage your candidate evaluations and system settings efficiently</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold">{{ now()->format('M d, Y') }}</div>
                    <div class="text-amber-100">{{ now()->format('l') }}</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-filament::card>
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <x-heroicon-o-users class="w-6 h-6 text-blue-600" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Candidates</h3>
                        <p class="text-sm text-gray-500">Manage candidates</p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <x-heroicon-o-clipboard-document-check class="w-6 h-6 text-green-600" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Jobs</h3>
                        <p class="text-sm text-gray-500">Manage Jobs</p>
                    </div>
                </div>
            </x-filament::card>

          
        </div>

        <!-- Recent Activity -->
   
    </div>
</x-filament-panels::page> 