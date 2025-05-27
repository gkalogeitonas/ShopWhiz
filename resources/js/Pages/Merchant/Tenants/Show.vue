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

// Forms for token management
const generateTokenForm = useForm({});
const revokeTokenForm = useForm({});

const generateToken = () => {
    generateTokenForm.post(route('merchant.tenants.token.generate', props.tenant.id));
};

const revokeToken = () => {
    revokeTokenForm.delete(route('merchant.tenants.token.revoke', props.tenant.id));
};

// For the delete tenant confirmation
const confirmingTenantDeletion = ref(false);
const deleteForm = useForm({});

const confirmTenantDeletion = () => {
    confirmingTenantDeletion.value = true;
};

const deleteTenant = () => {
    deleteForm.delete(route('merchant.tenants.destroy', props.tenant.id), {
        onSuccess: () => {
            confirmingTenantDeletion.value = false;
        }
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
                        :href="route('merchant.feeds.create', { tenant_id: tenant.id })"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md"
                    >
                        Add Feed
                    </Link>
                    <Link
                        :href="route('merchant.tenants.edit', tenant.id)"
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
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tenant Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-600">Name</p>
                                <p class="font-semibold">{{ tenant.name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Owner</p>
                                <p class="font-semibold">{{ tenant.user?.name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Meilisearch Index</p>
                                <p class="font-semibold">{{ tenant.meilisearch_index }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Vector Namespace</p>
                                <p class="font-semibold">{{ tenant.vector_namespace }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Status</p>
                                <p class="font-semibold" :class="tenant.active ? 'text-green-600' : 'text-red-600'">
                                    {{ tenant.active ? 'Active' : 'Inactive' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- API Token Management -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">API Token</h3>
                        <div class="flex flex-col space-y-4">
                            <div v-if="tenant.api_token" class="rounded-md bg-gray-100 p-4">
                                <div class="flex items-center">
                                    <span v-if="showToken" class="font-mono break-all">{{ tenant.api_token }}</span>
                                    <span v-else class="font-mono">••••••••••••••••••••••••••••••••</span>
                                    <button
                                        @click="toggleTokenVisibility"
                                        class="ml-2 text-sm text-blue-600 hover:text-blue-800"
                                    >
                                        {{ showToken ? 'Hide' : 'Show' }}
                                    </button>
                                </div>
                            </div>
                            <div v-else class="text-yellow-600">No API token generated yet.</div>

                            <div class="flex space-x-2">
                                <form @submit.prevent="generateToken">
                                    <PrimaryButton
                                        :class="{ 'opacity-25': generateTokenForm.processing }"
                                        :disabled="generateTokenForm.processing"
                                    >
                                        Generate New Token
                                    </PrimaryButton>
                                </form>

                                <form v-if="tenant.api_token" @submit.prevent="revokeToken">
                                    <DangerButton
                                        :class="{ 'opacity-25': revokeTokenForm.processing }"
                                        :disabled="revokeTokenForm.processing"
                                    >
                                        Revoke Token
                                    </DangerButton>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feeds List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Feeds</h3>
                            <Link
                                :href="route('merchant.feeds.create', { tenant_id: tenant.id })"
                                class="text-sm text-blue-600 hover:text-blue-800"
                            >
                                Add New Feed
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table v-if="tenant.feeds && tenant.feeds.length" class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">URL</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Format</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Schedule</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Status</th>
                                        <th class="py-2 px-4 border-b border-gray-200 text-left font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="feed in tenant.feeds" :key="feed.id" class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <a :href="feed.url" target="_blank" class="text-blue-600 hover:text-blue-900 underline">
                                                {{ feed.url.length > 30 ? feed.url.substring(0, 30) + '...' : feed.url }}
                                            </a>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200 capitalize">
                                            {{ feed.format.replace('_', ' ') }}
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200 capitalize">
                                            {{ feed.sync_schedule }}
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <span :class="feed.active ? 'text-green-600' : 'text-red-600'">
                                                {{ feed.active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <Link :href="route('merchant.feeds.show', feed.id)" class="text-blue-600 hover:text-blue-900 mr-2">
                                                View
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div v-else class="text-gray-500 text-center py-4">
                                No feeds configured yet.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-red-50">
                        <h3 class="text-lg font-medium text-red-900 mb-4">Danger Zone</h3>
                        <p class="text-red-700 mb-4">
                            Deleting this tenant will remove all associated feeds, products, and data. This action cannot be undone.
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
                <h2 class="text-lg font-medium text-gray-900 mb-3">
                    Are you sure you want to delete this tenant?
                </h2>

                <p class="mb-6 text-red-600">
                    This will permanently delete the tenant, all associated feeds, and all associated data. This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="confirmingTenantDeletion = false">Cancel</SecondaryButton>
                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': deleteForm.processing }"
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
