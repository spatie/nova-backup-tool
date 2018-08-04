<template>
    <div>
        <heading>Backups</heading>

        <div class="flex mb-6">
            <button class="ml-auto mr-3 btn btn-default btn-primary">
                Healthcheck
            </button>
            <button class="mr-3 btn btn-default btn-primary">
                Clean Backups
            </button>
            <button class="btn btn-default btn-primary">
                Create Backup
            </button>
        </div>

        <card class="relative bg-black mb-6">
            <a class="absolute pin-t pin-r p-2 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M16.24 14.83a1 1 0 0 1-1.41 1.41L12 13.41l-2.83 2.83a1 1 0 0 1-1.41-1.41L10.59 12 7.76 9.17a1 1 0 0 1 1.41-1.41L12 10.59l2.83-2.83a1 1 0 0 1 1.41 1.41L13.41 12l2.83 2.83z" class="fill-white opacity-50"/></svg>
            </a>

            <div class="p-6">
<pre class="font-mono text-sm text-white">+---------+-------+-----------+---------+--------------+---------------------+--------------+
| Name    | Disk  | Reachable | Healthy | # of backups | Newest backup       | Used storage |
+---------+-------+-----------+---------+--------------+---------------------+--------------+
| Laravel | local | ✔         | ✔       | 8            | 50.34 (1 month ago) | 1.29 KB      |
+---------+-------+-----------+---------+--------------+---------------------+--------------+</pre>
            </div>
        </card>

        <card>
            <div class="py-3 flex items-center border-b border-50">
                <div>
                    <div class="ml-4">
                        <select class="form-control form-select mr-2">
                            <option value="local" selected>Local</option>
                            <option value="s3">S3 Backups</option>
                        </select>
                        <button class="btn btn-default btn-primary">
                            Load Backups
                        </button>
                    </div>
                </div>
            </div>

            <table cellpadding="0" cellspacing="0" data-testid="resource-table" class="table w-full">
                <thead>
                <tr>
                    <th class="w-16">&nbsp;</th>
                    <th class="text-left">
                        File Name
                    </th>
                    <th class="text-left">
                        Name
                    </th>
                    <th class="text-left">
                        Created At
                    </th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td></td>
                    <td>2018-04-20 21-27-05.zip</td>
                    <td>1.29KB</td>
                    <td>2018-04-20</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>2018-04-20 21-27-05.zip</td>
                    <td>1.29KB</td>
                    <td>2018-04-20</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>2018-04-20 21-27-05.zip</td>
                    <td>1.29KB</td>
                    <td>2018-04-20</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>2018-04-20 21-27-05.zip</td>
                    <td>1.29KB</td>
                    <td>2018-04-20</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>2018-04-20 21-27-05.zip</td>
                    <td>1.29KB</td>
                    <td>2018-04-20</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>2018-04-20 21-27-05.zip</td>
                    <td>1.29KB</td>
                    <td>2018-04-20</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>2018-04-20 21-27-05.zip</td>
                    <td>1.29KB</td>
                    <td>2018-04-20</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>2018-04-20 21-27-05.zip</td>
                    <td>1.29KB</td>
                    <td>2018-04-20</td>
                    <td></td>
                </tr>
                </tbody>
            </table>

            <div class="bg-20 rounded-b">
                <nav class="flex">
                    <button disabled="disabled" rel="prev" class="btn btn-link py-3 px-4 text-80 opacity-50">
                        Previous
                    </button>
                    <button rel="next" class="ml-auto btn btn-link py-3 px-4 text-primary dim">
                        Next
                    </button>
                </nav>
            </div>
        </card>
    </div>
</template>

<script>
    import api from '../api'

    export default {
    }
</script>