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
                <td>{{ backup.date }}</td>
                <td>{{ backup.size }}</td>
                <td>
                    <button @click="downloadBackup(backup)" class="mr-3 btn btn-default btn-primary">
                        Download
                    </button>
                    <button @click="deleteBackup(backup)" class="mr-3 btn btn-default btn-primary">
                        Delete
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
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
                poller: null
            }
        },

        watch: {
            disks: function (newDisks) {
                console.log('disks changed!');

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
                if (! this.disks.length) {
                    return;
                }

                this.viewingDisk = this.disks[0];

                this.poller = window.setInterval(() => this.getBackups(), 1000);
            },

            async getBackups() {
                this.backups = await api.getBackups(this.viewingDisk);
            },

            downloadBackup(backup) {
                let downloadUrl = `/${Nova.config.base}/backup-tool/download-backup?disk=${this.viewingDisk}&path=${backup.path}`;

                window.location = downloadUrl;
            },

            async deleteBackup(backup) {
                await api.deleteBackup(this.viewingDisk, backup.path);

                this.$toasted.show('Backup deleted...', {type: 'success'})
            }
        },
    }
</script>