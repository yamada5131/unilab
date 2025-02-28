export interface User {
    id: string; // Changed to string for UUID
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface Room {
    id: string;
    name: string;
    grid_rows: number;
    grid_cols: number;
    machines: Machine[];
    // Keeping these existing fields for backwards compatibility
    created_at?: string;
    updated_at?: string;
}

export interface Machine {
    id: string;
    room_id: string;
    name: string;
    mac_address: string;
    ip_address: string;
    pos_row: number;
    pos_col: number;
    is_online: boolean;
    last_seen: string;
    created_at?: string;
    updated_at?: string;

    // Relations
    metrics?: MachineMetric[];
    processes?: MachineProcess[];
    commands?: Command[];
}

export interface MachineMetric {
    id: string;
    machine_id: string;
    cpu_usage?: number;
    ram_usage?: number;
    disk_usage?: number;
    created_at?: string;
    updated_at?: string;
}

export interface MachineProcess {
    id: string;
    machine_id: string;
    processes: any; // JSON structure
    reported_at: string;
    created_at?: string;
    updated_at?: string;
}

export interface Command {
    id: string;
    machine_id: string;
    command_type: string;
    payload?: any; // JSON structure
    status: 'pending' | 'in_progress' | 'done' | 'error';
    completed_at: string;
    created_at?: string;
    updated_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
};

// Type for process objects within MachineProcess.processes
export interface ProcessInfo {
    pid: number;
    name: string;
    cpu_percent: number;
    memory_percent: number;
    status: string;
    username: string;
    created_time?: string;
}
