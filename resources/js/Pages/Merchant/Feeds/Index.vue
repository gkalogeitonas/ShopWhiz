<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    feeds: Object,
    tenantId: String,
});
</script>

<template>
    <Head title="Manage Feeds" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Feeds</h2>
                <Link
                    :href="route('merchant.feeds.create', tenantId ? { tenant_id: tenantId } : {})"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md"
                >
                    Add New Feed
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Tenant</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">URL</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Format</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Schedule</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Last Sync</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Next Sync</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Status</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="feed in feeds.data" :key="feed.id" class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <Link :href="route('merchant.tenants.show', feed.tenant_id)" class="text-blue-600 hover:text-blue-900">
                                                {{ feed.tenant?.name }}
                                            </Link>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <a :href="feed.url" target="_blank" class="text-blue-600 hover:text-blue-900 underline">
                                                {{ feed.url.length > 30 ? feed.url.substring(0, 30) + '...' : feed.url }}
                                            </a>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <span class="capitalize">{{ feed.format.replace('_', ' ') }}</span>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200 capitalize">{{ feed.sync_schedule }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            {{ feed.last_sync_at ? new Date(feed.last_sync_at).toLocaleString() : 'Never' }}
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            {{ feed.next_sync_at ? new Date(feed.next_sync_at).toLocaleString() : 'N/A' }}
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <span :class="feed.active ? 'text-green-600' : 'text-red-600'">
                                                {{ feed.active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <div class="flex space-x-2">
                                                <Link :href="route('merchant.feeds.show', feed.id)" class="text-blue-600 hover:text-blue-900">
                                                    View
                                                </Link>
                                                <Link :href="route('merchant.feeds.edit', feed.id)" class="text-yellow-600 hover:text-yellow-900">
                                                    Edit
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="feeds.data.length === 0">
                                        <td colspan="8" class="py-4 px-4 text-center text-gray-500">No feeds found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination links would go here -->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
