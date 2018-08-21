<template>
    <div v-if="loaded">
        <div class="flex mb-6 items-center justify-between">
            <heading>
                Backups
            </heading>
            <button @click="createBackup" class="btn btn-default btn-primary">
                Create Backup
            </button>
        </div>

        <card class="mb-6">
            <backup-statusses
                :backup-statusses="backupStatusses"
            />
        </card>

        <card>
            <backups
                v-if="activeDisk"
                :disks="disks"
                :backups="activeDiskBackups"
                :active-disk.sync="activeDisk"
                @delete="deleteBackup"
            />
        </card>
    </div>
</template>

<script>
    import api from '../api';
    import Backups from './Backups';
    import BackupStatusses from './BackupStatusses';

    export default {
        components: {
            Backups,
            BackupStatusses,
        },

        computed: {
            disks() {
                return this.backupStatusses.map(backupStatus => backupStatus.disk);
            },
        },

        data: () =>  ({
            loaded: false,
            activeDisk: null,
            activeDiskBackups: [],
            backupStatusses: [],
        }),

        async created() {
            await this.updateBackupStatusses();
            await this.updateActiveDiskBackups();

            this.loaded = true;

            this.startPolling();
        },

        methods: {
            updateBackupStatusses() {
                return api.getBackupStatusses().then(backupStatusses => {
                    this.backupStatusses = backupStatusses;

                    if (!this.activeDisk) {
                        this.activeDisk = backupStatusses[0].disk;
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
                this.$toasted.show('Creating backup...', { type: 'success' });

                return api.createBackup();
            },

            deleteBackup({ disk, path }) {
                return api.deleteBackup({ disk, path });
            },

            startPolling() {
                const poller = window.setInterval(() => {
                    this.updateBackupStatusses();
                    this.updateActiveDiskBackups();
                }, 1000);

                this.$once('hook:beforeDestroy', () => {
                    window.clearInterval(poller);
                });
            },
        },
    }
</script>
