export default {
    async getBackups(disk) {
        let response = await window.axios.get(`${Nova.config.base}/backups`, {
            disk
        });

        return response.data;
    }
}