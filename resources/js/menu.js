import {
    mdiMonitor,
    mdiAccountGroup,
    mdiOfficeBuildingMarker,
    mdiQrcodeScan,
    mdiChartBar
} from "@mdi/js";

export default [
    {
        name: "Dashboard",
        link: "/dashboard",
        enabled: 1,
        weight: 0,
        icon: mdiMonitor,
    },
    {
        name: "Visit Request",
        link: "/request",
        enabled: 1,
        weight: 0,
        icon: mdiOfficeBuildingMarker,
    },
    {
        name: "Users",
        link: "/user",
        enabled: 1,
        weight: 0,
        icon: mdiAccountGroup
    },
    {
        name: "Scanner",
        link: "/scanner",
        enabled: 1,
        weight: 0,
        icon: mdiQrcodeScan
    },
    {
        name: "Analytic",
        link: "/analytic",
        enabled: 1,
        weight: 0,
        icon: mdiChartBar
    },
];
