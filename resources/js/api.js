export default {
    async getBackupStatusses() {
        let response = await window.axios.get(`${Nova.config.base}/backup-tool/backup-statusses`);

        return response.data;
    },

    async getBackups(disk) {
        let response = await window.axios.get(`${Nova.config.base}/backup-tool/backups`, {
            disk
        });

        return response.data;
    },

    createBackup(disk) {
        window.axios.post(`${Nova.config.base}/backup-tool/backups`);
    },

    deleteBackup(disk, path) {
        window.axios.delete(`${Nova.config.base}/backup-tool/backups`, {disk, path});
    }
}