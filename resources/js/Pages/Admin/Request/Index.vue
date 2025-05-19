<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import {
    mdiOfficeBuildingMarker,
    mdiPlus,
    mdiCheck,
    mdiClose,
    mdiAlertBoxOutline,
} from "@mdi/js";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import BaseButton from "@/Components/BaseButton.vue";
import CardBox from "@/Components/CardBox.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import Pagination from "@/Components/Admin/Pagination.vue";
import { getPIC } from "@/utils.js";
import Badge from "../../../Components/Badge.vue";
import Sort from "@/Components/Admin/Sort.vue";
import FormControl from "@/Components/FormControl.vue";
import FormField from "@/Components/FormField.vue";

const props = defineProps({
    requests: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    can: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    search: props.filters.search,
});

const formFilter = useForm({
    status: props.filters.status,
});

const formAccaptance = useForm({});

function acceptance(id, action) {
    if (confirm(`Are you sure you want to ${action} this request?`)) {
        formAccaptance.post(route(`request.acceptance`, { id, action }));
    }
}

const clearFilter = () => {
    formFilter.status = null;
    formFilter.get(route("request.index"));
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
    <LayoutAuthenticated>
        <Head title="Visit Request" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiOfficeBuildingMarker"
                title="Visit Request"
                main
            />
            <NotificationBar
                :key="Date.now()"
                v-if="$page.props.flash.message"
                :color="
                    $page.props.flash.color == null
                        ? 'success'
                        : $page.props.flash.color
                "
                :icon="mdiAlertBoxOutline"
            >
                {{ $page.props.flash.message }}
            </NotificationBar>

            <CardBox class="mb-6" has-table>
                <div class="py-2 flex flex-col lg:flex-row lg:justify-between">
                    <div class="flex items-end pl-4">
                        <form
                            @submit.prevent="form.get(route('request.index'))"
                        >
                            <input
                                type="search"
                                v-model="form.search"
                                class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-transparent"
                                placeholder="Search"
                            />
                            <BaseButton
                                label="Search"
                                type="submit"
                                color="info"
                                class="ml-4 inline-flex items-center px-4 py-2"
                            />
                        </form>
                    </div>

                    <div class="flex px-4">
                        <form
                            @submit.prevent="
                                formFilter.get(route('request.index'))
                            "
                        >
                            <FormField label="Filter by Status">
                                <FormControl
                                    class="min-w-[200px]"
                                    v-model="formFilter.status"
                                    type="select"
                                    :options="[
                                        'requested',
                                        'accepted',
                                        'rejected',
                                        'finished',
                                        'missed',
                                    ]"
                                    options-is-value
                                    change-is-submit
                                    :show-clear-button="
                                        formFilter.status !== null
                                    "
                                    @clear="clearFilter"
                                    @submitForm="
                                        formFilter.get(route('request.index'))
                                    "
                                />
                            </FormField>
                        </form>
                    </div>
                </div>
            </CardBox>

            <CardBox class="mb-6" has-table>
                <table>
                    <thead>
                        <tr>
                            <th>
                                <Sort
                                    label="Start Date"
                                    attribute="start_date"
                                />
                            </th>
                            <th>
                                <Sort label="End Date" attribute="end_date" />
                            </th>
                            <th>
                                <Sort
                                    label="Visit Purpose"
                                    attribute="visit_purpose"
                                />
                            </th>
                            <th>Visitor PIC</th>
                            <th>Status</th>
                            <th v-if="can.edit || can.delete">Actions</th>
                        </tr>
                    </thead>
                    <tbody v-if="requests.data.length > 0">
                        <tr v-for="request in requests.data" :key="request.id">
                            <td data-label="Start Date">
                                {{ formatDateTime(request.start_date) }}
                            </td>
                            <td data-label="End Date">
                                {{ formatDateTime(request.end_date) }}
                            </td>
                            <td data-label="Roles">
                                {{ request.visit_purpose }}
                            </td>
                            <td data-label="Visitor PIC">
                                <template v-if="getPIC(request.visitors)">
                                    <span class="block">{{
                                        getPIC(request.visitors)
                                    }}</span>

                                    <a
                                        :href="
                                            route(
                                                'admin.visitor.ktp',
                                                request.visitors.find(
                                                    (v) => v.file_ktp
                                                )?.id
                                            )
                                        "
                                        class="text-sm text-blue-500 hover:underline"
                                        target="_blank"
                                        v-if="
                                            request.visitors.find(
                                                (v) => v.file_ktp
                                            )?.id
                                        "
                                    >
                                        View KTP
                                    </a>
                                </template>
                            </td>
                            <td data-label="Status" class="text-center">
                                <Badge :data="request.status" />
                            </td>
                            <td class="before:hidden lg:w-1 whitespace-nowrap">
                                <div v-if="can.acceptance">
                                    <template
                                        v-if="request.status === 'requested'"
                                    >
                                        <BaseButtons
                                            type="justify-start lg:justify-end"
                                            no-wrap
                                        >
                                            <BaseButton
                                                color="info"
                                                :icon="mdiCheck"
                                                small
                                                @click="
                                                    acceptance(
                                                        request.id,
                                                        'accept'
                                                    )
                                                "
                                            />
                                            <BaseButton
                                                color="danger"
                                                :icon="mdiClose"
                                                small
                                                @click="
                                                    acceptance(
                                                        request.id,
                                                        'reject'
                                                    )
                                                "
                                            />
                                        </BaseButtons>
                                    </template>

                                    <template
                                        v-else-if="
                                            request.status === 'accepted'
                                        "
                                    >
                                        <BaseButtons
                                            type="justify-start lg:justify-end"
                                            no-wrap
                                        >
                                            <BaseButton
                                                color="success"
                                                label="Finish"
                                                small
                                                @click="
                                                    acceptance(
                                                        request.id,
                                                        'finished'
                                                    )
                                                "
                                            />
                                            <BaseButton
                                                color="warning"
                                                label="Missed"
                                                small
                                                @click="
                                                    acceptance(
                                                        request.id,
                                                        'missed'
                                                    )
                                                "
                                            />
                                        </BaseButtons>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td class="text-center" colspan="6">No Data.</td>
                        </tr>
                    </tbody>
                </table>
                <div class="py-4">
                    <Pagination :data="requests" />
                </div>
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>
