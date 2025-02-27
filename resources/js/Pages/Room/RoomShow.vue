<template>
    <AuthenticatedLayout>
        <!-- Cung cấp nội dung cho header-grid slot -->
        <template #header-grid>
            <h1 class="text-2xl font-bold">{{ room.name }} Layout</h1>
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="aspect-video rounded-xl bg-muted/50" />
                <div class="aspect-video rounded-xl bg-muted/50" />
                <div class="aspect-video rounded-xl bg-muted/50" />
            </div>
        </template>
        <ComputerList :room="enhancedRoom" />
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { computed, ref } from 'vue';
import ComputerList from './Components/Computers/ComputerList.vue';

const props = defineProps({
    room: Room,
});

const computers = ref([
    {
        id: 'pc-1',
        name: 'PC-01',
        specs: 'i7, 16GB',
        grid_row: 1,
        grid_col: 1,
        mac_address: '00:1A:2B:3C:4D:5E',
        ip_address: '192.168.1.101',
        is_online: true,
        status: 'on',
        last_seen: '2023-06-01T10:30:00Z',
    },
    {
        id: 'pc-2',
        name: 'PC-02',
        specs: 'i5, 8GB',
        grid_row: 2,
        grid_col: 3,
        mac_address: '00:1A:2B:3C:4D:6F',
        ip_address: '192.168.1.102',
        is_online: false,
        status: 'off',
        last_seen: '2023-05-30T15:45:00Z',
    },
    {
        id: 'pc-3',
        name: 'PC-03',
        specs: 'i9, 32GB',
        grid_row: 3,
        grid_col: 2,
        mac_address: '00:1A:2B:3C:4D:7G',
        ip_address: '192.168.1.103',
        is_online: true,
        status: 'standby',
        last_seen: '2023-06-01T11:15:00Z',
    },
    {
        id: 'pc-4',
        name: 'PC-04',
        specs: 'i7, 16GB',
        grid_row: 4,
        grid_col: 4,
        mac_address: '00:1A:2B:3C:4D:8H',
        ip_address: '192.168.1.104',
        is_online: true,
        status: 'on',
        last_seen: '2023-06-01T09:45:00Z',
    },
]);

// Create enhanced room data with computers field for ComputerLayout
const enhancedRoom = computed(() => ({
    ...room.value,
    computers: computers.value.map((computer) => ({
        id: computer.id,
        name: computer.name,
        room_id: room.value.id,
        mac_address: computer.mac_address,
        ip_address: computer.ip_address,
        pos_row: computer.grid_row,
        pos_col: computer.grid_col,
        is_online: computer.is_online,
        status: computer.status,
        last_seen: computer.last_seen,
    })),
}));
</script>
