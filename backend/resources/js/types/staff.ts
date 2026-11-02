export type StaffRoleValue = 'administrator' | 'dispatcher' | 'technician';

export interface StaffSummary {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
    email_verified: boolean;
    roles: StaffRoleValue[];
}

export interface PaginatedStaff {
    data: StaffSummary[];
    current_page: number;
    last_page: number;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
}

export interface StaffDetails extends StaffSummary {
    deactivated_at: string | null;
    deactivated_by: number | null;
    deactivation_reason: string | null;
}

export interface StaffRoleOption {
    value: StaffRoleValue;
    label: string;
}

export interface StaffPermissions {
    updateRoles: boolean;
    activate: boolean;
    deactivate: boolean;
}
