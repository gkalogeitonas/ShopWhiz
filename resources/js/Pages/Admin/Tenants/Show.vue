<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';

const props = defineProps({
    tenant: Object,
});

// API token management
const generateTokenForm = useForm({});
const revokeTokenForm = useForm({});

const generateToken = () => {
    generateTokenForm.post(route('admin.tenants.token.generate', props.tenant.id));
};

const revokeToken = () => {
    revokeTokenForm.delete(route('admin.tenants.token.revoke', props.tenant.id));
};

// Delete tenant modal
const confirmingTenantDeletion = ref(false);
const deleteForm = useForm({});

const confirmTenantDeletion = () => {
    confirmingTenantDeletion.value = true;
};

const deleteTenant = () => {
    deleteForm.delete(route('admin.tenants.destroy', props.tenant.id), {
        onSuccess: () => confirmingTenantDeletion.value = false,
    });
};

// Token visibility
const showToken = ref(false);
const toggleTokenVisibility = () => {
    showToken.value = !showToken.value;
};
</script>

<template>
    <Head :title="`Tenant: ${tenant.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tenant: {{ tenant.name }}</h2>
                <div class="flex space-x-2">
                    <Link
                        :href="route('admin.feeds.create', { tenant_id: tenant.id })"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md"
                    >
                        Add Feed
                    </Link>
                    <Link
                        :href="route('admin.tenants.edit', tenant.id)"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md"
                    >
                        Edit Tenant
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Tenant Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Tenant Details</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Name:</p>
                                <p class="font-medium">{{ tenant.name }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Owner:</p>
                                <p class="font-medium">{{ tenant.user ? tenant.user.name : 'N/A' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Meilisearch Index:</p>
                                <p class="font-mono text-sm bg-gray-100 p-2 rounded">{{ tenant.meilisearch_index }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Vector Namespace:</p>
                                <p class="font-mono text-sm bg-gray-100 p-2 rounded">{{ tenant.vector_namespace }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Status:</p>
                                <p>
                                    <span
                                        :class="tenant.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    >
                                        {{ tenant.active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Created:</p>
                                <p class="font-medium">{{ new Date(tenant.created_at).toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Token Management -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">API Token Management</h3>

                        <div v-if="tenant.api_token" class="mb-4">
                            <p class="text-sm text-gray-600 mb-2">API Token:</p>
                            <div class="flex items-center">
                                <div class="flex-grow bg-gray-100 p-2 rounded font-mono text-sm overflow-auto">
                                    <span v-if="showToken">{{ tenant.api_token }}</span>
                                    <span v-else>••••••••••••••••••••••••••••••</span>
                                </div>
                                <button
                                    @click="toggleTokenVisibility"
                                    class="ml-2 text-sm text-blue-600"
                                    type="button"
                                >
                                    {{ showToken ? 'Hide' : 'Show' }}
                                </button>
                            </div>

                            <div class="mt-4">
                                <form @submit.prevent="revokeToken">
                                    <DangerButton
                                        :disabled="revokeTokenForm.processing"
                                    >
                                        Revoke Token
                                    </DangerButton>
                                </form>
                            </div>
                        </div>

                        <div v-else>
                            <p class="text-sm text-gray-600 mb-4">No API token has been generated yet.</p>

                            <form @submit.prevent="generateToken">
                                <PrimaryButton
                                    :disabled="generateTokenForm.processing"
                                >
                                    Generate New Token
                                </PrimaryButton>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Feeds -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Feeds</h3>

                        <div v-if="tenant.feeds && tenant.feeds.length > 0">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Format</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Sync</th>
                                        <th class="border-b border-gray-200 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="feed in tenant.feeds" :key="feed.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ feed.url }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ feed.format }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ feed.sync_schedule }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ feed.last_sync_at ? new Date(feed.last_sync_at).toLocaleString() : 'Never' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <Link
                                                :href="route('admin.feeds.show', feed.id)"
                                                class="text-blue-600 hover:text-blue-900"
                                            >
                                                View
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-gray-500">
                            No feeds configured for this tenant yet.
                            <Link
                                :href="route('admin.feeds.create', { tenant_id: tenant.id })"
                                class="text-blue-600 hover:text-blue-900 ml-2"
                            >
                                Add Feed
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-red-800 mb-4">Danger Zone</h3>

                        <p class="text-sm text-gray-600 mb-4">
                            Deleting this tenant will permanently remove all associated data, including feeds,
                            search indices, and vector embeddings. This action cannot be undone.
                        </p>

                        <DangerButton @click="confirmTenantDeletion">
                            Delete Tenant
                        </DangerButton>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Tenant Confirmation Modal -->
        <Modal :show="confirmingTenantDeletion" @close="confirmingTenantDeletion = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete this tenant?
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Once this tenant is deleted, all of its resources and data will be permanently deleted.
                    Please confirm you would like to permanently delete this tenant.
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="confirmingTenantDeletion = false">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="ml-3"
                        :disabled="deleteForm.processing"
                        @click="deleteTenant"
                    >
                        Delete Tenant
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
