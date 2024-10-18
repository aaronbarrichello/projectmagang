<script setup>
import { computed, ref } from "vue";
import { useMainStore } from "@/Stores/main";
import { useStyleStore } from "@/Stores/style";
import { mdiEye, mdiTrashCan } from "@mdi/js";
import CardBoxModal from "@/Components/CardBoxModal.vue";
import TableCheckboxCell from "@/Components/TableCheckboxCell.vue";
import BaseLevel from "@/Components/BaseLevel.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseButton from "@/Components/BaseButton.vue";
import UserAvatar from "@/Components/UserAvatar.vue";
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

const styleStore = useStyleStore();

const mainStore = useMainStore();

const dataClients = [
    {
        id: 1,
        name: "Adhimas",
        company: "PT ADHI",
        city: "Bandung",
        Progress: "50%",
        Created: new Date(),
    },
];

const items = computed(() => dataClients);

const isModalActive = ref(false);

const isModalDangerActive = ref(false);

const perPage = ref(5);

const currentPage = ref(0);

const checkedRows = ref([]);

const itemsPaginated = computed(() =>
    items.value.slice(
        perPage.value * currentPage.value,
        perPage.value * (currentPage.value + 1)
    )
);

const numPages = computed(() => Math.ceil(items.value.length / perPage.value));

const currentPageHuman = computed(() => currentPage.value + 1);

const pagesList = computed(() => {
    const pagesList = [];

    for (let i = 0; i < numPages.value; i++) {
        pagesList.push(i);
    }

    return pagesList;
});

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
</script>

<template>
    <CardBoxModal v-model="isModalActive" title="Sample modal">
        <p>Lorem ipsum dolor sit amet <b>adipiscing elit</b></p>
        <p>This is sample modal</p>
    </CardBoxModal>

    <CardBoxModal
        v-model="isModalDangerActive"
        large-title="Please confirm"
        button="danger"
        has-cancel
    >
        <p>Lorem ipsum dolor sit amet <b>adipiscing elit</b></p>
        <p>This is sample modal</p>
    </CardBoxModal>

    <div v-if="checkedRows.length" class="p-3 bg-gray-100/50 dark:bg-slate-800">
        <span
            v-for="checkedRow in checkedRows"
            :key="checkedRow.id"
            class="inline-block px-2 py-1 rounded-sm mr-2 text-sm bg-gray-100 dark:bg-slate-700"
        >
            {{ checkedRow.name }}
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th v-if="checkable" />
                    <th v-for="(item, index) in columns" :key="index" class="whitespace-nowrap">{{ item }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="data in datas" :key="data.id">
                    <TableCheckboxCell
                        v-if="checkable"
                        @checked="checked($event, client)"
                    />
                    <td class="whitespace-nowrap" data-label="Start Date">
                        {{ data.start_date }}
                    </td>
                    <td class="whitespace-nowrap" data-label="End Date">
                        {{ data.end_date }}
                    </td>
                    <td class="text-center" data-label="Visit Purpose">
                        {{ data.visit_purpose }}
                    </td>
                    <td class="whitespace-nowrap" data-label="Visitor (PIC)">
                        {{ getPIC(data.visitors) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
