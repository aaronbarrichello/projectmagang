<script setup>
import {
    mdiPlus,
    mdiChevronDown,
    mdiChevronRight,
    mdiTrashCanOutline,
} from "@mdi/js";
import BaseButton from "@/Components/BaseButton.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import FormFilePicker from "@/Components/FormFilePicker.vue";
import { ref, computed } from "vue";
import BaseIcon from "@/Components/BaseIcon.vue";

const itemRefs = ref([]);

const { form } = defineProps({
    form: {
        type: Object,
        default: () => ({}),
    },
    visitors: {
        type: Array,
        default: () => [],
    },
    withoutHeader: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(["add", "delete", "toggleShow"]);
const visitorErrors = computed(() => {
    return Object.keys(form.errors).map((item) => item.replaceAll(".", " "));
});

</script>
<template>
    <div v-if="!withoutHeader" class="flex justify-between">
        <h1 class="flex justify-start text-xl font-semibold">Visitor List</h1>
        <BaseButton
            @click="emit('add')"
            :icon="mdiPlus"
            label="Add Visitor"
            color="info"
            rounded
            small
        />
    </div>
    <div
        v-for="(visitor, index) in visitors"
        :key="index"
        class="my-5"
        ref="itemRefs"
    >
        <div
            class="flex justify-end items-center gap-2 mb-5 py-3 border-b-[1px] border-gray-700 dark:border-gray-500"
            :class="
                visitorErrors.some((item) => item.includes(index)) &&
                'border-red-400'
            "
        >
            <div
                @click="emit('toggleShow', index)"
                class="flex items-center cursor-pointer"
                :class="
                    visitorErrors.some((item) => item.includes(index)) &&
                    'text-red-400'
                "
            >
                <BaseIcon :path="visitor.show ? mdiChevronDown : mdiChevronRight" />
                <span>
                    Visitor {{ index + 1 }}
                    <span v-if="index === 0">(PIC)</span>
                </span>
            </div>
            <div
                v-if="index > 0"
                @click="emit('delete', index)"
                class="cursor-pointer"
            >
                <BaseIcon :path="mdiTrashCanOutline" />
            </div>
        </div>

        <div class="visitor-container">
            <div :id="`visitorList-${index}`" class="visitor-list p-1" :class="visitor.show && 'show'">
                <FormField
                    label="NIK / KTP *"
                    :class="{
                        'text-red-400': form.errors[`visitors.${index}.ktp`],
                    }"
                >

                    <FormControl
                        v-model="visitor.ktp"
                        type="number"
                        placeholder="Enter NIK / KTP"
                        :error="form.errors[`visitors.${index}.ktp`]"
                    >
                        <div
                            class="text-red-400 text-sm mt-1"
                            v-if="form.errors[`visitors.${index}.ktp`]"
                        >
                            {{ form.errors[`visitors.${index}.ktp`] }}
                        </div>
                    </FormControl>
                </FormField>

                <FormField
                    label="Name *"
                    :class="{
                        'text-red-400': form.errors[`visitors.${index}.name`],
                    }"
                >
                    <FormControl
                        v-model="visitor.name"
                        type="text"
                        placeholder="Enter name"
                        :error="form.errors[`visitors.${index}.name`]"
                    >
                        <div
                            class="text-red-400 text-sm mt-1"
                            v-if="form.errors[`visitors.${index}.name`]"
                        >
                            {{ form.errors[`visitors.${index}.name`] }}
                        </div>
                    </FormControl>
                </FormField>

                <FormField
                    label="File KTP *"
                    :class="{
                        'text-red-400':
                            form.errors[`visitors.${index}.file_ktp`],
                    }"
                >
                    <FormFilePicker
                        v-model="visitor.file_ktp"
                        @update:model-value="visitor.file_ktp = $event.target.files[0]"
                        accept=".jpg, .jpeg, .png"
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
                        v-if="form.errors[`visitors.${index}.file_ktp`]"
                    >
                        {{ form.errors[`visitors.${index}.file_ktp`] }}
                    </div>
                    <div class="text-gray-400 text-sm">
                        Format file yang didukung: JPG/PNG
                    </div>
                    <div class="text-gray-400 text-sm">
                        Max 250KB
                    </div>
                </FormField>

                <FormField
                    label="Company *"
                    :class="{
                        'text-red-400':
                            form.errors[`visitors.${index}.company`],
                    }"
                >
                    <FormControl
                        v-model="visitor.company"
                        type="text"
                        placeholder="Enter company"
                        :error="form.errors[`visitors.${index}.company`]"
                    >
                        <div
                            class="text-red-400 text-sm mt-1"
                            v-if="form.errors[`visitors.${index}.company`]"
                        >
                            {{ form.errors[`visitors.${index}.company`] }}
                        </div>
                    </FormControl>
                </FormField>

                <FormField
                    label="Occupation *"
                    :class="{
                        'text-red-400':
                            form.errors[`visitors.${index}.occupation`],
                    }"
                >
                    <FormControl
                        v-model="visitor.occupation"
                        type="text"
                        placeholder="Enter occupation"
                        :error="form.errors[`visitors.${index}.occupation`]"
                    >
                        <div
                            class="text-red-400 text-sm mt-1"
                            v-if="form.errors[`visitors.${index}.occupation`]"
                        >
                            {{ form.errors[`visitors.${index}.occupation`] }}
                        </div>
                    </FormControl>
                </FormField>

                <FormField
                    label="Phone Number *"
                    :class="{
                        'text-red-400': form.errors[`visitors.${index}.phone`],
                    }"
                >
                    <FormControl
                        v-model="visitor.phone"
                        type="number"
                        placeholder="Enter phone"
                        :error="form.errors[`visitors.${index}.phone`]"
                    >
                        <div
                            class="text-red-400 text-sm mt-1"
                            v-if="form.errors[`visitors.${index}.phone`]"
                        >
                            {{ form.errors[`visitors.${index}.phone`] }}
                        </div>
                    </FormControl>
                </FormField>

                <FormField
                    label="Email *"
                    :class="{
                        'text-red-400': form.errors[`visitors.${index}.email`],
                    }"
                >
                    <FormControl
                        v-model="visitor.email"
                        type="email"
                        placeholder="Enter email"
                        :error="form.errors[`visitors.${index}.email`]"
                    >
                        <div
                            class="text-red-400 text-sm mt-1"
                            v-if="form.errors[`visitors.${index}.email`]"
                        >
                            {{ form.errors[`visitors.${index}.email`] }}
                        </div>
                    </FormControl>
                </FormField>
            </div>
        </div>
    </div>
</template>
<style scoped>
.visitor-container {
    overflow: hidden;
}
.visitor-list.show {
    margin-top: 0;
}
.visitor-list {
    margin-top: -100vh;
    transition: all 1s;
}
</style>
