<template>
    <div>
        <div v-if="disks.length > 1" class="py-3 flex items-center border-b border-50">
            <div>
                <div class="ml-4">
                    <select v-model="viewingDisk" class="form-control form-select mr-2">
                        <option v-for="disk in disks" :value="disk" selected>{{ disk}}</option>
                    </select>
                </div>
            </div>
        </div>

        <table cellpadding="0" cellspacing="0" data-testid="resource-table" class="table w-full">
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
                <td><span v-bind:class="spanClass(backup)">{{ backup.path }}</span></td>
                <td><span v-bind:class="spanClass(backup)">{{ backup.date }}</span></td>
                <td><span v-bind:class="spanClass(backup)">{{ backup.size }}</span></td>
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
                <td colspan="4">
                    No backups present.
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
                        <heading :level="2" class="mb-6">Delete backup</heading>
                        <p class="text-80 leading-normal">Are you sure you want to delete the backup created at {{
                            deletingBackup.date }}?</p>
                    </div>
                </delete-resource-modal>
            </transition>
        </portal>
    </div>
</template>

<script>
    import api from '../api';

    export default {
        props: ['disks'],

        data() {
            return {
                backups: [],
                viewingDisk: '',
                poller: null,
                deleteModalOpen: false,
                deletingBackup: null,
            }
        },

        watch: {
            disks: function () {
                if (this.poller !== null) {
                    return;
                }

                this.pollForBackups();
            },
        },

        beforeDestroy() {
            window.clearInterval(this.poller);
        },

        methods: {
            pollForBackups() {
                if (!this.disks.length) {
                    return;
                }

                this.viewingDisk = this.disks[0];

                this.getBackups();

                this.poller = window.setInterval(() => this.getBackups(), 1000);
            },

            async getBackups() {
                this.backups = await api.getBackups(this.viewingDisk);
            },

            downloadBackup(backup) {
                let downloadUrl = `/nova-vendor/spatie/backup-tool/download-backup?disk=${this.viewingDisk}&path=${backup.path}`;

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

            async confirmDelete() {
                this.deleteModalOpen = false;

                this.$toasted.show('Deleting backup...', {type: 'success'});

                await api.deleteBackup(this.viewingDisk, this.deletingBackup.path);
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
