<template>
    <div>
        <heading>Backups</heading>

        <div class="flex mb-6 justify-end">
            <button @click="createBackup" class="btn btn-default btn-primary">
                Create Backup
            </button>
        </div>

        <card class="relative bg-black mb-6">
            <a class="absolute pin-t pin-r p-2 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path
                      d="M16.24 14.83a1 1 0 0 1-1.41 1.41L12 13.41l-2.83 2.83a1 1 0 0 1-1.41-1.41L10.59 12 7.76 9.17a1 1 0 0 1 1.41-1.41L12 10.59l2.83-2.83a1 1 0 0 1 1.41 1.41L13.41 12l2.83 2.83z"
                      class="fill-white opacity-50"/>
                </svg>
            </a>
        </card>

        <card class="mb-8">
            <backup-statusses
              :backup-statusses="backupStatusses"
            ></backup-statusses>
        </card>

        <card>
            <backups
                :disks="disks"
            ></backups>
        </card>
    </div>
</template>

<script>
    import api from '../api'
    import BackupStatusses from "./BackupStatusses";
    import Backups from './Backups'

    export default {
        components: {
            BackupStatusses,
            Backups,
        },

        data() {
            return {
                poller: null,
            }
        },

        computed: {
            disks() {
                return window._.map(this.backupStatusses, 'disk');
            },
        },

        data() {
            return {
                backupStatusses: [],
            }
        },

        created() {
            this.getBackupStatusses();

            window.setInterval(this.getBackupStatusses, 1000);
        },

        beforeDestroy() {
            window.clearInterval(this.poller);
        },

        methods: {
            createBackup() {
                api.createBackup();

                this.$toasted.show('Creating backup...', {type: 'success'});
            },

            async getBackupStatusses() {
                this.backupStatusses = await api.getBackupStatusses();
            }
        }
    }
</script>