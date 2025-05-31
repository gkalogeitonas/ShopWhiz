<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { router } from '@inertiajs/vue3';

// Redirect to merchant dashboard on load if needed
onMounted(() => {
    // Uncomment the below line if you want to automatically redirect
    // router.visit(route('merchant.dashboard'));
});
</script>

<template>
    <Head title="ShopWhiz Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Welcome to ShopWhiz</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">Multi-Tenant Product Management Platform</h3>
                        <p class="mb-6">ShopWhiz is your complete solution for product feed management across multiple tenants.</p>

                        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 border border-blue-100 p-6 rounded-lg">
                                <h4 class="font-semibold text-blue-800 mb-2">Merchant Dashboard</h4>
                                <p class="text-gray-700 mb-4">Access your tenant management interface, view stats, and manage product feeds.</p>
                                <Link
                                    :href="route('merchant.dashboard')"
                                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition-colors duration-200"
                                >
                                    Go to Merchant Dashboard
                                </Link>
                            </div>

                            <div class="bg-green-50 border border-green-100 p-6 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2">Tenant Management</h4>
                                <p class="text-gray-700 mb-4">Create and manage your tenants, generate API tokens, and configure access.</p>
                                <Link
                                    :href="route('merchant.tenants.index')"
                                    class="inline-block bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded transition-colors duration-200"
                                >
                                    Manage Tenants
                                </Link>
                            </div>

                            <div class="bg-purple-50 border border-purple-100 p-6 rounded-lg">
                                <h4 class="font-semibold text-purple-800 mb-2">Feed Management</h4>
                                <p class="text-gray-700 mb-4">Configure and manage product feeds from various sources and formats.</p>
                                <Link
                                    :href="route('merchant.feeds.index')"
                                    class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded transition-colors duration-200"
                                >
                                    Manage Feeds
                                </Link>
                            </div>

                            <div class="bg-amber-50 border border-amber-100 p-6 rounded-lg">
                                <h4 class="font-semibold text-amber-800 mb-2">Your Profile</h4>
                                <p class="text-gray-700 mb-4">Update your account information, password, and profile settings.</p>
                                <Link
                                    :href="route('profile.edit')"
                                    class="inline-block bg-amber-600 hover:bg-amber-700 text-white font-medium py-2 px-4 rounded transition-colors duration-200"
                                >
                                    Edit Profile
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
