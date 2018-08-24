export default {
    getBackupStatusses() {
        return window.axios
            .get('/nova-vendor/spatie/backup-tool/backup-statusses')
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

    deleteBackup({ disk, path }) {
        return window.axios.delete(`/nova-vendor/spatie/backup-tool/backups`, {
            params: { disk, path },
        });
    },
};
