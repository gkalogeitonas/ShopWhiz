<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, computed } from 'vue';

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
    form.post(route('merchant.feeds.store'));
};

const selectedTenantName = computed(() => {
    if (!props.tenants) return '';
    const selected = props.tenants.find(t => t.id === parseInt(form.tenant_id));
    return selected ? selected.name : '';
});
</script>

<template>
    <Head title="Create Feed" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create New Feed</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div v-if="!tenant && tenants" class="mb-4">
                                <InputLabel for="tenant_id" value="Select Tenant" />
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

                            <div v-else class="mb-4">
                                <InputLabel value="Tenant" />
                                <div class="mt-1 p-2 bg-gray-100 rounded-md border border-gray-200">
                                    {{ tenant ? tenant.name : selectedTenantName }}
                                </div>
                                <input type="hidden" v-model="form.tenant_id" />
                            </div>

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
                            </div>

                            <div class="mb-4">
                                <InputLabel for="format" value="Feed Format" />
                                <select
                                    id="format"
                                    v-model="form.format"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                >
                                    <option value="google_merchant">Google Merchant</option>
                                    <option value="skroutz_xml">Skroutz XML</option>
                                    <option value="json">JSON</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.format" />
                            </div>

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
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton
                                    class="ml-4"
                                    :class="{ 'opacity-25': form.processing }"
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
