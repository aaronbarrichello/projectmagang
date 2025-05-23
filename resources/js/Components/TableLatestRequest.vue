<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import TableCheckboxCell from "@/Components/TableCheckboxCell.vue";
import Badge from "@/Components/Badge.vue";
import { getPIC } from "@/utils";

defineProps({
    checkable: Boolean,
    columns: {
        type: Array,
        default: () => [],
    },
    datas: {
        type: Array,
        default: () => [],
    },
});

const checkedRows = ref([]);

const remove = (arr, cb) => {
    const newArr = [];

    arr.forEach((item) => {
        if (!cb(item)) {
            newArr.push(item);
        }
    });

    return newArr;
};

const checked = (isChecked, client) => {
    if (isChecked) {
        checkedRows.value.push(client);
    } else {
        checkedRows.value = remove(
            checkedRows.value,
            (row) => row.id === client.id
        );
    }
};

function formatDateTime(datetime) {
    const date = new Date(datetime + "Z");
    const options = { timeZone: "Asia/Jakarta" };

    const year = date.toLocaleString("id-ID", { ...options, year: "numeric" });
    const month = date.toLocaleString("id-ID", {
        ...options,
        month: "2-digit",
    });
    const day = date.toLocaleString("id-ID", { ...options, day: "2-digit" });
    const hour = date
        .toLocaleString("id-ID", { ...options, hour: "2-digit", hour12: false })
        .padStart(2, "0");
    const minute = date
        .toLocaleString("id-ID", { ...options, minute: "2-digit" })
        .padStart(2, "0");
    const second = date
        .toLocaleString("id-ID", { ...options, second: "2-digit" })
        .padStart(2, "0");

    return `${day}/${month}/${year} ${hour}:${minute}:${second}`;
}
</script>

<template>
    <div class="overflow-x-auto">
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th v-if="checkable" />
                    <th
                        v-for="(item, index) in columns"
                        :key="index"
                        class="whitespace-nowrap"
                    >
                        {{ item }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="data in datas" :key="data.id">
                    <TableCheckboxCell
                        v-if="checkable"
                        @checked="checked($event, client)"
                    />
                    <td class="whitespace-nowrap" data-label="Start Date">
                        {{ formatDateTime(data.start_date) }}
                    </td>
                    <td class="whitespace-nowrap" data-label="End Date">
                        {{ formatDateTime(data.end_date) }}
                    </td>

                    <td class="text-center" data-label="Visit Purpose">
                        {{ data.visit_purpose }}
                    </td>
                    <td class="whitespace-nowrap" data-label="Visitor PIC">
                        <Link
                            :href="route('request.show', data.id)"
                            class="no-underline hover:underline text-cyan-600 dark:text-cyan-400"
                        >
                            {{ getPIC(data.visitors) }}
                        </Link>
                    </td>
                    <td
                        class="whitespace-nowrap text-center"
                        data-label="Status"
                    >
                        <Badge :data="data.status" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
