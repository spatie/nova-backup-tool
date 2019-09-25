<template>
    <loading-view :loading="initialLoading">
        <div class="flex mb-6 items-center justify-between">
            <heading>
                {{ __('Backups') }}
            </heading>
            <button @click="createBackup" class="btn btn-default btn-primary ml-auto mr-3">
                {{ __('Create Backup') }}
            </button>

            <dropdown dusk="select-all-dropdown" ref="backupDropdownMenu">
                <dropdown-trigger slot-scope="{ toggle }" :show-arrow="false" :handleClick="toggle" class="mr-3">
                    <button class="btn btn-default btn-icon btn-primary font-normal no-text-shadow">
                        <icon type="menu" view-box="0 0 24 24" width="20" height="20" class="text-white" />
                    </button>
                </dropdown-trigger>

                <dropdown-menu slot="menu" direction="rtl" width="250">
                    <ul class="list-reset">
                        <li>
                            <a class="block p-3 text-90 hover:bg-30 cursor-pointer" @click.prevent="createPartialBackup('only-db')">
                                {{ __('Create database backup') }}
                            </a>
                        </li>
                        <li>
                            <a class="block p-3 text-90 hover:bg-30 cursor-pointer" @click.prevent="createPartialBackup('only-files')">
                                {{ __('Create files backup') }}
                            </a>
                        </li>
                    </ul>
                </dropdown-menu>
            </dropdown>
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
        BackupStatuses
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

        createPartialBackup(option) {
            this.$toasted.show(this.__('Creating a new backup in the background...') + ' (' + option + ')', {
                type: 'success',
            });

            this.$refs.backupDropdownMenu.toggle();
            return api.createPartialBackup(option);
        },

        deleteBackup({ disk, path }) {
            return api.deleteBackup({ disk, path });
        },

        startPolling() {
            if(Nova.config.nova_backup_tool.polling){
                const poller = window.setInterval(() => {
                    this.updateBackupStatuses();
                    this.updateActiveDiskBackups();
                }, Nova.config.nova_backup_tool.polling_interval * 1000);

                this.$once('hook:beforeDestroy', () => {
                    window.clearInterval(poller);
                });
            }
        },
    },
};
</script>
