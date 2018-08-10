<template>
    <div>
        <div class="py-3 flex items-center border-b border-50">
            <div>
                <div class="ml-4">
                    <select class="form-control form-select mr-2">
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
            <tr v-for="backup in backups">
                <td>{{ backup.path }}</td>
                <td>{{ backup.date }}</td>
                <td>{{ backup.size }}</td>
                <td class="text-right">
                    <button @click="downloadBackup(backup)" class="mr-3 btn btn-default btn-primary">
                        Download
                    </button>
                    <button @click="openDeleteModal(backup)" class="mr-3 btn btn-default btn-danger">
                        Delete
                    </button>
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
                        <p class="text-80 leading-normal">Are you sure you want to delete the backup created at {{ deletingBackup.date }}?</p>
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
            disks: function (newDisks) {
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

                this.poller = window.setInterval(() => this.getBackups(), 1000);
            },

            async getBackups() {
                this.backups = await api.getBackups(this.viewingDisk);
            },

            downloadBackup(backup) {
                let downloadUrl = `${Nova.config.base}/backup-tool/download-backup?disk=${this.viewingDisk}&path=${backup.path}`;

                window.location = downloadUrl;
            },

            openDeleteModal(backup) {
                this.deleteModalOpen = true;
                this.deletingBackup = backup;
            },

            closeDeleteModal() {
                this.deleteModalOpen = false;
                this.deletingBackup = null;
            },

            confirmDelete() {
                this.deleteModalOpen = false;

                api.deleteBackup(this.viewingDisk, this.deletingBackup.path);

                this.deletingBackup = null;

                this.$toasted.show('Deleting backup...', {type: 'success'})
            },
        },
    }
</script>