<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { logout } from '@/routes';
import { inspect, store } from '@/routes/invitations/accept';
import type { InvitationPreview } from '@/types/invitations';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import PasswordInput from '@/components/PasswordInput.vue';
import { Input } from '@/components/ui/input';
import { formatUtc } from '@/lib/date';

defineOptions({
    layout: {
        title: 'Accept staff invitation',
        description: 'Review your invitation before creating your account.',
    },
});

const props = defineProps<{
    invitationId: number;
    mustSignOut: boolean;
}>();

const invitationToken = ref('');
const preview = ref<InvitationPreview | null>(null);
const loading = ref(!props.mustSignOut);
const previewError = ref('');

const acceptanceForm = useForm({
    invitation_token: '',
    name: '',
    password: '',
    password_confirmation: '',
});

acceptanceForm.dontRemember(
    'invitation_token',
    'password',
    'password_confirmation',
);

function acceptInvitation(): void {
    if (
        props.mustSignOut ||
        !preview.value ||
        !invitationToken.value ||
        acceptanceForm.processing
    ) {
        return;
    }

    acceptanceForm.invitation_token = invitationToken.value;

    acceptanceForm.post(store.url(props.invitationId), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            invitationToken.value = '';
            acceptanceForm.reset();
        },
        onFinish: () => {
            acceptanceForm.reset(
                'invitation_token',
                'password',
                'password_confirmation',
            );
        },
    });
}

function csrfToken(): string {
    const cookie = document.cookie
        .split('; ')
        .find((value) => value.startsWith('XSRF-TOKEN='));

    return cookie ? decodeURIComponent(cookie.slice('XSRF-TOKEN='.length)) : '';
}

onMounted(async () => {
    if (props.mustSignOut) {
        return;
    }

    const fragment = new URLSearchParams(window.location.hash.slice(1));
    invitationToken.value = fragment.get('token') ?? '';

    window.history.replaceState(
        window.history.state,
        '',
        window.location.pathname + window.location.search,
    );

    if (!/^[0-9a-f]{64}$/.test(invitationToken.value)) {
        previewError.value =
            'This invitation is invalid or no longer available.';
        loading.value = false;
        invitationToken.value = '';
        return;
    }

    try {
        const response = await fetch(inspect.url(props.invitationId), {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({
                invitation_token: invitationToken.value,
            }),
        });

        if (!response.ok) {
            if (response.status === 429) {
                previewError.value =
                    'Too many attempts. Please wait, then reopen the invitation link.';
            } else if (response.status === 419) {
                previewError.value =
                    'Your session expired. Please reopen the invitation link.';
            } else {
                previewError.value =
                    'This invitation is invalid or no longer available.';
            }

            invitationToken.value = '';
            return;
        }

        preview.value = (await response.json()) as InvitationPreview;
    } catch {
        previewError.value =
            'Unable to check this invitation. Please reopen the link and try again.';
        invitationToken.value = '';
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <Head title="Accept staff invitation" />

    <div class="space-y-6">
        <div v-if="mustSignOut" class="space-y-4">
            <p class="text-sm">
                You are currently signed in. Sign out, then reopen the
                invitation link from your email to continue.
            </p>

            <Link :href="logout()" as="button" class="text-sm underline">
                Sign out
            </Link>
        </div>

        <p v-else-if="loading" role="status" class="text-sm">
            Checking invitation…
        </p>

        <p
            v-else-if="previewError"
            role="alert"
            class="text-sm text-destructive"
        >
            {{ previewError }}
        </p>

        <section v-else-if="preview" class="space-y-4">
            <h2 class="font-semibold">Your invitation</h2>

            <dl class="space-y-3 rounded-lg border p-4 text-sm">
                <div>
                    <dt class="text-muted-foreground">Email address</dt>
                    <dd class="break-all">{{ preview.email }}</dd>
                </div>

                <div>
                    <dt class="text-muted-foreground">Staff roles</dt>
                    <dd>
                        {{ preview.roles.map((role) => role.label).join(', ') }}
                    </dd>
                </div>

                <div>
                    <dt class="text-muted-foreground">Expires (UTC)</dt>
                    <dd>{{ formatUtc(preview.expires_at) }}</dd>
                </div>
            </dl>

            <p class="text-sm text-muted-foreground">
                Your email address and roles were selected by an Administrator
                and cannot be changed here.
            </p>

            <form class="space-y-4" @submit.prevent="acceptInvitation">
                <fieldset
                    :disabled="acceptanceForm.processing"
                    class="space-y-4"
                >
                    <div class="space-y-2">
                        <Label for="invitation-name">Your name</Label>
                        <Input
                            id="invitation-name"
                            v-model="acceptanceForm.name"
                            name="name"
                            autocomplete="name"
                            maxlength="255"
                            required
                        />
                    </div>

                    <div class="space-y-2">
                        <Label for="invitation-password">Password</Label>
                        <PasswordInput
                            id="invitation-password"
                            v-model="acceptanceForm.password"
                            name="password"
                            autocomplete="new-password"
                            minlength="12"
                            required
                        />
                        <p class="text-sm text-muted-foreground">
                            Use at least 12 characters, including uppercase and
                            lowercase letters, a number, and a symbol.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="invitation-password-confirmation">
                            Confirm password
                        </Label>
                        <PasswordInput
                            id="invitation-password-confirmation"
                            v-model="acceptanceForm.password_confirmation"
                            name="password_confirmation"
                            autocomplete="new-password"
                            minlength="12"
                            required
                        />
                    </div>

                    <div
                        v-if="Object.keys(acceptanceForm.errors).length"
                        role="alert"
                        class="space-y-1 text-sm text-destructive"
                    >
                        <p
                            v-for="(message, field) in acceptanceForm.errors"
                            :key="field"
                        >
                            {{ message }}
                        </p>
                    </div>

                    <p class="text-sm text-muted-foreground">
                        After creating your account, you will be redirected to
                        log in.
                    </p>

                    <Button
                        type="submit"
                        class="w-full"
                        :disabled="acceptanceForm.processing"
                    >
                        {{
                            acceptanceForm.processing
                                ? 'Creating account…'
                                : 'Create my account'
                        }}
                    </Button>
                </fieldset>
            </form>
        </section>
    </div>
</template>

<style scoped></style>
