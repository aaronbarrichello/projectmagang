<script setup>
import { Head } from "@inertiajs/vue3";
import {
    mdiQrcodeScan,
} from "@mdi/js";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionMain from "@/Components/SectionMain.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";
import QrCodeReader from "@/Components/QrCodeReader.vue";

</script>

<template>
    <LayoutAuthenticated>
        <Head title="Scanner" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiQrcodeScan" title="Scan" main>
            </SectionTitleLineWithButton>

            <QrCodeReader />
        </SectionMain>
    </LayoutAuthenticated>
</template>
