<script setup>
import BaseButton from "@/Components/BaseButton.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { mdiPlus, mdiSend, mdiTelevisionGuide } from "@mdi/js";
import { mdiAlertBoxOutline } from "@mdi/js";
import VisitDetail from "./Admin/Request/partials/VisitDetail.vue";
import VisitorList from "./Admin/Request/partials/VisitorList.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import LayoutGuest from "@/Layouts/LayoutGuest.vue";
import { reactive, ref } from "vue";

const form = useForm({
    visit_purpose: "",
    start_date: "",
    end_date: "",
    description: "",
    spk: null,
    visitors: [],
});

const initialVisitor = {
    ktp: "",
    name: "",
    file_ktp: "",
    company: "",
    occupation: "",
    phone: "",
    email: "",
    show: true,
};

const terms = ref(false);
const dataVisitors = reactive([initialVisitor]);

const addVisitor = () => {
    dataVisitors.push({
        ktp: "",
        name: "",
        file_ktp: "",
        company: "",
        occupation: "",
        phone: "",
        email: "",
        show: true,
    });
};

const toggleShowVisitor = (index) =>
    (dataVisitors[index].show = !dataVisitors[index].show);

const deleteVisitor = (index) => dataVisitors.splice(index, 1);

const submit = () => {
    form.visitors = dataVisitors;
    form.post(route("form.visit.store"));
};
</script>

<template>
    <LayoutGuest>
        <Head title="Form" />
        <div
            class="relative min-h-screen flex flex-col items-center selection:bg-blue-500 selection:text-white"
        >
            <NotificationBar
                :key="Date.now()"
                v-if="$page.props.flash.message"
                :color="
                    $page.props.flash.color == null
                        ? 'success'
                        : $page.props.flash.color
                "
                :icon="mdiAlertBoxOutline"
                class="m-5 w-1/2"
            >
                {{ $page.props.flash.message }}
            </NotificationBar>
            <div class="flex flex-col lg:flex-row w-full">
                <div class="w-full lg:h-screen lg:w-1/3 flex flex-col justify-between items-center lg:fixed pt-10 gap-5 lg:py-10 dark:bg-slate-900/70 bg-white">
                    <div class="flex items-center gap-5">
                        <div class="logo1">
                            <img src="../../img/direktorat-logo.png" class="max-w-[75px] lg:max-w-[100px] inline-block rounded-full ring-2 ring-white bg-white" />
                        </div>
                        <div class="logo2">
                            <img src="../../img/tu-logo.png" class="max-h-[50px] lg:max-h-[75px] bg-white rounded-md" />
                        </div>
                    </div>
                    <div>
                        <span class="text-3xl font-semibold">Visit Request App</span>
                    </div>
                    <div>
                        <BaseButton
                            href="/pdf/PANDUAN_PENGGUNAAN_APLIKASI_VISITOR_MANAGEMENT_VISITOR.pdf"
                            target="_blank"
                            :icon="mdiTelevisionGuide"
                            label="User Manual"
                            color="info"
                            rounded
                            small
                        />
                    </div>
                    <div class="hidden lg:flex">
                        <img src="../../img/illustration.png" class="p-10 pt-0" />
                    </div>
                </div>
                <form
                    @submit.prevent="submit"
                    class="relative lg:left-1/3 w-full lg:w-2/3 flex flex-col p-8 dark:bg-slate-900/70 bg-white"
                >
                    <div class="flex flex-col gap-8">
                        <div class="flex flex-col w-full lg:w-50">
                            <div class="border-b border-slate-400 pb-4">
                                <h1 class="text-3xl">Visit Detail</h1>
                            </div>
                            <div class="py-4">
                                <VisitDetail :form="form" />
                            </div>
                        </div>
                        <div class="w-full lg:w-50">
                            <div
                                class="border-b border-slate-400 pb-4 flex justify-between"
                            >
                                <h1 class="text-3xl">Visitor List</h1>
                                <BaseButton
                                    @click="addVisitor"
                                    :icon="mdiPlus"
                                    label="Add Visitor"
                                    color="info"
                                    rounded
                                    small
                                />
                            </div>
                            <div class="py-4 h-screen overflow-auto pr-2">
                                <VisitorList
                                    :form="form"
                                    :visitors="dataVisitors"
                                    @delete="deleteVisitor"
                                    @toggleShow="toggleShowVisitor"
                                    without-header
                                />
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col lg:flex-row gap-5 justify-between pt-5 border-t-2 mt-5">
                        <div class="terms-checkbox flex items-center gap-2">
                            <input type="checkbox" v-model="terms" />
                            <span>I have entered the data correctly</span>
                        </div>
                        <div>
                            <BaseButton
                                type="submit"
                                :icon="mdiSend"
                                label="Submit"
                                color="info"
                                :disabled="!terms"
                                rounded
                            />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </LayoutGuest>
</template>
