<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
import {
    mdiMonitor,
    mdiTelevisionGuide,
    mdiAccountMultiple,
    mdiClockOutline,
    mdiClockCheckOutline,
    mdiClockAlertOutline,
    mdiCheckBold,
    mdiCloseThick,
} from "@mdi/js";
import SectionMain from "@/Components/SectionMain.vue";
import BaseButton from "@/Components/BaseButton.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import CardBoxWidget from "@/Components/CardBoxWidget.vue";
import TableLatestRequest from "@/Components/TableLatestRequest.vue";
import TableUpcomingVisit from "@/Components/TableUpcomingVisit.vue";

const props = defineProps({
    dataTotals: {
        type: Object,
        default: () => ({}),
    },
    latestRequest: {
        type: Array,
        default: () => [],
    },
    upcomingVisit: {
        type: Array,
        default: () => [],
    },
    dailyData: {
        type: Array,
        default: () => [],
    },
});

const columnLatestRequest = ref([
    "Start Date",
    "End Date",
    "Visit Purpose",
    "Visitor (PIC)",
    "Status",
]);

const columnUpcomingVisit = ref([
    "Start Date",
    "End Date",
    "Visit Purpose",
    "Visitor (PIC)",
]);
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Dashboard" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiMonitor"
                title="Overview"
                main
            >
                <BaseButton
                    href="/pdf/PANDUAN_PENGGUNAAN_APLIKASI_VISITOR_MANAGEMENT_ADMIN.pdf"
                    target="_blank"
                    :icon="mdiTelevisionGuide"
                    label="User Manual"
                    color="info"
                    rounded
                    small
                />
            </SectionTitleLineWithButton>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6">
                <CardBoxWidget
                    color="text-slate-600"
                    :icon="mdiAccountMultiple"
                    :number="dataTotals.all"
                    label="All Request"
                />
                <CardBoxWidget
                    color="text-green-500"
                    :icon="mdiCheckBold"
                    :number="dataTotals.accepted"
                    label="Accepted Request"
                />
                <CardBoxWidget
                    color="text-red-500"
                    :icon="mdiCloseThick"
                    :number="dataTotals.rejected"
                    label="Rejected Request"
                />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mb-6">
                <CardBoxWidget
                    color="text-cyan-500"
                    :icon="mdiClockOutline"
                    :number="dataTotals.requested"
                    label="Pending Request"
                />
                <CardBoxWidget
                    color="text-blue-500"
                    :icon="mdiClockCheckOutline"
                    :number="dataTotals.finished"
                    label="Finished Request"
                />
                <CardBoxWidget
                    color="text-amber-500"
                    :icon="mdiClockAlertOutline"
                    :number="dataTotals.missed"
                    label="Missed Request"
                />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
                <CardBox title="Latest Visit Request" :hasTable="true">
                    <TableLatestRequest
                        :columns="columnLatestRequest"
                        :datas="latestRequest"
                    />
                </CardBox>
                <CardBox title="Upcoming Visit" :hasTable="true">
                    <TableUpcomingVisit
                        :columns="columnUpcomingVisit"
                        :datas="upcomingVisit"
                    />
                </CardBox>
            </div>
        </SectionMain>
    </LayoutAuthenticated>
</template>
