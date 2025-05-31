<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

// We'll fetch the user's tenant info on component mount
const userTenant = ref(null);
const isLoading = ref(true);

onMounted(async () => {
    try {
        // In a real implementation, you'd fetch the actual tenant here
        // This is just a placeholder to simulate fetching user's tenant
        setTimeout(() => {
            isLoading.value = false;
        }, 300);
    } catch (error) {
        console.error("Failed to fetch tenant information:", error);
        isLoading.value = false;
    }
});
</script>

<template>
    <Head title="ShopWhiz Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">ShopWhiz Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Welcome and Stats Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <h3 class="text-xl font-medium text-blue-900 mb-2">Welcome to Your Product Management Platform</h3>
                        <p class="text-gray-700 mb-6">Manage your product feeds and integrate AI-powered search into your e-commerce store.</p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div class="bg-white p-4 rounded-lg shadow-sm flex items-center border border-blue-100">
                                <div class="rounded-full bg-blue-100 p-3 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-gray-500 text-sm">Manage Your Feeds</div>
                                    <Link :href="route('merchant.feeds.index')" class="text-blue-600 hover:text-blue-800 font-medium">View All Feeds</Link>
                                </div>
                            </div>

                            <div class="bg-white p-4 rounded-lg shadow-sm flex items-center border border-blue-100">
                                <div class="rounded-full bg-green-100 p-3 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-gray-500 text-sm">Your Account</div>
                                    <Link :href="route('profile.edit')" class="text-green-600 hover:text-green-800 font-medium">Edit Profile</Link>
                                </div>
                            </div>

                            <div class="bg-white p-4 rounded-lg shadow-sm flex items-center border border-blue-100">
                                <div class="rounded-full bg-purple-100 p-3 mr-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-gray-500 text-sm">Your API</div>
                                    <Link :href="route('merchant.tenants.index')" class="text-purple-600 hover:text-purple-800 font-medium">Manage API Token</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Sections -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Feed Management Section -->
                    <div class="col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Your Product Feeds</h3>
                                <Link :href="route('merchant.feeds.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                                    Add New Feed
                                </Link>
                            </div>

                            <div v-if="isLoading" class="text-center py-6">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                                <p class="mt-2 text-gray-600">Loading...</p>
                            </div>
                            <div v-else class="bg-gray-50 border rounded-md p-6 text-center">
                                <p class="text-gray-600 mb-4">View and manage all your product feed sources.</p>
                                <Link :href="route('merchant.feeds.index')" class="inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition-colors duration-200">
                                    Manage All Feeds
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Tenant Configuration Section -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Your Store Configuration</h3>

                            <div v-if="isLoading" class="text-center py-6">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                            </div>
                            <div v-else class="space-y-4">
                                <div class="bg-gray-50 p-4 rounded-md">
                                    <p class="text-gray-600 mb-2">Configure your store settings and API token for the ShopWhiz platform.</p>
                                    <Link :href="route('merchant.tenants.index')" class="text-blue-600 hover:text-blue-800">
                                        View Store Settings
                                    </Link>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-md">
                                    <h4 class="font-medium mb-1">Integration</h4>
                                    <p class="text-gray-600 text-sm mb-2">Get your chat widget code to embed in your e-commerce store.</p>
                                    <Link :href="route('merchant.tenants.index')" class="text-blue-600 hover:text-blue-800 text-sm">
                                        Get Integration Code
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
