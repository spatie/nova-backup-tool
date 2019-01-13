<template>
    <table cellpadding="0" cellspacing="0" class="table w-full">
        <thead>
            <tr>
                <th class="text-left rounded-tl">
                    {{ __('Disk') }}
                </th>
                <th class="text-left">
                    {{ __('Healthy') }}
                </th>
                <th class="text-left">
                    {{ __('Amount of backups') }}
                </th>
                <th class="text-left">
                    {{ __('Newest backup') }}
                </th>
                <th class="text-left rounded-tr">
                    {{ __('Used Storage') }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="backupStatus in backupStatuses" :key="backupStatus.disk">
                <td>{{ backupStatus.disk }}</td>
                <td>
                    <svg
                        :is="backupStatus.healthy ? 'icon-healthy' : 'icon-unhealthy'"
                        height="24px"
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
        backupStatuses: { required: true, type: Array },
    },

    components: {
        IconHealthy,
        IconUnhealthy,
    },
};
</script>
