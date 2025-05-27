<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';

const props = defineProps({
    feed: Object,
});

// For the delete feed confirmation
const confirmingFeedDeletion = ref(false);
const deleteForm = useForm({});

const confirmFeedDeletion = () => {
    confirmingFeedDeletion.value = true;
};

const deleteFeed = () => {
    deleteForm.delete(route('merchant.feeds.destroy', props.feed.id), {
        onSuccess: () => {
            confirmingFeedDeletion.value = false;
        }
    });
};

// For manual sync
const syncForm = useForm({});

const syncFeed = () => {
    syncForm.post(route('merchant.feeds.sync', props.feed.id));
};
</script>

<template>
    <Head :title="`Feed: ${feed.url}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Feed Details</h2>
                <div class="flex space-x-2">
                    <Link
                        :href="route('merchant.feeds.edit', feed.id)"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md"
                    >
                        Edit Feed
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Feed Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Feed Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-600">Tenant</p>
                                <p class="font-semibold">
                                    <Link :href="route('merchant.tenants.show', feed.tenant.id)" class="text-blue-600 hover:text-blue-900">
                                        {{ feed.tenant.name }}
                                    </Link>
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">URL</p>
                                <p class="font-semibold">
                                    <a :href="feed.url" target="_blank" class="text-blue-600 hover:text-blue-900 underline">
                                        {{ feed.url }}
                                    </a>
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">Format</p>
                                <p class="font-semibold capitalize">{{ feed.format.replace('_', ' ') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Sync Schedule</p>
                                <p class="font-semibold capitalize">{{ feed.sync_schedule }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Last Sync</p>
                                <p class="font-semibold">
                                    {{ feed.last_sync_at ? new Date(feed.last_sync_at).toLocaleString() : 'Never' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">Next Scheduled Sync</p>
                                <p class="font-semibold">
                                    {{ feed.next_sync_at ? new Date(feed.next_sync_at).toLocaleString() : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">Status</p>
                                <p class="font-semibold" :class="feed.active ? 'text-green-600' : 'text-red-600'">
                                    {{ feed.active ? 'Active' : 'Inactive' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <form @submit.prevent="syncFeed">
                                <PrimaryButton
                                    :class="{ 'opacity-25': syncForm.processing }"
                                    :disabled="syncForm.processing"
                                >
                                    Sync Now
                                </PrimaryButton>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sync Status and Logs -->
                <div v-if="feed.sync_status || feed.error_log" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Sync Status</h3>

                        <div v-if="feed.sync_status" class="mb-4">
                            <p class="text-gray-600">Status</p>
                            <div class="p-4 rounded-md" :class="{
                                'bg-green-100': feed.sync_status.status === 'completed',
                                'bg-yellow-100': ['queued', 'in_progress'].includes(feed.sync_status.status),
                                'bg-red-100': ['failed', 'error'].includes(feed.sync_status.status),
                            }">
                                <p class="capitalize font-semibold">{{ feed.sync_status.status }}</p>
                                <p v-if="feed.sync_status.message">{{ feed.sync_status.message }}</p>
                            </div>
                        </div>

                        <div v-if="feed.error_log">
                            <p class="text-gray-600">Error Log</p>
                            <div class="p-4 rounded-md bg-red-50 text-red-700 whitespace-pre-wrap font-mono text-sm">
                                {{ feed.error_log }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-red-50">
                        <h3 class="text-lg font-medium text-red-900 mb-4">Danger Zone</h3>
                        <p class="text-red-700 mb-4">
                            Deleting this feed will remove all associated data. This action cannot be undone.
                        </p>
                        <DangerButton @click="confirmFeedDeletion">
                            Delete Feed
                        </DangerButton>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Feed Confirmation Modal -->
        <Modal :show="confirmingFeedDeletion" @close="confirmingFeedDeletion = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-3">
                    Are you sure you want to delete this feed?
                </h2>

                <p class="mb-6 text-red-600">
                    This will permanently delete the feed and all associated data. This action cannot be undone.
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="confirmingFeedDeletion = false">Cancel</SecondaryButton>
                    <DangerButton
                        class="ml-3"
                        :class="{ 'opacity-25': deleteForm.processing }"
                        :disabled="deleteForm.processing"
                        @click="deleteFeed"
                    >
                        Delete Feed
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
