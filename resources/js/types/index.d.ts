export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface Room {
    id: string;
    name: string;
    computers: Computer[];
    capacity: number;
    status: 'vacant' | 'in use' | 'reserved' | 'under maintenance';
}

export interface HardwareSpecifications {
    CPU: string;
    RAM: string;
    Storage?: string; // Có thể tùy chọn
    [key: string]: any; // Cho phép các thuộc tính khác (nếu không cố định)
}

export interface Computer {
    id: string; // UUID
    status: 'on' | 'off' | 'standby';
    name: string;
    room_id: string; // UUID, foreign key
    hardware_specifications?: HardwareSpecifications; // JSON đã parse
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
};
