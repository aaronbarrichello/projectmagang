<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import {
    mdiAccount,
    mdiAccountCircle,
    mdiLock,
    mdiMail,
    mdiAsterisk,
    mdiFormTextboxPassword,
    mdiArrowLeftBoldOutline,
    mdiAlertBoxOutline,
} from "@mdi/js";
import SectionMain from "@/Components/SectionMain.vue";
import CardBox from "@/Components/CardBox.vue";
import BaseDivider from "@/Components/BaseDivider.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import NotificationBar from "@/Components/NotificationBar.vue";
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue";
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue";

const props = defineProps({
    user: {
        type: Object,
        default: () => ({}),
    },
});

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
});
const passwordForm = useForm({
    current_password: null,
    password: null,
    password_confirmation: null,
});
</script>

<template>
    <LayoutAuthenticated>
        <Head title="Profile" />
        <SectionMain>
            <SectionTitleLineWithButton :icon="mdiAccount" title="Profile" main>
                <BaseButton
                    :route-name="route('dashboard')"
                    :icon="mdiArrowLeftBoldOutline"
                    label="Back"
                    color="white"
                    rounded-full
                    small
                />
            </SectionTitleLineWithButton>
            <NotificationBar
                :key="Date.now()"
                v-if="$page.props.flash.message"
                color="success"
                :icon="mdiAlertBoxOutline"
            >
                {{ $page.props.flash.message }}
            </NotificationBar>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <CardBox
                    title="Edit Profile"
                    :icon="mdiAccountCircle"
                    form
                    @submit.prevent="
                        profileForm.post(route('account.info.store'))
                    "
                >
                    <FormField
                        label="Name"
                        help="Required. Your name"
                        :class="{ 'text-red-400': profileForm.errors.name }"
                    >
                        <FormControl
                            v-model="profileForm.name"
                            :icon="mdiAccount"
                            name="name"
                            required
                            :error="profileForm.errors.name"
                        >
                            <div
                                class="text-red-400 text-sm"
                                v-if="profileForm.errors.name"
                            >
                                {{ profileForm.errors.name }}
                            </div>
                        </FormControl>
                    </FormField>
                    <FormField
                        label="Email"
                        help="Required. Your e-mail"
                        :class="{ 'text-red-400': profileForm.errors.email }"
                    >
                        <FormControl
                            v-model="profileForm.email"
                            :icon="mdiMail"
                            type="email"
                            name="email"
                            required
                            :error="profileForm.errors.email"
                        >
                            <div
                                class="text-red-400 text-sm"
                                v-if="profileForm.errors.email"
                            >
                                {{ profileForm.errors.email }}
                            </div>
                        </FormControl>
                    </FormField>

                    <template #footer>
                        <BaseButtons>
                            <BaseButton
                                color="info"
                                type="submit"
                                label="Submit"
                            />
                        </BaseButtons>
                    </template>
                </CardBox>

                <CardBox
                    title="Change Password"
                    :icon="mdiLock"
                    form
                    @submit.prevent="
                        passwordForm.post(route('account.password.store'), {
                            preserveScroll: true,
                            onSuccess: () => passwordForm.reset(),
                        })
                    "
                >
                    <FormField
                        label="Current password"
                        help="Required. Your current password"
                        :class="{
                            'text-red-400':
                                passwordForm.errors.current_password,
                        }"
                    >
                        <FormControl
                            v-model="passwordForm.current_password"
                            :icon="mdiAsterisk"
                            name="current_password"
                            type="password"
                            required
                            :error="passwordForm.errors.current_password"
                        >
                            <div
                                class="text-red-400 text-sm"
                                v-if="passwordForm.errors.current_password"
                            >
                                {{ passwordForm.errors.current_password }}
                            </div>
                        </FormControl>
                    </FormField>

                    <BaseDivider />

                    <FormField
                        label="New password"
                        help="Required. New password"
                        :class="{
                            'text-red-400': passwordForm.errors.password,
                        }"
                    >
                        <FormControl
                            v-model="passwordForm.password"
                            :icon="mdiFormTextboxPassword"
                            name="password"
                            type="password"
                            required
                            :error="passwordForm.errors.password"
                        >
                            <div
                                class="text-red-400 text-sm"
                                v-if="passwordForm.errors.password"
                            >
                                {{ passwordForm.errors.password }}
                            </div>
                        </FormControl>
                    </FormField>

                    <FormField
                        label="Confirm password"
                        help="Required. New password one more time"
                        :class="{
                            'text-red-400':
                                passwordForm.errors.password_confirmation,
                        }"
                    >
                        <FormControl
                            v-model="passwordForm.password_confirmation"
                            :icon="mdiFormTextboxPassword"
                            name="password_confirmation"
                            type="password"
                            required
                            :error="passwordForm.errors.password_confirmation"
                        >
                            <div
                                class="text-red-400 text-sm"
                                v-if="passwordForm.errors.password_confirmation"
                            >
                                {{ passwordForm.errors.password_confirmation }}
                            </div>
                        </FormControl>
                    </FormField>

                    <template #footer>
                        <BaseButtons>
                            <BaseButton
                                type="submit"
                                color="info"
                                label="Submit"
                            />
                        </BaseButtons>
                    </template>
                </CardBox>
            </div>
        </SectionMain>
    </LayoutAuthenticated>
</template>
