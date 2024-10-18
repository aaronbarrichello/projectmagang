<script setup>
import { reactive, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useLayoutStore } from "@/Stores/layout.js";
import menu from '@/menu.js'
import AsideMenuLayer from "@/Components/AsideMenuLayer.vue";
import OverlayLayer from "@/Components/OverlayLayer.vue";

// let menu = reactive({});
// menu = computed(() => usePage().props.navigation.menu);
// let menu = [
//     {
//         name: "Dashboard",
//         link: "/dashboard",
//         enabled: 1,
//         weight: 0,
//         icon: "M13,3V9H21V3M13,21H21V11H13M3,21H11V15H3M3,13H11V3H3V13Z",
//     },
//     {
//         name: "Visit Request",
//         link: "/request",
//         enabled: 1,
//         weight: 2,
//         icon: "M3,6H21V8H3V6M3,11H21V13H3V11M3,16H21V18H3V16Z"
//     },
//     {
//         name: "Users",
//         link: "/user",
//         enabled: 1,
//         weight: 3,
//         icon: "M16 17V19H2V17S2 13 9 13 16 17 16 17M12.5 7.5A3.5 3.5 0 1 0 9 11A3.5 3.5 0 0 0 12.5 7.5M15.94 13A5.32 5.32 0 0 1 18 17V19H22V17S22 13.37 15.94 13M15 4A3.39 3.39 0 0 0 13.07 4.59A5 5 0 0 1 13.07 10.41A3.39 3.39 0 0 0 15 11A3.5 3.5 0 0 0 15 4Z",
//     },
//     {
//         name: "Scanner",
//         link: "/scanner",
//         enabled: 1,
//         weight: 3,
//         icon: "M16 17V19H2V17S2 13 9 13 16 17 16 17M12.5 7.5A3.5 3.5 0 1 0 9 11A3.5 3.5 0 0 0 12.5 7.5M15.94 13A5.32 5.32 0 0 1 18 17V19H22V17S22 13.37 15.94 13M15 4A3.39 3.39 0 0 0 13.07 4.59A5 5 0 0 1 13.07 10.41A3.39 3.39 0 0 0 15 11A3.5 3.5 0 0 0 15 4Z",
//     },
// ];


const layoutStore = useLayoutStore();
</script>

<template>
    <AsideMenuLayer
        v-if="Object.keys(menu).length"
        :menu="menu"
        :class="[
            layoutStore.isAsideMobileExpanded ? 'left-0' : '-left-60 lg:left-0',
            { 'lg:hidden xl:flex': !layoutStore.isAsideLgActive },
        ]"
    />
    <OverlayLayer
        v-show="layoutStore.isAsideLgActive"
        z-index="z-30"
        @overlay-click="layoutStore.isAsideLgActive = false"
    />
</template>
