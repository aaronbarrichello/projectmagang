<script setup>
import { ref, computed } from "vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import FormFilePicker from "@/Components/FormFilePicker.vue";
import FormCheckRadioGroup from "@/Components/FormCheckRadioGroup.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import { useStyleStore } from "@/Stores/style.js";

const { form } = defineProps({
    form: {
        type: Object,
        default: () => ({}),
    },
});

const styleStore = useStyleStore();

const puposes = [
    "Data Center",
    "Network Center",
    "Network Operation Center",
];

const startTime = ref({ hours: 8, minutes: 0 });
const minTime = ref({ hours: 8, minutes: 0 });
const maxTime = ref({ hours: 17, minutes: 0 });

const minTimeEndDate = computed(() => {
    let hours = 8;
    let minutes = 0;
    if (form.start_date !== "") {
        let newDate = new Date(form.start_date);
        hours = newDate.getHours();
        minutes = newDate.getMinutes();
    }
    return { hours, minutes };
});

const disabledEndDate = computed(() => form.start_date === "");
</script>
<template>
    <FormField
        label="Visit Purpose *"
        :class="{ 'text-red-400': form.errors.visit_purpose }"
    >
        <FormCheckRadioGroup
            v-model="form.visit_purpose"
            name="visit_purpose"
            type="radio"
            :options="puposes"
        />
        <div class="text-red-400 text-sm" v-if="form.errors.visit_purpose">
            {{ form.errors.visit_purpose }}
        </div>
    </FormField>

    <FormField
        label="Start Date *"
        :class="{ 'text-red-400': form.errors.start_date }"
    >
        <VueDatePicker
            v-model="form.start_date"
            :start-time="startTime"
            :min-time="minTime"
            :max-time="maxTime"
            :min-date="new Date()"
            placeholder="Enter Start Date"
            :error="form.errors.start_date"
            :dark="styleStore.darkMode"
        >
        </VueDatePicker>
        <div class="text-red-400 text-sm" v-if="form.errors.start_date">
            {{ form.errors.start_date }}
        </div>
    </FormField>

    <FormField
        label="End Date *"
        :class="{ 'text-red-400': form.errors.end_date }"
    >
        <VueDatePicker
            v-model="form.end_date"
            :disabled="disabledEndDate"
            :start-time="minTimeEndDate"
            :min-time="minTimeEndDate"
            :max-time="maxTime"
            :min-date="new Date(form.start_date)"
            placeholder="Enter End Date"
            :error="form.errors.end_date"
            :dark="styleStore.darkMode"
        >
        </VueDatePicker>
        <div class="text-red-400 text-sm" v-if="form.errors.end_date">
            {{ form.errors.end_date }}
        </div>
    </FormField>

    <FormField
        label="Description *"
        :class="{ 'text-red-400': form.errors.description }"
    >
        <FormControl
            v-model="form.description"
            type="textarea"
            placeholder="Enter description"
            :error="form.errors.description"
        >
            <div class="text-red-400 text-sm" v-if="form.errors.description">
                {{ form.errors.description }}
            </div>
        </FormControl>
    </FormField>

    <FormField
        label="Upload Intro (SPK/Surat Izin/Surat Tugas)"
        :class="{ 'text-red-400': form.errors.spk }"
    >
        <FormFilePicker
            v-model="form.spk"
            @update:model-value="form.spk = $event.target.files[0]"
            accept=".pdf"
        ></FormFilePicker>
        <progress
            v-if="form.progress"
            :value="form.progress.percentage"
            max="100"
        >
            {{ form.progress.percentage }}%
        </progress>
        <div
            class="text-red-400 text-sm"
            v-if="form.errors.spk"
        >
            {{ form.errors.spk }}
        </div>
        <div class="text-gray-400 text-sm">
            Format file yang didukung: PDF
        </div>
        <div class="text-gray-400 text-sm">
            Max 250KB
        </div>
    </FormField>
</template>
