<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    tenant: Object,
    tenants: Array,
});

const form = useForm({
    tenant_id: props.tenant ? props.tenant.id : '',
    url: '',
    format: 'google_merchant',
    sync_schedule: 'daily',
});

const submit = () => {
    form.post(route('admin.feeds.store'));
};
</script>

<template>
    <Head title="Create Feed" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ tenant ? `Create Feed for Tenant: ${tenant.name}` : 'Create New Feed' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <!-- Tenant Selection (only if tenant not preselected) -->
                            <div class="mb-4" v-if="!tenant && tenants">
                                <InputLabel for="tenant_id" value="Tenant" />
                                <select
                                    id="tenant_id"
                                    v-model="form.tenant_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="" disabled>Select a tenant</option>
                                    <option v-for="t in tenants" :key="t.id" :value="t.id">{{ t.name }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.tenant_id" />
                            </div>

                            <!-- Feed URL -->
                            <div class="mb-4">
                                <InputLabel for="url" value="Feed URL" />
                                <TextInput
                                    id="url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    v-model="form.url"
                                    required
                                    autofocus
                                    placeholder="https://example.com/products.xml"
                                />
                                <InputError class="mt-2" :message="form.errors.url" />
                                <p class="mt-1 text-sm text-gray-500">
                                    The URL to the product feed that will be synced.
                                </p>
                            </div>

                            <!-- Feed Format -->
                            <div class="mb-4">
                                <InputLabel for="format" value="Feed Format" />
                                <select
                                    id="format"
                                    v-model="form.format"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="google_merchant">Google Merchant XML</option>
                                    <option value="skroutz_xml">Skroutz XML</option>
                                    <option value="json">JSON</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.format" />
                                <p class="mt-1 text-sm text-gray-500">
                                    The format of the product feed.
                                </p>
                            </div>

                            <!-- Sync Schedule -->
                            <div class="mb-4">
                                <InputLabel for="sync_schedule" value="Sync Schedule" />
                                <select
                                    id="sync_schedule"
                                    v-model="form.sync_schedule"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="hourly">Hourly</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.sync_schedule" />
                                <p class="mt-1 text-sm text-gray-500">
                                    How often the feed should be synchronized.
                                </p>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton
                                    class="ml-4"
                                    :disabled="form.processing"
                                >
                                    Create Feed
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
