<template>
    <table cellpadding="0" cellspacing="0" class="table-default w-full">
        <thead class="bg-gray-50 dark:bg-gray-800 rounded-t-lg">
            <tr>
                <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide px-2 py-2">
                    {{ __('Name') }}
                </th>
                <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide px-2 py-2">
                    {{ __('Disk') }}
                </th>
                <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide px-2 py-2">
                    {{ __('Healthy') }}
                </th>
                <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide px-2 py-2">
                    {{ __('Amount of backups') }}
                </th>
                <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide px-2 py-2">
                    {{ __('Newest backup') }}
                </th>
                <th class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide px-2 py-2">
                    {{ __('Used Storage') }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="backupStatus in backupStatuses" :key="backupStatus.disk">
                <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">{{ __(backupStatus.name) }}</td>
                <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">{{ __(backupStatus.disk) }}</td>
                <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                    <svg
                        :is="backupStatus.healthy ? 'icon-healthy' : 'icon-unhealthy'"
                        height="24px"
                    />
                </td>
                <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">{{ backupStatus.amount }}</td>
                <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">{{ backupStatus.newest }}</td>
                <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">{{ backupStatus.usedStorage }}</td>
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
