<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Stats passed from controller
const props = defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Merchant Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Merchant Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">System Overview</h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-3xl font-bold text-blue-600">{{ stats.totalTenants }}</div>
                                <div class="text-sm text-gray-600">Total Tenants</div>
                            </div>

                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-3xl font-bold text-green-600">{{ stats.activeTenants }}</div>
                                <div class="text-sm text-gray-600">Active Tenants</div>
                            </div>

                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="text-3xl font-bold text-purple-600">{{ stats.totalFeeds }}</div>
                                <div class="text-sm text-gray-600">Total Feeds</div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <a
                                :href="route('merchant.tenants.index')"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md"
                            >
                                Manage Tenants
                            </a>

                            <a
                                :href="route('merchant.feeds.index')"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md"
                            >
                                Manage Feeds
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
