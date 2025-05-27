<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    tenants: Object,
});
</script>

<template>
    <Head title="Manage Tenants" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manage Tenants</h2>
                <Link
                    :href="route('merchant.tenants.create')"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md"
                >
                    Add New Tenant
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
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Name</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Meilisearch Index</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Vector Namespace</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Feeds</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="tenant in tenants.data" :key="tenant.id" class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <Link :href="route('merchant.tenants.show', tenant.id)" class="text-blue-600 hover:text-blue-900 font-medium">
                                                {{ tenant.name }}
                                            </Link>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ tenant.meilisearch_index }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ tenant.vector_namespace }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ tenant.feeds_count }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <div class="flex space-x-2">
                                                <Link :href="route('merchant.tenants.edit', tenant.id)" class="text-yellow-600 hover:text-yellow-900">
                                                    Edit
                                                </Link>
                                                <Link :href="route('merchant.tenants.show', tenant.id)" class="text-blue-600 hover:text-blue-900">
                                                    View
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="tenants.data.length === 0">
                                        <td colspan="5" class="py-4 px-4 text-center text-gray-500">No tenants found.</td>
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
