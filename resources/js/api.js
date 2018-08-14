export default {
    async getBackupStatusses() {
        let response = await window.axios.get('/nova-vendor/spatie/backup-tool/backup-statusses');

        return response.data;
    },

    async getBackups(disk) {
        let response = await window.axios.get(`/nova-vendor/spatie/backup-tool/backups?disk=${disk}`);

        return response.data;
    },

    createBackup() {
        window.axios.post(`/nova-vendor/spatie/backup-tool/backups`);
    },

    deleteBackup(disk, path) {
        window.axios.delete(`/nova-vendor/spatie/backup-tool/backups`, { params: {disk, path}});
    }
}