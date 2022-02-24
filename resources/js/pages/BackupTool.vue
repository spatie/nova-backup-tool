<template>
    <LoadingView :loading="initialLoading">
        <Head :title="__('Backups')" />
        <div class="flex mb-6 items-center justify-between">
            <Heading>
                {{ __('Backups') }}
            </Heading>
            <DefaultButton @click="createBackup">
                {{ __('Create Backup') }}
            </DefaultButton>

            <Dropdown dusk="select-all-dropdown" ref="backupDropdownMenu">
                <DropdownTrigger
                    slot-scope="{ toggle }"
                    :show-arrow="false"
                    :handleClick="toggle"
                    class="mr-3"
                >
                    <button class="rounded active:outline-none active:ring focus:outline-none focus:ring">
                        <icon
                            type="menu"
                            view-box="0 0 24 24"
                            width="20"
                            height="20"
                        />
                    </button>
                </DropdownTrigger>

                <template #menu>
                    <DropdownMenu slot="menu" direction="rtl" width="250">
                        <ul class="list-reset">
                            <li>
                                <a
                                    class="block p-3 text-90 hover:bg-30 cursor-pointer"
                                    @click.prevent="createPartialBackup('only-db')"
                                >
                                    {{ __('Create database backup') }}
                                </a>
                            </li>
                            <li>
                                <a
                                    class="block p-3 text-90 hover:bg-30 cursor-pointer"
                                    @click.prevent="createPartialBackup('only-files')"
                                >
                                    {{ __('Create files backup') }}
                                </a>
                            </li>
                        </ul>
                    </DropdownMenu>
                </template>
            </Dropdown>
        </div>

        <LoadingCard :loading="loading" class="mb-6">
            <backup-statuses :backup-statuses="backupStatuses" />
        </LoadingCard>

        <LoadingCard :loading="loading">
            <backups
                v-if="activeDisk"
                :disks="disks"
                :backups="activeDiskBackups"
                :active-disk.sync="activeDisk"
                @delete="deleteBackup"
                @setModalVisibility="setModalVisibility"
            />
        </LoadingCard>
    </LoadingView>
</template>

<script>
import api from '../api';
import Backups from '../components/Backups';
import BackupStatuses from '../components/BackupStatuses';

export default {
    inheritAttrs: false,
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
        modalVisibility: false,
        loading: true,
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
            this.$toasted.show(
                this.__('Creating a new backup in the background...') + ' (' + option + ')',
                {
                    type: 'success',
                }
            );

            this.$refs.backupDropdownMenu.toggle();
            return api.createPartialBackup(option);
        },

        deleteBackup({ disk, path }) {
            return api.deleteBackup({ disk, path });
        },

        startPolling() {
            if (Nova.config('nova_backup_tool').polling) {
                const poller = window.setInterval(() => {
                    if (!this.modalVisibility) {
                        this.updateBackupStatuses();
                        this.updateActiveDiskBackups();
                    }
                }, Nova.config('nova_backup_tool').polling_interval * 1000);

                // this.$once('hook:beforeDestroy', () => {
                //     window.clearInterval(poller);
                // });
            }
        },

        setModalVisibility(state) {
            this.modalVisibility = state;
        },
    },
};
</script>
