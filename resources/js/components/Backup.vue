<template>
    <tr :class="{ 'is-deleting': deleting }">
        <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">{{ path }}</td>
        <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">{{ date }}</td>
        <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">{{ size }}</td>
        <td class="px-2 py-2 border-t border-gray-100 dark:border-gray-700 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900 text-right">
            <a
                :href="downloadUrl"
                target="_blank"
                rel="noopener nofollow"
                :title="__('Download')"
                class="appearance-none cursor-pointer text-70 hover:text-primary mr-3"
            >
                <icon type="download" view-box="0 0 24 24" width="20" height="20" />
            </a>
            <button
                :title="__('Delete')"
                class="appearance-none mr-3"
                :class="deletable ? 'text-70 hover:text-primary' : 'cursor-default text-40'"
                :disabled="!deletable"
                @click.prevent="$emit('delete')"
            >
                <icon type="delete" />
            </button>
        </td>
    </tr>
</template>

<script>
export default {
    props: {
        date: { required: true },
        path: { required: true },
        size: { required: true },
        disk: { required: true },
        deletable: { required: true },
        deleting: { required: true },
    },

    computed: {
        downloadUrl() {
            const endpoint = '/nova-vendor/spatie/backup-tool/download-backup';

            return `${endpoint}?disk=${this.disk}&path=${this.path}`;
        },
    },
};
</script>

<style scoped>
.is-deleting td {
    color: var(--60);
}
</style>
