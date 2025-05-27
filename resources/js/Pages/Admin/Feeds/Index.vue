<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    feeds: Object,
    tenantId: Number,
});
</script>

<template>
    <Head title="Manage Feeds" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ tenantId ? 'Tenant Feeds' : 'All Feeds' }}
                </h2>
                <Link
                    :href="route('admin.feeds.create', tenantId ? { tenant_id: tenantId } : {})"
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
                                        <th v-if="!tenantId" class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feed URL</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Format</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Sync</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="feed in feeds.data" :key="feed.id" class="hover:bg-gray-50">
                                        <td v-if="!tenantId" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <Link
                                                :href="route('admin.tenants.show', feed.tenant_id)"
                                                class="text-blue-600 hover:text-blue-900"
                                            >
                                                {{ feed.tenant ? feed.tenant.name : 'N/A' }}
                                            </Link>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <div class="max-w-xs truncate">{{ feed.url }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="capitalize">{{ feed.format.replace('_', ' ') }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="capitalize">{{ feed.sync_schedule }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ feed.last_sync_at ? new Date(feed.last_sync_at).toLocaleString() : 'Never' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span
                                                :class="feed.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            >
                                                {{ feed.active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <Link
                                                :href="route('admin.feeds.show', feed.id)"
                                                class="text-blue-600 hover:text-blue-900"
                                            >
                                                View
                                            </Link>
                                            <Link
                                                :href="route('admin.feeds.edit', feed.id)"
                                                class="text-green-600 hover:text-green-900"
                                            >
                                                Edit
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="feeds.data.length === 0">
                                        <td :colspan="tenantId ? 6 : 7" class="px-6 py-4 text-center text-gray-500">
                                            No feeds found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination links (if available) -->
                        <div class="mt-4 flex justify-between" v-if="feeds.links && feeds.links.length > 3">
                            <div class="flex space-x-1">
                                <Link
                                    v-for="(link, i) in feeds.links"
                                    :key="i"
                                    v-html="link.label"
                                    :href="link.url"
                                    :class="{'text-gray-600 px-2 py-1': true, 'bg-blue-500 text-white': link.active}"
                                    class="rounded"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
