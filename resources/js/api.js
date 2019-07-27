export default {
    getBackupStatuses() {
        return window.axios
            .get('/nova-vendor/spatie/backup-tool/backup-statuses')
            .then(response => response.data);
    },

    getBackups(disk) {
        return window.axios
            .get(`/nova-vendor/spatie/backup-tool/backups?disk=${disk}`)
            .then(response => response.data);
    },

    createBackup() {
        return window.axios.post(`/nova-vendor/spatie/backup-tool/backups`);
    },

    createPartialBackup(option) {
        return window.axios.post(`/nova-vendor/spatie/backup-tool/backups`, { option });
    },

    deleteBackup({ disk, path }) {
        return window.axios.delete(`/nova-vendor/spatie/backup-tool/backups`, {
            params: { disk, path },
        });
    },
};
