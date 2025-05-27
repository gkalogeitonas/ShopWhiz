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
                    :href="route('admin.tenants.create')"
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
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feeds</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="tenant in tenants.data" :key="tenant.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ tenant.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ tenant.user ? tenant.user.name : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ tenant.feeds_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span
                                                :class="tenant.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            >
                                                {{ tenant.active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <Link
                                                :href="route('admin.tenants.show', tenant.id)"
                                                class="text-blue-600 hover:text-blue-900"
                                            >
                                                View
                                            </Link>
                                            <Link
                                                :href="route('admin.tenants.edit', tenant.id)"
                                                class="text-green-600 hover:text-green-900"
                                            >
                                                Edit
                                            </Link>
                                        </td>
                                    </tr>
                                    <tr v-if="tenants.data.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            No tenants found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination links (if available) -->
                        <div class="mt-4 flex justify-between" v-if="tenants.links && tenants.links.length > 3">
                            <div class="flex space-x-1">
                                <Link
                                    v-for="(link, i) in tenants.links"
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
