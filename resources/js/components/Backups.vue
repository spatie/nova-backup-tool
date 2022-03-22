<template>
    <div>
        <div v-if="disks.length > 1" class="p-3 flex items-center border-b border-50">
            <select
                class="form-control form-select"
                :value="activeDisk"
                @input="$emit('update:activeDisk', $event.target.value)"
            >
                <option v-for="disk in disks" :key="disk" :value="disk">
                    {{ disk }}
                </option>
            </select>
        </div>

        <div class="overflow-hidden overflow-x-auto relative rounded-lg">
            <table cellpadding="0" cellspacing="0" class="table-default w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide px-2 py-2">
                            {{ __('Path') }}
                        </th>
                        <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide px-2 py-2">
                            {{ __('Created at') }}
                        </th>
                        <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide px-2 py-2">
                            {{ __('Size') }}
                        </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <backup
                        v-for="backup in backups"
                        v-bind="backup"
                        :disk="activeDisk"
                        :deletable="backups.length > 1"
                        :deleting="
                            !deleteModalOpen && deletingBackup && backup.path === deletingBackup.path
                        "
                        :key="backup.id"
                        @delete="openDeleteModal(backup)"
                    />
                    <tr v-if="backups.length === 0">
                        <td class="text-center px-2 py-2" colspan="4">
                            {{ __('No backups present') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <portal to="modals">
            <transition name="fade">
                <DeleteResourceModal
                    v-if="deleteModalOpen"
                    @confirm="confirmDelete"
                    @close="closeDeleteModal"
                    mode="delete"
                >
                    <div class="p-8">
                        <heading :level="2" class="mb-6">
                            {{ __('Delete backup') }}
                        </heading>
                        <p class="text-80 leading-normal">
                            {{
                                __(
                                    'Are you sure you want to delete the backup created at :date ?',
                                    { date: deletingBackup.date }
                                )
                            }}
                        </p>
                    </div>
                </delete-resource-modal>
            </transition>
        </portal>
    </div>
</template>

<script>
import Backup from './Backup';

export default {
    emits: ['setModalVisibility', 'delete'],

    props: {
        disks: { required: true, type: Array },
        activeDisk: { required: true, type: String },
        backups: { required: true, type: Array },
    },

    data() {
        return {
            deletingBackup: null,
            deleteModalOpen: false,
        };
    },

    components: {
        Backup,
    },

    methods: {
        openDeleteModal(backup) {
            this.$emit('setModalVisibility', true);
            this.deleteModalOpen = true;
            this.deletingBackup = backup;
        },

        closeDeleteModal() {
            this.$emit('setModalVisibility', false);
            this.deleteModalOpen = false;
            this.deletingBackup = null;
        },

        confirmDelete() {
            this.$emit('setModalVisibility', false);
            this.deleteModalOpen = false;

            this.$emit('delete', {
                disk: this.activeDisk,
                path: this.deletingBackup.path,
            });
        },
    },
};
</script>
