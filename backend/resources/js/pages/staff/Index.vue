<script setup lang="ts">
import { index, show } from '@/routes/staff';
import type { PaginatedStaff, StaffRoleValue } from '@/types/staff';
import { Button } from '@/components/ui/button';
import { Head, Link } from '@inertiajs/vue3';

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

defineProps<{
    staff: PaginatedStaff;
}>();

const roleLabels: Record<StaffRoleValue, string> = {
    administrator: 'Administrator',
    dispatcher: 'Dispatcher',
    technician: 'Technician',
};
</script>

<template>
    <Head title="Staff" />

    <div class="flex flex-1 flex-col gap-6 p-4">
        <header>
            <h1 class="text-xl font-semibold">Staff</h1>
            <p class="text-sm text-muted-foreground">
                Review existing accounts, roles and access status.
            </p>
        </header>

        <div class="overflow-x-auto rounded-lg border">
            <table class="w-full text-left text-sm">
                <caption class="sr-only">
                    Staff accounts
                </caption>

                <thead class="border-b bg-muted/50">
                    <tr>
                        <th scope="col" class="p-3">Name</th>
                        <th scope="col" class="p-3">Email</th>
                        <th scope="col" class="p-3">Roles</th>
                        <th scope="col" class="p-3">Account</th>
                        <th scope="col" class="p-3">Email verification</th>
                        <th scope="col" class="p-3">Details</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="person in staff.data"
                        :key="person.id"
                        class="border-b"
                    >
                        <th scope="row" class="p-3 font-medium">
                            {{ person.name }}
                        </th>
                        <td class="p-3">{{ person.email }}</td>
                        <td class="p-3">
                            {{
                                person.roles
                                    .map((role) => roleLabels[role])
                                    .join(', ') || 'No roles'
                            }}
                        </td>
                        <td class="p-3">
                            {{ person.is_active ? 'Active' : 'Inactive' }}
                        </td>
                        <td class="p-3">
                            {{
                                person.email_verified
                                    ? 'Verified'
                                    : 'Unverified'
                            }}
                        </td>
                        <td class="p-3">
                            <Button variant="outline" size="sm" as-child>
                                <Link
                                    :href="show(person.id)"
                                    :aria-label="`View ${person.name}`"
                                >
                                    View
                                </Link>
                            </Button>
                        </td>
                    </tr>

                    <tr v-if="staff.data.length === 0">
                        <td
                            colspan="6"
                            class="p-6 text-center text-muted-foreground"
                        >
                            No staff accounts found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <nav
            aria-label="Staff pagination"
            class="flex flex-wrap items-center justify-between gap-3"
        >
            <p class="text-sm text-muted-foreground" aria-live="polite">
                Page {{ staff.current_page }} of {{ staff.last_page }} ·
                {{ staff.total }} accounts
            </p>

            <div class="flex gap-2">
                <Button v-if="staff.prev_page_url" variant="outline" as-child>
                    <Link :href="staff.prev_page_url">Previous</Link>
                </Button>
                <Button v-else variant="outline" disabled> Previous </Button>

                <Button v-if="staff.next_page_url" variant="outline" as-child>
                    <Link :href="staff.next_page_url">Next</Link>
                </Button>
                <Button v-else variant="outline" disabled> Next </Button>
            </div>
        </nav>
    </div>
</template>

<style scoped></style>
