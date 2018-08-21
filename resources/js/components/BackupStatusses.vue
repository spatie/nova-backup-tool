<template>
    <table cellpadding="0" cellspacing="0" class="backups-table table w-full">
        <thead>
            <tr>
                <th class="text-left rounded-tl">
                    Disk
                </th>
                <th class="text-left">
                    Healthy
                </th>
                <th class="text-left">
                    Amount of backups
                </th>
                <th class="text-left">
                    Newest backup
                </th>
                <th class="text-left rounded-tr">
                    Used Storage
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="backupStatus in backupStatusses" :key="backupStatus.disk">
                <td>{{ backupStatus.disk }}</td>
                <td>
                    <svg
                        :is="backupStatus.healthy ? 'icon-healthy' : 'icon-unhealthy'"
                        height="1.5rem"
                    />
                </td>
                <td>{{ backupStatus.amount }}</td>
                <td>{{ backupStatus.newest }}</td>
                <td>{{ backupStatus.usedStorage }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    import IconHealthy from './icons/IconHealthy';
    import IconUnhealthy from './icons/IconUnhealthy';

    export default {
        props: {
            backupStatusses: { required: true, type: Array },
        },

        components: {
            IconHealthy,
            IconUnhealthy,
        },
    }
</script>
