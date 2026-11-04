import type { StaffRoleOption, StaffRoleValue } from '@/types/staff';

export type InvitationStatus =
    | 'pending'
    | 'expired'
    | 'accepted'
    | 'revoked'
    | 'replaced';

export interface StaffInvitationSummary {
    id: number;
    email: string;
    invited_by: number;
    roles: StaffRoleValue[];
    status: InvitationStatus;
    created_at: string;
    expires_at: string;
    permissions: {
        resend: boolean;
        revoke: boolean;
    };
}

export interface PaginatedInvitations {
    data: StaffInvitationSummary[];
    current_page: number;
    last_page: number;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

export interface InvitationPreview {
    id: number;
    email: string;
    expires_at: string;
    roles: StaffRoleOption[];
}
