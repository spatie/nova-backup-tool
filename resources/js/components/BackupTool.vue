<template>
    <loading-view :loading="initialLoading">
        <div class="flex mb-6 items-center justify-between">
            <heading>
                {{ __('Backups') }}
            </heading>
            <button @click="createBackup" class="btn btn-default btn-primary">
                {{ __('Create Backup') }}
            </button>
        </div>

        <loading-card :loading="loading" class="mb-6">
            <backup-statuses
                :backup-statuses="backupStatuses"
            />
        </loading-card>

        <loading-card :loading="loading">
            <backups
                v-if="activeDisk"
                :disks="disks"
                :backups="activeDiskBackups"
                :active-disk.sync="activeDisk"
                @delete="deleteBackup"
            />
        </loading-card>
    </loading-view>
</template>

<script>
import api from '../api';
import Backups from './Backups';
import BackupStatuses from './BackupStatuses';

export default {
    components: {
        Backups,
        BackupStatuses,
    },

    computed: {
        disks() {
            return this.backupStatuses.map(backupStatus => backupStatus.disk);
        },
    },

    data: () => ({
        activeDisk: null,
        activeDiskBackups: [],
        backupStatuses: [],
        initialLoading: true,
        loading: true
    }),

    async created() {
        this.initialLoading = false;

        await this.updateBackupStatuses();
        await this.updateActiveDiskBackups();

        this.loading = false;

        this.startPolling();
    },

    methods: {
        updateBackupStatuses() {
            return api.getBackupStatuses().then(backupStatuses => {
                this.backupStatuses = backupStatuses;

                if (!this.activeDisk) {
                    this.activeDisk = backupStatuses[0].disk;
                }
            });
        },

        updateActiveDiskBackups() {
            if (!this.activeDisk) {
                return;
            }

            return api.getBackups(this.activeDisk).then(backups => {
                this.activeDiskBackups = backups;
            });
        },

        createBackup() {
            this.$toasted.show(this.__('Creating a new backup in the background...'), {
                type: 'success',
            });

            return api.createBackup();
        },

        deleteBackup({ disk, path }) {
            return api.deleteBackup({ disk, path });
        },

        startPolling() {
            const poller = window.setInterval(() => {
                this.updateBackupStatuses();
                this.updateActiveDiskBackups();
            }, 1000);

            this.$once('hook:beforeDestroy', () => {
                window.clearInterval(poller);
            });
        },
    },
};
</script>
