<script setup lang="ts">
import { index as staffIndex } from '@/routes/staff';
import { index, resend, revoke, store } from '@/routes/staff/invitations';
import type { StaffRoleOption, StaffRoleValue } from '@/types/staff';
import type {
    PaginatedInvitations,
    StaffInvitationSummary,
} from '@/types/invitations';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import { formatUtc } from '@/lib/date';

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Staff', href: staffIndex() },
            { title: 'Invitations', href: index() },
        ],
    },
});

defineProps<{
    invitations: PaginatedInvitations;
    roleOptions: StaffRoleOption[];
    permissions: {
        create: boolean;
    };
}>();

const issueForm = useForm<{
    email: string;
    roles: StaffRoleValue[];
    administrator_confirmation: boolean;
}>({
    email: '',
    roles: [],
    administrator_confirmation: false,
});

const resendForm = useForm({
    administrator_confirmation: false,
});

const revokeForm = useForm({});

const busy = computed(
    () =>
        issueForm.processing || resendForm.processing || revokeForm.processing,
);

const managementErrors = computed(() => ({
    ...resendForm.errors,
    ...revokeForm.errors,
}));

function resendInvitation(invitation: StaffInvitationSummary): void {
    if (busy.value) {
        return;
    }

    const includesAdmin = invitation.roles.includes('administrator');

    const message = includesAdmin
        ? `Resend to ${invitation.email}? This explicitly approves Administrator access and immediately invalidates the previous link.`
        : `Resend to ${invitation.email}? The previous link will immediately become invalid.`;

    if (!window.confirm(message)) {
        return;
    }

    resendForm.clearErrors();
    revokeForm.clearErrors();
    resendForm.administrator_confirmation = includesAdmin;

    resendForm.post(resend.url(invitation.id), {
        preserveScroll: true,
        onSuccess: () => resendForm.resetAndClearErrors(),
        onFinish: () => {
            resendForm.administrator_confirmation = false;
        },
    });
}

function revokeInvitation(invitation: StaffInvitationSummary): void {
    if (
        busy.value ||
        !window.confirm(
            `Revoke the invitation for ${invitation.email}? Its link will no longer work.`,
        )
    ) {
        return;
    }

    resendForm.clearErrors();
    revokeForm.clearErrors();

    revokeForm.post(revoke.url(invitation.id), {
        preserveScroll: true,
    });
}

const includesAdministrator = computed(() =>
    issueForm.roles.includes('administrator'),
);

function submitInvitation(): void {
    issueForm.post(store.url(), {
        preserveScroll: true,
        onSuccess: () => issueForm.resetAndClearErrors(),
    });
}
</script>

<template>
    <Head title="Staff invitations" />

    <div class="flex flex-1 flex-col gap-6 p-4">
        <header class="space-y-2">
            <Link :href="staffIndex()" class="text-sm underline">
                Back to staff
            </Link>
            <h1 class="text-xl font-semibold">Staff invitations</h1>
            <p class="text-sm text-muted-foreground">
                Invite a new staff member with explicitly selected roles.
                Invitations expire after 48 hours.
            </p>
        </header>

        <form
            v-if="permissions.create"
            class="max-w-xl space-y-4 rounded-lg border p-4"
            @submit.prevent="submitInvitation"
        >
            <h2 class="font-semibold">Create invitation</h2>

            <fieldset :disabled="busy" class="space-y-4">
                <div class="space-y-2">
                    <Label for="invitation-email">Email address</Label>
                    <Input
                        id="invitation-email"
                        v-model="issueForm.email"
                        type="email"
                        autocomplete="off"
                        maxlength="255"
                        required
                    />
                </div>

                <fieldset class="space-y-2">
                    <legend class="mb-2 text-sm font-medium">
                        Staff roles
                    </legend>

                    <label
                        v-for="option in roleOptions"
                        :key="option.value"
                        class="flex items-center gap-2 text-sm"
                    >
                        <input
                            v-model="issueForm.roles"
                            type="checkbox"
                            :value="option.value"
                        />
                        {{ option.label }}
                    </label>
                </fieldset>

                <label
                    v-if="includesAdministrator"
                    class="flex items-start gap-2 rounded-md border p-3 text-sm"
                >
                    <input
                        v-model="issueForm.administrator_confirmation"
                        type="checkbox"
                        required
                        class="mt-1"
                    />
                    <span>
                        I explicitly approve granting Administrator access to
                        this recipient.
                    </span>
                </label>

                <div
                    v-if="Object.keys(issueForm.errors).length"
                    role="alert"
                    class="space-y-1 text-sm text-destructive"
                >
                    <p
                        v-for="(message, field) in issueForm.errors"
                        :key="field"
                    >
                        {{ message }}
                    </p>
                </div>

                <p class="text-sm text-muted-foreground">
                    The recipient chooses their own password. An existing
                    account cannot be provisioned through an invitation. Recent
                    password confirmation may be required.
                </p>

                <Button
                    type="submit"
                    :disabled="
                        issueForm.processing ||
                        issueForm.roles.length === 0 ||
                        (includesAdministrator &&
                            !issueForm.administrator_confirmation)
                    "
                >
                    {{
                        issueForm.processing ? 'Creating…' : 'Create invitation'
                    }}
                </Button>
            </fieldset>
        </form>

        <section class="space-y-4">
            <h2 class="font-semibold">Invitation history</h2>

            <div
                v-if="Object.keys(managementErrors).length"
                role="alert"
                class="space-y-1 text-sm text-destructive"
            >
                <p v-for="(message, field) in managementErrors" :key="field">
                    {{ message }}
                </p>
            </div>

            <div class="overflow-x-auto rounded-lg border">
                <table class="w-full text-left text-sm">
                    <caption class="sr-only">
                        Staff invitations and available management actions
                    </caption>

                    <thead class="border-b bg-muted/50">
                        <tr>
                            <th scope="col" class="p-3">Email</th>
                            <th scope="col" class="p-3">Roles</th>
                            <th scope="col" class="p-3">Status</th>
                            <th scope="col" class="p-3">Expires (UTC)</th>
                            <th scope="col" class="p-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="invitation in invitations.data"
                            :key="invitation.id"
                            class="border-b"
                        >
                            <th scope="row" class="p-3 font-medium break-all">
                                {{ invitation.email }}
                            </th>

                            <td class="p-3">
                                {{
                                    roleOptions
                                        .filter((option) =>
                                            invitation.roles.includes(
                                                option.value,
                                            ),
                                        )
                                        .map((option) => option.label)
                                        .join(', ')
                                }}
                            </td>

                            <td class="p-3 capitalize">
                                {{ invitation.status }}
                            </td>

                            <td class="p-3">
                                {{ formatUtc(invitation.expires_at) }}
                            </td>

                            <td class="p-3">
                                <div class="flex flex-wrap gap-2">
                                    <Button
                                        v-if="invitation.permissions.resend"
                                        type="button"
                                        variant="outline"
                                        :disabled="busy"
                                        @click="resendInvitation(invitation)"
                                    >
                                        Resend
                                    </Button>

                                    <Button
                                        v-if="invitation.permissions.revoke"
                                        type="button"
                                        variant="destructive"
                                        :disabled="busy"
                                        @click="revokeInvitation(invitation)"
                                    >
                                        Revoke
                                    </Button>

                                    <span
                                        v-if="
                                            !invitation.permissions.resend &&
                                            !invitation.permissions.revoke
                                        "
                                        class="text-muted-foreground"
                                    >
                                        No actions
                                    </span>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="invitations.data.length === 0">
                            <td colspan="5" class="p-4 text-muted-foreground">
                                No invitations yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <nav
                aria-label="Invitation pagination"
                class="flex items-center gap-4 text-sm"
            >
                <Link
                    v-if="invitations.prev_page_url"
                    :href="invitations.prev_page_url"
                    preserve-scroll
                    class="underline"
                >
                    Previous
                </Link>

                <span>
                    Page {{ invitations.current_page }} of
                    {{ invitations.last_page }} · {{ invitations.total }} total
                </span>

                <Link
                    v-if="invitations.next_page_url"
                    :href="invitations.next_page_url"
                    preserve-scroll
                    class="underline"
                >
                    Next
                </Link>
            </nav>
        </section>
    </div>
</template>

<style scoped></style>
