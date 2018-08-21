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

        <table cellpadding="0" cellspacing="0" class="table w-full">
            <thead>
            <tr>
                <th class="text-left">
                    Path
                </th>
                <th class="text-left">
                    Created at
                </th>
                <th class="text-left">
                    Size
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="backup in backups" :key="backup.id">
                <td><span :class="spanClass(backup)">{{ backup.path }}</span></td>
                <td><span :class="spanClass(backup)">{{ backup.date }}</span></td>
                <td><span :class="spanClass(backup)">{{ backup.size }}</span></td>
                <td class="text-right">
                    <button
                        title="Download"
                        class="appearance-none cursor-pointer text-70 hover:text-primary mr-3"
                        @click.prevent="downloadBackup(backup)"
                    >
                        <icon type="download" />
                    </button>

                    <button
                        title="Delete"
                        class="appearance-none cursor-pointer text-70 hover:text-primary mr-3"
                        @click.prevent="openDeleteModal(backup)"
                    >
                        <icon type="delete" />
                    </button>
                </td>
            </tr>
            <tr v-if="backups.length === 0">
                <td class="text-center" colspan="4">
                    No backups present
                </td>
            </tr>
            </tbody>
        </table>

        <portal to="modals">
            <transition name="fade">
                <delete-resource-modal
                    v-if="deleteModalOpen"
                    @confirm="confirmDelete"
                    @close="closeDeleteModal"
                    mode="delete"
                >
                    <div class="p-8">
                        <heading :level="2" class="mb-6">
                            Delete backup
                        </heading>
                        <p class="text-80 leading-normal">
                            Are you sure you want to delete the backup created at {{ deletingBackup.date }}?
                        </p>
                    </div>
                </delete-resource-modal>
            </transition>
        </portal>
    </div>
</template>

<script>
    import api from '../api';

    export default {
        props: {
            disks: { required: true, type: Array },
            activeDisk: { required: true, type: String },
            backups: { required: true, type: Array },
        },

        data() {
            return {
                deletingBackup: null,
                deleteModalOpen: false,
            }
        },

        methods: {
            downloadBackup(backup) {
                let downloadUrl = `/nova-vendor/spatie/backup-tool/download-backup?disk=${this.activeDisk}&path=${backup.path}`;

                window.location = downloadUrl;
            },

            openDeleteModal(backup) {
                if (this.backups.length === 1) {
                    this.$toasted.show('Cannot delete the last backup!', {type: 'error'})

                    return;
                }

                this.deleteModalOpen = true;
                this.deletingBackup = backup;
            },

            closeDeleteModal() {
                this.deleteModalOpen = false;
                this.deletingBackup = null;
            },

            confirmDelete() {
                this.deleteModalOpen = false;

                this.$toasted.show('Deleting backup...', {type: 'success'});

                this.$emit('delete', {
                    disk: this.activeDisk,
                    path: this.deletingBackup.path,
                });
            },

            isDeletingBackup(backup) {
                if (this.deletingBackup === null) {
                    return;
                }

                return backup.path === this.deletingBackup.path;
            },

            spanClass(backup) {
                return {
                    'text-60': this.isDeletingBackup(backup),
                };
            },
        },
    }
</script>
