    <script setup>
import { ref } from "vue";
import AppMenuItem from "./AppMenuItem.vue";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";

const { t: $t } = useI18n();
const authUser = useStore().getters["adminAuth/user"].role;
const adminModel = ref([
    {
        label: $t('home'),
        items: [
            {
                label: $t('dashboard'),
                icon: "pi pi-fw pi-home",
                to: "/admin/dashboard",
                name: "admin.dashboard",
            },
        ],
    },
    {
        label: $t('collections'),
        items: [
            {
                label: $t('users'),
                icon: "pi pi-users",
                to: "/admin/users",
                name: "admin.users",
            },
            {
                label: 'Departments',
                icon: "pi pi-link",
                to: "/admin/departments",
                name: "admin.departments",
            },
            {
                label: 'Events',
                icon: "pi pi-calendar",
                to: "/admin/events",
                name: "admin.events",
            },
            {
                label: 'Colleges',
                icon: "pi pi-book",
                to: "/admin/colleges",
                name: "admin.colleges",
            },
            {
                label: 'Companies',
                icon: "pi pi-briefcase",
                to: "/admin/companies",
                name: "admin.companies",
            },
            {
                label: 'Educations',
                icon: "pi pi-inbox",
                to: "/admin/educations",
                name: "admin.educations",
            },
            {
                label: 'Experiences',
                icon: "pi pi-ticket",
                to: "/admin/experiences",
                name: "admin.experiences",
            },
            {
                label: 'Certifications',
                icon: "pi pi-file",
                to: "/admin/certifications",
                name: "admin.certifications",
            },
            {
                label: 'Posts',
                icon: "pi pi-users",
                to: "/admin/posts",
                name: "admin.posts",
            },
            {
                label: 'Comments',
                icon: "pi pi-comments",
                to: "/admin/comments",
                name: "admin.comments",
            }

        ],
    },
    // {
    //     label: $t('pages'),
    //     icon: "pi pi-fw pi-briefcase",
    //     to: "/pages",
    //     items: [
    //         // {
    //         //     label: "Landing",
    //         //     icon: "pi pi-fw pi-globe",
    //         //     to: "/landing",
    //         //     name: "landing",
    //         // },
    //         {
    //             label: $t('auth'),
    //             icon: "pi pi-fw pi-user",
    //             items: [
    //                 {
    //                     label: $t('error'),
    //                     icon: "pi pi-fw pi-times-circle",
    //                     to: "/error",
    //                     name: "error",
    //                 },
    //                 {
    //                     label: $t('accessDenied'),
    //                     icon: "pi pi-fw pi-lock",
    //                     to: "/access-denied",
    //                     name: "access-denied",
    //                 },
    //             ],
    //         },
    //     ],
    // },
    // {
    //     label: $t('primeBlocks'),
    //     items: [
    //         {
    //             label: $t('allBlocks'),
    //             icon: "pi pi-fw pi-globe",
    //             url: "https://www.primefaces.org/primeblocks-vue",
    //             target: "_blank",
    //         },
    //     ],
    // },
    // {
    //     label: $t('utilities'),
    //     items: [
    //         {
    //             label: $t('primeIcons'),
    //             icon: "pi pi-fw pi-prime",
    //             url: "https://www.primefaces.org/diamond/icons.xhtml",
    //             target: "_blank",
    //         },
    //         {
    //             label: $t('primeFlex'),
    //             icon: "pi pi-fw pi-desktop",
    //             url: "https://www.primefaces.org/primeflex/",
    //             target: "_blank",
    //         },
    //     ],
    // },
    // {
    //     label: $t('support'),
    //     items: [
    //         {
    //             label: $t('submenu1'),
    //             icon: "pi pi-fw pi-bookmark",
    //             items: [
    //                 {
    //                     label: $t('submenu11'),
    //                     icon: "pi pi-fw pi-bookmark",
    //                     items: [
    //                         {
    //                             label: $t('submenu111'),
    //                             icon: "pi pi-fw pi-bookmark",
    //                         },
    //                         {
    //                             label: $t('submenu112'),
    //                             icon: "pi pi-fw pi-bookmark",
    //                         },
    //                         {
    //                             label: $t('submenu113'),
    //                             icon: "pi pi-fw pi-bookmark",
    //                         },
    //                     ],
    //                 },
    //                 {
    //                     label: $t('submenu12'),
    //                     icon: "pi pi-fw pi-bookmark",
    //                     items: [
    //                         {
    //                             label: $t('submenu121'),
    //                             icon: "pi pi-fw pi-bookmark",
    //                         },
    //                     ],
    //                 },
    //             ],
    //         },
    //         {
    //             label: $t('submenu2'),
    //             icon: "pi pi-fw pi-bookmark",
    //             items: [
    //                 {
    //                     label: $t('submenu21'),
    //                     icon: "pi pi-fw pi-bookmark",
    //                     items: [
    //                         {
    //                             label: $t('submenu211'),
    //                             icon: "pi pi-fw pi-bookmark",
    //                         },
    //                         {
    //                             label: $t('submenu212'),
    //                             icon: "pi pi-fw pi-bookmark",
    //                         },
    //                     ],
    //                 },
    //                 {
    //                     label: $t('submenu22'),
    //                     icon: "pi pi-fw pi-bookmark",
    //                     items: [
    //                         {
    //                             label: $t('submenu221'),
    //                             icon: "pi pi-fw pi-bookmark",
    //                         },
    //                     ],
    //                 },
    //             ],
    //         },
    //     ],
    // },
]);

const model = ref([
    {
        label: $t('home'),
        items: [
            {
                label: $t('dashboard'),
                icon: "pi pi-fw pi-home",
                to: "/admin/dashboard",
                name: "admin.dashboard",
            },
        ],
    },
    {
        label: $t('collections'),
        items: [
            {
                label: $t('users'),
                icon: "pi pi-users",
                to: "/admin/users",
                name: "admin.users",
            },
        ],
    },
]);

</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in adminModel" :key="item">
            <app-menu-item
                v-if="!item.separator && authUser == 'admin' || authUser == 'super_admin'"
                :item="item"
                :index="i"
            ></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item
                v-if="!item.separator && authUser == 'manager' || authUser == 'team_leader'"
                :item="item"
                :index="i"
            ></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
