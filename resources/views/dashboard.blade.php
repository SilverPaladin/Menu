<x-layouts.app :title="__('TV Menu Display Guide')">
    <div class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">TV Menu Display System</h1>
                <p class="mt-2 text-xl text-gray-600 dark:text-gray-300">User Guide</p>
            </div>

            <!-- Introduction -->
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Welcome to Your TV Menu Display System</h2>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    This self-contained system allows you to create and manage menu displays optimized for viewing on large TVs. 
                    The system features a dark theme, large fonts, and high contrast to ensure visibility from a distance.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Collections</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">Create and manage product collections with custom headers and items.</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Screens</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">Configure display screens with multiple columns of collections.</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">View Mode</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-300">Launch the full-screen TV display mode to show your menus.</p>
                        <div class="mt-4">
                            <flux:button variant="primary" href="{{ route('viewer') }}" target="_blank">Launch Viewer</flux:button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- How To Use Section -->
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">How To Use This System</h2>
                
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">1. Create Collections</h3>
                    <div class="pl-6 border-l-4 border-indigo-500">
                        <p class="text-gray-600 dark:text-gray-300 mb-2">Collections are groups of items that will be displayed in columns on your TV screen.</p>
                        <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                            <li>Go to the Collections section and click "Create New Collection"</li>
                            <li>Give your collection a name and upload a header image</li>
                            <li>Add items to your collection</li>
                            <li>Adjust font size and other display settings as needed</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">2. Configure Screens</h3>
                    <div class="pl-6 border-l-4 border-indigo-500">
                        <p class="text-gray-600 dark:text-gray-300 mb-2">Screens define how your collections are arranged in columns on the TV display.</p>
                        <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                            <li>Go to the Screens section and click "Create New Screen"</li>
                            <li>Give your screen a name</li>
                            <li>Add columns to your screen</li>
                            <li>Assign collections to each column</li>
                        </ul>
                    </div>
                </div>
                
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">3. Launch Viewer Mode</h3>
                    <div class="pl-6 border-l-4 border-indigo-500">
                        <p class="text-gray-600 dark:text-gray-300 mb-2">The viewer mode displays your screen in a TV-optimized format.</p>
                        <ul class="list-disc pl-6 text-gray-600 dark:text-gray-300 space-y-2">
                            <li>Click "Launch Viewer" to open the full-screen display</li>
                            <li>Select which screen you want to display</li>
                            <li>The display will show your columns with their assigned collections</li>
                            <li>Click on items to hide them from the display</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tips Section -->
            <div class="mb-10">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Tips for Optimal Display</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow-sm">
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-gray-300">Use high-contrast header images for better visibility</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-gray-300">Keep item names concise for better readability</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-gray-300">Increase font size for viewing from greater distances</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-600 dark:text-gray-300">Use the dark theme for reduced eye strain in dimly lit environments</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
