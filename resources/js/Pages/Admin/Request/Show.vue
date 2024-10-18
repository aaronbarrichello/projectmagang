<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import {
    mdiOfficeBuildingMarker,
    mdiArrowLeftBoldOutline,
    mdiDownload,
    mdiCheck,
    mdiClose,
} from "@mdi/js";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import CardBox from "@/Components/CardBox.vue";
import BaseButton from "@/Components/BaseButton.vue";
import Badge from "@/Components/Badge.vue";

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    roles: {
        type: Object,
        default: () => ({}),
    },
});
console.log(props.data)

const formAccaptance = useForm({});

function acceptance(id, action) {
    if (confirm(`Are you sure you want to ${action} this request?`)) {
        formAccaptance.post(route(`request.acceptance`, { id, action }));
    }
}
</script>

<template>
    <LayoutAuthenticated>
        <Head title="View Visit Request" />
        <SectionMain>
            <SectionTitleLineWithButton
                :icon="mdiOfficeBuildingMarker"
                title="View Visit Request"
                main
            >
                <div class="flex flex-col-reverse lg:flex-row items-end lg:items-center gap-3">
                    <div v-if="data.status === 'requested'" class="flex gap-3">
                        <BaseButton
                            label="Accept"
                            color="info"
                            :icon="mdiCheck"
                            small
                            @click="acceptance(data.id, 'accept')"
                        />
                        <BaseButton
                            label="Reject"
                            color="danger"
                            :icon="mdiClose"
                            small
                            @click="acceptance(data.id, 'reject')"
                        />
                    </div>
                    <div v-else>
                        <Badge :data="data.status" />
                    </div>
                    <BaseButton
                        :route-name="$page.props.urlPrevious"
                        :icon="mdiArrowLeftBoldOutline"
                        label="Back"
                        color="white"
                        rounded-full
                        small
                    />
                </div>
            </SectionTitleLineWithButton>
            <CardBox class="mb-6">
                <table>
                    <tbody>
                        <tr>
                            <td
                                class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block"
                            >
                                Visit Purpose
                            </td>
                            <td data-label="Visit Purpose">
                                {{ data.visit_purpose }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block"
                            >
                                Start Date
                            </td>
                            <td data-label="Start Date">
                                {{ data.start_date }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block"
                            >
                                End Date
                            </td>
                            <td data-label="End Date">
                                {{ data.end_date }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block"
                            >
                                Description
                            </td>
                            <td data-label="Description">
                                {{ data.description }}
                            </td>
                        </tr>
                        <tr v-if="data.spk">
                            <td
                                class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block"
                            >
                                SPK/Surat Izin/Surat Tugas
                            </td>
                            <td data-label="spk">
                                <BaseButton
                                    :href="'../storage' + data.spk"
                                    :icon="mdiDownload"
                                    class="lg:w-[120px]"
                                    label="Download"
                                    color="white"
                                    rounded-full
                                    small
                                    download
                                />
                            </td>
                        </tr>
                        <tr v-if="data.qrcode">
                            <td
                                class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block"
                            >
                                QR Code
                            </td>
                            <td data-label="QR Code">
                                <div class="flex flex-col">
                                    <img
                                        :src="'../storage' + data.qrcode"
                                        class="p-2 lg:p-3 bg-white mb-3 w-[200px]"
                                    />
                                    <BaseButton
                                        :href="'../storage' + data.qrcode"
                                        :icon="mdiDownload"
                                        class="lg:w-[120px]"
                                        label="Download"
                                        color="white"
                                        rounded-full
                                        small
                                        download
                                    />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardBox>

            <div class="my-3 text-2xl">Visitor Data</div>

            <CardBox
                class="mb-6"
                v-for="visitor in data.visitors"
                :key="visitor.id"
            >
                <table>
                    <tbody>
                        <tr>
                            <td
                                class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block"
                            >
                                Visitor Name
                            </td>
                            <td data-label="Name">
                                {{ visitor.name }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block"
                            >
                                Identity Number (KTP / Passport)
                            </td>
                            <td data-label="KTP / Passport">
                                {{ visitor.ktp }}
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block">
                                Foto KTP / Passport
                            </td>
                            <td data-label="Foto KTP / Passport">
                                <img
                                        :src="'../storage' + visitor.file_ktp"
                                        class="p-2 lg:p-3 bg-white mb-3 w-[200px]"
                                    />
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block"
                            >
                                Company
                            </td>
                            <td data-label="Company">
                                {{ visitor.company }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block"
                            >
                                Occupation
                            </td>
                            <td data-label="Occupation">
                                {{ visitor.occupation }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block"
                            >
                                Email
                            </td>
                            <td data-label="Email">
                                {{ visitor.email }}
                            </td>
                        </tr>
                        <tr>
                            <td
                                class="p-4 pl-8 text-slate-500 dark:text-slate-400 hidden lg:block"
                            >
                                Phone
                            </td>
                            <td data-label="Phone">
                                {{ visitor.phone }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </CardBox>
        </SectionMain>
    </LayoutAuthenticated>
</template>
