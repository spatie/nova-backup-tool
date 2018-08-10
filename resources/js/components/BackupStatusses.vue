<template>
    <table cellpadding="0" cellspacing="0" data-testid="resource-table" class="table w-full">
        <thead>
        <tr>
            <th class="text-left">
                Disk
            </th>
            <th class="text-left">
                Reachable
            </th>
            <th class="text-left">
                Healthy
            </th>
            <th class="text-left">
                Amount
            </th>
            <th class="text-left">
                Newest
            </th>
            <th class="text-left">
                Used Storage
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="backupStatus in backupStatusses">
            <td>{{ backupStatus.disk }}</td>
            <td>{{ backupStatus.reachable }}</td>
            <td>{{ backupStatus.healthy }}</td>
            <td>{{ backupStatus.amount }}</td>
            <td>{{ backupStatus.newest }}</td>
            <td>{{ backupStatus.usedStorage }}</td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    export default {
        props: ['backupStatusses'],
    }
</script>