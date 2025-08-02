<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl p-6 text-white">
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
                        <h3 class="font-semibold text-gray-900">Evaluations</h3>
                        <p class="text-sm text-gray-500">View evaluations</p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <x-heroicon-o-chart-bar class="w-6 h-6 text-purple-600" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Reports</h3>
                        <p class="text-sm text-gray-500">Generate reports</p>
                    </div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-gray-100 rounded-lg">
                        <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-gray-600" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Settings</h3>
                        <p class="text-sm text-gray-500">System settings</p>
                    </div>
                </div>
            </x-filament::card>
        </div>

        <!-- Recent Activity -->
        <x-filament::card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
                <a href="#" class="text-sm text-amber-600 hover:text-amber-700">View all</a>
            </div>
            <div class="space-y-3">
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">New candidate registered</p>
                        <p class="text-xs text-gray-500">John Doe - 2 minutes ago</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Evaluation completed</p>
                        <p class="text-xs text-gray-500">Jane Smith - 15 minutes ago</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Report generated</p>
                        <p class="text-xs text-gray-500">Monthly Analytics - 1 hour ago</p>
                    </div>
                </div>
            </div>
        </x-filament::card>
    </div>
</x-filament-panels::page> 