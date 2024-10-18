<script setup>
import { ref, computed } from "vue";

const props = defineProps({
    data: {
        type: String,
        default: "requested",
    },
});

const componentClass = computed(() => {
    let bgColor, textColor, darkBgColor, darkTextColor;
    if (props.data === "accepted") {
        bgColor = 'bg-green-100'
        textColor = "text-green-800";
        darkBgColor = "dark:bg-green-900";
        darkTextColor = "dark:text-green-300";
    } else if (props.data === "rejected") {
        bgColor = 'bg-red-100'
        textColor = "text-red-800";
        darkBgColor = "dark:bg-red-900";
        darkTextColor = "dark:text-red-300";
    } else if (props.data === "finished") {
        bgColor = 'bg-blue-100'
        textColor = "text-blue-800";
        darkBgColor = "dark:bg-blue-900";
        darkTextColor = "dark:text-blue-300";
    } else if (props.data === 'missed') {
        bgColor = 'bg-amber-100'
        textColor = "text-amber-800";
        darkBgColor = "dark:bg-amber-900";
        darkTextColor = "dark:text-amber-300";
    } else {
        bgColor = 'bg-cyan-100'
        textColor = "text-cyan-800";
        darkBgColor = "dark:bg-cyan-900";
        darkTextColor = "dark:text-cyan-300";
    }

    const base = [
        bgColor,
        textColor,
        'text-xs font-medium me-2 px-2.5 py-0.5 rounded',
        darkBgColor,
        darkTextColor
    ]

    return base
});
</script>

<template>
    <span
        :class="componentClass"
    >
        {{ data }}
    </span>
</template>
