<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { activate, deactivate, index } from '@/routes/staff';
import { update as updateRoles } from '@/routes/staff/roles';
import type {
    StaffDetails,
    StaffPermissions,
    StaffRoleOption,
    StaffRoleValue,
} from '@/types/staff';
import { formatUtc } from '@/lib/date';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Staff',
                href: index(),
            },
        ],
    },
});

const props = defineProps<{
    staff: StaffDetails;
    roleOptions: StaffRoleOption[];
    permissions: StaffPermissions;
}>();

const accessForm = useForm<{ roles: StaffRoleValue[] }>({
    roles: [...props.staff.roles],
});

const deactivationForm = useForm({
    reason: '',
});

const busy = computed(
    () => accessForm.processing || deactivationForm.processing,
);

const currentRoles = computed(
    () =>
        props.roleOptions
            .filter((option) => props.staff.roles.includes(option.value))
            .map((option) => option.label)
            .join(', ') || 'No roles',
);

function resetForms(): void {
    accessForm.defaults({ roles: [...props.staff.roles] });
    accessForm.resetAndClearErrors();
    deactivationForm.resetAndClearErrors();
}

function submitAccess(): void {
    const selectedRoles = props.roleOptions
        .filter((option) => accessForm.roles.includes(option.value))
        .map((option) => option.label)
        .join(', ');

    const operation = props.staff.is_active
        ? 'Replace roles'
        : 'Activate account';

    if (
        !window.confirm(
            `${operation} for ${props.staff.name} with: ${selectedRoles}?`,
        )
    ) {
        return;
    }

    const options = {
        preserveScroll: true,
        onSuccess: resetForms,
    };

    if (props.staff.is_active) {
        accessForm.patch(updateRoles.url(props.staff.id), options);
    } else {
        accessForm.post(activate.url(props.staff.id), options);
    }
}

function submitDeactivation(): void {
    if (
        !window.confirm(
            `Deactivate ${props.staff.name} and revoke their browser access?`,
        )
    ) {
        return;
    }

    deactivationForm.post(deactivate.url(props.staff.id), {
        preserveScroll: true,
        onSuccess: resetForms,
    });
}
</script>

<template>
    <Head :title="`Staff: ${staff.name}`" />

    <div class="flex flex-1 flex-col gap-6 p-4">
        <header class="space-y-2">
            <Link :href="index()" class="text-sm underline">
                Back to staff
            </Link>
            <h1 class="text-xl font-semibold">{{ staff.name }}</h1>
        </header>

        <dl class="grid gap-4 rounded-lg border p-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm text-muted-foreground">Roles</dt>
                <dd>{{ currentRoles }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Email</dt>
                <dd class="break-all">{{ staff.email }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">Account</dt>
                <dd>{{ staff.is_active ? 'Active' : 'Inactive' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-muted-foreground">
                    Email verification
                </dt>
                <dd>{{ staff.email_verified ? 'Verified' : 'Unverified' }}</dd>
            </div>
            <div v-if="staff.deactivated_at">
                <dt class="text-sm text-muted-foreground">Deactivated at</dt>
                <dd>{{ formatUtc(staff.deactivated_at) }}</dd>
            </div>
            <div v-if="staff.deactivation_reason" class="sm:col-span-2">
                <dt class="text-sm text-muted-foreground">
                    Deactivation reason
                </dt>
                <dd class="wrap-break-word whitespace-pre-wrap">
                    {{ staff.deactivation_reason }}
                </dd>
            </div>
        </dl>

        <p class="text-sm text-muted-foreground">
            Active accounts also need a staff role and verified email to use
            staff pages.
        </p>

        <form
            v-if="
                staff.is_active ? permissions.updateRoles : permissions.activate
            "
            class="max-w-xl space-y-4 rounded-lg border p-4"
            @submit.prevent="submitAccess"
        >
            <fieldset :disabled="busy" class="space-y-3">
                <legend class="font-semibold">
                    {{ staff.is_active ? 'Manage roles' : 'Activate account' }}
                </legend>

                <p class="text-sm text-muted-foreground">
                    Select the complete desired role set. Review retained roles
                    before reactivation. Saved changes require a fresh login.
                </p>

                <label
                    v-for="option in roleOptions"
                    :key="option.value"
                    :for="`role-${option.value}`"
                    class="flex items-center gap-2"
                >
                    <input
                        :id="`role-${option.value}`"
                        v-model="accessForm.roles"
                        type="checkbox"
                        :value="option.value"
                    />
                    {{ option.label }}
                </label>
            </fieldset>

            <ul
                v-if="accessForm.hasErrors"
                role="alert"
                class="space-y-1 text-sm text-destructive"
            >
                <li v-for="(error, field) in accessForm.errors" :key="field">
                    {{ error }}
                </li>
            </ul>

            <Button
                type="submit"
                :disabled="
                    busy ||
                    accessForm.roles.length === 0 ||
                    (staff.is_active && !accessForm.isDirty)
                "
            >
                {{ staff.is_active ? 'Save roles' : 'Activate account' }}
            </Button>
        </form>

        <form
            v-if="permissions.deactivate"
            class="max-w-xl space-y-4 rounded-lg border p-4"
            @submit.prevent="submitDeactivation"
        >
            <h2 class="font-semibold">Deactivate account</h2>

            <div class="space-y-2">
                <label for="deactivation-reason" class="text-sm font-medium">
                    Business reason
                </label>
                <textarea
                    id="deactivation-reason"
                    v-model="deactivationForm.reason"
                    :disabled="busy"
                    required
                    maxlength="1000"
                    rows="3"
                    class="w-full rounded-md border bg-transparent px-3 py-2 text-sm"
                    aria-describedby="reason-help"
                />
                <p id="reason-help" class="text-sm text-muted-foreground">
                    This reason is retained in audit history. Do not include
                    passwords, tokens or other secrets.
                </p>
            </div>

            <ul
                v-if="deactivationForm.hasErrors"
                role="alert"
                class="space-y-1 text-sm text-destructive"
            >
                <li
                    v-for="(error, field) in deactivationForm.errors"
                    :key="field"
                >
                    {{ error }}
                </li>
            </ul>

            <Button
                type="submit"
                variant="destructive"
                :disabled="busy || deactivationForm.reason.trim().length === 0"
            >
                Deactivate account
            </Button>
        </form>

        <p
            v-if="
                !permissions.updateRoles &&
                !permissions.activate &&
                !permissions.deactivate
            "
            class="text-sm text-muted-foreground"
        >
            Access changes are unavailable for this account. You cannot change
            your own roles or activation state.
        </p>
    </div>
</template>

<style scoped></style>
